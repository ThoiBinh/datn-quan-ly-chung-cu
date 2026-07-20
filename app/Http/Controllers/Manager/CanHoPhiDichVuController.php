<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreCanHoPhiDichVuRequest;
use App\Http\Requests\Manager\UpdateCanHoPhiDichVuRequest;
use App\Models\CanHo;
use App\Models\CanHoPhiDichVu;
use App\Models\DonViTinhPhiDichVu;
use App\Models\HoaDon;
use App\Models\LoaiCanHo;
use App\Models\LoaiPhiDichVu;
use App\Models\PhiDichVu;
use App\Models\ToaNha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CanHoPhiDichVuController extends Controller
{
    private const SORTABLE = [
        'createdAt'       => 'can_ho_phi_dich_vu.createdAt',
        'don_gia'         => 'can_ho_phi_dich_vu.don_gia',
        'so_can_ho'       => 'can_ho.so_can_ho',
        'ten_phi_dich_vu' => 'phi_dich_vu.ten_phi_dich_vu',
        'toa_nha'         => 'toa_nha.ten_toa_nha',
    ];

    public function index(Request $request)
    {
        $sort      = array_key_exists($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';
        $sortCol   = self::SORTABLE[$sort];

        $query = CanHoPhiDichVu::with([
            'canHo.toaNha',
            'canHo.loaiCanHo',
            'canHo.trangThai',
            'canHo.thuocTinhCanHo.thuocTinh',
            'phiDichVu.loaiPhiDichVu',
            'phiDichVu.donViTinh',
            'phiDichVu.loaiTinhPhi',
            'nguoiCapNhat',
        ])
        ->join('can_ho', 'can_ho_phi_dich_vu.can_ho', '=', 'can_ho.id')
        ->join('toa_nha', 'can_ho.toa_nha', '=', 'toa_nha.id')
        ->join('phi_dich_vu', 'can_ho_phi_dich_vu.phi_dich_vu', '=', 'phi_dich_vu.id')
        ->select('can_ho_phi_dich_vu.*');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('phi_dich_vu.ten_phi_dich_vu', 'like', "%$s%")
                  ->orWhere('can_ho.so_can_ho', 'like', "%$s%")
                  ->orWhere('toa_nha.ten_toa_nha', 'like', "%$s%")
                  ->orWhereHas('canHo.loaiCanHo', fn($q2) => $q2->where('ten_loai_can_ho', 'like', "%$s%"));
            });
        }

        if ($request->filled('toa_nha')) {
            $query->where('can_ho.toa_nha', $request->toa_nha);
        }
        if ($request->filled('loai_can_ho')) {
            $query->where('can_ho.loai_can_ho', $request->loai_can_ho);
        }
        if ($request->filled('loai_phi_dich_vu')) {
            $query->where('phi_dich_vu.loai_phi_dich_vu', $request->loai_phi_dich_vu);
        }
        if ($request->filled('don_vi_tinh')) {
            $query->where('phi_dich_vu.don_vi_tinh', $request->don_vi_tinh);
        }

        $dsRecord = $query->orderBy($sortCol, $direction)->paginate(15)->withQueryString();

        // Thống kê
        $tongApDung   = CanHoPhiDichVu::count();
        $tongCanHo    = CanHoPhiDichVu::distinct('can_ho')->count('can_ho');
        $tongDichVu   = CanHoPhiDichVu::distinct('phi_dich_vu')->count('phi_dich_vu');

        // Filter dropdowns
        $dsToaNha        = ToaNha::orderBy('ten_toa_nha')->get();
        $dsLoaiCanHo     = LoaiCanHo::orderBy('ten_loai_can_ho')->get();
        $dsLoaiPhiDichVu = LoaiPhiDichVu::orderBy('ten_loai_phi_dich_vu')->get();
        $dsDonViTinh     = DonViTinhPhiDichVu::orderBy('don_vi')->get();

        return view('manager.can-ho-phi-dich-vu.index', compact(
            'dsRecord', 'sort', 'direction',
            'tongApDung', 'tongCanHo', 'tongDichVu',
            'dsToaNha', 'dsLoaiCanHo', 'dsLoaiPhiDichVu', 'dsDonViTinh'
        ));
    }

    public function create()
    {
        $dsCanHo    = CanHo::with(['toaNha', 'loaiCanHo'])->orderBy('so_can_ho')->get();
        $dsPhiDV    = PhiDichVu::with(['loaiPhiDichVu', 'donViTinh', 'loaiTinhPhi'])->orderBy('ten_phi_dich_vu')->get();
        $canHoJson  = $this->buildCanHoJson($dsCanHo);
        $phiDvJson  = $this->buildPhiDvJson($dsPhiDV);

        return view('manager.can-ho-phi-dich-vu.create', compact('dsCanHo', 'dsPhiDV', 'canHoJson', 'phiDvJson'));
    }

    public function store(StoreCanHoPhiDichVuRequest $request)
    {
        $donGia = $request->filled('don_gia')
            ? $request->don_gia
            : PhiDichVu::findOrFail($request->phi_dich_vu)->don_gia;

        $record = DB::transaction(function () use ($request, $donGia) {
            return CanHoPhiDichVu::create([
                'can_ho'         => $request->can_ho,
                'phi_dich_vu'    => $request->phi_dich_vu,
                'don_gia'        => $donGia,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);
        });

        return redirect()->route('manager.can-ho-phi-dich-vu.show', $record)
            ->with('success', 'Áp dụng dịch vụ cho căn hộ thành công.');
    }

    public function show(CanHoPhiDichVu $canHoPhiDichVu)
    {
        $canHoPhiDichVu->load([
            'canHo.toaNha',
            'canHo.loaiCanHo',
            'canHo.trangThai',
            'canHo.thuocTinhCanHo.thuocTinh',
            'canHo.chuHo.cuDan',
            'canHo.chuHo.vaiTro',
            'canHo.cuDanHienTai.cuDan',
            'canHo.cuDanHienTai.vaiTro',
            'phiDichVu.loaiPhiDichVu',
            'phiDichVu.donViTinh',
            'phiDichVu.loaiTinhPhi',
            'nguoiCapNhat',
        ]);

        $coHoaDon = HoaDon::where('can_ho', $canHoPhiDichVu->can_ho)
            ->whereHas('chiTiet', fn($q) => $q->where('ten_phi_dich_vu', $canHoPhiDichVu->phiDichVu->ten_phi_dich_vu))
            ->exists();

        return view('manager.can-ho-phi-dich-vu.show', compact('canHoPhiDichVu', 'coHoaDon'));
    }

    public function edit(CanHoPhiDichVu $canHoPhiDichVu)
    {
        $canHoPhiDichVu->load([
            'canHo.toaNha', 'canHo.loaiCanHo',
            'phiDichVu.loaiPhiDichVu', 'phiDichVu.donViTinh', 'phiDichVu.loaiTinhPhi',
        ]);

        $dsCanHo   = CanHo::with(['toaNha', 'loaiCanHo'])->orderBy('so_can_ho')->get();
        $dsPhiDV   = PhiDichVu::with(['loaiPhiDichVu', 'donViTinh', 'loaiTinhPhi'])->orderBy('ten_phi_dich_vu')->get();
        $canHoJson = $this->buildCanHoJson($dsCanHo);
        $phiDvJson = $this->buildPhiDvJson($dsPhiDV);

        return view('manager.can-ho-phi-dich-vu.edit', compact(
            'canHoPhiDichVu', 'dsCanHo', 'dsPhiDV', 'canHoJson', 'phiDvJson'
        ));
    }

    public function update(UpdateCanHoPhiDichVuRequest $request, CanHoPhiDichVu $canHoPhiDichVu)
    {
        $donGia = $request->filled('don_gia')
            ? $request->don_gia
            : PhiDichVu::findOrFail($request->phi_dich_vu)->don_gia;

        DB::transaction(function () use ($request, $canHoPhiDichVu, $donGia) {
            $canHoPhiDichVu->update([
                'can_ho'         => $request->can_ho,
                'phi_dich_vu'    => $request->phi_dich_vu,
                'don_gia'        => $donGia,
                'nguoi_cap_nhat' => auth('nhanvien')->id(),
            ]);
        });

        return redirect()->route('manager.can-ho-phi-dich-vu.show', $canHoPhiDichVu)
            ->with('success', 'Cập nhật dịch vụ căn hộ thành công.');
    }

    public function destroy(CanHoPhiDichVu $canHoPhiDichVu)
    {
        $canHoPhiDichVu->load('phiDichVu');

        $coHoaDon = HoaDon::where('can_ho', $canHoPhiDichVu->can_ho)
            ->whereHas('chiTiet', fn($q) => $q->where('ten_phi_dich_vu', $canHoPhiDichVu->phiDichVu->ten_phi_dich_vu))
            ->exists();

        if ($coHoaDon) {
            return back()->with('error', 'Dịch vụ đã phát sinh hóa đơn nên không thể xóa.');
        }

        DB::transaction(fn() => $canHoPhiDichVu->delete());

        return redirect()->route('manager.can-ho-phi-dich-vu.index')
            ->with('success', 'Đã xóa áp dụng dịch vụ thành công.');
    }

    private function buildCanHoJson($dsCanHo): string
    {
        $data = $dsCanHo->map(function ($c) {
            return [
                'id'          => $c->id,
                'so_can_ho'   => $c->so_can_ho,
                'tang'        => $c->tang,
                'toa_nha_ten' => $c->toaNha ? $c->toaNha->ten_toa_nha : null,
                'loai_can_ho' => $c->loaiCanHo ? $c->loaiCanHo->ten_loai_can_ho : null,
            ];
        })->values()->toArray();

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function buildPhiDvJson($dsPhiDV): string
    {
        $data = $dsPhiDV->map(function ($p) {
            return [
                'id'              => $p->id,
                'ten_phi_dich_vu' => $p->ten_phi_dich_vu,
                'don_gia'         => (float) $p->don_gia,
                'loai'            => $p->loaiPhiDichVu ? $p->loaiPhiDichVu->ten_loai_phi_dich_vu : null,
                'don_vi'          => $p->donViTinh ? $p->donViTinh->don_vi : null,
                'loai_tinh'       => $p->loaiTinhPhi ? $p->loaiTinhPhi->ten_loai : null,
            ];
        })->values()->toArray();

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
