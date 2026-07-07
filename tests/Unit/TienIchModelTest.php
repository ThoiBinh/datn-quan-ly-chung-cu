<?php

namespace Tests\Unit;

use App\Models\TienIch;
use Tests\TestCase;

/**
 * Test accessor/mutator thuần trên Model — extends Tests\TestCase (app đã
 * boot) thay vì PHPUnit\Framework\TestCase thuần, để tránh lỗi tiềm ẩn khi
 * Eloquent cần resolve connection (vd. cast 'datetime' cần getDateFormat()
 * từ query grammar của connection) — xem chi tiết ở DatLichTienIchModelTest.
 */
class TienIchModelTest extends TestCase
{
    public function test_gio_hoat_dong_dinh_dang_dung(): void
    {
        $tienIch = new TienIch(['gio_mo_cua' => '06:00:00', 'gio_dong_cua' => '22:00:00']);

        $this->assertSame('06:00 - 22:00', $tienIch->gio_hoat_dong);
    }

    public function test_gio_hoat_dong_null_khi_thieu_gio_mo_hoac_dong(): void
    {
        $this->assertNull((new TienIch(['gio_mo_cua' => '06:00:00']))->gio_hoat_dong);
        $this->assertNull((new TienIch(['gio_dong_cua' => '22:00:00']))->gio_hoat_dong);
        $this->assertNull((new TienIch())->gio_hoat_dong);
    }

    public function test_trang_thai_label_hoat_dong(): void
    {
        $tienIch = new TienIch(['trang_thai' => TienIch::TRANG_THAI_HOAT_DONG]);

        $this->assertSame('Đang hoạt động', $tienIch->trang_thai_label['text']);
        $this->assertStringContainsString('dark:', $tienIch->trang_thai_label['class']);
    }

    public function test_trang_thai_label_ngung_hoat_dong(): void
    {
        $tienIch = new TienIch(['trang_thai' => TienIch::TRANG_THAI_NGUNG_HOAT_DONG]);

        $this->assertSame('Ngừng hoạt động', $tienIch->trang_thai_label['text']);
    }

    public function test_ten_tien_ich_duoc_trim_khi_gan(): void
    {
        $tienIch = new TienIch(['ten_tien_ich' => '  Hồ bơi  ']);

        $this->assertSame('Hồ bơi', $tienIch->ten_tien_ich);
    }

    public function test_can_dat_truoc_duoc_cast_thanh_boolean(): void
    {
        $tienIch = new TienIch(['can_dat_truoc' => 1]);

        $this->assertTrue($tienIch->can_dat_truoc);
        $this->assertIsBool($tienIch->can_dat_truoc);
    }
}
