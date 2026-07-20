@extends('layouts.resident')
@section('title', 'Tiện ích')
@section('page-title', 'Tiện ích tòa nhà')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-lg font-bold text-gray-800 dark:text-white">Tiện ích tòa nhà</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400">Xem thông tin và đặt lịch sử dụng tiện ích chung cư.</p>
        </div>
        <a href="{{ route('resident.dat-lich-tien-ich.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Lịch sử đặt của tôi
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div class="sm:col-span-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên tiện ích..."
                   class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
        </div>
        <div>
            <select name="loai_tien_ich" class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="">-- Tất cả loại --</option>
                @foreach($dsLoaiTienIch as $lt)
                <option value="{{ $lt->id }}" {{ (string) request('loai_tien_ich') === (string) $lt->id ? 'selected' : '' }}>{{ $lt->ten_loai_tien_ich }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <select name="toa_nha" class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="">-- Tất cả tòa nhà --</option>
                @foreach($dsToaNha as $tn)
                <option value="{{ $tn->id }}" {{ (string) request('toa_nha') === (string) $tn->id ? 'selected' : '' }}>{{ $tn->ten_toa_nha }}</option>
                @endforeach
            </select>
            <button type="submit" class="flex-shrink-0 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">Lọc</button>
        </div>
    </form>

    <!-- Danh sách tiện ích -->
    @if($dsTienIch->isEmpty())
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-12 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <p class="text-gray-500 dark:text-slate-400 font-medium">Không tìm thấy tiện ích phù hợp</p>
        <p class="text-sm text-gray-400 dark:text-slate-500 mt-1">Thử điều chỉnh bộ lọc tìm kiếm.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($dsTienIch as $ti)
        <a href="{{ route('resident.tien-ich.show', $ti) }}"
           class="group bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
            <div class="h-40 bg-gray-100 dark:bg-slate-700 overflow-hidden">
                @if($ti->hinh_url_full)
                <img src="{{ $ti->hinh_url_full }}" alt="{{ $ti->ten_tien_ich }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-300 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h16M4 4h16M4 20h16"/></svg>
                </div>
                @endif
            </div>
            <div class="p-4 space-y-2.5">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="font-semibold text-gray-800 dark:text-white leading-snug">{{ $ti->ten_tien_ich }}</h3>
                    <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-xs font-medium {{ $ti->trang_thai_label['class'] }}">{{ $ti->trang_thai_label['text'] }}</span>
                </div>
                <div class="flex flex-wrap gap-1.5 text-xs">
                    @if($ti->loaiTienIch)
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400">{{ $ti->loaiTienIch->ten_loai_tien_ich }}</span>
                    @endif
                    @if($ti->toaNha)
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300">{{ $ti->toaNha->ten_toa_nha }}</span>
                    @endif
                </div>
                @if($ti->mo_ta)
                <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-2">{{ $ti->mo_ta }}</p>
                @endif
                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-slate-700 text-xs text-gray-500 dark:text-slate-400">
                    <span>{{ $ti->gio_hoat_dong ?? 'Cả ngày' }}</span>
                    <span class="font-semibold text-gray-800 dark:text-slate-100">{{ number_format((float) $ti->phi_su_dung, 0, ',', '.') }} đ/giờ/người(sân)</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div>{{ $dsTienIch->links() }}</div>
    @endif

</div>
@endsection
