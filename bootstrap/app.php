<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin'    => \App\Http\Middleware\AdminMiddleware::class,
            'manager'  => \App\Http\Middleware\ManagerMiddleware::class,
            'resident' => \App\Http\Middleware\ResidentMiddleware::class,
        ]);

        // Single Session Login: chạy sau khi session đã khởi tạo, áp dụng cho toàn bộ route web
        // (bản thân middleware sẽ bỏ qua guest và các route login/logout).
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureSingleSession::class);

        // IPN từ MoMo/VNPay là server-to-server, không có CSRF token
        $middleware->validateCsrfTokens(except: [
            'payment/momo/ipn',
            'payment/vnpay/ipn',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
