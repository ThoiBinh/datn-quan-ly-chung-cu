<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\HoaDon;
use App\Models\ToaNha;
use App\Services\AuditLogService;
use App\Services\HoaDonService;
use Illuminate\Http\Request;

class HoaDonController extends Controller
{
    public function __construct(private HoaDonService $hoaDonService) {}

    private const SORTABLE = ['createdAt', 'tong_tien', 'han_thanh_toan', 'thang', 'nam'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = HoaDon::with(['canHo.toaNha', 'canHo.chuHo.cuDan']);

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
        if ($request->filled('toa_nha')) {
            $query->whereHas('canHo', fn($q) => $q->where('toa_nha', $request->toa_nha));
        }

        $hoaDon   = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $dsToaNha = ToaNha::orderBy('ten_toa_nha')->get();

        return view('admin.hoa-don.index', compact('hoaDon', 'dsToaNha', 'sort', 'direction'));
    }

    public function create()
    {
        $dsCanHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        return view('admin.hoa-don.create', compact('dsCanHo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'can_ho' => 'required|exists:can_ho,id',
            'thang'  => 'required|integer|min:1|max:12',
            'nam'    => 'required|integer|min:2020',
        ], [
            'can_ho.required' => 'Vui lòng chọn căn hộ.',
            'thang.required'  => 'Vui lòng chọn tháng.',
            'nam.required'    => 'Vui lòng nhập năm.',
        ]);

        $exists = HoaDon::where('can_ho', $request->can_ho)
            ->where('thang', $request->thang)
            ->where('nam', $request->nam)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Hóa đơn tháng này đã tồn tại cho căn hộ này.');
        }

        $hoaDon = $this->hoaDonService->taoHoaDon($request->can_ho, $request->thang, $request->nam);
        return redirect()->route('admin.hoa-don.show', $hoaDon)
            ->with('success', 'Tạo hóa đơn thành công.');
    }

    public function show(HoaDon $hoaDon)
    {
        $hoaDon->load(['canHo.toaNha', 'canHo.chuHo.cuDan', 'chiTiet', 'lichSuThanhToan.nguoiThanhToan', 'lichSuThanhToan.nguonTao']);
        $isCanHuy = $hoaDon->trang_thai != HoaDon::TRANG_THAI_DA_HUY;
        return view('admin.hoa-don.show', compact('hoaDon', 'isCanHuy'));
    }

    public function edit(HoaDon $hoaDon)
    {
        return view('admin.hoa-don.edit', compact('hoaDon'));
    }

    public function update(Request $request, HoaDon $hoaDon)
    {
        $request->validate([
            'han_thanh_toan' => 'nullable|date',
            'trang_thai'     => 'required|integer|in:1,2,3,4',
        ], [
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'       => 'Trạng thái không hợp lệ.',
        ]);

        $old = $hoaDon->toArray();
        $hoaDon->update($request->only('han_thanh_toan', 'trang_thai'));
        AuditLogService::log('UPDATE', 'hoa_don', $hoaDon->id, $old, $hoaDon->fresh()->toArray());

        return redirect()->route('admin.hoa-don.show', $hoaDon)
            ->with('success', 'Cập nhật hóa đơn thành công.');
    }

    public function toggleStatus(HoaDon $hoaDon)
    {
        $old    = $hoaDon->toArray();
        $newVal = $hoaDon->trang_thai == HoaDon::TRANG_THAI_DA_HUY
            ? HoaDon::TRANG_THAI_CHUA_THANH_TOAN
            : HoaDon::TRANG_THAI_DA_HUY;

        $hoaDon->update(['trang_thai' => $newVal]);
        AuditLogService::log('UPDATE', 'hoa_don', $hoaDon->id, $old, $hoaDon->fresh()->toArray());

        $msg = $newVal == HoaDon::TRANG_THAI_DA_HUY ? 'Hủy' : 'Khôi phục';
        return back()->with('success', "$msg hóa đơn «{$hoaDon->ma_thanh_toan}» thành công.");
    }
}
