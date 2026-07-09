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
            'so_nguoi' => 1,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $a->trang_thai);

        // B: Cho duyet, con 3 gio nua bat dau (>2h) -> khong duoc dong toi
        $b = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHours(3)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(4)->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);

        // C: Da duyet (duyet tay), con 1 gio -> khong bi huy vi khac trang thai
        $c = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHour()->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);
        $this->service->duyet($c);

        $soLuong = $this->service->tuDongHuyQuaHan();

        $this->assertSame(1, $soLuong);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_HUY, $a->fresh()->trang_thai);
        $this->assertStringContainsString('không đủ sức chứa', $a->fresh()->ly_do_huy);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $b->fresh()->trang_thai);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $c->fresh()->trang_thai);
    }

    public function test_auto_complete_hoan_thanh_dung_booking_da_qua_gio_ket_thuc(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        // D: Da duyet (duyet tay), da qua gio ket thuc -> phai tu dong hoan thanh
        $d = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->subHours(3)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->subHour()->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);
        $this->service->duyet($d);

        // E: Da duyet (duyet tay), chua den gio ket thuc -> khong doi
        $e = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHours(5)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(6)->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);
        $this->service->duyet($e);

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
            'so_nguoi' => 1,
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
            'so_nguoi' => 1,
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
        $this->service->duyet($datLich);

        $exitCode = Artisan::call('dat-lich-tien-ich:auto-complete');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Đã tự động đánh dấu hoàn thành 1', Artisan::output());
        $this->assertSame(DatLichTienIch::TRANG_THAI_HOAN_THANH, $datLich->fresh()->trang_thai);
    }

    public function test_command_auto_approve_chay_dung_va_goi_service_fifo(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHours(3)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(4)->format('Y-m-d H:i:s'),
            'so_nguoi' => 10,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $datLich->trang_thai);

        $exitCode = Artisan::call('dat-lich-tien-ich:auto-approve');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Đã tự động duyệt 1', Artisan::output());
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $datLich->fresh()->trang_thai);
    }

    public function test_ca_ba_job_deu_duoc_dang_ky_lich_chay(): void
    {
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $moTa = collect($schedule->events())->map(fn ($e) => $e->command)->implode(' | ');

        $this->assertStringContainsString('dat-lich-tien-ich:auto-approve', $moTa);
        $this->assertStringContainsString('dat-lich-tien-ich:auto-cancel', $moTa);
        $this->assertStringContainsString('dat-lich-tien-ich:auto-complete', $moTa);
    }
}
