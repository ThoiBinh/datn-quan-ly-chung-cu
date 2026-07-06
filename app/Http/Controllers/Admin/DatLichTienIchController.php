<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDatLichTienIchRequest;
use App\Http\Requests\Admin\UpdateDatLichTienIchRequest;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\DatLichTienIch;
use App\Models\NhanVien;
use App\Models\TienIch;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DatLichTienIchController extends Controller
{
    private const SORTABLE = ['thoi_gian_bat_dau', 'createdAt', 'trang_thai'];

    private function danhSachLua(): array
    {
        return [
            'dsCuDan'    => CuDan::orderBy('ho_ten_dem')->orderBy('ten')->get(),
            'dsCanHo'    => CanHo::with('toaNha')->orderBy('so_can_ho')->get(),
            'dsTienIch'  => TienIch::with('loaiTienIch')->orderBy('ten_tien_ich')->get(),
            'dsNhanVien' => NhanVien::where('trang_thai', 1)->orderBy('ho_ten')->get(),
            'dsTrangThai' => DatLichTienIch::dsTrangThai(),
        ];
    }

    private function ghepNgayGio(string $ngay, string $gio): Carbon
    {
        return Carbon::parse($ngay . ' ' . $gio);
    }

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'thoi_gian_bat_dau';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = DatLichTienIch::with(['cuDan', 'canHo.toaNha', 'tienIch.loaiTienIch', 'nhanVienDuyet', 'nguoiCapNhat']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ma_dat_lich', 'like', "%{$s}%")
                    ->orWhereHas('cuDan', fn ($qc) => $qc->where('ho_ten_dem', 'like', "%{$s}%")
                        ->orWhere('ten', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%"))
                    ->orWhereHas('canHo', fn ($qc) => $qc->where('so_can_ho', 'like', "%{$s}%"))
                    ->orWhereHas('tienIch', fn ($qt) => $qt->where('ten_tien_ich', 'like', "%{$s}%"));

                foreach (DatLichTienIch::dsTrangThai() as $id => $label) {
                    if (mb_stripos($label, $s) !== false) {
                        $q->orWhere('trang_thai', $id);
                    }
                }
            });
        }
        if ($request->filled('tien_ich')) {
            $query->where('tien_ich', $request->tien_ich);
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('can_ho')) {
            $query->where('can_ho', $request->can_ho);
        }
        if ($request->filled('ngay_su_dung')) {
            $query->whereDate('thoi_gian_bat_dau', $request->ngay_su_dung);
        }
        if ($request->boolean('show_trashed')) {
            $query->onlyTrashed();
        }

        if ($sort === 'ho_ten') {
            $chuHoTen = CuDan::query()
                ->selectRaw("CONCAT(cu_dan.ho_ten_dem, ' ', cu_dan.ten)")
                ->whereColumn('cu_dan.id', 'dat_lich_tien_ich.cu_dan')
                ->limit(1);
            $query->orderBy($chuHoTen, $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $dsDatLich   = $query->paginate(15)->withQueryString();
        $dsTienIch   = TienIch::orderBy('ten_tien_ich')->get();
        $dsCanHo     = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsTrangThai = DatLichTienIch::dsTrangThai();

        return view('admin.dat-lich-tien-ich.index', compact(
            'dsDatLich', 'dsTienIch', 'dsCanHo', 'dsTrangThai', 'sort', 'direction'
        ));
    }

    public function create()
    {
        return view('admin.dat-lich-tien-ich.create', $this->danhSachLua());
    }

    public function store(StoreDatLichTienIchRequest $request)
    {
        $validated = $request->validated();

        $batDau  = $this->ghepNgayGio($validated['ngay_su_dung'], $validated['gio_bat_dau']);
        $ketThuc = $this->ghepNgayGio($validated['ngay_su_dung'], $validated['gio_ket_thuc']);
        $trangThai = (int) $validated['trang_thai'];

        $ngayDuyet = $validated['ngay_duyet'] ?? null;
        if ($trangThai === DatLichTienIch::TRANG_THAI_DA_DUYET && !$ngayDuyet) {
            $ngayDuyet = now();
        }
        $ngayHuy = $validated['ngay_huy'] ?? null;
        if ($trangThai === DatLichTienIch::TRANG_THAI_DA_HUY && !$ngayHuy) {
            $ngayHuy = now();
        }

        $datLich = DatLichTienIch::create([
            'ma_dat_lich'        => $validated['ma_dat_lich'],
            'cu_dan'             => $validated['cu_dan'],
            'can_ho'             => $validated['can_ho'] ?? null,
            'tien_ich'           => $validated['tien_ich'],
            'thoi_gian_bat_dau'  => $batDau,
            'thoi_gian_ket_thuc' => $ketThuc,
            'so_nguoi'           => $validated['so_nguoi'],
            'phi_su_dung'        => $validated['phi_su_dung'] ?? 0,
            'ghi_chu'            => $validated['ghi_chu'] ?? null,
            'trang_thai'         => $trangThai,
            'nhan_vien_duyet'    => $validated['nhan_vien_duyet'] ?? null,
            'ngay_duyet'         => $ngayDuyet,
            'ngay_huy'           => $ngayHuy,
            'ly_do_huy'          => $validated['ly_do_huy'] ?? null,
            'nguoi_cap_nhat'     => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('INSERT', 'dat_lich_tien_ich', $datLich->id, null, $datLich->toArray());

        return redirect()->route('admin.dat-lich-tien-ich.show', $datLich)
            ->with('success', "Thêm lịch đặt tiện ích «{$datLich->ma_dat_lich}» thành công.");
    }

    public function show(DatLichTienIch $datLichTienIch)
    {
        $datLichTienIch->load([
            'cuDan', 'canHo.toaNha', 'canHo.loaiCanHo', 'tienIch.loaiTienIch',
            'nhanVienDuyet', 'nguoiCapNhat',
        ]);

        return view('admin.dat-lich-tien-ich.show', compact('datLichTienIch'));
    }

    public function edit(DatLichTienIch $datLichTienIch)
    {
        return view('admin.dat-lich-tien-ich.edit', array_merge(
            ['datLichTienIch' => $datLichTienIch],
            $this->danhSachLua()
        ));
    }

    public function update(UpdateDatLichTienIchRequest $request, DatLichTienIch $datLichTienIch)
    {
        $validated = $request->validated();

        $batDau  = $this->ghepNgayGio($validated['ngay_su_dung'], $validated['gio_bat_dau']);
        $ketThuc = $this->ghepNgayGio($validated['ngay_su_dung'], $validated['gio_ket_thuc']);
        $trangThai = (int) $validated['trang_thai'];

        $ngayDuyet = $validated['ngay_duyet'] ?? null;
        if ($trangThai === DatLichTienIch::TRANG_THAI_DA_DUYET && !$ngayDuyet) {
            $ngayDuyet = $datLichTienIch->ngay_duyet ?? now();
        }
        $ngayHuy = $validated['ngay_huy'] ?? null;
        if ($trangThai === DatLichTienIch::TRANG_THAI_DA_HUY && !$ngayHuy) {
            $ngayHuy = $datLichTienIch->ngay_huy ?? now();
        }

        $old = $datLichTienIch->toArray();

        $datLichTienIch->update([
            'ma_dat_lich'        => $validated['ma_dat_lich'],
            'cu_dan'             => $validated['cu_dan'],
            'can_ho'             => $validated['can_ho'] ?? null,
            'tien_ich'           => $validated['tien_ich'],
            'thoi_gian_bat_dau'  => $batDau,
            'thoi_gian_ket_thuc' => $ketThuc,
            'so_nguoi'           => $validated['so_nguoi'],
            'phi_su_dung'        => $validated['phi_su_dung'] ?? 0,
            'ghi_chu'            => $validated['ghi_chu'] ?? null,
            'trang_thai'         => $trangThai,
            'nhan_vien_duyet'    => $validated['nhan_vien_duyet'] ?? null,
            'ngay_duyet'         => $ngayDuyet,
            'ngay_huy'           => $ngayHuy,
            'ly_do_huy'          => $validated['ly_do_huy'] ?? null,
            'nguoi_cap_nhat'     => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLichTienIch->id, $old, $datLichTienIch->fresh()->toArray());

        return redirect()->route('admin.dat-lich-tien-ich.show', $datLichTienIch)
            ->with('success', "Cập nhật lịch đặt tiện ích «{$datLichTienIch->ma_dat_lich}» thành công.");
    }

    public function destroy(DatLichTienIch $datLichTienIch)
    {
        AuditLogService::log('DELETE', 'dat_lich_tien_ich', $datLichTienIch->id, $datLichTienIch->toArray(), null);
        $datLichTienIch->delete();

        return redirect()->route('admin.dat-lich-tien-ich.index')
            ->with('success', "Đã xóa lịch đặt tiện ích «{$datLichTienIch->ma_dat_lich}».");
    }

    public function restore(int $id)
    {
        $datLichTienIch = DatLichTienIch::withTrashed()->findOrFail($id);
        $datLichTienIch->restore();
        AuditLogService::log('UPDATE', 'dat_lich_tien_ich', $datLichTienIch->id, null, $datLichTienIch->fresh()->toArray());

        return back()->with('success', "Khôi phục lịch đặt tiện ích «{$datLichTienIch->ma_dat_lich}» thành công.");
    }
}
