<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class TuDongDuyetDatLichFifo extends Command
{
    protected $signature = 'dat-lich-tien-ich:auto-approve';

    protected $description = 'Tự động duyệt các lượt đặt lịch tiện ích đang Chờ duyệt theo thứ tự FIFO (ai đặt trước, xét trước)';

    public function handle(BookingService $bookingService): int
    {
        $soLuong = $bookingService->tuDongDuyetTheoFifo();

        $this->info("Đã tự động duyệt {$soLuong} lượt đặt lịch theo FIFO.");

        return self::SUCCESS;
    }
}
