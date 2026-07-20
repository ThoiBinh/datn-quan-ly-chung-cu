@extends('layouts.admin')
@section('title', 'Quản lý cư dân')
@section('page-title', 'Quản lý cư dân')

@section('content')
@php
    $sortDir = fn($col) => $sort === $col ? ($direction === 'asc' ? 'desc' : 'asc') : 'asc';
    $sortUrl = fn($col) => request()->fullUrlWithQuery(['sort' => $col, 'direction' => $sortDir($col), 'page' => 1]);
    $sortIcon = fn($col) => $sort === $col
        ? ($direction === 'asc'
            ? '<svg class="w-3 h-3 inline ml-1 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>'
            : '<svg class="w-3 h-3 inline ml-1 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>')
        : '<svg class="w-3 h-3 inline ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>';
@endphp

<div class="space-y-4">

    {{-- Thống kê --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
            $statCards = [
                ['label' => 'Tổng căn hộ đang ở', 'value' => $stats['tong_can_ho'],      'color' => 'emerald'],
                ['label' => 'Tổng phương tiện',    'value' => $stats['tong_phuong_tien'], 'color' => 'orange'],
                ['label' => 'Tổng hóa đơn',        'value' => $stats['tong_hoa_don'],     'color' => 'blue'],
                ['label' => 'Tổng yêu cầu',        'value' => $stats['tong_yeu_cau'],     'color' => 'purple'],
            ];
            $colorMap = [
                'emerald' => 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300',
                'orange'  => 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-700 text-orange-700 dark:text-orange-300',
                'blue'    => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300',
                'purple'  => 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-700 text-purple-700 dark:text-purple-300',
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="rounded-xl border p-3.5 text-center {{ $colorMap[$card['color']] }}">
            <p class="text-2xl font-bold">{{ number_format($card['value']) }}</p>
            <p class="text-xs mt-0.5 font-medium">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Search + Filter + Add -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.cu-dan.index') }}" class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Họ tên, email, SĐT, CCCD, tòa nhà, căn hộ..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <select name="toa_nha" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                <option value="">Tất cả tòa nhà</option>
                @foreach($toaNha as $tn)
                <option value="{{ $tn->id }}" @selected(request('toa_nha') == $tn->id)>{{ $tn->ten_toa_nha }}</option>
                @endforeach
            </select>
            <select name="loai_can_ho" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                <option value="">Tất cả loại căn hộ</option>
                @foreach($loaiCanHo as $lc)
                <option value="{{ $lc->id }}" @selected(request('loai_can_ho') == $lc->id)>{{ $lc->ten_loai_can_ho }}</option>
                @endforeach
            </select>
            <select name="vai_tro" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                <option value="">Chủ hộ / Thành viên</option>
                <option value="chu_ho" @selected(request('vai_tro') === 'chu_ho')>Chủ hộ</option>
                <option value="thanh_vien" @selected(request('vai_tro') === 'thanh_vien')>Thành viên</option>
            </select>
            <select name="status" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                <option value="">Tất cả trạng thái</option>
                <option value="1" @selected(request('status') === '1')>Đang cư trú</option>
                <option value="2" @selected(request('status') === '2')>Tạm vắng</option>
                <option value="3" @selected(request('status') === '3')>Đã chuyển đi</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">Lọc</button>
            @if(request()->anyFilled(['search','toa_nha','loai_can_ho','vai_tro','status']))
            <a href="{{ route('admin.cu-dan.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition-colors">Xóa lọc</a>
            @endif
            <div class="ml-auto flex items-center gap-3">
                <a href="{{ route('admin.vai-tro.index') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Vai trò
                </a>
                <a href="{{ route('admin.cu-dan.create') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Thêm cư dân
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm"
         x-data="{
             confirmToggle: null,
             openToggle(id, name, action, isLock) { this.confirmToggle = { id, name, action, isLock }; },
             closeToggle() { this.confirmToggle = null; },
             confirmDelete: null,
             openDelete(id, name, action) { this.confirmDelete = { id, name, action }; },
             closeDelete() { this.confirmDelete = null; }
         }">

        <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700">
            <p class="text-sm text-gray-500 dark:text-slate-400">
                Tổng: <span class="font-semibold text-gray-700 dark:text-white">{{ $cuDan->total() }}</span> cư dân
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('ho_ten_dem') }}" class="hover:text-emerald-600 flex items-center gap-1">Cư dân {!! $sortIcon('ho_ten_dem') !!}</a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">CCCD</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Giới tính</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Liên hệ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Địa chỉ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('toa_nha') }}" class="hover:text-emerald-600 flex items-center gap-1">Tòa nhà {!! $sortIcon('toa_nha') !!}</a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('so_can_ho') }}" class="hover:text-emerald-600 flex items-center gap-1">Căn hộ {!! $sortIcon('so_can_ho') !!}</a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Trạng thái</th>
                        <th class="px-4 py-3 w-28"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($cuDan as $cd)
                    @php
                        $diaChi = collect([$cd->dia_chi, $cd->xa, $cd->tinh])->filter()->implode(', ');
                        $ttLabel = $cd->trang_thai_label;
                    @endphp
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if($cd->avatar_url)
                                <img src="{{ $cd->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                @else
                                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-xs font-bold text-emerald-700 dark:text-emerald-400 flex-shrink-0">
                                    {{ mb_strtoupper(mb_substr($cd->ten, 0, 1)) }}
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800 dark:text-white leading-tight">{{ $cd->ho_ten }}</p>
                                    <p class="text-xs text-gray-400 dark:text-slate-500">{{ $cd->ngay_sinh?->format('d/m/Y') ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 font-mono text-xs text-gray-600 dark:text-slate-300 whitespace-nowrap">{{ $cd->cccd ?: '—' }}</td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-gray-600 dark:text-slate-300 text-xs">{{ $cd->gioi_tinh_label }}</td>
                        <td class="px-4 py-3.5 text-xs">
                            <p class="text-gray-700 dark:text-slate-200">{{ $cd->email ?: '—' }}</p>
                            <p class="text-gray-400 dark:text-slate-500">{{ $cd->sdt ?: '—' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 text-xs max-w-xs">
                            <span class="line-clamp-1" title="{{ $diaChi }}">{{ $diaChi ?: '—' }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-xs">
                            @forelse($cd->cuDanCanHo as $cdch)
                                <p class="{{ !$loop->first ? 'mt-1' : '' }} text-gray-700 dark:text-slate-200">{{ $cdch->canHo?->toaNha?->ten_toa_nha ?? '—' }}</p>
                            @empty
                                <span class="text-gray-400 italic">Chưa phân công</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-3.5 text-xs">
                            @foreach($cd->cuDanCanHo as $cdch)
                                <div class="{{ !$loop->first ? 'mt-1' : '' }} flex items-center gap-1">
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $cdch->canHo?->so_can_ho ?? '—' }}</span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                        {{ $cdch->vaiTro?->vai_tro ?? '—' }}
                                    </span>
                                </div>
                            @endforeach
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ttLabel['class'] }}">
                                {{ $ttLabel['text'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="{{ route('admin.cu-dan.show', $cd) }}" title="Xem chi tiết"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.cu-dan.edit', $cd) }}" title="Chỉnh sửa"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button type="button" title="{{ $cd->trang_thai == 1 ? 'Đánh dấu đã chuyển đi' : 'Đánh dấu đang cư trú' }}"
                                        @click="openToggle({{ $cd->id }}, '{{ addslashes($cd->ho_ten) }}', '{{ route('admin.cu-dan.toggle-status', $cd) }}', {{ $cd->trang_thai == 1 ? 'true' : 'false' }})"
                                        class="p-1.5 rounded-md transition-colors {{ $cd->trang_thai == 1 ? 'text-gray-400 hover:text-amber-600 hover:bg-amber-50' : 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50' }}">
                                    @if($cd->trang_thai == 1)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    @endif
                                </button>
                                <button type="button" title="Xóa"
                                        @click="openDelete({{ $cd->id }}, '{{ addslashes($cd->ho_ten) }}', '{{ route('admin.cu-dan.destroy', $cd) }}')"
                                        class="p-1.5 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-14 text-center">
                            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-sm text-gray-400 dark:text-slate-500">Không tìm thấy cư dân nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cuDan->hasPages())
        <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $cuDan->links() }}
        </div>
        @endif

        <!-- Modal xác nhận toggle -->
        <template x-teleport="body">
        <div x-show="confirmToggle !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             @click.self="closeToggle()">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden" @click.stop>
                <div class="flex justify-end px-4 pt-4">
                    <button @click="closeToggle()" type="button"
                            class="w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div x-show="confirmToggle?.isLock" class="px-6 pt-2 pb-5 text-center">
                    <div class="w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Đánh dấu đã chuyển đi</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Cư dân sẽ không thể đăng nhập cho tới khi được đặt lại trạng thái đang cư trú.</p>
                </div>
                <div x-show="confirmToggle && !confirmToggle.isLock" class="px-6 pt-2 pb-5 text-center">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Đánh dấu đang cư trú</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Cư dân sẽ được kích hoạt lại, có thể đăng nhập trở lại.</p>
                </div>
                <div class="mx-6 mb-5 flex items-center gap-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                    <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-600 flex items-center justify-center text-sm font-bold text-slate-700 dark:text-slate-300 flex-shrink-0"
                         x-text="confirmToggle?.name?.charAt(0)?.toUpperCase() || '?'"></div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400 dark:text-slate-500">Cư dân</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white truncate" x-text="confirmToggle?.name"></p>
                    </div>
                </div>
                <div class="flex gap-3 px-6 pb-6">
                    <button @click="closeToggle()" type="button"
                            class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        Hủy bỏ
                    </button>
                    <form :action="confirmToggle?.action" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <button x-show="confirmToggle?.isLock" type="submit"
                                class="block w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-gray-900 text-sm font-semibold rounded-xl transition-colors">
                            Xác nhận
                        </button>
                        <button x-show="confirmToggle && !confirmToggle.isLock" type="submit"
                                class="block w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">
                            Xác nhận
                        </button>
                    </form>
                </div>
            </div>
        </div>
        </template>

        <!-- Modal xác nhận xóa -->
        <template x-teleport="body">
        <div x-show="confirmDelete !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             @click.self="closeDelete()">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden" @click.stop>
                <div class="flex justify-end px-4 pt-4">
                    <button @click="closeDelete()" type="button"
                            class="w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 pt-2 pb-5 text-center">
                    <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Xóa cư dân</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                        Nếu cư dân đã phát sinh dữ liệu (hóa đơn, thanh toán, phương tiện, yêu cầu), thao tác sẽ bị từ chối.
                    </p>
                </div>
                <div class="mx-6 mb-5 flex items-center gap-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                    <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-600 flex items-center justify-center text-sm font-bold text-slate-700 dark:text-slate-300 flex-shrink-0"
                         x-text="confirmDelete?.name?.charAt(0)?.toUpperCase() || '?'"></div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400 dark:text-slate-500">Cư dân</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white truncate" x-text="confirmDelete?.name"></p>
                    </div>
                </div>
                <div class="flex gap-3 px-6 pb-6">
                    <button @click="closeDelete()" type="button"
                            class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        Hủy bỏ
                    </button>
                    <form :action="confirmDelete?.action" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="block w-full py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors">
                            Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
        </template>
    </div>
</div>
@endsection
