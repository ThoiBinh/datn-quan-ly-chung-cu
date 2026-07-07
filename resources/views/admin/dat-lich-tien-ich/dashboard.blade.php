@extends('layouts.admin')
@section('title', 'Thống kê đặt lịch tiện ích')
@section('page-title', 'Thống kê đặt lịch tiện ích')

@section('content')
@php
    $cards = [
        ['label' => 'Tổng booking', 'value' => $thongKe['tong'], 'color' => 'indigo', 'fmt' => 'int', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => 'Hôm nay', 'value' => $thongKe['hom_nay'], 'color' => 'blue', 'fmt' => 'int', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Tuần này', 'value' => $thongKe['tuan'], 'color' => 'cyan', 'fmt' => 'int', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => 'Tháng này', 'value' => $thongKe['thang'], 'color' => 'violet', 'fmt' => 'int', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => 'Chờ duyệt', 'value' => $thongKe['cho_duyet'], 'color' => 'amber', 'fmt' => 'int', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Đã duyệt', 'value' => $thongKe['da_duyet'], 'color' => 'emerald', 'fmt' => 'int', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Đã hủy', 'value' => $thongKe['da_huy'], 'color' => 'gray', 'fmt' => 'int', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
        ['label' => 'Hoàn thành', 'value' => $thongKe['hoan_thanh'], 'color' => 'teal', 'fmt' => 'int', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Doanh thu', 'value' => $thongKe['doanh_thu'], 'color' => 'green', 'fmt' => 'money', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2m0-12a9 9 0 100 18 9 9 0 000-18z'],
        ['label' => 'Doanh thu tháng '.now()->format('m/Y'), 'value' => $thongKe['doanh_thu_thang'], 'color' => 'lime', 'fmt' => 'money', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
    ];
    $colorMap = [
        'indigo'  => ['light' => 'bg-indigo-50 dark:bg-indigo-900/20', 'text' => 'text-indigo-600 dark:text-indigo-400'],
        'blue'    => ['light' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-600 dark:text-blue-400'],
        'cyan'    => ['light' => 'bg-cyan-50 dark:bg-cyan-900/20', 'text' => 'text-cyan-600 dark:text-cyan-400'],
        'violet'  => ['light' => 'bg-violet-50 dark:bg-violet-900/20', 'text' => 'text-violet-600 dark:text-violet-400'],
        'amber'   => ['light' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-600 dark:text-amber-400'],
        'emerald' => ['light' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-600 dark:text-emerald-400'],
        'gray'    => ['light' => 'bg-gray-100 dark:bg-slate-700', 'text' => 'text-gray-600 dark:text-slate-400'],
        'teal'    => ['light' => 'bg-teal-50 dark:bg-teal-900/20', 'text' => 'text-teal-600 dark:text-teal-400'],
        'green'   => ['light' => 'bg-green-50 dark:bg-green-900/20', 'text' => 'text-green-600 dark:text-green-400'],
        'lime'    => ['light' => 'bg-lime-50 dark:bg-lime-900/20', 'text' => 'text-lime-600 dark:text-lime-400'],
    ];

    $trangThaiLabels = ['Chờ duyệt', 'Đã duyệt', 'Từ chối', 'Đã hủy', 'Hoàn thành'];
    $trangThaiValues = [
        $thongKe['cho_duyet'], $thongKe['da_duyet'], $thongKe['tu_choi'], $thongKe['da_huy'], $thongKe['hoan_thanh'],
    ];
    $trangThaiColors = ['#f59e0b', '#10b981', '#ef4444', '#94a3b8', '#6366f1'];
@endphp

<div class="space-y-5">

    <!-- Header -->
    <div class="flex flex-col justify-between gap-4 rounded-2xl bg-white dark:bg-slate-800 p-5 shadow-sm lg:flex-row lg:items-center">
        <div>
            <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400 mb-1">
                <a href="{{ route('admin.dat-lich-tien-ich.index') }}" class="hover:text-indigo-600 transition-colors">Đặt lịch tiện ích</a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-700 dark:text-slate-200">Thống kê</span>
            </nav>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Thống kê đặt lịch tiện ích</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Tổng quan số liệu và biểu đồ toàn bộ hệ thống đặt lịch</p>
        </div>
        <a href="{{ route('admin.dat-lich-tien-ich.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors self-start lg:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Về danh sách
        </a>
    </div>

    <!-- 10 Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach($cards as $card)
        @php $c = $colorMap[$card['color']]; @endphp
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400">{{ $card['label'] }}</p>
                <div class="{{ $c['light'] }} p-2 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
            @if($card['fmt'] === 'money')
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($card['value'], 0, ',', '.') }}<span class="text-xs font-medium text-gray-400 dark:text-slate-500"> đ</span></p>
            @else
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($card['value']) }}</p>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Charts row 1: xu hướng theo tháng -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white mb-1">Booking theo tháng</h2>
            <p class="text-xs text-gray-400 dark:text-slate-500 mb-4">Số lượt đặt lịch trong 12 tháng gần nhất</p>
            <div class="relative" style="height: 260px;">
                <canvas id="datLichBookingThangChart"
                    data-labels='{{ json_encode($nhanThang, JSON_UNESCAPED_UNICODE) }}'
                    data-values='{{ json_encode($bookingTheoThang) }}'></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white mb-1">Doanh thu theo tháng</h2>
            <p class="text-xs text-gray-400 dark:text-slate-500 mb-4">Doanh thu (Đã duyệt + Hoàn thành) trong 12 tháng gần nhất</p>
            <div class="relative" style="height: 260px;">
                <canvas id="datLichDoanhThuThangChart"
                    data-labels='{{ json_encode($nhanThang, JSON_UNESCAPED_UNICODE) }}'
                    data-values='{{ json_encode($doanhThuTheoThang) }}'></canvas>
            </div>
        </div>
    </div>

    <!-- Charts row 2: phân bố -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white mb-1">Trạng thái booking</h2>
            <p class="text-xs text-gray-400 dark:text-slate-500 mb-4">Phân bố toàn bộ lượt đặt theo trạng thái</p>
            <div class="relative" style="height: 260px;">
                <canvas id="datLichTrangThaiChart"
                    data-labels='{{ json_encode($trangThaiLabels, JSON_UNESCAPED_UNICODE) }}'
                    data-values='{{ json_encode($trangThaiValues) }}'
                    data-colors='{{ json_encode($trangThaiColors) }}'></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white mb-1">Top 5 tiện ích</h2>
            <p class="text-xs text-gray-400 dark:text-slate-500 mb-4">Tiện ích được đặt nhiều nhất</p>
            @if($topTienIch->isEmpty())
            <div class="flex items-center justify-center text-sm text-gray-400 dark:text-slate-500" style="height: 260px;">Chưa có dữ liệu</div>
            @else
            <div class="relative" style="height: 260px;">
                <canvas id="datLichTopTienIchChart"
                    data-labels='{{ json_encode($topTienIch->pluck('ten'), JSON_UNESCAPED_UNICODE) }}'
                    data-values='{{ json_encode($topTienIch->pluck('so_luong')) }}'></canvas>
            </div>
            @endif
        </div>
    </div>

    <!-- Charts row 3: top loai & top toa nha -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white mb-1">Top 5 loại tiện ích</h2>
            <p class="text-xs text-gray-400 dark:text-slate-500 mb-4">Nhóm tiện ích được đặt nhiều nhất</p>
            @if($topLoaiTienIch->isEmpty())
            <div class="flex items-center justify-center text-sm text-gray-400 dark:text-slate-500" style="height: 260px;">Chưa có dữ liệu</div>
            @else
            <div class="relative" style="height: 260px;">
                <canvas id="datLichTopLoaiTienIchChart"
                    data-labels='{{ json_encode($topLoaiTienIch->pluck('ten'), JSON_UNESCAPED_UNICODE) }}'
                    data-values='{{ json_encode($topLoaiTienIch->pluck('so_luong')) }}'></canvas>
            </div>
            @endif
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white mb-1">Top 5 tòa nhà</h2>
            <p class="text-xs text-gray-400 dark:text-slate-500 mb-4">Tòa nhà có nhiều lượt đặt lịch nhất (theo căn hộ gắn kèm)</p>
            @if($topToaNha->isEmpty())
            <div class="flex items-center justify-center text-sm text-gray-400 dark:text-slate-500" style="height: 260px;">Chưa có dữ liệu</div>
            @else
            <div class="relative" style="height: 260px;">
                <canvas id="datLichTopToaNhaChart"
                    data-labels='{{ json_encode($topToaNha->pluck('ten'), JSON_UNESCAPED_UNICODE) }}'
                    data-values='{{ json_encode($topToaNha->pluck('so_luong')) }}'></canvas>
            </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush
@endsection
