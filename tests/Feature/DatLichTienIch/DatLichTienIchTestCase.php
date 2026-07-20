<?php

namespace Tests\Feature\DatLichTienIch;

use App\Models\CanHo;
use App\Models\ChucVu;
use App\Models\CuDan;
use App\Models\CuDanCanHo;
use App\Models\LoaiCanHo;
use App\Models\LoaiTienIch;
use App\Models\NhanVien;
use App\Models\TienIch;
use App\Models\ToaNha;
use App\Models\TrangThaiCanHo;
use App\Models\VaiTro;
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

    /**
     * Tạo 1 căn hộ hợp lệ và gán cư dân $cuDan vào đó với trang_thai = 1
     * (đang cư trú) trên cu_dan_can_ho — điều kiện bắt buộc để
     * Resident\StoreDatLichTienIchRequest chấp nhận căn hộ này.
     */
    protected function ganCuDanVaoCanHoMoi(CuDan $cuDan): CanHo
    {
        $toaNha = ToaNha::create([
            'ten_toa_nha' => 'Tòa A',
            'dia_chi' => '123 Test',
            'so_tang' => 10,
        ]);
        $loaiCanHo = LoaiCanHo::create(['ten_loai_can_ho' => 'Căn hộ tiêu chuẩn']);
        $trangThaiCanHo = TrangThaiCanHo::create(['ten_trang_thai' => 'Đang sử dụng']);
        $vaiTro = VaiTro::create(['vai_tro' => 'Chủ hộ']);

        $canHo = CanHo::create([
            'toa_nha' => $toaNha->id,
            'so_can_ho' => 'A-'.uniqid(),
            'tang' => 1,
            'trang_thai' => $trangThaiCanHo->id,
            'loai_can_ho' => $loaiCanHo->id,
        ]);

        CuDanCanHo::create([
            'cu_dan' => $cuDan->id,
            'can_ho' => $canHo->id,
            'vai_tro' => $vaiTro->id,
            'trang_thai' => 1,
        ]);

        return $canHo;
    }
}
