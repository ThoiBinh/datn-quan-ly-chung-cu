<!DOCTYPE html>
<html lang="vi" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cư Dân') - {{ $tenChungCu }}</title>
    @if($chwSeoDescription || $moTaWebsite)
        <meta name="description" content="{{ $chwSeoDescription ?: $moTaWebsite }}">
    @endif
    @if($chwSeoKeywords)
        <meta name="keywords" content="{{ $chwSeoKeywords }}">
    @endif
    @if($chwFaviconUrl)
        <link rel="icon" href="{{ $chwFaviconUrl }}">
    @endif
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
                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center overflow-hidden"
                     @if($chwMauChinh) style="background-color: {{ $chwMauChinh }}" @endif>
                    @if($logoWebsite)
                        <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="w-8 h-8 object-cover rounded-lg">
                    @else
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                        </svg>
                    @endif
                </div>
                <span class="font-bold text-gray-800">{{ $tenChungCu }}</span>
            </div>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('resident.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('resident.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Dashboard</a>
                <a href="{{ route('resident.hoa-don.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('resident.hoa-don.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Hóa đơn</a>
                <a href="{{ route('resident.tien-ich.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('resident.tien-ich.*') || request()->routeIs('resident.dat-lich-tien-ich.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Tiện ích</a>
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
                        <a href="{{ route('resident.profile.change-password') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
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
<footer class="bg-slate-900 border-t border-slate-800 mt-12" x-data="{ showMap: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <!-- Cột 1: Giới thiệu -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center overflow-hidden flex-shrink-0"
                         @if($chwMauChinh) style="background-color: {{ $chwMauChinh }}" @endif>
                        @if($logoWebsite)
                            <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="w-10 h-10 object-cover rounded-xl">
                        @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                            </svg>
                        @endif
                    </div>
                    <span class="font-bold text-white text-base">{{ $tenChungCu }}</span>
                </div>
                @if($chwSeoDescription || $moTaWebsite)
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $chwSeoDescription ?: $moTaWebsite }}</p>
                @endif
                @if($chwMaSoThue)
                    <p class="text-gray-500 text-xs mt-3">Mã số thuế: {{ $chwMaSoThue }}</p>
                @endif
            </div>

            <!-- Cột 2: Liên kết nhanh -->
            <div>
                <p class="font-semibold text-white mb-4 text-sm uppercase tracking-wide">Liên kết nhanh</p>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('resident.dashboard') }}" class="inline-block text-gray-400 hover:text-emerald-400 hover:translate-x-1 transition-all duration-300">Dashboard</a></li>
                    <li><a href="{{ route('resident.hoa-don.index') }}" class="inline-block text-gray-400 hover:text-emerald-400 hover:translate-x-1 transition-all duration-300">Hóa đơn</a></li>
                    <li><a href="{{ route('resident.phuong-tien.index') }}" class="inline-block text-gray-400 hover:text-emerald-400 hover:translate-x-1 transition-all duration-300">Phương tiện</a></li>
                    <li><a href="{{ route('resident.yeu-cau.index') }}" class="inline-block text-gray-400 hover:text-emerald-400 hover:translate-x-1 transition-all duration-300">Phản ánh</a></li>
                    <li><a href="{{ route('resident.thong-bao.index') }}" class="inline-block text-gray-400 hover:text-emerald-400 hover:translate-x-1 transition-all duration-300">Thông báo</a></li>
                </ul>
            </div>

            <!-- Cột 3: Liên hệ -->
            @if($chwDiaChi || $chwHotline || $chwSoDienThoai || $chwEmail || $chwWebsiteUrl || $chwGioLamViec)
            <div>
                <p class="font-semibold text-white mb-4 text-sm uppercase tracking-wide">Liên hệ</p>
                <ul class="space-y-2.5 text-sm text-gray-400">
                    @if($chwDiaChi)
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ $chwDiaChi }}</span>
                        </li>
                    @endif
                    @if($chwHotline)
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $chwHotline) }}" class="hover:text-emerald-400 transition-colors duration-300">{{ $chwHotline }}</a>
                        </li>
                    @endif
                    @if($chwSoDienThoai)
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $chwSoDienThoai) }}" class="hover:text-emerald-400 transition-colors duration-300">{{ $chwSoDienThoai }}</a>
                        </li>
                    @endif
                    @if($chwEmail)
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:{{ $chwEmail }}" class="hover:text-emerald-400 transition-colors duration-300">{{ $chwEmail }}</a>
                        </li>
                    @endif
                    @if($chwWebsiteUrl)
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.6 9h16.8M3.6 15h16.8M11.5 3a17 17 0 000 18M12.5 3a17 17 0 010 18"/></svg>
                            <a href="{{ $chwWebsiteUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-400 transition-colors duration-300 truncate">{{ $chwWebsiteUrl }}</a>
                        </li>
                    @endif
                    @if($chwGioLamViec)
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $chwGioLamViec }}</span>
                        </li>
                    @endif
                </ul>
            </div>
            @endif

            <!-- Cột 4: Kết nối -->
            @if($chwFacebook || $chwZalo || $chwYoutube || $chwBanDoSrc)
            <div>
                <p class="font-semibold text-white mb-4 text-sm uppercase tracking-wide">Kết nối</p>
                <div class="flex flex-wrap gap-2">
                    @if($chwFacebook)
                        <a href="{{ $chwFacebook }}" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 text-gray-300 text-xs font-medium shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:text-emerald-400">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.987C18.343 21.128 22 16.991 22 12z"/></svg>
                            Facebook
                        </a>
                    @endif
                    @if($chwZalo)
                        <a href="{{ $chwZalo }}" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 text-gray-300 text-xs font-medium shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Zalo
                        </a>
                    @endif
                    @if($chwYoutube)
                        <a href="{{ $chwYoutube }}" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 text-gray-300 text-xs font-medium shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:text-emerald-400">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a2.994 2.994 0 00-2.107-2.117C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.391.569A2.994 2.994 0 00.502 6.186 31.16 31.16 0 000 12a31.16 31.16 0 00.502 5.814 2.994 2.994 0 002.107 2.117C4.495 20.5 12 20.5 12 20.5s7.505 0 9.391-.569a2.994 2.994 0 002.107-2.117A31.16 31.16 0 0024 12a31.16 31.16 0 00-.502-5.814zM9.75 15.568V8.432L15.818 12 9.75 15.568z"/></svg>
                            Youtube
                        </a>
                    @endif
                </div>
                @if($chwBanDoSrc)
                    <button type="button" @click="showMap = !showMap"
                            class="mt-3 flex items-center gap-1.5 text-xs font-medium text-emerald-400 hover:text-emerald-300 transition-colors duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        <span x-text="showMap ? 'Ẩn bản đồ' : 'Xem bản đồ'"></span>
                    </button>
                    <div x-show="showMap" x-cloak x-transition class="mt-3 rounded-xl overflow-hidden border border-slate-800">
                        <iframe src="{{ $chwBanDoSrc }}" class="w-full h-40" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Footer Bottom -->
        <div class="mt-10 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
            <p class="text-gray-400 text-sm">
                {{ $chwCopyright ?: '© '.date('Y').' '.$tenChungCu.'. All Rights Reserved.' }}
            </p>
            <p class="text-gray-500 text-xs">
                Phiên bản v1.0.0 • Powered by Laravel 12 • Tailwind CSS
            </p>
        </div>
    </div>
</footer>

</body>
</html>
