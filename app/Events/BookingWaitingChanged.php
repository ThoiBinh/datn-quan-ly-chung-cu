<?php

namespace App\Events;

use App\Models\DatLichTienIch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Hàng chờ (Chờ duyệt) của MỘT tiện ích vừa thay đổi số lượng (thêm mới, được duyệt,
 * bị từ chối/hủy, hoặc tự động hủy do quá hạn) — dùng để cập nhật số đếm "Chờ duyệt"
 * trên danh sách/dashboard mà không cần tải lại trang.
 */
class BookingWaitingChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly int $tienIchId)
    {
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
        return [
            'tien_ich'           => $this->tienIchId,
            'so_luong_cho_duyet' => DatLichTienIch::query()
                ->where('tien_ich', $this->tienIchId)
                ->where('trang_thai', DatLichTienIch::TRANG_THAI_CHO_DUYET)
                ->count(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'booking.waiting-changed';
    }
}
