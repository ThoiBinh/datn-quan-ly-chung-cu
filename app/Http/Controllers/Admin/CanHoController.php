<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCanHoRequest;
use App\Http\Requests\Admin\UpdateCanHoRequest;
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
    private const SORTABLE = ['so_can_ho', 'tang', 'gia', 'createdAt'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = CanHo::with(['toaNha', 'loaiCanHo', 'trangThai', 'chuHo.cuDan', 'thuocTinh'])
            ->withCount(['cuDanHienTai', 'phuongTien', 'hoaDon', 'canHoPhiDichVu']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('so_can_ho', 'like', "%{$s}%")
                    ->orWhereHas('toaNha', fn ($qn) => $qn->where('ten_toa_nha', 'like', "%{$s}%"))
                    ->orWhereHas('cuDanCanHo.cuDan', fn ($qc) => $qc->where('ho_ten_dem', 'like', "%{$s}%")
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
            $query->where('tang', (int) $request->tang);
        }

        $canHo       = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $dsToaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        $dsLoaiCanHo = LoaiCanHo::orderBy('ten_loai_can_ho')->get();
        $dsTrangThai = TrangThaiCanHo::orderBy('id')->get();

        $stats = [
            'tong'                 => CanHo::count(),
            'tong_cu_dan'          => CuDanCanHo::where('trang_thai', 1)->count(),
            'tong_phuong_tien'     => PhuongTien::count(),
            'tong_dich_vu'         => DB::table('can_ho_phi_dich_vu')->count(),
            'tong_hoa_don_chua_tt' => HoaDon::where('trang_thai', HoaDon::TRANG_THAI_CHUA_THANH_TOAN)->count(),
        ];
        $trangThaiStats = TrangThaiCanHo::withCount('canHo')->orderBy('id')->get();

        return view('admin.can-ho.index', compact(
            'canHo', 'dsToaNha', 'dsLoaiCanHo', 'dsTrangThai', 'sort', 'direction', 'stats', 'trangThaiStats'
        ));
    }

    public function create()
    {
        $dsToaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        $dsLoaiCanHo = LoaiCanHo::orderBy('ten_loai_can_ho')->get();
        $dsTrangThai = TrangThaiCanHo::orderBy('id')->get();
        $dsThuocTinh = ThuocTinh::orderBy('ten_thuoc_tinh')->get();

        return view('admin.can-ho.create', compact('dsToaNha', 'dsLoaiCanHo', 'dsTrangThai', 'dsThuocTinh'));
    }

    public function store(StoreCanHoRequest $request)
    {
        $validated = $request->validated();

        $canHo = DB::transaction(function () use ($request, $validated) {
            $canHo = CanHo::create([
                'toa_nha'        => $validated['toa_nha'],
                'so_can_ho'      => $validated['so_can_ho'],
                'tang'           => $validated['tang'],
                'loai_can_ho'    => $validated['loai_can_ho'],
                'trang_thai'     => $validated['trang_thai'],
                'gia'            => $validated['gia'] ?? null,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            $this->syncThuocTinh($canHo, $request->input('thuoc_tinh', []));

            AuditLogService::log('INSERT', 'can_ho', $canHo->id, null, $canHo->toArray());

            return $canHo;
        });

        return redirect()->route('admin.can-ho.index')
            ->with('success', "Thêm căn hộ «{$canHo->so_can_ho}» thành công.");
    }

    public function show(CanHo $canHo)
    {
        $canHo->load([
            'toaNha',
            'loaiCanHo',
            'trangThai',
            'cuDanCanHo.cuDan',
            'cuDanCanHo.vaiTro',
            'phuongTien.loaiPhuongTien',
            'canHoPhiDichVu.phiDichVu.loaiPhiDichVu',
            'canHoPhiDichVu.phiDichVu.donViTinh',
            'canHoPhiDichVu.phiDichVu.loaiTinhPhi',
            'hoaDon.lichSuThanhToan.nguoiThanhToan',
            'thuocTinh',
        ]);

        return view('admin.can-ho.show', compact('canHo'));
    }

    public function edit(CanHo $canHo)
    {
        $canHo->load('thuocTinh');
        $dsToaNha         = ToaNha::orderBy('ten_toa_nha')->get();
        $dsLoaiCanHo      = LoaiCanHo::orderBy('ten_loai_can_ho')->get();
        $dsTrangThai      = TrangThaiCanHo::orderBy('id')->get();
        $dsThuocTinh      = ThuocTinh::orderBy('ten_thuoc_tinh')->get();
        $currentThuocTinh = $canHo->thuocTinh->keyBy('id');

        return view('admin.can-ho.edit', compact(
            'canHo', 'dsToaNha', 'dsLoaiCanHo', 'dsTrangThai', 'dsThuocTinh', 'currentThuocTinh'
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
                'loai_can_ho'    => $validated['loai_can_ho'],
                'trang_thai'     => $validated['trang_thai'],
                'gia'            => $validated['gia'] ?? null,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            $this->syncThuocTinh($canHo, $request->input('thuoc_tinh', []));

            AuditLogService::log('UPDATE', 'can_ho', $canHo->id, $old, $canHo->fresh()->toArray());
        });

        return redirect()->route('admin.can-ho.show', $canHo)
            ->with('success', "Cập nhật căn hộ «{$canHo->so_can_ho}» thành công.");
    }

    public function destroy(CanHo $canHo)
    {
        $daPhatSinhDuLieu = $canHo->cuDanCanHo()->exists()
            || $canHo->phuongTien()->exists()
            || $canHo->canHoPhiDichVu()->exists()
            || $canHo->hoaDon()->exists();

        if ($daPhatSinhDuLieu) {
            return back()->with('error', 'Căn hộ đã phát sinh dữ liệu nên không thể xóa.');
        }

        // CanHo không dùng SoftDeletes (không có cột deletedAt) nên không xóa vật lý —
        // chỉ chuyển trạng thái căn hộ về "Trống" để tránh mất liên kết dữ liệu về sau.
        $old        = $canHo->toArray();
        $trangThaiTrong = TrangThaiCanHo::where('ten_trang_thai', 'Trống')->first();

        if (!$trangThaiTrong) {
            return back()->with('error', 'Không tìm thấy trạng thái "Trống" để cập nhật căn hộ.');
        }

        $canHo->update([
            'trang_thai'     => $trangThaiTrong->id,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('UPDATE', 'can_ho', $canHo->id, $old, $canHo->fresh()->toArray());

        return redirect()->route('admin.can-ho.index')
            ->with('success', "Căn hộ «{$canHo->so_can_ho}» đã được chuyển sang trạng thái Trống.");
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
