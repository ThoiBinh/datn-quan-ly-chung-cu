<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StorePhuongTienRequest;
use App\Http\Requests\Manager\UpdatePhuongTienRequest;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\LoaiPhuongTien;
use App\Models\PhuongTien;
use App\Models\ToaNha;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhuongTienController extends Controller
{
    private const SORTABLE = ['bien_so', 'so_can_ho', 'ho_ten', 'ngay_dang_ky', 'ngay_huy'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort_by, self::SORTABLE) ? $request->sort_by : 'ngay_dang_ky';
        $direction = $request->sort_dir === 'asc' ? 'asc' : 'desc';

        $query = PhuongTien::query()
            ->with(['loaiPhuongTien', 'canHo.toaNha', 'canHo.loaiCanHo', 'canHo.chuHo.cuDan']);

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

        if ($request->filled('loai_phuong_tien')) {
            $query->where('loai_phuong_tien', $request->loai_phuong_tien);
        }
        if ($request->filled('can_ho')) {
            $query->where('can_ho', $request->can_ho);
        }
        if ($request->filled('toa_nha')) {
            $query->whereHas('canHo', fn($q) => $q->where('toa_nha', $request->toa_nha));
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        if ($sort === 'so_can_ho') {
            $query->select('phuong_tien.*')
                  ->leftJoin('can_ho', 'can_ho.id', '=', 'phuong_tien.can_ho')
                  ->orderBy('can_ho.so_can_ho', $direction);
        } elseif ($sort === 'ho_ten') {
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

        $stats = [
            'tong'            => PhuongTien::count(),
            'dang_hoat_dong'  => PhuongTien::where('trang_thai', 1)->count(),
            'da_khoa'         => PhuongTien::where('trang_thai', 0)->count(),
        ];
        $statsTheoLoai = LoaiPhuongTien::withCount('phuongTien')->orderBy('ten_loai_phuong_tien')->get();

        $dsLoai   = LoaiPhuongTien::orderBy('ten_loai_phuong_tien')->get();
        $dsToaNha = ToaNha::orderBy('ten_toa_nha')->get();
        $dsCanHo  = CanHo::with('toaNha')->orderBy('so_can_ho')->get();

        return view('manager.phuong-tien.index', compact(
            'phuongTien', 'stats', 'statsTheoLoai', 'dsLoai', 'dsToaNha', 'dsCanHo', 'sort', 'direction'
        ));
    }

    public function create()
    {
        $dsCanHo = CanHo::with(['toaNha', 'loaiCanHo', 'cuDanHienTai.cuDan'])->orderBy('so_can_ho')->get();
        $dsLoai  = LoaiPhuongTien::orderBy('ten_loai_phuong_tien')->get();

        return view('manager.phuong-tien.create', compact('dsCanHo', 'dsLoai'));
    }

    public function store(StorePhuongTienRequest $request)
    {
        $validated = $request->validated();

        $phuongTien = DB::transaction(function () use ($validated) {
            $pt = PhuongTien::create([
                'ten_phuong_tien'  => $validated['ten_phuong_tien'] ?? null,
                'bien_so'          => strtoupper(trim($validated['bien_so'])),
                'loai_phuong_tien' => $validated['loai_phuong_tien'],
                'can_ho'           => $validated['can_ho'],
                'ngay_dang_ky'     => $validated['ngay_dang_ky'] ?: now(),
                'trang_thai'       => 1,
                'nguoi_cap_nhat'   => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('INSERT', 'phuong_tien', $pt->id, null, $pt->toArray());

            return $pt;
        });

        return redirect()->route('manager.phuong-tien.index')
            ->with('success', "Thêm phương tiện «{$phuongTien->bien_so}» thành công.");
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
            'canHo.cuDanHienTai.cuDan',
            'canHo.cuDanHienTai.vaiTro',
            'nguoiCapNhat',
        ]);

        return view('manager.phuong-tien.show', compact('phuongTien'));
    }

    public function edit(PhuongTien $phuongTien)
    {
        $dsCanHo = CanHo::with(['toaNha', 'loaiCanHo', 'cuDanHienTai.cuDan'])->orderBy('so_can_ho')->get();
        $dsLoai  = LoaiPhuongTien::orderBy('ten_loai_phuong_tien')->get();

        return view('manager.phuong-tien.edit', compact('phuongTien', 'dsCanHo', 'dsLoai'));
    }

    public function update(UpdatePhuongTienRequest $request, PhuongTien $phuongTien)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $phuongTien) {
            $old = $phuongTien->toArray();

            $ngayHuy = $validated['trang_thai'] == 0
                ? ($validated['ngay_huy'] ?: now())
                : null;

            $phuongTien->update([
                'ten_phuong_tien'  => $validated['ten_phuong_tien'] ?? null,
                'bien_so'          => strtoupper(trim($validated['bien_so'])),
                'loai_phuong_tien' => $validated['loai_phuong_tien'],
                'can_ho'           => $validated['can_ho'],
                'ngay_dang_ky'     => $validated['ngay_dang_ky'] ?: $phuongTien->ngay_dang_ky,
                'ngay_huy'         => $ngayHuy,
                'trang_thai'       => $validated['trang_thai'],
                'nguoi_cap_nhat'   => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'phuong_tien', $phuongTien->id, $old, $phuongTien->fresh()->toArray());
        });

        return redirect()->route('manager.phuong-tien.show', $phuongTien)
            ->with('success', "Cập nhật phương tiện «{$phuongTien->bien_so}» thành công.");
    }

    public function destroy(PhuongTien $phuongTien)
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

        return redirect()->route('manager.phuong-tien.index')
            ->with('success', "Đã hủy phương tiện «{$phuongTien->bien_so}» (dữ liệu được giữ lại).");
    }

    public function xoaMem(PhuongTien $phuongTien)
    {
        DB::transaction(function () use ($phuongTien) {
            $old = $phuongTien->toArray();

            $phuongTien->delete();

            AuditLogService::log('DELETE', 'phuong_tien', $phuongTien->id, $old, null);
        });

        return redirect()->route('manager.phuong-tien.index')
            ->with('success', "Đã xóa phương tiện «{$phuongTien->bien_so}» thành công.");
    }
}
