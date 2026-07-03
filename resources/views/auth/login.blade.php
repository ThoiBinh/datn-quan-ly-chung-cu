<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - {{ $tenChungCu }}</title>
    @if(!empty($chwFaviconUrl))
        <link rel="icon" href="{{ $chwFaviconUrl }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @keyframes auroraFloat1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(6%,8%) scale(1.12)} }
        @keyframes auroraFloat2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-8%,6%) scale(1.08)} }
        @keyframes auroraFloat3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(5%,-7%) scale(1.15)} }
        @keyframes fadeInUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        @keyframes cardIn { from{opacity:0;transform:scale(.98) translateY(8px);filter:blur(6px)} to{opacity:1;transform:scale(1) translateY(0);filter:blur(0)} }
        @keyframes shimmerSweep { from{transform:translateX(-150%) skewX(-12deg)} to{transform:translateX(250%) skewX(-12deg)} }
        .aurora-1{ animation: auroraFloat1 26s ease-in-out infinite; }
        .aurora-2{ animation: auroraFloat2 32s ease-in-out infinite; }
        .aurora-3{ animation: auroraFloat3 38s ease-in-out infinite; }
        .fade-in-up{ animation: fadeInUp .6s cubic-bezier(.16,1,.3,1) both; }
        .card-in{ animation: cardIn .55s cubic-bezier(.16,1,.3,1) both; }
        .shimmer-sweep{ animation: shimmerSweep .9s ease-out; }
        .noise-overlay{
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='120'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
            background-repeat: repeat;
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-slate-50 dark:bg-slate-950 transition-colors duration-300">

    <div class="relative min-h-screen flex overflow-hidden" x-data="{ ready: false }" x-init="requestAnimationFrame(() => ready = true)">

        <!-- Global layered background -->
        <div aria-hidden="true" class="pointer-events-none fixed inset-0 overflow-hidden bg-gradient-to-br from-slate-100 via-blue-50 to-indigo-100 dark:from-slate-950 dark:via-indigo-950 dark:to-blue-950">
            <div class="aurora-1 absolute -top-40 -left-32 w-[32rem] h-[32rem] rounded-full bg-blue-400/25 dark:bg-blue-500/20 blur-[110px]"></div>
            <div class="aurora-2 absolute top-1/3 -right-40 w-[36rem] h-[36rem] rounded-full bg-violet-400/20 dark:bg-violet-500/20 blur-[120px]"></div>
            <div class="aurora-3 absolute bottom-0 left-1/4 w-[28rem] h-[28rem] rounded-full bg-cyan-400/20 dark:bg-cyan-400/10 blur-[100px]"></div>
            <div class="noise-overlay absolute inset-0 opacity-[0.035] mix-blend-overlay"></div>
        </div>

        <!-- LEFT: Brand experience (desktop only) -->
        <div class="hidden lg:flex lg:w-[42%] relative flex-col justify-between p-14 overflow-hidden">
            <!-- floating grid lines -->
            <div aria-hidden="true"
                 class="pointer-events-none absolute inset-0 opacity-[0.06] dark:opacity-[0.08]"
                 style="background-image:linear-gradient(to right, currentColor 1px, transparent 1px),linear-gradient(to bottom, currentColor 1px, transparent 1px); background-size:56px 56px; color:#64748b;"></div>

            <!-- abstract 3D-style shapes -->
            <div aria-hidden="true" class="pointer-events-none absolute -top-10 right-10 w-56 h-56 rounded-[2rem] border border-slate-900/10 dark:border-white/10 rotate-12 bg-gradient-to-br from-blue-500/10 to-transparent"></div>
            <div aria-hidden="true" class="pointer-events-none absolute bottom-24 -left-10 w-40 h-40 rounded-[1.75rem] border border-slate-900/10 dark:border-white/10 -rotate-6 bg-gradient-to-tr from-violet-500/10 to-transparent"></div>

            <div class="relative z-10 flex items-center gap-3 fade-in-up" style="animation-delay:.05s">
                @if($logoWebsite)
                    <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="w-10 h-10 rounded-xl object-cover shadow-lg shadow-blue-900/10">
                @else
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-900/5 dark:bg-white/10 backdrop-blur">
                        <svg class="w-5 h-5 text-slate-700 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                @endif
                <span class="text-slate-800 dark:text-white font-semibold tracking-tight">{{ $tenChungCu }}</span>
            </div>

            <div class="relative z-10 max-w-md">
                <h1 class="fade-in-up text-[2.75rem] leading-[1.08] font-bold tracking-tight text-slate-900 dark:text-white" style="animation-delay:.12s">
                    {{ $tenChungCu }}
                </h1>
                <p class="fade-in-up mt-5 text-[1.0625rem] leading-relaxed text-slate-500 dark:text-slate-400" style="animation-delay:.2s">
                    {{ $moTaWebsite ?: 'Kết nối Ban quản lý và cư dân trên một nền tảng vận hành duy nhất.' }}
                </p>
            </div>

            <div class="relative z-10 fade-in-up text-xs text-slate-400 dark:text-slate-500" style="animation-delay:.3s">
                © {{ $chwCopyright ?: (date('Y') . ' ' . $tenChungCu) }}
            </div>
        </div>

        <!-- RIGHT: Auth core -->
        <div class="relative w-full lg:w-[58%] flex items-center justify-center p-5 sm:p-10">

            <!-- light beam behind card -->
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 flex items-center justify-center">
                <div class="w-[36rem] h-[36rem] rounded-full bg-blue-500/10 dark:bg-blue-400/[0.08] blur-[100px]"></div>
            </div>

            <div class="relative z-10 w-full max-w-[26rem]"
                 x-show="ready" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                <!-- Mobile-only compact header -->
                <div class="lg:hidden text-center mb-8 fade-in-up">
                    @if($logoWebsite)
                        <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="w-14 h-14 rounded-2xl object-cover mx-auto mb-4 shadow-lg shadow-blue-900/10">
                    @else
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-600 mb-4 shadow-lg shadow-blue-900/20">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                    <h1 class="text-lg font-semibold text-slate-800 dark:text-white">{{ $tenChungCu }}</h1>
                </div>

                <!-- Ultra premium glass card -->
                <div class="card-in relative rounded-[1.75rem] p-8 sm:p-10
                            bg-white/80 dark:bg-white/[0.06]
                            backdrop-blur-xl
                            border border-slate-900/[0.06] dark:border-white/10
                            shadow-[0_1px_1px_rgba(15,23,42,0.03),0_8px_24px_-4px_rgba(15,23,42,0.08),0_24px_48px_-12px_rgba(15,23,42,0.12)]
                            dark:shadow-[0_1px_1px_rgba(0,0,0,0.4),0_8px_24px_-4px_rgba(0,0,0,0.5),0_24px_64px_-12px_rgba(0,0,0,0.6)]">

                    <div class="hidden lg:flex items-center gap-2.5 mb-7">
                        @if($logoWebsite)
                            <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="w-8 h-8 rounded-lg object-cover">
                        @else
                            <div class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-600">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        @endif
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ $tenChungCu }}</span>
                    </div>

                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-1.5">Chào mừng trở lại</h2>
                    <p class="text-[0.9375rem] text-slate-500 dark:text-slate-400 mb-7">Đăng nhập để tiếp tục sử dụng hệ thống quản lý chung cư.</p>

                    @if($errors->any())
                        <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 p-3.5">
                            @foreach($errors->all() as $error)
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 p-3.5">
                            <p class="text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 p-3.5">
                            <p class="text-sm text-emerald-600 dark:text-emerald-400">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Segmented control: Admin / Cư dân (native radios — works even before Alpine finishes loading) -->
                        <div class="relative flex rounded-full bg-slate-100/80 dark:bg-white/5 p-1 select-none"
                             x-data="{ loai: '{{ old('loai', 'nhanvien') }}' }">
                            <div class="absolute inset-y-1 left-1 w-[calc(50%-4px)] rounded-full bg-white dark:bg-white/10
                                        shadow-[0_1px_2px_rgba(15,23,42,0.06),0_4px_10px_-2px_rgba(15,23,42,0.10)]
                                        transition-transform duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                                 :class="loai === 'cudan' ? 'translate-x-full' : 'translate-x-0'"></div>

                            <label class="relative z-10 flex-1 py-2.5 text-sm text-center rounded-full cursor-pointer transition-colors duration-300 focus-within:ring-2 focus-within:ring-blue-500/40"
                                   :class="loai === 'nhanvien' ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-500 dark:text-slate-400'">
                                <input type="radio" name="loai" value="nhanvien" x-model="loai" class="sr-only"
                                       {{ old('loai', 'nhanvien') === 'nhanvien' ? 'checked' : '' }}>
                                Admin / Ban quản lý
                            </label>
                            <label class="relative z-10 flex-1 py-2.5 text-sm text-center rounded-full cursor-pointer transition-colors duration-300 focus-within:ring-2 focus-within:ring-blue-500/40"
                                   :class="loai === 'cudan' ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-500 dark:text-slate-400'">
                                <input type="radio" name="loai" value="cudan" x-model="loai" class="sr-only"
                                       {{ old('loai') === 'cudan' ? 'checked' : '' }}>
                                Cư dân
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                       class="w-full pl-11 pr-4 py-3 rounded-xl text-sm
                                              bg-white dark:bg-white/5
                                              text-slate-900 dark:text-white
                                              placeholder:text-slate-400 dark:placeholder:text-slate-500
                                              border {{ $errors->has('email') ? 'border-red-300 dark:border-red-500/40 bg-red-50/50 dark:bg-red-500/5' : 'border-slate-200 dark:border-white/10' }}
                                              hover:border-slate-300 dark:hover:border-white/20
                                              focus:outline-none focus:border-blue-500 dark:focus:border-blue-400
                                              focus:ring-4 focus:ring-blue-500/10 dark:focus:ring-blue-400/10
                                              focus:-translate-y-px
                                              transition-all duration-300"
                                       placeholder="name@example.com">
                            </div>
                        </div>

                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mật khẩu</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z"/>
                                    </svg>
                                </span>
                                <input :type="show ? 'text' : 'password'" name="password" required
                                       class="w-full pl-11 pr-11 py-3 rounded-xl text-sm
                                              bg-white dark:bg-white/5
                                              text-slate-900 dark:text-white
                                              placeholder:text-slate-400 dark:placeholder:text-slate-500
                                              border {{ $errors->has('password') ? 'border-red-300 dark:border-red-500/40 bg-red-50/50 dark:bg-red-500/5' : 'border-slate-200 dark:border-white/10' }}
                                              hover:border-slate-300 dark:hover:border-white/20
                                              focus:outline-none focus:border-blue-500 dark:focus:border-blue-400
                                              focus:ring-4 focus:ring-blue-500/10 dark:focus:ring-blue-400/10
                                              focus:-translate-y-px
                                              transition-all duration-300"
                                       placeholder="••••••••">
                                <button type="button" @click="show = !show"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors duration-200">
                                    <span class="relative block w-[18px] h-[18px]">
                                        <svg x-show="!show" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75" class="absolute inset-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="show" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75" class="absolute inset-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer select-none">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 dark:border-white/20 text-blue-600 focus:ring-blue-500/40 focus:ring-offset-0">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>

                        <button type="submit"
                                x-data="{ sweep: false }"
                                @mouseenter="sweep = true" @animationend="sweep = false"
                                class="group relative w-full h-12 overflow-hidden rounded-xl
                                       bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600
                                       text-white text-sm font-semibold
                                       shadow-[0_1px_0_rgba(255,255,255,0.15)_inset,0_8px_20px_-6px_rgba(79,70,229,0.5)]
                                       hover:shadow-[0_1px_0_rgba(255,255,255,0.2)_inset,0_12px_28px_-6px_rgba(79,70,229,0.65)]
                                       hover:-translate-y-0.5
                                       active:scale-[0.98] active:translate-y-0
                                       disabled:opacity-50 disabled:pointer-events-none
                                       transition-all duration-300
                                       flex items-center justify-center gap-2">
                            <span x-show="sweep" class="shimmer-sweep pointer-events-none absolute inset-y-0 left-0 w-1/3 bg-gradient-to-r from-transparent via-white/30 to-transparent"></span>
                            <svg class="w-4 h-4 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span class="relative">Đăng nhập</span>
                        </button>
                    </form>
                </div>

                <p class="text-center text-slate-400 dark:text-slate-500 text-xs mt-7">
                    © {{ date('Y') }} {{ $tenChungCu }}. {{ $chwCopyright ?: 'Hệ thống quản lý chung cư.' }}
                </p>
            </div>
        </div>
    </div>

</body>
</html>
