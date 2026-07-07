<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Chung Cư</title>
    @if($faviconUrl = optional($cauHinhWebsite->get('favicon'))->gia_tri_url)
        <link rel="icon" href="{{ $faviconUrl }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white" x-data="{ mobileMenu: false }">

@php
    $chwLienHe      = $cauHinhWebsite->where('ten_nhom', 'Liên hệ');
    $chwMangXaHoi   = $cauHinhWebsite->where('ten_nhom', 'Mạng xã hội');

    $tenChungCu   = optional($cauHinhWebsite->get('ten_chung_cu'))->gia_tri;
    $website      = optional($cauHinhWebsite->get('website'))->gia_tri;
    $banQuyen     = optional($cauHinhWebsite->get('copyright'))->gia_tri;
    $moTaWebsite  = optional($cauHinhWebsite->get('mo_ta_seo'))->gia_tri;
    $logoUrl      = optional($cauHinhWebsite->get('logo'))->gia_tri_url;

    $diaChi       = optional($cauHinhWebsite->get('dia_chi'))->gia_tri;
    $soDienThoai  = optional($cauHinhWebsite->get('so_dien_thoai'))->gia_tri;
    $hotline      = optional($cauHinhWebsite->get('hotline'))->gia_tri;
    $email        = optional($cauHinhWebsite->get('email'))->gia_tri;
    $gioLamViec   = optional($cauHinhWebsite->get('gio_lam_viec'))->gia_tri;
@endphp

{{-- Navigation --}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-2">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $tenChungCu ?? 'Logo' }}" class="w-8 h-8 rounded-lg object-cover">
                @else
                    <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                @endif
                <span class="font-bold text-gray-900 text-lg">{{ $tenChungCu ?? 'Chung Cư Pro' }}</span>
            </div>
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('bang-tin.index') }}" class="text-sm text-gray-600 hover:text-emerald-600 font-medium">Bảng tin</a>
                <a href="#tinh-nang" class="text-sm text-gray-600 hover:text-emerald-600 font-medium">Tính năng</a>
                <a href="#lien-he" class="text-sm text-gray-600 hover:text-emerald-600 font-medium">Liên hệ</a>
                
            </div>
            <div class="hidden md:flex items-center gap-3">
                @if(auth('nhanvien')->check())
                    @if(auth('nhanvien')->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">Dashboard</a>
                    @else
                        <a href="{{ route('manager.dashboard') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">Dashboard</a>
                    @endif
                @elseif(auth('cudan')->check())
                    <a href="{{ route('resident.dashboard') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">Cổng cư dân</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Đăng nhập</a>
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">Bắt đầu</a>
                @endif
            </div>
            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div x-show="mobileMenu" x-cloak class="md:hidden py-3 border-t border-gray-100 space-y-1">
            <a href="#tinh-nang" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-50">Tính năng</a>
            <a href="{{ route('login') }}" class="block px-3 py-2 text-sm font-medium text-emerald-600 rounded-lg hover:bg-emerald-50">Đăng nhập</a>
        </div>
    </div>
</nav>

{{-- Hero --}}
<section class="pt-24 pb-20 bg-gradient-to-br from-emerald-50 via-white to-teal-50 relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-20 right-10 w-72 h-72 bg-emerald-200/30 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-teal-100/40 rounded-full blur-3xl"></div>
    </div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold mb-6">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Hệ thống quản lý chung cư toàn diện
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                Quản lý chung cư<br>
                <span class="text-emerald-600">thông minh & hiệu quả</span>
            </h1>
            <p class="text-lg text-gray-600 mb-10 leading-relaxed">
                Nền tảng quản lý toàn diện cho ban quản lý và cư dân: từ hóa đơn, thanh toán online đến phản ánh và thông báo — tất cả trong một hệ thống.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition text-center">
                    Đăng nhập hệ thống
                </a>
                <a href="#tinh-nang" class="px-8 py-3.5 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-center">
                    Tìm hiểu thêm
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="py-12 bg-white border-y border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <p class="text-3xl font-extrabold text-emerald-600">500+</p>
                <p class="text-sm text-gray-500 mt-1">Căn hộ quản lý</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-emerald-600">1,200+</p>
                <p class="text-sm text-gray-500 mt-1">Cư dân sử dụng</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-emerald-600">99.9%</p>
                <p class="text-sm text-gray-500 mt-1">Uptime đảm bảo</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-emerald-600">24/7</p>
                <p class="text-sm text-gray-500 mt-1">Hỗ trợ kỹ thuật</p>
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section id="tinh-nang" class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Tính năng nổi bật</h2>
            <p class="text-gray-500">Đầy đủ công cụ cho ban quản lý và cổng self-service cho cư dân</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $features = [
                ['icon'=>'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'color'=>'emerald', 'title'=>'Quản lý căn hộ', 'desc'=>'Quản lý thông tin tòa nhà, tầng và từng căn hộ. Theo dõi tình trạng sử dụng, danh sách cư dân và lịch sử hợp đồng.'],
                ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color'=>'blue', 'title'=>'Hóa đơn tự động', 'desc'=>'Tự động tạo hóa đơn hàng tháng dựa trên phí dịch vụ đã đăng ký. Theo dõi công nợ và nhắc nhở thanh toán.'],
                ['icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'color'=>'purple', 'title'=>'Thanh toán online', 'desc'=>'Tích hợp cổng thanh toán MoMo và VNPay QR. Cư dân thanh toán hóa đơn trực tuyến mọi lúc, mọi nơi.'],
                ['icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'color'=>'orange', 'title'=>'Phản ánh & Hỗ trợ', 'desc'=>'Cư dân gửi phản ánh, yêu cầu sửa chữa trực tiếp qua app. Ban quản lý tiếp nhận và phản hồi nhanh chóng.'],
                ['icon'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'color'=>'red', 'title'=>'Thông báo tức thời', 'desc'=>'Gửi thông báo đến tất cả cư dân hoặc từng căn hộ cụ thể. Lịch tắt nước, bảo trì thang máy... được thông báo kịp thời.'],
                ['icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color'=>'teal', 'title'=>'Báo cáo & Thống kê', 'desc'=>'Báo cáo doanh thu, tỷ lệ thu tiền, thống kê tình trạng căn hộ và phương tiện. Xuất báo cáo dạng biểu đồ trực quan.'],
            ];
            @endphp
            @foreach($features as $f)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-{{ $f['color'] }}-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-{{ $f['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">{{ $f['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Roles --}}
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Dành cho tất cả mọi người</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-8 rounded-2xl border-2 border-gray-100 hover:border-emerald-200 transition">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Admin</h3>
                <p class="text-sm text-gray-500">Toàn quyền quản trị hệ thống, phân quyền người dùng, xem log audit.</p>
            </div>
            <div class="text-center p-8 rounded-2xl border-2 border-indigo-100 bg-indigo-50/50">
                <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Ban Quản Lý</h3>
                <p class="text-sm text-gray-500">Quản lý căn hộ, cư dân, phí dịch vụ, hóa đơn và xử lý yêu cầu hàng ngày.</p>
            </div>
            <div class="text-center p-8 rounded-2xl border-2 border-emerald-100 bg-emerald-50/50">
                <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Cư Dân</h3>
                <p class="text-sm text-gray-500">Xem và thanh toán hóa đơn, đăng ký xe, gửi phản ánh và nhận thông báo.</p>
            </div>
        </div>
    </div>
</section>

{{-- Bảng tin --}}
<section id="bang-tin" class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Bảng tin</h2>
            <p class="text-gray-500 mb-6">Thông tin mới nhất từ ban quản lý</p>
            <a href="{{ route('bang-tin.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                Xem tất cả
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        @if($dsBangTin->isEmpty())
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <p class="text-gray-500 font-medium">Hiện chưa có bài viết nào.</p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($dsBangTin as $bt)
            <a href="{{ route('bang-tin.show', $bt) }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col">
                <h3 class="font-bold text-gray-900 text-base mb-3 line-clamp-2 leading-snug">{{ $bt->tieu_de }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed line-clamp-4 flex-1">{{ Str::limit(strip_tags($bt->noi_dung), 160) }}</p>
                <div class="mt-5 pt-4 border-t border-gray-100 space-y-1.5">
                    <div class="flex items-center gap-2 text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Đăng: {{ $bt->createdAt?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                    @if($bt->updatedAt && $bt->updatedAt->ne($bt->createdAt))
                    <div class="flex items-center gap-2 text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Cập nhật: {{ $bt->updatedAt->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>



{{-- Liên hệ --}}
@php
    // Các thuộc tính khác thuộc nhóm "Liên hệ" chưa được hiển thị riêng ở trên
    $explicitLienHeKeys = ['dia_chi', 'so_dien_thoai', 'hotline', 'email', 'gio_lam_viec', 'ban_do_google'];
    $chwLienHeKhac = $chwLienHe->reject(fn ($item) => in_array($item->ma_thuoc_tinh, $explicitLienHeKeys) || blank($item->gia_tri));

    // Google Map: chấp nhận cả iframe nhúng lẫn URL thuần
    $banDoRaw = optional($cauHinhWebsite->get('ban_do_google'))->gia_tri;
    $banDoSrc = null;
    if (filled($banDoRaw)) {
        if (preg_match('/src=["\']([^"\']+)["\']/i', $banDoRaw, $m)) {
            $banDoSrc = $m[1] !== '' ? $m[1] : null;
        } elseif (filter_var(trim($banDoRaw), FILTER_VALIDATE_URL)) {
            $banDoSrc = trim($banDoRaw);
        }
    }
@endphp
<section id="lien-he" class="py-20 bg-gray-50 dark:bg-gray-950 transition-colors border-t border-gray-200 dark:border-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $tenChungCu ?? 'Logo' }}" class="w-16 h-16 mx-auto mb-4 rounded-2xl object-cover shadow-sm">
            @else
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                </div>
            @endif
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Liên hệ{{ $tenChungCu ? ' — '.$tenChungCu : '' }}</h2>
            @if($moTaWebsite)
                <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">{{ $moTaWebsite }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
            {{-- Thông tin liên hệ --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8 {{ $banDoSrc ? '' : 'lg:col-span-2' }}">
                <ul class="space-y-5">
                    @if($diaChi)
                    <li class="flex items-start gap-4">
                        <span class="w-10 h-10 flex-shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Địa chỉ</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $diaChi }}</p>
                        </div>
                    </li>
                    @endif

                    @if($soDienThoai)
                    <li class="flex items-start gap-4">
                        <span class="w-10 h-10 flex-shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Số điện thoại</p>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $soDienThoai) }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">{{ $soDienThoai }}</a>
                        </div>
                    </li>
                    @endif

                    @if($hotline)
                    <li class="flex items-start gap-4">
                        <span class="w-10 h-10 flex-shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Hotline</p>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $hotline) }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">{{ $hotline }}</a>
                        </div>
                    </li>
                    @endif

                    @if($email)
                    <li class="flex items-start gap-4">
                        <span class="w-10 h-10 flex-shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Email</p>
                            <a href="mailto:{{ $email }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">{{ $email }}</a>
                        </div>
                    </li>
                    @endif

                    @if($gioLamViec)
                    <li class="flex items-start gap-4">
                        <span class="w-10 h-10 flex-shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Giờ làm việc</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $gioLamViec }}</p>
                        </div>
                    </li>
                    @endif

                    @foreach($chwLienHeKhac as $item)
                    <li class="flex items-start gap-4">
                        <span class="w-10 h-10 flex-shrink-0 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->ten_thuoc_tinh }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 whitespace-pre-line">{{ $item->gia_tri }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>

                @if($website || $chwMangXaHoi->isNotEmpty())
                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap gap-3">
                    @if($website)
                    <a href="{{ $website }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        Truy cập Website
                    </a>
                    @endif

                    @foreach($chwMangXaHoi as $item)
                        @continue(blank($item->gia_tri))
                        @php
                            $socialColor = match($item->ma_thuoc_tinh) {
                                'facebook' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 hover:bg-blue-100',
                                'zalo'     => 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400 hover:bg-sky-100',
                                'youtube'  => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 hover:bg-red-100',
                                'tiktok'   => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 hover:bg-gray-200',
                                default    => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 hover:bg-emerald-100',
                            };
                        @endphp
                        <a href="{{ $item->gia_tri }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl {{ $socialColor }} text-sm font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            {{ $item->ten_thuoc_tinh }}
                        </a>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Google Map --}}
            @if($banDoSrc)
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden min-h-[320px]">
                <iframe src="{{ $banDoSrc }}" class="w-full h-full min-h-[320px]" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            @endif
        </div>

       
    </div>
</section>

{{-- Footer --}}
@php
    $footerNavLinks = [
        ['label' => 'Trang chủ', 'href' => '#'],
        ['label' => 'Bảng tin', 'href' => route('bang-tin.index')],
        ['label' => 'Tính năng', 'href' => '#tinh-nang'],
        ['label' => 'Liên hệ', 'href' => '#lien-he'],
        ['label' => 'Đăng nhập', 'href' => route('login')],
    ];
@endphp
<footer class="bg-slate-900 text-gray-300 border-t border-slate-800 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            {{-- Cột 1: Giới thiệu --}}
            <div>
                <div class="flex items-center gap-3 mb-4">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $tenChungCu ?? 'Logo' }}" class="w-10 h-10 rounded-xl object-cover shadow-sm">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        </div>
                    @endif
                    <span class="font-bold text-white text-lg">{{ $tenChungCu }}</span>
                </div>
                @if($moTaWebsite)
                    <p class="text-sm text-gray-400 leading-relaxed">{{ $moTaWebsite }}</p>
                @endif
            </div>

            {{-- Cột 2: Liên kết nhanh --}}
            <div>
                <h3 class="text-white font-semibold mb-5">Liên kết nhanh</h3>
                <ul class="space-y-3 text-sm">
                    @foreach($footerNavLinks as $link)
                    <li>
                        <a href="{{ $link['href'] }}" class="inline-block text-gray-300 hover:text-emerald-400 hover:translate-x-1 transition-all duration-300">
                            {{ $link['label'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Cột 3: Thông tin liên hệ --}}
            @if($diaChi || $soDienThoai || $hotline || $email || $gioLamViec)
            <div>
                <h3 class="text-white font-semibold mb-5">Thông tin liên hệ</h3>
                <ul class="space-y-3 text-sm">
                    @if($diaChi)
                    <li class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        <span class="text-gray-300">{{ $diaChi }}</span>
                    </li>
                    @endif
                    @if($soDienThoai)
                    <li class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $soDienThoai) }}" class="text-gray-300 hover:text-emerald-400 transition-colors duration-300">{{ $soDienThoai }}</a>
                    </li>
                    @endif
                    @if($hotline)
                    <li class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $hotline) }}" class="text-gray-300 hover:text-emerald-400 transition-colors duration-300">Hotline: {{ $hotline }}</a>
                    </li>
                    @endif
                    @if($email)
                    <li class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        <a href="mailto:{{ $email }}" class="text-gray-300 hover:text-emerald-400 transition-colors duration-300 break-all">{{ $email }}</a>
                    </li>
                    @endif
                    @if($gioLamViec)
                    <li class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-gray-300">{{ $gioLamViec }}</span>
                    </li>
                    @endif
                </ul>
            </div>
            @endif

            {{-- Cột 4: Website & Mạng xã hội --}}
            @if($website || $chwMangXaHoi->isNotEmpty())
            <div>
                <h3 class="text-white font-semibold mb-5">Kết nối với chúng tôi</h3>
                <div class="flex flex-wrap gap-3">
                    @if($website)
                    <a href="{{ $website }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 text-gray-200 text-sm font-medium transition-all duration-300 hover:bg-emerald-600 hover:text-white hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        Website
                    </a>
                    @endif

                    @foreach($chwMangXaHoi as $item)
                        @continue(blank($item->gia_tri))
                        <a href="{{ $item->gia_tri }}" target="_blank" rel="noopener noreferrer"
                           title="{{ $item->ten_thuoc_tinh }}"
                           class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-800 text-gray-200 transition-all duration-300 hover:bg-emerald-600 hover:text-white hover:scale-105">
                            @switch($item->ma_thuoc_tinh)
                                @case('facebook')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.891h-2.33v6.987C18.343 21.128 22 16.991 22 12z"/></svg>
                                    @break
                                @case('zalo')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193a48.11 48.11 0 01-1.02.072v3.091l-3-3a48.6 48.6 0 01-4.02-.163 2.115 2.115 0 01-1.976-2.192v-4.286c0-1.136.847-2.1 1.98-2.193A48.855 48.855 0 0115.75 8.25c1.556 0 3.095.062 4.5.261z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 6.637c0-1.621 1.152-3.026 2.76-3.235A48.455 48.455 0 0113.5 3c2.115 0 4.198.137 6.24.402"/></svg>
                                    @break
                                @case('youtube')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M21.582 7.203a2.51 2.51 0 00-1.766-1.775C18.254 5 12 5 12 5s-6.254 0-7.816.428a2.51 2.51 0 00-1.766 1.775C2 8.769 2 12 2 12s0 3.231.418 4.797a2.51 2.51 0 001.766 1.775C5.746 19 12 19 12 19s6.254 0 7.816-.428a2.51 2.51 0 001.766-1.775C22 15.231 22 12 22 12s0-3.231-.418-4.797zM10 15.5v-7l6 3.5-6 3.5z"/></svg>
                                    @break
                                @case('tiktok')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M16.5 2.75c.36 2.31 1.87 3.86 4.5 4.02v2.86c-1.6.06-3.02-.43-4.24-1.34v6.02a5.55 5.55 0 11-5.55-5.55c.24 0 .48.02.72.05v2.9a2.65 2.65 0 102.13 2.6V2.75h2.44z"/></svg>
                                    @break
                                @default
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                            @endswitch
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Footer bottom --}}
    <div class="border-t border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex flex-col md:flex-row items-center justify-center md:justify-between gap-2 text-center">
            <p class="text-xs text-gray-400">{{ $banQuyen ?: '© 2026 Apartment Management System. All Rights Reserved.' }}</p>
            <p class="text-xs text-gray-500">Phiên bản v1.0.0 • Powered by Laravel 12 • Tailwind CSS</p>
        </div>
    </div>
</footer>

</body>
</html>
