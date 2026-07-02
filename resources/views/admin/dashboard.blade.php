@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Export buttons --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Tổng quan hệ thống</h2>
        <p class="text-xs text-gray-400 dark:text-slate-400 mt-0.5">Dữ liệu thống kê toàn bộ hệ thống</p>
    </div>
    @include('reports.export-modal', [
        'filterOptions' => $filterOptions,
        'pdfRoute'      => 'admin.dashboard.export.pdf',
        'excelRoute'    => 'admin.dashboard.export.excel',
        'darkMode'      => true,
    ])
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Tòa nhà', 'value' => $stats['tong_toa_nha'], 'color' => 'blue', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
            ['label' => 'Căn hộ', 'value' => $stats['tong_can_ho'], 'color' => 'indigo', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['label' => 'Cư dân', 'value' => $stats['tong_cu_dan'], 'color' => 'emerald', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'Nhân viên', 'value' => $stats['tong_nhan_vien'], 'color' => 'violet', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ];
        $colorMap = [
            'blue' => ['light' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-600 dark:text-blue-400'],
            'indigo' => ['light' => 'bg-indigo-50 dark:bg-indigo-900/20', 'text' => 'text-indigo-600 dark:text-indigo-400'],
            'emerald' => ['light' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-600 dark:text-emerald-400'],
            'violet' => ['light' => 'bg-violet-50 dark:bg-violet-900/20', 'text' => 'text-violet-600 dark:text-violet-400'],
        ];
    @endphp

    @foreach($cards as $card)
    @php $c = $colorMap[$card['color']]; @endphp
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400">{{ $card['label'] }}</p>
            <div class="{{ $c['light'] }} p-2 rounded-lg">
                <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($card['value']) }}</p>
    </div>
    @endforeach
</div>

<!-- Alert Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/40 rounded-2xl p-4 hover:shadow-sm transition-shadow duration-200">
        <div class="flex items-center gap-3">
            <div class="bg-amber-100 dark:bg-amber-900/30 p-2 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">Chưa thanh toán</p>
                <p class="text-xl font-bold text-amber-700 dark:text-amber-300">{{ $stats['hoa_don_chua_tt'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/40 rounded-2xl p-4 hover:shadow-sm transition-shadow duration-200">
        <div class="flex items-center gap-3">
            <div class="bg-red-100 dark:bg-red-900/30 p-2 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-red-600 dark:text-red-400 font-medium">Hóa đơn quá hạn</p>
                <p class="text-xl font-bold text-red-700 dark:text-red-300">{{ $stats['hoa_don_qua_han'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/40 rounded-2xl p-4 hover:shadow-sm transition-shadow duration-200">
        <div class="flex items-center gap-3">
            <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Yêu cầu mới</p>
                <p class="text-xl font-bold text-blue-700 dark:text-blue-300">{{ $stats['yeu_cau_moi'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Doanh thu -->
    <div class="lg:col-span-2">
        @include('partials.revenue-widget')
    </div>

    <!-- Recent Requests -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-5 shadow-sm">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Yêu cầu mới nhất</h3>
        <div class="space-y-2">
            @forelse($yeuCauMoi as $yc)
            <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-slate-700/40 hover:bg-gray-100 dark:hover:bg-slate-700/70 transition-colors duration-200">
                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-700 dark:text-blue-300 text-sm font-medium flex-shrink-0">
                    {{ strtoupper(substr($yc->cuDan?->ho_ten ?? 'N', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-800 dark:text-slate-100 truncate">{{ $yc->tieu_de }}</p>
                    <p class="text-xs text-gray-500 dark:text-slate-400">{{ $yc->cuDan?->ho_ten }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $yc->muc_do_label['class'] }} flex-shrink-0">
                    {{ $yc->muc_do_label['text'] }}
                </span>
            </div>
            @empty
            <p class="text-center text-gray-400 dark:text-slate-500 text-sm py-6">Không có yêu cầu mới</p>
            @endforelse
        </div>
        @if(count($yeuCauMoi))
        <a href="{{ route('admin.yeu-cau.index') }}" class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mt-4 font-medium">Xem tất cả →</a>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush
