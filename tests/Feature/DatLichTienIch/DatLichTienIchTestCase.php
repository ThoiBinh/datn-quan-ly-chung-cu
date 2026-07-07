<?php

namespace Tests\Feature\DatLichTienIch;

use App\Models\ChucVu;
use App\Models\CuDan;
use App\Models\LoaiTienIch;
use App\Models\NhanVien;
use App\Models\TienIch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class DatLichTienIchTestCase extends TestCase
{
    use RefreshDatabase;

    protected function taoNhanVien(string $chucVuTen = 'Admin'): NhanVien
    {
        $chucVu = ChucVu::create(['chuc_vu' => $chucVuTen]);

        return NhanVien::create([
            'ho_ten'       => 'Nhân viên '.$chucVuTen,
            'chuc_vu'      => $chucVu->id,
            'email'        => 'nv'.uniqid().'@test.local',
            'mat_khau'     => 'x',
            'trang_thai'   => 1,
            'ma_nhan_vien' => 'NV'.uniqid(),
            'cccd'         => (string) random_int(100000000000, 999999999999),
        ]);
    }

    protected function taoCuDan(): CuDan
    {
        return CuDan::create([
            'ho_ten_dem'  => 'Nguyễn Văn',
            'ten'         => 'Test'.uniqid(),
            'email'       => 'cd'.uniqid().'@test.local',
            'trang_thai'  => 1,
        ]);
    }

    protected function taoLoaiTienIch(): LoaiTienIch
    {
        return LoaiTienIch::create(['ten_loai_tien_ich' => 'Thể thao']);
    }

    protected function taoTienIch(array $overrides = []): TienIch
    {
        $loaiId = $overrides['loai_tien_ich'] ?? $this->taoLoaiTienIch()->id;

        return TienIch::create(array_merge([
            'ten_tien_ich'  => 'Hồ bơi test',
            'loai_tien_ich' => $loaiId,
            'suc_chua'      => 80,
            'phi_su_dung'   => 200000,
            'can_dat_truoc' => true,
            'trang_thai'    => TienIch::TRANG_THAI_HOAT_DONG,
            'gio_mo_cua'    => '06:00:00',
            'gio_dong_cua'  => '22:00:00',
        ], $overrides));
    }
}
