<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreCanHoRequest;
use App\Http\Requests\Manager\UpdateCanHoRequest;
use App\Models\CanHo;
use App\Models\CuDanCanHo;
use App\Models\HoaDon;
use App\Models\LoaiCanHo;
use App\Models\PhuongTien;
use App\Models\ThuocTinh;
use App\Models\ToaNha;
use App\Models\TrangThaiCanHo;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CanHoController extends Controller
{
    public function index(Request $request)
    {
        $query = CanHo::with(['toaNha', 'loaiCanHo', 'trangThai', 'thuocTinh'])
            ->withCount(['cuDanHienTai', 'phuongTien', 'hoaDon']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('so_can_ho', 'like', "%{$s}%")
                  ->orWhereHas('toaNha', fn($qn) => $qn->where('ten_toa_nha', 'like', "%{$s}%"))
                  ->orWhereHas('cuDanHienTai.cuDan', fn($qc) => $qc->where('ho_ten_dem', 'like', "%{$s}%")
                      ->orWhere('ten', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('toa_nha')) {
            $query->where('toa_nha', $request->toa_nha);
        }
        if ($request->filled('loai_can_ho')) {
            $query->where('loai_can_ho', $request->loai_can_ho);
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('tang')) {
            $query->where('tang', $request->tang);
        }

        $allowedSorts = ['so_can_ho', 'tang', 'gia', 'createdAt'];
        $sortBy  = in_array($request->sort_by, $allowedSorts) ? $request->sort_by : 'createdAt';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $canHo = $query->paginate(15)->withQueryString();

        $stats = [
            'tong'                 => CanHo::count(),
            'tong_cu_dan'          => CuDanCanHo::where('trang_thai', 1)->count(),
            'tong_phuong_tien'     => PhuongTien::count(),
            'tong_hoa_don_chua_tt' => HoaDon::where('trang_thai', HoaDon::TRANG_THAI_CHUA_THANH_TOAN)->count(),
        ];
        $trangThaiStats = TrangThaiCanHo::withCount('canHo')->get();

        $toaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        $loaiCanHo = LoaiCanHo::orderBy('ten_loai_can_ho')->get();
        $trangThai = TrangThaiCanHo::orderBy('id')->get();

        return view('manager.can-ho.index', compact(
            'canHo', 'stats', 'trangThaiStats', 'toaNha', 'loaiCanHo', 'trangThai'
        ));
    }

    public function create()
    {
        $toaNha      = ToaNha::orderBy('ten_toa_nha')->get();
        $loaiCanHo   = LoaiCanHo::orderBy('ten_loai_can_ho')->get();
        $trangThai   = TrangThaiCanHo::orderBy('id')->get();
        $dsThuocTinh = ThuocTinh::orderBy('ten_thuoc_tinh')->get();

        return view('manager.can-ho.create', compact('toaNha', 'loaiCanHo', 'trangThai', 'dsThuocTinh'));
    }

    public function store(StoreCanHoRequest $request)
    {
        $validated = $request->validated();

        $canHo = DB::transaction(function () use ($request, $validated) {
            $canHo = CanHo::create([
                'toa_nha'        => $validated['toa_nha'],
                'so_can_ho'      => $validated['so_can_ho'],
                'tang'           => $validated['tang'],
                'trang_thai'     => $validated['trang_thai'],
                'gia'            => $validated['gia'] ?? null,
                'loai_can_ho'    => $validated['loai_can_ho'],
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            $this->syncThuocTinh($canHo, $request->input('thuoc_tinh', []));

            AuditLogService::log('INSERT', 'can_ho', $canHo->id, null, $canHo->toArray());

            return $canHo;
        });

        return redirect()->route('manager.can-ho.index')->with('success', "Thêm căn hộ «{$canHo->so_can_ho}» thành công.");
    }

    public function show(CanHo $canHo)
    {
        $canHo->load([
            'toaNha',
            'loaiCanHo',
            'trangThai',
            'cuDanHienTai.cuDan',
            'cuDanHienTai.vaiTro',
            'cuDanCanHo.cuDan',
            'cuDanCanHo.vaiTro',
            'phiDichVu.loaiPhiDichVu',
            'phiDichVu.donViTinh',
            'phiDichVu.loaiTinhPhi',
            'thuocTinh',
            'phuongTien.loaiPhuongTien',
            'hoaDon.lichSuThanhToan',
            'hoaDon.chiTiet',
        ]);

        return view('manager.can-ho.show', compact('canHo'));
    }

    public function edit(CanHo $canHo)
    {
        $canHo->load(['cuDanHienTai.cuDan', 'cuDanHienTai.vaiTro', 'thuocTinh']);
        $toaNha           = ToaNha::orderBy('ten_toa_nha')->get();
        $loaiCanHo        = LoaiCanHo::orderBy('ten_loai_can_ho')->get();
        $trangThai        = TrangThaiCanHo::orderBy('id')->get();
        $dsThuocTinh      = ThuocTinh::orderBy('ten_thuoc_tinh')->get();
        $currentThuocTinh = $canHo->thuocTinh->keyBy('id');

        return view('manager.can-ho.edit', compact(
            'canHo', 'toaNha', 'loaiCanHo', 'trangThai', 'dsThuocTinh', 'currentThuocTinh'
        ));
    }

    public function update(UpdateCanHoRequest $request, CanHo $canHo)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $canHo) {
            $old = $canHo->toArray();

            $canHo->update([
                'toa_nha'        => $validated['toa_nha'],
                'so_can_ho'      => $validated['so_can_ho'],
                'tang'           => $validated['tang'],
                'trang_thai'     => $validated['trang_thai'],
                'gia'            => $validated['gia'] ?? null,
                'loai_can_ho'    => $validated['loai_can_ho'],
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            $this->syncThuocTinh($canHo, $request->input('thuoc_tinh', []));

            AuditLogService::log('UPDATE', 'can_ho', $canHo->id, $old, $canHo->fresh()->toArray());
        });

        return redirect()->route('manager.can-ho.index')->with('success', "Cập nhật căn hộ «{$canHo->so_can_ho}» thành công.");
    }

    public function destroy(CanHo $canHo)
    {
        if ($canHo->cuDanCanHo()->exists()) {
            return back()->with('error', 'Không thể xóa căn hộ đã có lịch sử cư dân.');
        }
        if ($canHo->hoaDon()->exists()) {
            return back()->with('error', 'Không thể xóa căn hộ đã có hóa đơn.');
        }
        if ($canHo->phuongTien()->exists()) {
            return back()->with('error', 'Không thể xóa căn hộ đã có phương tiện đăng ký.');
        }
        if ($canHo->phiDichVu()->exists()) {
            return back()->with('error', 'Không thể xóa căn hộ đã đăng ký phí dịch vụ.');
        }

        AuditLogService::log('DELETE', 'can_ho', $canHo->id, $canHo->toArray(), null);
        $canHo->delete();

        return redirect()->route('manager.can-ho.index')->with('success', 'Xóa căn hộ thành công.');
    }

    private function syncThuocTinh(CanHo $canHo, array $rawInput): void
    {
        $pivotData = [];
        foreach ($rawInput as $ttId => $ttData) {
            if (!empty($ttData['active']) && isset($ttData['gia_tri']) && $ttData['gia_tri'] !== '') {
                $pivotData[(int) $ttId] = [
                    'gia_tri_thuoc_tinh' => $ttData['gia_tri'],
                    'kieu_du_lieu'       => is_numeric($ttData['gia_tri']) ? 1 : 2,
                ];
            }
        }
        $canHo->thuocTinh()->sync($pivotData);
    }
}
