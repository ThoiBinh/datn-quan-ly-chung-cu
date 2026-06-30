<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThanhToanHoaDonRequest;
use App\Models\CauHinhThanhToan;
use App\Models\HoaDon;
use App\Models\LichSuThanhToan;
use App\Models\NguonTao;
use App\Services\HoaDonService;
use Illuminate\Http\Request;

class ThanhToanController extends Controller
{
    public function __construct(private HoaDonService $hoaDonService) {}

    public function index(Request $request)
    {
        $query = LichSuThanhToan::with([
            'hoaDon.canHo.toaNha',
            'hoaDon.canHo.chuHo.cuDan',
            'nguonTao',
        ])->orderByDesc('createdAt');

        if ($request->filled('phuong_thuc')) {
            $query->where('phuong_thuc_thanh_toan', 'like', '%' . $request->phuong_thuc . '%');
        }
        if ($request->filled('tu_ngay')) {
            $query->whereDate('ngay_thanh_toan', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('ngay_thanh_toan', '<=', $request->den_ngay);
        }
        if ($request->filled('hoa_don_id')) {
            $query->where('hoa_don', $request->hoa_don_id);
        }

        $lichSuList   = $query->paginate(20)->withQueryString();
        $tongDoanhThu = LichSuThanhToan::sum('so_tien');

        return view('manager.thanh-toan.index', compact('lichSuList', 'tongDoanhThu'));
    }

    public function create(HoaDon $hoaDon)
    {
        $conNo = max(0, (float) $hoaDon->tong_tien - (float) $hoaDon->so_tien_da_thanh_toan);
        if ($conNo <= 0) {
            return redirect()->route('manager.hoa-don.show', $hoaDon)
                ->with('error', 'Hóa đơn đã được thanh toán đầy đủ.');
        }

        $hoaDon->load([
            'canHo.toaNha',
            'canHo.chuHo.cuDan',
            'canHo.cuDanHienTai.cuDan',
            'lichSuThanhToan' => fn ($q) => $q->orderBy('ngay_thanh_toan'),
            'lichSuThanhToan.nguonTao',
        ]);

        $phuongThuc = CauHinhThanhToan::where('trang_thai', 1)->orderBy('loai_phuong_thuc')->get();

        return view('manager.thanh-toan.create', compact('hoaDon', 'phuongThuc', 'conNo'));
    }

    public function store(ThanhToanHoaDonRequest $request, HoaDon $hoaDon)
    {
        $conNo = max(0, (float) $hoaDon->tong_tien - (float) $hoaDon->so_tien_da_thanh_toan);
        if ((float) $request->so_tien > $conNo) {
            return back()->withInput()->with(
                'error',
                'Số tiền thanh toán không được lớn hơn số tiền còn nợ (' . number_format($conNo, 0, ',', '.') . 'đ).'
            );
        }

        $this->hoaDonService->ghiNhanThanhToan(
            $hoaDon,
            (float) $request->so_tien,
            $request->phuong_thuc_thanh_toan,
            null,
            NguonTao::ADMIN,
            $request->ngay_thanh_toan,
            $request->ghi_chu,
            $request->nguoi_thanh_toan ? (int) $request->nguoi_thanh_toan : null
        );

        return redirect()->route('manager.hoa-don.show', $hoaDon)
            ->with('success', 'Ghi nhận thanh toán ' . number_format($request->so_tien, 0, ',', '.') . 'đ thành công.');
    }

    public function show(LichSuThanhToan $lichSu)
    {
        $lichSu->load([
            'hoaDon.canHo.toaNha',
            'hoaDon.canHo.chuHo.cuDan',
            'nguoiThanhToan',
            'nguonTao',
        ]);

        return view('manager.thanh-toan.show', compact('lichSu'));
    }
}
