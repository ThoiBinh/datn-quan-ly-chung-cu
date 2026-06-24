<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\LoaiCanHo;
use App\Models\ToaNha;
use App\Models\TrangThaiCanHo;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CanHoController extends Controller
{
    private const SORTABLE = ['so_can_ho', 'tang', 'gia', 'createdAt'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = CanHo::with(['toaNha', 'loaiCanHo', 'trangThai', 'chuHo.cuDan']);

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

        $canHo    = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $dsToaNha = ToaNha::orderBy('ten_toa_nha')->get();
        $dsTrangThai = TrangThaiCanHo::all();

        return view('admin.can-ho.index', compact('canHo', 'dsToaNha', 'dsTrangThai', 'sort', 'direction'));
    }

    public function create()
    {
        $dsToaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        $dsLoaiCanHo = LoaiCanHo::all();
        $dsTrangThai = TrangThaiCanHo::all();

        return view('admin.can-ho.create', compact('dsToaNha', 'dsLoaiCanHo', 'dsTrangThai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'toa_nha'     => 'required|exists:toa_nha,id',
            'so_can_ho'   => 'required|string|max:50',
            'tang'        => 'required|integer|min:1',
            'loai_can_ho' => 'required|exists:loai_can_ho,id',
            'trang_thai'  => 'required|exists:trang_thai_can_ho,id',
            'gia'         => 'nullable|numeric|min:0',
        ], [
            'toa_nha.required'     => 'Vui lòng chọn tòa nhà.',
            'toa_nha.exists'       => 'Tòa nhà không hợp lệ.',
            'so_can_ho.required'   => 'Vui lòng nhập số căn hộ.',
            'tang.required'        => 'Vui lòng nhập tầng.',
            'tang.min'             => 'Tầng phải lớn hơn 0.',
            'loai_can_ho.required' => 'Vui lòng chọn loại căn hộ.',
            'trang_thai.required'  => 'Vui lòng chọn trạng thái.',
        ]);

        $canHo = CanHo::create([
            'toa_nha'        => $request->toa_nha,
            'so_can_ho'      => $request->so_can_ho,
            'tang'           => $request->tang,
            'loai_can_ho'    => $request->loai_can_ho,
            'trang_thai'     => $request->trang_thai,
            'gia'            => $request->gia ?: null,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('INSERT', 'can_ho', $canHo->id, null, $canHo->toArray());

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
        $dsToaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        $dsLoaiCanHo = LoaiCanHo::all();
        $dsTrangThai = TrangThaiCanHo::all();

        return view('admin.can-ho.edit', compact('canHo', 'dsToaNha', 'dsLoaiCanHo', 'dsTrangThai'));
    }

    public function update(Request $request, CanHo $canHo)
    {
        $request->validate([
            'toa_nha'     => 'required|exists:toa_nha,id',
            'so_can_ho'   => 'required|string|max:50',
            'tang'        => 'required|integer|min:1',
            'loai_can_ho' => 'required|exists:loai_can_ho,id',
            'trang_thai'  => 'required|exists:trang_thai_can_ho,id',
            'gia'         => 'nullable|numeric|min:0',
        ], [
            'toa_nha.required'     => 'Vui lòng chọn tòa nhà.',
            'so_can_ho.required'   => 'Vui lòng nhập số căn hộ.',
            'tang.required'        => 'Vui lòng nhập tầng.',
            'loai_can_ho.required' => 'Vui lòng chọn loại căn hộ.',
            'trang_thai.required'  => 'Vui lòng chọn trạng thái.',
        ]);

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

        AuditLogService::log('UPDATE', 'can_ho', $canHo->id, $old, $canHo->fresh()->toArray());

        return redirect()->route('admin.can-ho.show', $canHo)
            ->with('success', "Cập nhật căn hộ «{$canHo->so_can_ho}» thành công.");
    }
}
