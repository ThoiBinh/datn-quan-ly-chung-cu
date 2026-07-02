@extends('layouts.admin')
@section('title', 'Quản lý căn hộ')
@section('page-title', 'Quản lý căn hộ')

@section('content')
@php
    $sortDir = fn($col) => $sort === $col ? ($direction === 'asc' ? 'desc' : 'asc') : 'asc';
    $sortUrl = fn($col) => request()->fullUrlWithQuery(['sort' => $col, 'direction' => $sortDir($col), 'page' => 1]);
    $sortIcon = fn($col) => $sort === $col
        ? ($direction === 'asc'
            ? '<svg class="w-3 h-3 inline ml-1 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>'
            : '<svg class="w-3 h-3 inline ml-1 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>')
        : '<svg class="w-3 h-3 inline ml-1 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>';
@endphp

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
     class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl shadow-lg">
    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span class="text-sm font-medium">{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
     class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white dark:bg-slate-800 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl shadow-lg">
    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    <span class="text-sm font-medium">{{ session('error') }}</span>
</div>
@endif

<div class="space-y-4">

    <!-- Dashboard thống kê -->
<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-4 gap-5">

    <!-- Tổng căn hộ -->
    <div class="group bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400">
                    Tổng căn hộ
                </p>
                <h3 class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                    {{ $stats['tong'] }}
                </h3>
            </div>

            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 10l9-7 9 7v10a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V10z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Tổng cư dân -->
    <div class="group bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400">
                    Tổng cư dân
                </p>
                <h3 class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                    {{ $stats['tong_cu_dan'] }}
                </h3>
            </div>

            <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 20h5V4H2v16h5m10 0v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4m10 0H7"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Phương tiện -->
    <div class="group bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400">
                    Phương tiện
                </p>
                <h3 class="mt-2 text-3xl font-bold text-purple-600 dark:text-purple-400">
                    {{ $stats['tong_phuong_tien'] }}
                </h3>
            </div>

            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 17h14l-1-5H6l-1 5zm2 0a2 2 0 104 0m6 0a2 2 0 104 0M5 17l-1 3m15-3l1 3"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Hóa đơn chưa thanh toán -->
    <div class="group bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400">
                    HĐ chưa thanh toán
                </p>
                <h3 class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">
                    {{ $stats['tong_hoa_don_chua_tt'] }}
                </h3>
            </div>

            <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M10.29 3.86l-8 14A1 1 0 003.14 19h17.72a1 1 0 00.85-1.5l-8-14a1 1 0 00-1.72 0z"/>
                </svg>
            </div>
        </div>
    </div>

</div>

    <!-- Filter bar -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.can-ho.index') }}" class="flex flex-wrap gap-3">
            <!-- Search -->
            <div class="relative min-w-40">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Số căn hộ, tòa nhà, chủ hộ..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <!-- Tòa nhà -->
            <select name="toa_nha" class="py-2.5 px-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Tất cả tòa nhà</option>
                @foreach($dsToaNha as $tn)
                <option value="{{ $tn->id }}" @selected(request('toa_nha') == $tn->id)>{{ $tn->ten_toa_nha }}</option>
                @endforeach
            </select>
            <!-- Loại căn hộ -->
            <select name="loai_can_ho" class="py-2.5 px-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Tất cả loại</option>
                @foreach($dsLoaiCanHo as $loai)
                <option value="{{ $loai->id }}" @selected(request('loai_can_ho') == $loai->id)>{{ $loai->ten_loai_can_ho }}</option>
                @endforeach
            </select>
            <!-- Trạng thái -->
            <select name="trang_thai" class="py-2.5 px-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Tất cả trạng thái</option>
                @foreach($dsTrangThai as $tt)
                <option value="{{ $tt->id }}" @selected(request('trang_thai') == $tt->id)>{{ $tt->ten_trang_thai }}</option>
                @endforeach
            </select>
            <!-- Tầng -->
            <input type="number" name="tang" value="{{ request('tang') }}" min="1" placeholder="Tầng"
                   class="w-24 py-2.5 px-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">Lọc</button>
            @if(request()->hasAny(['search', 'toa_nha', 'loai_can_ho', 'trang_thai', 'tang']))
            <a href="{{ route('admin.can-ho.index') }}" class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">Xóa lọc</a>
            @endif
            <div>
                <a href="{{ route('admin.thuoc-tinh.index') }}" class="sidebar-link {{ request()->routeIs('admin.thuoc-tinh.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Thuộc tính</span>
            </a>
            
            <a href="{{ route('admin.loai-can-ho.index') }}" class="sidebar-link {{ request()->routeIs('admin.loai-can-ho.*') ? 'active' : 'text-slate-300' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Loại căn hộ</span>
            </a>
                <a href="{{ route('admin.can-ho.create') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Thêm căn hộ
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700">
            <p class="text-sm text-gray-500 dark:text-slate-400">
                Tổng: <span class="font-semibold text-gray-700 dark:text-white">{{ $canHo->total() }}</span> căn hộ
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            <a href="{{ $sortUrl('so_can_ho') }}" class="hover:text-emerald-600 flex items-center gap-1 whitespace-nowrap">Số căn hộ {!! $sortIcon('so_can_ho') !!}</a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Tòa nhà</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            <a href="{{ $sortUrl('tang') }}" class="hover:text-emerald-600 flex items-center justify-center gap-1">Tầng {!! $sortIcon('tang') !!}</a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Loại</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Trạng thái</th>
                        
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Chủ hộ</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider" title="Tổng cư dân đang ở">Cư dân</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider" title="Tổng phương tiện">Xe</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider" title="Tổng hóa đơn">Hóa đơn</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Thuộc tính</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('createdAt') }}" class="hover:text-emerald-600 flex items-center gap-1">Ngày tạo {!! $sortIcon('createdAt') !!}</a>
                        </th>
                        <th class="px-4 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($canHo as $ch)
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-800 dark:text-white font-mono">{{ $ch->so_can_ho }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 text-xs whitespace-nowrap">
                            <a href="{{ route('admin.toa-nha.show', $ch->toaNha) }}" class="hover:text-emerald-600 transition-colors">
                                {{ $ch->toaNha?->ten_toa_nha ?? '—' }}
                            </a>
                        </td>
                        <td class="px-4 py-3.5 text-center text-gray-600 dark:text-slate-300">{{ $ch->tang }}</td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 text-xs whitespace-nowrap">{{ $ch->loaiCanHo?->ten_loai_can_ho ?? '—' }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 whitespace-nowrap">
                                {{ $ch->trangThai?->ten_trang_thai ?? '—' }}
                            </span>
                        </td>
                        
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 text-xs whitespace-nowrap">
                            {{ $ch->chuHo?->cuDan?->ho_ten ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold
                                {{ $ch->cu_dan_hien_tai_count > 0 ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'bg-gray-100 text-gray-400 dark:bg-slate-700 dark:text-slate-500' }}">
                                {{ $ch->cu_dan_hien_tai_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold
                                {{ $ch->phuong_tien_count > 0 ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-gray-100 text-gray-400 dark:bg-slate-700 dark:text-slate-500' }}">
                                {{ $ch->phuong_tien_count }}
                            </span>
                        </td>
                        
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold
                                {{ $ch->hoa_don_count > 0 ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-gray-100 text-gray-400 dark:bg-slate-700 dark:text-slate-500' }}">
                                {{ $ch->hoa_don_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 max-w-[200px]">
                            @if($ch->thuocTinh->isEmpty())
                            <span class="text-gray-400 dark:text-slate-600">—</span>
                            @else
                            <div class="flex flex-wrap gap-1">
                                @foreach($ch->thuocTinh as $tt)
                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-xs bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-900/20 dark:text-purple-300 dark:border-purple-800 whitespace-nowrap"
                                      title="{{ $tt->ten_thuoc_tinh }}: {{ $tt->pivot->gia_tri_thuoc_tinh ?? '—' }}">
                                    <span class="font-medium">{{ $tt->ten_thuoc_tinh }}:</span>
                                    <span>{{ $tt->pivot->gia_tri_thuoc_tinh ?? '—' }}</span>
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 dark:text-slate-400 text-xs whitespace-nowrap">
                            {{ $ch->createdAt?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="relative flex justify-start" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open" title="Thao tác"
                                        class="p-1.5 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                </button>
                                <div x-show="open" x-transition x-cloak
                                     class="absolute right-0 top-8 z-10 w-44 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-lg py-1">
                                    <a href="{{ route('admin.can-ho.show', $ch) }}"
                                       class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Xem chi tiết
                                    </a>
                                    <a href="{{ route('admin.can-ho.edit', $ch) }}"
                                       class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Chỉnh sửa
                                    </a>
                                    <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                                    <form method="POST" action="{{ route('admin.can-ho.destroy', $ch) }}"
                                          onsubmit="return confirm('Căn hộ «{{ addslashes($ch->so_can_ho) }}» sẽ được chuyển sang trạng thái Trống nếu chưa phát sinh dữ liệu. Tiếp tục?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-5 py-14 text-center">
                            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <p class="text-sm text-gray-400 dark:text-slate-500">Không tìm thấy căn hộ nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($canHo->hasPages())
        <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $canHo->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
