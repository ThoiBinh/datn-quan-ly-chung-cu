<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CauHinhThanhToan;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CauHinhThanhToanController extends Controller
{
    private const SORTABLE = ['createdAt', 'loai_phuong_thuc', 'trang_thai'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = CauHinhThanhToan::with('nguoiCapNhat');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('ten_nha_cung_cap', 'like', "%$s%")
                ->orWhere('ten_chu_tai_khoan', 'like', "%$s%")
                ->orWhere('loai_phuong_thuc', 'like', "%$s%")
            );
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $dsCauHinh    = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $dsLoai       = CauHinhThanhToan::select('loai_phuong_thuc')->distinct()->pluck('loai_phuong_thuc');
        $tongHoatDong = CauHinhThanhToan::where('trang_thai', 1)->count();
        $tongVoHieu   = CauHinhThanhToan::where('trang_thai', 0)->count();

        return view('manager.cau-hinh-thanh-toan.index', compact('dsCauHinh', 'dsLoai', 'sort', 'direction', 'tongHoatDong', 'tongVoHieu'));
    }

    public function create()
    {
        $dsLoai = CauHinhThanhToan::select('loai_phuong_thuc')->distinct()->pluck('loai_phuong_thuc');
        return view('manager.cau-hinh-thanh-toan.create', compact('dsLoai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'loai_phuong_thuc'    => 'required|string|max:100',
            'ten_nha_cung_cap'    => 'nullable|string|max:150',
            'dinh_danh_thu_huong' => 'nullable|string|max:150',
            'ma_nhan_dien'        => 'nullable|string|max:150',
            'ten_chu_tai_khoan'   => 'nullable|string|max:150',
        ], [
            'loai_phuong_thuc.required' => 'Vui lòng nhập loại phương thức.',
        ]);

        $ch = CauHinhThanhToan::create([
            'loai_phuong_thuc'    => $request->loai_phuong_thuc,
            'ten_nha_cung_cap'    => $request->ten_nha_cung_cap ?: null,
            'dinh_danh_thu_huong' => $request->dinh_danh_thu_huong ?: null,
            'ma_nhan_dien'        => $request->ma_nhan_dien ?: null,
            'ten_chu_tai_khoan'   => $request->ten_chu_tai_khoan ?: null,
            'trang_thai'          => 1,
            'nguoi_cap_nhat'      => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('INSERT', 'cau_hinh_thanh_toan', $ch->id, null, $ch->toArray());

        return redirect()->route('manager.cau-hinh-thanh-toan.show', $ch)
            ->with('success', "Thêm cấu hình «{$ch->ten_nha_cung_cap}» thành công.");
    }

    public function show(CauHinhThanhToan $cauHinhThanhToan)
    {
        $cauHinhThanhToan->load('nguoiCapNhat');
        return view('manager.cau-hinh-thanh-toan.show', compact('cauHinhThanhToan'));
    }

    public function edit(CauHinhThanhToan $cauHinhThanhToan)
    {
        $dsLoai = CauHinhThanhToan::select('loai_phuong_thuc')->distinct()->pluck('loai_phuong_thuc');
        return view('manager.cau-hinh-thanh-toan.edit', compact('cauHinhThanhToan', 'dsLoai'));
    }

    public function update(Request $request, CauHinhThanhToan $cauHinhThanhToan)
    {
        $request->validate([
            'loai_phuong_thuc'    => 'required|string|max:100',
            'ten_nha_cung_cap'    => 'nullable|string|max:150',
            'dinh_danh_thu_huong' => 'nullable|string|max:150',
            'ma_nhan_dien'        => 'nullable|string|max:150',
            'ten_chu_tai_khoan'   => 'nullable|string|max:150',
        ], [
            'loai_phuong_thuc.required' => 'Vui lòng nhập loại phương thức.',
        ]);

        $old = $cauHinhThanhToan->toArray();
        $cauHinhThanhToan->update([
            'loai_phuong_thuc'    => $request->loai_phuong_thuc,
            'ten_nha_cung_cap'    => $request->ten_nha_cung_cap ?: null,
            'dinh_danh_thu_huong' => $request->dinh_danh_thu_huong ?: null,
            'ma_nhan_dien'        => $request->ma_nhan_dien ?: null,
            'ten_chu_tai_khoan'   => $request->ten_chu_tai_khoan ?: null,
            'nguoi_cap_nhat'      => auth('nhanvien')->id(),
        ]);
        AuditLogService::log('UPDATE', 'cau_hinh_thanh_toan', $cauHinhThanhToan->id, $old, $cauHinhThanhToan->fresh()->toArray());

        return redirect()->route('manager.cau-hinh-thanh-toan.show', $cauHinhThanhToan)
            ->with('success', 'Cập nhật cấu hình thành công.');
    }

    public function toggleStatus(CauHinhThanhToan $cauHinhThanhToan)
    {
        $old    = $cauHinhThanhToan->toArray();
        $newVal = $cauHinhThanhToan->trang_thai == 1 ? 0 : 1;
        $cauHinhThanhToan->update([
            'trang_thai'     => $newVal,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ]);
        AuditLogService::log('UPDATE', 'cau_hinh_thanh_toan', $cauHinhThanhToan->id, $old, $cauHinhThanhToan->fresh()->toArray());
        $msg = $newVal == 1 ? 'Kích hoạt' : 'Vô hiệu hóa';
        return back()->with('success', "$msg cấu hình thành công.");
    }
}
