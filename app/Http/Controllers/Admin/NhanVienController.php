<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChucVu;
use App\Models\NhanVien;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class NhanVienController extends Controller
{
    private const SORTABLE = ['ho_ten', 'ma_nhan_vien', 'email', 'ngay_vao_lam', 'createdAt', 'trang_thai'];

    public function index(Request $request)
    {
        $query = NhanVien::with('chucVu');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ho_ten',       'like', "%$s%")
                  ->orWhere('email',      'like', "%$s%")
                  ->orWhere('sdt',        'like', "%$s%")
                  ->orWhere('ma_nhan_vien', 'like', "%$s%")
                  ->orWhere('cccd',       'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status === 'active' ? 1 : 0);
        }

        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $nhanVien = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        return view('admin.nhan-vien.index', compact('nhanVien', 'sort', 'direction'));
    }

    public function create()
    {
        $chucVu = ChucVu::all();
        return view('admin.nhan-vien.create', compact('chucVu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ho_ten'        => 'required|string|max:255',
            'chuc_vu'       => 'required|exists:chuc_vu,id',
            'sdt'           => 'nullable|string|max:20',
            'email'         => 'required|email|unique:nhan_vien,email',
            'mat_khau'      => 'required|min:8|confirmed',
            'ma_nhan_vien'  => 'nullable|string|max:50|unique:nhan_vien,ma_nhan_vien',
            'cccd'          => 'required|string|max:20|unique:nhan_vien,cccd',
            'ngay_sinh'     => 'nullable|date',
            'ngay_vao_lam'  => 'nullable|date',
            'ngay_nghi_lam' => 'nullable|date',
            'ghi_chu'       => 'nullable|string|max:1000',
            'trang_thai'    => 'required|in:0,1',
        ], [
            'ho_ten.required'    => 'Vui lòng nhập họ tên.',
            'chuc_vu.required'   => 'Vui lòng chọn chức vụ.',
            'chuc_vu.exists'     => 'Chức vụ không hợp lệ.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.email'        => 'Email không đúng định dạng.',
            'email.unique'       => 'Email đã tồn tại trong hệ thống.',
            'mat_khau.required'  => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'ma_nhan_vien.unique' => 'Mã nhân viên đã tồn tại.',
            'cccd.required'      => 'Vui lòng nhập CCCD.',
            'cccd.unique'        => 'CCCD đã được sử dụng.',
        ]);

        $nextId = (NhanVien::max('id') ?? 0) + 1;

        $nv = NhanVien::create([
            'ho_ten'          => $request->ho_ten,
            'chuc_vu'         => $request->chuc_vu,
            'sdt'             => $request->sdt,
            'email'           => $request->email,
            'mat_khau'        => Hash::make($request->mat_khau),
            'ma_nhan_vien'    => $request->ma_nhan_vien ?: 'NV' . str_pad($nextId, 4, '0', STR_PAD_LEFT),
            'cccd'            => $request->cccd,
            'ngay_sinh'       => $request->ngay_sinh     ?: null,
            'ngay_vao_lam'    => $request->ngay_vao_lam  ?: null,
            'ngay_nghi_lam'   => $request->ngay_nghi_lam ?: null,
            'ghi_chu'         => $request->ghi_chu,
            'trang_thai'      => (int) $request->trang_thai,
            'nguoi_cap_nhat'  => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('INSERT', 'nhan_vien', $nv->id, null, $nv->toArray());

        return redirect()->route('admin.nhan-vien.index')
            ->with('success', "Thêm nhân viên «{$nv->ho_ten}» thành công.");
    }

    public function show(NhanVien $nhanVien)
    {
        $nhanVien->load('chucVu');
        return view('admin.nhan-vien.show', compact('nhanVien'));
    }

    public function edit(NhanVien $nhanVien)
    {
        $nhanVien->load('chucVu');
        $chucVu = ChucVu::all();
        return view('admin.nhan-vien.edit', compact('nhanVien', 'chucVu'));
    }

    public function update(Request $request, NhanVien $nhanVien)
    {
        $request->validate([
            'ho_ten'        => 'required|string|max:255',
            'chuc_vu'       => 'required|exists:chuc_vu,id',
            'sdt'           => 'nullable|string|max:20',
            'email'         => 'required|email|unique:nhan_vien,email,' . $nhanVien->id,
            'ma_nhan_vien'  => 'nullable|string|max:50|unique:nhan_vien,ma_nhan_vien,' . $nhanVien->id,
            'cccd'          => 'required|string|max:20|unique:nhan_vien,cccd,' . $nhanVien->id,
            'ngay_sinh'     => 'nullable|date',
            'ngay_vao_lam'  => 'nullable|date',
            'ngay_nghi_lam' => 'nullable|date',
            'ghi_chu'       => 'nullable|string|max:1000',
            'trang_thai'    => 'required|in:0,1',
        ], [
            'ho_ten.required'    => 'Vui lòng nhập họ tên.',
            'chuc_vu.required'   => 'Vui lòng chọn chức vụ.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.unique'       => 'Email đã được sử dụng.',
            'ma_nhan_vien.unique' => 'Mã nhân viên đã tồn tại.',
            'cccd.required'      => 'Vui lòng nhập CCCD.',
            'cccd.unique'        => 'CCCD đã được sử dụng.',
        ]);

        $old = $nhanVien->toArray();

        $data = [
            'ho_ten'         => $request->ho_ten,
            'chuc_vu'        => $request->chuc_vu,
            'sdt'            => $request->sdt,
            'email'          => $request->email,
            'ma_nhan_vien'   => $request->ma_nhan_vien ?: $nhanVien->ma_nhan_vien,
            'cccd'           => $request->cccd,
            'ngay_sinh'      => $request->ngay_sinh     ?: null,
            'ngay_vao_lam'   => $request->ngay_vao_lam  ?: null,
            'ngay_nghi_lam'  => $request->ngay_nghi_lam ?: null,
            'ghi_chu'        => $request->ghi_chu,
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

        $nhanVien->update($data);
        AuditLogService::log('UPDATE', 'nhan_vien', $nhanVien->id, $old, $nhanVien->fresh()->toArray());

        return redirect()->route('admin.nhan-vien.show', $nhanVien)
            ->with('success', 'Cập nhật nhân viên thành công.');
    }

    public function toggleStatus(NhanVien $nhanVien)
    {
        if ($nhanVien->id === auth('nhanvien')->id()) {
            return back()->with('error', 'Không thể thay đổi trạng thái tài khoản của chính mình.');
        }

        $old       = ['trang_thai' => $nhanVien->trang_thai];
        $newStatus = $nhanVien->trang_thai == 1 ? 0 : 1;
        $nhanVien->update(['trang_thai' => $newStatus]);

        AuditLogService::log('UPDATE', 'nhan_vien', $nhanVien->id, $old, ['trang_thai' => $newStatus]);

        $msg = $newStatus === 1 ? "Đã mở khóa tài khoản «{$nhanVien->ho_ten}»." : "Đã khóa tài khoản «{$nhanVien->ho_ten}».";
        return back()->with('success', $msg);
    }
}
