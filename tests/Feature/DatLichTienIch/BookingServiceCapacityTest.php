<?php

namespace Tests\Feature\DatLichTienIch;

use App\Models\DatLichTienIch;
use App\Models\TienIch;
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

    public function test_tao_dat_lich_luon_o_trang_thai_cho_duyet_bat_ke_suc_chua(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIchConCho = $this->taoTienIch(['suc_chua' => 80]);
        $tienIchKhongGioiHan = $this->taoTienIch(['suc_chua' => 0]);

        // Con du cho rieng le -> van la Cho duyet (FIFO tuyệt đối: không tự
        // động duyệt ngay lúc tạo nữa, phải chờ scheduler tuDongDuyetTheoFifo()).
        $duCho = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIchConCho->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 10,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $duCho->trang_thai);
        $this->assertNull($duCho->ngay_duyet);
        $this->assertMatchesRegularExpression('/^DL\d{8}\d{4}$/', $duCho->ma_dat_lich);

        // Vuot suc chua -> cung la Cho duyet
        $vuotCho = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIchConCho->id,
            'thoi_gian_bat_dau' => '2026-09-02 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-02 09:00:00',
            'so_nguoi' => 90,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $vuotCho->trang_thai);

        // Khong gioi han suc chua (suc_chua = 0) -> VAN la Cho duyet luc tao
        $khongGioiHan = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIchKhongGioiHan->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 100000,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $khongGioiHan->trang_thai);
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
        $tienIch = $this->taoTienIch(['trang_thai' => TienIch::TRANG_THAI_NGUNG_HOAT_DONG]);

        $this->expectException(ValidationException::class);

        $this->service->taoDatLich([
            'cu_dan' => $cuDan->id,
            'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  FIFO tuyệt đối (tuDongDuyetTheoFifo)
    // ─────────────────────────────────────────────────────────────

    /**
     * Kịch bản cốt lõi của FIFO tuyệt đối, thu hẹp theo CỤM khung giờ giao
     * nhau: A tạo trước, đủ chỗ riêng lẻ -> được duyệt. B tạo sau A, giao
     * giờ với A, cộng lại vượt sức chứa -> phải chờ, và vì B là booking đầu
     * tiên KHÔNG đủ chỗ trong CỤM của nó nên CỤM đó dừng lại. C tạo sau B
     * nhưng khung giờ hoàn toàn KHÔNG giao với A/B (thuộc một cụm khác) nên
     * KHÔNG bị ảnh hưởng bởi việc B đang chờ — C vẫn được duyệt ngay.
     */
    public function test_fifo_duyet_dung_thu_tu_tao_truoc_va_dung_lai_dung_cho(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $a = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 10:00:00',
            'so_nguoi' => 50,
        ]);
        usleep(1000);
        $b = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 09:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 11:00:00',
            'so_nguoi' => 40, // giao voi A: 50+40=90 > 80 -> khong du, chan cum nay
        ]);
        usleep(1000);
        $c = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-10-01 09:00:00',
            'so_nguoi' => 5, // khong giao gio voi A/B -> khac cum, khong bi chan
        ]);

        $soLuong = $this->service->tuDongDuyetTheoFifo();

        $this->assertSame(2, $soLuong);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $a->fresh()->trang_thai);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $b->fresh()->trang_thai);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $c->fresh()->trang_thai);
    }

    /**
     * Sau khi A (chặn cụm của nó) bị hủy, giải phóng sức chứa, lần chạy FIFO
     * kế tiếp phải duyệt được B (vì cụm A-B nay đã đủ chỗ). C ở cụm khác nên
     * đã được duyệt ngay từ lần chạy đầu tiên, không liên quan tới việc hủy A.
     */
    public function test_fifo_tiep_tuc_hang_doi_sau_khi_giai_phong_cho(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $a = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 10:00:00',
            'so_nguoi' => 50,
        ]);
        $b = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 09:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 11:00:00',
            'so_nguoi' => 40,
        ]);
        $c = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-10-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-10-01 09:00:00',
            'so_nguoi' => 5,
        ]);

        // Lan chay dau: A duyet (du cho rieng), C duyet (khac cum), B cho (chan cum voi A)
        $soLuongLanDau = $this->service->tuDongDuyetTheoFifo();
        $this->assertSame(2, $soLuongLanDau);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $b->fresh()->trang_thai);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $c->fresh()->trang_thai);

        $this->service->huy($a->fresh(), 'giải phóng test');

        $soLuong = $this->service->tuDongDuyetTheoFifo();

        $this->assertSame(1, $soLuong);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $b->fresh()->trang_thai);
    }

    /**
     * Bắc cầu qua booking trung gian: D (08h-09h) và F (09h15-10h) không giao
     * giờ trực tiếp, nhưng E (08h30-09h30) giao cả D lẫn F nên D-E-F thuộc
     * CÙNG một cụm theo tính bắc cầu. E khiến cụm vượt sức chứa (D+E > 80)
     * nên cụm dừng tại E — F dù không giao trực tiếp với D vẫn phải chờ vì
     * cùng cụm, không được xét tách biệt.
     */
    public function test_fifo_bac_cau_qua_booking_trung_gian_van_chung_mot_cum(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $d = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2027-01-10 08:00:00', 'thoi_gian_ket_thuc' => '2027-01-10 09:00:00',
            'so_nguoi' => 70,
        ]);
        $e = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2027-01-10 08:30:00', 'thoi_gian_ket_thuc' => '2027-01-10 09:30:00',
            'so_nguoi' => 20, // giao D: 70+20=90 > 80 -> chan cum ngay tai E
        ]);
        $f = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2027-01-10 09:15:00', 'thoi_gian_ket_thuc' => '2027-01-10 10:00:00',
            'so_nguoi' => 5, // khong giao D truc tiep nhung giao E -> bac cau chung cum voi D
        ]);

        $soLuong = $this->service->tuDongDuyetTheoFifo();

        $this->assertSame(1, $soLuong);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $d->fresh()->trang_thai);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $e->fresh()->trang_thai);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $f->fresh()->trang_thai);
    }

    public function test_fifo_duyet_het_khi_suc_chua_khong_gioi_han(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 0]);

        $a = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 100000,
        ]);
        $b = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:30:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:30:00',
            'so_nguoi' => 200000,
        ]);

        $soLuong = $this->service->tuDongDuyetTheoFifo();

        $this->assertSame(2, $soLuong);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $a->fresh()->trang_thai);
        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $b->fresh()->trang_thai);
    }

    public function test_fifo_hang_doi_tach_biet_theo_tung_tien_ich(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIchBiChan = $this->taoTienIch(['suc_chua' => 80]);
        $tienIchKhac = $this->taoTienIch(['suc_chua' => 80]);

        // Tien ich 1: A du cho, B (giao gio) khong du -> hang doi bi chan tai B
        $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIchBiChan->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 10:00:00',
            'so_nguoi' => 50,
        ]);
        $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIchBiChan->id,
            'thoi_gian_bat_dau' => '2026-09-01 09:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 11:00:00',
            'so_nguoi' => 40,
        ]);

        // Tien ich 2: hoan toan doc lap, khong lien quan gi den hang doi bi chan o tren
        $dKhac = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIchKhac->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 10,
        ]);

        $this->service->tuDongDuyetTheoFifo();

        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_DUYET, $dKhac->fresh()->trang_thai);
    }

    // ─────────────────────────────────────────────────────────────
    //  Duyệt thủ công (nhân viên) — vẫn hoạt động ngoài luồng FIFO tự động
    // ─────────────────────────────────────────────────────────────

    public function test_duyet_thu_cong_that_bai_neu_khong_du_cho(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $a = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 10:00:00',
            'so_nguoi' => 80,
        ]);
        $this->service->duyet($a); // nhân viên duyệt tay, bỏ qua FIFO — vẫn được phép

        $b = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:30:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:30:00',
            'so_nguoi' => 5,
        ]);

        $this->expectException(ValidationException::class);
        $this->service->duyet($b);
    }

    public function test_duyet_thu_cong_thanh_cong_sau_khi_huy_giai_phong_suc_chua(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $a = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 10:00:00',
            'so_nguoi' => 80,
        ]);
        $this->service->duyet($a);

        $b = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:30:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:30:00',
            'so_nguoi' => 5,
        ]);

        $this->service->huy($a->fresh(), 'giải phóng test');
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
        $this->service->duyet($datLich);

        $this->expectException(ValidationException::class);
        $this->service->capNhatDatLich($datLich->fresh(), [
            'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 2,
        ]);
    }

    public function test_cap_nhat_dat_lich_khong_tu_nhay_da_duyet(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);
        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $datLich->trang_thai);

        // So_nguoi con rat nho, thua suc chua rieng le, nhung sua khong duoc
        // phep tu nhay len Da duyet — van phai cho FIFO nhu moi booking khac.
        $datLich = $this->service->capNhatDatLich($datLich, [
            'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00',
            'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 2,
        ]);

        $this->assertSame(DatLichTienIch::TRANG_THAI_CHO_DUYET, $datLich->trang_thai);
        $this->assertSame(2, $datLich->so_nguoi);
    }

    public function test_tu_choi_chi_ap_dung_cho_cho_duyet(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
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
        $this->service->duyet($datLich);

        $datLich = $this->service->hoanThanh($datLich->fresh());
        $this->assertSame(DatLichTienIch::TRANG_THAI_HOAN_THANH, $datLich->trang_thai);

        $this->expectException(ValidationException::class);
        $this->service->hoanThanh($datLich);
    }

    public function test_scope_active_loai_tru_tu_choi_va_da_huy(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch(['suc_chua' => 80]);

        $seChoDuyet = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-01 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-01 09:00:00',
            'so_nguoi' => 1,
        ]);
        $daDuyet = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => '2026-09-02 08:00:00', 'thoi_gian_ket_thuc' => '2026-09-02 09:00:00',
            'so_nguoi' => 1,
        ]);
        $this->service->duyet($daDuyet);
        $bTuChoi = $this->service->tuChoi($seChoDuyet->fresh());

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

    public function test_huy_boi_cu_dan_thanh_cong_khi_con_hon_2_gio(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHours(3)->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(4)->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);

        $datLich = $this->service->huyBoiCuDan($datLich);

        $this->assertSame(DatLichTienIch::TRANG_THAI_DA_HUY, $datLich->trang_thai);
        $this->assertSame('Cư dân hủy lịch', $datLich->ly_do_huy);
    }

    public function test_huy_boi_cu_dan_that_bai_khi_con_duoi_2_gio(): void
    {
        $cuDan = $this->taoCuDan();
        $tienIch = $this->taoTienIch();

        $datLich = $this->service->taoDatLich([
            'cu_dan' => $cuDan->id, 'tien_ich' => $tienIch->id,
            'thoi_gian_bat_dau' => now()->addHour()->format('Y-m-d H:i:s'),
            'thoi_gian_ket_thuc' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'so_nguoi' => 1,
        ]);

        $this->expectException(ValidationException::class);
        $this->service->huyBoiCuDan($datLich);
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
