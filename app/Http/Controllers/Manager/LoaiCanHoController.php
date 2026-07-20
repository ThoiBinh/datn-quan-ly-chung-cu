<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LoaiCanHo;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoaiCanHoController extends Controller
{
    private const SORTABLE = ['id', 'ten_loai_can_ho'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'id';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $tongTatCa    = LoaiCanHo::count();
        $tongDangDung = LoaiCanHo::whereHas('canHo')->count();

        $query = LoaiCanHo::withCount('canHo');

        if ($request->filled('search')) {
            $query->where('ten_loai_can_ho', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('su_dung')) {
            if ($request->su_dung === 'co') {
                $query->whereHas('canHo');
            } elseif ($request->su_dung === 'khong') {
                $query->whereDoesntHave('canHo');
            }
        }

        $dsLoai = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('manager.loai-can-ho.index', compact(
            'dsLoai', 'sort', 'direction', 'tongTatCa', 'tongDangDung'
        ));
    }

    public function create()
    {
        return view('manager.loai-can-ho.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_loai_can_ho' => [
                'required', 'string', 'max:100',
                Rule::unique('loai_can_ho', 'ten_loai_can_ho')->whereNull('deletedAt'),
            ],
        ], [
            'ten_loai_can_ho.required' => 'Vui lòng nhập tên loại căn hộ.',
            'ten_loai_can_ho.unique'   => 'Tên loại căn hộ đã tồn tại.',
            'ten_loai_can_ho.max'      => 'Tên loại căn hộ không được vượt quá 100 ký tự.',
        ]);

        $loai = LoaiCanHo::create(['ten_loai_can_ho' => $request->ten_loai_can_ho]);
        AuditLogService::log('INSERT', 'loai_can_ho', $loai->id, null, $loai->toArray());

        return redirect()->route('manager.loai-can-ho.show', $loai)
            ->with('success', "Thêm loại căn hộ «{$loai->ten_loai_can_ho}» thành công.");
    }

    public function show(LoaiCanHo $loaiCanHo)
    {
        $loaiCanHo->load(['canHo.toaNha', 'canHo.trangThai']);
        return view('manager.loai-can-ho.show', compact('loaiCanHo'));
    }

    public function edit(LoaiCanHo $loaiCanHo)
    {
        return view('manager.loai-can-ho.edit', compact('loaiCanHo'));
    }

    public function update(Request $request, LoaiCanHo $loaiCanHo)
    {
        $request->validate([
            'ten_loai_can_ho' => [
                'required', 'string', 'max:100',
                Rule::unique('loai_can_ho', 'ten_loai_can_ho')->whereNull('deletedAt')->ignore($loaiCanHo->id),
            ],
        ], [
            'ten_loai_can_ho.required' => 'Vui lòng nhập tên loại căn hộ.',
            'ten_loai_can_ho.unique'   => 'Tên loại căn hộ đã tồn tại.',
            'ten_loai_can_ho.max'      => 'Tên loại căn hộ không được vượt quá 100 ký tự.',
        ]);

        $old = $loaiCanHo->toArray();
        $loaiCanHo->update(['ten_loai_can_ho' => $request->ten_loai_can_ho]);
        AuditLogService::log('UPDATE', 'loai_can_ho', $loaiCanHo->id, $old, $loaiCanHo->fresh()->toArray());

        return redirect()->route('manager.loai-can-ho.show', $loaiCanHo)
            ->with('success', 'Cập nhật loại căn hộ thành công.');
    }

    public function destroy(LoaiCanHo $loaiCanHo)
    {
        if ($loaiCanHo->canHo()->exists()) {
            $count = $loaiCanHo->canHo()->count();
            return back()->with('error', "Không thể xóa loại căn hộ «{$loaiCanHo->ten_loai_can_ho}» vì đang có {$count} căn hộ thuộc loại này.");
        }

        AuditLogService::log('DELETE', 'loai_can_ho', $loaiCanHo->id, $loaiCanHo->toArray(), null);
        $loaiCanHo->delete();

        return redirect()->route('manager.loai-can-ho.index')
            ->with('success', "Đã xóa loại căn hộ «{$loaiCanHo->ten_loai_can_ho}».");
    }
}
