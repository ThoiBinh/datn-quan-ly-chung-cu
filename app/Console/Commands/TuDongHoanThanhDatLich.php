<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class TuDongHoanThanhDatLich extends Command
{
    protected $signature = 'dat-lich-tien-ich:auto-complete';

    protected $description = 'Tự động đánh dấu Hoàn thành các lượt đặt lịch tiện ích Đã duyệt đã qua giờ kết thúc';

    public function handle(BookingService $bookingService): int
    {
        $soLuong = $bookingService->tuDongHoanThanh();

        $this->info("Đã tự động đánh dấu hoàn thành {$soLuong} lượt đặt lịch.");

        return self::SUCCESS;
    }
}
    