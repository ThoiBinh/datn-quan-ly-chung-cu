<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BangTin;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BangTinController extends Controller
{
    private const SORTABLE = ['createdAt', 'tieu_de', 'nguoi_tao'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = BangTin::withTrashed()->with(['nguoiTao', 'nguoiCapNhat']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('tieu_de', 'like', "%$s%")
                ->orWhere('noi_dung', 'like', "%$s%")
            );
        }

        if ($request->filled('trang_thai')) {
            if ($request->trang_thai === 'an') {
                $query->onlyTrashed();
            } elseif ($request->trang_thai === 'hien') {
                $query->whereNull('deletedAt');
            }
        }

        $dsBangTin = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $tongHien  = BangTin::count();
        $tongAn    = BangTin::onlyTrashed()->count();

        return view('admin.bang-tin.index', compact(
            'dsBangTin', 'sort', 'direction', 'tongHien', 'tongAn'
        ));
    }

    public function create()
    {
        return view('admin.bang-tin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tieu_de'  => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'tieu_de.required'  => 'Vui lòng nhập tiêu đề.',
            'noi_dung.required' => 'Vui lòng nhập nội dung.',
            'hinh_anh.image'    => 'Tệp tải lên phải là hình ảnh.',
            'hinh_anh.mimes'    => 'Ảnh phải có định dạng jpeg, png, jpg hoặc webp.',
            'hinh_anh.max'      => 'Dung lượng ảnh không được vượt quá 2MB.',
        ]);

        $hinhUrl = null;
        if ($request->hasFile('hinh_anh')) {
            $hinhUrl = $request->file('hinh_anh')->store('bang-tin', 'public');
        }

        $bt = BangTin::create([
            'tieu_de'        => $request->tieu_de,
            'noi_dung'       => $request->noi_dung,
            'hinh_url'       => $hinhUrl,
            'nguoi_tao'      => auth('nhanvien')->id(),
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('INSERT', 'bang_tin', $bt->id, null, $bt->toArray());

        return redirect()->route('admin.bang-tin.show', $bt)
            ->with('success', "Đăng bài «{$bt->tieu_de}» thành công.");
    }

    public function show(BangTin $bangTin)
    {
        $bangTin->load(['nguoiTao', 'nguoiCapNhat']);
        return view('admin.bang-tin.show', compact('bangTin'));
    }

    public function edit(BangTin $bangTin)
    {
        return view('admin.bang-tin.edit', compact('bangTin'));
    }

    public function update(Request $request, BangTin $bangTin)
    {
        $request->validate([
            'tieu_de'  => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'xoa_hinh' => 'nullable|boolean',
        ], [
            'tieu_de.required'  => 'Vui lòng nhập tiêu đề.',
            'noi_dung.required' => 'Vui lòng nhập nội dung.',
            'hinh_anh.image'    => 'Tệp tải lên phải là hình ảnh.',
            'hinh_anh.mimes'    => 'Ảnh phải có định dạng jpeg, png, jpg hoặc webp.',
            'hinh_anh.max'      => 'Dung lượng ảnh không được vượt quá 2MB.',
        ]);

        $old = $bangTin->toArray();

        $data = [
            'tieu_de'        => $request->tieu_de,
            'noi_dung'       => $request->noi_dung,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ];

        if ($request->hasFile('hinh_anh')) {
            if ($bangTin->hinh_url && Storage::disk('public')->exists($bangTin->hinh_url)) {
                Storage::disk('public')->delete($bangTin->hinh_url);
            }
            $data['hinh_url'] = $request->file('hinh_anh')->store('bang-tin', 'public');
        } elseif ($request->boolean('xoa_hinh')) {
            if ($bangTin->hinh_url && Storage::disk('public')->exists($bangTin->hinh_url)) {
                Storage::disk('public')->delete($bangTin->hinh_url);
            }
            $data['hinh_url'] = null;
        }

        $bangTin->update($data);
        AuditLogService::log('UPDATE', 'bang_tin', $bangTin->id, $old, $bangTin->fresh()->toArray());

        return redirect()->route('admin.bang-tin.show', $bangTin)
            ->with('success', 'Cập nhật bài đăng thành công.');
    }

    public function toggleHide(BangTin $bangTin)
    {
        $old = $bangTin->toArray();
        $bangTin->delete();
        AuditLogService::log('UPDATE', 'bang_tin', $bangTin->id, $old, array_merge($old, ['deletedAt' => now()]));
        return back()->with('success', "Đã ẩn bài «{$bangTin->tieu_de}».");
    }

    public function restore(int $id)
    {
        $bangTin = BangTin::withTrashed()->findOrFail($id);
        $old = $bangTin->toArray();
        $bangTin->restore();
        AuditLogService::log('UPDATE', 'bang_tin', $bangTin->id, $old, $bangTin->fresh()->toArray());
        return back()->with('success', "Đã khôi phục bài «{$bangTin->tieu_de}».");
    }
}
