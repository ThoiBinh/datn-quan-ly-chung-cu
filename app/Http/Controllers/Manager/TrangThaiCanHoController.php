<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreTrangThaiCanHoRequest;
use App\Http\Requests\Manager\UpdateTrangThaiCanHoRequest;
use App\Models\TrangThaiCanHo;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class TrangThaiCanHoController extends Controller
{
    private const SORTABLE = ['id', 'ten_trang_thai'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'id';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $tongTatCa    = TrangThaiCanHo::count();
        $tongDangDung = TrangThaiCanHo::whereHas('canHo')->count();

        $query = TrangThaiCanHo::withCount('canHo');

        if ($request->filled('search')) {
            $query->where('ten_trang_thai', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('su_dung')) {
            if ($request->su_dung === 'co') {
                $query->whereHas('canHo');
            } elseif ($request->su_dung === 'khong') {
                $query->whereDoesntHave('canHo');
            }
        }

        $dsTrangThai = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('manager.trang-thai-can-ho.index', compact(
            'dsTrangThai', 'sort', 'direction', 'tongTatCa', 'tongDangDung'
        ));
    }

    public function create()
    {
        return view('manager.trang-thai-can-ho.create');
    }

    public function store(StoreTrangThaiCanHoRequest $request)
    {
        $trangThai = TrangThaiCanHo::create(['ten_trang_thai' => $request->ten_trang_thai]);
        AuditLogService::log('INSERT', 'trang_thai_can_ho', $trangThai->id, null, $trangThai->toArray());

        return redirect()->route('manager.trang-thai-can-ho.show', $trangThai)
            ->with('success', "Thêm trạng thái căn hộ «{$trangThai->ten_trang_thai}» thành công.");
    }

    public function show(TrangThaiCanHo $trangThaiCanHo)
    {
        $trangThaiCanHo->load(['canHo.toaNha', 'canHo.loaiCanHo', 'canHo.chuHo.cuDan']);
        return view('manager.trang-thai-can-ho.show', compact('trangThaiCanHo'));
    }

    public function edit(TrangThaiCanHo $trangThaiCanHo)
    {
        return view('manager.trang-thai-can-ho.edit', compact('trangThaiCanHo'));
    }

    public function update(UpdateTrangThaiCanHoRequest $request, TrangThaiCanHo $trangThaiCanHo)
    {
        $old = $trangThaiCanHo->toArray();
        $trangThaiCanHo->update(['ten_trang_thai' => $request->ten_trang_thai]);
        AuditLogService::log('UPDATE', 'trang_thai_can_ho', $trangThaiCanHo->id, $old, $trangThaiCanHo->fresh()->toArray());

        return redirect()->route('manager.trang-thai-can-ho.show', $trangThaiCanHo)
            ->with('success', 'Cập nhật trạng thái căn hộ thành công.');
    }

    public function destroy(TrangThaiCanHo $trangThaiCanHo)
    {
        if ($trangThaiCanHo->canHo()->exists()) {
            $count = $trangThaiCanHo->canHo()->count();
            return back()->with('error', "Không thể xóa trạng thái «{$trangThaiCanHo->ten_trang_thai}» vì đang có {$count} căn hộ ở trạng thái này.");
        }

        AuditLogService::log('DELETE', 'trang_thai_can_ho', $trangThaiCanHo->id, $trangThaiCanHo->toArray(), null);
        $trangThaiCanHo->delete();

        return redirect()->route('manager.trang-thai-can-ho.index')
            ->with('success', "Đã xóa trạng thái căn hộ «{$trangThaiCanHo->ten_trang_thai}».");
    }
}
