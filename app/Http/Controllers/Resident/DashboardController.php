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
        $cuDan = auth('cudan')->user();
        $canHoIds = $cuDan?->canHoIdsHienTai() ?? collect();
        $canHo = $cuDan?->canHoHienTai?->canHo;

        $soHoaDonChuaThanhToan = 0;
        $tongNo = 0;
        $hoaDonChuaThanhToan = collect();
        $soPhuongTien = 0;

        if ($canHoIds->isNotEmpty()) {
            $unpaidQuery = HoaDon::whereIn('can_ho', $canHoIds)->whereIn('trang_thai', [1, 3]);
            $soHoaDonChuaThanhToan = (clone $unpaidQuery)->count();
            $tongNo = (clone $unpaidQuery)->selectSumDuNo('tong_no')->value('tong_no') ?? 0;
            $hoaDonChuaThanhToan = (clone $unpaidQuery)->with('canHo')
                ->latest('createdAt')->limit(5)->get();
            $soPhuongTien = PhuongTien::whereIn('can_ho', $canHoIds)->where('trang_thai', 1)->count();
        }

        $soYeuCauMo = $cuDan
            ? YeuCauCuDan::where('cu_dan', $cuDan->id)->whereIn('trang_thai', [1, 2])->count()
            : 0;

        $thongBaoMoi = ThongBao::orderByDesc('createdAt')->limit(5)->get();

        return view('resident.dashboard', compact(
            'cuDan', 'canHo',
            'soHoaDonChuaThanhToan', 'tongNo', 'hoaDonChuaThanhToan',
            'soYeuCauMo', 'soPhuongTien', 'thongBaoMoi'
        ));
    }
}
