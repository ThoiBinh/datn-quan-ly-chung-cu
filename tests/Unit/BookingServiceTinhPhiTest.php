<?php

namespace Tests\Unit;

use App\Models\TienIch;
use App\Services\BookingCapacityService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Unit test thuần cho công thức tính phí của BookingCapacityService::tinhPhi()
 * (trước refactor: BookingService::tinhPhi() — logic tính phí nay thuộc về
 * BookingCapacityService, không đổi công thức/chữ ký method).
 *
 *   phi_su_dung = so_nguoi × tien_ich.phi_su_dung × số giờ
 *   số giờ      = TIMESTAMPDIFF(MINUTE, batDau, ketThuc) / 60
 *
 * Không cần Laravel app/DB — chỉ khởi tạo Model và Carbon thuần trong bộ nhớ.
 * BookingCapacityService không có constructor dependency nên khởi tạo trực
 * tiếp được mà không cần container/app() — giữ đúng tính chất "pure unit
 * test" của file này.
 */
class BookingServiceTinhPhiTest extends TestCase
{
    private BookingCapacityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingCapacityService();
    }

    private function tienIchGiaPhi(float $phiSuDung): TienIch
    {
        return new TienIch(['phi_su_dung' => $phiSuDung]);
    }

    public function test_tinh_phi_co_ban_dung_1_gio(): void
    {
        $tienIch = $this->tienIchGiaPhi(100000);
        $batDau   = Carbon::parse('2026-01-01 08:00:00');
        $ketThuc  = Carbon::parse('2026-01-01 09:00:00'); // 60 phút = 1 giờ

        $phi = $this->service->tinhPhi($tienIch, 2, $batDau, $ketThuc);

        // 2 người × 100.000đ × 1 giờ = 200.000đ
        $this->assertEqualsWithDelta(200000.0, $phi, 0.0000001);
    }

    public function test_tinh_phi_cho_phep_so_gio_le_90_phut(): void
    {
        $tienIch = $this->tienIchGiaPhi(50000);
        $batDau   = Carbon::parse('2026-01-01 08:00:00');
        $ketThuc  = Carbon::parse('2026-01-01 09:30:00'); // 90 phút = 1.5 giờ

        $phi = $this->service->tinhPhi($tienIch, 3, $batDau, $ketThuc);

        // 3 người × 50.000đ × 1.5 giờ = 225.000đ
        $this->assertEqualsWithDelta(225000.0, $phi, 0.0000001);
    }

    public function test_tinh_phi_cho_phep_so_gio_le_100_phut(): void
    {
        $tienIch = $this->tienIchGiaPhi(60000);
        $batDau   = Carbon::parse('2026-01-01 08:00:00');
        $ketThuc  = Carbon::parse('2026-01-01 09:40:00'); // 100 phút = 1.6666... giờ

        $phi = $this->service->tinhPhi($tienIch, 1, $batDau, $ketThuc);

        $expected = 1 * 60000 * (100 / 60);
        $this->assertEqualsWithDelta($expected, $phi, 0.0000001);
    }

    public function test_tinh_phi_khong_lam_tron_ket_qua(): void
    {
        $tienIch = $this->tienIchGiaPhi(100000);
        $batDau   = Carbon::parse('2026-01-01 08:00:00');
        $ketThuc  = Carbon::parse('2026-01-01 08:20:00'); // 20 phút = 0.3333... giờ

        $phi = $this->service->tinhPhi($tienIch, 1, $batDau, $ketThuc);

        // 100.000 × (20/60) = 33.333,333... — không được làm tròn về 33.333,33
        $expected = 1 * 100000 * (20 / 60);
        $this->assertEqualsWithDelta($expected, $phi, 0.0000001);

        // Chứng minh kết quả KHÔNG bị làm tròn 2 chữ số thập phân: chênh lệch
        // giữa giá trị thô và giá trị làm tròn 2dp phải đáng kể (~0.0033),
        // nếu code có round(...,2) thì assertion dưới đây sẽ thất bại.
        $this->assertGreaterThan(0.001, abs($phi - round($phi, 2)));
    }

    public function test_timestampdiff_minute_cat_bo_phan_giay_le(): void
    {
        // 90 phút 45 giây: TIMESTAMPDIFF(MINUTE) phải cắt còn 90 phút tròn
        // (không làm tròn lên 91, không giữ phần lẻ 90.75 phút).
        $tienIch = $this->tienIchGiaPhi(60000);
        $batDau   = Carbon::parse('2026-01-01 08:00:00');
        $ketThuc  = Carbon::parse('2026-01-01 09:30:45');

        $phi = $this->service->tinhPhi($tienIch, 1, $batDau, $ketThuc);

        // Nếu tính đúng: 90 phút / 60 = 1.5 giờ → 60.000 × 1.5 = 90.000
        // Nếu tính SAI (giữ nguyên phần giây lẻ 90.75 phút): sẽ ra 90.750
        $this->assertEqualsWithDelta(90000.0, $phi, 0.0000001);
    }

    public function test_tinh_phi_ty_le_thuan_voi_so_nguoi(): void
    {
        $tienIch = $this->tienIchGiaPhi(75000);
        $batDau   = Carbon::parse('2026-01-01 08:00:00');
        $ketThuc  = Carbon::parse('2026-01-01 09:15:00'); // 75 phút = 1.25 giờ

        $phiChoMot = $this->service->tinhPhi($tienIch, 1, $batDau, $ketThuc);
        $phiChoBon = $this->service->tinhPhi($tienIch, 4, $batDau, $ketThuc);

        $this->assertEqualsWithDelta($phiChoMot * 4, $phiChoBon, 0.0000001);
    }

    public function test_tinh_phi_bang_khong_khi_khong_co_thoi_luong(): void
    {
        $tienIch = $this->tienIchGiaPhi(100000);
        $thoiDiem = Carbon::parse('2026-01-01 08:00:00');

        $phi = $this->service->tinhPhi($tienIch, 5, $thoiDiem, $thoiDiem->copy());

        $this->assertEqualsWithDelta(0.0, $phi, 0.0000001);
    }

    public function test_tinh_phi_tien_ich_mien_phi_luon_bang_khong(): void
    {
        $tienIch = $this->tienIchGiaPhi(0);
        $batDau   = Carbon::parse('2026-01-01 08:00:00');
        $ketThuc  = Carbon::parse('2026-01-01 10:00:00');

        $phi = $this->service->tinhPhi($tienIch, 10, $batDau, $ketThuc);

        $this->assertEqualsWithDelta(0.0, $phi, 0.0000001);
    }
}
