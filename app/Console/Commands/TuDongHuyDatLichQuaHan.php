<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class TuDongHuyDatLichQuaHan extends Command
{
    protected $signature = 'dat-lich-tien-ich:auto-cancel';

    protected $description = 'Tự động hủy các lượt đặt lịch tiện ích đang Chờ duyệt mà chỉ còn <= 2 giờ đến giờ sử dụng';

    public function handle(BookingService $bookingService): int
    {
        $soLuong = $bookingService->tuDongHuyQuaHan();

        $this->info("Đã tự động hủy {$soLuong} lượt đặt lịch quá hạn duyệt.");

        return self::SUCCESS;
    }
}
