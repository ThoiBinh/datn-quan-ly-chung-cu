<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CuDan;
use App\Models\ThongBao;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ThongBaoController extends Controller
{
    private const SORTABLE = ['createdAt', 'tieu_de'];

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = ThongBao::withTrashed()->with('nguoiTao');

        if ($request->filled('search')) {
            $query->where('tieu_de', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('trang_thai')) {
            if ($request->trang_thai === 'an') {
                $query->onlyTrashed();
            } elseif ($request->trang_thai === 'hien') {
                $query->whereNull('deletedAt');
            }
        }

        $thongBao = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('manager.thong-bao.index', compact('thongBao', 'sort', 'direction'));
    }

    public function create()
    {
        return view('manager.thong-bao.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tieu_de'  => 'required|string|max:255',
            'noi_dung' => 'required|string',
        ], [
            'tieu_de.required'  => 'Vui lòng nhập tiêu đề.',
            'noi_dung.required' => 'Vui lòng nhập nội dung.',
        ]);

        $tb = ThongBao::create([
            'tieu_de'   => $request->tieu_de,
            'noi_dung'  => $request->noi_dung,
            'nguoi_tao' => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('INSERT', 'thong_bao', $tb->id, null, $tb->toArray());
        return redirect()->route('manager.thong-bao.show', $tb)
            ->with('success', 'Đăng thông báo thành công.');
    }

    public function show(Request $request, ThongBao $thongBao)
    {
        $thongBao->load('nguoiTao');

        $tongCuDan   = CuDan::count();
        $tongDaDoc   = $thongBao->daDoc()->count();
        $tongChuaDoc = max(0, $tongCuDan - $tongDaDoc);
        $tyLe        = $tongCuDan > 0 ? round($tongDaDoc / $tongCuDan * 100, 1) : 0;

        $dsDaDoc = $thongBao->daDoc()
            ->with(['cuDan.canHoHienTai.canHo.toaNha', 'cuDan.canHoHienTai.vaiTro'])
            ->orderBy('read_at', 'desc')
            ->paginate(15, ['*'], 'da_doc_page')
            ->withQueryString();

        $dsChuaDoc = CuDan::whereNotIn('id', function ($q) use ($thongBao) {
                $q->select('cu_dan_id')->from('thong_bao_da_doc')->where('thong_bao_id', $thongBao->id);
            })
            ->with(['canHoHienTai.canHo.toaNha', 'canHoHienTai.vaiTro'])
            ->orderBy('ho_ten_dem')->orderBy('ten')
            ->paginate(15, ['*'], 'chua_doc_page')
            ->withQueryString();

        return view('manager.thong-bao.show', compact(
            'thongBao', 'tongCuDan', 'tongDaDoc', 'tongChuaDoc', 'tyLe', 'dsDaDoc', 'dsChuaDoc'
        ));
    }

    public function edit(ThongBao $thongBao)
    {
        return view('manager.thong-bao.edit', compact('thongBao'));
    }

    public function update(Request $request, ThongBao $thongBao)
    {
        $request->validate([
            'tieu_de'  => 'required|string|max:255',
            'noi_dung' => 'required|string',
        ]);

        $old = $thongBao->toArray();
        $thongBao->update($request->only('tieu_de', 'noi_dung'));
        AuditLogService::log('UPDATE', 'thong_bao', $thongBao->id, $old, $thongBao->fresh()->toArray());

        return redirect()->route('manager.thong-bao.show', $thongBao)
            ->with('success', 'Cập nhật thông báo thành công.');
    }

    public function toggleHide(ThongBao $thongBao)
    {
        $old = $thongBao->toArray();
        $thongBao->delete();
        AuditLogService::log('UPDATE', 'thong_bao', $thongBao->id, $old, array_merge($old, ['deletedAt' => now()]));
        return back()->with('success', "Đã ẩn thông báo «{$thongBao->tieu_de}».");
    }

    public function restore(int $id)
    {
        $thongBao = ThongBao::withTrashed()->findOrFail($id);
        $old = $thongBao->toArray();
        $thongBao->restore();
        AuditLogService::log('UPDATE', 'thong_bao', $thongBao->id, $old, $thongBao->fresh()->toArray());
        return back()->with('success', "Đã khôi phục thông báo «{$thongBao->tieu_de}».");
    }

    public function destroy(ThongBao $thongBao)
    {
        $old = $thongBao->toArray();
        $thongBao->delete();
        AuditLogService::log('UPDATE', 'thong_bao', $thongBao->id, $old, array_merge($old, ['deletedAt' => now()]));
        return redirect()->route('manager.thong-bao.index')->with('success', 'Đã ẩn thông báo thành công.');
    }
}
