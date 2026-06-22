@extends('layouts.admin')
@section('title', 'Chi tiết cư dân')
@section('page-title', 'Chi tiết cư dân')

@section('content')
@php
    $isActive = $cuDan->trang_thai == 1;
    $hoTen    = trim(($cuDan->ho_ten_dem ?? '') . ' ' . ($cuDan->ten ?? ''));
    $gioiTinhMap = [1 => 'Nam', 0 => 'Nữ'];
    $diaChi = collect([$cuDan->dia_chi, $cuDan->xa, $cuDan->tinh])->filter()->implode(', ');
@endphp

<div class="max-w-4xl space-y-5"
     x-data="{
         confirmToggle: false,
         confirmAction: '{{ route('admin.cu-dan.toggle-status', $cuDan) }}'
     }">

    <!-- Header card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-xl font-bold text-emerald-700 dark:text-emerald-400 flex-shrink-0">
                {{ mb_strtoupper(mb_substr($hoTen, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $hoTen }}</h2>
                    @if(isset($cuDan->gioi_tinh) && $cuDan->gioi_tinh !== null)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $cuDan->gioi_tinh == 1 ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300' }}">
                        {{ $gioiTinhMap[$cuDan->gioi_tinh] }}
                    </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400">{{ $cuDan->email ?: 'Chưa có email' }}</p>
                @if($diaChi)
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">{{ $diaChi }}</p>
                @endif
            </div>
            @if($isActive)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400 flex-shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Hoạt động
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-xs font-semibold text-red-600 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 flex-shrink-0">
                <span class="w-2 h-2 rounded-full bg-red-500"></span> Đã khóa
            </span>
            @endif
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex flex-wrap gap-3">
            <a href="{{ route('admin.cu-dan.edit', $cuDan) }}"
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <button type="button" @click="confirmToggle = true"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $isActive ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600' }} text-white">
                @if($isActive)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Khóa tài khoản
                @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                Mở khóa
                @endif
            </button>
            <a href="{{ route('admin.cu-dan.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Detail cards -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Thông tin chi tiết</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            @php
            $fields = [
                ['label' => 'ID', 'value' => $cuDan->id, 'mono' => true],
                ['label' => 'Họ tên đệm', 'value' => $cuDan->ho_ten_dem],
                ['label' => 'Tên', 'value' => $cuDan->ten],
                ['label' => 'Email', 'value' => $cuDan->email],
                ['label' => 'Số điện thoại', 'value' => $cuDan->sdt],
                ['label' => 'CCCD', 'value' => $cuDan->cccd, 'mono' => true],
                ['label' => 'Ngày sinh', 'value' => $cuDan->ngay_sinh ? \Carbon\Carbon::parse($cuDan->ngay_sinh)->format('d/m/Y') : null],
                ['label' => 'Giới tính', 'value' => isset($cuDan->gioi_tinh) && $cuDan->gioi_tinh !== null ? ($gioiTinhMap[$cuDan->gioi_tinh] ?? '—') : null],
                ['label' => 'Tỉnh / Thành phố', 'value' => $cuDan->tinh],
                ['label' => 'Xã / Phường', 'value' => $cuDan->xa],
                ['label' => 'Địa chỉ chi tiết', 'value' => $cuDan->dia_chi],
                ['label' => 'Ngày tạo', 'value' => $cuDan->created_at?->format('d/m/Y H:i')],
                ['label' => 'Cập nhật bởi', 'value' => $cuDan->nguoi_cap_nhat],
            ];
            @endphp

            @foreach($fields as $f)
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">{{ $f['label'] }}</dt>
                <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200 {{ ($f['mono'] ?? false) ? 'font-mono' : '' }}">
                    {{ $f['value'] ?: '—' }}
                </dd>
            </div>
            @endforeach

            <!-- Trạng thái -->
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">Trạng thái</dt>
                <dd>
                    @if($isActive)
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Hoạt động
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-500 dark:text-red-400">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Đã khóa
                    </span>
                    @endif
                </dd>
            </div>

        </div>
    </div>

    <!-- Modal xác nhận toggle -->
    <div x-show="confirmToggle"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"  x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in  duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="confirmToggle = false" style="display:none">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            <!-- Header màu -->
            <div class="px-6 pt-6 pb-8 text-white {{ $isActive ? 'bg-gradient-to-r from-amber-500 to-orange-500' : 'bg-gradient-to-r from-emerald-500 to-teal-500' }}">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                        @if($isActive)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        @else
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        @endif
                    </div>
                    <button @click="confirmToggle = false" type="button"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <h3 class="text-lg font-bold text-white">{{ $isActive ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}</h3>
                <p class="text-sm text-white/75 mt-1">{{ $isActive ? 'Tài khoản sẽ bị vô hiệu hóa' : 'Tài khoản sẽ được kích hoạt lại' }}</p>
            </div>

            <!-- Body -->
            <div class="px-6 py-5">
                <!-- User info -->
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50 dark:bg-slate-700/60 border border-gray-100 dark:border-slate-600 mb-5">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-sm font-bold text-emerald-700 dark:text-emerald-400 flex-shrink-0">
                        {{ mb_strtoupper(mb_substr($hoTen, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400 dark:text-slate-500 uppercase tracking-wide">Cư dân</p>
                        <p class="font-semibold text-gray-800 dark:text-white truncate">{{ $hoTen }}</p>
                    </div>
                </div>

                <!-- Warning -->
                <div class="flex items-start gap-2.5 p-3.5 rounded-xl border text-sm
                    {{ $isActive ? 'bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800' : 'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800' }}">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 {{ $isActive ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="{{ $isActive ? 'text-amber-700 dark:text-amber-400' : 'text-emerald-700 dark:text-emerald-400' }}">
                        {{ $isActive
                            ? 'Cư dân sẽ không thể đăng nhập vào hệ thống sau khi bị khóa.'
                            : 'Cư dân sẽ có thể đăng nhập và sử dụng hệ thống sau khi mở khóa.' }}
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 mt-5">
                    <button @click="confirmToggle = false" type="button"
                            class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        Hủy bỏ
                    </button>
                    <form :action="confirmAction" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full py-2.5 text-white text-sm font-semibold rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2
                                    {{ $isActive ? 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-300' : 'bg-emerald-500 hover:bg-emerald-600 focus:ring-emerald-300' }}">
                            {{ $isActive ? 'Khóa tài khoản' : 'Mở khóa' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
