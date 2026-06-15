<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\HoaDon;
use App\Models\PhuongTien;
use App\Models\ThongBao;
use App\Models\YeuCauCuDan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cuDan = $user->cuDan;
        $canHo = $cuDan?->canHoHienTai?->canHo;

        $soHoaDonChuaThanhToan = 0;
        $tongNo = 0;
        $hoaDonChuaThanhToan = collect();
        $soPhuongTien = 0;

        if ($canHo) {
            $unpaidInvoices = HoaDon::where('can_ho', $canHo->id)
                ->whereIn('trang_thai', [1, 3])->get();
            $soHoaDonChuaThanhToan = $unpaidInvoices->count();
            $tongNo = $unpaidInvoices->sum('tong_tien');
            $hoaDonChuaThanhToan = $unpaidInvoices->take(5);
            $soPhuongTien = PhuongTien::where('can_ho', $canHo->id)->where('trang_thai', 1)->count();
        }

        $soYeuCauMo = $cuDan
            ? YeuCauCuDan::where('cu_dan', $cuDan->id)->whereIn('trang_thai', [1, 2])->count()
            : 0;

        $thongBaoMoi = ThongBao::orderByDesc('created_at')->limit(5)->get();

        return view('resident.dashboard', compact(
            'cuDan', 'canHo',
            'soHoaDonChuaThanhToan', 'tongNo', 'hoaDonChuaThanhToan',
            'soYeuCauMo', 'soPhuongTien', 'thongBaoMoi'
        ));
    }
}
