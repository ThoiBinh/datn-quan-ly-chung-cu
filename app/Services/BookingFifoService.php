<?php

namespace App\Services;

use App\Models\DatLichTienIch;
use App\Models\TienIch;
use Illuminate\Support\Facades\DB;

/**
 * Xử lý hàng đợi FIFO tuyệt đối (ai đặt trước, xét trước) của các lượt đang Chờ
 * duyệt. Được gọi ở 2 nơi:
 *   - ĐỒNG BỘ, ngay trong transaction của BookingApprovalService, mỗi khi một
 *     booking Đã duyệt giải phóng chỗ (hủy/hoàn thành) — đây là cơ chế FIFO
 *     CHÍNH ("hủy ngay khi hủy, không chờ Scheduler, gọi lại xét hàng chờ").
 *   - Từ BookingSchedulerService (auto-approve mỗi phút) — quét theo FIFO
 *     tuyệt đối cho các booking Chờ duyệt, dừng lại đúng lúc booking đầu tiên
 *     không đủ chỗ.
 */
class BookingFifoService
{
    public function __construct(
        private readonly BookingCapacityService $capacityService,
        private readonly BookingRealtimeService $realtimeService,
    ) {
    }

    /**
     * Quét TOÀN BỘ tiện ích đang có booking Chờ duyệt (dùng cho Scheduler
     * auto-approve mỗi phút).
     *
     * @return int Tổng số lượt đã tự động duyệt (cộng dồn mọi tiện ích).
     */
    public function xuLyToanBo(): int
    {
        $tienIchIds = DatLichTienIch::query()
            ->where('trang_thai', DatLichTienIch::TRANG_THAI_CHO_DUYET)
            ->distinct()
            ->pluck('tien_ich');

        $soLuong = 0;

        foreach ($tienIchIds as $tienIchId) {
            $soLuong += $this->xuLyHangDoiChoTienIch((int) $tienIchId);
        }

        return $soLuong;
    }

    /**
     * Xử lý hàng đợi FIFO của MỘT tiện ích. Vì "dừng khi không đủ chỗ" chỉ có
     * ý nghĩa giữa các booking THỰC SỰ tranh chấp cùng một khung giờ, hàng
     * đợi được chia thành từng CỤM độc lập bằng gomNhomGiaoNhau() (các booking
     * có khung giờ giao nhau, kể cả bắc cầu qua booking khác, gộp chung 1
     * cụm). Trong mỗi cụm, xét đúng thứ tự tạo trước (created_at ASC, id ASC
     * — FIFO tuyệt đối) và DỪNG hẳn cụm đó ngay khi gặp booking đầu tiên
     * không đủ chỗ — không xét các booking phía sau CÙNG CỤM, kể cả khi tự
     * chúng đủ chỗ. Nhưng một cụm bị chặn KHÔNG ảnh hưởng tới cụm khác (khung
     * giờ không liên quan gì nhau) — cụm đó vẫn được xét và duyệt bình thường
     * trong cùng lượt chạy.
     *
     * Trong 1 cụm: booking nào đủ chỗ thì duyệt ngay (tuDongDuyet() bên dưới)
     * rồi mới xét booking kế tiếp — nhờ vậy tongNguoiDaDuyetGiaoNhau() ở vòng
     * lặp sau tự động thấy cả những booking vừa được duyệt trước đó trong
     * CÙNG lượt chạy này.
     *
     * lockForUpdate() trên tien_ich đảm bảo lượt quét này không chồng lấn với
     * một request khác đang xử lý đồng thời cùng tiện ích. Khi được gọi từ
     * BÊN TRONG một transaction đã lock sẵn tien_ich này (từ
     * BookingApprovalService), DB::transaction() lồng nhau tự động dùng
     * SAVEPOINT và việc lockForUpdate() lại đúng dòng đã lock là vô hại
     * (cùng connection/transaction, InnoDB không tự-deadlock với chính nó).
     */
    public function xuLyHangDoiChoTienIch(int $tienIchId): int
    {
        return DB::transaction(function () use ($tienIchId) {
            $tienIch = TienIch::withTrashed()->lockForUpdate()->find($tienIchId);

            if (!$tienIch) {
                return 0;
            }

            $hangDoi = DatLichTienIch::query()
                ->where('tien_ich', $tienIchId)
                ->where('trang_thai', DatLichTienIch::TRANG_THAI_CHO_DUYET)
                ->lockForUpdate()
                ->get();

            $soLuong = 0;

            foreach ($this->gomNhomGiaoNhau($hangDoi) as $nhom) {
                // Trong 1 cụm: xét đúng thứ tự tạo trước (FIFO tuyệt đối)
                $theoThuTuTao = $nhom
                    ->sortBy(fn (DatLichTienIch $dl) => [$dl->createdAt->getTimestamp(), $dl->id])
                    ->values();

                foreach ($theoThuTuTao as $datLich) {
                    $tongDaDuyet = $this->capacityService->tongNguoiDaDuyetGiaoNhau(
                        $tienIchId,
                        $datLich->thoi_gian_bat_dau,
                        $datLich->thoi_gian_ket_thuc
                    );

                    if ($tienIch->suc_chua && ($tongDaDuyet + $datLich->so_nguoi > $tienIch->suc_chua)) {
                        break; // Dừng hẳn CỤM này, không xét booking phía sau CÙNG CỤM
                    }

                    $this->tuDongDuyet($datLich);
                    $this->realtimeService->logFifoApproved($tienIchId, $datLich->id, $datLich->ma_dat_lich);
                    $soLuong++;
                }
            }

            return $soLuong;
        }, 3);
    }

    /**
     * Hệ thống tự động duyệt một lượt đang chờ duyệt (không gắn nhân viên duyệt
     * cụ thể) trong lúc quét FIFO. Idempotent: nếu vì lý do nào đó booking không
     * còn ở Chờ duyệt nữa (đã bị hủy/từ chối bởi một request khác), bỏ qua êm
     * thấm thay vì lỗi — dòng đã được lockForUpdate() cùng transaction ở
     * xuLyHangDoiChoTienIch() nên đây là dữ liệu mới nhất, không phải dữ liệu cũ.
     */
    private function tuDongDuyet(DatLichTienIch $datLich): void
    {
        if ((int) $datLich->trang_thai !== DatLichTienIch::TRANG_THAI_CHO_DUYET) {
            return;
        }

        $old = $datLich->toArray();

        $datLich->update([
            'trang_thai'      => DatLichTienIch::TRANG_THAI_DA_DUYET,
            'nhan_vien_duyet' => null,
            'ngay_duyet'      => now(),
        ]);

        AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLich->id, $old, $datLich->fresh()->toArray());

        $this->realtimeService->daDuyet($datLich);
        $this->realtimeService->hangChoThayDoi((int) $datLich->tien_ich);
        $this->realtimeService->slotThayDoi(
            (int) $datLich->tien_ich,
            $datLich->thoi_gian_bat_dau,
            $datLich->thoi_gian_ket_thuc
        );
    }

    /**
     * Gom các booking có khung giờ giao nhau (trực tiếp hoặc bắc cầu qua một
     * booking trung gian) thành từng cụm độc lập — thuật toán "merge
     * overlapping intervals" kinh điển: sắp theo thoi_gian_bat_dau tăng dần,
     * duyệt tuần tự; một booking thuộc cụm đang mở nếu nó bắt đầu TRƯỚC mốc
     * kết thúc xa nhất đã thấy trong cụm đó (đúng ngữ nghĩa giao nhau s1 < e2
     * dùng xuyên suốt Service này — hai booking nối đuôi chạm đúng mốc giờ
     * KHÔNG được coi là giao nhau, sẽ tách thành 2 cụm riêng).
     *
     * Public vì BookingSchedulerService::tuDongHuyQuaHan() cũng cần gom cụm y
     * hệt để xét FIFO (duyệt/hủy xen kẽ, tính lại sức chứa sau mỗi bước) —
     * tránh chép lại thuật toán gom cụm ở hai nơi.
     *
     * @param  \Illuminate\Support\Collection<int, DatLichTienIch>  $danhSach
     * @return \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, DatLichTienIch>>
     */
    public function gomNhomGiaoNhau($danhSach)
    {
        $daSapXep = $danhSach
            ->sortBy(fn (DatLichTienIch $dl) => [
                $dl->thoi_gian_bat_dau->getTimestamp(),
                $dl->thoi_gian_ket_thuc->getTimestamp(),
            ])
            ->values();

        $cacCum = collect();
        $cumHienTai = collect();
        $mocKetThucXaNhat = null;

        foreach ($daSapXep as $datLich) {
            $thuocCumHienTai = $mocKetThucXaNhat !== null
                && $datLich->thoi_gian_bat_dau->lt($mocKetThucXaNhat);

            if (!$thuocCumHienTai && $cumHienTai->isNotEmpty()) {
                $cacCum->push($cumHienTai);
                $cumHienTai = collect();
            }

            $cumHienTai->push($datLich);

            $mocKetThucXaNhat = ($mocKetThucXaNhat === null || $datLich->thoi_gian_ket_thuc->gt($mocKetThucXaNhat))
                ? $datLich->thoi_gian_ket_thuc
                : $mocKetThucXaNhat;
        }

        if ($cumHienTai->isNotEmpty()) {
            $cacCum->push($cumHienTai);
        }

        return $cacCum;
    }
}
