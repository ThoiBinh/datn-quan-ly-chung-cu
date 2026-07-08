<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Đặt lịch tiện ích — tự động hủy các lượt Chờ duyệt sắp đến giờ mà chưa được duyệt.
Schedule::command('dat-lich-tien-ich:auto-cancel')
    ->everyMinute()
    ->withoutOverlapping();

// Đặt lịch tiện ích — tự động đánh dấu Hoàn thành các lượt Đã duyệt đã qua giờ kết thúc.
Schedule::command('dat-lich-tien-ich:auto-complete')
    ->everyMinute()
    ->withoutOverlapping();
