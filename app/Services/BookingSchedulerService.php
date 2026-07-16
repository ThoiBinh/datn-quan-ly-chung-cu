<?php

namespace App\Services;

use App\Models\DatLichTienIch;
use App\Models\TienIch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Ba job Scheduler của module:
 *   - auto-approve (mỗi phút): quét FIFO tuyệt đối cho các booking Chờ duyệt.
 *   - auto-cancel (mỗi phút): quét FIFO tuyệt đối, xen kẽ duyệt/hủy, tính lại
 *     sức chứa sau mỗi bước — xem tuDongHuyQuaHan() bên dưới.
 *   - auto-complete (mỗi phút): Đã duyệt đã hết giờ -> Hoàn thành.
 *
 * Cả ba job đều tái sử dụng đúng state machine của BookingApprovalService
 * (lock, transaction, idempotent, audit log, realtime, và — với hoàn thành —
 * xét FIFO ngay trong cùng transaction) thay vì tự viết lại logic chuyển
 * trạng thái ở đây.
 */
class BookingSchedulerService
{
    private const SO_LAN_THU_LAI = 3;

    public function __construct(
        private readonly BookingApprovalService $approvalService,
        private readonly BookingFifoService $fifoService,
        private readonly BookingCapacityService $capacityService,
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
     * (thoi_gian_bat_dau) VÀ không còn đủ sức chứa — hệ thống không kịp cho
     * nhân viên xét duyệt trước giờ diễn ra thì coi như hủy, tránh giữ chỗ
     * "treo" vô thời hạn.
     *
     * Quét đúng FIFO tuyệt đối theo từng cụm khung giờ giao nhau (dùng lại
     * BookingFifoService::gomNhomGiaoNhau()) và XÉT LẠI SỨC CHỨA sau MỖI bước
     * duyệt/hủy — không dùng kết quả đã tính trước đó:
     *   - Đủ sức chứa tại thời điểm xét -> duyệt ngay (approvalService::duyet()),
     *     rồi xét tiếp booking kế tiếp trong cụm.
     *   - Không đủ sức chứa:
     *     - Còn <= 2 giờ đến giờ sử dụng -> hủy (approvalService::huy(), có
     *       audit log + broadcast qua Service đó), rồi TIẾP TỤC xét booking kế
     *       tiếp (không dừng cụm) — booking sau có thể ít người hơn và vừa đủ
     *       chỗ sau khi booking đầu bị loại khỏi hàng đợi.
     *     - Còn > 2 giờ -> chưa đến hạn xử lý, dừng hẳn cụm này để giữ đúng
     *       FIFO (không cho booking sau "vượt mặt" một booking vẫn còn cơ hội
     *       được duyệt sau).
     *
     * @return int Số lượt đã tự động hủy.
     */
    public function tuDongHuyQuaHan(): int
    {
        $mocGio = now()->addHours(2);

        $tienIchIds = DatLichTienIch::query()
            ->where('trang_thai', DatLichTienIch::TRANG_THAI_CHO_DUYET)
            ->where('thoi_gian_bat_dau', '<=', $mocGio)
            ->distinct()
            ->pluck('tien_ich');

        $soLuongHuy = 0;

        foreach ($tienIchIds as $tienIchId) {
            $soLuongHuy += $this->xuLyHangDoiVaHuyQuaHanChoTienIch((int) $tienIchId, $mocGio);
        }

        return $soLuongHuy;
    }

    /**
     * Xử lý FIFO tuyệt đối (duyệt/hủy xen kẽ) cho MỘT tiện ích, trong một
     * transaction duy nhất. lockForUpdate() trên tien_ich rồi trên toàn bộ
     * hàng đợi Chờ duyệt của nó đảm bảo không có request khác (tạo/duyệt/hủy
     * thủ công) chen vào giữa lúc đang tính lại sức chứa từng bước — chống
     * race condition/overbook.
     */
    private function xuLyHangDoiVaHuyQuaHanChoTienIch(int $tienIchId, Carbon $mocGio): int
    {
        return DB::transaction(function () use ($tienIchId, $mocGio) {
            $tienIch = TienIch::withTrashed()->lockForUpdate()->find($tienIchId);

            if (!$tienIch) {
                return 0;
            }

            $hangDoi = DatLichTienIch::query()
                ->where('tien_ich', $tienIchId)
                ->where('trang_thai', DatLichTienIch::TRANG_THAI_CHO_DUYET)
                ->lockForUpdate()
                ->get();

            $soLuongHuy = 0;

            foreach ($this->fifoService->gomNhomGiaoNhau($hangDoi) as $nhom) {
                // FIFO tuyệt đối trong cụm: createdAt ASC, id ASC.
                $hangDoiCum = $nhom
                    ->sortBy(fn (DatLichTienIch $dl) => [$dl->createdAt->getTimestamp(), $dl->id])
                    ->values();

                while ($hangDoiCum->isNotEmpty()) {
                    $datLich = $hangDoiCum->shift();

                    // Luôn tính lại tại thời điểm hiện tại, không dùng số đã
                    // tính trước đó (kể cả của vòng lặp trước).
                    $tongDaDuyet = $this->capacityService->tongNguoiDaDuyetGiaoNhau(
                        $tienIchId,
                        $datLich->thoi_gian_bat_dau,
                        $datLich->thoi_gian_ket_thuc
                    );

                    $conDuChoNgay = !$tienIch->suc_chua
                        || ($tongDaDuyet + $datLich->so_nguoi <= $tienIch->suc_chua);

                    if ($conDuChoNgay) {
                        $this->approvalService->duyet($datLich);

                        continue; // Xét tiếp booking kế tiếp cùng cụm.
                    }

                    if (!$datLich->thoi_gian_bat_dau->gt($mocGio)) {
                        $this->approvalService->huy(
                            $datLich,
                            'Hệ thống tự động hủy do không đủ sức chứa trước giờ sử dụng 2 tiếng.'
                        );
                        $soLuongHuy++;

                        continue; // Không dừng cụm: xét lại sức chứa cho booking kế tiếp.
                    }

                    break; // Chưa tới hạn 2h -> dừng hẳn cụm này, giữ đúng FIFO.
                }
            }

            return $soLuongHuy;
        }, self::SO_LAN_THU_LAI);
    }
}
