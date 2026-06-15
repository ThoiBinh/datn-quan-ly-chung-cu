<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('manager.users.index', compact('users'));
    }

    public function create()
    {
        return view('manager.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:manager,resident',
            'status'   => 'required|in:active,inactive',
        ], [
            'name.required'      => 'Vui lòng nhập họ tên.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.unique'       => 'Email đã tồn tại.',
            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'status'   => $request->status,
        ]);

        AuditLogService::log('INSERT', 'users', $user->id, null, $user->toArray());

        return redirect()->route('manager.users.index')->with('success', 'Tạo tài khoản thành công.');
    }

    public function edit(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Không có quyền chỉnh sửa tài khoản Admin.');
        }

        return view('manager.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Không có quyền chỉnh sửa tài khoản Admin.');
        }

        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'phone'  => 'nullable|string|max:20',
            'role'   => 'required|in:manager,resident',
            'status' => 'required|in:active,inactive',
        ]);

        $old  = $user->toArray();
        $data = $request->only('name', 'email', 'phone', 'role', 'status');

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        AuditLogService::log('UPDATE', 'users', $user->id, $old, $user->fresh()->toArray());

        return redirect()->route('manager.users.index')->with('success', 'Cập nhật tài khoản thành công.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Không có quyền xóa tài khoản Admin.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể xóa tài khoản của chính mình.');
        }

        AuditLogService::log('DELETE', 'users', $user->id, $user->toArray(), null);
        $user->delete();

        return redirect()->route('manager.users.index')->with('success', 'Xóa tài khoản thành công.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Không có quyền thay đổi trạng thái tài khoản Admin.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể thay đổi trạng thái tài khoản của chính mình.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $msg = $newStatus === 'active' ? 'Đã mở khóa tài khoản.' : 'Đã khóa tài khoản.';
        return back()->with('success', $msg);
    }
}
