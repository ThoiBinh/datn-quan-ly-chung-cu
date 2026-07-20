<?php

namespace App\Services;

use App\Events\BookingApproved;
use App\Events\BookingCancelled;
use App\Events\BookingCompleted;
use App\Events\BookingCreated;
use App\Events\BookingRejected;
use App\Events\BookingRescheduled;
use App\Events\BookingSlotUpdated;
use App\Events\BookingWaitingChanged;
use App\Models\DatLichTienIch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
        [$actorLoai, $actorId] = $this->nguoiThucHien();
        DB::afterCommit(function () use ($datLich, $actorLoai, $actorId) {
            broadcast(new BookingCreated($datLich, $actorLoai, $actorId));
            Log::info('booking.created', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
        });
    }

    public function daDuyet(DatLichTienIch $datLich): void
    {
        $datLich->loadMissing(['tienIch', 'cuDan', 'canHo']);
        [$actorLoai, $actorId] = $this->nguoiThucHien();
        DB::afterCommit(function () use ($datLich, $actorLoai, $actorId) {
            broadcast(new BookingApproved($datLich, $actorLoai, $actorId));
            Log::info('booking.approved', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
        });
    }

    public function daHuy(DatLichTienIch $datLich): void
    {
        $datLich->loadMissing(['tienIch', 'cuDan', 'canHo']);
        [$actorLoai, $actorId] = $this->nguoiThucHien();
        DB::afterCommit(function () use ($datLich, $actorLoai, $actorId) {
            broadcast(new BookingCancelled($datLich, $actorLoai, $actorId));
            Log::info('booking.cancelled', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
        });
    }

    public function daTuChoi(DatLichTienIch $datLich): void
    {
        $datLich->loadMissing(['tienIch', 'cuDan', 'canHo']);
        [$actorLoai, $actorId] = $this->nguoiThucHien();
        DB::afterCommit(function () use ($datLich, $actorLoai, $actorId) {
            broadcast(new BookingRejected($datLich, $actorLoai, $actorId));
            Log::info('booking.rejected', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
        });
    }

    public function daHoanThanh(DatLichTienIch $datLich): void
    {
        $datLich->loadMissing(['tienIch', 'cuDan', 'canHo']);
        [$actorLoai, $actorId] = $this->nguoiThucHien();
        DB::afterCommit(function () use ($datLich, $actorLoai, $actorId) {
            broadcast(new BookingCompleted($datLich, $actorLoai, $actorId));
            Log::info('booking.completed', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
        });
    }

    /**
     * Giờ sử dụng của MỘT lượt đặt lịch đang Chờ duyệt vừa được sửa (chỉ khi
     * thoi_gian_bat_dau/thoi_gian_ket_thuc thực sự đổi — xem
     * BookingApprovalService::capNhatDatLich()) — dùng cho Toast Notification
     * báo "bên còn lại" (Ban quản lý hoặc cư dân sở hữu) biết giờ đặt đã đổi.
     */
    public function daCapNhatThoiGian(DatLichTienIch $datLich): void
    {
        $datLich->loadMissing(['tienIch', 'cuDan', 'canHo']);
        [$actorLoai, $actorId] = $this->nguoiThucHien();
        DB::afterCommit(function () use ($datLich, $actorLoai, $actorId) {
            broadcast(new BookingRescheduled($datLich, $actorLoai, $actorId));
            Log::info('booking.rescheduled', ['id' => $datLich->id, 'ma_dat_lich' => $datLich->ma_dat_lich]);
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

    /**
     * Xác định ai vừa gây ra thay đổi trạng thái NGAY tại thời điểm gọi (trước
     * khi vào DB::afterCommit()) — dựa vào guard nào đang đăng nhập trong
     * request hiện tại. Không có request nào đang đăng nhập (Scheduler/FIFO
     * chạy từ Console, không gắn với người dùng) -> 'system'.
     *
     * @return array{0: string, 1: ?int} [actorLoai, actorId]
     */
    private function nguoiThucHien(): array
    {
        if (Auth::guard('nhanvien')->check()) {
            return ['nhanvien', Auth::guard('nhanvien')->id()];
        }

        if (Auth::guard('cudan')->check()) {
            return ['cudan', Auth::guard('cudan')->id()];
        }

        return ['system', null];
    }
}
