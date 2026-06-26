<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoaiPhuongTien;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class LoaiPhuongTienController extends Controller
{
    private const SORTABLE = ['id', 'ten_loai_phuong_tien'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'id';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $tongTatCa    = LoaiPhuongTien::count();
        $tongDangDung = LoaiPhuongTien::whereHas('phuongTien')->count();

        $query = LoaiPhuongTien::withCount('phuongTien');

        if ($request->filled('search')) {
            $query->where('ten_loai_phuong_tien', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('su_dung')) {
            if ($request->su_dung === 'co') {
                $query->whereHas('phuongTien');
            } elseif ($request->su_dung === 'khong') {
                $query->whereDoesntHave('phuongTien');
            }
        }

        $dsLoai = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('admin.loai-phuong-tien.index', compact(
            'dsLoai', 'sort', 'direction', 'tongTatCa', 'tongDangDung'
        ));
    }

    public function create()
    {
        return view('admin.loai-phuong-tien.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_loai_phuong_tien' => 'required|string|max:100|unique:loai_phuong_tien,ten_loai_phuong_tien',
        ], [
            'ten_loai_phuong_tien.required' => 'Vui lòng nhập tên loại phương tiện.',
            'ten_loai_phuong_tien.unique'   => 'Tên loại phương tiện đã tồn tại.',
            'ten_loai_phuong_tien.max'      => 'Tên loại phương tiện không được vượt quá 100 ký tự.',
        ]);

        $loai = LoaiPhuongTien::create(['ten_loai_phuong_tien' => $request->ten_loai_phuong_tien]);
        AuditLogService::log('INSERT', 'loai_phuong_tien', $loai->id, null, $loai->toArray());

        return redirect()->route('admin.loai-phuong-tien.show', $loai)
            ->with('success', "Thêm loại phương tiện «{$loai->ten_loai_phuong_tien}» thành công.");
    }

    public function show(LoaiPhuongTien $loaiPhuongTien)
    {
        $loaiPhuongTien->load(['phuongTien.canHo.toaNha']);
        return view('admin.loai-phuong-tien.show', compact('loaiPhuongTien'));
    }

    public function edit(LoaiPhuongTien $loaiPhuongTien)
    {
        return view('admin.loai-phuong-tien.edit', compact('loaiPhuongTien'));
    }

    public function update(Request $request, LoaiPhuongTien $loaiPhuongTien)
    {
        $request->validate([
            'ten_loai_phuong_tien' => "required|string|max:100|unique:loai_phuong_tien,ten_loai_phuong_tien,{$loaiPhuongTien->id}",
        ], [
            'ten_loai_phuong_tien.required' => 'Vui lòng nhập tên loại phương tiện.',
            'ten_loai_phuong_tien.unique'   => 'Tên loại phương tiện đã tồn tại.',
            'ten_loai_phuong_tien.max'      => 'Tên loại phương tiện không được vượt quá 100 ký tự.',
        ]);

        $old = $loaiPhuongTien->toArray();
        $loaiPhuongTien->update(['ten_loai_phuong_tien' => $request->ten_loai_phuong_tien]);
        AuditLogService::log('UPDATE', 'loai_phuong_tien', $loaiPhuongTien->id, $old, $loaiPhuongTien->fresh()->toArray());

        return redirect()->route('admin.loai-phuong-tien.show', $loaiPhuongTien)
            ->with('success', 'Cập nhật loại phương tiện thành công.');
    }

    public function destroy(LoaiPhuongTien $loaiPhuongTien)
    {
        if ($loaiPhuongTien->phuongTien()->exists()) {
            $count = $loaiPhuongTien->phuongTien()->count();
            return back()->with('error', "Không thể xóa loại phương tiện «{$loaiPhuongTien->ten_loai_phuong_tien}» vì đang có {$count} phương tiện thuộc loại này.");
        }

        AuditLogService::log('DELETE', 'loai_phuong_tien', $loaiPhuongTien->id, $loaiPhuongTien->toArray(), null);
        $loaiPhuongTien->delete();

        return redirect()->route('admin.loai-phuong-tien.index')
            ->with('success', "Đã xóa loại phương tiện «{$loaiPhuongTien->ten_loai_phuong_tien}».");
    }
}
