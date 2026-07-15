<?php

namespace App\Services;

use App\Models\DatLichTienIch;
use App\Models\TienIch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * State machine của một lượt đặt lịch: tạo/sửa/duyệt/từ chối/hủy/hoàn thành/xóa.
 * Mọi thay đổi trạng thái đều:
 *   - Chạy trong DB::transaction() với retry tự động khi deadlock (tham số $attempts).
 *   - Lock đúng thứ tự cố định: tien_ich trước, rồi đến chính dòng dat_lich_tien_ich
 *     (chống deadlock + đảm bảo đọc lại trạng thái mới nhất ngay trong transaction,
 *     không dùng instance đã load trước đó từ route-model-binding).
 *   - Idempotent: gọi lại một hành động đã ở đúng trạng thái đích trả về thành công,
 *     không ghi đè dữ liệu, không log audit trùng, không broadcast trùng.
 *   - Khi một booking Đã duyệt rời khỏi trạng thái đó (hủy/hoàn thành) và giải
 *     phóng chỗ, gọi BookingFifoService NGAY trong CÙNG transaction để duyệt tiếp
 *     booking đầu hàng đợi (nếu có) — không chờ Scheduler.
 */
class BookingApprovalService
{
    private const SO_LAN_THU_LAI = 3;

    public function __construct(
        private readonly BookingCapacityService $capacityService,
        private readonly BookingFifoService $fifoService,
        private readonly BookingRealtimeService $realtimeService,
    ) {
    }

    // ─────────────────────────────────────────────────────────────
    //  Tạo / sửa
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
            // buộc các request tạo/sửa/duyệt/hủy khác trên CÙNG tiện ích phải chờ
            // tuần tự — tránh race condition kiểu check-then-act khi tính sức chứa.
            $tienIch = TienIch::lockForUpdate()->findOrFail($data['tien_ich']);

            if ((int) $tienIch->trang_thai !== TienIch::TRANG_THAI_HOAT_DONG) {
                throw ValidationException::withMessages([
                    'tien_ich' => ['Tiện ích hiện không hoạt động, không thể đặt lịch.'],
                ]);
            }

            $batDau  = Carbon::parse($data['thoi_gian_bat_dau']);
            $ketThuc = Carbon::parse($data['thoi_gian_ket_thuc']);
            $soNguoi = (int) $data['so_nguoi'];

            // Luôn tạo Chờ duyệt trước, KHÔNG tự quyết "đủ chỗ -> Đã duyệt ngay"
            // chỉ dựa trên sức chứa thô của riêng booking này — làm vậy có thể
            // để một booking mới (nhỏ, tự nó đủ chỗ) "vượt hàng" một booking cũ
            // hơn (created_at sớm hơn, cùng giao khung giờ) nhưng lớn hơn vẫn
            // đang Chờ duyệt, phá vỡ FIFO tuyệt đối.
            $datLich = $this->taoBanGhiVoiMaDuyNhat([
                'cu_dan'             => $data['cu_dan'],
                'can_ho'             => $data['can_ho'] ?? null,
                'tien_ich'           => $tienIch->id,
                'thoi_gian_bat_dau'  => $batDau,
                'thoi_gian_ket_thuc' => $ketThuc,
                'so_nguoi'           => $soNguoi,
                // Phí luôn do hệ thống tự tính — không nhận phi_su_dung từ $data
                // để tránh Controller/FormRequest tự ý ghi đè số tiền.
                'phi_su_dung'        => $this->capacityService->tinhPhi($tienIch, $soNguoi, $batDau, $ketThuc),
                'ghi_chu'            => $data['ghi_chu'] ?? null,
                'trang_thai'         => DatLichTienIch::TRANG_THAI_CHO_DUYET,
                'nhan_vien_duyet'    => null,
                'ngay_duyet'         => null,
                'nguoi_cap_nhat'     => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('INSERT', 'dat_lich_tien_ich', $datLich->id, null, $datLich->fresh()->toArray());

            $this->realtimeService->daTao($datLich);
            $this->realtimeService->hangChoThayDoi($tienIch->id);

            // Xét FIFO NGAY trong CÙNG transaction: nếu booking này thật sự đủ
            // điều kiện (đứng đầu hàng đợi của cụm khung giờ của nó VÀ đủ sức
            // chứa), nó được duyệt ngay lập tức (ngay_duyet = NOW()) — nếu
            // không, nó ở lại Chờ duyệt đúng vị trí FIFO của mình. Cách này vừa
            // thỏa "đủ chỗ thì duyệt ngay", vừa không bao giờ phá vỡ thứ tự
            // tạo trước (created_at ASC, id ASC) của các booking giao khung giờ.
            $this->fifoService->xuLyHangDoiChoTienIch($tienIch->id);

            return $datLich->fresh();
        }, self::SO_LAN_THU_LAI);
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

    /**
     * Cập nhật một lượt đặt lịch đang chờ duyệt (chỉ cho phép sửa khi chưa
     * duyệt/hủy/từ chối/hoàn thành — các trạng thái đó là kết quả của một
     * quyết định đã chốt, sửa nội dung sau đó sẽ làm sai lệch quyết định ấy).
     * Tính lại phí theo sức chứa hiện tại, không đổi trạng thái (vẫn giữ
     * nguyên Chờ duyệt, không "nhảy hàng" lên Đã duyệt).
     *
     * $data cần có: tien_ich, thoi_gian_bat_dau, thoi_gian_ket_thuc, so_nguoi.
     * $data có thể có: can_ho, ghi_chu.
     */
    public function capNhatDatLich(DatLichTienIch $datLich, array $data): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich, $data) {
            // Lock tien_ich trước, rồi đến chính dòng dat_lich_tien_ich — đúng thứ tự
            // cố định dùng xuyên suốt Service này để chống deadlock.
            $tienIch = TienIch::lockForUpdate()->findOrFail($data['tien_ich']);
            $datLich = DatLichTienIch::lockForUpdate()->findOrFail($datLich->id);

            $this->damBaoTrangThai($datLich, [DatLichTienIch::TRANG_THAI_CHO_DUYET], 'chỉnh sửa');

            if ((int) $tienIch->trang_thai !== TienIch::TRANG_THAI_HOAT_DONG) {
                throw ValidationException::withMessages([
                    'tien_ich' => ['Tiện ích hiện không hoạt động, không thể đặt lịch.'],
                ]);
            }

            $batDau  = Carbon::parse($data['thoi_gian_bat_dau']);
            $ketThuc = Carbon::parse($data['thoi_gian_ket_thuc']);
            $soNguoi = (int) $data['so_nguoi'];

            $old = $datLich->toArray();

            $datLich->update([
                'can_ho'             => $data['can_ho'] ?? null,
                'tien_ich'           => $tienIch->id,
                'thoi_gian_bat_dau'  => $batDau,
                'thoi_gian_ket_thuc' => $ketThuc,
                'so_nguoi'           => $soNguoi,
                'phi_su_dung'        => $this->capacityService->tinhPhi($tienIch, $soNguoi, $batDau, $ketThuc),
                'ghi_chu'            => $data['ghi_chu'] ?? null,
                'nguoi_cap_nhat'     => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            return $datLich->fresh();
        }, self::SO_LAN_THU_LAI);
    }

    // ─────────────────────────────────────────────────────────────
    //  Duyệt / từ chối / chuyển chờ duyệt
    // ─────────────────────────────────────────────────────────────

    /**
     * Nhân viên duyệt thủ công một lượt đang chờ duyệt. Kiểm tra lại sức chứa
     * tại thời điểm duyệt để tránh vượt sức chứa do nhiều yêu cầu cùng tranh
     * chấp một khung giờ. Idempotent: gọi lại trên booking đã Đã duyệt trả về
     * thành công, không xử lý lại (không đổi nhan_vien_duyet/ngay_duyet).
     */
    public function duyet(DatLichTienIch $datLich, ?int $nguoiDuyet = null): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich, $nguoiDuyet) {
            $tienIch = TienIch::withTrashed()->lockForUpdate()->findOrFail($datLich->tien_ich);
            $datLich = DatLichTienIch::lockForUpdate()->findOrFail($datLich->id);

            if ($this->daODungDichHoacGuard($datLich, DatLichTienIch::TRANG_THAI_DA_DUYET, [DatLichTienIch::TRANG_THAI_CHO_DUYET], 'duyệt')) {
                return $datLich;
            }

            $this->capacityService->kiemTraSucChua(
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

            $this->realtimeService->daDuyet($datLich);
            $this->realtimeService->hangChoThayDoi($tienIch->id);
            $this->realtimeService->slotThayDoi($tienIch->id, $datLich->thoi_gian_bat_dau, $datLich->thoi_gian_ket_thuc);

            return $datLich->fresh();
        }, self::SO_LAN_THU_LAI);
    }

    /**
     * Từ chối một lượt đang chờ duyệt. Không áp dụng cho lượt đã duyệt/đã
     * hủy/đã từ chối/hoàn thành — muốn dừng một lượt đã duyệt thì dùng huy().
     * Idempotent: gọi lại trên booking đã Từ chối trả về thành công.
     */
    public function tuChoi(DatLichTienIch $datLich, ?string $lyDo = null): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich, $lyDo) {
            $tienIch = TienIch::withTrashed()->lockForUpdate()->findOrFail($datLich->tien_ich);
            $datLich = DatLichTienIch::lockForUpdate()->findOrFail($datLich->id);

            if ($this->daODungDichHoacGuard($datLich, DatLichTienIch::TRANG_THAI_TU_CHOI, [DatLichTienIch::TRANG_THAI_CHO_DUYET], 'từ chối')) {
                return $datLich;
            }

            $old = $datLich->toArray();

            $datLich->update([
                'trang_thai'      => DatLichTienIch::TRANG_THAI_TU_CHOI,
                'nhan_vien_duyet' => auth('nhanvien')->id(),
                'ngay_huy'        => now(),
                'ly_do_huy'       => $lyDo,
                'nguoi_cap_nhat'  => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            $this->realtimeService->daTuChoi($datLich);
            $this->realtimeService->hangChoThayDoi($tienIch->id);

            return $datLich->fresh();
        }, self::SO_LAN_THU_LAI);
    }

    /**
     * Chuyển một lượt đã duyệt về lại trạng thái chờ duyệt (ví dụ nhân viên
     * cần xem xét lại trước khi diễn ra). Không áp dụng cho lượt đã hoàn
     * thành/đã hủy/đã từ chối vì đó là trạng thái cuối (terminal). Giải phóng
     * sức chứa nên xét FIFO ngay (có thể một booking khác đủ điều kiện được
     * duyệt thay vào khung giờ đó — booking này quay lại xếp hàng theo đúng
     * createdAt gốc, không "nhảy hàng").
     */
    public function chuyenChoDuyet(DatLichTienIch $datLich): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich) {
            $tienIch = TienIch::withTrashed()->lockForUpdate()->findOrFail($datLich->tien_ich);
            $datLich = DatLichTienIch::lockForUpdate()->findOrFail($datLich->id);

            if ($this->daODungDichHoacGuard($datLich, DatLichTienIch::TRANG_THAI_CHO_DUYET, [DatLichTienIch::TRANG_THAI_DA_DUYET], 'chuyển về chờ duyệt')) {
                return $datLich;
            }

            $old = $datLich->toArray();
            $batDau  = $datLich->thoi_gian_bat_dau;
            $ketThuc = $datLich->thoi_gian_ket_thuc;

            $datLich->update([
                'trang_thai'      => DatLichTienIch::TRANG_THAI_CHO_DUYET,
                'nhan_vien_duyet' => null,
                'ngay_duyet'      => null,
                'nguoi_cap_nhat'  => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            $this->realtimeService->hangChoThayDoi($tienIch->id);
            $this->realtimeService->slotThayDoi($tienIch->id, $batDau, $ketThuc);
            $this->fifoService->xuLyHangDoiChoTienIch($tienIch->id);

            return $datLich->fresh();
        }, self::SO_LAN_THU_LAI);
    }

    // ─────────────────────────────────────────────────────────────
    //  Hủy / hoàn thành
    // ─────────────────────────────────────────────────────────────

    /**
     * Hủy một lượt đặt đang chờ duyệt hoặc đã duyệt. Không thể hủy lượt đã
     * hoàn thành/đã hủy/đã từ chối. Idempotent: gọi lại trên booking đã hủy
     * trả về thành công.
     *
     * Quy tắc 2 giờ: CHỈ áp dụng khi booking đang Đã duyệt — Chờ duyệt hủy
     * được bất kỳ lúc nào (Chờ duyệt quá hạn 2h đã có Scheduler tự động hủy
     * riêng, xem BookingSchedulerService::tuDongHuyQuaHan()). Áp dụng như
     * nhau cho Admin/Manager/Resident.
     */
    public function huy(DatLichTienIch $datLich, ?string $lyDoHuy = null): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich, $lyDoHuy) {
            $tienIch = TienIch::withTrashed()->lockForUpdate()->findOrFail($datLich->tien_ich);
            $datLich = DatLichTienIch::lockForUpdate()->findOrFail($datLich->id);

            if ($this->daODungDichHoacGuard(
                $datLich,
                DatLichTienIch::TRANG_THAI_DA_HUY,
                [DatLichTienIch::TRANG_THAI_CHO_DUYET, DatLichTienIch::TRANG_THAI_DA_DUYET],
                'hủy'
            )) {
                return $datLich;
            }

            $dangDaDuyet = (int) $datLich->trang_thai === DatLichTienIch::TRANG_THAI_DA_DUYET;

            // Booking Đã duyệt chỉ được hủy khi còn cách giờ bắt đầu sử dụng ít
            // nhất 2 giờ — áp dụng chung cho Admin, Manager và Resident.
            if ($dangDaDuyet && now()->addHours(2)->gt($datLich->thoi_gian_bat_dau)) {
                throw ValidationException::withMessages([
                    'thoi_gian_bat_dau' => ['Bạn chỉ có thể hủy booking trước thời gian bắt đầu ít nhất 2 giờ.'],
                ]);
            }

            $old = $datLich->toArray();
            $batDau  = $datLich->thoi_gian_bat_dau;
            $ketThuc = $datLich->thoi_gian_ket_thuc;

            $datLich->update([
                'trang_thai'     => DatLichTienIch::TRANG_THAI_DA_HUY,
                'ngay_huy'       => now(),
                'ly_do_huy'      => $lyDoHuy,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            $this->realtimeService->daHuy($datLich);
            $this->realtimeService->hangChoThayDoi($tienIch->id);

            if ($dangDaDuyet) {
                // Vừa giải phóng chỗ — xét FIFO NGAY trong cùng transaction,
                // không chờ Scheduler.
                $this->realtimeService->slotThayDoi($tienIch->id, $batDau, $ketThuc);
                $this->fifoService->xuLyHangDoiChoTienIch($tienIch->id);
            }

            return $datLich->fresh();
        }, self::SO_LAN_THU_LAI);
    }

    /**
     * Cư dân tự hủy lượt đặt lịch của chính mình. Lý do hủy luôn cố định là
     * "Cư dân hủy lịch", không nhận lý do tự nhập từ Controller để tránh cư
     * dân ghi nội dung tùy ý vào log hệ thống. Việc kiểm tra $datLich có
     * thuộc về cư dân đang đăng nhập hay không là một quyết định
     * authorization (ai được phép gọi hành động này), không phải business
     * rule của bản thân việc hủy, nên được thực hiện ở Controller.
     */
    public function huyBoiCuDan(DatLichTienIch $datLich): DatLichTienIch
    {
        return $this->huy($datLich, 'Cư dân hủy lịch');
    }

    /**
     * Đánh dấu hoàn thành một lượt đặt đã duyệt (đã sử dụng tiện ích xong).
     * Idempotent: gọi lại trên booking đã Hoàn thành trả về thành công. Giải
     * phóng sức chứa nên xét FIFO ngay trong cùng transaction.
     */
    public function hoanThanh(DatLichTienIch $datLich): DatLichTienIch
    {
        return DB::transaction(function () use ($datLich) {
            $tienIch = TienIch::withTrashed()->lockForUpdate()->findOrFail($datLich->tien_ich);
            $datLich = DatLichTienIch::lockForUpdate()->findOrFail($datLich->id);

            if ($this->daODungDichHoacGuard($datLich, DatLichTienIch::TRANG_THAI_HOAN_THANH, [DatLichTienIch::TRANG_THAI_DA_DUYET], 'hoàn thành')) {
                return $datLich;
            }

            $old = $datLich->toArray();
            $batDau  = $datLich->thoi_gian_bat_dau;
            $ketThuc = $datLich->thoi_gian_ket_thuc;

            $datLich->update([
                'trang_thai'     => DatLichTienIch::TRANG_THAI_HOAN_THANH,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

            $this->realtimeService->daHoanThanh($datLich);
            $this->realtimeService->slotThayDoi($tienIch->id, $batDau, $ketThuc);
            $this->fifoService->xuLyHangDoiChoTienIch($tienIch->id);

            return $datLich->fresh();
        }, self::SO_LAN_THU_LAI);
    }

    // ─────────────────────────────────────────────────────────────
    //  Xóa / khôi phục
    // ─────────────────────────────────────────────────────────────

    public function xoa(DatLichTienIch $datLich): void
    {
        DB::transaction(function () use ($datLich) {
            $datLich = DatLichTienIch::lockForUpdate()
            ->findOrFail($datLich->id);

             $datLich->update([
            'trang_thai' => DatLichTienIch::TRANG_THAI_DA_HUY,
        ]);
            AuditLogService::log('DELETE', 'dat_lich_tien_ich', $datLich->id, $datLich->toArray(), null);
            $datLich->delete();
        }, self::SO_LAN_THU_LAI);
    }

    public function khoiPhuc(int $id): DatLichTienIch
    {
        return DB::transaction(function () use ($id) {
            $datLich = DatLichTienIch::withTrashed()->findOrFail($id);
            $datLich->restore();

            AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, null, $datLich->fresh()->toArray());

            return $datLich->fresh();
        }, self::SO_LAN_THU_LAI);
    }

    // ─────────────────────────────────────────────────────────────
    //  Private helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Guard dùng chung cho mọi hành động chuyển trạng thái, có xét Idempotent:
     * nếu $datLich ĐÃ ở đúng trạng thái đích của hành động (vd. duyệt lại một
     * booking đã Đã duyệt), trả về true và KHÔNG làm gì thêm (coi là thành
     * công, không lỗi, không audit log, không broadcast trùng). Nếu đang ở
     * một trạng thái khác không nằm trong $trangThaiNguonChoPhep, đây là
     * chuyển trạng thái không hợp lệ thật sự — vẫn throw như cũ.
     *
     * @param  int[]  $trangThaiNguonChoPhep
     * @return bool true nếu đã ở đúng trạng thái đích (no-op)
     */
    private function daODungDichHoacGuard(
        DatLichTienIch $datLich,
        int $trangThaiDich,
        array $trangThaiNguonChoPhep,
        string $hanhDong
    ): bool {
        $hienTai = (int) $datLich->trang_thai;

        if ($hienTai === $trangThaiDich) {
            return true;
        }

        if (!in_array($hienTai, $trangThaiNguonChoPhep, true)) {
            throw ValidationException::withMessages([
                'trang_thai' => ["Không thể {$hanhDong} lượt đặt lịch ở trạng thái hiện tại ({$datLich->trang_thai_label['text']})."],
            ]);
        }

        return false;
    }

    /**
     * Guard "cứng" (không idempotent) — dùng cho capNhatDatLich() vì sửa nội
     * dung không có khái niệm "trạng thái đích" để so sánh idempotent.
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
