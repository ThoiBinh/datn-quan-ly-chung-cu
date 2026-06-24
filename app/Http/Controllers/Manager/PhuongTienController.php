<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\LoaiPhuongTien;
use App\Models\PhuongTien;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class PhuongTienController extends Controller
{
    public function index(Request $request)
    {
        $query = PhuongTien::with(['loaiPhuongTien', 'canHo.toaNha']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('bien_so', 'like', '%' . $request->search . '%')
                  ->orWhere('ten_phuong_tien', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('loai')) {
            $query->where('loai_phuong_tien', $request->loai);
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $phuongTien = $query->orderByDesc('ngay_dang_ky')->paginate(15)->withQueryString();
        $loaiPhuongTien = LoaiPhuongTien::all();
        return view('manager.phuong-tien.index', compact('phuongTien', 'loaiPhuongTien'));
    }

    public function create()
    {
        $canHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $loaiPhuongTien = LoaiPhuongTien::all();
        return view('manager.phuong-tien.create', compact('canHo', 'loaiPhuongTien'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_phuong_tien'  => 'nullable|string|max:255',
            'bien_so'          => 'required|string|max:50|unique:phuong_tien,bien_so',
            'loai_phuong_tien' => 'required|exists:loai_phuong_tien,id',
            'can_ho'           => 'required|exists:can_ho,id',
            'ngay_dang_ky'     => 'nullable|date',
        ], [
            'bien_so.required'          => 'Vui lòng nhập biển số xe.',
            'bien_so.unique'            => 'Biển số xe đã tồn tại.',
            'loai_phuong_tien.required' => 'Vui lòng chọn loại phương tiện.',
            'loai_phuong_tien.exists'   => 'Loại phương tiện không hợp lệ.',
            'can_ho.required'           => 'Vui lòng chọn căn hộ.',
            'can_ho.exists'             => 'Căn hộ không hợp lệ.',
        ]);

        $pt = PhuongTien::create(array_merge(
            $request->only('ten_phuong_tien', 'bien_so', 'loai_phuong_tien', 'can_ho', 'ngay_dang_ky'),
            ['trang_thai' => 1]
        ));

        AuditLogService::log('INSERT', 'phuong_tien', $pt->id, null, $pt->toArray());
        return redirect()->route('manager.phuong-tien.index')->with('success', 'Thêm phương tiện thành công.');
    }

    public function edit(PhuongTien $phuongTien)
    {
        $canHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $loaiPhuongTien = LoaiPhuongTien::all();
        return view('manager.phuong-tien.edit', compact('phuongTien', 'canHo', 'loaiPhuongTien'));
    }

    public function update(Request $request, PhuongTien $phuongTien)
{
    $request->validate([
        'bien_so'          => 'required|string|max:50|unique:phuong_tien,bien_so,' . $phuongTien->id,
        'loai_phuong_tien' => 'required|exists:loai_phuong_tien,id',
        'can_ho'           => 'required|exists:can_ho,id',
        'trang_thai'       => 'required|in:0,1',
    ], [
        'bien_so.required'          => 'Vui lòng nhập biển số xe.',
        'bien_so.unique'            => 'Biển số xe đã tồn tại.',
        'loai_phuong_tien.required' => 'Vui lòng chọn loại phương tiện.',
        'loai_phuong_tien.exists'   => 'Loại phương tiện không hợp lệ.',
        'can_ho.required'           => 'Vui lòng chọn căn hộ.',
        'can_ho.exists'             => 'Căn hộ không hợp lệ.',
    ]);

    $old = $phuongTien->toArray();

    $data = $request->only([
        'ten_phuong_tien',
        'loai_phuong_tien',
        'can_ho',
        'ngay_dang_ky',
        'trang_thai',
    ]);

    $data['bien_so'] = strtoupper(trim($request->bien_so));
    $data['nguoi_cap_nhat'] = auth('nhanvien')->id();

    $phuongTien->update($data);

    AuditLogService::log(
        'UPDATE',
        'phuong_tien',
        $phuongTien->id,
        $old,
        $phuongTien->fresh()->toArray()
    );

    return redirect()
        ->route('manager.phuong-tien.index')
        ->with('success', 'Cập nhật phương tiện thành công.');
}

    public function destroy(PhuongTien $phuongTien)
    {
        AuditLogService::log('DELETE', 'phuong_tien', $phuongTien->id, $phuongTien->toArray(), null);
        $phuongTien->delete();
        return redirect()->route('manager.phuong-tien.index')->with('success', 'Xóa phương tiện thành công.');
    }
}
