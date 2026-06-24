<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\CuDanCanHo;
use App\Models\ToaNha;
use App\Models\VaiTro;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CuDanCanHoController extends Controller
{
    private const SORTABLE = ['createdAt', 'ngay_chuyen_den', 'ngay_chuyen_di', 'trang_thai'];

    private function dsTrangThai(): array
    {
        return [
            1 => ['text' => 'Đang cư trú',  'class' => 'bg-emerald-100 text-emerald-700'],
            0 => ['text' => 'Đã chuyển đi', 'class' => 'bg-gray-100 text-gray-600'],
        ];
    }

    public function index(Request $request)
    {
        $query = CuDanCanHo::with(['cuDan', 'canHo.toaNha', 'vaiTro']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('cuDan', fn($r) => $r
                    ->where('ho_ten_dem', 'like', "%{$s}%")
                    ->orWhere('ten',      'like', "%{$s}%")
                    ->orWhere('cccd',     'like', "%{$s}%")
                    ->orWhere('email',    'like', "%{$s}%"))
                  ->orWhereHas('canHo', fn($r) => $r->where('so_can_ho', 'like', "%{$s}%"))
                  ->orWhereHas('canHo.toaNha', fn($r) => $r->where('ten_toa_nha', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('toa_nha')) {
            $query->whereHas('canHo', fn($q) => $q->where('toa_nha', $request->toa_nha));
        }
        if ($request->filled('can_ho')) {
            $query->where('can_ho', $request->can_ho);
        }
        if ($request->filled('vai_tro')) {
            $query->where('vai_tro', $request->vai_tro);
        }
        if ($request->trang_thai !== null && $request->trang_thai !== '') {
            $query->where('trang_thai', $request->trang_thai);
        }

        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $dsRecord    = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $dsTrangThai = $this->dsTrangThai();
        $dsToaNha    = ToaNha::orderBy('ten_toa_nha')->get();
        $dsVaiTro    = VaiTro::orderBy('vai_tro')->get();
        $dsCanHo     = CanHo::with('toaNha')->orderBy('so_can_ho')->get();

        return view('manager.cu-dan-can-ho.index', compact(
            'dsRecord', 'dsTrangThai', 'dsToaNha', 'dsVaiTro', 'dsCanHo', 'sort', 'direction'
        ));
    }

    public function create()
    {
        $dsCuDan     = CuDan::orderBy('ho_ten_dem')->orderBy('ten')->get();
        $dsCanHo     = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsVaiTro    = VaiTro::orderBy('vai_tro')->get();
        $dsTrangThai = $this->dsTrangThai();
        return view('manager.cu-dan-can-ho.create', compact('dsCuDan', 'dsCanHo', 'dsVaiTro', 'dsTrangThai'));
    }

    public function store(Request $request)
    {
        $validTrangThai = implode(',', array_keys($this->dsTrangThai()));
        $request->validate([
            'cu_dan'          => 'required|integer|exists:cu_dan,id',
            'can_ho'          => 'required|integer|exists:can_ho,id',
            'vai_tro'         => 'nullable|integer|exists:vai_tro,id',
            'ngay_chuyen_den' => 'nullable|date',
            'ngay_chuyen_di'  => 'nullable|date|after_or_equal:ngay_chuyen_den',
            'trang_thai'      => 'required|in:' . $validTrangThai,
        ], [
            'cu_dan.required'              => 'Vui lòng chọn cư dân.',
            'can_ho.required'              => 'Vui lòng chọn căn hộ.',
            'ngay_chuyen_di.after_or_equal' => 'Ngày chuyển đi phải sau hoặc bằng ngày chuyển đến.',
        ]);

        $record = CuDanCanHo::create([
            'cu_dan'          => $request->cu_dan,
            'can_ho'          => $request->can_ho,
            'vai_tro'         => $request->vai_tro ?: null,
            'ngay_chuyen_den' => $request->ngay_chuyen_den ?: null,
            'ngay_chuyen_di'  => $request->ngay_chuyen_di  ?: null,
            'trang_thai'      => (int) $request->trang_thai,
            'nguoi_cap_nhat'  => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('INSERT', 'cu_dan_can_ho', $record->id, null, $record->toArray());

        return redirect()->route('manager.cu-dan-can-ho.index')
            ->with('success', 'Thêm thông tin cư trú thành công.');
    }

    public function show(CuDanCanHo $cuDanCanHo)
    {
        $cuDanCanHo->load([
            'cuDan',
            'canHo.toaNha',
            'canHo.loaiCanHo',
            'canHo.trangThai',
            'canHo.phuongTien.loaiPhuongTien',
            'canHo.phiDichVu',
            'vaiTro',
        ]);

        $hoaDonGanNhat = $cuDanCanHo->canHo?->hoaDon()->orderByDesc('createdAt')->first();
        $tongYeuCau    = $cuDanCanHo->cuDan?->yeuCau()->count() ?? 0;
        $yeuCauGanNhat = $cuDanCanHo->cuDan?->yeuCau()->with('loaiYeuCau')->orderByDesc('createdAt')->first();
        $dsTrangThai   = $this->dsTrangThai();

        return view('manager.cu-dan-can-ho.show', compact(
            'cuDanCanHo', 'hoaDonGanNhat', 'tongYeuCau', 'yeuCauGanNhat', 'dsTrangThai'
        ));
    }

    public function edit(CuDanCanHo $cuDanCanHo)
    {
        $dsCuDan     = CuDan::orderBy('ho_ten_dem')->orderBy('ten')->get();
        $dsCanHo     = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsVaiTro    = VaiTro::orderBy('vai_tro')->get();
        $dsTrangThai = $this->dsTrangThai();
        return view('manager.cu-dan-can-ho.edit', compact('cuDanCanHo', 'dsCuDan', 'dsCanHo', 'dsVaiTro', 'dsTrangThai'));
    }

    public function update(Request $request, CuDanCanHo $cuDanCanHo)
    {
        $validTrangThai = implode(',', array_keys($this->dsTrangThai()));
        $request->validate([
            'cu_dan'          => 'required|integer|exists:cu_dan,id',
            'can_ho'          => 'required|integer|exists:can_ho,id',
            'vai_tro'         => 'nullable|integer|exists:vai_tro,id',
            'ngay_chuyen_den' => 'nullable|date',
            'ngay_chuyen_di'  => 'nullable|date|after_or_equal:ngay_chuyen_den',
            'trang_thai'      => 'required|in:' . $validTrangThai,
        ], [
            'cu_dan.required'              => 'Vui lòng chọn cư dân.',
            'can_ho.required'              => 'Vui lòng chọn căn hộ.',
            'ngay_chuyen_di.after_or_equal' => 'Ngày chuyển đi phải sau hoặc bằng ngày chuyển đến.',
        ]);

        $old = $cuDanCanHo->toArray();
        $cuDanCanHo->update([
            'cu_dan'          => $request->cu_dan,
            'can_ho'          => $request->can_ho,
            'vai_tro'         => $request->vai_tro ?: null,
            'ngay_chuyen_den' => $request->ngay_chuyen_den ?: null,
            'ngay_chuyen_di'  => $request->ngay_chuyen_di  ?: null,
            'trang_thai'      => (int) $request->trang_thai,
            'nguoi_cap_nhat'  => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('UPDATE', 'cu_dan_can_ho', $cuDanCanHo->id, $old, $cuDanCanHo->fresh()->toArray());

        return redirect()->route('manager.cu-dan-can-ho.show', $cuDanCanHo)
            ->with('success', 'Cập nhật thông tin cư trú thành công.');
    }

    public function toggleStatus(CuDanCanHo $cuDanCanHo)
    {
        $old       = ['trang_thai' => $cuDanCanHo->trang_thai];
        $newStatus = $cuDanCanHo->trang_thai == 1 ? 0 : 1;
        $cuDanCanHo->update([
            'trang_thai'     => $newStatus,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('UPDATE', 'cu_dan_can_ho', $cuDanCanHo->id, $old, ['trang_thai' => $newStatus]);

        $label = $this->dsTrangThai()[$newStatus]['text'];
        return back()->with('success', "Đã cập nhật trạng thái thành «{$label}».");
    }
}
