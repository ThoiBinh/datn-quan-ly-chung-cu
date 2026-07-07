@extends('layouts.manager')
@section('title', $loaiTienIch->ten_loai_tien_ich)
@section('page-title', 'Chi tiết loại tiện ích')

@section('content')
<div class="max-w-4xl mx-auto space-y-5" x-data="{ open: false }">

    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('manager.loai-tien-ich.index') }}" class="hover:text-indigo-600 transition-colors">Loại tiện ích</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200 truncate max-w-xs">{{ $loaiTienIch->ten_loai_tien_ich }}</span>
    </nav>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5"/></svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900 dark:text-white">{{ $loaiTienIch->ten_loai_tien_ich }}</h1>
                <p class="text-sm text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5">
                    {{ $loaiTienIch->tien_ich_count }} tiện ích thuộc loại này
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('manager.loai-tien-ich.edit', $loaiTienIch) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
        </div>
    </div>

    {{-- Thống kê --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $loaiTienIch->tien_ich_count }}</p>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Tổng số tiện ích</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $loaiTienIch->dat_lich_count }}</p>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Tổng lượt đặt của các tiện ích</p>
        </div>
    </div>

    {{-- Danh sách tiện ích thuộc loại --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
                Tiện ích thuộc loại này
                <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400">{{ $loaiTienIch->tien_ich_count }}</span>
            </h2>
            <a href="{{ route('manager.tien-ich.index', ['loai_tien_ich' => $loaiTienIch->id]) }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Xem tất cả</a>
        </div>
        @if($loaiTienIch->tienIch->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase">Tên tiện ích</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase">Tòa nhà</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase">Trạng thái</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase">Lượt đặt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($loaiTienIch->tienIch as $ti)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('manager.tien-ich.show', $ti) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">{{ $ti->ten_tien_ich }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-slate-200">{{ $ti->toaNha?->ten_toa_nha ?? 'Toàn khu' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ti->trang_thai_label['class'] }}">
                                {{ $ti->trang_thai_label['text'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center min-w-7 h-7 px-1.5 rounded-full text-xs font-semibold {{ $ti->dat_lich_count > 0 ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400' : 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400' }}">
                                {{ $ti->dat_lich_count }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="px-5 py-8 text-center text-sm text-gray-400 dark:text-slate-500">Chưa có tiện ích nào thuộc loại này.</p>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between">
        @if($loaiTienIch->tien_ich_count === 0)
        <button type="button" @click="open = true"
                class="px-4 py-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
            Xóa loại tiện ích
        </button>
        @else
        <div class="relative group">
            <button type="button" disabled
                    class="px-4 py-2.5 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900 text-red-400 dark:text-red-700 text-sm font-medium rounded-lg opacity-60 cursor-not-allowed">
                Xóa loại tiện ích
            </button>
            <div class="absolute bottom-full left-0 mb-2 px-3 py-2 bg-gray-900 dark:bg-slate-700 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                Không thể xóa vì loại tiện ích đang được sử dụng.
            </div>
        </div>
        @endif

        <a href="{{ route('manager.loai-tien-ich.index') }}"
           class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
            Quay lại
        </a>
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
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Loại «{{ $loaiTienIch->ten_loai_tien_ich }}» sẽ được xóa.</p>
            </div>
            <div class="flex gap-3">
                <button @click="open = false" class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">Hủy</button>
                <form action="{{ route('manager.loai-tien-ich.destroy', $loaiTienIch) }}" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="block w-full py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">Xóa</button>
                </form>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection
