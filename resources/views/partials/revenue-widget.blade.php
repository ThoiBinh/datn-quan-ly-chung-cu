@php
    $growth = $tyLeTangTruong ?? 0;
    $growthColor = $growth > 0
        ? 'text-emerald-600 dark:text-emerald-400'
        : ($growth < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-slate-400');
    $growthBg = $growth > 0
        ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-900/40'
        : ($growth < 0 ? 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-900/40' : 'bg-gray-50 dark:bg-slate-700/40 border-gray-100 dark:border-slate-700');

    $duNo = $tongDuNo ?? 0;
    $duNoColor = $duNo == 0
        ? 'text-emerald-600 dark:text-emerald-400'
        : ($duNo < 100000000 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
    $duNoBg = $duNo == 0
        ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-900/40'
        : ($duNo < 100000000 ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-100 dark:border-amber-900/40' : 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-900/40');
@endphp

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Doanh thu {{ now()->year }}</h3>
            <p class="text-xs text-gray-400 dark:text-slate-400 mt-0.5">Tổng hợp doanh thu theo tháng</p>
        </div>

        <div class="flex items-center gap-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl px-4 py-2.5">
            <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/>
                </svg>
            </div>
            <div class="text-right">
                <p class="text-xs text-blue-600 dark:text-blue-300 font-medium">Tháng {{ now()->format('m') }}</p>
                <p class="text-base font-bold text-blue-700 dark:text-blue-200">{{ number_format($doanhThuThang, 0, ',', '.') }} đ</p>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
        <div class="rounded-xl border border-gray-100 dark:border-slate-700 p-3.5">
            <p class="text-xs text-gray-400 dark:text-slate-400 mb-1">Doanh thu tháng</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($doanhThuThang, 0, ',', '.') }}đ</p>
        </div>
        <div class="rounded-xl border border-gray-100 dark:border-slate-700 p-3.5">
            <p class="text-xs text-gray-400 dark:text-slate-400 mb-1">Tháng trước</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($doanhThuThangTruoc, 0, ',', '.') }}đ</p>
        </div>
        <div class="rounded-xl border border-gray-100 dark:border-slate-700 p-3.5">
            <p class="text-xs text-gray-400 dark:text-slate-400 mb-1">Tổng năm</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($tongDoanhThuNam, 0, ',', '.') }}đ</p>
        </div>
        <div class="rounded-xl border {{ $growthBg }} p-3.5">
            <p class="text-xs text-gray-400 dark:text-slate-400 mb-1">So với tháng trước</p>
            <p class="text-lg font-bold {{ $growthColor }} flex items-center gap-1">
                @if($growth > 0)
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                @elseif($growth < 0)
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                @endif
                {{ ($growth > 0 ? '+' : '') . number_format($growth, 1) }}%
            </p>
        </div>
        <div class="rounded-xl border {{ $duNoBg }} p-3.5 hover:shadow-sm transition-shadow duration-200 cursor-default"
             title="Tổng số tiền còn phải thu từ các hóa đơn chưa được thanh toán hoặc mới thanh toán một phần.">
            <div class="flex items-center gap-1.5 mb-1">
                <svg class="w-3.5 h-3.5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0018.75 4.5H5.25A2.25 2.25 0 003 6.75v10.5A2.25 2.25 0 005.25 19.5z"/>
                </svg>
                <p class="text-xs text-gray-400 dark:text-slate-400">Tổng dư nợ</p>
            </div>
            <p class="text-lg font-bold {{ $duNoColor }}">{{ $duNo > 0 ? number_format($duNo, 0, ',', '.') . 'đ' : '0 đ' }}</p>
            <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-0.5 leading-tight">Cập nhật theo hóa đơn chưa thanh toán</p>
        </div>
    </div>

    {{-- Chart --}}
    <div class="relative rounded-xl border border-gray-100 dark:border-slate-700 p-4" style="height: 300px;">
        <canvas id="revenueWidgetChart"
            data-labels='{{ json_encode($labels) }}'
            data-values='{{ json_encode($doanhThuNam) }}'
            data-du-no-values='{{ json_encode($duNoTheoThang) }}'></canvas>
    </div>
</div>
