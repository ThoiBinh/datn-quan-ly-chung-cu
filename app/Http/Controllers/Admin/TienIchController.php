<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTienIchRequest;
use App\Http\Requests\Admin\UpdateTienIchRequest;
use App\Models\DatLichTienIch;
use App\Models\LoaiTienIch;
use App\Models\TienIch;
use App\Models\ToaNha;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TienIchController extends Controller
{
    private const SORTABLE = ['ten_tien_ich', 'phi_su_dung', 'suc_chua', 'createdAt', 'updatedAt'];

    private function danhSachLua(): array
    {
        return [
            'dsLoaiTienIch' => LoaiTienIch::orderBy('ten_loai_tien_ich')->get(),
            'dsToaNha'      => ToaNha::orderBy('ten_toa_nha')->get(),
        ];
    }

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = TienIch::with(['loaiTienIch', 'toaNha', 'nguoiCapNhat'])
            ->withCount('datLich');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ten_tien_ich', 'like', "%{$s}%")
                    ->orWhere('vi_tri', 'like', "%{$s}%")
                    ->orWhereHas('toaNha', fn ($qt) => $qt->where('ten_toa_nha', 'like', "%{$s}%"))
                    ->orWhereHas('loaiTienIch', fn ($ql) => $ql->where('ten_loai_tien_ich', 'like', "%{$s}%"));

                foreach (TienIch::dsTrangThai() as $id => $label) {
                    if (mb_stripos($label, $s) !== false) {
                        $q->orWhere('trang_thai', $id);
                    }
                }
            });
        }
        if ($request->filled('loai_tien_ich')) {
            $query->where('loai_tien_ich', $request->loai_tien_ich);
        }
        if ($request->filled('toa_nha')) {
            $query->where('toa_nha', $request->toa_nha);
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $dsTienIch = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('admin.tien-ich.index', array_merge(
            compact('dsTienIch', 'sort', 'direction'),
            $this->danhSachLua()
        ));
    }

    public function create()
    {
        return view('admin.tien-ich.create', $this->danhSachLua());
    }

    public function store(StoreTienIchRequest $request)
    {
        $data = $request->safe()->except(['hinh_anh']);
        $data['can_dat_truoc'] = $request->boolean('can_dat_truoc');
        $data['trang_thai']    = $request->boolean('trang_thai');
        $data['nguoi_cap_nhat'] = auth('nhanvien')->id();

        if ($request->hasFile('hinh_anh')) {
            $data['hinh_url'] = $request->file('hinh_anh')->store('tien-ich', 'public');
        }

        $tienIch = DB::transaction(function () use ($data) {
            $tienIch = TienIch::create($data);
            AuditLogService::log('INSERT', 'tien_ich', $tienIch->id, null, $tienIch->toArray());

            return $tienIch;
        });

        return redirect()->route('admin.tien-ich.show', $tienIch)
            ->with('success', "Thêm tiện ích «{$tienIch->ten_tien_ich}» thành công.");
    }

    public function show(TienIch $tienIch)
    {
        $tienIch->load(['loaiTienIch', 'toaNha', 'nguoiCapNhat', 'datLich' => function ($q) {
            $q->with('cuDan')->latest('thoi_gian_bat_dau')->limit(10);
        }]);

        $thongKe = DatLichTienIch::query()
            ->where('tien_ich', $tienIch->id)
            ->selectRaw(
                'COUNT(*) as tong,
                 SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as hoan_thanh',
                [DatLichTienIch::TRANG_THAI_HOAN_THANH]
            )->first();

        return view('admin.tien-ich.show', [
            'tienIch'     => $tienIch,
            'tongLuotDat' => (int) $thongKe->tong,
            'tongHoanThanh' => (int) $thongKe->hoan_thanh,
        ]);
    }

    public function edit(TienIch $tienIch)
    {
        return view('admin.tien-ich.edit', array_merge(
            ['tienIch' => $tienIch],
            $this->danhSachLua()
        ));
    }

    public function update(UpdateTienIchRequest $request, TienIch $tienIch)
    {
        $old = $tienIch->toArray();

        $data = $request->safe()->except(['hinh_anh', 'xoa_hinh']);
        $data['can_dat_truoc'] = $request->boolean('can_dat_truoc');
        $data['trang_thai']    = $request->boolean('trang_thai');
        $data['nguoi_cap_nhat'] = auth('nhanvien')->id();

        if ($request->hasFile('hinh_anh')) {
            if ($tienIch->hinh_url && Storage::disk('public')->exists($tienIch->hinh_url)) {
                Storage::disk('public')->delete($tienIch->hinh_url);
            }
            $data['hinh_url'] = $request->file('hinh_anh')->store('tien-ich', 'public');
        } elseif ($request->boolean('xoa_hinh')) {
            if ($tienIch->hinh_url && Storage::disk('public')->exists($tienIch->hinh_url)) {
                Storage::disk('public')->delete($tienIch->hinh_url);
            }
            $data['hinh_url'] = null;
        }

        DB::transaction(function () use ($tienIch, $data, $old) {
            $tienIch->update($data);
            AuditLogService::log('UPDATE', 'tien_ich', $tienIch->id, $old, $tienIch->fresh()->toArray());
        });

        return redirect()->route('admin.tien-ich.show', $tienIch)
            ->with('success', "Cập nhật tiện ích «{$tienIch->ten_tien_ich}» thành công.");
    }

    public function destroy(TienIch $tienIch)
    {
        if ($tienIch->datLich()->exists()) {
            return back()->with('error', "Tiện ích «{$tienIch->ten_tien_ich}» đang có lịch đặt, không thể xóa.");
        }

        DB::transaction(function () use ($tienIch) {
            AuditLogService::log('DELETE', 'tien_ich', $tienIch->id, $tienIch->toArray(), null);
            $tienIch->delete();
        });

        return redirect()->route('admin.tien-ich.index')
            ->with('success', "Đã xóa tiện ích «{$tienIch->ten_tien_ich}».");
    }
}
