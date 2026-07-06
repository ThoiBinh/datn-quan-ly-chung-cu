@extends('layouts.manager')
@section('title', 'Đặt lịch tiện ích')
@section('page-title', 'Đặt lịch tiện ích')

@section('content')
@php
$sortUrl = fn($col) => request()->fullUrlWithQuery([
    'sort' => $col,
    'direction' => (request('sort', 'thoi_gian_bat_dau') === $col && request('direction', 'desc') === 'asc') ? 'desc' : 'asc',
]);
$sortIcon = function($col) {
    $sort = request('sort', 'thoi_gian_bat_dau');
    $direction = request('direction', 'desc');
    if ($sort !== $col) return '<svg class="w-3.5 h-3.5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>';
    return $direction === 'asc'
        ? '<svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>'
        : '<svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
};
@endphp

<div class="space-y-4" x-data="{ open: false, url: '', name: '' }" @open-delete.window="open = true; url = $event.detail.url; name = $event.detail.name">

    <!-- Header -->
    <div class="mb-2 flex flex-col justify-between gap-4 rounded-2xl bg-white p-5 shadow-sm lg:flex-row lg:items-center dark:bg-slate-800">
        <div>
            <p class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400">
                <span class="font-medium text-gray-700 dark:text-slate-200">Tổng:</span>
                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                    {{ $dsDatLich->total() }}
                </span>
                <span>lượt đặt lịch</span>
            </p>
        </div>
        <a href="{{ route('manager.dat-lich-tien-ich.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm đặt lịch
        </a>
    </div>

    <!-- Filter bar -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('manager.dat-lich-tien-ich.index') }}" class="flex flex-wrap gap-2">
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tìm mã, cư dân, email, căn hộ, tiện ích, trạng thái..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <select name="tien_ich" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Tất cả tiện ích</option>
                @foreach($dsTienIch as $ti)
                <option value="{{ $ti->id }}" {{ request('tien_ich') == $ti->id ? 'selected' : '' }}>{{ $ti->ten_tien_ich }}</option>
                @endforeach
            </select>
            <select name="can_ho" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Tất cả căn hộ</option>
                @foreach($dsCanHo as $ch)
                <option value="{{ $ch->id }}" {{ request('can_ho') == $ch->id ? 'selected' : '' }}>{{ $ch->so_can_ho }} — {{ $ch->toaNha?->ten_toa_nha }}</option>
                @endforeach
            </select>
            <select name="trang_thai" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Tất cả trạng thái</option>
                @foreach($dsTrangThai as $id => $label)
                <option value="{{ $id }}" {{ (string) request('trang_thai') === (string) $id ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="ngay_su_dung" value="{{ request('ngay_su_dung') }}"
                   class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <select name="sort" onchange="this.form.submit()" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="thoi_gian_bat_dau" {{ request('sort', 'thoi_gian_bat_dau') === 'thoi_gian_bat_dau' ? 'selected' : '' }}>Sắp xếp: Ngày sử dụng</option>
                <option value="createdAt" {{ request('sort') === 'createdAt' ? 'selected' : '' }}>Sắp xếp: Ngày tạo</option>
                <option value="ho_ten" {{ request('sort') === 'ho_ten' ? 'selected' : '' }}>Sắp xếp: Cư dân</option>
                <option value="trang_thai" {{ request('sort') === 'trang_thai' ? 'selected' : '' }}>Sắp xếp: Trạng thái</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                Lọc
            </button>
            @if(request()->hasAny(['search', 'tien_ich', 'can_ho', 'trang_thai', 'ngay_su_dung']))
            <a href="{{ route('manager.dat-lich-tien-ich.index') }}" class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">Xóa lọc</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">STT</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Mã đặt lịch</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Cư dân</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Căn hộ</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Tiện ích</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('thoi_gian_bat_dau') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-slate-200">
                                Ngày sử dụng / Giờ {!! $sortIcon('thoi_gian_bat_dau') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('trang_thai') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-slate-200">
                                Trạng thái {!! $sortIcon('trang_thai') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Người tạo</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('createdAt') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-slate-200">
                                Ngày tạo {!! $sortIcon('createdAt') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    @forelse($dsDatLich as $i => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3.5 text-gray-500 dark:text-slate-400">{{ $dsDatLich->firstItem() + $i }}</td>
                        <td class="px-4 py-3.5 font-mono font-semibold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                            {{ $item->ma_dat_lich }}
                        </td>
                        <td class="px-4 py-3.5 text-gray-700 dark:text-slate-200">
                            <p class="font-medium">{{ $item->cuDan?->ho_ten ?? '—' }}</p>
                            <p class="text-xs text-gray-400 dark:text-slate-500">{{ $item->cuDan?->email ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-gray-600 dark:text-slate-300">
                            @if($item->canHo)
                                {{ $item->canHo->so_can_ho }}
                                <p class="text-xs text-gray-400 dark:text-slate-500">{{ $item->canHo->toaNha?->ten_toa_nha ?? '—' }}</p>
                            @else
                                <span class="text-gray-400 dark:text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                {{ $item->tienIch?->ten_tien_ich ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-gray-600 dark:text-slate-300">
                            {{ $item->thoi_gian_bat_dau?->format('d/m/Y') }}
                            <p class="text-xs text-gray-400 dark:text-slate-500">
                                {{ $item->thoi_gian_bat_dau?->format('H:i') }} - {{ $item->thoi_gian_ket_thuc?->format('H:i') }}
                            </p>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->trang_thai_label['class'] }}">
                                {{ $item->trang_thai_label['text'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300">{{ $item->cuDan?->ho_ten ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-gray-500 dark:text-slate-400 whitespace-nowrap text-xs">
                            {{ $item->createdAt?->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="{{ route('manager.dat-lich-tien-ich.show', $item) }}" title="Xem chi tiết"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('manager.dat-lich-tien-ich.edit', $item) }}" title="Chỉnh sửa"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button type="button"
                                        @click="$dispatch('open-delete', { url: '{{ route('manager.dat-lich-tien-ich.destroy', $item) }}', name: '{{ addslashes($item->ma_dat_lich) }}' })"
                                        title="Xóa" class="p-1.5 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-14 text-center">
                            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm text-gray-400 dark:text-slate-500">Không tìm thấy lịch đặt tiện ích nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dsDatLich->hasPages())
        <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $dsDatLich->links() }}
        </div>
        @endif
    </div>

    <!-- Modal xác nhận xóa -->
    <template x-teleport="body">
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="open = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-5">
                <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-800 dark:text-white">Xóa lịch đặt tiện ích?</p>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Mã <span class="font-medium text-gray-700 dark:text-slate-200" x-text="`«${name}»`"></span> sẽ được chuyển vào thùng rác.</p>
            </div>
            <div class="flex gap-3">
                <button @click="open = false" type="button" class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">Hủy</button>
                <form :action="url" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="block w-full py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">Xóa</button>
                </form>
            </div>
        </div>
    </div>
    </template>

</div>
@endsection
