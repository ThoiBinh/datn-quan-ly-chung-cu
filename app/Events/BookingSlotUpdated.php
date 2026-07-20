<?php

namespace App\Events;

use App\Models\TienIch;
use App\Services\BookingCapacityService;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Sức chứa của MỘT khung giờ cụ thể trên một tiện ích vừa thay đổi (duyệt/hủy/từ chối/
 * hoàn thành làm giải phóng hoặc chiếm chỗ) — payload mang sẵn số liệu còn lại đã tính
 * lại ngay tại thời điểm broadcast, để trang tạo lịch tự bật/tắt nút "Đặt lịch" mà
 * không cần gọi thêm request nào khác.
 */
class BookingSlotUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $tienIchId,
        public readonly Carbon $thoiGianBatDau,
        public readonly Carbon $thoiGianKetThuc,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('tien-ich.'.$this->tienIchId),
            new PrivateChannel('dat-lich-tien-ich.nhanvien'),
        ];
    }

    public function broadcastWith(): array
    {
        $tienIch = TienIch::withTrashed()->find($this->tienIchId);
        $daDuyet = $tienIch
            ? resolve(BookingCapacityService::class)->tongNguoiDaDuyetGiaoNhau(
                $this->tienIchId,
                $this->thoiGianBatDau,
                $this->thoiGianKetThuc
            )
            : 0;
        $sucChua = $tienIch?->suc_chua ?? 0;

        return [
            'tien_ich'           => $this->tienIchId,
            'thoi_gian_bat_dau'  => $this->thoiGianBatDau->toIso8601String(),
            'thoi_gian_ket_thuc' => $this->thoiGianKetThuc->toIso8601String(),
            'suc_chua'           => (int) $sucChua,
            'da_duyet'           => $daDuyet,
            'con_lai'            => $sucChua ? max(0, $sucChua - $daDuyet) : null,
        ];
    }

    public function broadcastAs(): string
    {
        return 'booking.slot-updated';
    }
}
