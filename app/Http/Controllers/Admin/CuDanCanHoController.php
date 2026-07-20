<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCuDanCanHoRequest;
use App\Http\Requests\Admin\UpdateCuDanCanHoRequest;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\CuDanCanHo;
use App\Models\ToaNha;
use App\Models\VaiTro;
use App\Rules\ChuHoDuyNhat;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        CuDanCanHo::whereNotNull('ngay_chuyen_di')
            ->where('ngay_chuyen_di', '<', Carbon::today())
            ->where('trang_thai', '!=', 0)
            ->update(['trang_thai' => 0]);

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

        return view('admin.cu-dan-can-ho.index', compact(
            'dsRecord', 'dsTrangThai', 'dsToaNha', 'dsVaiTro', 'dsCanHo', 'sort', 'direction'
        ));
    }

    public function create()
    {
        $dsCuDan     = CuDan::orderBy('ho_ten_dem')->orderBy('ten')->get();
        $dsCanHo     = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsVaiTro    = VaiTro::orderBy('vai_tro')->get();
        $dsTrangThai = $this->dsTrangThai();
        return view('admin.cu-dan-can-ho.create', compact('dsCuDan', 'dsCanHo', 'dsVaiTro', 'dsTrangThai'));
    }

    /**
     * Kiểm tra tức thời (AJAX) căn hộ đã có Chủ hộ đang cư trú hay chưa, dùng để
     * chặn sớm ở frontend trước khi Submit. Backend (ChuHoDuyNhat rule) vẫn là
     * nguồn kiểm tra cuối cùng khi lưu dữ liệu.
     */
    public function kiemTraChuHo(Request $request)
    {
        $canHoId  = $request->integer('can_ho');
        $ignoreId = $request->integer('ignore_id') ?: null;

        return response()->json([
            'has_owner' => ChuHoDuyNhat::daCoChuHo($canHoId, null, $ignoreId),
        ]);
    }

    public function store(StoreCuDanCanHoRequest $request)
    {
        $today        = Carbon::today();
        $ngayChuyenDi = $request->ngay_chuyen_di ? Carbon::parse($request->ngay_chuyen_di) : null;
        $trangThai    = (!$ngayChuyenDi || $ngayChuyenDi->gt($today)) ? 1 : 0;

        DB::transaction(function () use ($request, $ngayChuyenDi, $trangThai) {
            $record = CuDanCanHo::create([
                'cu_dan'          => $request->cu_dan,
                'can_ho'          => $request->can_ho,
                'vai_tro'         => $request->vai_tro ?: null,
                'ngay_chuyen_den' => $request->ngay_chuyen_den ?: null,
                'ngay_chuyen_di'  => $ngayChuyenDi,
                'trang_thai'      => $trangThai,
                'nguoi_cap_nhat'  => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('INSERT', 'cu_dan_can_ho', $record->id, null, $record->toArray());
        });

        return redirect()->route('admin.cu-dan-can-ho.index')
            ->with('success', 'Thêm thông tin cư trú thành công.');
    }

    public function show(CuDanCanHo $cuDanCanHo)
    {
        if ($cuDanCanHo->ngay_chuyen_di
            && $cuDanCanHo->ngay_chuyen_di->lt(Carbon::today())
            && $cuDanCanHo->trang_thai != 0) {
            $cuDanCanHo->update(['trang_thai' => 0]);
        }

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

        return view('admin.cu-dan-can-ho.show', compact(
            'cuDanCanHo', 'hoaDonGanNhat', 'tongYeuCau', 'yeuCauGanNhat', 'dsTrangThai'
        ));
    }

    public function edit(CuDanCanHo $cuDanCanHo)
    {
        $dsCuDan     = CuDan::orderBy('ho_ten_dem')->orderBy('ten')->get();
        $dsCanHo     = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsVaiTro    = VaiTro::orderBy('vai_tro')->get();
        $dsTrangThai = $this->dsTrangThai();
        return view('admin.cu-dan-can-ho.edit', compact('cuDanCanHo', 'dsCuDan', 'dsCanHo', 'dsVaiTro', 'dsTrangThai'));
    }

    public function update(UpdateCuDanCanHoRequest $request, CuDanCanHo $cuDanCanHo)
    {
        $today          = Carbon::today();
        $oldTrangThai   = $cuDanCanHo->trang_thai;
        $inputTrangThai = (int) $request->trang_thai;
        $ngayChuyenDi   = $request->ngay_chuyen_di ? Carbon::parse($request->ngay_chuyen_di) : null;

        if ($inputTrangThai === 0 && $oldTrangThai != 0 && empty($ngayChuyenDi)) {
            $ngayChuyenDi = $today->copy();
        }
        if ($inputTrangThai === 1 && $oldTrangThai != 1) {
            $ngayChuyenDi = null;
        }

        $trangThai = (!$ngayChuyenDi || $ngayChuyenDi->gt($today)) ? 1 : 0;

        $old = $cuDanCanHo->toArray();

        DB::transaction(function () use ($request, $cuDanCanHo, $ngayChuyenDi, $trangThai, $old) {
            $cuDanCanHo->update([
                'cu_dan'          => $request->cu_dan,
                'can_ho'          => $request->can_ho,
                'vai_tro'         => $request->vai_tro ?: null,
                'ngay_chuyen_den' => $request->ngay_chuyen_den ?: null,
                'ngay_chuyen_di'  => $ngayChuyenDi,
                'trang_thai'      => $trangThai,
                'nguoi_cap_nhat'  => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('UPDATE', 'cu_dan_can_ho', $cuDanCanHo->id, $old, $cuDanCanHo->fresh()->toArray());
        });

        return redirect()->route('admin.cu-dan-can-ho.show', $cuDanCanHo)
            ->with('success', 'Cập nhật thông tin cư trú thành công.');
    }

    public function toggleStatus(CuDanCanHo $cuDanCanHo)
    {
        $old          = ['trang_thai' => $cuDanCanHo->trang_thai, 'ngay_chuyen_di' => $cuDanCanHo->ngay_chuyen_di];
        $newStatus    = $cuDanCanHo->trang_thai == 1 ? 0 : 1;
        $ngayChuyenDi = $cuDanCanHo->ngay_chuyen_di;

        if ($newStatus === 0 && empty($ngayChuyenDi)) {
            $ngayChuyenDi = Carbon::today();
        }
        if ($newStatus === 1) {
            $ngayChuyenDi = null;
        }

        $cuDanCanHo->update([
            'trang_thai'     => $newStatus,
            'ngay_chuyen_di' => $ngayChuyenDi,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('UPDATE', 'cu_dan_can_ho', $cuDanCanHo->id, $old, ['trang_thai' => $newStatus, 'ngay_chuyen_di' => $ngayChuyenDi]);

        $label = $this->dsTrangThai()[$newStatus]['text'];
        return back()->with('success', "Đã cập nhật trạng thái thành «{$label}».");
    }
}
