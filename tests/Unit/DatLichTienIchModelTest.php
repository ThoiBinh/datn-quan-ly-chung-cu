<?php

namespace Tests\Unit;

use App\Models\DatLichTienIch;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Test accessor/mutator thuần trên Model — không cần RefreshDatabase vì
 * không chạm DB, nhưng cần app Laravel đã boot (extends Tests\TestCase)
 * để Eloquent có connection resolver hợp lệ bất kể thứ tự chạy chung với
 * các Feature test khác trong cùng tiến trình PHPUnit.
 */
class DatLichTienIchModelTest extends TestCase
{
    public function test_thoi_luong_phut_tinh_dung(): void
    {
        $datLich = new DatLichTienIch([
            'thoi_gian_bat_dau' => Carbon::parse('2026-01-01 08:00:00'),
            'thoi_gian_ket_thuc' => Carbon::parse('2026-01-01 09:30:00'),
        ]);

        $this->assertSame(90, $datLich->thoi_luong_phut);
    }

    public function test_thoi_luong_phut_null_khi_thieu_moc_thoi_gian(): void
    {
        $datLich = new DatLichTienIch(['thoi_gian_bat_dau' => Carbon::now()]);

        $this->assertNull($datLich->thoi_luong_phut);
    }

    #[DataProvider('trangThaiProvider')]
    public function test_trang_thai_label_dung_text_va_class(int $trangThai, string $text): void
    {
        $datLich = new DatLichTienIch(['trang_thai' => $trangThai]);

        $this->assertSame($text, $datLich->trang_thai_label['text']);
        $this->assertStringContainsString('dark:', $datLich->trang_thai_label['class']);
    }

    public static function trangThaiProvider(): array
    {
        return [
            'cho duyet' => [DatLichTienIch::TRANG_THAI_CHO_DUYET, 'Chờ duyệt'],
            'da duyet' => [DatLichTienIch::TRANG_THAI_DA_DUYET, 'Đã duyệt'],
            'tu choi' => [DatLichTienIch::TRANG_THAI_TU_CHOI, 'Từ chối'],
            'da huy' => [DatLichTienIch::TRANG_THAI_DA_HUY, 'Đã hủy'],
            'hoan thanh' => [DatLichTienIch::TRANG_THAI_HOAN_THANH, 'Hoàn thành'],
        ];
    }

    public function test_trang_thai_label_khong_xac_dinh_voi_gia_tri_la(): void
    {
        $datLich = new DatLichTienIch(['trang_thai' => 999]);

        $this->assertSame('Không xác định', $datLich->trang_thai_label['text']);
    }

    public function test_ma_dat_lich_duoc_trim_khi_gan(): void
    {
        $datLich = new DatLichTienIch(['ma_dat_lich' => '  DL0001  ']);

        $this->assertSame('DL0001', $datLich->ma_dat_lich);
    }
}
