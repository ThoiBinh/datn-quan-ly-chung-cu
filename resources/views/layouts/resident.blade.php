<!DOCTYPE html>
<html lang="vi" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cư Dân') - Quản Lý Chung Cư</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-50 font-sans antialiased">

<!-- Top Navigation -->
<nav class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                </div>
                <span class="font-bold text-gray-800">Urbano</span>
            </div>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('resident.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('resident.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Dashboard</a>
                <a href="{{ route('resident.hoa-don.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('resident.hoa-don.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Hóa đơn</a>
                <a href="{{ route('resident.phuong-tien.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('resident.phuong-tien.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Phương tiện</a>
                <a href="{{ route('resident.yeu-cau.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('resident.yeu-cau.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Phản ánh</a>
                <a href="{{ route('resident.thong-bao.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('resident.thong-bao.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Thông báo</a>
            </div>

            <div class="flex items-center gap-3">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-semibold text-sm">
                            {{ strtoupper(substr(auth('cudan')->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden sm:block font-medium">{{ auth('cudan')->user()->name }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="{{ route('resident.profile.show') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Hồ sơ cá nhân
                        </a>
                        <a href="{{ route('password.change') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Đổi mật khẩu
                        </a>
                        <hr class="my-1 border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-green-600 hover:text-green-800">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center justify-between">
            <span class="text-sm font-medium">{{ session('error') }}</span>
            <button @click="show = false" class="text-red-600">✕</button>
        </div>
    @endif

    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-white border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-gray-500">
        © {{ date('Y') }} Urbano - Hệ thống quản lý chung cư. All rights reserved.
    </div>
</footer>

</body>
</html>
