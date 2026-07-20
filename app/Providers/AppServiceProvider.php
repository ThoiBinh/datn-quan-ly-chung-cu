<?php

namespace App\Providers;

use App\Auth\CuDanUserProvider;
use App\Auth\NhanVienUserProvider;
use App\Hashing\Pbkdf2Hasher;
use App\Models\NhanVien;
use App\View\Composers\CauHinhWebsiteComposer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Route::model('user', NhanVien::class);

        // Register PBKDF2 as the application hasher driver
        Hash::extend('pbkdf2', fn() => new Pbkdf2Hasher());

        // Guard: cudan — PBKDF2 native (cu_dan dump data already PBKDF2 format)
        Auth::provider('cudan-eloquent', function ($app, array $config) {
            return new CuDanUserProvider($app['hash'], $config['model']);
        });

        // Guard: nhanvien — handles legacy bcrypt, auto-upgrades to PBKDF2
        Auth::provider('nhanvien-eloquent', function ($app, array $config) {
            return new NhanVienUserProvider($app['hash'], $config['model']);
        });

        View::composer(['layouts.admin', 'layouts.manager', 'layouts.resident', 'layouts.public', 'auth.login'], CauHinhWebsiteComposer::class);
    }
}
