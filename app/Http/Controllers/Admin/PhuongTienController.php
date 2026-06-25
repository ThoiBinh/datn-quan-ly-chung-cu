<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\LoaiPhuongTien;
use App\Models\PhuongTien;
use App\Models\ToaNha;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class PhuongTienController extends Controller
{
    private const SORTABLE = ['bien_so', 'ngay_dang_ky', 'trang_thai', 'id'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'id';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = PhuongTien::with(['loaiPhuongTien', 'canHo.toaNha', 'canHo.chuHo.cuDan']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('bien_so', 'like', "%$s%")
                ->orWhere('ten_phuong_tien', 'like', "%$s%")
            );
        }
        if ($request->filled('loai')) {
            $query->where('loai_phuong_tien', $request->loai);
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('toa_nha')) {
            $query->whereHas('canHo', fn($q) => $q->where('toa_nha', $request->toa_nha));
        }

        $phuongTien = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $dsLoai     = LoaiPhuongTien::all();
        $dsToaNha   = ToaNha::orderBy('ten_toa_nha')->get();

        return view('admin.phuong-tien.index', compact('phuongTien', 'dsLoai', 'dsToaNha', 'sort', 'direction'));
    }

    public function create()
    {
        $dsCanHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsLoai  = LoaiPhuongTien::all();
        return view('admin.phuong-tien.create', compact('dsCanHo', 'dsLoai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bien_so'          => 'required|string|max:50|unique:phuong_tien,bien_so',
            'ten_phuong_tien'  => 'nullable|string|max:255',
            'loai_phuong_tien' => 'required|exists:loai_phuong_tien,id',
            'can_ho'           => 'required|exists:can_ho,id',
            'ngay_dang_ky'     => 'nullable|date',
        ], [
            'bien_so.required'          => 'Vui lòng nhập biển số.',
            'bien_so.unique'            => 'Biển số đã tồn tại trong hệ thống.',
            'loai_phuong_tien.required' => 'Vui lòng chọn loại phương tiện.',
            'can_ho.required'           => 'Vui lòng chọn căn hộ.',
        ]);

        $pt = PhuongTien::create([
            'ten_phuong_tien'  => $request->ten_phuong_tien,
            'bien_so'          => strtoupper($request->bien_so),
            'loai_phuong_tien' => $request->loai_phuong_tien,
            'can_ho'           => $request->can_ho,
            'ngay_dang_ky'     => $request->ngay_dang_ky ?: null,
            'trang_thai'       => 1,
        ]);

        AuditLogService::log('INSERT', 'phuong_tien', $pt->id, null, $pt->toArray());
        return redirect()->route('admin.phuong-tien.index')
            ->with('success', "Thêm phương tiện «{$pt->bien_so}» thành công.");
    }

    public function show(PhuongTien $phuongTien)
    {
        $phuongTien->load(['loaiPhuongTien', 'canHo.toaNha', 'canHo.chuHo.cuDan']);
        $isActive = $phuongTien->trang_thai == 1;
        return view('admin.phuong-tien.show', compact('phuongTien', 'isActive'));
    }

    public function edit(PhuongTien $phuongTien)
    {
        $dsCanHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsLoai  = LoaiPhuongTien::all();
        return view('admin.phuong-tien.edit', compact('phuongTien', 'dsCanHo', 'dsLoai'));
    }

    public function update(Request $request, PhuongTien $phuongTien)
    {
        $request->validate([
            'bien_so'          => 'required|string|max:50|unique:phuong_tien,bien_so,' . $phuongTien->id,
            'ten_phuong_tien'  => 'nullable|string|max:255',
            'loai_phuong_tien' => 'required|exists:loai_phuong_tien,id',
            'can_ho'           => 'required|exists:can_ho,id',
            'ngay_dang_ky'     => 'nullable|date',
            'ngay_huy'         => 'nullable|date',
        ], [
            'bien_so.required'          => 'Vui lòng nhập biển số.',
            'bien_so.unique'            => 'Biển số đã tồn tại trong hệ thống.',
            'loai_phuong_tien.required' => 'Vui lòng chọn loại phương tiện.',
            'can_ho.required'           => 'Vui lòng chọn căn hộ.',
        ]);

        $old = $phuongTien->toArray();
        $phuongTien->update([
            'ten_phuong_tien'  => $request->ten_phuong_tien,
            'bien_so'          => strtoupper($request->bien_so),
            'loai_phuong_tien' => $request->loai_phuong_tien,
            'can_ho'           => $request->can_ho,
            'ngay_dang_ky'     => $request->ngay_dang_ky ?: null,
            'ngay_huy'         => $request->ngay_huy ?: null,
            'nguoi_cap_nhat'     => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('UPDATE', 'phuong_tien', $phuongTien->id, $old, $phuongTien->fresh()->toArray());
        return redirect()->route('admin.phuong-tien.show', $phuongTien)
            ->with('success', "Cập nhật phương tiện «{$phuongTien->bien_so}» thành công.");
    }

    public function toggleStatus(PhuongTien $phuongTien)
    {
        $old    = $phuongTien->toArray();
        $newVal = $phuongTien->trang_thai == 1 ? 0 : 1;
        $phuongTien->update(['trang_thai' => $newVal]);
        AuditLogService::log('UPDATE', 'phuong_tien', $phuongTien->id, $old, $phuongTien->fresh()->toArray());
        $msg = $newVal == 1 ? 'Mở khóa' : 'Khóa';
        return back()->with('success', "$msg phương tiện «{$phuongTien->bien_so}» thành công.");
    }
}
