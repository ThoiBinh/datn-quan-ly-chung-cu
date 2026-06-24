    <!DOCTYPE html>
<html lang="vi" x-data="{ sidebarOpen: true, mobileOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Quản Lý Chung Cư</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'w-64' : 'w-16'"
        class="hidden lg:flex flex-col bg-gradient-to-b from-slate-800 to-slate-900 text-white transition-all duration-300 ease-in-out flex-shrink-0"
    >
        <!-- Logo -->
        <div class="flex items-center h-16 px-4 border-b border-white/10">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                    <p class="text-sm font-bold text-white leading-tight">Urbano Admin</p>
                    <p class="text-xs text-slate-400">Quản trị hệ thống</p>
                </div>
            </div>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 overflow-y-auto sidebar-scroll p-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <span x-show="sidebarOpen" class="truncate">Dashboard</span>
            </a>

            <div x-show="sidebarOpen" class="pt-3 pb-1">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-2">Người dùng</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span x-show="sidebarOpen" class="truncate">Tài khoản</span>
            </a>
            <a href="{{ route('admin.nhan-vien.index') }}" class="sidebar-link {{ request()->routeIs('admin.nhan-vien.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span x-show="sidebarOpen" class="truncate">Nhân viên</span>
            </a>
            <a href="{{ route('admin.cu-dan.index') }}" class="sidebar-link {{ request()->routeIs('admin.cu-dan.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-show="sidebarOpen" class="truncate">Cư dân</span>
            </a>

            <div x-show="sidebarOpen" class="pt-3 pb-1">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-2">Chung cư</p>
            </div>
            <a href="{{ route('admin.toa-nha.index') }}" class="sidebar-link {{ request()->routeIs('admin.toa-nha.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span x-show="sidebarOpen" class="truncate">Tòa nhà</span>
            </a>
            <a href="{{ route('admin.can-ho.index') }}" class="sidebar-link {{ request()->routeIs('admin.can-ho.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="sidebarOpen" class="truncate">Căn hộ</span>
            </a>
            <a href="{{ route('admin.phuong-tien.index') }}" class="sidebar-link {{ request()->routeIs('admin.phuong-tien.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span x-show="sidebarOpen" class="truncate">Phương tiện</span>
            </a>

            <div x-show="sidebarOpen" class="pt-3 pb-1">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-2">Tài chính</p>
            </div>
            <a href="{{ route('admin.hoa-don.index') }}" class="sidebar-link {{ request()->routeIs('admin.hoa-don.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-show="sidebarOpen" class="truncate">Hóa đơn</span>
            </a>
            <a href="{{ route('admin.can-ho-phi-dich-vu.index') }}" class="sidebar-link {{ request()->routeIs('admin.can-ho-phi-dich-vu.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span x-show="sidebarOpen" class="truncate">Dịch vụ căn hộ</span>
            </a>

            <a href="{{ route('admin.cau-hinh-thanh-toan.index') }}" class="sidebar-link {{ request()->routeIs('admin.cau-hinh-thanh-toan.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span x-show="sidebarOpen" class="truncate">Cấu hình thanh toán</span>
            </a>

            <div x-show="sidebarOpen" class="pt-3 pb-1">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-2">Truyền thông</p>
            </div>
            <a href="{{ route('admin.thong-bao.index') }}" class="sidebar-link {{ request()->routeIs('admin.thong-bao.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span x-show="sidebarOpen" class="truncate">Thông báo</span>
            </a>
            <a href="{{ route('admin.bang-tin.index') }}" class="sidebar-link {{ request()->routeIs('admin.bang-tin.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <span x-show="sidebarOpen" class="truncate">Bảng tin</span>
            </a>

            <div x-show="sidebarOpen" class="pt-3 pb-1">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-2">Hệ thống</p>
            </div>
            <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span x-show="sidebarOpen" class="truncate">Nhật ký hệ thống</span>
            </a>
        </nav>

        <!-- User section -->
        <div class="p-3 border-t border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">
                    {{ strtoupper(substr(auth('nhanvien')->user()->name, 0, 1)) }}
                </div>
                <div x-show="sidebarOpen" class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth('nhanvien')->user()->name }}</p>
                    <p class="text-xs text-slate-400">Admin</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top navbar -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 lg:px-6 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden sm:block text-sm text-gray-500">{{ now()->format('d/m/Y') }}</span>
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-semibold text-sm">
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
