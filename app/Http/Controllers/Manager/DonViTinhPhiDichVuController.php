<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DonViTinhPhiDichVu;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonViTinhPhiDichVuController extends Controller
{
    private const SORTABLE = ['id', 'don_vi'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'id';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $tongTatCa    = DonViTinhPhiDichVu::count();
        $tongDangDung = DonViTinhPhiDichVu::whereHas('phiDichVu')->count();

        $query = DonViTinhPhiDichVu::withCount('phiDichVu');

        if ($request->filled('search')) {
            $query->where('don_vi', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('su_dung')) {
            if ($request->su_dung === 'co') {
                $query->whereHas('phiDichVu');
            } elseif ($request->su_dung === 'khong') {
                $query->whereDoesntHave('phiDichVu');
            }
        }

        $dsItem = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('manager.don-vi-tinh-phi-dich-vu.index', compact(
            'dsItem', 'sort', 'direction', 'tongTatCa', 'tongDangDung'
        ));
    }

    public function create()
    {
        return view('manager.don-vi-tinh-phi-dich-vu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'don_vi' => [
                'required', 'string', 'max:50',
                Rule::unique('don_vi_tinh_phi_dich_vu', 'don_vi')->whereNull('deletedAt'),
            ],
        ], [
            'don_vi.required' => 'Vui lòng nhập tên đơn vị tính.',
            'don_vi.unique'   => 'Tên đơn vị tính đã tồn tại.',
            'don_vi.max'      => 'Tên đơn vị tính không được vượt quá 50 ký tự.',
        ]);

        $item = DonViTinhPhiDichVu::create(['don_vi' => $request->don_vi]);
        AuditLogService::log('INSERT', 'don_vi_tinh_phi_dich_vu', $item->id, null, $item->toArray());

        return redirect()->route('manager.don-vi-tinh-phi-dich-vu.show', $item)
            ->with('success', "Thêm đơn vị tính «{$item->don_vi}» thành công.");
    }

    public function show(DonViTinhPhiDichVu $donViTinhPhiDichVu)
    {
        $donViTinhPhiDichVu->load([
            'phiDichVu.loaiPhiDichVu',
            'phiDichVu.loaiTinhPhi',
            'phiDichVu.canHo',
        ]);

        return view('manager.don-vi-tinh-phi-dich-vu.show', compact('donViTinhPhiDichVu'));
    }

    public function edit(DonViTinhPhiDichVu $donViTinhPhiDichVu)
    {
        return view('manager.don-vi-tinh-phi-dich-vu.edit', compact('donViTinhPhiDichVu'));
    }

    public function update(Request $request, DonViTinhPhiDichVu $donViTinhPhiDichVu)
    {
        $request->validate([
            'don_vi' => [
                'required', 'string', 'max:50',
                Rule::unique('don_vi_tinh_phi_dich_vu', 'don_vi')->whereNull('deletedAt')->ignore($donViTinhPhiDichVu->id),
            ],
        ], [
            'don_vi.required' => 'Vui lòng nhập tên đơn vị tính.',
            'don_vi.unique'   => 'Tên đơn vị tính đã tồn tại.',
            'don_vi.max'      => 'Tên đơn vị tính không được vượt quá 50 ký tự.',
        ]);

        $old = $donViTinhPhiDichVu->toArray();
        $donViTinhPhiDichVu->update(['don_vi' => $request->don_vi]);
        AuditLogService::log('UPDATE', 'don_vi_tinh_phi_dich_vu', $donViTinhPhiDichVu->id, $old, $donViTinhPhiDichVu->fresh()->toArray());

        return redirect()->route('manager.don-vi-tinh-phi-dich-vu.show', $donViTinhPhiDichVu)
            ->with('success', 'Cập nhật đơn vị tính thành công.');
    }

    public function destroy(DonViTinhPhiDichVu $donViTinhPhiDichVu)
    {
        if ($donViTinhPhiDichVu->phiDichVu()->exists()) {
            $count = $donViTinhPhiDichVu->phiDichVu()->count();
            return back()->with('error', "Không thể xóa đơn vị tính «{$donViTinhPhiDichVu->don_vi}» vì đang có {$count} phí dịch vụ sử dụng.");
        }

        AuditLogService::log('DELETE', 'don_vi_tinh_phi_dich_vu', $donViTinhPhiDichVu->id, $donViTinhPhiDichVu->toArray(), null);
        $donViTinhPhiDichVu->delete();

        return redirect()->route('manager.don-vi-tinh-phi-dich-vu.index')
            ->with('success', "Đã xóa đơn vị tính «{$donViTinhPhiDichVu->don_vi}».");
    }
}
