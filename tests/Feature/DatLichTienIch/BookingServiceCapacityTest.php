<?php

namespace Tests\Feature\DatLichTienIch;

use App\Models\DatLichTienIch;
use App\Services\BookingService;
use Illuminate\Validation\ValidationException;

class BookingServiceCapacityTest extends DatLichTienIchTestCase
{
    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingService();
    }

    public function test_tao_dat_lich_tu_dong_duyet_khi_du_suc_chua(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id,
            'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 10,
        ]);

        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $datLich->trang_thai);
        $this->assertNotNull($datLich->ngay_duyet);
        $this->assertNull($datLich->nhan_vien_duyet);
        $this->assertMatchesRegularExpression('/^DL\d{8}\d{4}$/', $datLich->ma_dat_lich);
    }

    public function test_tao_dat_lich_cho_duyet_khi_vuot_suc_chua(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id,
            'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 90,
        ]);

        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $datLich->trang_thai);
        $this->assertNull($datLich->ngay_duyet);
    }

    public function test_khong_gioi_han_khi_suc_chua_bang_khong(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 0]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id,
            'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 100000,
        ]);

        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $datLich->trang_thai);
    }

    public function test_tao_dat_lich_tinh_dung_phi(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80, 'phi_su_dung' => 100000]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id,
            'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-01 09:30:00', // 90 phut = 1.5h
            'so_nguoi' => 3,
        ]);

        // 3 nguoi x 100.000 x 1.5h = 450.000
        $this->assertEqualsWithDelta(450000.0, (float) $datLich->phi_su_dung, 0.01);
    }

    public function test_tao_dat_lich_that_bai_khi_tien_ich_ngung_hoat_dong(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['trang_thai' => \App\Models\TienIch::TRANG_THAI_NGUNG_HOAT_DONG]);

        $this->expectException(ValidationException::class);

        $this->service->taoDatLich([
            'cu_dan' => $cuDan->id,
            'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);
    }

    /**
     * Ma trận giao nhau thời gian: xác nhận thuật toán sức chứa xử lý đúng
     * mọi kiểu chồng lấn (một phần, lồng nhau, nối đuôi chạm mốc, rời rạc).
     */
    public function test_giao_nhau_thoi_gian_xu_ly_dung_moi_truong_hop(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        // A: 08h-10h, 50 nguoi -> doc lap, du cho -> Da duyet
        $a = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 10:00:00',
            'so_nguoi' => 50,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $a->trang_thai);

        // B: 09h-11h, giao mot phan voi A, 40 nguoi -> 50+40=90>80 -> Cho duyet
        $b = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 09:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 11:00:00',
            'so_nguoi' => 40,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $b->trang_thai);

        // C: 10h-11h, noi duoi A dung moc gio (khong giao), 40 nguoi -> 0+40<=80 -> Da duyet
        $c = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 10:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 11:00:00',
            'so_nguoi' => 40,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $c->trang_thai);

        // D: 08:30-09:30, long trong A hoan toan, 35 nguoi -> 50+35=85>80 -> Cho duyet
        $d = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:30:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:30:00',
            'so_nguoi' => 35,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $d->trang_thai);

        // E: ngay khac hoan toan roi rac, 80 nguoi -> Da duyet
        $e = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-10 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-10 09:00:00',
            'so_nguoi' => 80,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $e->trang_thai);
    }

    public function test_duyet_that_bai_neu_het_cho_luc_duyet(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $a = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 10:00:00',
            'so_nguoi' => 80,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $a->trang_thai);

        $b = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:30:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:30:00',
            'so_nguoi' => 5,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $b->trang_thai);

        $this->expectException(ValidationException::class);
        $this->service->duyet($b);
    }

    public function test_duyet_thanh_cong_sau_khi_huy_giai_phong_suc_chua(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $a = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 10:00:00',
            'so_nguoi' => 80,
        ]);
        $b = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:30:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:30:00',
            'so_nguoi' => 5,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $b->trang_thai);

        $this->service->huy($a, 'giải phóng test');
        $b = $this->service->duyet($b);

        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $b->trang_thai);
    }

    public function test_khong_the_sua_khi_da_duyet(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $datLich->trang_thai);

        $this->expectException(ValidationException::class);
        $this->service->capNhatDatLich($datLich, [
            'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 2,
        ]);
    }

    public function test_tu_choi_chi_ap_dung_cho_cho_duyet(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 200, // vuot suc chua -> Cho duyet
        ]);

        $datLich = $this->service->tuChoi($datLich, 'không phù hợp');

        $this->assertSame(DatLichTienIch::TRANG_THAI_TU_CHOI, $datLich->trang_thai);
        $this->assertSame('không phù hợp', $datLich->ly_do_huy);

        $this->expectException(ValidationException::class);
        $this->service->tuChoi($datLich, 'lần 2');
    }

    public function test_hoan_thanh_chi_ap_dung_cho_da_duyet(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $datLich->trang_thai);

        $datLich = $this->service->hoanThanh($datLich);
        $this->assertSame(DatLichTienIch::TRANG_THAI_HOAN_THANH, $datLich->trang_thai);

        $this->expectException(ValidationException::class);
        $this->service->hoanThanh($datLich);
    }

    public function test_scope_active_loai_tru_tu_choi_va_da_huy(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $choDuyet = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 999,
        ]);
        $daDuyet = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-02 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-02 09:00:00',
            'so_nguoi' => 1,
        ]);
        $bTuChoi = $this->service->tuChoi($choDuyet, 'test');

        $daHuyGoc = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-03 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-03 09:00:00',
            'so_nguoi' => 1,
        ]);
        $this->service->huy($daHuyGoc, 'test');

        $active = DatLichTienIch::active()->pluck('id')->all();

        $this->assertContains($daDuyet->id, $active);
        $this->assertNotContains($bTuChoi->id, $active);
        $this->assertNotContains($daHuyGoc->id, $active);
    }

    public function test_xoa_va_khoi_phuc(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);

        $this->service->xoa($datLich);
        $this->assertSoftDeleted('dat_lich_tien_ich', ['id' => $datLich->id], deletedAtColumn: 'deletedAt');

        $khoiPhuc = $this->service->khoiPhuc($datLich->id);
        $this->assertNull($khoiPhuc->deletedAt);
    }
}
