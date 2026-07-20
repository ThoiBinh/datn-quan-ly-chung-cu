<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bảng tin') - {{ $tenChungCu }}</title>
    @hasSection('meta-description')
        <meta name="description" content="@yield('meta-description')">
    @endif
    @hasSection('og-image')
        <meta property="og:image" content="@yield('og-image')">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

<nav class="sticky top-0 z-50 bg-emerald-500 backdrop-blur border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                @if($logoWebsite)
                    <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="w-8 h-8 rounded-lg object-cover">
                @else
                    <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                @endif
                <span class="font-bold text-gray-900 text-lg">{{ $tenChungCu }}</span>
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-black-500 text-sm font-medium hover:bg-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m0 0h18"/></svg>
                Trang chủ
            </a>
        </div>
    </div>
</nav>

<main>
    @yield('content')
</main>

<footer class="bg-slate-900 text-gray-400 py-8 mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center text-xs">
        &copy; {{ now()->year }} {{ $tenChungCu }}. All Rights Reserved.
    </div>
</footer>

</body>
</html>
