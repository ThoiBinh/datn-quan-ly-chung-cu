<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\LoaiCanHo;
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

        $query = CanHo::with(['toaNha', 'loaiCanHo', 'trangThai', 'chuHo.cuDan', 'thuocTinh']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('so_can_ho', 'like', "%$s%");
        }
        if ($request->filled('toa_nha')) {
            $query->where('toa_nha', $request->toa_nha);
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('tang')) {
            $query->where('tang', (int) $request->tang);
        }

        $canHo       = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $dsToaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        $dsTrangThai = TrangThaiCanHo::all();

        return view('admin.can-ho.index', compact('canHo', 'dsToaNha', 'dsTrangThai', 'sort', 'direction'));
    }

    public function create()
    {
        $dsToaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        $dsLoaiCanHo = LoaiCanHo::all();
        $dsTrangThai = TrangThaiCanHo::all();
        $dsThuocTinh = ThuocTinh::orderBy('ten_thuoc_tinh')->get();

        return view('admin.can-ho.create', compact('dsToaNha', 'dsLoaiCanHo', 'dsTrangThai', 'dsThuocTinh'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'toa_nha'              => 'required|exists:toa_nha,id',
            'so_can_ho'            => 'required|string|max:50',
            'tang'                 => 'required|integer|min:1',
            'loai_can_ho'          => 'required|exists:loai_can_ho,id',
            'trang_thai'           => 'required|exists:trang_thai_can_ho,id',
            'gia'                  => 'nullable|numeric|min:0',
            'thuoc_tinh'           => 'nullable|array',
            'thuoc_tinh.*.gia_tri' => 'nullable|string|max:150',
        ], [
            'toa_nha.required'     => 'Vui lòng chọn tòa nhà.',
            'toa_nha.exists'       => 'Tòa nhà không hợp lệ.',
            'so_can_ho.required'   => 'Vui lòng nhập số căn hộ.',
            'tang.required'        => 'Vui lòng nhập tầng.',
            'tang.min'             => 'Tầng phải lớn hơn 0.',
            'loai_can_ho.required' => 'Vui lòng chọn loại căn hộ.',
            'trang_thai.required'  => 'Vui lòng chọn trạng thái.',
        ]);

        $canHo = DB::transaction(function () use ($request) {
            $canHo = CanHo::create([
                'toa_nha'        => $request->toa_nha,
                'so_can_ho'      => $request->so_can_ho,
                'tang'           => $request->tang,
                'loai_can_ho'    => $request->loai_can_ho,
                'trang_thai'     => $request->trang_thai,
                'gia'            => $request->gia ?: null,
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
            'cuDanHienTai.cuDan',
            'cuDanHienTai.vaiTro',
            'hoaDon',
            'phuongTien',
            'thuocTinh',
        ]);

        return view('admin.can-ho.show', compact('canHo'));
    }

    public function edit(CanHo $canHo)
    {
        $canHo->load('thuocTinh');
        $dsToaNha         = ToaNha::orderBy('ten_toa_nha')->get();
        $dsLoaiCanHo      = LoaiCanHo::all();
        $dsTrangThai      = TrangThaiCanHo::all();
        $dsThuocTinh      = ThuocTinh::orderBy('ten_thuoc_tinh')->get();
        $currentThuocTinh = $canHo->thuocTinh->keyBy('id');

        return view('admin.can-ho.edit', compact(
            'canHo', 'dsToaNha', 'dsLoaiCanHo', 'dsTrangThai', 'dsThuocTinh', 'currentThuocTinh'
        ));
    }

    public function update(Request $request, CanHo $canHo)
    {
        $request->validate([
            'toa_nha'              => 'required|exists:toa_nha,id',
            'so_can_ho'            => 'required|string|max:50',
            'tang'                 => 'required|integer|min:1',
            'loai_can_ho'          => 'required|exists:loai_can_ho,id',
            'trang_thai'           => 'required|exists:trang_thai_can_ho,id',
            'gia'                  => 'nullable|numeric|min:0',
            'thuoc_tinh'           => 'nullable|array',
            'thuoc_tinh.*.gia_tri' => 'nullable|string|max:150',
        ], [
            'toa_nha.required'     => 'Vui lòng chọn tòa nhà.',
            'so_can_ho.required'   => 'Vui lòng nhập số căn hộ.',
            'tang.required'        => 'Vui lòng nhập tầng.',
            'loai_can_ho.required' => 'Vui lòng chọn loại căn hộ.',
            'trang_thai.required'  => 'Vui lòng chọn trạng thái.',
        ]);

        DB::transaction(function () use ($request, $canHo) {
            $old = $canHo->toArray();

            $canHo->update([
                'toa_nha'        => $request->toa_nha,
                'so_can_ho'      => $request->so_can_ho,
                'tang'           => $request->tang,
                'loai_can_ho'    => $request->loai_can_ho,
                'trang_thai'     => $request->trang_thai,
                'gia'            => $request->gia ?: null,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);

            $this->syncThuocTinh($canHo, $request->input('thuoc_tinh', []));

            AuditLogService::log('UPDATE', 'can_ho', $canHo->id, $old, $canHo->fresh()->toArray());
        });

        return redirect()->route('admin.can-ho.show', $canHo)
            ->with('success', "Cập nhật căn hộ «{$canHo->so_can_ho}» thành công.");
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
