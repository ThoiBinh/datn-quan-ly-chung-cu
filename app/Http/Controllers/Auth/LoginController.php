<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('nhanvien')->check()) {
            return $this->redirectByRole(Auth::guard('nhanvien')->user()->vaitro);
        }
        if (Auth::guard('cudan')->check()) {
            return redirect()->route('resident.dashboard');
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

        $credentials = ['email' => $request->email, 'password' => $request->password];
        $remember    = $request->boolean('remember');
        $loai        = $request->input('loai', 'nhanvien');

        if ($loai === 'nhanvien') {
            if (Auth::guard('nhanvien')->attempt($credentials, $remember)) {
                $user = Auth::guard('nhanvien')->user();
                if (!$user->isActive()) {
                    Auth::guard('nhanvien')->logout();
                    return back()->withErrors(['email' => 'Tài khoản đã bị khóa.'])->withInput($request->only('email', 'loai'));
                }
                $request->session()->regenerate();
                return $this->redirectByRole($user->vaitro);
            }
        }

        if ($loai === 'cudan') {
            if (Auth::guard('cudan')->attempt($credentials, $remember)) {
                $user = Auth::guard('cudan')->user();
                if (!$user->isActive()) {
                    Auth::guard('cudan')->logout();
                    return back()->withErrors(['email' => 'Tài khoản đã bị khóa.'])->withInput($request->only('email', 'loai'));
                }
                $request->session()->regenerate();
                return redirect()->route('resident.dashboard');
            }
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])->withInput($request->only('email', 'loai'));
    }

    public function logout(Request $request)
    {
        Auth::guard('nhanvien')->logout();
        Auth::guard('cudan')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Đã đăng xuất thành công.');
    }

    private function redirectByRole(string $role)
    {
        return match($role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'manager' => redirect()->route('manager.dashboard'),
            default   => redirect()->route('home'),
        };
    }
}
