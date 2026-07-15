<?php

namespace App\Services;

use App\Events\BookingApproved;
use App\Events\BookingCancelled;
use App\Events\BookingCompleted;
use App\Events\BookingCreated;
use App\Events\BookingRejected;
use App\Events\BookingSlotUpdated;
use App\Events\BookingWaitingChanged;
use App\Models\DatLichTienIch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Nơi DUY NHẤT của module gọi DB::afterCommit() + broadcast() — mọi service khác
 * (Approval/Fifo/Scheduler) gọi qua đây thay vì tự broadcast trực tiếp, để đảm bảo
 * không lặp code và tuyệt đối không có broadcast nào lọt ra ngoài khi transaction
 * rollback (afterCommit() chỉ chạy callback nếu transaction NGOÀI CÙNG commit thành
 * công; nếu rollback, callback bị huỷ hoàn toàn, không hề chạy).
 */
class BookingRealtimeService
{
    public function daTao(DatLichTienIch $datLich): void
    {
        $datLich->loadMissing(['tienIch', 'cuDan', 'canHo']);
        DB::afterCommit(function () use ($datLich) {
            broadcast(new BookingCreated($datLich));
            Log::info('booking.created', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
        });
    }

    public function daDuyet(DatLichTienIch $datLich): void
    {
        $datLich->loadMissing(['tienIch', 'cuDan', 'canHo']);
        DB::afterCommit(function () use ($datLich) {
            broadcast(new BookingApproved($datLich));
            Log::info('booking.approved', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
        });
    }

    public function daHuy(DatLichTienIch $datLich): void
    {
        $datLich->loadMissing(['tienIch', 'cuDan', 'canHo']);
        DB::afterCommit(function () use ($datLich) {
            broadcast(new BookingCancelled($datLich));
            Log::info('booking.cancelled', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
        });
    }

    public function daTuChoi(DatLichTienIch $datLich): void
    {
        $datLich->loadMissing(['tienIch', 'cuDan', 'canHo']);
        DB::afterCommit(function () use ($datLich) {
            broadcast(new BookingRejected($datLich));
            Log::info('booking.rejected', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
        });
    }

    public function daHoanThanh(DatLichTienIch $datLich): void
    {
        $datLich->loadMissing(['tienIch', 'cuDan', 'canHo']);
        DB::afterCommit(function () use ($datLich) {
            broadcast(new BookingCompleted($datLich));
            Log::info('booking.completed', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
        });
    }

    /**
     * Hàng chờ của 1 tiện ích vừa thay đổi số lượng (không nhất thiết đổi sức chứa).
     */
    public function hangChoThayDoi(int $tienIchId): void
    {
        DB::afterCommit(function () use ($tienIchId) {
            broadcast(new BookingWaitingChanged($tienIchId));
        });
    }

    /**
     * Sức chứa của 1 khung giờ cụ thể trên 1 tiện ích vừa thay đổi (duyệt/hủy/từ
     * chối/hoàn thành một booking Đã duyệt).
     */
    public function slotThayDoi(int $tienIchId, Carbon $batDau, Carbon $ketThuc): void
    {
        DB::afterCommit(function () use ($tienIchId, $batDau, $ketThuc) {
            broadcast(new BookingSlotUpdated($tienIchId, $batDau, $ketThuc));
        });
    }

    /**
     * Dùng khi FIFO service tự động duyệt nhiều lượt trong 1 lượt quét — ghi log
     * riêng để phân biệt với duyệt thủ công (đã log ở daDuyet()).
     */
    public function logFifoApproved(int $tienIchId, int $datLichId, string $maDatLich): void
    {
        Log::info('booking.fifo-approved', [
            'tien_ich'    => $tienIchId,
            'id'          => $datLichId,
            'ma_dat_lich' => $maDatLich,
        ]);
    }

    public function logDeadlockRetry(string $thaoTac, int $lanThu): void
    {
        Log::warning('booking.deadlock-retry', ['thao_tac' => $thaoTac, 'lan_thu' => $lanThu]);
    }
}
