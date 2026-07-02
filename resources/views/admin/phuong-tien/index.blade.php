@extends('layouts.admin')
@section('title', 'Quản lý phương tiện')
@section('page-title', 'Quản lý phương tiện')

@section('content')
@php
$sortUrl = fn($col) => request()->fullUrlWithQuery([
    'sort' => $col,
    'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc',
]);
$sortIcon = function($col) use ($sort, $direction) {
    if ($sort !== $col) return '<svg class="w-3.5 h-3.5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>';
    return $direction === 'asc'
        ? '<svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>'
        : '<svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
};
@endphp

<div class="space-y-4" x-data="{
    confirmToggle: null,
    openToggle(id, name, action, isLock) { this.confirmToggle = { id, name, action, isLock }; },
    closeToggle() { this.confirmToggle = null; }
}">

    <!-- Header -->
<div class="mb-6 flex flex-col justify-between gap-5 rounded-2xl bg-white p-5 shadow-sm lg:flex-row lg:items-center dark:bg-slate-800">        <div>
<p class="mt-1 flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400">
    <span class="font-medium text-gray-700 dark:text-slate-200">Tổng:</span>

    <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
        {{ $phuongTien->total() }}
    </span>

    <span>phương tiện</span>
</p>        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.loai-phuong-tien.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.83H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 0h-12"/></svg>
                <span>Loại phương tiện</span>
            </a>
            <a href="{{ route('admin.phuong-tien.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm phương tiện
            </a>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.phuong-tien.index') }}" class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tìm biển số, tên xe, cư dân, CCCD, căn hộ, tòa nhà, loại xe..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <select name="loai" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-400">
                <option value="">Tất cả loại</option>
                @foreach($dsLoai as $loai)
                <option value="{{ $loai->id }}" {{ request('loai') == $loai->id ? 'selected' : '' }}>{{ $loai->ten_loai_phuong_tien }}</option>
                @endforeach
            </select>
            <select name="toa_nha" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-400">
                <option value="">Tất cả tòa nhà</option>
                @foreach($dsToaNha as $tn)
                <option value="{{ $tn->id }}" {{ request('toa_nha') == $tn->id ? 'selected' : '' }}>{{ $tn->ten_toa_nha }}</option>
                @endforeach
            </select>
            <select name="can_ho" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-400">
                <option value="">Tất cả căn hộ</option>
                @foreach($dsCanHo as $ch)
                <option value="{{ $ch->id }}" {{ request('can_ho') == $ch->id ? 'selected' : '' }}>{{ $ch->so_can_ho }} — {{ $ch->toaNha?->ten_toa_nha }}</option>
                @endforeach
            </select>
            <select name="trang_thai" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-400">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Đã hủy</option>
            </select>
            <select name="sort" onchange="this.form.submit()" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-400">
                <option value="bien_so" {{ $sort === 'bien_so' ? 'selected' : '' }}>Sắp xếp: Biển số</option>
                <option value="ho_ten" {{ $sort === 'ho_ten' ? 'selected' : '' }}>Sắp xếp: Họ tên chủ hộ</option>
                <option value="ngay_dang_ky" {{ $sort === 'ngay_dang_ky' ? 'selected' : '' }}>Sắp xếp: Ngày đăng ký</option>
                <option value="ngay_huy" {{ $sort === 'ngay_huy' ? 'selected' : '' }}>Sắp xếp: Ngày hủy</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors">
                Lọc
            </button>
            @if(request()->hasAny(['search','loai','toa_nha','can_ho','trang_thai']))
            <a href="{{ route('admin.phuong-tien.index') }}" class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Xóa lọc
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('bien_so') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-slate-200">
                                Biển số {!! $sortIcon('bien_so') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Tên PT</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Loại</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Căn hộ / Tòa nhà</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Chủ sở hữu</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('ngay_dang_ky') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-slate-200">
                                Ngày ĐK {!! $sortIcon('ngay_dang_ky') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Trạng thái</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    @forelse($phuongTien as $pt)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3.5 font-mono font-semibold text-orange-600 dark:text-orange-400 whitespace-nowrap">
                            {{ $pt->bien_so ?: '—' }}
                        </td>
                        <td class="px-4 py-3.5 text-gray-700 dark:text-slate-200">{{ $pt->ten_phuong_tien ?: '—' }}</td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($pt->loaiPhuongTien)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                                {{ $pt->loaiPhuongTien->ten_loai_phuong_tien }}
                            </span>
                            @else
                            <span class="text-gray-400 dark:text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($pt->canHo)
                            <div>
                                <a href="{{ route('admin.can-ho.show', $pt->canHo) }}"
                                   class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $pt->canHo->so_can_ho }}
                                </a>
                                <p class="text-xs text-gray-400 dark:text-slate-500">{{ $pt->canHo->toaNha?->ten_toa_nha ?? '—' }}</p>
                            </div>
                            @else
                            <span class="text-gray-400 dark:text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300">
                            {{ $pt->canHo?->chuHo?->cuDan?->ho_ten ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 dark:text-slate-400 whitespace-nowrap text-xs">
                            {{ $pt->ngay_dang_ky?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($pt->trang_thai == 1)
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-500 dark:text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Đã hủy
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="{{ route('admin.phuong-tien.show', $pt) }}" title="Xem chi tiết"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.phuong-tien.edit', $pt) }}" title="Chỉnh sửa"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button type="button"
                                        title="{{ $pt->trang_thai == 1 ? 'Hủy phương tiện' : 'Khôi phục phương tiện' }}"
                                        @click="openToggle({{ $pt->id }}, '{{ addslashes($pt->bien_so) }}', '{{ $pt->trang_thai == 1 ? route('admin.phuong-tien.toggle-status', $pt) : route('admin.phuong-tien.restore', $pt) }}', {{ $pt->trang_thai == 1 ? 'true' : 'false' }})"
                                        class="p-1.5 rounded-md transition-colors {{ $pt->trang_thai == 1 ? 'text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30' : 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30' }}">
                                    @if($pt->trang_thai == 1)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    @endif
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-14 text-center">
                            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <p class="text-sm text-gray-400 dark:text-slate-500">Không tìm thấy phương tiện nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($phuongTien->hasPages())
        <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $phuongTien->links() }}
        </div>
        @endif
    </div>

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
                    <svg class="w-8 h-8 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Hủy phương tiện</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Phương tiện sẽ chuyển sang trạng thái "Đã hủy", ngày hủy được ghi nhận là hôm nay. Dữ liệu vẫn được giữ lại.</p>
            </div>
            <div x-show="confirmToggle && !confirmToggle.isLock" class="px-6 pt-2 pb-5 text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Khôi phục phương tiện</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Phương tiện sẽ chuyển về trạng thái "Hoạt động", ngày hủy sẽ được xóa.</p>
            </div>
            <div class="mx-6 mb-5 flex items-center gap-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                <div class="w-9 h-9 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 dark:text-slate-500">Biển số</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white font-mono truncate" x-text="confirmToggle?.name"></p>
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
                        Hủy phương tiện
                    </button>
                    <button x-show="confirmToggle && !confirmToggle.isLock" type="submit"
                            class="block w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">
                        Khôi phục
                    </button>
                </form>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection
