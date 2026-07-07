<?php

namespace Tests\Feature\DatLichTienIch;

use App\Models\DatLichTienIch;
use App\Services\BookingService;
use Illuminate\Support\Facades\Artisan;

class BookingSchedulerTest extends DatLichTienIchTestCase
{
    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingService();
    }

    public function test_auto_cancel_huy_dung_booking_cho_duyet_trong_pham_vi_2_gio(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        // A: Cho duyet, con 1 gio nua bat dau (<=2h) -> phai bi huy
        $a = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHour()->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'so_nguoi' => 999, // vuot suc chua -> Cho duyet
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $a->trang_thai);

        // B: Cho duyet, con 3 gio nua bat dau (>2h) -> khong duoc dong toi
        $b = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHours(3)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(4)->format('Y-m-d H:i:s'),
            'so_nguoi' => 999,
        ]);

        // C: Da duyet, con 1 gio -> khong bi huy vi khac trang thai
        $c = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHour()->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $c->trang_thai);

        $soLuong = $this->service->tuDongHuyQuaHan();

        $this->assertSame(1, $soLuong);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_HUY, $a->fresh()->trang_thai);
        $this->assertStringContainsString('quá hạn duyệt', $a->fresh()->ly_do_huy);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $b->fresh()->trang_thai);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $c->fresh()->trang_thai);
    }

    public function test_auto_complete_hoan_thanh_dung_booking_da_qua_gio_ket_thuc(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        // D: Da duyet, da qua gio ket thuc -> phai tu dong hoan thanh
        $d = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->subHours(3)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->subHour()->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $d->trang_thai);

        // E: Da duyet, chua den gio ket thuc -> khong doi
        $e = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHours(5)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(6)->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);

        $soLuong = $this->service->tuDongHoanThanh();

        $this->assertSame(1, $soLuong);
        $this->assertSame(DatLichTienIch::TRANG_THAI_HOAN_THANH, $d->fresh()->trang_thai);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $e->fresh()->trang_thai);
    }

    public function test_scheduler_idempotent_khong_xu_ly_trung_lap(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHour()->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'so_nguoi' => 999,
        ]);

        $lanMot = $this->service->tuDongHuyQuaHan();
        $lanHai = $this->service->tuDongHuyQuaHan();

        $this->assertSame(1, $lanMot);
        $this->assertSame(0, $lanHai);
    }

    public function test_command_auto_cancel_chay_dung_va_goi_service(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHour()->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'so_nguoi' => 999,
        ]);

        $exitCode = Artisan::call('dat-lich-tien-ich:auto-cancel');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Đã tự động hủy 1', Artisan::output());
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_HUY, $datLich->fresh()->trang_thai);
    }

    public function test_command_auto_complete_chay_dung_va_goi_service(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->subHours(3)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->subHour()->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);

        $exitCode = Artisan::call('dat-lich-tien-ich:auto-complete');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Đã tự động đánh dấu hoàn thành 1', Artisan::output());
        $this->assertSame(DatLichTienIch::TRANG_THAI_HOAN_THANH, $datLich->fresh()->trang_thai);
    }

    public function test_ca_hai_job_deu_duoc_dang_ky_moi_5_phut(): void
    {
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $moTa = collect($schedule->events())->map(fn ($e) => $e->command)->implode(' | ');

        $this->assertStringContainsString('dat-lich-tien-ich:auto-cancel', $moTa);
        $this->assertStringContainsString('dat-lich-tien-ich:auto-complete', $moTa);
    }
}
