<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResidentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('cudan');

        if (!$guard->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Chưa đăng nhập'], 401);
            }
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $user = $guard->user();

        if (!$user->isActive()) {
            $guard->logout();
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa.');
        }

        return $next($request);
    }
}
