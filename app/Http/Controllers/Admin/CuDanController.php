<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuDan;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CuDanController extends Controller
{
    private const SORTABLE = ['ho_ten_dem', 'ten', 'email', 'ngay_sinh', 'createdAt', 'trang_thai'];

    public function index(Request $request)
    {
        $query = CuDan::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ho_ten_dem', 'like', "%$s%")
                  ->orWhere('ten',      'like', "%$s%")
                  ->orWhere('email',    'like', "%$s%")
                  ->orWhere('sdt',      'like', "%$s%")
                  ->orWhere('cccd',     'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status === 'active' ? 1 : 0);
        }

        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $cuDan = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('admin.cu-dan.index', compact('cuDan', 'sort', 'direction'));
    }

    public function create()
    {
        return view('admin.cu-dan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ho_ten_dem' => 'required|string|max:255',
            'ten'        => 'required|string|max:100',
            'sdt'        => 'nullable|string|max:20',
            'cccd'       => 'required|string|max:50|unique:cu_dan,cccd',
            'email'      => 'nullable|email|unique:cu_dan,email',
            'mat_khau'   => 'required|min:8|confirmed',
            'ngay_sinh'  => 'nullable|date',
            'gioi_tinh'  => 'nullable|in:0,1',
            'tinh'       => 'nullable|string|max:100',
            'xa'         => 'nullable|string|max:100',
            'dia_chi'    => 'nullable|string|max:255',
            'trang_thai' => 'required|in:0,1',
        ], [
            'ho_ten_dem.required' => 'Vui lòng nhập họ tên đệm.',
            'ten.required'        => 'Vui lòng nhập tên.',
            'email.email'         => 'Email không đúng định dạng.',
            'email.unique'        => 'Email đã tồn tại trong hệ thống.',
            'cccd.required'       => 'Vui lòng nhập CCCD.',
            'cccd.unique'         => 'CCCD đã tồn tại trong hệ thống.',
            'mat_khau.required'   => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min'        => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed'  => 'Xác nhận mật khẩu không khớp.',
        ]);

        $cd = CuDan::create([
            'ho_ten_dem'     => $request->ho_ten_dem,
            'ten'            => $request->ten,
            'sdt'            => $request->sdt,
            'cccd'           => $request->cccd,
            'email'          => $request->email,
            'mat_khau'       => Hash::make($request->mat_khau),
            'ngay_sinh'      => $request->ngay_sinh  ?: null,
            'gioi_tinh'      => $request->gioi_tinh !== null && $request->gioi_tinh !== '' ? (int) $request->gioi_tinh : null,
            'tinh'           => $request->tinh,
            'xa'             => $request->xa,
            'dia_chi'        => $request->dia_chi,
            'trang_thai'     => (int) $request->trang_thai,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('INSERT', 'cu_dan', $cd->id, null, $cd->toArray());

        return redirect()->route('admin.cu-dan.index')
            ->with('success', "Thêm cư dân «{$cd->ho_ten}» thành công.");
    }

    public function show(CuDan $cuDan)
    {
        return view('admin.cu-dan.show', compact('cuDan'));
    }

    public function edit(CuDan $cuDan)
    {
        return view('admin.cu-dan.edit', compact('cuDan'));
    }

    public function update(Request $request, CuDan $cuDan)
    {
        $request->validate([
            'ho_ten_dem' => 'required|string|max:255',
            'ten'        => 'required|string|max:100',
            'sdt'        => 'nullable|string|max:20',
            'cccd'       => 'required|string|max:50|unique:cu_dan,cccd,' . $cuDan->id,
            'email'      => 'nullable|email|unique:cu_dan,email,' . $cuDan->id,
            'ngay_sinh'  => 'nullable|date',
            'gioi_tinh'  => 'nullable|in:0,1',
            'tinh'       => 'nullable|string|max:100',
            'xa'         => 'nullable|string|max:100',
            'dia_chi'    => 'nullable|string|max:255',
            'trang_thai' => 'required|in:0,1',
        ], [
            'ho_ten_dem.required' => 'Vui lòng nhập họ tên đệm.',
            'ten.required'        => 'Vui lòng nhập tên.',
            'email.email'         => 'Email không đúng định dạng.',
            'email.unique'        => 'Email đã được sử dụng.',
            'cccd.required'       => 'Vui lòng nhập CCCD.',
            'cccd.unique'         => 'CCCD đã được sử dụng.',
        ]);

        $old = $cuDan->toArray();

        $data = [
            'ho_ten_dem'     => $request->ho_ten_dem,
            'ten'            => $request->ten,
            'sdt'            => $request->sdt,
            'cccd'           => $request->cccd,
            'email'          => $request->email,
            'ngay_sinh'      => $request->ngay_sinh  ?: null,
            'gioi_tinh'      => $request->gioi_tinh !== null && $request->gioi_tinh !== '' ? (int) $request->gioi_tinh : null,
            'tinh'           => $request->tinh,
            'xa'             => $request->xa,
            'dia_chi'        => $request->dia_chi,
            'trang_thai'     => (int) $request->trang_thai,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ];

        if ($request->filled('mat_khau')) {
            $request->validate([
                'mat_khau' => 'min:8|confirmed',
            ], [
                'mat_khau.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
                'mat_khau.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ]);
            $data['mat_khau'] = Hash::make($request->mat_khau);
        }

        $cuDan->update($data);
        AuditLogService::log('UPDATE', 'cu_dan', $cuDan->id, $old, $cuDan->fresh()->toArray());

        return redirect()->route('admin.cu-dan.show', $cuDan)
            ->with('success', 'Cập nhật cư dân thành công.');
    }

    public function toggleStatus(CuDan $cuDan)
    {
        $old       = ['trang_thai' => $cuDan->trang_thai];
        $newStatus = $cuDan->trang_thai == 1 ? 0 : 1;
        $cuDan->update(['trang_thai' => $newStatus]);

        AuditLogService::log('UPDATE', 'cu_dan', $cuDan->id, $old, ['trang_thai' => $newStatus]);

        $msg = $newStatus === 1 ? "Đã mở khóa tài khoản «{$cuDan->ho_ten}»." : "Đã khóa tài khoản «{$cuDan->ho_ten}».";
        return back()->with('success', $msg);
    }
}
