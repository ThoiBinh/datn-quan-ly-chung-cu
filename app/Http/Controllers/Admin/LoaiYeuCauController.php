<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoaiYeuCau;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class LoaiYeuCauController extends Controller
{
    private const SORTABLE = ['id', 'name', 'createdAt', 'updatedAt'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'id';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $tongTatCa   = LoaiYeuCau::withTrashed()->count();
        $tongHoatDong = LoaiYeuCau::count();
        $tongDaXoa   = LoaiYeuCau::onlyTrashed()->count();

        $query = LoaiYeuCau::withTrashed()
            ->with('nguoiCapNhat')
            ->withCount('yeuCau');

        if (!$request->boolean('show_trashed')) {
            $query->whereNull('deletedAt');
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $dsLoai = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('admin.loai-yeu-cau.index', compact(
            'dsLoai', 'sort', 'direction', 'tongTatCa', 'tongHoatDong', 'tongDaXoa'
        ));
    }

    public function create()
    {
        return view('admin.loai-yeu-cau.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120|unique:loai_yeu_cau,name',
        ], [
            'name.required' => 'Vui lòng nhập tên loại yêu cầu.',
            'name.unique'   => 'Tên loại yêu cầu đã tồn tại.',
            'name.max'      => 'Tên loại yêu cầu không được vượt quá 120 ký tự.',
        ]);

        $loai = LoaiYeuCau::create([
            'name'            => $request->name,
            'nguoi_cap_nhat'  => auth('nhanvien')->id(),
        ]);
        AuditLogService::log('INSERT', 'loai_yeu_cau', $loai->id, null, $loai->toArray());

        return redirect()->route('admin.loai-yeu-cau.show', $loai)
            ->with('success', "Thêm loại yêu cầu «{$loai->name}» thành công.");
    }

    public function show(LoaiYeuCau $loaiYeuCau)
    {
        $loaiYeuCau->load([
            'nguoiCapNhat',
            'yeuCau.cuDan',
            'yeuCau.nhanVienXuLy',
        ]);
        return view('admin.loai-yeu-cau.show', compact('loaiYeuCau'));
    }

    public function edit(LoaiYeuCau $loaiYeuCau)
    {
        return view('admin.loai-yeu-cau.edit', compact('loaiYeuCau'));
    }

    public function update(Request $request, LoaiYeuCau $loaiYeuCau)
    {
        $request->validate([
            'name' => "required|string|max:120|unique:loai_yeu_cau,name,{$loaiYeuCau->id}",
        ], [
            'name.required' => 'Vui lòng nhập tên loại yêu cầu.',
            'name.unique'   => 'Tên loại yêu cầu đã tồn tại.',
            'name.max'      => 'Tên loại yêu cầu không được vượt quá 120 ký tự.',
        ]);

        $old = $loaiYeuCau->toArray();
        $loaiYeuCau->update([
            'name'           => $request->name,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ]);
        AuditLogService::log('UPDATE', 'loai_yeu_cau', $loaiYeuCau->id, $old, $loaiYeuCau->fresh()->toArray());

        return redirect()->route('admin.loai-yeu-cau.show', $loaiYeuCau)
            ->with('success', 'Cập nhật loại yêu cầu thành công.');
    }

    public function destroy(LoaiYeuCau $loaiYeuCau)
    {
        AuditLogService::log('DELETE', 'loai_yeu_cau', $loaiYeuCau->id, $loaiYeuCau->toArray(), null);
        $loaiYeuCau->delete();

        return redirect()->route('admin.loai-yeu-cau.index')
            ->with('success', "Đã xóa loại yêu cầu «{$loaiYeuCau->name}».");
    }

    public function restore(int $id)
    {
        $loaiYeuCau = LoaiYeuCau::withTrashed()->findOrFail($id);
        $loaiYeuCau->restore();
        AuditLogService::log('UPDATE', 'loai_yeu_cau', $loaiYeuCau->id, ['deletedAt' => $loaiYeuCau->deletedAt], ['deletedAt' => null]);

        return redirect()->route('admin.loai-yeu-cau.show', $loaiYeuCau->id)
            ->with('success', "Đã khôi phục loại yêu cầu «{$loaiYeuCau->name}».");
    }
}
