<?php

namespace Tests\Feature\DatLichTienIch;

use App\Models\DatLichTienIch;

class ResidentDatLichTienIchControllerTest extends DatLichTienIchTestCase
{
    public function test_khach_chua_dang_nhap_bi_chan(): void
    {
        $response = $this->get(route('resident.dat-lich-tien-ich.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_cu_dan_dat_lich_thanh_cong_va_luon_o_cho_duyet(): void
    {
        $cuDan = $this->taoCuDan();
        $canHo = $this->ganCuDanVaoCanHoMoi($cuDan);
        $tienIch = $this->taoTienIch(['can_dat_truoc' => true]);

        $response = $this->actingAs($cuDan, 'cudan')->post(
            route('resident.dat-lich-tien-ich.store'),
            [
                'tien_ich' => $tienIch->id,
                'can_ho' => $canHo->id,
                'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
                'so_nguoi' => 2,
            ]
        );

        $datLich = DatLichTienIch::first();
        $response->assertRedirect(route('resident.dat-lich-tien-ich.show', $datLich));
        $this->assertSame($cuDan->id, $datLich->cu_dan);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $datLich->trang_thai);
    }

    public function test_cu_dan_khong_the_dat_can_ho_khong_thuoc_ve_minh(): void
    {
        $cuDan = $this->taoCuDan();
        $cuDanKhac = $this->taoCuDan();
        $canHoCuaNguoiKhac = $this->ganCuDanVaoCanHoMoi($cuDanKhac);
        $tienIch = $this->taoTienIch(['can_dat_truoc' => true]);

        $response = $this->actingAs($cuDan, 'cudan')->post(
            route('resident.dat-lich-tien-ich.store'),
            [
                'tien_ich' => $tienIch->id,
                'can_ho' => $canHoCuaNguoiKhac->id,
                'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
                'so_nguoi' => 1,
            ]
        );

        $response->assertSessionHasErrors('can_ho');
        $this->assertDatabaseCount('dat_lich_tien_ich', 0);
    }

    public function test_cu_dan_xem_danh_sach_va_chi_tiet_lich_cua_minh(): void
    {
        $cuDan = $this->taoCuDan();
        $canHo = $this->ganCuDanVaoCanHoMoi($cuDan);
        $tienIch = $this->taoTienIch();
        $datLich = app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDan->id, 'can_ho' => $canHo->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);

        $this->actingAs($cuDan, 'cudan')
            ->get(route('resident.dat-lich-tien-ich.index'))
            ->assertOk()
            ->assertSee($datLich->ma_dat_lich);

        $this->actingAs($cuDan, 'cudan')
            ->get(route('resident.dat-lich-tien-ich.show', $datLich))
            ->assertOk()
            ->assertSee($datLich->ma_dat_lich);
    }

    public function test_cu_dan_khong_the_xem_lich_cua_nguoi_khac(): void
    {
        $cuDan = $this->taoCuDan();
        $cuDanKhac = $this->taoCuDan();
        $tienIch = $this->taoTienIch();
        $datLichCuaNguoiKhac = app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDanKhac->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);

        $this->actingAs($cuDan, 'cudan')
            ->get(route('resident.dat-lich-tien-ich.show', $datLichCuaNguoiKhac))
            ->assertForbidden();
    }

    public function test_cu_dan_huy_lich_thanh_cong_khi_con_hon_2_gio(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();
        $datLich = app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHours(3)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(4)->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);

        $this->actingAs($cuDan, 'cudan')
            ->patch(route('resident.dat-lich-tien-ich.huy', $datLich))
            ->assertRedirect(route('resident.dat-lich-tien-ich.show', $datLich));

        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_HUY, $datLich->fresh()->trang_thai);
    }

    public function test_cu_dan_khong_the_huy_lich_cua_nguoi_khac(): void
    {
        $cuDan = $this->taoCuDan();
        $cuDanKhac = $this->taoCuDan();
        $tienIch = $this->taoTienIch();
        $datLichCuaNguoiKhac = app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDanKhac->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHours(3)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(4)->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);

        $this->actingAs($cuDan, 'cudan')
            ->patch(route('resident.dat-lich-tien-ich.huy', $datLichCuaNguoiKhac))
            ->assertForbidden();

        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $datLichCuaNguoiKhac->fresh()->trang_thai);
    }

    /**
     * Kịch bản đầu-cuối: cư dân đặt lịch (Chờ duyệt) -> scheduler FIFO chạy
     * -> lịch tự động chuyển Đã duyệt mà không cần ai can thiệp thủ công.
     */
    public function test_lich_cu_dan_dat_duoc_tu_dong_duyet_boi_scheduler_fifo(): void
    {
        $cuDan = $this->taoCuDan();
        $canHo = $this->ganCuDanVaoCanHoMoi($cuDan);
        $tienIch = $this->taoTienIch(['can_dat_truoc' => true, 'suc_chua' => 80]);

        $this->actingAs($cuDan, 'cudan')->post(
            route('resident.dat-lich-tien-ich.store'),
            [
                'tien_ich' => $tienIch->id,
                'can_ho' => $canHo->id,
                'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
                'so_nguoi' => 2,
            ]
        );

        $datLich = DatLichTienIch::first();
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $datLich->trang_thai);

        app(\App\Services\BookingService::class)->tuDongDuyetTheoFifo();

        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $datLich->fresh()->trang_thai);
    }
}
