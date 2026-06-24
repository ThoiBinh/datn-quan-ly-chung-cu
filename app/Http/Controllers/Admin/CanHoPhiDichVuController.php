<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\CanHoPhiDichVu;
use App\Models\PhiDichVu;
use App\Models\ToaNha;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CanHoPhiDichVuController extends Controller
{
    private const SORTABLE = ['createdAt', 'can_ho', 'phi_dich_vu', 'don_gia'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = CanHoPhiDichVu::with([
            'canHo.toaNha',
            'canHo.loaiCanHo',
            'canHo.trangThai',
            'phiDichVu.loaiPhiDichVu',
            'phiDichVu.donViTinh',
            'phiDichVu.loaiTinhPhi',
            'nguoiCapNhat',
        ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('canHo', fn($q2) => $q2
                    ->where('so_can_ho', 'like', "%$s%")
                )
                ->orWhereHas('canHo.toaNha', fn($q2) => $q2
                    ->where('ten_toa_nha', 'like', "%$s%")
                )
                ->orWhereHas('phiDichVu', fn($q2) => $q2
                    ->where('ten_phi_dich_vu', 'like', "%$s%")
                );
            });
        }

        if ($request->filled('toa_nha')) {
            $query->whereHas('canHo', fn($q) => $q->where('toa_nha', $request->toa_nha));
        }

        if ($request->filled('can_ho')) {
            $query->where('can_ho', $request->can_ho);
        }

        if ($request->filled('phi_dich_vu')) {
            $query->where('phi_dich_vu', $request->phi_dich_vu);
        }

        $dsRecord  = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $dsToaNha  = ToaNha::orderBy('ten_toa_nha')->get();
        $dsCanHo   = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsPhiDV   = PhiDichVu::with('loaiPhiDichVu')->orderBy('ten_phi_dich_vu')->get();

        return view('admin.can-ho-phi-dich-vu.index', compact(
            'dsRecord', 'dsToaNha', 'dsCanHo', 'dsPhiDV', 'sort', 'direction'
        ));
    }

    public function create()
    {
        $dsCanHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsPhiDV = PhiDichVu::with(['loaiPhiDichVu', 'donViTinh', 'loaiTinhPhi'])
            ->orderBy('ten_phi_dich_vu')->get();

        return view('admin.can-ho-phi-dich-vu.create', compact('dsCanHo', 'dsPhiDV'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'can_ho'      => 'required|exists:can_ho,id',
            'phi_dich_vu' => 'required|exists:phi_dich_vu,id',
            'don_gia'     => 'nullable|numeric|min:0',
        ], [
            'can_ho.required'      => 'Vui lòng chọn căn hộ.',
            'can_ho.exists'        => 'Căn hộ không tồn tại.',
            'phi_dich_vu.required' => 'Vui lòng chọn dịch vụ.',
            'phi_dich_vu.exists'   => 'Dịch vụ không tồn tại.',
            'don_gia.numeric'      => 'Đơn giá phải là số.',
            'don_gia.min'          => 'Đơn giá không được âm.',
        ]);

        $exists = CanHoPhiDichVu::where('can_ho', $request->can_ho)
            ->where('phi_dich_vu', $request->phi_dich_vu)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->with('error', 'Căn hộ này đã được gán dịch vụ đó rồi.');
        }

        $donGia = $request->filled('don_gia')
            ? $request->don_gia
            : PhiDichVu::findOrFail($request->phi_dich_vu)->don_gia;

        $record = CanHoPhiDichVu::create([
            'can_ho'          => $request->can_ho,
            'phi_dich_vu'     => $request->phi_dich_vu,
            'don_gia'         => $donGia,
            'nguoi_cap_nhat'  => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('INSERT', 'can_ho_phi_dich_vu', $record->id, null, $record->toArray());

        return redirect()->route('admin.can-ho-phi-dich-vu.show', $record)
            ->with('success', 'Thêm dịch vụ căn hộ thành công.');
    }

    public function show(CanHoPhiDichVu $canHoPhiDichVu)
    {
        $canHoPhiDichVu->load([
            'canHo.toaNha',
            'canHo.loaiCanHo',
            'canHo.trangThai',
            'phiDichVu.loaiPhiDichVu',
            'phiDichVu.donViTinh',
            'phiDichVu.loaiTinhPhi',
            'nguoiCapNhat',
        ]);

        // Thống kê: tổng phí & số dịch vụ theo căn hộ này
        $canHoId     = $canHoPhiDichVu->can_ho;
        $tongPhiDV   = CanHoPhiDichVu::where('can_ho', $canHoId)->sum('don_gia');
        $soDichVu    = CanHoPhiDichVu::where('can_ho', $canHoId)->count();

        return view('admin.can-ho-phi-dich-vu.show', compact(
            'canHoPhiDichVu', 'tongPhiDV', 'soDichVu'
        ));
    }

    public function edit(CanHoPhiDichVu $canHoPhiDichVu)
    {
        $canHoPhiDichVu->load(['canHo.toaNha', 'phiDichVu.loaiPhiDichVu']);
        $dsCanHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsPhiDV = PhiDichVu::with(['loaiPhiDichVu', 'donViTinh', 'loaiTinhPhi'])
            ->orderBy('ten_phi_dich_vu')->get();

        return view('admin.can-ho-phi-dich-vu.edit', compact(
            'canHoPhiDichVu', 'dsCanHo', 'dsPhiDV'
        ));
    }

    public function update(Request $request, CanHoPhiDichVu $canHoPhiDichVu)
    {
        $request->validate([
            'can_ho'      => 'required|exists:can_ho,id',
            'phi_dich_vu' => 'required|exists:phi_dich_vu,id',
            'don_gia'     => 'nullable|numeric|min:0',
        ], [
            'can_ho.required'      => 'Vui lòng chọn căn hộ.',
            'phi_dich_vu.required' => 'Vui lòng chọn dịch vụ.',
            'don_gia.numeric'      => 'Đơn giá phải là số.',
            'don_gia.min'          => 'Đơn giá không được âm.',
        ]);

        $exists = CanHoPhiDichVu::where('can_ho', $request->can_ho)
            ->where('phi_dich_vu', $request->phi_dich_vu)
            ->where('id', '!=', $canHoPhiDichVu->id)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->with('error', 'Căn hộ này đã được gán dịch vụ đó rồi.');
        }

        $donGia = $request->filled('don_gia')
            ? $request->don_gia
            : PhiDichVu::findOrFail($request->phi_dich_vu)->don_gia;

        $old = $canHoPhiDichVu->toArray();
        $canHoPhiDichVu->update([
            'can_ho'         => $request->can_ho,
            'phi_dich_vu'    => $request->phi_dich_vu,
            'don_gia'        => $donGia,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('UPDATE', 'can_ho_phi_dich_vu', $canHoPhiDichVu->id, $old, $canHoPhiDichVu->fresh()->toArray());

        return redirect()->route('admin.can-ho-phi-dich-vu.show', $canHoPhiDichVu)
            ->with('success', 'Cập nhật dịch vụ căn hộ thành công.');
    }
}
