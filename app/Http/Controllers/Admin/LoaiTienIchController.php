<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLoaiTienIchRequest;
use App\Http\Requests\Admin\UpdateLoaiTienIchRequest;
use App\Models\LoaiTienIch;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoaiTienIchController extends Controller
{
    private const SORTABLE = ['ten_loai_tien_ich', 'tien_ich_count'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'ten_loai_tien_ich';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';

        $tongTatCa    = LoaiTienIch::count();
        $tongDangDung = LoaiTienIch::whereHas('tienIch')->count();

        $query = LoaiTienIch::withCount('tienIch');

        if ($request->filled('search')) {
            $query->where('ten_loai_tien_ich', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('su_dung')) {
            if ($request->su_dung === 'co') {
                $query->whereHas('tienIch');
            } elseif ($request->su_dung === 'khong') {
                $query->whereDoesntHave('tienIch');
            }
        }

        $dsLoai = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('admin.loai-tien-ich.index', compact(
            'dsLoai', 'sort', 'direction', 'tongTatCa', 'tongDangDung'
        ));
    }

    public function create()
    {
        return view('admin.loai-tien-ich.create');
    }

    public function store(StoreLoaiTienIchRequest $request)
    {
        $loaiTienIch = DB::transaction(function () use ($request) {
            $loaiTienIch = LoaiTienIch::create($request->validated());
            AuditLogService::log('INSERT', 'loai_tien_ich', $loaiTienIch->id, null, $loaiTienIch->toArray());

            return $loaiTienIch;
        });

        return redirect()->route('admin.loai-tien-ich.show', $loaiTienIch)
            ->with('success', "Thêm loại tiện ích «{$loaiTienIch->ten_loai_tien_ich}» thành công.");
    }

    public function show(LoaiTienIch $loaiTienIch)
    {
        $loaiTienIch->loadCount(['tienIch', 'datLich']);
        $loaiTienIch->load(['tienIch' => function ($q) {
            $q->withCount('datLich')->orderBy('ten_tien_ich');
        }]);

        return view('admin.loai-tien-ich.show', compact('loaiTienIch'));
    }

    public function edit(LoaiTienIch $loaiTienIch)
    {
        return view('admin.loai-tien-ich.edit', compact('loaiTienIch'));
    }

    public function update(UpdateLoaiTienIchRequest $request, LoaiTienIch $loaiTienIch)
    {
        $old = $loaiTienIch->toArray();

        DB::transaction(function () use ($loaiTienIch, $request, $old) {
            $loaiTienIch->update($request->validated());
            AuditLogService::log('UPDATE', 'loai_tien_ich', $loaiTienIch->id, $old, $loaiTienIch->fresh()->toArray());
        });

        return redirect()->route('admin.loai-tien-ich.show', $loaiTienIch)
            ->with('success', "Cập nhật loại tiện ích «{$loaiTienIch->ten_loai_tien_ich}» thành công.");
    }

    public function destroy(LoaiTienIch $loaiTienIch)
    {
        if ($loaiTienIch->tienIch()->exists()) {
            $count = $loaiTienIch->tienIch()->count();
            return back()->with('error', "Không thể xóa vì loại tiện ích đang được sử dụng bởi {$count} tiện ích.");
        }

        DB::transaction(function () use ($loaiTienIch) {
            AuditLogService::log('DELETE', 'loai_tien_ich', $loaiTienIch->id, $loaiTienIch->toArray(), null);
            $loaiTienIch->delete();
        });

        return redirect()->route('admin.loai-tien-ich.index')
            ->with('success', "Đã xóa loại tiện ích «{$loaiTienIch->ten_loai_tien_ich}».");
    }
}
