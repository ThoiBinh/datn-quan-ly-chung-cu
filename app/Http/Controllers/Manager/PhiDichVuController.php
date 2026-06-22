<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DonViTinhPhiDichVu;
use App\Models\LoaiPhiDichVu;
use App\Models\LoaiTinhPhiDichVu;
use App\Models\PhiDichVu;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class PhiDichVuController extends Controller
{
    public function index(Request $request)
    {
        $query = PhiDichVu::with(['loaiPhiDichVu', 'donViTinh', 'loaiTinhPhi']);

        if ($request->filled('search')) {
            $query->where('ten_phi_dich_vu', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('loai')) {
            $query->where('loai_phi_dich_vu', $request->loai);
        }

        $phiDichVu = $query->orderByDesc('createdAt')->paginate(15)->withQueryString();
        $loaiPhiDichVu = LoaiPhiDichVu::all();
        return view('manager.phi-dich-vu.index', compact('phiDichVu', 'loaiPhiDichVu'));
    }

    public function create()
    {
        $loaiPhiDichVu = LoaiPhiDichVu::all();
        $donViTinh = DonViTinhPhiDichVu::all();
        $loaiTinhPhi = LoaiTinhPhiDichVu::all();
        return view('manager.phi-dich-vu.create', compact('loaiPhiDichVu', 'donViTinh', 'loaiTinhPhi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_phi_dich_vu' => 'required|string|max:255',
            'don_gia'         => 'required|numeric|min:0',
            'loai_phi_dich_vu' => 'required|exists:loai_phi_dich_vu,id',
            'don_vi_tinh'     => 'required|exists:don_vi_tinh_phi_dich_vu,id',
            'loai_tinh_phi'   => 'required|exists:loai_tinh_phi_dich_vu,id',
        ], [
            'ten_phi_dich_vu.required' => 'Vui lòng nhập tên phí dịch vụ.',
            'don_gia.required'         => 'Vui lòng nhập đơn giá.',
        ]);

        $phi = PhiDichVu::create($request->only('loai_phi_dich_vu', 'ten_phi_dich_vu', 'don_gia', 'don_vi_tinh', 'loai_tinh_phi'));
        AuditLogService::log('INSERT', 'phi_dich_vu', $phi->id, null, $phi->toArray());

        return redirect()->route('manager.phi-dich-vu.index')->with('success', 'Thêm phí dịch vụ thành công.');
    }

    public function edit(PhiDichVu $phiDichVu)
    {
        $loaiPhiDichVu = LoaiPhiDichVu::all();
        $donViTinh = DonViTinhPhiDichVu::all();
        $loaiTinhPhi = LoaiTinhPhiDichVu::all();
        return view('manager.phi-dich-vu.edit', compact('phiDichVu', 'loaiPhiDichVu', 'donViTinh', 'loaiTinhPhi'));
    }

    public function update(Request $request, PhiDichVu $phiDichVu)
    {
        $request->validate([
            'ten_phi_dich_vu' => 'required|string|max:255',
            'don_gia'         => 'required|numeric|min:0',
        ]);

        $old = $phiDichVu->toArray();
        $phiDichVu->update($request->only('loai_phi_dich_vu', 'ten_phi_dich_vu', 'don_gia', 'don_vi_tinh', 'loai_tinh_phi'));
        AuditLogService::log('UPDATE', 'phi_dich_vu', $phiDichVu->id, $old, $phiDichVu->fresh()->toArray());

        return redirect()->route('manager.phi-dich-vu.index')->with('success', 'Cập nhật phí dịch vụ thành công.');
    }

    public function destroy(PhiDichVu $phiDichVu)
    {
        AuditLogService::log('DELETE', 'phi_dich_vu', $phiDichVu->id, $phiDichVu->toArray(), null);
        $phiDichVu->delete();
        return redirect()->route('manager.phi-dich-vu.index')->with('success', 'Xóa phí dịch vụ thành công.');
    }
}
