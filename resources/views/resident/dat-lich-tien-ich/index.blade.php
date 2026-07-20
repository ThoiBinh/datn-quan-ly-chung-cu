@extends('layouts.resident')
@section('title', 'Lịch sử đặt tiện ích')
@section('page-title', 'Lịch sử đặt tiện ích')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-lg font-bold text-gray-800 dark:text-white">Lịch sử đặt tiện ích</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400">Toàn bộ lượt đặt lịch tiện ích của bạn.</p>
        </div>
        <a href="{{ route('resident.tien-ich.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Đặt lịch mới
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div class="sm:col-span-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã đặt lịch, tên tiện ích..."
                   class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
        </div>
        <div>
            <select name="trang_thai" class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="">-- Tất cả trạng thái --</option>
                @foreach($dsTrangThai as $id => $label)
                <option value="{{ $id }}" {{ (string) request('trang_thai') === (string) $id ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <input type="date" name="ngay_su_dung" value="{{ request('ngay_su_dung') }}"
                   class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
            <button type="submit" class="flex-shrink-0 px-4 py-2.5 bg-gray-800 hover:bg-gray-900 dark:bg-slate-600 dark:hover:bg-slate-500 text-white text-sm font-medium rounded-lg transition-colors">Lọc</button>
        </div>
    </form>

    @if($dsDatLich->isEmpty())
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-12 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <p class="text-gray-500 dark:text-slate-400 font-medium">Bạn chưa có lượt đặt lịch nào</p>
        <p class="text-sm text-gray-400 dark:text-slate-500 mt-1">Bắt đầu đặt lịch sử dụng tiện ích chung cư ngay.</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($dsDatLich as $dl)
        <a href="{{ route('resident.dat-lich-tien-ich.show', $dl) }}"
           class="block bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 dark:text-white truncate">{{ $dl->tienIch?->ten_tien_ich ?? '—' }}</p>
                        <p class="text-xs text-gray-400 dark:text-slate-500 font-mono">{{ $dl->ma_dat_lich }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $dl->trang_thai_label['class'] }}">{{ $dl->trang_thai_label['text'] }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-1 mt-3 text-xs text-gray-500 dark:text-slate-400">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $dl->thoi_gian_bat_dau?->format('H:i, d/m/Y') }} — {{ $dl->thoi_gian_ket_thuc?->format('H:i') }}
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ $dl->so_nguoi }} người
                </span>
                <span class="font-semibold text-gray-700 dark:text-slate-200">{{ number_format((float) $dl->phi_su_dung, 0, ',', '.') }} đ</span>
                <span>{{ $dl->createdAt?->format('d/m/Y H:i') }}</span>
            </div>
        </a>
        @endforeach
    </div>

    <div>{{ $dsDatLich->links() }}</div>
    @endif

</div>
@endsection
