<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ThuocTinh;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ThuocTinhController extends Controller
{
    private const SORTABLE = ['ten_thuoc_tinh', 'createdAt', 'updatedAt'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $tongHoatDong = ThuocTinh::count();
        $tongDaXoa    = ThuocTinh::onlyTrashed()->count();

        $query = ThuocTinh::withCount('canHo');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('ten_thuoc_tinh', 'like', "%{$s}%");
        }

        if ($request->boolean('show_trashed')) {
            $query->onlyTrashed();
        }

        $dsThuocTinh = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('manager.thuoc-tinh.index', compact(
            'dsThuocTinh', 'sort', 'direction', 'tongHoatDong', 'tongDaXoa'
        ));
    }

    public function create()
    {
        return view('manager.thuoc-tinh.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_thuoc_tinh' => 'required|string|max:150|unique:thuoc_tinh,ten_thuoc_tinh',
        ], [
            'ten_thuoc_tinh.required' => 'Vui lòng nhập tên thuộc tính.',
            'ten_thuoc_tinh.unique'   => 'Tên thuộc tính đã tồn tại.',
            'ten_thuoc_tinh.max'      => 'Tên thuộc tính không được vượt quá 150 ký tự.',
        ]);

        $thuocTinh = ThuocTinh::create(['ten_thuoc_tinh' => $request->ten_thuoc_tinh]);
        AuditLogService::log('INSERT', 'thuoc_tinh', $thuocTinh->id, null, $thuocTinh->toArray());

        return redirect()->route('manager.thuoc-tinh.show', $thuocTinh)
            ->with('success', "Thêm thuộc tính «{$thuocTinh->ten_thuoc_tinh}» thành công.");
    }

    public function show(ThuocTinh $thuocTinh)
    {
        $thuocTinh->load('canHo');
        return view('manager.thuoc-tinh.show', compact('thuocTinh'));
    }

    public function edit(ThuocTinh $thuocTinh)
    {
        return view('manager.thuoc-tinh.edit', compact('thuocTinh'));
    }

    public function update(Request $request, ThuocTinh $thuocTinh)
    {
        $request->validate([
            'ten_thuoc_tinh' => "required|string|max:150|unique:thuoc_tinh,ten_thuoc_tinh,{$thuocTinh->id}",
        ], [
            'ten_thuoc_tinh.required' => 'Vui lòng nhập tên thuộc tính.',
            'ten_thuoc_tinh.unique'   => 'Tên thuộc tính đã tồn tại.',
            'ten_thuoc_tinh.max'      => 'Tên thuộc tính không được vượt quá 150 ký tự.',
        ]);

        $old = $thuocTinh->toArray();
        $thuocTinh->update(['ten_thuoc_tinh' => $request->ten_thuoc_tinh]);
        AuditLogService::log('UPDATE', 'thuoc_tinh', $thuocTinh->id, $old, $thuocTinh->fresh()->toArray());

        return redirect()->route('manager.thuoc-tinh.show', $thuocTinh)
            ->with('success', 'Cập nhật thuộc tính thành công.');
    }

    public function destroy(ThuocTinh $thuocTinh)
    {
        AuditLogService::log('DELETE', 'thuoc_tinh', $thuocTinh->id, $thuocTinh->toArray(), null);
        $thuocTinh->delete();

        return redirect()->route('manager.thuoc-tinh.index')
            ->with('success', "Đã xóa thuộc tính «{$thuocTinh->ten_thuoc_tinh}».");
    }

    public function restore(int $id)
    {
        $thuocTinh = ThuocTinh::withTrashed()->findOrFail($id);
        $thuocTinh->restore();
        AuditLogService::log('UPDATE', 'thuoc_tinh', $thuocTinh->id, null, $thuocTinh->fresh()->toArray());

        return back()->with('success', "Khôi phục thuộc tính «{$thuocTinh->ten_thuoc_tinh}» thành công.");
    }
}
