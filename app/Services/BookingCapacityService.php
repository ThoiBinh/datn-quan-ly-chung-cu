<?php

namespace App\Services;

use App\Models\DatLichTienIch;
use App\Models\TienIch;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Tính phí và tính/kiểm tra sức chứa của một tiện ích cho một khung giờ — thuần đọc,
 * không thay đổi dữ liệu, không tự mở transaction (caller chịu trách nhiệm
 * lockForUpdate()+transaction nếu cần dữ liệu nhất quán khi ghi).
 */
class BookingCapacityService
{
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

    /**
     * Tính xem một khung giờ CÓ ĐANG còn đủ sức chứa hay không (tại thời điểm
     * gọi, không xét thứ tự tạo):
     *
     *   tổng so_nguoi của các booking ĐÃ DUYỆT giao nhau về thời gian + so_nguoi mới
     *       <= suc_chua  → Đã duyệt
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
     * mọi kiểu chồng lấn (một phần, lồng nhau, trùng khít — A bắt đầu trong
     * B, A kết thúc trong B, A bao toàn B, B bao toàn A) và loại đúng trường
     * hợp hai lượt nối đuôi sát giờ (chạm mốc, không thực sự chồng chỗ).
     *
     * Gộp thành 1 câu SUM ở tầng DB (không fetch rồi cộng tay ở PHP), lọc theo
     * tien_ich trước để tận dụng index idx_dltl_tienich_thoigian hiện có.
     */
    public function tongNguoiDaDuyetGiaoNhau(int $tienIchId, Carbon $batDau, Carbon $ketThuc): int
    {
        return (int) DatLichTienIch::query()
            ->where('tien_ich', $tienIchId)
            ->where('trang_thai', DatLichTienIch::TRANG_THAI_DA_DUYET)
            ->where('thoi_gian_bat_dau', '<', $ketThuc)
            ->where('thoi_gian_ket_thuc', '>', $batDau)
            ->sum('so_nguoi');
    }
}
