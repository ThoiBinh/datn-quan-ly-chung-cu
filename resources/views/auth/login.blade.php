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
</head>
<body class="min-h-screen font-sans bg-gradient-to-br from-slate-100 via-blue-50 to-indigo-100 relative overflow-x-hidden">

    <!-- Background depth blobs -->
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-400/30 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-24 w-[28rem] h-[28rem] bg-indigo-400/25 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-1/4 w-80 h-80 bg-white/50 rounded-full blur-3xl"></div>
    </div>

    <div class="relative min-h-screen flex" x-data="{ loai: '{{ old('loai', 'nhanvien') }}' }">

        <!-- Left panel (desktop only) -->
        <div class="hidden lg:flex lg:w-[45%] relative flex-col justify-between overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 p-12">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute -top-24 -left-20 w-72 h-72 bg-blue-500/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 -right-16 w-80 h-80 bg-indigo-500/30 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10 flex items-center gap-3">
                @if($logoWebsite)
                    <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="w-11 h-11 rounded-xl object-cover bg-white/10 backdrop-blur">
                @else
                    <div class="inline-flex items-center justify-center w-11 h-11 bg-white/10 backdrop-blur rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                @endif
                <span class="text-white font-semibold text-lg tracking-tight">{{ $tenChungCu }}</span>
            </div>

            <div class="relative z-10">
                <h1 class="text-4xl font-bold text-white leading-tight">
                    {{ $tenChungCu }}
                </h1>
                <p class="mt-4 text-blue-200 text-base leading-relaxed max-w-sm">
                    {{ $moTaWebsite ?: 'Giúp Ban quản lý và cư dân kết nối, quản lý và vận hành chung cư trên cùng một nền tảng.' }}
                </p>

                <!-- Illustration: simple skyline -->
                <div class="mt-10 flex items-end gap-3 h-40">
                    <div class="w-14 h-24 bg-white/10 backdrop-blur rounded-lg"></div>
                    <div class="w-16 h-36 bg-white/15 backdrop-blur rounded-lg"></div>
                    <div class="w-14 h-28 bg-white/10 backdrop-blur rounded-lg"></div>
                    <div class="w-20 h-40 bg-gradient-to-t from-blue-500/30 to-white/10 backdrop-blur rounded-lg relative overflow-hidden">
                        <div class="absolute inset-3 grid grid-cols-2 gap-2">
                            <span class="w-2 h-2 bg-white/40 rounded-sm"></span>
                            <span class="w-2 h-2 bg-white/40 rounded-sm"></span>
                            <span class="w-2 h-2 bg-white/40 rounded-sm"></span>
                            <span class="w-2 h-2 bg-white/40 rounded-sm"></span>
                        </div>
                    </div>
                    <div class="w-14 h-20 bg-white/10 backdrop-blur rounded-lg"></div>
                </div>
            </div>

            <div class="relative z-10 text-blue-200/70 text-xs">
                © {{ $chwCopyright ?: (date('Y') . ' ' . $tenChungCu) }}
            </div>
        </div>

        <!-- Right panel -->
        <div class="w-full lg:w-[55%] flex items-center justify-center p-4 sm:p-8 lg:p-12">
            <div class="w-full max-w-md">

                <!-- Mobile-only compact header -->
                <div class="lg:hidden text-center mb-8">
                    @if($logoWebsite)
                        <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="w-14 h-14 rounded-2xl object-cover mx-auto mb-4 shadow-lg">
                    @else
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl mb-4 shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                    <h1 class="text-xl font-bold text-slate-800">{{ $tenChungCu }}</h1>
                </div>

                <!-- Glass card -->
                <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl shadow-blue-900/10 p-8 sm:p-10 transition-all duration-300">

                    <div class="hidden lg:flex items-center gap-2.5 mb-6">
                        @if($logoWebsite)
                            <img src="{{ $logoWebsite }}" alt="{{ $tenChungCu }}" class="w-9 h-9 rounded-lg object-cover">
                        @else
                            <div class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        @endif
                        <span class="text-slate-700 font-semibold">{{ $tenChungCu }}</span>
                    </div>

                    <!-- Tab chọn loại tài khoản -->
                    <div class="flex rounded-full bg-slate-100 p-1 mb-6">
                        <button type="button"
                                @click="loai = 'nhanvien'"
                                :class="loai === 'nhanvien' ? 'bg-white shadow text-blue-600 font-semibold' : 'text-slate-500'"
                                class="flex-1 py-2 text-sm rounded-full transition-all duration-300">
                            Admin / Ban quản lý
                        </button>
                        <button type="button"
                                @click="loai = 'cudan'"
                                :class="loai === 'cudan' ? 'bg-white shadow text-blue-600 font-semibold' : 'text-slate-500'"
                                class="flex-1 py-2 text-sm rounded-full transition-all duration-300">
                            Cư dân
                        </button>
                    </div>

                    <h2 class="text-xl font-bold text-slate-800 mb-1">Đăng nhập</h2>
                    <p class="text-slate-500 text-sm mb-6">Đăng nhập để sử dụng hệ thống quản lý chung cư.</p>

                    @if($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-3">
                            @foreach($errors->all() as $error)
                                <p class="text-red-700 text-sm">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-3">
                            <p class="text-red-700 text-sm">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 rounded-xl p-3">
                            <p class="text-green-700 text-sm">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Field ẩn loại tài khoản -->
                        <input type="hidden" name="loai" :value="loai">

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                       class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:shadow-lg focus:shadow-blue-500/10 transition-all duration-300"
                                       placeholder="name@example.com">
                            </div>
                        </div>

                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Mật khẩu</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z"/>
                                    </svg>
                                </span>
                                <input :type="show ? 'text' : 'password'" name="password" required
                                       class="w-full pl-11 pr-11 py-3 border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:shadow-lg focus:shadow-blue-500/10 transition-all duration-300"
                                       placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors duration-200">
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer select-none">
                                <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>

                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-500/30 active:scale-[0.98] active:translate-y-0 text-white font-semibold py-3 rounded-xl text-sm transition-all duration-300 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Đăng nhập
                        </button>
                    </form>
                </div>

                <p class="text-center text-slate-500 text-xs mt-6">
                    © {{ date('Y') }} {{ $tenChungCu }}. {{ $chwCopyright ?: 'Hệ thống quản lý chung cư.' }}
                </p>
            </div>
        </div>
    </div>

</body>
</html>
