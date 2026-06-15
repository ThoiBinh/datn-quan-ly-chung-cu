<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\LoaiCanHo;
use App\Models\ToaNha;
use App\Models\TrangThaiCanHo;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CanHoController extends Controller
{
    public function index(Request $request)
    {
        $query = CanHo::with(['toaNha', 'loaiCanHo', 'trangThai']);

        if ($request->filled('search')) {
            $query->where('so_can_ho', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('toa_nha')) {
            $query->where('toa_nha', $request->toa_nha);
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('tang')) {
            $query->where('tang', $request->tang);
        }

        $canHo = $query->orderBy('so_can_ho')->paginate(15)->withQueryString();
        $toaNha = ToaNha::all();
        $trangThai = TrangThaiCanHo::all();

        return view('manager.can-ho.index', compact('canHo', 'toaNha', 'trangThai'));
    }

    public function create()
    {
        $toaNha = ToaNha::all();
        $loaiCanHo = LoaiCanHo::all();
        $trangThai = TrangThaiCanHo::all();
        return view('manager.can-ho.create', compact('toaNha', 'loaiCanHo', 'trangThai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'toa_nha'    => 'required|exists:toa_nha,id',
            'so_can_ho'  => 'required|string|max:50',
            'tang'       => 'required|integer|min:1',
            'dien_tich'  => 'nullable|numeric|min:0',
            'gia'        => 'nullable|numeric|min:0',
            'loai_can_ho' => 'nullable|exists:loai_can_ho,id',
            'trang_thai' => 'required|exists:trang_thai_can_ho,id',
        ], [
            'toa_nha.required'   => 'Vui lòng chọn tòa nhà.',
            'so_can_ho.required' => 'Vui lòng nhập số căn hộ.',
            'tang.required'      => 'Vui lòng nhập tầng.',
        ]);

        $canHo = CanHo::create($request->only(
            'toa_nha', 'tinh_trang_so_huu', 'so_can_ho', 'tang',
            'dien_tich', 'trang_thai', 'gia', 'loai_can_ho'
        ));

        AuditLogService::log('INSERT', 'can_ho', $canHo->id, null, $canHo->toArray());
        return redirect()->route('manager.can-ho.index')->with('success', 'Thêm căn hộ thành công.');
    }

    public function show(CanHo $canHo)
    {
        $canHo->load(['toaNha', 'loaiCanHo', 'trangThai', 'cuDanHienTai.cuDan', 'phiDichVu', 'phuongTien']);
        return view('manager.can-ho.show', compact('canHo'));
    }

    public function edit(CanHo $canHo)
    {
        $toaNha = ToaNha::all();
        $loaiCanHo = LoaiCanHo::all();
        $trangThai = TrangThaiCanHo::all();
        return view('manager.can-ho.edit', compact('canHo', 'toaNha', 'loaiCanHo', 'trangThai'));
    }

    public function update(Request $request, CanHo $canHo)
    {
        $request->validate([
            'toa_nha'    => 'required|exists:toa_nha,id',
            'so_can_ho'  => 'required|string|max:50',
            'tang'       => 'required|integer|min:1',
            'dien_tich'  => 'nullable|numeric|min:0',
            'gia'        => 'nullable|numeric|min:0',
            'trang_thai' => 'required|exists:trang_thai_can_ho,id',
        ]);

        $old = $canHo->toArray();
        $canHo->update($request->only(
            'toa_nha', 'tinh_trang_so_huu', 'so_can_ho', 'tang',
            'dien_tich', 'trang_thai', 'gia', 'loai_can_ho'
        ));

        AuditLogService::log('UPDATE', 'can_ho', $canHo->id, $old, $canHo->fresh()->toArray());
        return redirect()->route('manager.can-ho.index')->with('success', 'Cập nhật căn hộ thành công.');
    }

    public function destroy(CanHo $canHo)
    {
        if ($canHo->cuDanHienTai()->exists()) {
            return back()->with('error', 'Không thể xóa căn hộ đang có cư dân.');
        }
        AuditLogService::log('DELETE', 'can_ho', $canHo->id, $canHo->toArray(), null);
        $canHo->delete();
        return redirect()->route('manager.can-ho.index')->with('success', 'Xóa căn hộ thành công.');
    }
}
