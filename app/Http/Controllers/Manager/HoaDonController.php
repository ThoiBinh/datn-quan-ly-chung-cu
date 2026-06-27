<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\HoaDon;
use App\Services\HoaDonService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class HoaDonController extends Controller
{
    public function __construct(private HoaDonService $hoaDonService) {}

    public function index(Request $request)
    {
        $query = HoaDon::with(['canHo.toaNha']);

        if ($request->filled('search')) {
            $query->where('ma_thanh_toan', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('thang')) {
            $query->where('thang', $request->thang);
        }
        if ($request->filled('nam')) {
            $query->where('nam', $request->nam);
        }

        $hoaDon = $query->orderByDesc('createdAt')->paginate(15)->withQueryString();
        return view('manager.hoa-don.index', compact('hoaDon'));
    }

    public function create()
    {
        $canHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        return view('manager.hoa-don.create', compact('canHo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'can_ho' => 'required|exists:can_ho,id',
            'thang'  => 'required|integer|min:1|max:12',
            'nam'    => 'required|integer|min:2020',
        ]);

        $exists = HoaDon::where('can_ho', $request->can_ho)
            ->where('thang', $request->thang)
            ->where('nam', $request->nam)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Hóa đơn tháng này đã tồn tại cho căn hộ này.');
        }

        $hoaDon = $this->hoaDonService->taoHoaDon($request->can_ho, $request->thang, $request->nam);
        return redirect()->route('manager.hoa-don.show', $hoaDon)->with('success', 'Tạo hóa đơn thành công.');
    }

    public function show(HoaDon $hoaDon)
    {
        $hoaDon->load(['canHo.toaNha', 'chiTiet', 'lichSuThanhToan']);
        return view('manager.hoa-don.show', compact('hoaDon'));
    }

    public function edit(HoaDon $hoaDon)
    {
        return view('manager.hoa-don.edit', compact('hoaDon'));
    }

    public function update(Request $request, HoaDon $hoaDon)
    {
        $request->validate([
            'han_thanh_toan' => 'nullable|date',
            'trang_thai'     => 'required|integer|in:1,2,3,4',
        ]);

        $old = $hoaDon->toArray();
        $hoaDon->update($request->only('han_thanh_toan', 'trang_thai'));
        AuditLogService::log('UPDATE', 'hoa_don', $hoaDon->id, $old, $hoaDon->fresh()->toArray());

        return redirect()->route('manager.hoa-don.show', $hoaDon)->with('success', 'Cập nhật hóa đơn thành công.');
    }

    public function ghiNhanThanhToan(Request $request, HoaDon $hoaDon)
    {
        $request->validate([
            'so_tien'               => 'required|numeric|min:1000',
            'phuong_thuc_thanh_toan' => 'required|string',
            'ma_giao_dich'          => 'nullable|string',
            'ghi_chu'               => 'nullable|string',
        ]);

        $this->hoaDonService->ghiNhanThanhToan(
            $hoaDon,
            $request->so_tien,
            $request->phuong_thuc_thanh_toan,
            $request->ma_giao_dich
        );

        return back()->with('success', 'Ghi nhận thanh toán thành công.');
    }
}
