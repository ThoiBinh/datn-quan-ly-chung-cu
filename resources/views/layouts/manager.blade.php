<!DOCTYPE html>
<html lang="vi" x-data="{ sidebarOpen: true }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ban Quản Lý') - Quản Lý Chung Cư</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'"
           class="hidden lg:flex flex-col bg-gradient-to-b from-indigo-800 to-indigo-900 text-white transition-all duration-300 ease-in-out flex-shrink-0">
        <div class="flex items-center h-16 px-4 border-b border-white/10">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                </div>
                <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                    <p class="text-sm font-bold leading-tight">Urbano</p>
                    <p class="text-xs text-indigo-300">Ban quản lý</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto sidebar-scroll p-3 space-y-1">
            @php
                $navItems = [
                    ['route' => 'manager.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                    ['route' => 'manager.toa-nha.index', 'label' => 'Tòa nhà', 'icon' => 'building'],
                    ['route' => 'manager.can-ho.index', 'label' => 'Căn hộ', 'icon' => 'home'],
                    ['route' => 'manager.cu-dan.index', 'label' => 'Cư dân', 'icon' => 'users'],
                    ['route' => 'manager.phuong-tien.index', 'label' => 'Phương tiện', 'icon' => 'vehicle'],
                    ['route' => 'manager.hoa-don.index', 'label' => 'Hóa đơn', 'icon' => 'invoice'],
                    ['route' => 'manager.yeu-cau.index', 'label' => 'Phản ánh', 'icon' => 'chat'],
                    ['route' => 'manager.thong-bao.index', 'label' => 'Thông báo', 'icon' => 'bell'],
                    ['route' => 'manager.bang-tin.index', 'label' => 'Bảng tin', 'icon' => 'news'],
                    ['route' => 'manager.users.index', 'label' => 'Tài khoản', 'icon' => 'user-manage'],
                ];
                $icons = [
                    'dashboard'   => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                    'building'    => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    'home'        => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    'users'       => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    'vehicle'     => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                    'invoice'     => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'chat'        => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
                    'bell'        => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                    'news'        => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
                    'user-manage' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                ];
            @endphp

            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="sidebar-link {{ request()->routeIs($item['route']) ? 'active' : 'text-indigo-200' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$item['icon']] }}"/>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">{{ $item['label'] }}</span>
                </a>
            @endforeach

            {{-- Cấu hình --}}
            <div x-show="sidebarOpen" class="pt-3 pb-1">
                <p class="text-xs font-semibold text-indigo-400/70 uppercase tracking-wider px-2">Cấu hình</p>
            </div>
            <a href="{{ route('manager.cau-hinh-thanh-toan.index') }}"
               class="sidebar-link {{ request()->routeIs('manager.cau-hinh-thanh-toan.*') ? 'active' : 'text-indigo-200' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Cấu hình thanh toán</span>
            </a>
            <a href="{{ route('manager.phi-dich-vu.index') }}"
               class="sidebar-link {{ request()->routeIs('manager.phi-dich-vu.*') ? 'active' : 'text-indigo-200' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Phí dịch vụ</span>
            </a>
            <a href="{{ route('manager.thuoc-tinh.index') }}"
               class="sidebar-link {{ request()->routeIs('manager.thuoc-tinh.*') ? 'active' : 'text-indigo-200' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Thuộc tính</span>
            </a>
            <a href="{{ route('manager.vai-tro.index') }}"
               class="sidebar-link {{ request()->routeIs('manager.vai-tro.*') ? 'active' : 'text-indigo-200' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Vai trò</span>
            </a>
            <a href="{{ route('manager.loai-phuong-tien.index') }}"
               class="sidebar-link {{ request()->routeIs('manager.loai-phuong-tien.*') ? 'active' : 'text-indigo-200' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Loại phương tiện</span>
            </a>
            <a href="{{ route('manager.loai-yeu-cau.index') }}"
               class="sidebar-link {{ request()->routeIs('manager.loai-yeu-cau.*') ? 'active' : 'text-indigo-200' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Loại yêu cầu</span>
            </a>
            <a href="{{ route('manager.loai-can-ho.index') }}"
               class="sidebar-link {{ request()->routeIs('manager.loai-can-ho.*') ? 'active' : 'text-indigo-200' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Loại căn hộ</span>
            </a>
            <a href="{{ route('manager.loai-phi-dich-vu.index') }}"
               class="sidebar-link {{ request()->routeIs('manager.loai-phi-dich-vu.*') ? 'active' : 'text-indigo-200' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Loại phí dịch vụ</span>
            </a>
        </nav>

        <div class="p-3 border-t border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth('nhanvien')->user()->name, 0, 1)) }}
                </div>
                <div x-show="sidebarOpen" class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth('nhanvien')->user()->name }}</p>
                    <p class="text-xs text-indigo-300">Ban quản lý</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen">
                    @csrf
                    <button type="submit" class="text-indigo-300 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 lg:px-6 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <nav class="text-sm text-gray-500">
                    <span class="font-semibold text-gray-800">@yield('page-title', 'Dashboard')</span>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('manager.yeu-cau.index') }}" class="relative text-gray-500 hover:text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </a>
                <span class="text-sm text-gray-500 hidden sm:block">{{ now()->format('d/m/Y') }}</span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <span class="text-sm">{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-600">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <span class="text-sm">{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-600">✕</button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
