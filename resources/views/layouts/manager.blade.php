<!DOCTYPE html>
<html lang="vi" x-data="{ sidebarOpen: true, mobileSidebarOpen: false }">
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
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Mobile overlay -->
    <div x-show="mobileSidebarOpen"
         x-cloak
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm lg:hidden"></div>

    <!-- Sidebar -->
    <aside
        :class="[
            sidebarOpen ? 'lg:w-[280px]' : 'lg:w-[88px]',
            mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]"
        class="fixed inset-y-0 left-0 z-50 flex w-[280px] flex-col overflow-hidden rounded-r-3xl border-r border-slate-800 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 shadow-2xl transition-all duration-300 ease-in-out lg:static">

        <!-- Logo -->
        <div class="flex-shrink-0 p-4">
            <div class="flex items-center gap-3 rounded-2xl bg-slate-800/50 p-4 ring-1 ring-white/5">
                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-950/50">
                    @if($logoWebsite)
                        <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="h-11 w-11 rounded-xl object-cover">
                    @else
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M13.5 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/>
                        </svg>
                    @endif
                </div>
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-out duration-200 delay-100"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="min-w-0 overflow-hidden">
                    <p class="truncate text-sm font-bold leading-tight text-white">{{ $tenChungCu }}</p>
                    @if($moTaWebsite)
                        <p class="truncate text-[11px] text-slate-400">{{ $moTaWebsite }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <div class="flex-shrink-0 px-4 pb-2">
            <div class="flex items-center gap-3 rounded-2xl bg-slate-800/40 p-3 ring-1 ring-white/5">
                <div class="relative flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-400 to-blue-600 text-sm font-bold text-white ring-2 ring-white/20">
                    {{ strtoupper(substr(auth('nhanvien')->user()->name, 0, 1)) }}
                </div>
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-out duration-200 delay-100"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="min-w-0 flex-1 overflow-hidden">
                    <p class="truncate text-sm font-semibold text-white">{{ auth('nhanvien')->user()->name }}</p>
                    <span class="mt-0.5 inline-flex items-center gap-1 rounded-full bg-blue-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-blue-300 ring-1 ring-inset ring-blue-500/30">
                        Manager
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen">
                    @csrf
                    <button type="submit" title="Đăng xuất" class="rounded-lg p-1.5 text-slate-400 transition-colors duration-200 hover:bg-white/10 hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0110.5 3h6a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-6a2.25 2.25 0 01-2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Menu -->
        <nav id="sidebarNav" class="sidebar-scroll flex-1 space-y-5 overflow-y-auto px-3 pb-3">
            @php
                $navGroups = [
                    'TỔNG QUAN' => [
                        ['route' => 'manager.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                    ],
                    'QUẢN LÝ CHUNG CƯ' => [
                        ['route' => 'manager.toa-nha.index', 'label' => 'Tòa nhà', 'icon' => 'building'],
                        ['route' => 'manager.can-ho.index', 'label' => 'Căn hộ', 'icon' => 'home'],
                        ['route' => 'manager.cu-dan.index', 'label' => 'Cư dân', 'icon' => 'users'],
                        ['route' => 'manager.phuong-tien.index', 'label' => 'Phương tiện', 'icon' => 'vehicle'],
                    ],
                    'QUẢN LÝ TÀI CHÍNH' => [
                        ['route' => 'manager.hoa-don.index', 'label' => 'Hóa đơn', 'icon' => 'invoice'],
                    ],
                    'QUẢN LÝ DỊCH VỤ' => [
                        ['route' => 'manager.phi-dich-vu.index', 'label' => 'Phí dịch vụ', 'icon' => 'wallet', 'active' => 'manager.phi-dich-vu.*'],
                        ['route' => 'manager.can-ho-phi-dich-vu.index', 'label' => 'Phí DV căn hộ', 'icon' => 'creditcard', 'active' => 'manager.can-ho-phi-dich-vu.*'],
                    ],
                    'QUẢN LÝ VẬN HÀNH' => [
                        ['route' => 'manager.yeu-cau.index', 'label' => 'Phản ánh', 'icon' => 'chat'],
                        ['route' => 'manager.dat-lich-tien-ich.index', 'label' => 'Đặt lịch tiện ích', 'icon' => 'calendar', 'active' => 'manager.dat-lich-tien-ich.*'],
                        ['route' => 'manager.thong-bao.index', 'label' => 'Thông báo', 'icon' => 'bell'],
                        ['route' => 'manager.bang-tin.index', 'label' => 'Bảng tin', 'icon' => 'news'],
                    ],
                    'QUẢN LÝ NHÂN SỰ' => [
                        ['route' => 'manager.nhan-vien.index', 'label' => 'Nhân viên', 'icon' => 'staff'],
                    ],
                    'HỆ THỐNG' => [
                        ['route' => 'manager.cau-hinh-thanh-toan.index', 'label' => 'Cấu hình thanh toán', 'icon' => 'settings', 'active' => 'manager.cau-hinh-thanh-toan.*'],
                        ['route' => 'manager.cau-hinh-website.index', 'label' => 'Cấu hình website', 'icon' => 'settings', 'active' => 'manager.cau-hinh-website.*'],
                    ],
                ];
                $icons = [
                    'dashboard'  => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
                    'building'   => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M13.5 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21',
                    'home'       => 'M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125V21M3 9l9-6 9 6m-1.5-.75V21a.75.75 0 01-.75.75H4.5A.75.75 0 013.75 21V8.25',
                    'users'      => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                    'vehicle'    => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v11.177m0-11.177L12.62 4.16a1.125 1.125 0 00-1.24 0L8.5 7.573m8-.001V16.5m-8-8.927V16.5m0 0h8',
                    'invoice'    => 'M9 12h3.75M9 15h3.75M9 18h3.75M6.75 4.5h6.879a1.5 1.5 0 011.06.44l4.622 4.62a1.5 1.5 0 01.439 1.061V19.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25z',
                    'chat'       => 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z',
                    'bell'       => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0',
                    'news'       => 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z',
                    'staff'      => 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0',
                    'settings'   => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.49l1.216.455c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
                    'wallet'     => 'M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9A2.25 2.25 0 0018.75 6.75H5.25A2.25 2.25 0 003 9v3',
                    'calendar'   => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0V11.25A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008z',
                    'creditcard' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a1.5 1.5 0 001.5-1.5V6.75a1.5 1.5 0 00-1.5-1.5h-15a1.5 1.5 0 00-1.5 1.5v10.5a1.5 1.5 0 001.5 1.5z',
                ];
            @endphp

            @foreach($navGroups as $groupLabel => $items)
                <div>
                    <p x-show="sidebarOpen"
                       x-transition:enter="transition ease-out duration-200 delay-100"
                       x-transition:enter-start="opacity-0"
                       x-transition:enter-end="opacity-100"
                       class="mb-2 truncate px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        {{ $groupLabel }}
                    </p>
                    <div class="space-y-1">
                        @foreach($items as $item)
                            @php $isActive = request()->routeIs($item['active'] ?? $item['route']); @endphp
                            <div class="group relative">
                                <a href="{{ route($item['route']) }}"
                                   class="relative flex items-center gap-3 overflow-hidden rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                                          {{ $isActive
                                                ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-950/40'
                                                : 'text-slate-300 hover:bg-slate-800 hover:text-white hover:shadow-sm' }}">
                                    @if($isActive)
                                        <span class="absolute left-0 top-0 h-full w-1 rounded-r bg-blue-300"></span>
                                    @endif
                                    <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-110 {{ $isActive ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}"/>
                                    </svg>
                                    <span x-show="sidebarOpen"
                                          x-transition:enter="transition ease-out duration-200 delay-100"
                                          x-transition:enter-start="opacity-0"
                                          x-transition:enter-end="opacity-100"
                                          class="truncate">{{ $item['label'] }}</span>
                                </a>
                                <span x-show="!sidebarOpen"
                                      x-cloak
                                      class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 hidden -translate-y-1/2 whitespace-nowrap rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-medium text-white opacity-0 shadow-lg ring-1 ring-white/10 transition-opacity duration-200 group-hover:opacity-100 lg:block">
                                    {{ $item['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <script>
            (function () {
                var sidebarNav = document.getElementById('sidebarNav');
                if (!sidebarNav) return;
                var storageKey = 'manager_sidebar_scroll_top';
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

        <!-- Footer -->
        <div class="flex-shrink-0 p-3">
            <div class="flex items-center gap-3 rounded-2xl bg-slate-800/50 p-3 ring-1 ring-white/5">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-slate-700/60 text-blue-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/>
                    </svg>
                </div>
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-out duration-200 delay-100"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="min-w-0 overflow-hidden">
                    <p class="truncate text-xs font-semibold text-white">{{ $tenChungCu }}</p>
                    <p class="truncate text-[11px] text-slate-400">v1.0.0 &middot; Build 2026</p>
                </div>
            </div>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
        <div class="flex-shrink-0 px-4 pt-4 lg:px-6">
            <header class="flex h-16 items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 shadow-sm lg:px-6">
                <div class="flex items-center gap-4">
                    <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="rounded-xl p-2 text-slate-500 transition-colors duration-200 hover:bg-slate-100 hover:text-slate-800 lg:hidden">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
                    </button>
                    <button @click="sidebarOpen = !sidebarOpen" class="hidden rounded-xl p-2 text-slate-500 transition-colors duration-200 hover:bg-slate-100 hover:text-slate-800 lg:block">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
                    </button>
                    
                    <nav class="text-sm text-gray-500">
                        <span class="font-semibold text-gray-800">@yield('page-title', 'Dashboard')</span>
                    </nav>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <span class="hidden text-sm text-slate-500 sm:block">{{ now()->format('d/m/Y') }}</span>
                    <a href="{{ route('manager.yeu-cau.index') }}" class="relative rounded-xl p-2 text-slate-500 transition-colors duration-200 hover:bg-slate-100 hover:text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                        <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-blue-600 ring-2 ring-white"></span>
                    </a>
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-400 to-blue-600 text-xs font-bold text-white ring-2 ring-slate-100">
                        {{ strtoupper(substr(auth('nhanvien')->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>
        </div>

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
