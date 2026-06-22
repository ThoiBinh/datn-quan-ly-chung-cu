<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\CuDanCanHo;
use App\Models\VaiTro;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CuDanController extends Controller
{
    public function index(Request $request)
    {
        $query = CuDan::with('canHoHienTai.canHo.toaNha');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ho_ten_dem', 'like', '%' . $request->search . '%')
                  ->orWhere('ten', 'like', '%' . $request->search . '%')
                  ->orWhere('sdt', 'like', '%' . $request->search . '%')
                  ->orWhere('cccd', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $cuDan = $query->orderByDesc('createdAt')->paginate(15)->withQueryString();
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
            'ho_ten_dem' => 'nullable|string|max:255',
            'ten'        => 'required|string|max:100',
            'sdt'        => 'nullable|string|max:20',
            'cccd'       => 'nullable|string|max:50|unique:cu_dan,cccd',
            'email'      => 'nullable|email|max:255|unique:cu_dan,email',
            'ngay_sinh'  => 'nullable|date',
            'gioi_tinh'  => 'nullable|in:0,1',
            'tinh'       => 'nullable|string|max:100',
            'dia_chi'    => 'nullable|string|max:500',
            'mat_khau'   => 'nullable|string|min:6',
        ], [
            'ten.required'   => 'Vui lòng nhập tên.',
            'cccd.unique'    => 'CCCD đã tồn tại trong hệ thống.',
            'email.unique'   => 'Email đã tồn tại trong hệ thống.',
        ]);

        $matKhau = $request->filled('mat_khau')
            ? Hash::make($request->mat_khau)
            : Hash::make('12345678');

        $data = $request->only('ho_ten_dem', 'ten', 'sdt', 'cccd', 'email', 'ngay_sinh', 'tinh', 'dia_chi');
        $gt   = $request->input('gioi_tinh');
        $data['gioi_tinh'] = ($gt !== null && $gt !== '') ? (int) $gt : null;

        $cuDan = CuDan::create(array_merge($data, ['mat_khau' => $matKhau, 'trang_thai' => 1]));

        if ($request->filled('can_ho') && $request->filled('vai_tro')) {
            CuDanCanHo::create([
                'cu_dan'          => $cuDan->id,
                'can_ho'          => $request->can_ho,
                'vai_tro'         => $request->vai_tro,
                'ngay_chuyen_den' => $request->ngay_chuyen_den ?? now(),
                'trang_thai'      => 1,
            ]);
        }

        AuditLogService::log('INSERT', 'cu_dan', $cuDan->id, null, $cuDan->toArray());
        return redirect()->route('manager.cu-dan.index')->with('success', 'Thêm cư dân thành công.');
    }

    public function show(CuDan $cuDan)
    {
        $cuDan->load(['canHoHienTai.canHo.toaNha', 'canHoHienTai.vaiTro', 'yeuCau']);
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
            'ho_ten_dem' => 'nullable|string|max:255',
            'ten'        => 'required|string|max:100',
            'sdt'        => 'nullable|string|max:20',
            'cccd'       => 'nullable|string|max:50|unique:cu_dan,cccd,' . $cuDan->id,
            'email'      => 'nullable|email|max:255|unique:cu_dan,email,' . $cuDan->id,
            'ngay_sinh'  => 'nullable|date',
            'gioi_tinh'  => 'nullable|in:0,1',
            'tinh'       => 'nullable|string|max:100',
            'dia_chi'    => 'nullable|string|max:500',
        ], [
            'ten.required' => 'Vui lòng nhập tên.',
        ]);

        $old  = $cuDan->toArray();
        $data = $request->only('ho_ten_dem', 'ten', 'sdt', 'cccd', 'email', 'ngay_sinh', 'tinh', 'dia_chi');
        if ($request->input('gioi_tinh') !== null && $request->input('gioi_tinh') !== '') {
            $data['gioi_tinh'] = (int) $request->input('gioi_tinh');
        } else {
            $data['gioi_tinh'] = null;
        }
        $cuDan->update($data);
        AuditLogService::log('UPDATE', 'cu_dan', $cuDan->id, $old, $cuDan->fresh()->toArray());

        $canHoMoi     = $request->can_ho;
        $canHoHienTai = $cuDan->canHoHienTai;

        if ($canHoMoi) {
            if ($canHoHienTai) {
                if ($canHoHienTai->can_ho != $canHoMoi) {
                    $canHoHienTai->update(['trang_thai' => 0, 'ngay_chuyen_di' => now()]);
                    CuDanCanHo::create([
                        'cu_dan'          => $cuDan->id,
                        'can_ho'          => $canHoMoi,
                        'vai_tro'         => $request->vai_tro,
                        'ngay_chuyen_den' => $request->ngay_chuyen_den ?? now(),
                        'trang_thai'      => 1,
                    ]);
                } else {
                    $canHoHienTai->update(['vai_tro' => $request->vai_tro]);
                }
            } else {
                CuDanCanHo::create([
                    'cu_dan'          => $cuDan->id,
                    'can_ho'          => $canHoMoi,
                    'vai_tro'         => $request->vai_tro,
                    'ngay_chuyen_den' => $request->ngay_chuyen_den ?? now(),
                    'trang_thai'      => 1,
                ]);
            }
        } else {
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
