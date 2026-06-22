<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\YeuCauCuDan;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class YeuCauController extends Controller
{
    public function index(Request $request)
    {
        $query = YeuCauCuDan::with(['cuDan', 'nhanVienXuLy']);

        if ($request->filled('search')) {
            $query->where('tieu_de', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('muc_do')) {
            $query->where('muc_do_uu_tien', $request->muc_do);
        }

        $yeuCau = $query->orderByDesc('createdAt')->paginate(15)->withQueryString();
        return view('manager.yeu-cau.index', compact('yeuCau'));
    }

    public function show(YeuCauCuDan $yeuCau)
    {
        $yeuCau->load(['cuDan', 'nhanVienXuLy']);
        return view('manager.yeu-cau.show', compact('yeuCau'));
    }

    public function update(Request $request, YeuCauCuDan $yeuCau)
    {
        $request->validate([
            'trang_thai' => 'required|integer|in:1,2,3,4',
        ]);

        $old = $yeuCau->toArray();
        $data = ['trang_thai' => $request->trang_thai];

        if ($request->trang_thai == YeuCauCuDan::TRANG_THAI_DANG_XU_LY) {
            $data['nhan_vien_xu_ly'] = auth('nhanvien')->id();
        }

        if ($request->trang_thai == YeuCauCuDan::TRANG_THAI_HOAN_THANH) {
            $data['ngay_hoan_thanh'] = now();
        }

        $yeuCau->update($data);
        AuditLogService::log('UPDATE', 'yeu_cau_cu_dan', $yeuCau->id, $old, $yeuCau->fresh()->toArray());

        return back()->with('success', 'Cập nhật trạng thái yêu cầu thành công.');
    }

    public function destroy(YeuCauCuDan $yeuCau)
    {
        AuditLogService::log('DELETE', 'yeu_cau_cu_dan', $yeuCau->id, $yeuCau->toArray(), null);
        $yeuCau->delete();
        return redirect()->route('manager.yeu-cau.index')->with('success', 'Xóa yêu cầu thành công.');
    }
}
