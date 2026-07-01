<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreCuDanRequest;
use App\Http\Requests\Manager\UpdateCuDanRequest;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\CuDanCanHo;
use App\Models\HoaDon;
use App\Models\PhuongTien;
use App\Models\ToaNha;
use App\Models\VaiTro;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CuDanController extends Controller
{
    public function index(Request $request)
    {
        $query = CuDan::with([
            'cuDanCanHo' => fn($q) => $q->where('trang_thai', 1)
                ->with(['canHo.toaNha', 'vaiTro']),
        ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ho_ten_dem', 'like', "%{$s}%")
                  ->orWhere('ten', 'like', "%{$s}%")
                  ->orWhere('sdt', 'like', "%{$s}%")
                  ->orWhere('cccd', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        if ($request->input('gioi_tinh') !== null && $request->input('gioi_tinh') !== '') {
            $query->where('gioi_tinh', $request->gioi_tinh);
        }

        if ($request->filled('toa_nha') || $request->filled('can_ho')) {
            $query->whereHas('cuDanCanHo', function ($q) use ($request) {
                $q->where('cu_dan_can_ho.trang_thai', 1)
                  ->whereHas('canHo', function ($qc) use ($request) {
                      if ($request->filled('toa_nha')) {
                          $qc->where('toa_nha', $request->toa_nha);
                      }
                      if ($request->filled('can_ho')) {
                          $qc->where('id', $request->can_ho);
                      }
                  });
            });
        }

        $allowedSorts = ['createdAt', 'updatedAt', 'ho_ten_dem', 'ten'];
        $sortBy  = in_array($request->sort_by, $allowedSorts) ? $request->sort_by : 'createdAt';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $cuDanList = $query->paginate(15)->withQueryString();

        $stats = [
            'tong'             => CuDan::count(),
            'dang_cu_tru'      => CuDan::where('trang_thai', 1)->count(),
            'tam_vang'         => CuDan::where('trang_thai', 2)->count(),
            'da_chuyen_di'     => CuDan::where('trang_thai', 3)->count(),
            'chu_ho'           => CuDanCanHo::where('trang_thai', 1)
                ->whereHas('vaiTro', fn($q) => $q->where('vai_tro', 'Chủ hộ'))->count(),
            'thanh_vien'       => CuDanCanHo::where('trang_thai', 1)
                ->whereHas('vaiTro', fn($q) => $q->where('vai_tro', '!=', 'Chủ hộ'))->count(),
            'tong_phuong_tien' => PhuongTien::count(),
            'can_ho_dang_o'    => CuDanCanHo::where('trang_thai', 1)->distinct('can_ho')->count('can_ho'),
        ];

        $toaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        $canHoList = CanHo::with('toaNha')->orderBy('so_can_ho')->get();

        return view('manager.cu-dan.index', compact('cuDanList', 'stats', 'toaNha', 'canHoList'));
    }

    public function create()
    {
        $canHoList = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $vaiTro    = VaiTro::orderBy('id')->get();
        $toaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        return view('manager.cu-dan.create', compact('canHoList', 'vaiTro', 'toaNha'));
    }

    public function store(StoreCuDanRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $matKhau = !empty($validated['mat_khau'])
                ? Hash::make($validated['mat_khau'])
                : Hash::make('12345678');

            $gt = $validated['gioi_tinh'] ?? null;

            $cuDan = CuDan::create([
                'ho_ten_dem'     => $validated['ho_ten_dem'] ?? '',
                'ten'            => $validated['ten'],
                'sdt'            => $validated['sdt'] ?? null,
                'cccd'           => $validated['cccd'] ?? null,
                'email'          => $validated['email'] ?? null,
                'ngay_sinh'      => $validated['ngay_sinh'] ?? null,
                'gioi_tinh'      => ($gt !== null && $gt !== '') ? (int) $gt : null,
                'tinh'           => $validated['tinh'] ?? null,
                'xa'             => $validated['xa'] ?? null,
                'dia_chi'        => $validated['dia_chi'] ?? null,
                'mat_khau'       => $matKhau,
                'trang_thai'     => 1,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            if (!empty($validated['can_ho']) && !empty($validated['vai_tro'])) {
                CuDanCanHo::create([
                    'cu_dan'          => $cuDan->id,
                    'can_ho'          => $validated['can_ho'],
                    'vai_tro'         => $validated['vai_tro'],
                    'ngay_chuyen_den' => $validated['ngay_chuyen_den'] ?? now(),
                    'trang_thai'      => 1,
                    'nguoi_cap_nhat'  => auth('nhanvien')->id(),
                ]);
            }

            AuditLogService::log('INSERT', 'cu_dan', $cuDan->id, null, $cuDan->toArray());
        });

        return redirect()->route('manager.cu-dan.index')->with('success', 'Thêm cư dân thành công.');
    }

    public function show(CuDan $cuDan)
    {
        $cuDan->load([
            'cuDanCanHo.canHo.toaNha',
            'cuDanCanHo.canHo.loaiCanHo',
            'cuDanCanHo.vaiTro',
            'cuDanCanHo.canHo.phuongTien.loaiPhuongTien',
            'cuDanCanHo.canHo.hoaDon',
            'yeuCau.loaiYeuCau',
            'yeuCau.nhanVienXuLy',
            'thongBaoDaDoc.thongBao',
            'lichSuThanhToan.hoaDon.canHo.toaNha',
            'lichSuThanhToan.nguonTao',
        ]);

        return view('manager.cu-dan.show', compact('cuDan'));
    }

    public function edit(CuDan $cuDan)
    {
        $cuDan->load(['canHoHienTai.canHo.toaNha', 'canHoHienTai.vaiTro']);
        $canHoList = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $vaiTro    = VaiTro::orderBy('id')->get();
        $toaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        return view('manager.cu-dan.edit', compact('cuDan', 'canHoList', 'vaiTro', 'toaNha'));
    }

    public function update(UpdateCuDanRequest $request, CuDan $cuDan)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $cuDan) {
            $old = $cuDan->toArray();
            $gt  = $validated['gioi_tinh'] ?? null;

            $cuDan->update([
                'ho_ten_dem'     => $validated['ho_ten_dem'] ?? '',
                'ten'            => $validated['ten'],
                'sdt'            => $validated['sdt'] ?? null,
                'cccd'           => $validated['cccd'] ?? null,
                'email'          => $validated['email'] ?? null,
                'ngay_sinh'      => $validated['ngay_sinh'] ?? null,
                'gioi_tinh'      => ($gt !== null && $gt !== '') ? (int) $gt : null,
                'tinh'           => $validated['tinh'] ?? null,
                'xa'             => $validated['xa'] ?? null,
                'dia_chi'        => $validated['dia_chi'] ?? null,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'cu_dan', $cuDan->id, $old, $cuDan->fresh()->toArray());

            $canHoMoi     = $validated['can_ho'] ?? null;
            $canHoHienTai = $cuDan->canHoHienTai;

            if ($canHoMoi) {
                if ($canHoHienTai) {
                    if ($canHoHienTai->can_ho != $canHoMoi) {
                        $canHoHienTai->update(['trang_thai' => 0, 'ngay_chuyen_di' => now()]);
                        CuDanCanHo::create([
                            'cu_dan'          => $cuDan->id,
                            'can_ho'          => $canHoMoi,
                            'vai_tro'         => $validated['vai_tro'] ?? $canHoHienTai->vai_tro,
                            'ngay_chuyen_den' => $validated['ngay_chuyen_den'] ?? now(),
                            'trang_thai'      => 1,
                            'nguoi_cap_nhat'  => auth('nhanvien')->id(),
                        ]);
                    } else {
                        $canHoHienTai->update([
                            'vai_tro'        => $validated['vai_tro'] ?? $canHoHienTai->vai_tro,
                            'nguoi_cap_nhat' => auth('nhanvien')->id(),
                        ]);
                    }
                } else {
                    CuDanCanHo::create([
                        'cu_dan'          => $cuDan->id,
                        'can_ho'          => $canHoMoi,
                        'vai_tro'         => $validated['vai_tro'],
                        'ngay_chuyen_den' => $validated['ngay_chuyen_den'] ?? now(),
                        'trang_thai'      => 1,
                        'nguoi_cap_nhat'  => auth('nhanvien')->id(),
                    ]);
                }
            } else {
                if ($canHoHienTai) {
                    $canHoHienTai->update(['trang_thai' => 0, 'ngay_chuyen_di' => now()]);
                }
            }
        });

        return redirect()->route('manager.cu-dan.index')->with('success', 'Cập nhật cư dân thành công.');
    }

    public function destroy(CuDan $cuDan)
    {
        $coYeuCau    = $cuDan->yeuCau()->exists();
        $coThanhToan = $cuDan->lichSuThanhToan()->exists();

        $canHoIds     = $cuDan->cuDanCanHo()->pluck('can_ho');
        $coPhuongTien = $canHoIds->isNotEmpty() && PhuongTien::whereIn('can_ho', $canHoIds)->exists();
        $coHoaDon     = $canHoIds->isNotEmpty() && HoaDon::whereIn('can_ho', $canHoIds)->exists();

        if ($coYeuCau || $coThanhToan || $coPhuongTien || $coHoaDon) {
            return back()->with('error', 'Cư dân đã phát sinh dữ liệu nên không thể xóa.');
        }

        AuditLogService::log('DELETE', 'cu_dan', $cuDan->id, $cuDan->toArray(), null);
        $cuDan->delete();

        return redirect()->route('manager.cu-dan.index')->with('success', 'Đã xóa cư dân thành công.');
    }
}
