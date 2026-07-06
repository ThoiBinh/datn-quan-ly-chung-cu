<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\CuDanCanHo;
use App\Models\ToaNha;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ToaNhaController extends Controller
{
    private const SORTABLE = ['ten_toa_nha', 'dia_chi', 'so_tang', 'createdAt'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = ToaNha::withCount([
            'canHo',
            'canHo as can_ho_co_cu_dan_count' => fn($q) => $q->whereHas('cuDanHienTai'),
        ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('ten_toa_nha', 'like', "%$s%")
                ->orWhere('dia_chi', 'like', "%$s%")
            );
        }

        $toaNha = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('admin.toa-nha.index', compact('toaNha', 'sort', 'direction'));
    }

    public function create()
    {
        return view('admin.toa-nha.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_toa_nha' => 'required|string|max:255',
            'dia_chi'     => 'nullable|string|max:255',
            'so_tang'     => 'nullable|integer|min:1|max:200',
        ], [
            'ten_toa_nha.required' => 'Vui lòng nhập tên tòa nhà.',
            'so_tang.min'          => 'Số tầng phải lớn hơn 0.',
            'so_tang.max'          => 'Số tầng không được vượt quá 200.',
        ]);

        $toaNha = ToaNha::create([
            'ten_toa_nha' => $request->ten_toa_nha,
            'dia_chi'     => $request->dia_chi ?? '',
            'so_tang'     => $request->so_tang ?? 1,
        ]);
        AuditLogService::log('INSERT', 'toa_nha', $toaNha->id, null, $toaNha->toArray());

        return redirect()->route('admin.toa-nha.index')
            ->with('success', "Thêm tòa nhà «{$toaNha->ten_toa_nha}» thành công.");
    }

    public function show(ToaNha $toaNha)
    {
        $toaNha->load(['canHo.trangThai', 'canHo.loaiCanHo', 'canHo.chuHo.cuDan']);

        $statsByTrangThai = $toaNha->canHo
            ->groupBy(fn($ch) => $ch->trangThai?->ten_trang_thai ?? 'Không xác định')
            ->map->count();

        $totalCuDan = CuDanCanHo::whereIn('can_ho', $toaNha->canHo->pluck('id'))
            ->where('trang_thai', 1)
            ->distinct('cu_dan')
            ->count('cu_dan');

        return view('admin.toa-nha.show', compact('toaNha', 'statsByTrangThai', 'totalCuDan'));
    }

    public function edit(ToaNha $toaNha)
    {
        return view('admin.toa-nha.edit', compact('toaNha'));
    }

    public function update(Request $request, ToaNha $toaNha)
    {
        $request->validate([
            'ten_toa_nha' => 'required|string|max:255',
            'dia_chi'     => 'nullable|string|max:255',
            'so_tang'     => 'nullable|integer|min:1|max:200',
        ], [
            'ten_toa_nha.required' => 'Vui lòng nhập tên tòa nhà.',
        ]);

        $old = $toaNha->toArray();
        $toaNha->update([
            'ten_toa_nha' => $request->ten_toa_nha,
            'dia_chi'     => $request->dia_chi ?? '',
            'so_tang'     => $request->so_tang ?? 1,
        ]);
        AuditLogService::log('UPDATE', 'toa_nha', $toaNha->id, $old, $toaNha->fresh()->toArray());

        return redirect()->route('admin.toa-nha.show', $toaNha)
            ->with('success', "Cập nhật tòa nhà «{$toaNha->ten_toa_nha}» thành công.");
    }

    public function destroy(ToaNha $toaNha)
    {
        if ($toaNha->canHo()->exists()) {
            return back()->with('error', 'Không thể xóa tòa nhà đang có căn hộ.');
        }

        AuditLogService::log('DELETE', 'toa_nha', $toaNha->id, $toaNha->toArray(), null);
        $toaNha->delete();

        return redirect()->route('admin.toa-nha.index')
            ->with('success', "Đã xóa tòa nhà «{$toaNha->ten_toa_nha}».");
    }
}
