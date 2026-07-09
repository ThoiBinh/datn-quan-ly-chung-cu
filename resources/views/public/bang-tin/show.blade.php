@extends('layouts.public')

@section('title', $bangTin->tieu_de)
@section('meta-description', Str::limit(strip_tags($bangTin->noi_dung), 160))
@if($bangTin->hinh_url_full)
@section('og-image', $bangTin->hinh_url_full)
@endif

@section('content')
@php
    // Nội dung được nhập qua ô textarea thường (không có trình soạn thảo rich-text),
    // nên trong hầu hết trường hợp đây là văn bản thuần. Nếu tác giả từng chèn HTML thật
    // (ví dụ <h2>, <p>...) thì hiển thị nguyên vẹn HTML đó; ngược lại tự bọc đoạn văn cho dễ đọc.
    $noiDungRaw = $bangTin->noi_dung ?? '';
    $hasHtml    = $noiDungRaw !== '' && preg_match('/<(p|h[1-6]|ul|ol|li|div|br|table)[\s>]/i', $noiDungRaw);
    $tocItems   = [];

    if ($hasHtml) {
        $dem = 0;
        $noiDungHtml = preg_replace_callback('/<h([2-3])([^>]*)>(.*?)<\/h\1>/is', function ($m) use (&$tocItems, &$dem) {
            $dem++;
            $id = 'muc-' . $dem;
            $tocItems[] = ['id' => $id, 'level' => (int) $m[1], 'text' => trim(strip_tags($m[3]))];
            return '<h' . $m[1] . ' id="' . $id . '"' . $m[2] . '>' . $m[3] . '</h' . $m[1] . '>';
        }, $noiDungRaw);
    } else {
        $doan = collect(preg_split('/\n\s*\n/', trim($noiDungRaw)))->filter(fn ($d) => trim($d) !== '');
        $noiDungHtml = $doan->isNotEmpty()
            ? $doan->map(fn ($d) => '<p>' . nl2br(e(trim($d))) . '</p>')->implode('')
            : '';
    }

    $tinMoiNhat = $baiLienQuan->take(5);
    $ngayCapNhatKhac = $bangTin->nguoiCapNhat && $bangTin->nguoi_cap_nhat !== $bangTin->nguoi_tao;
@endphp

<div x-data="{ sidebarMobileOpen: false, backToTop: false }"
     x-init="window.addEventListener('scroll', () => backToTop = window.scrollY > 500)">

    {{-- Breadcrumb --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-6">
        <nav class="flex items-center gap-1.5 text-sm text-gray-500 overflow-x-auto whitespace-nowrap">
            <a href="{{ route('home') }}" class="flex items-center gap-1 hover:text-emerald-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Trang chủ
            </a>
            <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('bang-tin.index') }}" class="hover:text-emerald-600 transition-colors">Bảng tin</a>
            <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium truncate max-w-xs">{{ $bangTin->tieu_de }}</span>
        </nav>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="grid grid-cols-1 md:grid-cols-10 lg:grid-cols-4 gap-8">

            {{-- ============ NỘI DUNG CHÍNH (75%) ============ --}}
            <article class="md:col-span-7 lg:col-span-3 min-w-0">

                <a href="{{ route('bang-tin.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-300 mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m0 0h18"/></svg>
                    Quay lại Bảng tin
                </a>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight text-gray-900 mb-5">
                    {{ $bangTin->tieu_de }}
                </h1>

                {{-- Meta --}}
                <div class="flex flex-wrap items-center gap-2.5 mb-7">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold ring-1 ring-emerald-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $bangTin->nguoiTao?->ho_ten ?? 'Ban quản lý' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ optional($bangTin->createdAt)->format('d/m/Y H:i') ?? '—' }}
                    </span>
                    @if($ngayCapNhatKhac)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold ring-1 ring-amber-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Cập nhật {{ $bangTin->updatedAt ? \Illuminate\Support\Carbon::parse($bangTin->updatedAt)->format('d/m/Y H:i') : '—' }}
                    </span>
                    @endif
                </div>

                {{-- Ảnh đại diện --}}
                <div class="w-full h-[280px] sm:h-[400px] lg:h-[520px] rounded-3xl shadow-xl overflow-hidden bg-gray-100 mb-10">
                    @if($bangTin->hinh_url_full)
                        <img src="{{ $bangTin->hinh_url_full }}" alt="{{ $bangTin->tieu_de }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-50 to-teal-50">
                            <svg class="w-20 h-20 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Nội dung --}}
                <div class="prose prose-slate prose-lg max-w-none leading-8 tracking-wide text-gray-700 prose-headings:font-bold prose-headings:text-gray-900 prose-a:text-emerald-600">
                    @if($noiDungHtml !== '')
                        {!! $noiDungHtml !!}
                    @else
                        <p class="text-gray-400 italic">Chưa có nội dung.</p>
                    @endif
                </div>

                {{-- Chia sẻ (dưới nội dung, bản rút gọn cho mobile/desktop) --}}
                <div x-data="{ copied: false }" class="mt-10 pt-6 border-t border-gray-100 flex flex-wrap items-center gap-3">
                    <span class="text-sm font-semibold text-gray-500">Chia sẻ:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 hover:-translate-y-1 shadow-sm transition-all duration-300" title="Chia sẻ Facebook">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 9H15V6h-1.5A3.5 3.5 0 0010 9.5V11H8v3h2v7h3v-7h2.1l.4-3H13v-1a1 1 0 011-1z"/></svg>
                    </a>
                    <a href="https://zalo.me/share/link?u={{ urlencode(url()->current()) }}&title={{ urlencode($bangTin->tieu_de) }}" target="_blank" rel="noopener"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-sky-50 text-sky-600 hover:bg-sky-100 hover:-translate-y-1 shadow-sm transition-all duration-300" title="Chia sẻ Zalo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.684 18.523 12.478 22 17 22c1.5 0 3-.5 3-.5s-1-1.5-1-3.5c1.5-1 2-3 2-5 0-5.181-3.794-9-8.5-9S3 7.819 3 13c0 1.5.316 2.5.316 2.5"/></svg>
                    </a>
                    <a href="mailto:?subject={{ urlencode($bangTin->tieu_de) }}&body={{ urlencode(url()->current()) }}"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 hover:-translate-y-1 shadow-sm transition-all duration-300" title="Chia sẻ qua Email">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </a>
                    <button type="button"
                            @click="navigator.clipboard.writeText('{{ url()->current() }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="inline-flex items-center gap-1.5 px-3 h-9 rounded-full bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:-translate-y-1 shadow-sm transition-all duration-300 text-xs font-semibold">
                        <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/></svg>
                        <svg x-show="copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="copied ? 'Đã sao chép' : 'Copy link'"></span>
                    </button>
                </div>
            </article>

            {{-- ============ SIDEBAR (25%) ============ --}}
            <aside class="md:col-span-3 lg:col-span-1" x-data="{ sidebarOpen: false }">
                <button type="button" @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden w-full flex items-center justify-between px-4 py-3 mb-4 bg-white rounded-xl border border-gray-200 shadow-sm text-sm font-semibold text-gray-700">
                    <span>Nội dung liên quan</span>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div class="lg:sticky lg:top-24 space-y-6" :class="sidebarOpen ? 'block' : 'hidden lg:block'">

                    {{-- Mục lục (chỉ hiện khi bài viết có heading thật) --}}
                    @if(count($tocItems) > 1)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            Mục lục
                        </h3>
                        <ul class="space-y-2 text-sm">
                            @foreach($tocItems as $muc)
                            <li style="padding-left: {{ ($muc['level'] - 2) * 12 }}px">
                                <a href="#{{ $muc['id'] }}" class="text-gray-500 hover:text-emerald-600 transition-colors line-clamp-2">{{ $muc['text'] }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Tin mới nhất --}}
                    @if($tinMoiNhat->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Tin mới nhất
                        </h3>
                        <ul class="space-y-4">
                            @foreach($tinMoiNhat as $bt)
                            <li>
                                <a href="{{ route('bang-tin.show', $bt) }}" class="flex items-start gap-3 group">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                        @if($bt->hinh_url_full)
                                            <img src="{{ $bt->hinh_url_full }}" alt="{{ $bt->tieu_de }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-50 to-teal-50">
                                                <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 line-clamp-2 leading-snug group-hover:text-emerald-600 transition-colors">{{ $bt->tieu_de }}</p>
                                        <span class="text-xs text-gray-400 mt-1 block">{{ optional($bt->createdAt)->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                </div>
            </aside>
        </div>

        {{-- ============ BÀI VIẾT LIÊN QUAN ============ --}}
        @if($baiLienQuan->isNotEmpty())
        <div class="mt-16 pt-10 border-t border-gray-100">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-6">Bài viết liên quan</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($baiLienQuan as $bt)
                <a href="{{ route('bang-tin.show', $bt) }}"
                   class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col overflow-hidden">
                    <div class="h-40 bg-gray-100 overflow-hidden">
                        @if($bt->hinh_url_full)
                            <img src="{{ $bt->hinh_url_full }}" alt="{{ $bt->tieu_de }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-50 to-teal-50">
                                <svg class="w-10 h-10 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-bold text-gray-900 text-sm line-clamp-2 mb-2 leading-snug">{{ $bt->tieu_de }}</h3>
                        <div class="mt-auto pt-3 flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-xs text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ optional($bt->createdAt)->format('d/m/Y') ?? '—' }}
                            </span>
                            <span class="text-xs font-semibold text-emerald-600">Xem thêm &rarr;</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Back to top --}}
    <button type="button" @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            x-show="backToTop" x-transition.opacity
            class="fixed bottom-6 right-6 z-40 w-11 h-11 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg flex items-center justify-center transition-all duration-300 hover:-translate-y-1"
            title="Lên đầu trang">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
    </button>
</div>
@endsection
