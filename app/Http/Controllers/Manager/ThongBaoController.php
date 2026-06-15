<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ThongBaoController extends Controller
{
    public function index(Request $request)
    {
        $query = ThongBao::with('nguoiTao');

        if ($request->filled('search')) {
            $query->where('tieu_de', 'like', '%' . $request->search . '%');
        }

        $thongBao = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('manager.thong-bao.index', compact('thongBao'));
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
            'tieu_de'  => $request->tieu_de,
            'noi_dung' => $request->noi_dung,
            'nguoi_tao' => auth()->id(),
        ]);

        AuditLogService::log('INSERT', 'thong_bao', $tb->id, null, $tb->toArray());
        return redirect()->route('manager.thong-bao.index')->with('success', 'Đăng thông báo thành công.');
    }

    public function show(ThongBao $thongBao)
    {
        return view('manager.thong-bao.show', compact('thongBao'));
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

        return redirect()->route('manager.thong-bao.index')->with('success', 'Cập nhật thông báo thành công.');
    }

    public function destroy(ThongBao $thongBao)
    {
        AuditLogService::log('DELETE', 'thong_bao', $thongBao->id, $thongBao->toArray(), null);
        $thongBao->delete();
        return redirect()->route('manager.thong-bao.index')->with('success', 'Xóa thông báo thành công.');
    }
}
