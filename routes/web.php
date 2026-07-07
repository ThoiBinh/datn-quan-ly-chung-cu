<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', fn() => view('home', [
    'dsBangTin'      => \App\Models\BangTin::latest()->take(6)->get(),
    'cauHinhWebsite' => \App\Models\CauHinhWebsite::where('trang_thai', 1)->get()->keyBy('ma_thuoc_tinh'),
]))->name('home');

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
    Route::get('/dashboard/export/pdf',   [\App\Http\Controllers\Admin\DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');
    Route::get('/dashboard/export/excel', [\App\Http\Controllers\Admin\DashboardController::class, 'exportExcel'])->name('dashboard.export.excel');
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('users/{type}/{id}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show')->where('type', 'nhan-vien|cu-dan');
    Route::get('users/{type}/{id}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit')->where('type', 'nhan-vien|cu-dan');
    Route::put('users/{type}/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update')->where('type', 'nhan-vien|cu-dan');
    Route::patch('users/{type}/{id}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status')->where('type', 'nhan-vien|cu-dan');
    Route::get('audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{nhatKy}', [\App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('audit-logs.show');
    // Quản lý tòa nhà
    Route::resource('toa-nha', \App\Http\Controllers\Admin\ToaNhaController::class);

    // Quản lý căn hộ
    Route::resource('can-ho', \App\Http\Controllers\Admin\CanHoController::class);

    // Quản lý nhân viên
    Route::resource('nhan-vien', \App\Http\Controllers\Admin\NhanVienController::class)
        ->parameters(['nhan-vien' => 'nhanVien']);
    Route::patch('nhan-vien/{nhanVien}/toggle-status', [\App\Http\Controllers\Admin\NhanVienController::class, 'toggleStatus'])
        ->name('nhan-vien.toggle-status');

    // Quản lý cư dân
    Route::resource('cu-dan', \App\Http\Controllers\Admin\CuDanController::class)
        ->parameters(['cu-dan' => 'cuDan']);
    Route::patch('cu-dan/{cuDan}/toggle-status', [\App\Http\Controllers\Admin\CuDanController::class, 'toggleStatus'])
        ->name('cu-dan.toggle-status');

    // Quản lý phương tiện
    Route::resource('phuong-tien', \App\Http\Controllers\Admin\PhuongTienController::class)
        ->parameters(['phuong-tien' => 'phuongTien']);
    Route::patch('phuong-tien/{phuongTien}/toggle-status', [\App\Http\Controllers\Admin\PhuongTienController::class, 'toggleStatus'])
        ->name('phuong-tien.toggle-status');
    Route::patch('phuong-tien/{phuongTien}/restore', [\App\Http\Controllers\Admin\PhuongTienController::class, 'restore'])
        ->name('phuong-tien.restore');

    // Quản lý hóa đơn
    Route::get('hoa-don/preview-phi', [\App\Http\Controllers\Admin\HoaDonController::class, 'previewPhi'])->name('hoa-don.preview-phi');
    Route::get('hoa-don/can-ho-services', [\App\Http\Controllers\Admin\HoaDonController::class, 'canHoServices'])->name('hoa-don.can-ho-services');
    Route::post('hoa-don/can-ho-services', [\App\Http\Controllers\Admin\HoaDonController::class, 'syncCanHoServices'])->name('hoa-don.can-ho-services.sync');
    Route::delete('hoa-don/{hoaDon}', [\App\Http\Controllers\Admin\HoaDonController::class, 'destroy'])->name('hoa-don.destroy');
    Route::delete('hoa-don/{hoaDon}/chi-tiet/{chiTiet}', [\App\Http\Controllers\Admin\HoaDonController::class, 'destroyChiTiet'])->name('hoa-don.chi-tiet.destroy');
    Route::resource('hoa-don', \App\Http\Controllers\Admin\HoaDonController::class)
        ->except(['destroy'])
        ->parameters(['hoa-don' => 'hoaDon']);

    // Thanh toán hóa đơn (immutable — no edit/delete)
    Route::get('thanh-toan', [\App\Http\Controllers\Admin\ThanhToanController::class, 'index'])->name('thanh-toan.index');
    Route::get('thanh-toan/{lichSu}', [\App\Http\Controllers\Admin\ThanhToanController::class, 'show'])->name('thanh-toan.show');
    Route::get('hoa-don/{hoaDon}/thanh-toan/create', [\App\Http\Controllers\Admin\ThanhToanController::class, 'create'])->name('thanh-toan.create');
    Route::post('hoa-don/{hoaDon}/thanh-toan', [\App\Http\Controllers\Admin\ThanhToanController::class, 'store'])->name('thanh-toan.store');
    Route::post('hoa-don/{hoaDon}/momo', [\App\Http\Controllers\Admin\ThanhToanController::class, 'initiateMomo'])->name('thanh-toan.momo');
    Route::post('hoa-don/{hoaDon}/vnpay', [\App\Http\Controllers\Admin\ThanhToanController::class, 'initiateVnpay'])->name('thanh-toan.vnpay');

    // Quản lý phí dịch vụ
    Route::resource('phi-dich-vu', \App\Http\Controllers\Admin\PhiDichVuController::class)
        ->parameters(['phi-dich-vu' => 'phiDichVu']);

    // Quản lý phí dịch vụ căn hộ
    Route::resource('can-ho-phi-dich-vu', \App\Http\Controllers\Admin\CanHoPhiDichVuController::class)
        ->except(['destroy'])
        ->parameters(['can-ho-phi-dich-vu' => 'canHoPhiDichVu']);

    // Quản lý thông báo
    Route::resource('thong-bao', \App\Http\Controllers\Admin\ThongBaoController::class)
        ->except(['destroy'])
        ->parameters(['thong-bao' => 'thongBao']);
    Route::patch('thong-bao/{thongBao}/toggle-hide', [\App\Http\Controllers\Admin\ThongBaoController::class, 'toggleHide'])
        ->name('thong-bao.toggle-hide');
    Route::patch('thong-bao/{id}/restore', [\App\Http\Controllers\Admin\ThongBaoController::class, 'restore'])
        ->name('thong-bao.restore');

    // Quản lý bảng tin
    Route::resource('bang-tin', \App\Http\Controllers\Admin\BangTinController::class)
        ->except(['destroy'])
        ->parameters(['bang-tin' => 'bangTin']);
    Route::patch('bang-tin/{bangTin}/toggle-hide', [\App\Http\Controllers\Admin\BangTinController::class, 'toggleHide'])
        ->name('bang-tin.toggle-hide');
    Route::patch('bang-tin/{id}/restore', [\App\Http\Controllers\Admin\BangTinController::class, 'restore'])
        ->name('bang-tin.restore');

    // Quản lý cấu hình thanh toán
    Route::resource('cau-hinh-thanh-toan', \App\Http\Controllers\Admin\CauHinhThanhToanController::class)
        ->except(['destroy'])
        ->parameters(['cau-hinh-thanh-toan' => 'cauHinhThanhToan']);
    Route::patch('cau-hinh-thanh-toan/{cauHinhThanhToan}/toggle-status', [\App\Http\Controllers\Admin\CauHinhThanhToanController::class, 'toggleStatus'])
        ->name('cau-hinh-thanh-toan.toggle-status');

    // Quản lý cấu hình website
    Route::resource('cau-hinh-website', \App\Http\Controllers\Admin\CauHinhWebsiteController::class)
        ->except(['create', 'store', 'destroy'])
        ->parameters(['cau-hinh-website' => 'cauHinhWebsite']);
    Route::patch('cau-hinh-website/{cauHinhWebsite}/toggle-status', [\App\Http\Controllers\Admin\CauHinhWebsiteController::class, 'toggleStatus'])
        ->name('cau-hinh-website.toggle-status');

    // Quản lý thuộc tính
    Route::resource('thuoc-tinh', \App\Http\Controllers\Admin\ThuocTinhController::class)
        ->parameters(['thuoc-tinh' => 'thuocTinh']);
    Route::patch('thuoc-tinh/{id}/restore', [\App\Http\Controllers\Admin\ThuocTinhController::class, 'restore'])
        ->name('thuoc-tinh.restore');

    // Quản lý vai trò
    Route::resource('vai-tro', \App\Http\Controllers\Admin\VaiTroController::class)
        ->parameters(['vai-tro' => 'vaiTro']);

    // Quản lý chức vụ
    Route::resource('chuc-vu', \App\Http\Controllers\Admin\ChucVuController::class)
        ->parameters(['chuc-vu' => 'chucVu']);
    Route::patch('chuc-vu/{id}/restore', [\App\Http\Controllers\Admin\ChucVuController::class, 'restore'])
        ->name('chuc-vu.restore');

    // Quản lý loại phương tiện
    Route::resource('loai-phuong-tien', \App\Http\Controllers\Admin\LoaiPhuongTienController::class)
        ->parameters(['loai-phuong-tien' => 'loaiPhuongTien']);

    // Quản lý loại yêu cầu
    Route::resource('loai-yeu-cau', \App\Http\Controllers\Admin\LoaiYeuCauController::class)
        ->parameters(['loai-yeu-cau' => 'loaiYeuCau']);
    Route::patch('loai-yeu-cau/{id}/restore', [\App\Http\Controllers\Admin\LoaiYeuCauController::class, 'restore'])
        ->name('loai-yeu-cau.restore');

    // Quản lý loại căn hộ
    Route::resource('loai-can-ho', \App\Http\Controllers\Admin\LoaiCanHoController::class)
        ->parameters(['loai-can-ho' => 'loaiCanHo']);

    // Quản lý trạng thái căn hộ
    Route::resource('trang-thai-can-ho', \App\Http\Controllers\Admin\TrangThaiCanHoController::class)
        ->parameters(['trang-thai-can-ho' => 'trangThaiCanHo']);

    // Quản lý loại phí dịch vụ
    Route::resource('loai-phi-dich-vu', \App\Http\Controllers\Admin\LoaiPhiDichVuController::class)
        ->parameters(['loai-phi-dich-vu' => 'loaiPhiDichVu']);

    // Quản lý đơn vị tính phí dịch vụ
    Route::resource('don-vi-tinh-phi-dich-vu', \App\Http\Controllers\Admin\DonViTinhPhiDichVuController::class)
        ->parameters(['don-vi-tinh-phi-dich-vu' => 'donViTinhPhiDichVu']);

    // Quản lý yêu cầu cư dân
    Route::resource('yeu-cau', \App\Http\Controllers\Admin\YeuCauCuDanController::class)
        ->only(['index', 'show', 'update', 'destroy'])
        ->parameters(['yeu-cau' => 'yeuCau']);
    Route::patch('yeu-cau/{yeuCau}/duyet', [\App\Http\Controllers\Admin\YeuCauCuDanController::class, 'approve'])->name('yeu-cau.approve');
    Route::patch('yeu-cau/{yeuCau}/tu-choi', [\App\Http\Controllers\Admin\YeuCauCuDanController::class, 'reject'])->name('yeu-cau.reject');

    // Quản lý cư dân căn hộ
    Route::resource('cu-dan-can-ho', \App\Http\Controllers\Admin\CuDanCanHoController::class)
        ->except(['destroy'])
        ->parameters(['cu-dan-can-ho' => 'cuDanCanHo']);
    Route::patch('cu-dan-can-ho/{cuDanCanHo}/toggle-status', [\App\Http\Controllers\Admin\CuDanCanHoController::class, 'toggleStatus'])
        ->name('cu-dan-can-ho.toggle-status');

    // Quản lý đặt lịch tiện ích
    // Lưu ý: route dashboard phải đăng ký TRƯỚC resource() để không bị route
    // show ('{datLichTienIch}') nuốt mất chuỗi "dashboard" như một ID.
    Route::get('dat-lich-tien-ich/dashboard', [\App\Http\Controllers\Admin\DatLichTienIchController::class, 'dashboard'])
        ->name('dat-lich-tien-ich.dashboard');
    Route::resource('dat-lich-tien-ich', \App\Http\Controllers\Admin\DatLichTienIchController::class)
        ->parameters(['dat-lich-tien-ich' => 'datLichTienIch']);
    Route::patch('dat-lich-tien-ich/{id}/restore', [\App\Http\Controllers\Admin\DatLichTienIchController::class, 'restore'])
        ->name('dat-lich-tien-ich.restore');
    Route::patch('dat-lich-tien-ich/{datLichTienIch}/approve', [\App\Http\Controllers\Admin\DatLichTienIchController::class, 'approve'])
        ->name('dat-lich-tien-ich.approve');
    Route::patch('dat-lich-tien-ich/{datLichTienIch}/reject', [\App\Http\Controllers\Admin\DatLichTienIchController::class, 'reject'])
        ->name('dat-lich-tien-ich.reject');
    Route::patch('dat-lich-tien-ich/{datLichTienIch}/cancel', [\App\Http\Controllers\Admin\DatLichTienIchController::class, 'cancel'])
        ->name('dat-lich-tien-ich.cancel');

    // Quản lý tiện ích
    Route::resource('tien-ich', \App\Http\Controllers\Admin\TienIchController::class)
        ->parameters(['tien-ich' => 'tienIch']);

    // Quản lý loại tiện ích
    Route::resource('loai-tien-ich', \App\Http\Controllers\Admin\LoaiTienIchController::class)
        ->parameters(['loai-tien-ich' => 'loaiTienIch']);
});

// ==================== MANAGER ====================
Route::prefix('manager')->name('manager.')->middleware('manager')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export/pdf',   [\App\Http\Controllers\Manager\DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');
    Route::get('/dashboard/export/excel', [\App\Http\Controllers\Manager\DashboardController::class, 'exportExcel'])->name('dashboard.export.excel');
    Route::resource('toa-nha', \App\Http\Controllers\Manager\ToaNhaController::class);
    Route::resource('can-ho', \App\Http\Controllers\Manager\CanHoController::class);
    Route::resource('cu-dan', \App\Http\Controllers\Manager\CuDanController::class);
    Route::resource('phuong-tien', \App\Http\Controllers\Manager\PhuongTienController::class)
        ->parameters(['phuong-tien' => 'phuongTien']);
    Route::delete('phuong-tien/{phuongTien}/xoa-mem', [\App\Http\Controllers\Manager\PhuongTienController::class, 'xoaMem'])
        ->name('phuong-tien.xoa-mem');
    Route::resource('phi-dich-vu', \App\Http\Controllers\Manager\PhiDichVuController::class);
    Route::get('hoa-don/preview-phi', [\App\Http\Controllers\Manager\HoaDonController::class, 'previewPhi'])->name('hoa-don.preview-phi');
    Route::get('hoa-don/can-ho-services', [\App\Http\Controllers\Manager\HoaDonController::class, 'canHoServices'])->name('hoa-don.can-ho-services');
    Route::post('hoa-don/can-ho-services', [\App\Http\Controllers\Manager\HoaDonController::class, 'syncCanHoServices'])->name('hoa-don.can-ho-services.sync');
    Route::delete('hoa-don/{hoaDon}', [\App\Http\Controllers\Manager\HoaDonController::class, 'destroy'])->name('hoa-don.destroy');
    Route::delete('hoa-don/{hoaDon}/chi-tiet/{chiTiet}', [\App\Http\Controllers\Manager\HoaDonController::class, 'destroyChiTiet'])->name('hoa-don.chi-tiet.destroy');
    Route::resource('hoa-don', \App\Http\Controllers\Manager\HoaDonController::class)->except(['destroy'])->parameters(['hoa-don' => 'hoaDon']);

    // Thanh toán hóa đơn (immutable — no edit/delete)
    Route::get('thanh-toan', [\App\Http\Controllers\Manager\ThanhToanController::class, 'index'])->name('thanh-toan.index');
    Route::get('thanh-toan/{lichSu}', [\App\Http\Controllers\Manager\ThanhToanController::class, 'show'])->name('thanh-toan.show');
    Route::get('hoa-don/{hoaDon}/thanh-toan/create', [\App\Http\Controllers\Manager\ThanhToanController::class, 'create'])->name('thanh-toan.create');
    Route::post('hoa-don/{hoaDon}/thanh-toan', [\App\Http\Controllers\Manager\ThanhToanController::class, 'store'])->name('thanh-toan.store');
    Route::post('hoa-don/{hoaDon}/momo', [\App\Http\Controllers\Manager\ThanhToanController::class, 'initiateMomo'])->name('thanh-toan.momo');
    Route::post('hoa-don/{hoaDon}/vnpay', [\App\Http\Controllers\Manager\ThanhToanController::class, 'initiateVnpay'])->name('thanh-toan.vnpay');

    Route::resource('yeu-cau', \App\Http\Controllers\Manager\YeuCauController::class)->except(['create', 'store', 'edit']);
    Route::patch('yeu-cau/{yeuCau}/duyet', [\App\Http\Controllers\Manager\YeuCauController::class, 'approve'])->name('yeu-cau.approve');
    Route::patch('yeu-cau/{yeuCau}/tu-choi', [\App\Http\Controllers\Manager\YeuCauController::class, 'reject'])->name('yeu-cau.reject');
    Route::resource('thong-bao', \App\Http\Controllers\Manager\ThongBaoController::class)
        ->parameters(['thong-bao' => 'thongBao']);
    Route::patch('thong-bao/{thongBao}/toggle-hide', [\App\Http\Controllers\Manager\ThongBaoController::class, 'toggleHide'])
        ->name('thong-bao.toggle-hide');
    Route::patch('thong-bao/{id}/restore', [\App\Http\Controllers\Manager\ThongBaoController::class, 'restore'])
        ->name('thong-bao.restore');

    // Bảng tin
    Route::resource('bang-tin', \App\Http\Controllers\Manager\BangTinController::class)
        ->except(['destroy'])
        ->parameters(['bang-tin' => 'bangTin']);
    Route::patch('bang-tin/{bangTin}/toggle-hide', [\App\Http\Controllers\Manager\BangTinController::class, 'toggleHide'])
        ->name('bang-tin.toggle-hide');
    Route::patch('bang-tin/{id}/restore', [\App\Http\Controllers\Manager\BangTinController::class, 'restore'])
        ->name('bang-tin.restore');

    // Cấu hình thanh toán
    Route::resource('cau-hinh-thanh-toan', \App\Http\Controllers\Manager\CauHinhThanhToanController::class)
        ->except(['destroy'])
        ->parameters(['cau-hinh-thanh-toan' => 'cauHinhThanhToan']);
    Route::patch('cau-hinh-thanh-toan/{cauHinhThanhToan}/toggle-status', [\App\Http\Controllers\Manager\CauHinhThanhToanController::class, 'toggleStatus'])
        ->name('cau-hinh-thanh-toan.toggle-status');

    // Cấu hình website
    Route::resource('cau-hinh-website', \App\Http\Controllers\Manager\CauHinhWebsiteController::class)
        ->except(['create', 'store', 'destroy'])
        ->parameters(['cau-hinh-website' => 'cauHinhWebsite']);
    Route::patch('cau-hinh-website/{cauHinhWebsite}/toggle-status', [\App\Http\Controllers\Manager\CauHinhWebsiteController::class, 'toggleStatus'])
        ->name('cau-hinh-website.toggle-status');

    // Quản lý thuộc tính
    Route::resource('thuoc-tinh', \App\Http\Controllers\Manager\ThuocTinhController::class)
        ->parameters(['thuoc-tinh' => 'thuocTinh']);
    Route::patch('thuoc-tinh/{id}/restore', [\App\Http\Controllers\Manager\ThuocTinhController::class, 'restore'])
        ->name('thuoc-tinh.restore');

    // Quản lý vai trò
    Route::resource('vai-tro', \App\Http\Controllers\Manager\VaiTroController::class)
        ->parameters(['vai-tro' => 'vaiTro']);

    // Quản lý chức vụ
    Route::resource('chuc-vu', \App\Http\Controllers\Manager\ChucVuController::class)
        ->parameters(['chuc-vu' => 'chucVu']);
    Route::patch('chuc-vu/{id}/restore', [\App\Http\Controllers\Manager\ChucVuController::class, 'restore'])
        ->name('chuc-vu.restore');

    // Quản lý loại phương tiện
    Route::resource('loai-phuong-tien', \App\Http\Controllers\Manager\LoaiPhuongTienController::class)
        ->parameters(['loai-phuong-tien' => 'loaiPhuongTien']);

    // Quản lý loại yêu cầu
    Route::resource('loai-yeu-cau', \App\Http\Controllers\Manager\LoaiYeuCauController::class)
        ->parameters(['loai-yeu-cau' => 'loaiYeuCau']);
    Route::patch('loai-yeu-cau/{id}/restore', [\App\Http\Controllers\Manager\LoaiYeuCauController::class, 'restore'])
        ->name('loai-yeu-cau.restore');

    // Quản lý loại căn hộ
    Route::resource('loai-can-ho', \App\Http\Controllers\Manager\LoaiCanHoController::class)
        ->parameters(['loai-can-ho' => 'loaiCanHo']);

    // Quản lý trạng thái căn hộ
    Route::resource('trang-thai-can-ho', \App\Http\Controllers\Manager\TrangThaiCanHoController::class)
        ->parameters(['trang-thai-can-ho' => 'trangThaiCanHo']);

    // Quản lý loại phí dịch vụ
    Route::resource('loai-phi-dich-vu', \App\Http\Controllers\Manager\LoaiPhiDichVuController::class)
        ->parameters(['loai-phi-dich-vu' => 'loaiPhiDichVu']);

    // Quản lý đơn vị tính phí dịch vụ
    Route::resource('don-vi-tinh-phi-dich-vu', \App\Http\Controllers\Manager\DonViTinhPhiDichVuController::class)
        ->parameters(['don-vi-tinh-phi-dich-vu' => 'donViTinhPhiDichVu']);

    // Quản lý nhân viên
    Route::resource('nhan-vien', \App\Http\Controllers\Manager\NhanVienController::class)
        ->parameters(['nhan-vien' => 'nhanVien']);

    Route::resource('users', \App\Http\Controllers\Manager\UserController::class);
    Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\Manager\UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Quản lý cư dân căn hộ
    Route::resource('cu-dan-can-ho', \App\Http\Controllers\Manager\CuDanCanHoController::class)
        ->except(['destroy'])
        ->parameters(['cu-dan-can-ho' => 'cuDanCanHo']);
    Route::patch('cu-dan-can-ho/{cuDanCanHo}/toggle-status', [\App\Http\Controllers\Manager\CuDanCanHoController::class, 'toggleStatus'])
        ->name('cu-dan-can-ho.toggle-status');

    // Quản lý phí dịch vụ căn hộ
    Route::resource('can-ho-phi-dich-vu', \App\Http\Controllers\Manager\CanHoPhiDichVuController::class)
        ->parameters(['can-ho-phi-dich-vu' => 'canHoPhiDichVu']);

    // Quản lý đặt lịch tiện ích
    // Lưu ý: route dashboard phải đăng ký TRƯỚC resource() để không bị route
    // show ('{datLichTienIch}') nuốt mất chuỗi "dashboard" như một ID.
    Route::get('dat-lich-tien-ich/dashboard', [\App\Http\Controllers\Manager\DatLichTienIchController::class, 'dashboard'])
        ->name('dat-lich-tien-ich.dashboard');
    Route::resource('dat-lich-tien-ich', \App\Http\Controllers\Manager\DatLichTienIchController::class)
        ->parameters(['dat-lich-tien-ich' => 'datLichTienIch']);
    Route::patch('dat-lich-tien-ich/{id}/restore', [\App\Http\Controllers\Manager\DatLichTienIchController::class, 'restore'])
        ->name('dat-lich-tien-ich.restore');
    Route::patch('dat-lich-tien-ich/{datLichTienIch}/approve', [\App\Http\Controllers\Manager\DatLichTienIchController::class, 'approve'])
        ->name('dat-lich-tien-ich.approve');
    Route::patch('dat-lich-tien-ich/{datLichTienIch}/reject', [\App\Http\Controllers\Manager\DatLichTienIchController::class, 'reject'])
        ->name('dat-lich-tien-ich.reject');
    Route::patch('dat-lich-tien-ich/{datLichTienIch}/cancel', [\App\Http\Controllers\Manager\DatLichTienIchController::class, 'cancel'])
        ->name('dat-lich-tien-ich.cancel');

    // Quản lý tiện ích
    Route::resource('tien-ich', \App\Http\Controllers\Manager\TienIchController::class)
        ->parameters(['tien-ich' => 'tienIch']);

    // Quản lý loại tiện ích
    Route::resource('loai-tien-ich', \App\Http\Controllers\Manager\LoaiTienIchController::class)
        ->parameters(['loai-tien-ich' => 'loaiTienIch']);
});

// ==================== RESIDENT ====================
Route::prefix('resident')->name('resident.')->middleware('resident')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Resident\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Resident\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\Resident\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Resident\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [\App\Http\Controllers\Resident\ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::get('/profile/change-password', [\App\Http\Controllers\Resident\ProfileController::class, 'showChangePassword'])->name('profile.change-password');
    Route::post('/profile/change-password', [\App\Http\Controllers\Resident\ProfileController::class, 'updatePassword'])->name('profile.update-password');
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
    Route::get('/thong-bao', [\App\Http\Controllers\Resident\ThongBaoController::class, 'index'])->name('thong-bao.index');
    Route::get('/thong-bao/{thongBao}', [\App\Http\Controllers\Resident\ThongBaoController::class, 'show'])->name('thong-bao.show');
    Route::get('/yeu-cau', [\App\Http\Controllers\Resident\YeuCauController::class, 'index'])->name('yeu-cau.index');
    Route::get('/yeu-cau/gui', [\App\Http\Controllers\Resident\YeuCauController::class, 'create'])->name('yeu-cau.create');
    Route::post('/yeu-cau', [\App\Http\Controllers\Resident\YeuCauController::class, 'store'])->name('yeu-cau.store');
    Route::get('/yeu-cau/{yeuCau}', [\App\Http\Controllers\Resident\YeuCauController::class, 'show'])->name('yeu-cau.show');
    Route::get('/yeu-cau/{yeuCau}/chinh-sua', [\App\Http\Controllers\Resident\YeuCauController::class, 'edit'])->name('yeu-cau.edit');
    Route::put('/yeu-cau/{yeuCau}', [\App\Http\Controllers\Resident\YeuCauController::class, 'update'])->name('yeu-cau.update');
    Route::patch('/yeu-cau/{yeuCau}/huy', [\App\Http\Controllers\Resident\YeuCauController::class, 'cancel'])->name('yeu-cau.huy');

    // Tiện ích (xem danh sách / chi tiết)
    Route::get('/tien-ich', [\App\Http\Controllers\Resident\TienIchController::class, 'index'])->name('tien-ich.index');
    Route::get('/tien-ich/{tienIch}', [\App\Http\Controllers\Resident\TienIchController::class, 'show'])->name('tien-ich.show');

    // Đặt lịch tiện ích
    // Lưu ý: route "dat-lich" (form tạo mới) phải đăng ký TRƯỚC route show
    // ('{datLichTienIch}') để không bị nuốt mất chuỗi "dat-lich" như một ID.
    Route::get('/dat-lich-tien-ich/dat-lich', [\App\Http\Controllers\Resident\DatLichTienIchController::class, 'create'])->name('dat-lich-tien-ich.create');
    Route::post('/dat-lich-tien-ich', [\App\Http\Controllers\Resident\DatLichTienIchController::class, 'store'])->name('dat-lich-tien-ich.store');
    Route::get('/dat-lich-tien-ich', [\App\Http\Controllers\Resident\DatLichTienIchController::class, 'index'])->name('dat-lich-tien-ich.index');
    Route::get('/dat-lich-tien-ich/{datLichTienIch}', [\App\Http\Controllers\Resident\DatLichTienIchController::class, 'show'])->name('dat-lich-tien-ich.show');
    Route::patch('/dat-lich-tien-ich/{datLichTienIch}/huy', [\App\Http\Controllers\Resident\DatLichTienIchController::class, 'cancel'])->name('dat-lich-tien-ich.huy');
});

// Payment Callbacks (legacy — redirect URL đã chuyển sang payment.momo.return)
Route::get('/resident/payment/momo/callback', [\App\Http\Controllers\Resident\HoaDonController::class, 'callbackMomo'])->name('payment.momo.callback');
Route::get('/resident/payment/vnpay/callback', [\App\Http\Controllers\Resident\HoaDonController::class, 'callbackVnpay'])->name('payment.vnpay.callback');

// MoMo Payment — nằm ngoài mọi auth middleware
Route::get('/payment/momo/return', [\App\Http\Controllers\Payment\MomoController::class, 'return'])->name('payment.momo.return');
Route::post('/payment/momo/ipn', [\App\Http\Controllers\Payment\MomoController::class, 'ipn'])->name('payment.momo.ipn');

// VNPay Payment — nằm ngoài mọi auth middleware
Route::get('/payment/vnpay/return', [\App\Http\Controllers\Payment\VnpayController::class, 'return'])->name('payment.vnpay.return');
Route::get('/payment/vnpay/ipn', [\App\Http\Controllers\Payment\VnpayController::class, 'ipn'])->name('payment.vnpay.ipn');
