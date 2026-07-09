<?php

namespace Tests\Feature\DatLichTienIch;

use App\Models\DatLichTienIch;
use App\Models\NhanVien;

class DatLichTienIchControllerTest extends DatLichTienIchTestCase
{
    private NhanVien $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->taoNhanVien('Admin');
    }

    public function test_khach_chua_dang_nhap_bi_chan_khoi_index(): void
    {
        $response = $this->get(route('admin.dat-lich-tien-ich.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_hien_thi_danh_sach_va_dashboard_mini(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();
        app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);

        $response = $this->actingAs($this->admin, 'nhanvien')
            ->get(route('admin.dat-lich-tien-ich.index'));

        $response->assertOk();
        // FIFO tuyệt đối: booking mới tạo luôn ở Chờ duyệt, không tự động
        // duyệt ngay lập tức nữa (xem BookingService::tuDongDuyetTheoFifo()).
        $response->assertViewHas('thongKe', fn ($tk) => $tk['tong'] === 1 && $tk['cho_duyet'] === 1);
    }

    public function test_store_tao_thanh_cong_va_redirect_sang_show(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        $response = $this->actingAs($this->admin, 'nhanvien')->post(
            route('admin.dat-lich-tien-ich.store'),
            [
                'cu_dan' => $cuDan->id,
                'tien_ich' => $tienIch->id,
                'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
                'so_nguoi' => 2,
            ]
        );

        $this->assertDatabaseCount('dat_lich_tien_ich', 1);
        $datLich = DatLichTienIch::first();
        $response->assertRedirect(route('admin.dat-lich-tien-ich.show', $datLich));
        $response->assertSessionHas('success');
    }

    public function test_store_bao_loi_khi_thieu_truong_bat_buoc(): void
    {
        $response = $this->actingAs($this->admin, 'nhanvien')->post(
            route('admin.dat-lich-tien-ich.store'),
            []
        );

        $response->assertSessionHasErrors(['cu_dan', 'tien_ich', 'thoi_gian_bat_dau', 'thoi_gian_ket_thuc', 'so_nguoi']);
        $this->assertDatabaseCount('dat_lich_tien_ich', 0);
    }

    public function test_store_bao_loi_khi_khac_ngay(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        $response = $this->actingAs($this->admin, 'nhanvien')->post(
            route('admin.dat-lich-tien-ich.store'),
            [
                'cu_dan' => $cuDan->id,
                'tien_ich' => $tienIch->id,
                'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-02 09:00:00',
                'so_nguoi' => 1,
            ]
        );

        $response->assertSessionHasErrors(['thoi_gian_ket_thuc']);
    }

    public function test_store_bao_loi_khi_ngoai_gio_mo_cua(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['gio_mo_cua' => '06:00:00', 'gio_dong_cua' => '22:00:00']);

        $response = $this->actingAs($this->admin, 'nhanvien')->post(
            route('admin.dat-lich-tien-ich.store'),
            [
                'cu_dan' => $cuDan->id,
                'tien_ich' => $tienIch->id,
                'thoi_gian_bat_dau' => '2026-09-01 02:00:00',
                'thoi_gian_ket_thuc' => '2026-09-01 03:00:00',
                'so_nguoi' => 1,
            ]
        );

        $response->assertSessionHasErrors(['thoi_gian_bat_dau']);
    }

    public function test_store_bao_loi_khi_dat_lich_qua_khu(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        $response = $this->actingAs($this->admin, 'nhanvien')->post(
            route('admin.dat-lich-tien-ich.store'),
            [
                'cu_dan' => $cuDan->id,
                'tien_ich' => $tienIch->id,
                'thoi_gian_bat_dau' => now()->subHour()->format('Y-m-d H:i:s'),
                'thoi_gian_ket_thuc' => now()->addHour()->format('Y-m-d H:i:s'),
                'so_nguoi' => 1,
            ]
        );

        $response->assertSessionHasErrors(['thoi_gian_bat_dau']);
        $this->assertDatabaseCount('dat_lich_tien_ich', 0);
    }

    public function test_store_bao_loi_khi_thoi_luong_duoi_30_phut(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        $response = $this->actingAs($this->admin, 'nhanvien')->post(
            route('admin.dat-lich-tien-ich.store'),
            [
                'cu_dan' => $cuDan->id,
                'tien_ich' => $tienIch->id,
                'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-01 08:15:00',
                'so_nguoi' => 1,
            ]
        );

        $response->assertSessionHasErrors(['thoi_gian_ket_thuc']);
    }

    public function test_store_bao_loi_khi_thoi_luong_vuot_8_gio(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        $response = $this->actingAs($this->admin, 'nhanvien')->post(
            route('admin.dat-lich-tien-ich.store'),
            [
                'cu_dan' => $cuDan->id,
                'tien_ich' => $tienIch->id,
                'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-01 18:00:00',
                'so_nguoi' => 1,
            ]
        );

        $response->assertSessionHasErrors(['thoi_gian_ket_thuc']);
    }

    public function test_store_hop_le_voi_thoi_luong_bien_30_phut_va_8_gio(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['gio_mo_cua' => '00:00:00', 'gio_dong_cua' => '23:59:59']);

        $this->actingAs($this->admin, 'nhanvien')->post(
            route('admin.dat-lich-tien-ich.store'),
            [
                'cu_dan' => $cuDan->id,
                'tien_ich' => $tienIch->id,
                'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-01 08:30:00', // dung 30 phut
                'so_nguoi' => 1,
            ]
        )->assertSessionDoesntHaveErrors();

        $this->actingAs($this->admin, 'nhanvien')->post(
            route('admin.dat-lich-tien-ich.store'),
            [
                'cu_dan' => $cuDan->id,
                'tien_ich' => $tienIch->id,
                'thoi_gian_bat_dau' => '2026-09-02 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-02 16:00:00', // dung 8 gio
                'so_nguoi' => 1,
            ]
        )->assertSessionDoesntHaveErrors();

        $this->assertDatabaseCount('dat_lich_tien_ich', 2);
    }

    public function test_show_hien_thi_chi_tiet(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();
        $datLich = app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);

        $response = $this->actingAs($this->admin, 'nhanvien')
            ->get(route('admin.dat-lich-tien-ich.show', $datLich));

        $response->assertOk();
        $response->assertSee($datLich->ma_dat_lich);
    }

    public function test_update_thanh_cong_khi_dang_cho_duyet(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);
        $datLich = app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 999, // vuot suc chua -> Cho duyet
        ]);

        $response = $this->actingAs($this->admin, 'nhanvien')->put(
            route('admin.dat-lich-tien-ich.update', $datLich),
            [
                'tien_ich' => $tienIch->id,
                'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
                'so_nguoi' => 5,
            ]
        );

        $response->assertRedirect(route('admin.dat-lich-tien-ich.show', $datLich));
        $this->assertSame(5, $datLich->fresh()->so_nguoi);
    }

    public function test_update_that_bai_khi_da_duyet(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();
        $service = app(\App\Services\BookingService::class);
        $datLich = $service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);
        // FIFO tuyệt đối: tạo xong luôn là Chờ duyệt, phải chủ động duyệt để
        // thiết lập đúng tiền đề "đã duyệt" cho bài test này.
        $service->duyet($datLich);

        $response = $this->actingAs($this->admin, 'nhanvien')->put(
            route('admin.dat-lich-tien-ich.update', $datLich),
            [
                'tien_ich' => $tienIch->id,
                'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
                'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
                'so_nguoi' => 2,
            ]
        );

        $response->assertSessionHasErrors('trang_thai');
        $this->assertSame(1, $datLich->fresh()->so_nguoi);
    }

    public function test_approve_chuyen_trang_thai_thanh_cong(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);
        $service = app(\App\Services\BookingService::class);

        // Ca A va B deu tao xong la Cho duyet (FIFO tuyet doi, khong tu duyet
        // ngay). A (80 nguoi) van dang "giu cho" it mo hinh dung — huy A de B
        // (5 nguoi, giao gio voi A) co the duoc duyet() thu cong qua HTTP.
        $a = $service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 80,
        ]);
        $datLich = $service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:30:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:30:00',
            'so_nguoi' => 5,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $datLich->trang_thai);
        $service->huy($a, 'giải phóng test');

        $response = $this->actingAs($this->admin, 'nhanvien')
            ->patch(route('admin.dat-lich-tien-ich.approve', $datLich));

        $response->assertRedirect();
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $datLich->fresh()->trang_thai);
    }

    public function test_reject_voi_ly_do(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);
        $datLich = app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 999,
        ]);

        $response = $this->actingAs($this->admin, 'nhanvien')->patch(
            route('admin.dat-lich-tien-ich.reject', $datLich),
            ['ly_do' => 'Không phù hợp lịch']
        );

        $response->assertRedirect();
        $datLich->refresh();
        $this->assertSame(DatLichTienIch::TRANG_THAI_TU_CHOI, $datLich->trang_thai);
        $this->assertSame('Không phù hợp lịch', $datLich->ly_do_huy);
    }

    public function test_cancel_voi_ly_do(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();
        $datLich = app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);

        $response = $this->actingAs($this->admin, 'nhanvien')->patch(
            route('admin.dat-lich-tien-ich.cancel', $datLich),
            ['ly_do' => 'Cư dân đổi ý']
        );

        $response->assertRedirect();
        $datLich->refresh();
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_HUY, $datLich->trang_thai);
        $this->assertSame('Cư dân đổi ý', $datLich->ly_do_huy);
    }

    public function test_destroy_xoa_mem_va_restore(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();
        $datLich = app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);

        $this->actingAs($this->admin, 'nhanvien')
            ->delete(route('admin.dat-lich-tien-ich.destroy', $datLich))
            ->assertRedirect(route('admin.dat-lich-tien-ich.index'));

        $this->assertSoftDeleted('dat_lich_tien_ich', ['id' => $datLich->id], deletedAtColumn: 'deletedAt');

        $this->actingAs($this->admin, 'nhanvien')
            ->patch(route('admin.dat-lich-tien-ich.restore', $datLich->id))
            ->assertRedirect();

        $this->assertNull($datLich->fresh()->deletedAt);
    }

    public function test_dashboard_tra_ve_200_va_du_lieu_dung(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();
        app(\App\Services\BookingService::class)->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHour()->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);

        $response = $this->actingAs($this->admin, 'nhanvien')
            ->get(route('admin.dat-lich-tien-ich.dashboard'));

        $response->assertOk();
        $response->assertViewHas('thongKe', fn ($tk) => $tk['tong'] === 1 && $tk['hom_nay'] === 1);
        $response->assertViewHas('topTienIch', fn ($top) => $top->count() === 1);
    }

    public function test_dashboard_route_dang_ky_truoc_show_khong_bi_nuot_lam_id(): void
    {
        $response = $this->actingAs($this->admin, 'nhanvien')
            ->get(route('admin.dat-lich-tien-ich.dashboard'));

        // Neu route show ({datLichTienIch}) vo tinh nuot mat "dashboard" lam id,
        // day se la 404 (khong tim thay model) thay vi 200.
        $response->assertOk();
    }
}
