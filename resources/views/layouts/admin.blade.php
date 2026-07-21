    <!DOCTYPE html>
<html lang="vi" x-data="{ sidebarOpen: true, mobileOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Quản Lý Chung Cư</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        // Đọc bởi resources/js/booking-toast.js (Toast Notification realtime) —
        // đặt TRƯỚC thẻ Vite vì module app.js chạy deferred, còn script thường này
        // chạy ngay khi parse tới, đảm bảo window.AppUser luôn sẵn sàng trước.
        window.AppUser = { loai: 'nhanvien', id: {{ (int) auth('nhanvien')->id() }} };
        window.DatLichTienIchRoutes = {
            show: {!! \Illuminate\Support\Js::from(route('admin.dat-lich-tien-ich.show', ['datLichTienIch' => '__ID__'])) !!},
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Mobile overlay (closes sidebar on outside click) -->
    <div
        x-show="mobileOpen"
        x-cloak
        x-transition:enter="transition-opacity ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileOpen = false"
        class="fixed inset-0 z-30 bg-slate-950/60 backdrop-blur-sm lg:hidden"
    ></div>

    <!-- Sidebar -->
    <aside
        x-init="$el.style.width = ''"
        :class="[sidebarOpen ? 'w-64' : 'w-20', mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']"
        style="width: 16rem"
        class="fixed inset-y-0 left-0 z-40 my-4 ml-4 flex flex-col rounded-3xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-white shadow-2xl shadow-black/20 ring-1 ring-white/10 backdrop-blur-xl transition-all duration-300 ease-in-out lg:static dark:shadow-black/40 flex-shrink-0"
    >
        <!-- Logo -->
        <div
            x-init="$el.style.paddingLeft = ''; $el.style.paddingRight = ''"
            :class="sidebarOpen ? 'justify-start px-5' : 'justify-center px-0'"
            style="padding-left: 1.25rem; padding-right: 1.25rem"
            class="flex h-16 items-center border-b border-white/5 transition-all duration-300">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-950/50">
                    @if($logoWebsite)
                        <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="h-9 w-9 rounded-xl object-cover">
                    @else
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    @endif
                </div>
                <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                    <p class="truncate text-sm font-bold leading-tight tracking-wide text-white">{{ $tenChungCu }}</p>
                    @if($moTaWebsite)
                        <p class="truncate text-[11px] text-slate-400">{{ $moTaWebsite }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Nav Links -->
        <nav id="sidebarNav" class="sidebar-scroll flex-1 space-y-1 overflow-y-auto overflow-x-hidden p-4">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link group {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Dashboard</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Dashboard</span>
            </a>

            <div x-show="sidebarOpen" x-transition class="px-3 pb-1 pt-4">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500">Người dùng</p>
            </div>
            <div x-show="!sidebarOpen" x-cloak class="mx-3 my-2 border-t border-white/5"></div>

            <a href="{{ route('admin.users.index') }}" class="sidebar-link group {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Tài khoản</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Tài khoản</span>
            </a>
            <a href="{{ route('admin.nhan-vien.index') }}" class="sidebar-link group {{ request()->routeIs('admin.nhan-vien.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Nhân viên</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Nhân viên</span>
            </a>
            <a href="{{ route('admin.cu-dan.index') }}" class="sidebar-link group {{ request()->routeIs('admin.cu-dan.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Cư dân</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Cư dân</span>
            </a>

            <div x-show="sidebarOpen" x-transition class="px-3 pb-1 pt-4">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500">Chung cư</p>
            </div>
            <div x-show="!sidebarOpen" x-cloak class="mx-3 my-2 border-t border-white/5"></div>

            <a href="{{ route('admin.toa-nha.index') }}" class="sidebar-link group {{ request()->routeIs('admin.toa-nha.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Tòa nhà</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Tòa nhà</span>
            </a>
            <a href="{{ route('admin.can-ho.index') }}" class="sidebar-link group {{ request()->routeIs('admin.can-ho.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Căn hộ</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Căn hộ</span>
            </a>
            <a href="{{ route('admin.cu-dan-can-ho.index') }}" class="sidebar-link group {{ request()->routeIs('admin.cu-dan-can-ho.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7v12a2 2 0 002 2h10a2 2 0 002-2V7M9 11h6M9 15h4"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Cư dân - Căn hộ</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Cư dân - Căn hộ</span>
            </a>
            <a href="{{ route('admin.phuong-tien.index') }}" class="sidebar-link group {{ request()->routeIs('admin.phuong-tien.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Phương tiện</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Phương tiện</span>
            </a>
            <a href="{{ route('admin.yeu-cau.index') }}" class="sidebar-link group {{ request()->routeIs('admin.yeu-cau.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Yêu cầu cư dân</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Yêu cầu cư dân</span>
            </a>
            <a href="{{ route('admin.dat-lich-tien-ich.index') }}" class="sidebar-link group {{ request()->routeIs('admin.dat-lich-tien-ich.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Đặt lịch tiện ích</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Đặt lịch tiện ích</span>
            </a>

            <div x-show="sidebarOpen" x-transition class="px-3 pb-1 pt-4">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500">Tài chính</p>
            </div>
            <div x-show="!sidebarOpen" x-cloak class="mx-3 my-2 border-t border-white/5"></div>

            <a href="{{ route('admin.hoa-don.index') }}" class="sidebar-link group {{ request()->routeIs('admin.hoa-don.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Hóa đơn</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Hóa đơn</span>
            </a>
            <a href="{{ route('admin.phi-dich-vu.index') }}" class="sidebar-link group {{ request()->routeIs('admin.phi-dich-vu.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Phí dịch vụ</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Phí dịch vụ</span>
            </a>
            <a href="{{ route('admin.can-ho-phi-dich-vu.index') }}" class="sidebar-link group {{ request()->routeIs('admin.can-ho-phi-dich-vu.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Dịch vụ căn hộ</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Dịch vụ căn hộ</span>
            </a>

            <div x-show="sidebarOpen" x-transition class="px-3 pb-1 pt-4">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500">Truyền thông</p>
            </div>
            <div x-show="!sidebarOpen" x-cloak class="mx-3 my-2 border-t border-white/5"></div>

            <a href="{{ route('admin.thong-bao.index') }}" class="sidebar-link group {{ request()->routeIs('admin.thong-bao.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Thông báo</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Thông báo</span>
            </a>
            <a href="{{ route('admin.bang-tin.index') }}" class="sidebar-link group {{ request()->routeIs('admin.bang-tin.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Bảng tin</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Bảng tin</span>
            </a>

            <div x-show="sidebarOpen" x-transition class="px-3 pb-1 pt-4">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500">Cấu hình</p>
            </div>
            <div x-show="!sidebarOpen" x-cloak class="mx-3 my-2 border-t border-white/5"></div>

            <a href="{{ route('admin.cau-hinh-thanh-toan.index') }}" class="sidebar-link group {{ request()->routeIs('admin.cau-hinh-thanh-toan.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Cấu hình thanh toán</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Cấu hình thanh toán</span>
            </a>
            <a href="{{ route('admin.cau-hinh-website.index') }}" class="sidebar-link group {{ request()->routeIs('admin.cau-hinh-website.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.49l1.216.455c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Cấu hình website</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Cấu hình website</span>
            </a>

            <div x-show="sidebarOpen" x-transition class="px-3 pb-1 pt-4">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500">Hệ thống</p>
            </div>
            <div x-show="!sidebarOpen" x-cloak class="mx-3 my-2 border-t border-white/5"></div>

            <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-link group {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Nhật ký hệ thống</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Nhật ký hệ thống</span>
            </a>
            <a href="{{ route('admin.import.index') }}" class="sidebar-link group {{ request()->routeIs('admin.import.*') ? 'active' : '' }}" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">Nhập dữ liệu</span>
                <span x-show="!sidebarOpen" x-cloak class="sidebar-tooltip">Import dữ liệu</span>
            </a>
        </nav>

        <script>
            (function () {
                var sidebarNav = document.getElementById('sidebarNav');
                if (!sidebarNav) return;
                var storageKey = 'admin_sidebar_scroll_top';
                var saved = sessionStorage.getItem(storageKey);
                if (saved !== null) {
                    sidebarNav.scrollTop = parseInt(saved, 10) || 0;
                }
                var ticking = false;
                sidebarNav.addEventListener('scroll', function () {
                    if (ticking) return;
                    ticking = true;
                    window.requestAnimationFrame(function () {
                        sessionStorage.setItem(storageKey, sidebarNav.scrollTop);
                        ticking = false;
                    });
                }, { passive: true });
            })();
        </script>

        <!-- User section -->
        <div class="border-t border-white/5 p-3">
            <div :class="sidebarOpen ? '' : 'justify-center'" class="flex items-center gap-3 rounded-2xl bg-slate-800/60 p-2 transition-colors duration-300 hover:bg-slate-800/80">
                <div class="relative flex-shrink-0">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 text-sm font-bold text-white shadow-md ring-2 ring-white/10">
                        {{ strtoupper(substr(auth('nhanvien')->user()->name, 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-emerald-400 ring-2 ring-slate-950"></span>
                </div>
                <div x-show="sidebarOpen" x-transition class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-white">{{ auth('nhanvien')->user()->name }}</p>
                    <p class="text-xs text-slate-400">Admin</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen" x-transition class="group relative flex-shrink-0">
                    @csrf
                    <button type="submit" class="rounded-lg p-2 text-slate-400 transition-all duration-300 hover:bg-red-500/10 hover:text-red-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                    <span class="pointer-events-none absolute bottom-full right-0 z-50 mb-2 whitespace-nowrap rounded-lg bg-slate-800 px-2.5 py-1.5 text-xs font-medium text-white opacity-0 shadow-lg ring-1 ring-white/10 transition-opacity duration-300 group-hover:opacity-100">Đăng xuất</span>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main content -->
    <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
        <!-- Top navbar -->
        <header class="flex h-16 flex-shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm lg:px-6">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="hidden h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition-all duration-300 hover:bg-indigo-50 hover:text-indigo-600 lg:flex">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <button @click="mobileOpen = !mobileOpen" class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition-all duration-300 hover:bg-indigo-50 hover:text-indigo-600 lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden text-sm text-gray-500 sm:block">{{ now()->format('d/m/Y') }}</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-100 to-violet-100 text-sm font-semibold text-indigo-700 shadow-sm ring-2 ring-white">
                    {{ strtoupper(substr(auth('nhanvien')->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <span class="text-sm">{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-600 hover:text-green-800">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <span class="text-sm">{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-600 hover:text-red-800">✕</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
