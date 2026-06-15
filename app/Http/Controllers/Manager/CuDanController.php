<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\CuDanCanHo;
use App\Models\VaiTro;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CuDanController extends Controller
{
    public function index(Request $request)
    {
        $query = CuDan::with('canHoHienTai.canHo.toaNha');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ho_ten', 'like', '%' . $request->search . '%')
                  ->orWhere('sdt', 'like', '%' . $request->search . '%')
                  ->orWhere('cccd', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $cuDan = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('manager.cu-dan.index', compact('cuDan'));
    }

    public function create()
    {
        $canHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $vaiTro = VaiTro::all();
        return view('manager.cu-dan.create', compact('canHo', 'vaiTro'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ho_ten'   => 'required|string|max:255',
            'sdt'      => 'nullable|string|max:20',
            'cccd'     => 'nullable|string|max:50|unique:cu_dan,cccd',
            'email'    => 'nullable|email|max:255',
            'nam_sinh' => 'nullable|date',
            'que_quan' => 'nullable|string|max:255',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'cccd.unique'     => 'CCCD đã tồn tại trong hệ thống.',
        ]);

        $cuDan = CuDan::create($request->only('ho_ten', 'sdt', 'cccd', 'email', 'nam_sinh', 'que_quan'));

        if ($request->filled('can_ho') && $request->filled('vai_tro')) {
            CuDanCanHo::create([
                'cu_dan'        => $cuDan->id,
                'can_ho'        => $request->can_ho,
                'vai_tro'       => $request->vai_tro,
                'ngay_chuyen_den' => $request->ngay_chuyen_den ?? now(),
                'trang_thai'    => 1,
            ]);
        }

        AuditLogService::log('INSERT', 'cu_dan', $cuDan->id, null, $cuDan->toArray());
        return redirect()->route('manager.cu-dan.index')->with('success', 'Thêm cư dân thành công.');
    }

    public function show(CuDan $cuDan)
    {
        $cuDan->load(['canHoHienTai.canHo.toaNha', 'canHoHienTai.vaiTro', 'yeuCau', 'hopDong']);
        return view('manager.cu-dan.show', compact('cuDan'));
    }

    public function edit(CuDan $cuDan)
    {
        $cuDan->load('canHoHienTai');
        $canHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $vaiTro = VaiTro::all();
        return view('manager.cu-dan.edit', compact('cuDan', 'canHo', 'vaiTro'));
    }

    public function update(Request $request, CuDan $cuDan)
    {
        $request->validate([
            'ho_ten'   => 'required|string|max:255',
            'sdt'      => 'nullable|string|max:20',
            'cccd'     => 'nullable|string|max:50|unique:cu_dan,cccd,' . $cuDan->id,
            'email'    => 'nullable|email|max:255',
            'nam_sinh' => 'nullable|date',
            'que_quan' => 'nullable|string|max:255',
        ]);

        $old = $cuDan->toArray();
        $cuDan->update($request->only('ho_ten', 'sdt', 'cccd', 'email', 'nam_sinh', 'que_quan'));
        AuditLogService::log('UPDATE', 'cu_dan', $cuDan->id, $old, $cuDan->fresh()->toArray());

        $canHoMoi     = $request->can_ho;
        $canHoHienTai = $cuDan->canHoHienTai;

        if ($canHoMoi) {
            if ($canHoHienTai) {
                if ($canHoHienTai->can_ho != $canHoMoi) {
                    // Chuyển sang căn hộ khác: đóng bản ghi cũ, tạo mới
                    $canHoHienTai->update(['trang_thai' => 0, 'ngay_chuyen_di' => now()]);
                    CuDanCanHo::create([
                        'cu_dan'          => $cuDan->id,
                        'can_ho'          => $canHoMoi,
                        'vai_tro'         => $request->vai_tro,
                        'ngay_chuyen_den' => $request->ngay_chuyen_den ?? now(),
                        'trang_thai'      => 1,
                    ]);
                } else {
                    // Cùng căn hộ: chỉ cập nhật vai trò
                    $canHoHienTai->update(['vai_tro' => $request->vai_tro]);
                }
            } else {
                // Chưa có căn hộ: tạo mới
                CuDanCanHo::create([
                    'cu_dan'          => $cuDan->id,
                    'can_ho'          => $canHoMoi,
                    'vai_tro'         => $request->vai_tro,
                    'ngay_chuyen_den' => $request->ngay_chuyen_den ?? now(),
                    'trang_thai'      => 1,
                ]);
            }
        } else {
            // Không chọn căn hộ: hủy phân công hiện tại nếu có
            if ($canHoHienTai) {
                $canHoHienTai->update(['trang_thai' => 0, 'ngay_chuyen_di' => now()]);
            }
        }

        return redirect()->route('manager.cu-dan.index')->with('success', 'Cập nhật cư dân thành công.');
    }

    public function destroy(CuDan $cuDan)
    {
        AuditLogService::log('DELETE', 'cu_dan', $cuDan->id, $cuDan->toArray(), null);
        $cuDan->delete();
        return redirect()->route('manager.cu-dan.index')->with('success', 'Xóa cư dân thành công.');
    }
}
