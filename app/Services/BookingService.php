<?php

namespace App\Services;

use App\Models\DatLichTienIch;
use App\Models\TienIch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Toàn bộ business logic của module Đặt lịch tiện ích (sinh mã, tính phí,
 * kiểm tra sức chứa, và state machine duyệt/hủy/hoàn thành) nằm ở đây.
 * Controller chỉ được gọi các method public của service này, không tự
 * thao tác trực tiếp lên model DatLichTienIch.
 */
class BookingService
{
    // ─────────────────────────────────────────────────────────────
    //  Tạo lượt đặt lịch (entry point chính)
    // ─────────────────────────────────────────────────────────────

    /**
     * Tạo một lượt đặt lịch tiện ích mới.
     *
     * $data cần có: cu_dan, tien_ich, thoi_gian_bat_dau, thoi_gian_ket_thuc, so_nguoi.
     * $data có thể có: can_ho, ghi_chu.
     *
     * Các trường thuộc quy trình nội bộ (ma_dat_lich, trang_thai, nhan_vien_duyet,
     * ngay_duyet, ngay_huy, ly_do_huy, nguoi_cap_nhat, phi_su_dung) do Service tự
     * quyết định, không nhận từ tham số để tránh Controller/FormRequest can thiệp
     * vào state machine hoặc tự ý ghi đè số tiền.
     */
    public function taoDatLich(array $data): DatLichTienIch
    {
        return DB::transaction(function () use ($data) {
            // lockForUpdate(): khóa dòng tien_ich cho đến khi transaction này commit,
            // buộc các request tạo/sửa/duyệt khác trên CÙNG tiện ích phải chờ tuần tự.
            // Không có lock này, hai request đồng thời đều có thể đọc cùng một tổng
            // "đã duyệt" hiện tại, cùng tính ra "còn đủ chỗ" và cùng được tự động
            // duyệt — dẫn tới vượt sức chứa thực tế sau khi cả hai commit (race
            // condition kinh điển kiểu check-then-act trên dữ liệu tổng hợp).
            $tienIch = TienIch::lockForUpdate()->findOrFail($data['tien_ich']);

            if ((int) $tienIch->trang_thai !== TienIch::TRANG_THAI_HOAT_DONG) {
                throw ValidationException::withMessages([
                    'tien_ich' => ['Tiện ích hiện không hoạt động, không thể đặt lịch.'],
                ]);
            }

            $batDau  = Carbon::parse($data['thoi_gian_bat_dau']);
            $ketThuc = Carbon::parse($data['thoi_gian_ket_thuc']);
            $soNguoi = (int) $data['so_nguoi'];

            $trangThai = $this->xacDinhTrangThaiTheoSucChua($tienIch, $batDau, $ketThuc, $soNguoi);
            $daDuocDuyet = $trangThai === DatLichTienIch::TRANG_THAI_DA_DUYET;

            $datLich = $this->taoBanGhiVoiMaDuyNhat([
                'cu_dan'             => $data['cu_dan'],
                'can_ho'             => $data['can_ho'] ?? null,
                'tien_ich'           => $tienIch->id,
                'thoi_gian_bat_dau'  => $batDau,
                'thoi_gian_ket_thuc' => $ketThuc,
                'so_nguoi'           => $soNguoi,
                // Phí luôn do hệ thống tự tính (xem tinhPhi()) — không nhận phi_su_dung
                // từ $data để tránh Controller/FormRequest tự ý ghi đè số tiền.
                'phi_su_dung'        => $this->tinhPhi($tienIch, $soNguoi, $batDau, $ketThuc),
                'ghi_chu'            => $data['ghi_chu'] ?? null,
                'trang_thai'         => $trangThai,
                // Đã duyệt ngay từ khi tạo là do hệ thống tự động duyệt theo sức chứa,
                // không phải một nhân viên cụ thể duyệt → nhan_vien_duyet để null.
                'nhan_vien_duyet'    => null,
                'ngay_duyet'         => $daDuocDuyet ? now() : null,
                'nguoi_cap_nhat'     => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('INSERT', 'dat_lich_tien_ich', $datLich->id, null, $datLich->fresh()->toArray());

            return $datLich->fresh();
        });
    }

    /**
     * Tạo bản ghi với ma_dat_lich tự sinh, tự thử lại nếu đụng unique
     * constraint. sinhMaDatLich() tự nó là "kiểm tra rồi tạo" (check-then-act)
     * không atomic: hai request đồng thời đều có thể đọc thấy cùng một mã kế
     * tiếp còn trống trước khi request nào insert xong, dẫn tới cùng sinh ra
     * một mã và một trong hai bị DB từ chối do vi phạm unique. Thay vì để lỗi
     * đó rơi ra ngoài thành lỗi 500, bắt riêng UniqueConstraintViolationException
     * và thử sinh mã mới rồi tạo lại (tối đa vài lần).
     */
    private function taoBanGhiVoiMaDuyNhat(array $thuocTinh, int $soLanThuToiDa = 3): DatLichTienIch
    {
        for ($lan = 1; $lan <= $soLanThuToiDa; $lan++) {
            try {
                return DatLichTienIch::create(['ma_dat_lich' => $this->sinhMaDatLich()] + $thuocTinh);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                if ($lan === $soLanThuToiDa) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Cập nhật một lượt đặt lịch đang chờ duyệt (chỉ cho phép sửa khi chưa
     * duyệt/hủy/từ chối/hoàn thành — các trạng thái đó là kết quả của một
     * quyết định đã chốt, sửa nội dung sau đó sẽ làm sai lệch quyết định ấy).
     * Tính lại phí và trạng thái (Đã duyệt/Chờ duyệt) theo sức chứa hiện tại,
     * giống hệt logic của taoDatLich().
     *
     * $data cần có: tien_ich, thoi_gian_bat_dau, thoi_gian_ket_thuc, so_nguoi.
     * $data có thể có: can_ho, ghi_chu.
     */
    public function capNhatDatLich(DatLichTienIch $datLich, array $data): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich, $data) {
            $this->damBaoTrangThai($datLich, [DatLichTienIch::TRANG_THAI_CHO_DUYET], 'chỉnh sửa');

            // Xem giải thích lockForUpdate() ở taoDatLich() — cùng lý do: tránh
            // race condition khi tính lại sức chứa.
            $tienIch = TienIch::lockForUpdate()->findOrFail($data['tien_ich']);

            if ((int) $tienIch->trang_thai !== TienIch::TRANG_THAI_HOAT_DONG) {
                throw ValidationException::withMessages([
                    'tien_ich' => ['Tiện ích hiện không hoạt động, không thể đặt lịch.'],
                ]);
            }

            $batDau  = Carbon::parse($data['thoi_gian_bat_dau']);
            $ketThuc = Carbon::parse($data['thoi_gian_ket_thuc']);
            $soNguoi = (int) $data['so_nguoi'];

            $trangThai   = $this->xacDinhTrangThaiTheoSucChua($tienIch, $batDau, $ketThuc, $soNguoi);
            $daDuocDuyet = $trangThai === DatLichTienIch::TRANG_THAI_DA_DUYET;

            $old = $datLich->toArray();

            $datLich->update([
                'can_ho'             => $data['can_ho'] ?? null,
                'tien_ich'           => $tienIch->id,
                'thoi_gian_bat_dau'  => $batDau,
                'thoi_gian_ket_thuc' => $ketThuc,
                'so_nguoi'           => $soNguoi,
                'phi_su_dung'        => $this->tinhPhi($tienIch, $soNguoi, $batDau, $ketThuc),
                'ghi_chu'            => $data['ghi_chu'] ?? null,
                'trang_thai'         => $trangThai,
                'nhan_vien_duyet'    => null,
                'ngay_duyet'         => $daDuocDuyet ? now() : null,
                'nguoi_cap_nhat'     => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            return $datLich->fresh();
        });
    }

    // ─────────────────────────────────────────────────────────────
    //  Sinh mã đặt lịch
    // ─────────────────────────────────────────────────────────────

    /**
     * Sinh mã đặt lịch dạng DL + yyyymmdd + số thứ tự 4 chữ số, đảm bảo
     * duy nhất kể cả với các bản ghi đã xóa mềm (unique constraint ở DB
     * không loại trừ deletedAt).
     */
    public function sinhMaDatLich(): string
    {
        $prefix  = 'DL'.now()->format('Ymd');
        $soThuTu = DatLichTienIch::withTrashed()
            ->where('ma_dat_lich', 'like', $prefix.'%')
            ->count() + 1;

        do {
            $ma = $prefix.str_pad((string) $soThuTu, 4, '0', STR_PAD_LEFT);
            $daTonTai = DatLichTienIch::withTrashed()->where('ma_dat_lich', $ma)->exists();
            $soThuTu++;
        } while ($daTonTai);

        return $ma;
    }

    // ─────────────────────────────────────────────────────────────
    //  Tính phí (tự động, không cho nhập tay)
    // ─────────────────────────────────────────────────────────────

    /**
     * Phí sử dụng được tự động tính, không nhận giá trị nhập tay từ bên ngoài:
     *
     *   phi_su_dung = so_nguoi × tien_ich.phi_su_dung × số giờ
     *   số giờ      = TIMESTAMPDIFF(MINUTE, batDau, ketThuc) / 60
     *
     * Chia phút cho 60 cho ra số giờ lẻ (vd. 90 phút = 1.5 giờ) — không làm
     * tròn số giờ lẫn kết quả cuối; giá trị thô được lưu thẳng vào cột
     * decimal(15,2), phần vượt quá 2 chữ số thập phân do chính cột DECIMAL
     * xử lý khi lưu, không phải do logic tính toán chủ động round().
     *
     * Lưu ý: Carbon::diffInMinutes() (Carbon 3.x) trả về float có phần giây
     * lẻ (vd. 90 phút 45 giây → 90.75), khác với TIMESTAMPDIFF(MINUTE) của
     * MySQL vốn cắt bỏ phần lẻ chỉ lấy phút nguyên. Phải floor() lại để đúng
     * ngữ nghĩa TIMESTAMPDIFF(MINUTE) mà công thức yêu cầu.
     */
    public function tinhPhi(TienIch $tienIch, int $soNguoi, Carbon $batDau, Carbon $ketThuc): float
    {
        $soPhut = (int) floor($batDau->diffInMinutes($ketThuc));
        $soGio  = $soPhut / 60;

        return $soNguoi * (float) $tienIch->phi_su_dung * $soGio;
    }

    // ─────────────────────────────────────────────────────────────
    //  Kiểm tra sức chứa
    // ─────────────────────────────────────────────────────────────

    /**
     * Quyết định trạng thái ban đầu của một lượt đặt lịch dựa trên sức chứa:
     *
     *   tổng so_nguoi của các booking ĐÃ DUYỆT giao nhau về thời gian + so_nguoi mới
     *       <= suc_chua  → Đã duyệt (tự động duyệt)
     *       >  suc_chua  → Chờ duyệt
     *
     * suc_chua rỗng/0 nghĩa là không giới hạn → luôn Đã duyệt.
     */
    public function xacDinhTrangThaiTheoSucChua(TienIch $tienIch, Carbon $batDau, Carbon $ketThuc, int $soNguoiMoi): int
    {
        if (!$tienIch->suc_chua) {
            return DatLichTienIch::TRANG_THAI_DA_DUYET;
        }

        $tongDaDuyet = $this->tongNguoiDaDuyetGiaoNhau($tienIch->id, $batDau, $ketThuc);

        return ($tongDaDuyet + $soNguoiMoi) <= $tienIch->suc_chua
            ? DatLichTienIch::TRANG_THAI_DA_DUYET
            : DatLichTienIch::TRANG_THAI_CHO_DUYET;
    }

    /**
     * Guard dùng khi nhân viên chủ động bấm "Duyệt" một lượt đang chờ duyệt:
     * chặn hành động (ném lỗi) nếu sức chứa không còn đủ tại thời điểm duyệt,
     * thay vì âm thầm giữ nguyên trạng thái như lúc tạo mới.
     */
    public function kiemTraSucChua(TienIch $tienIch, Carbon $batDau, Carbon $ketThuc, int $soNguoi): void
    {
        if (!$tienIch->suc_chua) {
            return;
        }

        $tongDaDuyet = $this->tongNguoiDaDuyetGiaoNhau($tienIch->id, $batDau, $ketThuc);

        if ($tongDaDuyet + $soNguoi > $tienIch->suc_chua) {
            $conLai = max(0, $tienIch->suc_chua - $tongDaDuyet);

            throw ValidationException::withMessages([
                'so_nguoi' => ["Tiện ích \"{$tienIch->ten_tien_ich}\" chỉ còn chỗ cho {$conLai} người trong khung giờ này (sức chứa tối đa {$tienIch->suc_chua})."],
            ]);
        }
    }

    /**
     * Tổng so_nguoi của các booking ĐÃ DUYỆT (trang_thai = Đã duyệt) mà khoảng
     * thời gian giao nhau với [$batDau, $ketThuc) trên cùng một tiện ích.
     *
     * Điều kiện giao nhau chuẩn cho 2 khoảng nửa mở [s1,e1) và [s2,e2):
     *   s1 < e2 AND e1 > s2
     * Đây là phủ định của "không giao nhau" (e1<=s2 OR s1>=e2) nên bao trọn
     * mọi kiểu chồng lấn (một phần, lồng nhau, trùng khít) và loại đúng
     * trường hợp hai lượt nối đuôi sát giờ (chạm mốc, không thực sự chồng chỗ).
     *
     * Gộp thành 1 câu SUM ở tầng DB (không fetch rồi cộng tay ở PHP), lọc theo
     * tien_ich trước để tận dụng index idx_dltl_tienich_thoigian hiện có.
     */
    private function tongNguoiDaDuyetGiaoNhau(int $tienIchId, Carbon $batDau, Carbon $ketThuc): int
    {
        return (int) DatLichTienIch::query()
            ->where('tien_ich', $tienIchId)
            ->where('trang_thai', DatLichTienIch::TRANG_THAI_DA_DUYET)
            ->where('thoi_gian_bat_dau', '<', $ketThuc)
            ->where('thoi_gian_ket_thuc', '>', $batDau)
            ->sum('so_nguoi');
    }

    // ─────────────────────────────────────────────────────────────
    //  State machine: tự động duyệt / chuyển chờ duyệt / hủy / hoàn thành
    // ─────────────────────────────────────────────────────────────

    /**
     * Hệ thống tự động duyệt một lượt đang chờ duyệt (không gắn nhân viên
     * duyệt cụ thể). Dùng cho các luồng ngoài taoDatLich() cần chuyển một
     * booking đang chờ sang đã duyệt mà không qua thao tác thủ công của
     * nhân viên (vd. job nền tái đánh giá sức chứa sau khi có chỗ trống).
     */
    public function tuDongDuyet(DatLichTienIch $datLich): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich) {
            $this->damBaoTrangThai($datLich, [DatLichTienIch::TRANG_THAI_CHO_DUYET], 'tự động duyệt');

            $old = $datLich->toArray();

            $datLich->update([
                'trang_thai'      => DatLichTienIch::TRANG_THAI_DA_DUYET,
                'nhan_vien_duyet' => null,
                'ngay_duyet'      => now(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            return $datLich->fresh();
        });
    }

    /**
     * Nhân viên duyệt thủ công một lượt đang chờ duyệt. Kiểm tra lại sức chứa
     * tại thời điểm duyệt để tránh vượt sức chứa do nhiều yêu cầu cùng tranh
     * chấp một khung giờ (booking đang được duyệt luôn ở trạng thái Chờ duyệt
     * nên không lọt vào tổng "đã duyệt" của chính nó — không cần loại trừ).
     */
    public function duyet(DatLichTienIch $datLich, ?int $nguoiDuyet = null): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich, $nguoiDuyet) {
            $this->damBaoTrangThai($datLich, [DatLichTienIch::TRANG_THAI_CHO_DUYET], 'duyệt');

            // Xem giải thích lockForUpdate() ở taoDatLich() — cùng lý do: tránh
            // race condition khi tính lại sức chứa lúc duyệt.
            $tienIch = TienIch::withTrashed()->lockForUpdate()->findOrFail($datLich->tien_ich);
            $this->kiemTraSucChua(
                $tienIch,
                $datLich->thoi_gian_bat_dau,
                $datLich->thoi_gian_ket_thuc,
                (int) $datLich->so_nguoi
            );

            $old = $datLich->toArray();

            $datLich->update([
                'trang_thai'      => DatLichTienIch::TRANG_THAI_DA_DUYET,
                'nhan_vien_duyet' => $nguoiDuyet ?? auth('nhanvien')->id(),
                'ngay_duyet'      => now(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            return $datLich->fresh();
        });
    }

    /**
     * Từ chối một lượt đang chờ duyệt. Không áp dụng cho lượt đã duyệt/đã
     * hủy/đã từ chối/hoàn thành — muốn dừng một lượt đã duyệt thì dùng huy().
     */
    public function tuChoi(DatLichTienIch $datLich, ?string $lyDo = null): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich, $lyDo) {
            $this->damBaoTrangThai($datLich, [DatLichTienIch::TRANG_THAI_CHO_DUYET], 'từ chối');

            $old = $datLich->toArray();

            $datLich->update([
                'trang_thai'      => DatLichTienIch::TRANG_THAI_TU_CHOI,
                'nhan_vien_duyet' => auth('nhanvien')->id(),
                'ngay_huy'        => now(),
                'ly_do_huy'       => $lyDo,
                'nguoi_cap_nhat'  => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            return $datLich->fresh();
        });
    }

    /**
     * Chuyển một lượt đã duyệt về lại trạng thái chờ duyệt (ví dụ nhân viên
     * cần xem xét lại trước khi diễn ra). Không áp dụng cho lượt đã hoàn
     * thành/đã hủy/đã từ chối vì đó là trạng thái cuối (terminal).
     */
    public function chuyenChoDuyet(DatLichTienIch $datLich): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich) {
            $this->damBaoTrangThai($datLich, [DatLichTienIch::TRANG_THAI_DA_DUYET], 'chuyển về chờ duyệt');

            $old = $datLich->toArray();

            $datLich->update([
                'trang_thai'      => DatLichTienIch::TRANG_THAI_CHO_DUYET,
                'nhan_vien_duyet' => null,
                'ngay_duyet'      => null,
                'nguoi_cap_nhat'  => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            return $datLich->fresh();
        });
    }

    /**
     * Hủy một lượt đặt đang chờ duyệt hoặc đã duyệt. Không thể hủy lượt đã
     * hoàn thành/đã hủy/đã từ chối.
     */
    public function huy(DatLichTienIch $datLich, ?string $lyDoHuy = null): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich, $lyDoHuy) {
            $this->damBaoTrangThai(
                $datLich,
                [DatLichTienIch::TRANG_THAI_CHO_DUYET, DatLichTienIch::TRANG_THAI_DA_DUYET],
                'hủy'
            );

            $old = $datLich->toArray();

            $datLich->update([
                'trang_thai'     => DatLichTienIch::TRANG_THAI_DA_HUY,
                'ngay_huy'       => now(),
                'ly_do_huy'      => $lyDoHuy,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            return $datLich->fresh();
        });
    }

    /**
     * Cư dân tự hủy lượt đặt lịch của chính mình. Khác với huy() (dùng cho
     * nhân viên, lý do tự nhập, không giới hạn thời gian):
     *   - chỉ được hủy khi còn cách giờ sử dụng (thoi_gian_bat_dau) ít nhất 2 giờ;
     *   - lý do hủy luôn cố định là "Cư dân hủy lịch", không nhận lý do tự nhập
     *     từ Controller để tránh cư dân ghi nội dung tùy ý vào log hệ thống.
     * Việc kiểm tra $datLich có thuộc về cư dân đang đăng nhập hay không là
     * một quyết định authorization (ai được phép gọi hành động này), không
     * phải business rule của bản thân việc hủy, nên được thực hiện ở Controller.
     */
    public function huyBoiCuDan(DatLichTienIch $datLich): DatLichTienIch
    {
        if (now()->addHours(2)->gt($datLich->thoi_gian_bat_dau)) {
            throw ValidationException::withMessages([
                'thoi_gian_bat_dau' => ['Chỉ có thể hủy lịch khi còn cách giờ sử dụng ít nhất 2 giờ.'],
            ]);
        }

        return $this->huy($datLich, 'Cư dân hủy lịch');
    }

    /**
     * Đánh dấu hoàn thành một lượt đặt đã duyệt (đã sử dụng tiện ích xong).
     */
    public function hoanThanh(DatLichTienIch $datLich): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich) {
            $this->damBaoTrangThai($datLich, [DatLichTienIch::TRANG_THAI_DA_DUYET], 'hoàn thành');

            $old = $datLich->toArray();

            $datLich->update([
                'trang_thai'     => DatLichTienIch::TRANG_THAI_HOAN_THANH,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            return $datLich->fresh();
        });
    }

    // ─────────────────────────────────────────────────────────────
    //  Scheduler: tự động hủy quá hạn / tự động hoàn thành
    // ─────────────────────────────────────────────────────────────

    /**
     * Tự động hủy các lượt đang Chờ duyệt mà chỉ còn <= 2 giờ đến giờ sử dụng
     * (thoi_gian_bat_dau). Dùng cho scheduler chạy mỗi 5 phút — nhân viên
     * không kịp duyệt trước giờ diễn ra thì hệ thống tự hủy để tránh giữ chỗ
     * "treo" vô thời hạn. Tái sử dụng huy() nên vẫn qua đúng state-machine
     * guard và audit log như hủy thủ công.
     *
     * @return int Số lượt đã tự động hủy.
     */
    public function tuDongHuyQuaHan(): int
    {
        $mocGio = now()->addHours(2);
        $soLuong = 0;

        DatLichTienIch::query()
            ->where('trang_thai', DatLichTienIch::TRANG_THAI_CHO_DUYET)
            ->where('thoi_gian_bat_dau', '<=', $mocGio)
            ->get()
            ->each(function (DatLichTienIch $datLich) use (&$soLuong) {
                $this->huy($datLich, 'Hệ thống tự động hủy do không đủ sức chứa trước giờ sử dụng 2 tiếng.');
                $soLuong++;
            });

        return $soLuong;
    }

    /**
     * Tự động đánh dấu Hoàn thành các lượt Đã duyệt mà đã qua giờ kết thúc
     * (thoi_gian_ket_thuc < now()). Dùng cho scheduler. Tái sử dụng
     * hoanThanh() nên vẫn qua đúng state-machine guard và audit log.
     *
     * @return int Số lượt đã tự động đánh dấu hoàn thành.
     */
    public function tuDongHoanThanh(): int
    {
        $soLuong = 0;

        DatLichTienIch::query()
            ->where('trang_thai', DatLichTienIch::TRANG_THAI_DA_DUYET)
            ->where('thoi_gian_ket_thuc', '<', now())
            ->get()
            ->each(function (DatLichTienIch $datLich) use (&$soLuong) {
                $this->hoanThanh($datLich);
                $soLuong++;
            });

        return $soLuong;
    }

    // ─────────────────────────────────────────────────────────────
    //  Xóa / khôi phục
    // ─────────────────────────────────────────────────────────────

    /**
     * Xóa mềm một lượt đặt lịch.
     */
    public function xoa(DatLichTienIch $datLich): void
    {
        DB::transaction(function () use ($datLich) {
            AuditLogService::log('DELETE', 'dat_lich_tien_ich', $datLich->id, $datLich->toArray(), null);
            $datLich->delete();
        });
    }

    /**
     * Khôi phục một lượt đặt lịch đã xóa mềm.
     */
    public function khoiPhuc(int $id): DatLichTienIch
    {
        return DB::transaction(function () use ($id) {
            $datLich = DatLichTienIch::withTrashed()->findOrFail($id);
            $datLich->restore();

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, null, $datLich->fresh()->toArray());

            return $datLich->fresh();
        });
    }

    // ─────────────────────────────────────────────────────────────
    //  Private helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Chặn chuyển trạng thái không hợp lệ (state machine guard) dùng chung
     * cho mọi hành động chuyển trạng thái.
     *
     * @param  int[]  $trangThaiChoPhep
     */
    private function damBaoTrangThai(DatLichTienIch $datLich, array $trangThaiChoPhep, string $hanhDong): void
    {
        if (!in_array((int) $datLich->trang_thai, $trangThaiChoPhep, true)) {
            throw ValidationException::withMessages([
                'trang_thai' => ["Không thể {$hanhDong} lượt đặt lịch ở trạng thái hiện tại ({$datLich->trang_thai_label['text']})."],
            ]);
        }
    }
}
