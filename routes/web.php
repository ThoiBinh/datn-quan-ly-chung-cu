<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', fn() => view('home'))->name('home');

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Đổi mật khẩu — dùng bởi admin/manager (nhanvien guard)
Route::get('/doi-mat-khau', [PasswordController::class, 'showChangeForm'])->name('password.change')->middleware('manager');
Route::post('/doi-mat-khau', [PasswordController::class, 'change'])->middleware('manager');

// ==================== ADMIN ====================
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{nhatKy}', [\App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('audit-logs.show');
    // Admin cũng có quyền quản lý tòa nhà
    Route::resource('toa-nha', \App\Http\Controllers\Manager\ToaNhaController::class);
});

// ==================== MANAGER ====================
Route::prefix('manager')->name('manager.')->middleware('manager')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('toa-nha', \App\Http\Controllers\Manager\ToaNhaController::class);
    Route::resource('can-ho', \App\Http\Controllers\Manager\CanHoController::class);
    Route::resource('cu-dan', \App\Http\Controllers\Manager\CuDanController::class);
    Route::resource('phuong-tien', \App\Http\Controllers\Manager\PhuongTienController::class);
    Route::resource('phi-dich-vu', \App\Http\Controllers\Manager\PhiDichVuController::class);
    Route::resource('hoa-don', \App\Http\Controllers\Manager\HoaDonController::class);
    Route::post('hoa-don/{hoaDon}/ghi-nhan-thanh-toan', [\App\Http\Controllers\Manager\HoaDonController::class, 'ghiNhanThanhToan'])->name('hoa-don.ghi-nhan-thanh-toan');
    Route::resource('yeu-cau', \App\Http\Controllers\Manager\YeuCauController::class)->except(['create', 'store', 'edit']);
    Route::resource('thong-bao', \App\Http\Controllers\Manager\ThongBaoController::class);
    Route::resource('users', \App\Http\Controllers\Manager\UserController::class);
    Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\Manager\UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

// ==================== RESIDENT ====================
Route::prefix('resident')->name('resident.')->middleware('resident')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Resident\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Resident\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\Resident\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Resident\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/hoa-don', [\App\Http\Controllers\Resident\HoaDonController::class, 'index'])->name('hoa-don.index');
    Route::get('/hoa-don/{hoaDon}', [\App\Http\Controllers\Resident\HoaDonController::class, 'show'])->name('hoa-don.show');
    Route::post('/hoa-don/{hoaDon}/momo', [\App\Http\Controllers\Resident\HoaDonController::class, 'thanhToanMomo'])->name('hoa-don.momo');
    Route::post('/hoa-don/{hoaDon}/vnpay', [\App\Http\Controllers\Resident\HoaDonController::class, 'thanhToanVnpay'])->name('hoa-don.vnpay');
    Route::get('/phuong-tien', [\App\Http\Controllers\Resident\PhuongTienController::class, 'index'])->name('phuong-tien.index');
    Route::get('/phuong-tien/dang-ky', [\App\Http\Controllers\Resident\PhuongTienController::class, 'create'])->name('phuong-tien.create');
    Route::post('/phuong-tien', [\App\Http\Controllers\Resident\PhuongTienController::class, 'store'])->name('phuong-tien.store');
    Route::delete('/phuong-tien/{phuongTien}', [\App\Http\Controllers\Resident\PhuongTienController::class, 'destroy'])->name('phuong-tien.destroy');
    // Đổi mật khẩu cư dân (cudan guard)
    Route::get('/doi-mat-khau', [PasswordController::class, 'showChangeForm'])->name('change-password');
    Route::put('/doi-mat-khau', [PasswordController::class, 'change'])->name('change-password.update');
    Route::get('/thong-bao', fn() => view('resident.thong-bao.index', [
        'thongBao' => \App\Models\ThongBao::orderByDesc('createdAt')->paginate(10)
    ]))->name('thong-bao.index');
    Route::get('/thong-bao/{thongBao}', fn(\App\Models\ThongBao $thongBao) => view('resident.thong-bao.show', compact('thongBao')))->name('thong-bao.show');
    Route::get('/yeu-cau', [\App\Http\Controllers\Resident\YeuCauController::class, 'index'])->name('yeu-cau.index');
    Route::get('/yeu-cau/gui', [\App\Http\Controllers\Resident\YeuCauController::class, 'create'])->name('yeu-cau.create');
    Route::post('/yeu-cau', [\App\Http\Controllers\Resident\YeuCauController::class, 'store'])->name('yeu-cau.store');
    Route::get('/yeu-cau/{yeuCau}', [\App\Http\Controllers\Resident\YeuCauController::class, 'show'])->name('yeu-cau.show');
});

// Payment Callbacks
Route::get('/resident/payment/momo/callback', [\App\Http\Controllers\Resident\HoaDonController::class, 'callbackMomo'])->name('payment.momo.callback');
Route::get('/resident/payment/vnpay/callback', [\App\Http\Controllers\Resident\HoaDonController::class, 'callbackVnpay'])->name('payment.vnpay.callback');
