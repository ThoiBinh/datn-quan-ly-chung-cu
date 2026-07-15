<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\UpdateManagerPasswordRequest;
use App\Http\Requests\Manager\UpdateManagerProfileRequest;
use App\Services\ManagerProfileService;
use App\Services\SingleSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ManagerProfileService $profileService,
        private readonly SingleSessionService $singleSession,
    ) {
    }

    /**
     * Luôn lấy tài khoản từ guard 'nhanvien' đang đăng nhập — không bao giờ
     * nhận ID từ route/request, nên Manager không thể xem/sửa hồ sơ người khác.
     */
    public function show(): View
    {
        $nhanVien = auth('nhanvien')->user();
        $nhanVien->load('chucVu');

        return view('manager.profile.show', compact('nhanVien'));
    }

    public function update(UpdateManagerProfileRequest $request): RedirectResponse
    {
        $nhanVien = auth('nhanvien')->user();

        $this->profileService->updateProfile($nhanVien, $request->validated());

        return redirect()->route('manager.profile.show')->with('success', 'Cập nhật hồ sơ thành công.');
    }

    public function updatePassword(UpdateManagerPasswordRequest $request): RedirectResponse
    {
        $nhanVien = auth('nhanvien')->user();

        $this->profileService->changePassword($nhanVien, $request->validated()['mat_khau']);

        // Regenerate session ID + CSRF token sau khi đổi mật khẩu, không đăng xuất người dùng.
        $request->session()->regenerate();

        // App có cơ chế single-session: session-id hợp lệ được cache theo (guard, user).
        // Phải cập nhật lại cache này sau regenerate(), nếu không request kế tiếp sẽ bị
        // EnsureSingleSession coi là "đăng nhập nơi khác" và tự động đăng xuất.
        $this->singleSession->remember('nhanvien', $nhanVien->id, $request->session()->getId());

        return redirect()->route('manager.profile.show')->with('success', 'Đổi mật khẩu thành công.');
    }
}
