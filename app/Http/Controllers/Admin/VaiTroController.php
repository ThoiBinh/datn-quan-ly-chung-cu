<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VaiTro;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class VaiTroController extends Controller
{
    private const SORTABLE = ['vai_tro', 'id'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'id';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $tongTatCa    = VaiTro::count();
        $tongDangDung = VaiTro::whereHas('cuDanCanHo')->count();

        $query = VaiTro::withCount('cuDanCanHo');

        if ($request->filled('search')) {
            $query->where('vai_tro', 'like', '%' . $request->search . '%');
        }

        $dsVaiTro = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('admin.vai-tro.index', compact('dsVaiTro', 'sort', 'direction', 'tongTatCa', 'tongDangDung'));
    }

    public function create()
    {
        return view('admin.vai-tro.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'vai_tro' => 'required|string|max:100|unique:vai_tro,vai_tro',
        ], [
            'vai_tro.required' => 'Vui lòng nhập tên vai trò.',
            'vai_tro.unique'   => 'Tên vai trò đã tồn tại.',
            'vai_tro.max'      => 'Tên vai trò không được vượt quá 100 ký tự.',
        ]);

        $vaiTro = VaiTro::create(['vai_tro' => $request->vai_tro]);
        AuditLogService::log('INSERT', 'vai_tro', $vaiTro->id, null, $vaiTro->toArray());

        return redirect()->route('admin.vai-tro.show', $vaiTro)
            ->with('success', "Thêm vai trò «{$vaiTro->vai_tro}» thành công.");
    }

    public function show(VaiTro $vaiTro)
    {
        $vaiTro->load(['cuDanCanHo.cuDan', 'cuDanCanHo.canHo']);
        return view('admin.vai-tro.show', compact('vaiTro'));
    }

    public function edit(VaiTro $vaiTro)
    {
        return view('admin.vai-tro.edit', compact('vaiTro'));
    }

    public function update(Request $request, VaiTro $vaiTro)
    {
        $request->validate([
            'vai_tro' => "required|string|max:100|unique:vai_tro,vai_tro,{$vaiTro->id}",
        ], [
            'vai_tro.required' => 'Vui lòng nhập tên vai trò.',
            'vai_tro.unique'   => 'Tên vai trò đã tồn tại.',
            'vai_tro.max'      => 'Tên vai trò không được vượt quá 100 ký tự.',
        ]);

        $old = $vaiTro->toArray();
        $vaiTro->update(['vai_tro' => $request->vai_tro]);
        AuditLogService::log('UPDATE', 'vai_tro', $vaiTro->id, $old, $vaiTro->fresh()->toArray());

        return redirect()->route('admin.vai-tro.show', $vaiTro)
            ->with('success', 'Cập nhật vai trò thành công.');
    }

    public function destroy(VaiTro $vaiTro)
    {
        if ($vaiTro->cuDanCanHo()->exists()) {
            return back()->with('error', "Không thể xóa vai trò «{$vaiTro->vai_tro}» vì đang được sử dụng bởi {$vaiTro->cuDanCanHo()->count()} bản ghi cư dân - căn hộ.");
        }

        AuditLogService::log('DELETE', 'vai_tro', $vaiTro->id, $vaiTro->toArray(), null);
        $vaiTro->delete();

        return redirect()->route('admin.vai-tro.index')
            ->with('success', "Đã xóa vai trò «{$vaiTro->vai_tro}».");
    }
}
