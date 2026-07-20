<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Quản lý cache session-id phục vụ cơ chế Single Session Login.
 * Mỗi tài khoản (guard + id) chỉ được lưu đúng 1 session-id còn hiệu lực.
 */
class SingleSessionService
{
    /**
     * TTL của bản ghi session trong cache — khớp với thời gian sống tối đa của phiên đăng nhập.
     */
    private const TTL_HOURS = 24;

    public function cacheKey(string $guard, int|string $userId): string
    {
        return "user_session_{$guard}_{$userId}";
    }

    /**
     * Ghi đè session-id hiện hành của tài khoản (last login wins).
     */
    public function remember(string $guard, int|string $userId, string $sessionId): void
    {
        Cache::put($this->cacheKey($guard, $userId), $sessionId, now()->addHours(self::TTL_HOURS));
    }

    public function forget(string $guard, int|string $userId): void
    {
        Cache::forget($this->cacheKey($guard, $userId));
    }

    /**
     * Trả về true nếu sessionId truyền vào là phiên đang được công nhận hợp lệ.
     * Cache lỗi/hết hạn/bị xoá đều coi là không hợp lệ (fail-safe: buộc đăng nhập lại).
     */
    public function isValidSession(string $guard, int|string $userId, string $sessionId): bool
    {
        try {
            $cachedSessionId = Cache::get($this->cacheKey($guard, $userId));
        } catch (\Throwable $e) {
            $cachedSessionId = null;
        }

        return $cachedSessionId !== null && hash_equals($cachedSessionId, $sessionId);
    }
}
