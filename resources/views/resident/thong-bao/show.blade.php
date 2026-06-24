@extends('layouts.resident')
@section('title', $thongBao->tieu_de)
@section('page-title', 'Thông báo')

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('resident.thong-bao.index') }}" class="hover:text-emerald-600 transition-colors">Thông báo</a>
        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 font-medium truncate max-w-xs">{{ $thongBao->tieu_de }}</span>
    </nav>

    {{-- CARD 1 — NỘI DUNG THÔNG BÁO --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 pt-6 pb-4 border-b border-gray-100">
            <div class="flex items-start justify-between gap-3">
                <h1 class="text-xl font-bold text-gray-900 leading-snug">{{ $thongBao->tieu_de }}</h1>
                @if($baiDoc)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 flex-shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Đã đọc
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 flex-shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Chưa đọc
                </span>
                @endif
            </div>
        </div>
        <div class="px-6 py-5">
            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $thongBao->noi_dung }}</div>
        </div>
    </div>

    {{-- CARD 2 — THÔNG TIN GỬI --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin gửi</h2>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide font-medium">Người gửi</p>
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs flex-shrink-0">
                        {{ strtoupper(substr($thongBao->nguoiTao?->ho_ten ?? $thongBao->nguoiTao?->name ?? 'HT', 0, 1)) }}
                    </div>
                    <p class="text-sm font-semibold text-gray-700">{{ $thongBao->nguoiTao?->ho_ten ?? $thongBao->nguoiTao?->name ?? 'Hệ thống' }}</p>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide font-medium">Ngày gửi</p>
                <p class="text-sm font-semibold text-gray-700">{{ $thongBao->getCreatedAtAttribute()?->format('d/m/Y H:i') ?? '—' }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $thongBao->getCreatedAtAttribute()?->diffForHumans() ?? '' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide font-medium">Mã thông báo</p>
                <p class="text-sm font-semibold text-gray-700 font-mono">#{{ $thongBao->id }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide font-medium">Đối tượng</p>
                <p class="text-sm font-semibold text-gray-700">Toàn bộ cư dân</p>
            </div>
        </div>
    </div>

    {{-- CARD 3 — TRẠNG THÁI ĐỌC --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Trạng thái đọc</h2>
        </div>
        <div class="px-6 py-5">
            @if($baiDoc && $baiDoc->read_at)
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-emerald-700">Đã đọc</p>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Lần đọc gần nhất: <span class="font-medium text-gray-700">{{ $baiDoc->read_at->format('H:i — d/m/Y') }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $baiDoc->read_at->diffForHumans() }}</p>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-0.5">thong_bao_id</p>
                    <p class="text-sm font-semibold text-gray-700 font-mono">{{ $baiDoc->thong_bao_id }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-0.5">cu_dan_id</p>
                    <p class="text-sm font-semibold text-gray-700 font-mono">{{ $baiDoc->cu_dan_id }}</p>
                </div>
            </div>
            @else
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-amber-700">Chưa đọc</p>
                    <p class="text-sm text-gray-500 mt-0.5">Thông báo này chưa được ghi nhận lần đọc.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Back link --}}
    <div>
        <a href="{{ route('resident.thong-bao.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Quay lại danh sách thông báo
        </a>
    </div>

</div>
@endsection
