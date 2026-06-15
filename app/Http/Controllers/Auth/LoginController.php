<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'Vui lòng nhập email.',
            'email.email'       => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');
        $loai        = $request->input('loai', 'nhanvien');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.'])->withInput($request->only('email', 'loai'));
            }

            // Kiểm tra đúng loại tab đăng nhập
            if ($loai === 'nhanvien' && $user->role === 'resident') {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản này không phải nhân viên.'])->withInput($request->only('email', 'loai'));
            }

            if ($loai === 'cudan' && $user->role !== 'resident') {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản này không phải cư dân.'])->withInput($request->only('email', 'loai'));
            }

            $request->session()->regenerate();
            return $this->redirectByRole($user->role);
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])->withInput($request->only('email', 'loai'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Đã đăng xuất thành công.');
    }

    private function redirectByRole(string $role)
    {
        return match($role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'manager'  => redirect()->route('manager.dashboard'),
            'resident' => redirect()->route('resident.dashboard'),
            default    => redirect()->route('home'),
        };
    }
}
