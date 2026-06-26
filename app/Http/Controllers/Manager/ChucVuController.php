<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ChucVu;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ChucVuController extends Controller
{
    private const SORTABLE = ['id', 'chuc_vu', 'createdAt', 'updatedAt'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'id';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $tongTatCa    = ChucVu::withTrashed()->count();
        $tongHoatDong = ChucVu::count();
        $tongDaXoa    = ChucVu::onlyTrashed()->count();

        $query = ChucVu::withTrashed()->withCount('nhanVien');

        if (!$request->boolean('show_trashed')) {
            $query->whereNull('deletedAt');
        }

        if ($request->filled('search')) {
            $query->where('chuc_vu', 'like', '%' . $request->search . '%');
        }

        $dsChucVu = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('manager.chuc-vu.index', compact(
            'dsChucVu', 'sort', 'direction', 'tongTatCa', 'tongHoatDong', 'tongDaXoa'
        ));
    }

    public function create()
    {
        return view('manager.chuc-vu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'chuc_vu' => 'required|string|max:100|unique:chuc_vu,chuc_vu',
        ], [
            'chuc_vu.required' => 'Vui lòng nhập tên chức vụ.',
            'chuc_vu.unique'   => 'Tên chức vụ đã tồn tại.',
            'chuc_vu.max'      => 'Tên chức vụ không được vượt quá 100 ký tự.',
        ]);

        $chucVu = ChucVu::create(['chuc_vu' => $request->chuc_vu]);
        AuditLogService::log('INSERT', 'chuc_vu', $chucVu->id, null, $chucVu->toArray());

        return redirect()->route('manager.chuc-vu.show', $chucVu)
            ->with('success', "Thêm chức vụ «{$chucVu->chuc_vu}» thành công.");
    }

    public function show(ChucVu $chucVu)
    {
        $chucVu->load('nhanVien');

        $tongNhanVien = $chucVu->nhanVien->count();
        $tongHoatDong = $chucVu->nhanVien->where('trang_thai', 1)->count();
        $tongBiKhoa   = $chucVu->nhanVien->where('trang_thai', '!=', 1)->count();

        return view('manager.chuc-vu.show', compact(
            'chucVu', 'tongNhanVien', 'tongHoatDong', 'tongBiKhoa'
        ));
    }

    public function edit(ChucVu $chucVu)
    {
        return view('manager.chuc-vu.edit', compact('chucVu'));
    }

    public function update(Request $request, ChucVu $chucVu)
    {
        $request->validate([
            'chuc_vu' => "required|string|max:100|unique:chuc_vu,chuc_vu,{$chucVu->id}",
        ], [
            'chuc_vu.required' => 'Vui lòng nhập tên chức vụ.',
            'chuc_vu.unique'   => 'Tên chức vụ đã tồn tại.',
            'chuc_vu.max'      => 'Tên chức vụ không được vượt quá 100 ký tự.',
        ]);

        $old = $chucVu->toArray();
        $chucVu->update(['chuc_vu' => $request->chuc_vu]);
        AuditLogService::log('UPDATE', 'chuc_vu', $chucVu->id, $old, $chucVu->fresh()->toArray());

        return redirect()->route('manager.chuc-vu.show', $chucVu)
            ->with('success', 'Cập nhật chức vụ thành công.');
    }

    public function destroy(ChucVu $chucVu)
    {
        AuditLogService::log('DELETE', 'chuc_vu', $chucVu->id, $chucVu->toArray(), null);
        $chucVu->delete();

        return redirect()->route('manager.chuc-vu.index')
            ->with('success', "Đã xóa chức vụ «{$chucVu->chuc_vu}».");
    }

    public function restore(int $id)
    {
        $chucVu = ChucVu::withTrashed()->findOrFail($id);
        $chucVu->restore();
        AuditLogService::log('UPDATE', 'chuc_vu', $chucVu->id, ['deletedAt' => $chucVu->deletedAt], ['deletedAt' => null]);

        return redirect()->route('manager.chuc-vu.index')
            ->with('success', "Đã khôi phục chức vụ «{$chucVu->chuc_vu}».");
    }
}
