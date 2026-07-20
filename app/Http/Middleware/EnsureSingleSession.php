<?php

namespace App\Http\Middleware;

use App\Services\SingleSessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Đảm bảo mỗi tài khoản (nhân viên hoặc cư dân) chỉ tồn tại đúng 1 phiên đăng nhập.
 * Đăng nhập ở thiết bị/trình duyệt khác sẽ tự động đăng xuất phiên cũ ở request kế tiếp.
 */
class EnsureSingleSession
{
    /**
     * Route không áp dụng kiểm tra single-session.
     */
    private const EXCLUDED_ROUTES = [
        'login',
        'logout',
        'password.request',
        'password.email',
        'password.reset',
        'password.update',
    ];

    public function __construct(private readonly SingleSessionService $singleSession) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs(self::EXCLUDED_ROUTES)) {
            return $next($request);
        }

        // Duyệt qua toàn bộ guard session đã cấu hình, không hard-code tên guard cụ thể.
        foreach (array_keys(config('auth.guards', [])) as $guard) {
            if (config("auth.guards.{$guard}.driver") !== 'session') {
                continue;
            }

            $authGuard = Auth::guard($guard);

            if (!$authGuard->check()) {
                continue;
            }

            $userId = $authGuard->id();

            if ($this->singleSession->isValidSession($guard, $userId, $request->session()->getId())) {
                return $next($request);
            }

            return $this->forceLogout($request, $guard, $userId);
        }

        return $next($request);
    }

    /**
     * Đăng xuất phiên hiện tại một cách an toàn khi phát hiện đăng nhập từ nơi khác
     * (hoặc cache session đã hết hạn/bị xoá).
     */
    private function forceLogout(Request $request, string $guard, int|string $userId): Response
    {
        $this->singleSession->forget($guard, $userId);

        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('error', 'Tài khoản của bạn đã được đăng nhập trên một thiết bị khác. Vui lòng đăng nhập lại.');
    }
}
