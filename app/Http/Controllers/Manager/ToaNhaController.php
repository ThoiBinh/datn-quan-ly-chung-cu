<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreToaNhaRequest;
use App\Http\Requests\Manager\UpdateToaNhaRequest;
use App\Models\CuDanCanHo;
use App\Models\ToaNha;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ToaNhaController extends Controller
{
    private const SORTABLE = ['ten_toa_nha', 'tien_to', 'so_tang', 'createdAt'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = ToaNha::withCount('canHo');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('ten_toa_nha', 'like', "%$s%")
                ->orWhere('tien_to', 'like', "%$s%")
                ->orWhere('dia_chi', 'like', "%$s%")
            );
        }

        if ($request->filled('so_tang')) {
            $query->where('so_tang', $request->so_tang);
        }

        if ($request->co_can_ho === 'co') {
            $query->whereHas('canHo');
        } elseif ($request->co_can_ho === 'khong') {
            $query->whereDoesntHave('canHo');
        }

        $toaNha = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('manager.toa-nha.index', compact('toaNha', 'sort', 'direction'));
    }

    public function create()
    {
        return view('manager.toa-nha.create');
    }

    public function store(StoreToaNhaRequest $request)
    {
        $toaNha = ToaNha::create($request->validated());
        AuditLogService::log('INSERT', 'toa_nha', $toaNha->id, null, $toaNha->toArray());

        return redirect()->route('manager.toa-nha.index')
            ->with('success', "Thêm tòa nhà «{$toaNha->ten_toa_nha}» thành công.");
    }

    public function show(ToaNha $toaNha)
    {
        $toaNha->loadCount('tienIch');
        $toaNha->load(['canHo.trangThai', 'canHo.loaiCanHo', 'canHo.chuHo.cuDan']);

        $statsByTrangThai = $toaNha->canHo
            ->groupBy(fn($ch) => $ch->trangThai?->ten_trang_thai ?? 'Không xác định')
            ->map->count();

        $totalCuDan = CuDanCanHo::whereIn('can_ho', $toaNha->canHo->pluck('id'))
            ->where('trang_thai', 1)
            ->distinct('cu_dan')
            ->count('cu_dan');

        return view('manager.toa-nha.show', compact('toaNha', 'statsByTrangThai', 'totalCuDan'));
    }

    public function edit(ToaNha $toaNha)
    {
        return view('manager.toa-nha.edit', compact('toaNha'));
    }

    public function update(UpdateToaNhaRequest $request, ToaNha $toaNha)
    {
        $old = $toaNha->toArray();
        $toaNha->update($request->validated());
        AuditLogService::log('UPDATE', 'toa_nha', $toaNha->id, $old, $toaNha->fresh()->toArray());

        return redirect()->route('manager.toa-nha.show', $toaNha)
            ->with('success', "Cập nhật tòa nhà «{$toaNha->ten_toa_nha}» thành công.");
    }

    public function destroy(ToaNha $toaNha)
    {
        if ($toaNha->canHo()->exists()) {
            return back()->with('error', 'Tòa nhà đang có căn hộ, không thể xóa.');
        }

        AuditLogService::log('DELETE', 'toa_nha', $toaNha->id, $toaNha->toArray(), null);
        $toaNha->delete();

        return redirect()->route('manager.toa-nha.index')
            ->with('success', "Đã xóa tòa nhà «{$toaNha->ten_toa_nha}».");
    }
}
