<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreCauHinhThanhToanRequest;
use App\Http\Requests\Manager\UpdateCauHinhThanhToanRequest;
use App\Models\CauHinhWebsite;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CauHinhThanhToanController extends Controller
{
    private const SEARCHABLE = ['ten_thuoc_tinh', 'gia_tri', 'ma_thuoc_tinh'];

    public function index(Request $request)
    {
        $query = CauHinhWebsite::payment()->orderBy('thu_tu');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                foreach (self::SEARCHABLE as $col) {
                    $q->orWhere($col, 'like', "%$s%");
                }
            });
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $dsCauHinh = $query->get();

        $nhomMomo  = $dsCauHinh->filter(fn ($c) => str_starts_with($c->ma_thuoc_tinh, 'momo_'))->values();
        $nhomVnpay = $dsCauHinh->filter(fn ($c) => str_starts_with($c->ma_thuoc_tinh, 'vnp_'))->values();
        $nhomQr    = $dsCauHinh->filter(fn ($c) => str_starts_with($c->ma_thuoc_tinh, 'qr_'))->values();
        $nhomKhac  = $dsCauHinh->reject(fn ($c) => str_starts_with($c->ma_thuoc_tinh, 'momo_')
            || str_starts_with($c->ma_thuoc_tinh, 'vnp_')
            || str_starts_with($c->ma_thuoc_tinh, 'qr_'))->values();

        $tongHoatDong  = CauHinhWebsite::payment()->where('trang_thai', 1)->count();
        $tongVoHieu    = CauHinhWebsite::payment()->where('trang_thai', 0)->count();

        return view('manager.cau-hinh-thanh-toan.index', compact(
            'nhomMomo', 'nhomVnpay', 'nhomQr', 'nhomKhac', 'tongHoatDong', 'tongVoHieu'
        ));
    }

    public function create()
    {
        return view('manager.cau-hinh-thanh-toan.create');
    }

    public function store(StoreCauHinhThanhToanRequest $request)
    {
        $data                   = $request->validated();
        $data['ma_nhom']        = 'payment';
        $data['ten_nhom']       = CauHinhWebsite::NHOM_OPTIONS['payment'];
        $data['trang_thai']     = $request->boolean('trang_thai', true);
        $data['la_bao_mat']     = $request->boolean('la_bao_mat');
        $data['duoc_chinh_sua'] = true;

        $cauHinh = DB::transaction(function () use ($data) {
            $ch = CauHinhWebsite::create($data);
            AuditLogService::log('INSERT', 'cau_hinh_website', $ch->id, null, $ch->toArray());
            return $ch;
        });

        return redirect()->route('manager.cau-hinh-thanh-toan.show', $cauHinh)
            ->with('success', "Thêm cấu hình «{$cauHinh->ten_thuoc_tinh}» thành công.");
    }

    public function show(CauHinhWebsite $cauHinhThanhToan)
    {
        $this->ensurePayment($cauHinhThanhToan);

        return view('manager.cau-hinh-thanh-toan.show', compact('cauHinhThanhToan'));
    }

    public function edit(CauHinhWebsite $cauHinhThanhToan)
    {
        $this->ensurePayment($cauHinhThanhToan);

        return view('manager.cau-hinh-thanh-toan.edit', compact('cauHinhThanhToan'));
    }

    public function update(UpdateCauHinhThanhToanRequest $request, CauHinhWebsite $cauHinhThanhToan)
    {
        $this->ensurePayment($cauHinhThanhToan);

        if (! $cauHinhThanhToan->duoc_chinh_sua) {
            return back()->with('error', 'Cấu hình này được bảo vệ và không thể chỉnh sửa.');
        }

        $data               = $request->validated();
        $data['trang_thai'] = $request->boolean('trang_thai');

        DB::transaction(function () use ($data, $cauHinhThanhToan) {
            $old = $cauHinhThanhToan->toArray();
            $cauHinhThanhToan->update($data);
            AuditLogService::log('UPDATE', 'cau_hinh_website', $cauHinhThanhToan->id, $old, $cauHinhThanhToan->fresh()->toArray());
        });

        return redirect()->route('manager.cau-hinh-thanh-toan.show', $cauHinhThanhToan)
            ->with('success', 'Cập nhật cấu hình thanh toán thành công.');
    }

    public function toggleStatus(CauHinhWebsite $cauHinhThanhToan)
    {
        $this->ensurePayment($cauHinhThanhToan);

        if (! $cauHinhThanhToan->duoc_chinh_sua) {
            return back()->with('error', 'Cấu hình này được bảo vệ và không thể thay đổi trạng thái.');
        }

        $old    = $cauHinhThanhToan->toArray();
        $newVal = ! $cauHinhThanhToan->trang_thai;
        $cauHinhThanhToan->update(['trang_thai' => $newVal]);
        AuditLogService::log('UPDATE', 'cau_hinh_website', $cauHinhThanhToan->id, $old, $cauHinhThanhToan->fresh()->toArray());

        $msg = $newVal ? 'Kích hoạt' : 'Vô hiệu hóa';
        return back()->with('success', "$msg cấu hình thành công.");
    }

    private function ensurePayment(CauHinhWebsite $cauHinhThanhToan): void
    {
        abort_unless($cauHinhThanhToan->ma_nhom === 'payment', 404);
    }
}
