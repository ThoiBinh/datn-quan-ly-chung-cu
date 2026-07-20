<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCuDanRequest;
use App\Http\Requests\Admin\UpdateCuDanRequest;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\CuDanCanHo;
use App\Models\HoaDon;
use App\Models\LoaiCanHo;
use App\Models\PhuongTien;
use App\Models\ToaNha;
use App\Models\VaiTro;
use App\Models\YeuCauCuDan;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CuDanController extends Controller
{
    private const SORTABLE = ['ho_ten_dem', 'createdAt', 'toa_nha', 'so_can_ho'];

    public function index(Request $request)
    {
        $query = CuDan::with([
                'cuDanCanHo' => fn($q) => $q->where('trang_thai', 1)
                    ->with(['canHo.toaNha', 'canHo.loaiCanHo', 'vaiTro']),
            ])
            ->withCount(['cuDanCanHo', 'yeuCau']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ho_ten_dem', 'like', "%$s%")
                  ->orWhere('ten',      'like', "%$s%")
                  ->orWhere('email',    'like', "%$s%")
                  ->orWhere('sdt',      'like', "%$s%")
                  ->orWhere('cccd',     'like', "%$s%")
                  ->orWhereHas('cuDanCanHo.canHo', fn($qc) => $qc->where('so_can_ho', 'like', "%$s%"))
                  ->orWhereHas('cuDanCanHo.canHo.toaNha', fn($qt) => $qt->where('ten_toa_nha', 'like', "%$s%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('trang_thai', (int) $request->status);
        }

        if ($request->filled('toa_nha') || $request->filled('loai_can_ho')) {
            $query->whereHas('cuDanCanHo', function ($q) use ($request) {
                $q->where('cu_dan_can_ho.trang_thai', 1)
                  ->whereHas('canHo', function ($qc) use ($request) {
                      if ($request->filled('toa_nha')) {
                          $qc->where('toa_nha', $request->toa_nha);
                      }
                      if ($request->filled('loai_can_ho')) {
                          $qc->where('loai_can_ho', $request->loai_can_ho);
                      }
                  });
            });
        }

        if ($request->filled('vai_tro')) {
            $query->whereHas('cuDanCanHo', function ($q) use ($request) {
                $q->where('cu_dan_can_ho.trang_thai', 1)
                  ->whereHas('vaiTro', fn($qv) => $request->vai_tro === 'chu_ho'
                      ? $qv->where('vai_tro', 'Chủ hộ')
                      : $qv->where('vai_tro', '!=', 'Chủ hộ'));
            });
        }

        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        if ($sort === 'toa_nha') {
            $query->orderBy(
                ToaNha::select('ten_toa_nha')
                    ->join('can_ho', 'can_ho.toa_nha', '=', 'toa_nha.id')
                    ->join('cu_dan_can_ho', 'cu_dan_can_ho.can_ho', '=', 'can_ho.id')
                    ->whereColumn('cu_dan_can_ho.cu_dan', 'cu_dan.id')
                    ->where('cu_dan_can_ho.trang_thai', 1)
                    ->limit(1),
                $direction
            );
        } elseif ($sort === 'so_can_ho') {
            $query->orderBy(
                CanHo::select('so_can_ho')
                    ->join('cu_dan_can_ho', 'cu_dan_can_ho.can_ho', '=', 'can_ho.id')
                    ->whereColumn('cu_dan_can_ho.cu_dan', 'cu_dan.id')
                    ->where('cu_dan_can_ho.trang_thai', 1)
                    ->limit(1),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        $cuDan = $query->paginate(15)->withQueryString();

        $stats = [
            'tong_can_ho'      => CuDanCanHo::where('trang_thai', 1)->distinct('can_ho')->count('can_ho'),
            'tong_phuong_tien' => PhuongTien::count(),
            'tong_hoa_don'     => HoaDon::count(),
            'tong_yeu_cau'     => YeuCauCuDan::count(),
        ];

        $toaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        $loaiCanHo = LoaiCanHo::orderBy('ten_loai_can_ho')->get();

        return view('admin.cu-dan.index', compact('cuDan', 'sort', 'direction', 'stats', 'toaNha', 'loaiCanHo'));
    }

    public function create()
    {
        $canHoList = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $vaiTro    = VaiTro::orderBy('id')->get();
        $toaNha    = ToaNha::orderBy('ten_toa_nha')->get();

        return view('admin.cu-dan.create', compact('canHoList', 'vaiTro', 'toaNha'));
    }

    public function store(StoreCuDanRequest $request)
    {
        $validated = $request->validated();

        $cuDan = DB::transaction(function () use ($validated) {
            $cuDan = CuDan::create([
                'ho_ten_dem'     => $validated['ho_ten_dem'],
                'ten'            => $validated['ten'],
                'sdt'            => $validated['sdt'] ?? null,
                'cccd'           => $validated['cccd'],
                'email'          => $validated['email'] ?? null,
                'mat_khau'       => Hash::make($validated['mat_khau']),
                'ngay_sinh'      => $validated['ngay_sinh'] ?? null,
                'gioi_tinh'      => isset($validated['gioi_tinh']) && $validated['gioi_tinh'] !== '' ? (int) $validated['gioi_tinh'] : null,
                'tinh'           => $validated['tinh'] ?? null,
                'xa'             => $validated['xa'] ?? null,
                'dia_chi'        => $validated['dia_chi'] ?? null,
                'trang_thai'     => (int) $validated['trang_thai'],
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            if (!empty($validated['can_ho'])) {
                CuDanCanHo::create([
                    'cu_dan'          => $cuDan->id,
                    'can_ho'          => $validated['can_ho'],
                    'vai_tro'         => $validated['vai_tro'] ?? null,
                    'ngay_chuyen_den' => $validated['ngay_chuyen_den'] ?? now(),
                    'trang_thai'      => 1,
                    'nguoi_cap_nhat'  => auth('nhanvien')->id(),
                ]);
            }

            AuditLogService::log('INSERT', 'cu_dan', $cuDan->id, null, $cuDan->toArray());

            return $cuDan;
        });

        return redirect()->route('admin.cu-dan.index')
            ->with('success', "Thêm cư dân «{$cuDan->ho_ten}» thành công.");
    }

    public function show(CuDan $cuDan)
    {
        $cuDan->load([
            'cuDanCanHo.canHo.toaNha',
            'cuDanCanHo.canHo.loaiCanHo',
            'cuDanCanHo.vaiTro',
            'cuDanCanHo.canHo.thuocTinhCanHo.thuocTinh',
            'phuongTien.loaiPhuongTien',
            'hoaDon.canHo.toaNha',
            'lichSuThanhToan.hoaDon.canHo.toaNha',
            'lichSuThanhToan.nguonTao',
            'yeuCau.loaiYeuCau',
            'yeuCau.nhanVienXuLy',
            'thongBaoDaDoc.thongBao',
        ])->loadCount(['cuDanCanHo', 'yeuCau']);

        return view('admin.cu-dan.show', compact('cuDan'));
    }

    public function edit(CuDan $cuDan)
    {
        $cuDan->load(['canHoHienTai.canHo.toaNha', 'canHoHienTai.vaiTro']);
        $canHoList = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $vaiTro    = VaiTro::orderBy('id')->get();
        $toaNha    = ToaNha::orderBy('ten_toa_nha')->get();

        return view('admin.cu-dan.edit', compact('cuDan', 'canHoList', 'vaiTro', 'toaNha'));
    }

    public function update(UpdateCuDanRequest $request, CuDan $cuDan)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $cuDan) {
            $old = $cuDan->toArray();

            $data = [
                'ho_ten_dem'     => $validated['ho_ten_dem'],
                'ten'            => $validated['ten'],
                'sdt'            => $validated['sdt'] ?? null,
                'cccd'           => $validated['cccd'],
                'email'          => $validated['email'] ?? null,
                'ngay_sinh'      => $validated['ngay_sinh'] ?? null,
                'gioi_tinh'      => isset($validated['gioi_tinh']) && $validated['gioi_tinh'] !== '' ? (int) $validated['gioi_tinh'] : null,
                'tinh'           => $validated['tinh'] ?? null,
                'xa'             => $validated['xa'] ?? null,
                'dia_chi'        => $validated['dia_chi'] ?? null,
                'trang_thai'     => (int) $validated['trang_thai'],
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ];

            if (!empty($validated['mat_khau'])) {
                $data['mat_khau'] = Hash::make($validated['mat_khau']);
            }

            $cuDan->update($data);
            AuditLogService::log('UPDATE', 'cu_dan', $cuDan->id, $old, $cuDan->fresh()->toArray());

            // Đồng bộ căn hộ: chỉ cập nhật bảng trung gian cu_dan_can_ho, không đụng tới can_ho.
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
                        'vai_tro'         => $validated['vai_tro'] ?? null,
                        'ngay_chuyen_den' => $validated['ngay_chuyen_den'] ?? now(),
                        'trang_thai'      => 1,
                        'nguoi_cap_nhat'  => auth('nhanvien')->id(),
                    ]);
                }
            } elseif ($canHoHienTai) {
                $canHoHienTai->update(['trang_thai' => 0, 'ngay_chuyen_di' => now()]);
            }
        });

        return redirect()->route('admin.cu-dan.show', $cuDan)
            ->with('success', 'Cập nhật cư dân thành công.');
    }

    public function destroy(CuDan $cuDan)
    {
        $canHoIds = $cuDan->cuDanCanHo()->pluck('can_ho');

        $coPhatSinh = $cuDan->yeuCau()->exists()
            || $cuDan->lichSuThanhToan()->exists()
            || ($canHoIds->isNotEmpty() && PhuongTien::whereIn('can_ho', $canHoIds)->exists())
            || ($canHoIds->isNotEmpty() && HoaDon::whereIn('can_ho', $canHoIds)->exists());

        if ($coPhatSinh) {
            return back()->with('error', 'Cư dân đã phát sinh dữ liệu nên không thể xóa.');
        }

        AuditLogService::log('DELETE', 'cu_dan', $cuDan->id, $cuDan->toArray(), null);
        $cuDan->delete(); // SoftDeletes: chỉ ghi deletedAt, không xóa vật lý.

        return redirect()->route('admin.cu-dan.index')->with('success', 'Đã xóa cư dân thành công.');
    }

    public function toggleStatus(CuDan $cuDan)
    {
        $old       = ['trang_thai' => $cuDan->trang_thai];
        $newStatus = $cuDan->trang_thai == 1 ? 3 : 1;
        $cuDan->update(['trang_thai' => $newStatus, 'nguoi_cap_nhat' => auth('nhanvien')->id()]);

        AuditLogService::log('UPDATE', 'cu_dan', $cuDan->id, $old, ['trang_thai' => $newStatus]);

        $label = $cuDan->trang_thai_label['text'];
        return back()->with('success', "Đã cập nhật trạng thái «{$cuDan->ho_ten}» thành «{$label}».");
    }
}
