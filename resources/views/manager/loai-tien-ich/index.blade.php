@extends('layouts.manager')
@section('title', 'Quản lý loại tiện ích')
@section('page-title', 'Quản lý loại tiện ích')

@section('content')
@php
$sortUrl = fn($col) => request()->fullUrlWithQuery([
    'sort' => $col,
    'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc',
]);
$sortIcon = function($col) use ($sort, $direction) {
    if ($sort !== $col) return '<svg class="w-3.5 h-3.5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>';
    return $direction === 'asc'
        ? '<svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>'
        : '<svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
};
@endphp

<div class="space-y-5" x-data="{ open: false, url: '', name: '' }" @open-delete.window="open = true; url = $event.detail.url; name = $event.detail.name">

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
         class="flex items-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col justify-between gap-4 rounded-2xl bg-white dark:bg-slate-800 p-5 shadow-sm lg:flex-row lg:items-center">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Quản lý loại tiện ích</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                Tổng: <span class="font-semibold text-gray-700 dark:text-slate-200">{{ $tongTatCa }}</span> loại,
                <span class="font-semibold text-gray-700 dark:text-slate-200">{{ $tongDangDung }}</span> đang được sử dụng
            </p>
        </div>
        <div class="flex items-center gap-2 self-start lg:self-auto">
            <a href="{{ route('manager.tien-ich.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quản lý tiện ích
            </a>
            <a href="{{ route('manager.loai-tien-ich.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm loại tiện ích
            </a>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('manager.loai-tien-ich.index') }}" class="flex flex-wrap gap-2">
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tìm tên loại tiện ích..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <select name="su_dung" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Tất cả trạng thái</option>
                <option value="co" {{ request('su_dung') === 'co' ? 'selected' : '' }}>Đang sử dụng</option>
                <option value="khong" {{ request('su_dung') === 'khong' ? 'selected' : '' }}>Chưa sử dụng</option>
            </select>
            <select name="sort" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="ten_loai_tien_ich" {{ $sort === 'ten_loai_tien_ich' ? 'selected' : '' }}>Sắp xếp: Tên A-Z</option>
                <option value="tien_ich_count" {{ $sort === 'tien_ich_count' ? 'selected' : '' }}>Sắp xếp: Số lượng tiện ích</option>
            </select>
            <select name="direction" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>Tăng dần</option>
                <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>Giảm dần</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                Lọc
            </button>
            @if(request()->hasAny(['search', 'su_dung', 'sort', 'direction']))
            <a href="{{ route('manager.loai-tien-ich.index') }}" class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">Xóa lọc</a>
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
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('ten_loai_tien_ich') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-slate-200">
                                Tên loại tiện ích {!! $sortIcon('ten_loai_tien_ich') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('tien_ich_count') }}" class="inline-flex items-center gap-1 justify-center hover:text-gray-700 dark:hover:text-slate-200">
                                Số tiện ích {!! $sortIcon('tien_ich_count') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Tình trạng</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    @forelse($dsLoai as $loai)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400 dark:text-slate-500">{{ $loop->iteration + ($dsLoai->currentPage() - 1) * $dsLoai->perPage() }}</td>
                        <td class="px-4 py-3.5">
                            <a href="{{ route('manager.loai-tien-ich.show', $loai) }}" class="font-medium text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                                {{ $loai->ten_loai_tien_ich }}
                            </a>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center justify-center min-w-7 h-7 px-1.5 rounded-full text-xs font-semibold {{ $loai->tien_ich_count > 0 ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400' : 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400' }}">
                                {{ $loai->tien_ich_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($loai->tien_ich_count > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Đang sử dụng</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Có thể xóa</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="{{ route('manager.loai-tien-ich.show', $loai) }}" title="Xem chi tiết"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('manager.loai-tien-ich.edit', $loai) }}" title="Chỉnh sửa"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @if($loai->tien_ich_count === 0)
                                <button type="button"
                                        @click="$dispatch('open-delete', { url: '{{ route('manager.loai-tien-ich.destroy', $loai) }}', name: '{{ addslashes($loai->ten_loai_tien_ich) }}' })"
                                        class="p-1.5 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @else
                                <span class="p-1.5 text-gray-300 dark:text-slate-600 cursor-not-allowed" title="Đang có tiện ích thuộc loại này — không thể xóa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-14 text-center">
                            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <p class="text-sm text-gray-400 dark:text-slate-500">Không tìm thấy loại tiện ích nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dsLoai->hasPages())
        <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $dsLoai->links() }}
        </div>
        @endif
    </div>

    {{-- Modal xóa --}}
    <template x-teleport="body">
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="open = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-5">
                <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <p class="font-semibold text-gray-800 dark:text-white">Xóa loại tiện ích?</p>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Loại <span class="font-medium text-gray-700 dark:text-slate-200" x-text="`«${name}»`"></span> sẽ được xóa.</p>
            </div>
            <div class="flex gap-3">
                <button @click="open = false" class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">Hủy</button>
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
