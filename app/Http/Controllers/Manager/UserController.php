<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ChucVu;
use App\Models\NhanVien;
use App\Services\AuditLogService;
use App\Services\NhanVienTrangThaiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private function adminChucVuId(): ?int
    {
        return ChucVu::where('chuc_vu', 'Admin')->value('id');
    }

    public function index(Request $request)
    {
        NhanVienTrangThaiService::syncExpired();

        $adminId = $this->adminChucVuId();
        $query = NhanVien::with('chucVu');

        if ($adminId) {
            $query->where('chuc_vu', '!=', $adminId);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ho_ten', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('sdt', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status === 'active' ? 1 : 0);
        }

        $users = $query->orderByDesc('createdAt')->paginate(15)->withQueryString();

        return view('manager.users.index', compact('users'));
    }

    public function create()
    {
        $adminId = $this->adminChucVuId();
        $chucVu  = ChucVu::when($adminId, fn($q) => $q->where('id', '!=', $adminId))->get();
        return view('manager.users.create', compact('chucVu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required', 'email',
                Rule::unique('nhan_vien', 'email')->whereNull('deletedAt'),
            ],
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
            'status'   => 'required|in:active,inactive',
        ], [
            'name.required'      => 'Vui lòng nhập họ tên.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.unique'       => 'Email đã tồn tại.',
            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $adminId  = $this->adminChucVuId();
        $chucVuId = ChucVu::when($adminId, fn($q) => $q->where('id', '!=', $adminId))->value('id') ?? 1;
        $nextId   = (NhanVien::max('id') ?? 0) + 1;

        $nv = NhanVien::create([
            'ho_ten'       => $request->name,
            'email'        => $request->email,
            'sdt'          => $request->phone,
            'mat_khau'     => Hash::make($request->password),
            'chuc_vu'      => $request->input('chuc_vu') ?: $chucVuId,
            'trang_thai'   => $request->status === 'active' ? 1 : 0,
            'ma_nhan_vien' => $request->input('ma_nhan_vien') ?: 'NV-' . strtoupper(Str::uuid()),
            'cccd'         => $request->input('cccd') ?: '',
            'nguoi_cap_nhat'     => auth('nhanvien')->id(),
        ]);

        AuditLogService::log('INSERT', 'nhan_vien', $nv->id, null, $nv->toArray());

        return redirect()->route('manager.users.index')->with('success', 'Tạo nhân viên thành công.');
    }

    public function show(NhanVien $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Không có quyền xem tài khoản Admin.');
        }
        NhanVienTrangThaiService::syncOne($user);
        $user->load('chucVu');
        return view('manager.users.show', compact('user'));
    }

    public function edit(NhanVien $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Không có quyền chỉnh sửa tài khoản Admin.');
        }
        NhanVienTrangThaiService::syncOne($user);
        $adminId = $this->adminChucVuId();
        $chucVu  = ChucVu::when($adminId, fn($q) => $q->where('id', '!=', $adminId))->get();
        return view('manager.users.edit', compact('user', 'chucVu'));
    }

    public function update(Request $request, NhanVien $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Không có quyền chỉnh sửa tài khoản Admin.');
        }

        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => [
                'required', 'email',
                Rule::unique('nhan_vien', 'email')->whereNull('deletedAt')->ignore($user->id),
            ],
            'phone'  => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $old  = $user->toArray();
        $data = [
            'ho_ten'     => $request->name,
            'email'      => $request->email,
            'sdt'        => $request->phone,
            'trang_thai' => $request->status === 'active' ? 1 : 0,
            'nguoi_cap_nhat'     => auth('nhanvien')->id(),
        ];

        if ($request->filled('chuc_vu')) {
            $data['chuc_vu'] = $request->chuc_vu;
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['mat_khau'] = Hash::make($request->password);
        }

        $user->update($data);
        AuditLogService::log('UPDATE', 'nhan_vien', $user->id, $old, $user->fresh()->toArray());

        return redirect()->route('manager.users.index')->with('success', 'Cập nhật nhân viên thành công.');
    }

    public function destroy(NhanVien $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Không có quyền xóa tài khoản Admin.');
        }

        if ($user->id === auth('nhanvien')->id()) {
            return back()->with('error', 'Không thể xóa tài khoản của chính mình.');
        }

        AuditLogService::log('DELETE', 'nhan_vien', $user->id, $user->toArray(), null);
        $user->delete();

        return redirect()->route('manager.users.index')->with('success', 'Xóa nhân viên thành công.');
    }

    public function toggleStatus(NhanVien $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Không có quyền thay đổi trạng thái tài khoản Admin.');
        }

        if ($user->id === auth('nhanvien')->id()) {
            return back()->with('error', 'Không thể thay đổi trạng thái tài khoản của chính mình.');
        }

        $newTrangThai = $user->trang_thai == 1 ? 0 : 1;
        $user->update(['trang_thai' => $newTrangThai]);

        $msg = $newTrangThai === 1 ? 'Đã mở khóa tài khoản.' : 'Đã khóa tài khoản.';
        return back()->with('success', $msg);
    }
}
