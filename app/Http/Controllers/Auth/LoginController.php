<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ChucVu;
use App\Services\SingleSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(private readonly SingleSessionService $singleSession) {}

    /**
     * Lấy ID chức vụ Admin
     */
    private function adminChucVuId(): ?int
    {
        return ChucVu::where('chuc_vu', 'Admin')->value('id');
    }

    /**
     * Lấy ID chức vụ Quản lý
     */
    private function managerChucVuId(): ?int
    {
        return ChucVu::where('chuc_vu', 'Quản lý')->value('id');
    }

    public function showLoginForm()
    {
        if (Auth::guard('nhanvien')->check()) {

            $user = Auth::guard('nhanvien')->user();

            if ($user->chuc_vu == $this->adminChucVuId()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->chuc_vu == $this->managerChucVuId()) {
                return redirect()->route('manager.dashboard');
            }
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

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        $remember = $request->boolean('remember');
        $loai = $request->input('loai', 'nhanvien');

        // =======================
        // Đăng nhập nhân viên
        // =======================
        if ($loai === 'nhanvien') {

            if (Auth::guard('nhanvien')->attempt($credentials, $remember)) {

                $user = Auth::guard('nhanvien')->user();

                // Kiểm tra trạng thái
                if (!$user->isActive()) {

                    Auth::guard('nhanvien')->logout();

                    return back()
                        ->withErrors([
                            'email' => 'Tài khoản đã bị khóa.'
                        ])
                        ->withInput($request->only('email', 'loai'));
                }

                // Chỉ Admin và Quản lý được đăng nhập
                $allowRoles = [
                    $this->adminChucVuId(),
                    $this->managerChucVuId(),
                ];

                if (!in_array($user->chuc_vu, $allowRoles)) {

                    Auth::guard('nhanvien')->logout();

                    return back()
                        ->withErrors([
                            'email' => 'Bạn không có quyền truy cập hệ thống.'
                        ])
                        ->withInput($request->only('email', 'loai'));
                }

                $request->session()->regenerate();
                $this->singleSession->remember('nhanvien', $user->getAuthIdentifier(), $request->session()->getId());

                if ($user->chuc_vu == $this->adminChucVuId()) {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->chuc_vu == $this->managerChucVuId()) {
                    return redirect()->route('manager.dashboard');
                }
            }
        }

        // =======================
        // Đăng nhập cư dân
        // =======================
        if ($loai === 'cudan') {

            if (Auth::guard('cudan')->attempt($credentials, $remember)) {

                $user = Auth::guard('cudan')->user();

                if (!$user->isActive()) {

                    Auth::guard('cudan')->logout();

                    return back()
                        ->withErrors([
                            'email' => 'Tài khoản đã bị khóa.'
                        ])
                        ->withInput($request->only('email', 'loai'));
                }

                $request->session()->regenerate();
                $this->singleSession->remember('cudan', $user->getAuthIdentifier(), $request->session()->getId());

                return redirect()->route('resident.dashboard');
            }
        }

        return back()
            ->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng.'
            ])
            ->withInput($request->only('email', 'loai'));
    }

    public function logout(Request $request)
    {
        foreach (['nhanvien', 'cudan'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $this->singleSession->forget($guard, Auth::guard($guard)->id());
            }

            Auth::guard($guard)->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Đăng xuất thành công.');
    }
}