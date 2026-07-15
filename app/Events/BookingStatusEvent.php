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
 * Base dùng chung cho 5 event thay đổi trạng thái 1 lượt đặt lịch cụ thể
 * (Created/Approved/Cancelled/Rejected/Completed) — tránh lặp lại channel
 * và payload ở từng event con, mỗi event con chỉ cần định danh broadcastAs().
 *
 * ShouldBroadcastNow (không phải ShouldBroadcast): broadcast ngay trong tiến
 * trình hiện tại, không qua hàng đợi — dự án chưa có bảng `jobs` (chưa từng
 * dùng queue thật) nên tránh phụ thuộc vào nó, đặc biệt vì DB kết nối tới
 * MySQL chia sẻ ở xa, không nên tự ý migrate thêm bảng hệ thống lên đó. Độ
 * trễ thêm vào response không đáng kể (broadcast tới Reverb qua HTTP nội bộ,
 * thường dưới 50ms).
 */
abstract class BookingStatusEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly DatLichTienIch $datLich)
    {
    }

    /**
     * Public channel theo tiện ích (số liệu, không dữ liệu cá nhân) + private channel
     * cho Admin/Manager (toàn bộ) + private channel riêng cho đúng cư dân sở hữu.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('tien-ich.'.$this->datLich->tien_ich),
            new PrivateChannel('dat-lich-tien-ich.nhanvien'),
            new PrivateChannel('dat-lich-tien-ich.cudan.'.$this->datLich->cu_dan),
        ];
    }

    public function broadcastWith(): array
    {
        $b = $this->datLich;

        return [
            'id'                 => $b->id,
            'ma_dat_lich'        => $b->ma_dat_lich,
            'tien_ich'           => $b->tien_ich,
            'ten_tien_ich'       => $b->tienIch?->ten_tien_ich,
            'cu_dan'             => $b->cu_dan,
            'ten_cu_dan'         => trim(($b->cuDan?->ho_ten_dem ?? '').' '.($b->cuDan?->ten ?? '')) ?: null,
            'can_ho'             => $b->can_ho,
            'so_can_ho'          => $b->canHo?->so_can_ho,
            'thoi_gian_bat_dau'  => $b->thoi_gian_bat_dau?->toIso8601String(),
            'thoi_gian_ket_thuc' => $b->thoi_gian_ket_thuc?->toIso8601String(),
            'so_nguoi'           => $b->so_nguoi,
            'phi_su_dung'        => (float) $b->phi_su_dung,
            'trang_thai'         => $b->trang_thai,
            'trang_thai_label'   => $b->trang_thai_label,
            'ly_do_huy'          => $b->ly_do_huy,
        ];
    }
}
