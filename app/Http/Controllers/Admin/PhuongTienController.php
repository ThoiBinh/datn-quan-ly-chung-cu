<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\LoaiPhuongTien;
use App\Models\PhuongTien;
use App\Models\ToaNha;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PhuongTienController extends Controller
{
    private const SORTABLE = ['bien_so', 'ngay_dang_ky', 'ngay_huy', 'trang_thai', 'id'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, [...self::SORTABLE, 'ho_ten']) ? $request->sort : 'id';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = PhuongTien::with(['loaiPhuongTien', 'canHo.toaNha', 'canHo.loaiCanHo', 'canHo.chuHo.cuDan']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('bien_so', 'like', "%{$s}%")
                  ->orWhere('ten_phuong_tien', 'like', "%{$s}%")
                  ->orWhereHas('canHo', fn($qc) => $qc->where('so_can_ho', 'like', "%{$s}%"))
                  ->orWhereHas('canHo.toaNha', fn($qt) => $qt->where('ten_toa_nha', 'like', "%{$s}%"))
                  ->orWhereHas('loaiPhuongTien', fn($ql) => $ql->where('ten_loai_phuong_tien', 'like', "%{$s}%"))
                  ->orWhereHas('canHo.cuDanHienTai.cuDan', fn($qr) => $qr
                      ->where('ho_ten_dem', 'like', "%{$s}%")
                      ->orWhere('ten', 'like', "%{$s}%")
                      ->orWhere('cccd', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('loai')) {
            $query->where('loai_phuong_tien', $request->loai);
        }
        if ($request->filled('can_ho')) {
            $query->where('can_ho', $request->can_ho);
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('toa_nha')) {
            $query->whereHas('canHo', fn($q) => $q->where('toa_nha', $request->toa_nha));
        }

        if ($sort === 'ho_ten') {
            $chuHoTen = CuDan::query()
                ->selectRaw("CONCAT(cu_dan.ho_ten_dem, ' ', cu_dan.ten)")
                ->join('cu_dan_can_ho', 'cu_dan_can_ho.cu_dan', '=', 'cu_dan.id')
                ->whereColumn('cu_dan_can_ho.can_ho', 'phuong_tien.can_ho')
                ->where('cu_dan_can_ho.trang_thai', 1)
                ->orderBy('cu_dan_can_ho.id')
                ->limit(1);
            $query->orderBy($chuHoTen, $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $phuongTien = $query->paginate(15)->withQueryString();
        $dsLoai     = LoaiPhuongTien::orderBy('ten_loai_phuong_tien')->get();
        $dsToaNha   = ToaNha::orderBy('ten_toa_nha')->get();
        $dsCanHo    = CanHo::with('toaNha')->orderBy('so_can_ho')->get();

        return view('admin.phuong-tien.index', compact('phuongTien', 'dsLoai', 'dsToaNha', 'dsCanHo', 'sort', 'direction'));
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
            'bien_so'          => [
                'required', 'string', 'max:50',
                Rule::unique('phuong_tien', 'bien_so')->whereNull('deletedAt'),
            ],
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

        $pt = DB::transaction(function () use ($request) {
            $pt = PhuongTien::create([
                'ten_phuong_tien'  => $request->ten_phuong_tien,
                'bien_so'          => strtoupper($request->bien_so),
                'loai_phuong_tien' => $request->loai_phuong_tien,
                'can_ho'           => $request->can_ho,
                'ngay_dang_ky'     => $request->ngay_dang_ky ?: null,
                'trang_thai'       => 1,
                'nguoi_cap_nhat'   => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('INSERT', 'phuong_tien', $pt->id, null, $pt->toArray());

            return $pt;
        });

        return redirect()->route('admin.phuong-tien.index')
            ->with('success', "Thêm phương tiện «{$pt->bien_so}» thành công.");
    }

    public function show(PhuongTien $phuongTien)
    {
        $phuongTien->load([
            'loaiPhuongTien',
            'canHo.toaNha',
            'canHo.loaiCanHo',
            'canHo.trangThai',
            'canHo.thuocTinh',
            'canHo.chuHo.cuDan',
            'nguoiCapNhat',
        ]);
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
            'bien_so'          => [
                'required', 'string', 'max:50',
                Rule::unique('phuong_tien', 'bien_so')->whereNull('deletedAt')->ignore($phuongTien->id),
            ],
            'ten_phuong_tien'  => 'nullable|string|max:255',
            'loai_phuong_tien' => 'required|exists:loai_phuong_tien,id',
            'can_ho'           => 'required|exists:can_ho,id',
            'ngay_dang_ky'     => 'nullable|date',
            'trang_thai'       => 'required|in:0,1',
        ], [
            'bien_so.required'          => 'Vui lòng nhập biển số.',
            'bien_so.unique'            => 'Biển số đã tồn tại trong hệ thống.',
            'loai_phuong_tien.required' => 'Vui lòng chọn loại phương tiện.',
            'can_ho.required'           => 'Vui lòng chọn căn hộ.',
            'trang_thai.required'       => 'Vui lòng chọn trạng thái.',
        ]);

        DB::transaction(function () use ($request, $phuongTien) {
            $old = $phuongTien->toArray();

            // Trạng thái chuyển sang "Đã hủy" -> tự set ngay_huy = now(); chuyển về "Hoạt động" -> tự xóa ngay_huy.
            $ngayHuy = $request->trang_thai == 0
                ? ($phuongTien->ngay_huy ?? now())
                : null;

            $phuongTien->update([
                'ten_phuong_tien'  => $request->ten_phuong_tien,
                'bien_so'          => strtoupper($request->bien_so),
                'loai_phuong_tien' => $request->loai_phuong_tien,
                'can_ho'           => $request->can_ho,
                'ngay_dang_ky'     => $request->ngay_dang_ky ?: null,
                'ngay_huy'         => $ngayHuy,
                'trang_thai'       => $request->trang_thai,
                'nguoi_cap_nhat'   => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'phuong_tien', $phuongTien->id, $old, $phuongTien->fresh()->toArray());
        });

        return redirect()->route('admin.phuong-tien.show', $phuongTien)
            ->with('success', "Cập nhật phương tiện «{$phuongTien->bien_so}» thành công.");
    }

    public function toggleStatus(PhuongTien $phuongTien)
    {
        if ($phuongTien->trang_thai == 0) {
            return back()->with('error', 'Phương tiện đã được hủy trước đó.');
        }

        DB::transaction(function () use ($phuongTien) {
            $old = $phuongTien->toArray();

            $phuongTien->update([
                'trang_thai'     => 0,
                'ngay_huy'       => now(),
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'phuong_tien', $phuongTien->id, $old, $phuongTien->fresh()->toArray());
        });

        return back()->with('success', "Đã hủy phương tiện «{$phuongTien->bien_so}» thành công.");
    }

    public function restore(PhuongTien $phuongTien)
    {
        if ($phuongTien->trang_thai == 1) {
            return back()->with('error', 'Phương tiện đang ở trạng thái hoạt động.');
        }

        DB::transaction(function () use ($phuongTien) {
            $old = $phuongTien->toArray();

            $phuongTien->update([
                'trang_thai'     => 1,
                'ngay_huy'       => null,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'phuong_tien', $phuongTien->id, $old, $phuongTien->fresh()->toArray());
        });

        return back()->with('success', "Đã khôi phục phương tiện «{$phuongTien->bien_so}» thành công.");
    }

    public function destroy(PhuongTien $phuongTien)
    {
        DB::transaction(function () use ($phuongTien) {
            $old = $phuongTien->toArray();

            $phuongTien->delete();

            AuditLogService::log('DELETE', 'phuong_tien', $phuongTien->id, $old, null);
        });

        return redirect()->route('admin.phuong-tien.index')
            ->with('success', "Đã xóa phương tiện «{$phuongTien->bien_so}» thành công.");
    }
}
