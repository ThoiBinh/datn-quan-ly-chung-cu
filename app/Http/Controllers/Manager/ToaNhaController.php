<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ToaNha;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ToaNhaController extends Controller
{
    public function index(Request $request)
    {
        $query = ToaNha::withCount('canHo');

        if ($request->filled('search')) {
            $query->where('ten_toa_nha', 'like', '%' . $request->search . '%')
                  ->orWhere('dia_chi', 'like', '%' . $request->search . '%');
        }

        $toaNha = $query->orderByDesc('createdAt')->paginate(15)->withQueryString();
        return view('manager.toa-nha.index', compact('toaNha'));
    }

    public function create()
    {
        return view('manager.toa-nha.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_toa_nha' => 'required|string|max:255',
            'dia_chi'     => 'nullable|string|max:255',
            'so_tang'     => 'nullable|integer|min:1|max:100',
        ], [
            'ten_toa_nha.required' => 'Vui lòng nhập tên tòa nhà.',
        ]);

        $toaNha = ToaNha::create($request->only('ten_toa_nha', 'dia_chi', 'so_tang'));
        AuditLogService::log('INSERT', 'toa_nha', $toaNha->id, null, $toaNha->toArray());

        return redirect()->route('manager.toa-nha.index')->with('success', 'Thêm tòa nhà thành công.');
    }

    public function edit(ToaNha $toaNha)
    {
        return view('manager.toa-nha.edit', compact('toaNha'));
    }

    public function update(Request $request, ToaNha $toaNha)
    {
        $request->validate([
            'ten_toa_nha' => 'required|string|max:255',
            'dia_chi'     => 'nullable|string|max:255',
            'so_tang'     => 'nullable|integer|min:1|max:100',
        ]);

        $old = $toaNha->toArray();
        $toaNha->update($request->only('ten_toa_nha', 'dia_chi', 'so_tang'));
        AuditLogService::log('UPDATE', 'toa_nha', $toaNha->id, $old, $toaNha->fresh()->toArray());

        return redirect()->route('manager.toa-nha.index')->with('success', 'Cập nhật tòa nhà thành công.');
    }

    public function destroy(ToaNha $toaNha)
    {
        if ($toaNha->canHo()->exists()) {
            return back()->with('error', 'Không thể xóa tòa nhà đang có căn hộ.');
        }
        AuditLogService::log('DELETE', 'toa_nha', $toaNha->id, $toaNha->toArray(), null);
        $toaNha->delete();
        return redirect()->route('manager.toa-nha.index')->with('success', 'Xóa tòa nhà thành công.');
    }
}
