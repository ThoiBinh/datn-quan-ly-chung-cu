<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\HoaDon;
use App\Models\NhatKyHeThong;
use App\Models\PhuongTien;
use App\Models\ToaNha;
use App\Models\User;
use App\Models\YeuCauCuDan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'tong_toa_nha'   => ToaNha::count(),
            'tong_can_ho'    => CanHo::count(),
            'tong_cu_dan'    => CuDan::count(),
            'tong_nhan_vien' => User::whereIn('role', ['admin', 'manager'])->count(),
            'hoa_don_chua_tt' => HoaDon::where('trang_thai', 1)->count(),
            'hoa_don_qua_han' => HoaDon::where('trang_thai', 3)->count(),
            'yeu_cau_moi'    => YeuCauCuDan::where('trang_thai', 1)->count(),
            'phuong_tien'    => PhuongTien::where('trang_thai', 1)->count(),
        ];

        $doanhThuThang = HoaDon::where('trang_thai', 2)
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->sum('tong_tien');

        $doanhThuNam = HoaDon::where('trang_thai', 2)
            ->whereYear('updated_at', now()->year)
            ->sum('tong_tien');

        $nhatKy = NhatKyHeThong::with('nguoiThucHien')
            ->orderByDesc('createdAt')
            ->limit(10)
            ->get();

        $yeuCauMoi = YeuCauCuDan::with('cuDan')
            ->where('trang_thai', 1)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $doanhThuTheoThang = [];
        for ($i = 1; $i <= 12; $i++) {
            $doanhThuTheoThang[] = HoaDon::where('trang_thai', 2)
                ->whereYear('updated_at', now()->year)
                ->whereMonth('updated_at', $i)
                ->sum('tong_tien');
        }

        return view('admin.dashboard', compact(
            'stats', 'doanhThuThang', 'doanhThuNam',
            'nhatKy', 'yeuCauMoi', 'doanhThuTheoThang'
        ));
    }
}
