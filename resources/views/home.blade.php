<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Chung Cư</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white" x-data="{ mobileMenu: false }">

{{-- Navigation --}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="font-bold text-gray-900 text-lg">ChungCư Pro</span>
            </div>
            <div class="hidden md:flex items-center gap-8">
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

{{-- CTA --}}
<section id="lien-he" class="py-20 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-center">
    <div class="max-w-2xl mx-auto px-4">
        <h2 class="text-3xl font-bold mb-4">Sẵn sàng trải nghiệm?</h2>
        <p class="text-emerald-100 mb-8">Đăng nhập ngay để khám phá đầy đủ tính năng của hệ thống quản lý chung cư hiện đại.</p>
        <a href="{{ route('login') }}" class="inline-block px-8 py-3.5 bg-white text-emerald-700 font-bold rounded-xl shadow-lg hover:shadow-xl hover:bg-emerald-50 transition">
            Đăng nhập ngay
        </a>
    </div>
</section>

{{-- Footer --}}
<footer class="py-8 bg-gray-900 text-center text-gray-500 text-sm">
    <p>&copy; {{ date('Y') }} ChungCư Pro — Hệ thống quản lý căn hộ chung cư</p>
</footer>

</body>
</html>
