<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\UpdateCauHinhWebsiteRequest;
use App\Models\CauHinhWebsite;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CauHinhWebsiteController extends Controller
{
    private const SORTABLE = ['id', 'ten_thuoc_tinh', 'ten_nhom', 'thu_tu', 'trang_thai', 'created_at', 'updated_at'];
    private const SEARCHABLE = ['ma_thuoc_tinh', 'ten_thuoc_tinh', 'gia_tri', 'mo_ta', 'placeholder'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'thu_tu';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $query = CauHinhWebsite::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                foreach (self::SEARCHABLE as $col) {
                    $q->orWhere($col, 'like', "%$s%");
                }
            });
        }

        if ($request->filled('ma_nhom')) {
            $query->where('ma_nhom', $request->ma_nhom);
        }

        if ($request->filled('kieu_du_lieu')) {
            $query->where('kieu_du_lieu', $request->kieu_du_lieu);
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $dsCauHinh = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        $tongThuocTinh = CauHinhWebsite::count();
        $tongHoatDong  = CauHinhWebsite::where('trang_thai', 1)->count();
        $tongVoHieuHoa = CauHinhWebsite::where('trang_thai', 0)->count();

        return view('manager.cau-hinh-website.index', compact(
            'dsCauHinh', 'sort', 'direction', 'tongThuocTinh', 'tongHoatDong', 'tongVoHieuHoa'
        ));
    }

    public function show(CauHinhWebsite $cauHinhWebsite)
    {
        return view('manager.cau-hinh-website.show', compact('cauHinhWebsite'));
    }

    public function edit(CauHinhWebsite $cauHinhWebsite)
    {
        return view('manager.cau-hinh-website.edit', compact('cauHinhWebsite'));
    }

    public function update(UpdateCauHinhWebsiteRequest $request, CauHinhWebsite $cauHinhWebsite)
    {
        if (! $cauHinhWebsite->duoc_chinh_sua) {
            return back()->with('error', 'Cấu hình này được bảo vệ và không thể chỉnh sửa.');
        }

        $data = $request->safe()->only([
            'gia_tri', 'mo_ta', 'placeholder', 'thu_tu', 'la_bao_mat', 'duoc_chinh_sua', 'trang_thai',
        ]);
        $data['trang_thai']     = $request->boolean('trang_thai');
        $data['la_bao_mat']     = $request->boolean('la_bao_mat');
        $data['duoc_chinh_sua'] = $request->boolean('duoc_chinh_sua', true);

        DB::transaction(function () use ($request, $data, $cauHinhWebsite) {
            $old = $cauHinhWebsite->toArray();
            $data['gia_tri'] = $this->resolveGiaTri($request, $cauHinhWebsite->kieu_du_lieu, $data['gia_tri'] ?? null, $cauHinhWebsite);
            $cauHinhWebsite->update($data);
            AuditLogService::log('UPDATE', 'cau_hinh_website', $cauHinhWebsite->id, $old, $cauHinhWebsite->fresh()->toArray());
        });

        return redirect()->route('manager.cau-hinh-website.show', $cauHinhWebsite)
            ->with('success', 'Cập nhật cấu hình thành công.');
    }

    public function toggleStatus(CauHinhWebsite $cauHinhWebsite)
    {
        if (! $cauHinhWebsite->duoc_chinh_sua) {
            return back()->with('error', 'Cấu hình này được bảo vệ và không thể thay đổi trạng thái.');
        }

        $old    = $cauHinhWebsite->toArray();
        $newVal = ! $cauHinhWebsite->trang_thai;
        $cauHinhWebsite->update(['trang_thai' => $newVal]);
        AuditLogService::log('UPDATE', 'cau_hinh_website', $cauHinhWebsite->id, $old, $cauHinhWebsite->fresh()->toArray());

        $msg = $newVal ? 'Kích hoạt' : 'Vô hiệu hóa';
        return back()->with('success', "$msg cấu hình thành công.");
    }

    protected function resolveGiaTri(Request $request, string $kieuDuLieu, ?string $giaTri, ?CauHinhWebsite $existing = null): ?string
    {
        if (! in_array($kieuDuLieu, CauHinhWebsite::UPLOAD_TYPES)) {
            return $giaTri;
        }

        if ($request->hasFile('gia_tri_file')) {
            if ($existing && $existing->gia_tri && Storage::disk('public')->exists($existing->gia_tri)) {
                Storage::disk('public')->delete($existing->gia_tri);
            }
            return $request->file('gia_tri_file')->store('cau-hinh-website', 'public');
        }

        return $existing->gia_tri ?? null;
    }
}
