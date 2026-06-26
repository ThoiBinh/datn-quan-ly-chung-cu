<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LoaiPhiDichVu;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class LoaiPhiDichVuController extends Controller
{
    private const SORTABLE = ['id', 'ten_loai_phi_dich_vu'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'id';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $tongTatCa    = LoaiPhiDichVu::count();
        $tongDangDung = LoaiPhiDichVu::whereHas('phiDichVu')->count();

        $query = LoaiPhiDichVu::withCount('phiDichVu');

        if ($request->filled('search')) {
            $query->where('ten_loai_phi_dich_vu', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('su_dung')) {
            if ($request->su_dung === 'co') {
                $query->whereHas('phiDichVu');
            } elseif ($request->su_dung === 'khong') {
                $query->whereDoesntHave('phiDichVu');
            }
        }

        $dsLoai = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('manager.loai-phi-dich-vu.index', compact(
            'dsLoai', 'sort', 'direction', 'tongTatCa', 'tongDangDung'
        ));
    }

    public function create()
    {
        return view('manager.loai-phi-dich-vu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_loai_phi_dich_vu' => 'required|string|max:150|unique:loai_phi_dich_vu,ten_loai_phi_dich_vu',
        ], [
            'ten_loai_phi_dich_vu.required' => 'Vui lòng nhập tên loại phí dịch vụ.',
            'ten_loai_phi_dich_vu.unique'   => 'Tên loại phí dịch vụ đã tồn tại.',
            'ten_loai_phi_dich_vu.max'      => 'Tên không được vượt quá 150 ký tự.',
        ]);

        $loai = LoaiPhiDichVu::create(['ten_loai_phi_dich_vu' => $request->ten_loai_phi_dich_vu]);
        AuditLogService::log('INSERT', 'loai_phi_dich_vu', $loai->id, null, $loai->toArray());

        return redirect()->route('manager.loai-phi-dich-vu.show', $loai)
            ->with('success', "Thêm loại phí dịch vụ «{$loai->ten_loai_phi_dich_vu}» thành công.");
    }

    public function show(LoaiPhiDichVu $loaiPhiDichVu)
    {
        $loaiPhiDichVu->load([
            'phiDichVu.donViTinh',
            'phiDichVu.loaiTinhPhi',
            'phiDichVu.canHo',
        ]);

        return view('manager.loai-phi-dich-vu.show', compact('loaiPhiDichVu'));
    }

    public function edit(LoaiPhiDichVu $loaiPhiDichVu)
    {
        return view('manager.loai-phi-dich-vu.edit', compact('loaiPhiDichVu'));
    }

    public function update(Request $request, LoaiPhiDichVu $loaiPhiDichVu)
    {
        $request->validate([
            'ten_loai_phi_dich_vu' => "required|string|max:150|unique:loai_phi_dich_vu,ten_loai_phi_dich_vu,{$loaiPhiDichVu->id}",
        ], [
            'ten_loai_phi_dich_vu.required' => 'Vui lòng nhập tên loại phí dịch vụ.',
            'ten_loai_phi_dich_vu.unique'   => 'Tên loại phí dịch vụ đã tồn tại.',
            'ten_loai_phi_dich_vu.max'      => 'Tên không được vượt quá 150 ký tự.',
        ]);

        $old = $loaiPhiDichVu->toArray();
        $loaiPhiDichVu->update(['ten_loai_phi_dich_vu' => $request->ten_loai_phi_dich_vu]);
        AuditLogService::log('UPDATE', 'loai_phi_dich_vu', $loaiPhiDichVu->id, $old, $loaiPhiDichVu->fresh()->toArray());

        return redirect()->route('manager.loai-phi-dich-vu.show', $loaiPhiDichVu)
            ->with('success', 'Cập nhật loại phí dịch vụ thành công.');
    }

    public function destroy(LoaiPhiDichVu $loaiPhiDichVu)
    {
        if ($loaiPhiDichVu->phiDichVu()->exists()) {
            $count = $loaiPhiDichVu->phiDichVu()->count();
            return back()->with('error', "Không thể xóa loại phí «{$loaiPhiDichVu->ten_loai_phi_dich_vu}» vì đang có {$count} phí dịch vụ thuộc loại này.");
        }

        AuditLogService::log('DELETE', 'loai_phi_dich_vu', $loaiPhiDichVu->id, $loaiPhiDichVu->toArray(), null);
        $loaiPhiDichVu->delete();

        return redirect()->route('manager.loai-phi-dich-vu.index')
            ->with('success', "Đã xóa loại phí dịch vụ «{$loaiPhiDichVu->ten_loai_phi_dich_vu}».");
    }
}
