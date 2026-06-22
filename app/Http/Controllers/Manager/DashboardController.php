<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\HoaDon;
use App\Models\PhuongTien;
use App\Models\ToaNha;
use App\Models\YeuCauCuDan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'tong_toa_nha'    => ToaNha::count(),
            'tong_can_ho'     => CanHo::count(),
            'can_ho_co_nguoi' => CanHo::where('trang_thai', 1)->count(),
            'can_ho_trong'    => CanHo::where('trang_thai', 2)->count(),
            'tong_cu_dan'     => CuDan::count(),
            'hoa_don_chua_tt' => HoaDon::where('trang_thai', 1)->count(),
            'hoa_don_qua_han' => HoaDon::where('trang_thai', 3)->count(),
            'yeu_cau_moi'     => YeuCauCuDan::where('trang_thai', 1)->count(),
            'phuong_tien'     => PhuongTien::where('trang_thai', 1)->count(),
        ];

        $doanhThuThang = HoaDon::where('trang_thai', 2)
            ->whereYear('updatedAt', now()->year)
            ->whereMonth('updatedAt', now()->month)
            ->sum('tong_tien');

        $yeuCauGanDay = YeuCauCuDan::with('cuDan')
            ->whereIn('trang_thai', [1, 2])
            ->orderByDesc('createdAt')
            ->limit(8)
            ->get();

        $hoaDonQuaHan = HoaDon::with(['canHo.toaNha'])
            ->where('trang_thai', 3)
            ->orderByDesc('han_thanh_toan')
            ->limit(5)
            ->get();

        $doanhThuTheoThang = [];
        for ($i = 1; $i <= 12; $i++) {
            $doanhThuTheoThang[] = HoaDon::where('trang_thai', 2)
                ->whereYear('updatedAt', now()->year)
                ->whereMonth('updatedAt', $i)
                ->sum('tong_tien');
        }

        return view('manager.dashboard', compact(
            'stats', 'doanhThuThang', 'yeuCauGanDay',
            'hoaDonQuaHan', 'doanhThuTheoThang'
        ));
    }
}
