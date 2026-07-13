<?php

namespace App\Services;

use App\Models\DatLichTienIch;

/**
 * Ba job Scheduler của module:
 *   - auto-approve (mỗi phút): quét FIFO tuyệt đối cho các booking Chờ duyệt.
 *   - auto-cancel (mỗi phút): Chờ duyệt còn <=2 giờ đến giờ dùng -> Hủy.
 *   - auto-complete (mỗi phút): Đã duyệt đã hết giờ -> Hoàn thành.
 *
 * Cả hai job auto-cancel/auto-complete đều tái sử dụng đúng state machine của
 * BookingApprovalService (lock, transaction, idempotent, audit log, realtime,
 * và — với hoàn thành — xét FIFO ngay trong cùng transaction) thay vì tự viết
 * lại logic chuyển trạng thái ở đây.
 */
class BookingSchedulerService
{
    public function __construct(
        private readonly BookingApprovalService $approvalService,
        private readonly BookingFifoService $fifoService,
    ) {
    }

    /**
     * Auto approve: quét FIFO tuyệt đối cho MỌI tiện ích đang có booking Chờ
     * duyệt, theo đúng thứ tự tạo trước; nếu booking đầu tiên của một cụm
     * không đủ chỗ thì dừng hẳn cụm đó, không xét các booking phía sau.
     *
     * @return int Tổng số lượt đã tự động duyệt.
     */
    public function tuDongDuyetTheoFifo(): int
    {
        return $this->fifoService->xuLyToanBo();
    }

    /**
     * Tự động đánh dấu Hoàn thành các lượt Đã duyệt mà đã qua giờ kết thúc
     * (thoi_gian_ket_thuc < now()). hoanThanh() tự xét FIFO ngay khi giải
     * phóng chỗ, không cần làm thêm gì ở đây.
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
                $this->approvalService->hoanThanh($datLich);
                $soLuong++;
            });

        return $soLuong;
    }

    /**
     * Tự động HỦY các lượt đang Chờ duyệt mà chỉ còn <= 2 giờ đến giờ sử dụng
     * (thoi_gian_bat_dau) — hệ thống không kịp cho nhân viên xét duyệt trước
     * giờ diễn ra thì coi như hủy, tránh giữ chỗ "treo" vô thời hạn. Tái sử
     * dụng huy() nên vẫn qua đúng state-machine guard, idempotent và audit
     * log như hủy thủ công (lưu ngay_huy, ly_do_huy).
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
                $this->approvalService->huy(
                    $datLich,
                    'Hệ thống tự động hủy do không đủ sức chứa trước giờ sử dụng 2 tiếng.'
                );
                $soLuong++;
            });

        return $soLuong;
    }
}
