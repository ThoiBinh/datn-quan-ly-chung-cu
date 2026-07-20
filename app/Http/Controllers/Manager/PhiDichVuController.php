<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StorePhiDichVuRequest;
use App\Http\Requests\Manager\UpdatePhiDichVuRequest;
use App\Models\DonViTinhPhiDichVu;
use App\Models\LoaiPhiDichVu;
use App\Models\LoaiTinhPhiDichVu;
use App\Models\PhiDichVu;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhiDichVuController extends Controller
{
    private const SORTABLE = ['id', 'ten_phi_dich_vu', 'don_gia', 'createdAt'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'id';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $tongTatCa      = PhiDichVu::count();
        $tongDangSuDung = PhiDichVu::whereHas('canHo')->count();
        $tongCanHo      = DB::table('can_ho_phi_dich_vu')->distinct('can_ho')->count('can_ho');

        $query = PhiDichVu::with(['loaiPhiDichVu', 'donViTinh', 'loaiTinhPhi', 'nguoiCapNhat'])
            ->withCount('canHo');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ten_phi_dich_vu', 'like', "%$s%")
                  ->orWhereHas('loaiPhiDichVu', fn($q2) => $q2->where('ten_loai_phi_dich_vu', 'like', "%$s%"))
                  ->orWhereHas('donViTinh', fn($q2) => $q2->where('don_vi', 'like', "%$s%"))
                  ->orWhereHas('loaiTinhPhi', fn($q2) => $q2->where('ten_loai', 'like', "%$s%"));
            });
        }

        if ($request->filled('loai_phi_dich_vu')) {
            $query->where('loai_phi_dich_vu', $request->loai_phi_dich_vu);
        }

        if ($request->filled('don_vi_tinh')) {
            $query->where('don_vi_tinh', $request->don_vi_tinh);
        }

        if ($request->filled('loai_tinh_phi')) {
            $query->where('loai_tinh_phi', $request->loai_tinh_phi);
        }

        if ($request->filled('tu_ngay')) {
            $query->where('createdAt', '>=', $request->tu_ngay . ' 00:00:00');
        }

        if ($request->filled('den_ngay')) {
            $query->where('createdAt', '<=', $request->den_ngay . ' 23:59:59');
        }

        $dsPhi         = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $dsLoaiPhi     = LoaiPhiDichVu::orderBy('ten_loai_phi_dich_vu')->get();
        $dsDonViTinh   = DonViTinhPhiDichVu::orderBy('don_vi')->get();
        $dsLoaiTinhPhi = LoaiTinhPhiDichVu::orderBy('ten_loai')->get();

        return view('manager.phi-dich-vu.index', compact(
            'dsPhi', 'dsLoaiPhi', 'dsDonViTinh', 'dsLoaiTinhPhi',
            'sort', 'direction',
            'tongTatCa', 'tongDangSuDung', 'tongCanHo'
        ));
    }

    public function create()
    {
        $dsLoaiPhi     = LoaiPhiDichVu::orderBy('ten_loai_phi_dich_vu')->get();
        $dsDonViTinh   = DonViTinhPhiDichVu::orderBy('don_vi')->get();
        $dsLoaiTinhPhi = LoaiTinhPhiDichVu::orderBy('ten_loai')->get();

        return view('manager.phi-dich-vu.create', compact('dsLoaiPhi', 'dsDonViTinh', 'dsLoaiTinhPhi'));
    }

    public function store(StorePhiDichVuRequest $request)
    {
        $phi = DB::transaction(function () use ($request) {
            return PhiDichVu::create([
                'ten_phi_dich_vu'  => $request->ten_phi_dich_vu,
                'don_gia'          => $request->don_gia,
                'loai_phi_dich_vu' => $request->loai_phi_dich_vu,
                'don_vi_tinh'      => $request->don_vi_tinh,
                'loai_tinh_phi'    => $request->loai_tinh_phi,
                'nguoi_cap_nhat'   => auth('nhanvien')->id(),
            ]);
        });

        AuditLogService::log('INSERT', 'phi_dich_vu', $phi->id, null, $phi->toArray());

        return redirect()->route('manager.phi-dich-vu.show', $phi)
            ->with('success', "Thêm phí dịch vụ «{$phi->ten_phi_dich_vu}» thành công.");
    }

    public function show(PhiDichVu $phiDichVu)
    {
        $phiDichVu->load([
            'loaiPhiDichVu',
            'donViTinh',
            'loaiTinhPhi',
            'nguoiCapNhat',
            'canHo.toaNha',
            'canHo.trangThai',
        ]);

        $soCanHo = $phiDichVu->canHo->count();

        return view('manager.phi-dich-vu.show', compact('phiDichVu', 'soCanHo'));
    }

    public function edit(PhiDichVu $phiDichVu)
    {
        $phiDichVu->load(['loaiPhiDichVu', 'donViTinh', 'loaiTinhPhi']);
        $dsLoaiPhi     = LoaiPhiDichVu::orderBy('ten_loai_phi_dich_vu')->get();
        $dsDonViTinh   = DonViTinhPhiDichVu::orderBy('don_vi')->get();
        $dsLoaiTinhPhi = LoaiTinhPhiDichVu::orderBy('ten_loai')->get();

        return view('manager.phi-dich-vu.edit', compact('phiDichVu', 'dsLoaiPhi', 'dsDonViTinh', 'dsLoaiTinhPhi'));
    }

    public function update(UpdatePhiDichVuRequest $request, PhiDichVu $phiDichVu)
    {
        $old = $phiDichVu->toArray();

        DB::transaction(function () use ($request, $phiDichVu) {
            $phiDichVu->update([
                'ten_phi_dich_vu'  => $request->ten_phi_dich_vu,
                'don_gia'          => $request->don_gia,
                'loai_phi_dich_vu' => $request->loai_phi_dich_vu,
                'don_vi_tinh'      => $request->don_vi_tinh,
                'loai_tinh_phi'    => $request->loai_tinh_phi,
                'nguoi_cap_nhat'   => auth('nhanvien')->id(),
            ]);
        });

        AuditLogService::log('UPDATE', 'phi_dich_vu', $phiDichVu->id, $old, $phiDichVu->fresh()->toArray());

        return redirect()->route('manager.phi-dich-vu.show', $phiDichVu)
            ->with('success', "Cập nhật phí dịch vụ «{$phiDichVu->ten_phi_dich_vu}» thành công.");
    }

    public function destroy(PhiDichVu $phiDichVu)
    {
        if ($phiDichVu->canHo()->exists()) {
            $count = $phiDichVu->canHo()->count();
            return back()->with('error', "Phí dịch vụ «{$phiDichVu->ten_phi_dich_vu}» đã được sử dụng trong {$count} căn hộ, không thể xóa.");
        }

        DB::transaction(function () use ($phiDichVu) {
            AuditLogService::log('DELETE', 'phi_dich_vu', $phiDichVu->id, $phiDichVu->toArray(), null);
            $phiDichVu->delete();
        });

        return redirect()->route('manager.phi-dich-vu.index')
            ->with('success', "Đã xóa phí dịch vụ «{$phiDichVu->ten_phi_dich_vu}».");
    }
}
