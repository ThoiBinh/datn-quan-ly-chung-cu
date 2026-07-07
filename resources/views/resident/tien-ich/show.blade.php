@extends('layouts.resident')
@section('title', $tienIch->ten_tien_ich)
@section('page-title', $tienIch->ten_tien_ich)

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('resident.tien-ich.index') }}" class="hover:text-emerald-600 transition-colors">Tiện ích</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">{{ $tienIch->ten_tien_ich }}</span>
    </nav>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="h-56 bg-gray-100 dark:bg-slate-700">
            @if($tienIch->hinh_url_full)
            <img src="{{ $tienIch->hinh_url_full }}" alt="{{ $tienIch->ten_tien_ich }}" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-14 h-14 text-gray-300 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h16M4 4h16M4 20h16"/></svg>
            </div>
            @endif
        </div>

        <div class="p-6 space-y-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $tienIch->ten_tien_ich }}</h1>
                    <div class="flex flex-wrap gap-1.5 mt-2 text-xs">
                        @if($tienIch->loaiTienIch)
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400">{{ $tienIch->loaiTienIch->ten_loai_tien_ich }}</span>
                        @endif
                        @if($tienIch->toaNha)
                        <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300">{{ $tienIch->toaNha->ten_toa_nha }}</span>
                        @endif
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $tienIch->trang_thai_label['class'] }}">{{ $tienIch->trang_thai_label['text'] }}</span>
            </div>

            @if($tienIch->mo_ta)
            <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed">{{ $tienIch->mo_ta }}</p>
            @endif

            <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100 dark:border-slate-700">
                <div>
                    <dt class="text-xs text-gray-400 dark:text-slate-500">Sức chứa</dt>
                    <dd class="text-sm font-semibold text-gray-800 dark:text-white mt-0.5">{{ $tienIch->suc_chua ? $tienIch->suc_chua.' người' : 'Không giới hạn' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 dark:text-slate-500">Giá / người / giờ</dt>
                    <dd class="text-sm font-semibold text-gray-800 dark:text-white mt-0.5">{{ number_format((float) $tienIch->phi_su_dung, 0, ',', '.') }} đ</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 dark:text-slate-500">Giờ hoạt động</dt>
                    <dd class="text-sm font-semibold text-gray-800 dark:text-white mt-0.5">{{ $tienIch->gio_hoat_dong ?? 'Cả ngày' }}</dd>
                </div>
                @if($tienIch->vi_tri)
                <div class="col-span-2 sm:col-span-3">
                    <dt class="text-xs text-gray-400 dark:text-slate-500">Vị trí</dt>
                    <dd class="text-sm font-semibold text-gray-800 dark:text-white mt-0.5">{{ $tienIch->vi_tri }}</dd>
                </div>
                @endif
            </dl>

            <div class="pt-4 border-t border-gray-100 dark:border-slate-700">
                @if($tienIch->can_dat_truoc)
                <a href="{{ route('resident.dat-lich-tien-ich.create', ['tien_ich' => $tienIch->id]) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Đặt lịch ngay
                </a>
                @else
                <p class="text-sm text-gray-400 dark:text-slate-500 italic">Tiện ích này hiện không nhận đặt lịch trước.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
