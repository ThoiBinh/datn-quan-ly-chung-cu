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
    $gioiTinhMap = [1 => 'Nam', 0 => 'Nữ'];
@endphp

<div class="space-y-4">

    <!-- Search + Filter + Add -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.cu-dan.index') }}" class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tên, email, SĐT, CCCD..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <select name="status"
                    class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                <option value="">Tất cả trạng thái</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đã khóa</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">Lọc</button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.cu-dan.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition-colors">Xóa lọc</a>
            @endif
            <div class="ml-auto">
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
             closeToggle() { this.confirmToggle = null; }
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
                            <a href="{{ $sortUrl('ho_ten_dem') }}" class="hover:text-emerald-600 flex items-center gap-1">Họ tên đệm {!! $sortIcon('ho_ten_dem') !!}</a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('ten') }}" class="hover:text-emerald-600 flex items-center gap-1">Tên {!! $sortIcon('ten') !!}</a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('email') }}" class="hover:text-emerald-600 flex items-center gap-1">Email {!! $sortIcon('email') !!}</a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">SĐT</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">CCCD</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Giới tính</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('ngay_sinh') }}" class="hover:text-emerald-600 flex items-center gap-1">Ngày sinh {!! $sortIcon('ngay_sinh') !!}</a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Địa chỉ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('trang_thai') }}" class="hover:text-emerald-600 flex items-center gap-1">Trạng thái {!! $sortIcon('trang_thai') !!}</a>
                        </th>
                        <th class="px-4 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($cuDan as $cd)
                    @php
                        $diaChi = collect([$cd->dia_chi, $cd->xa, $cd->tinh])->filter()->implode(', ');
                    @endphp
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-4 py-3.5 font-medium text-gray-800 dark:text-white whitespace-nowrap">{{ $cd->ho_ten_dem }}</td>
                        <td class="px-4 py-3.5 font-medium text-gray-800 dark:text-white whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-xs font-bold text-emerald-700 dark:text-emerald-400 flex-shrink-0">
                                    {{ mb_strtoupper(mb_substr($cd->ten, 0, 1)) }}
                                </div>
                                {{ $cd->ten }}
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 text-xs">{{ $cd->email ?: '—' }}</td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 whitespace-nowrap">{{ $cd->sdt ?: '—' }}</td>
                        <td class="px-4 py-3.5 font-mono text-xs text-gray-600 dark:text-slate-300 whitespace-nowrap">{{ $cd->cccd ?: '—' }}</td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if(isset($cd->gioi_tinh) && $cd->gioi_tinh !== null)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $cd->gioi_tinh == 1 ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300' }}">
                                {{ $gioiTinhMap[$cd->gioi_tinh] ?? '—' }}
                            </span>
                            @else
                            <span class="text-gray-400 dark:text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 whitespace-nowrap text-xs">
                            {{ $cd->ngay_sinh ? \Carbon\Carbon::parse($cd->ngay_sinh)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 text-xs max-w-xs">
                            <span class="line-clamp-1" title="{{ $diaChi }}">{{ $diaChi ?: '—' }}</span>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($cd->trang_thai == 1)
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-500 dark:text-red-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Đã khóa
                                </span>
                            @endif
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
                                <button type="button" title="{{ $cd->trang_thai == 1 ? 'Khóa' : 'Mở khóa' }}"
                                        @click="openToggle({{ $cd->id }}, '{{ addslashes($cd->ho_ten) }}', '{{ route('admin.cu-dan.toggle-status', $cd) }}', {{ $cd->trang_thai == 1 ? 'true' : 'false' }})"
                                        class="p-1.5 rounded-md transition-colors {{ $cd->trang_thai == 1 ? 'text-gray-400 hover:text-amber-600 hover:bg-amber-50' : 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50' }}">
                                    @if($cd->trang_thai == 1)
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
                        <td colspan="10" class="px-5 py-14 text-center">
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
        <div x-show="confirmToggle !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"  x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in  duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             @click.self="closeToggle()" style="display:none">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                <!-- Header màu -->
                <div :class="confirmToggle?.isLock
                        ? 'bg-gradient-to-r from-amber-500 to-orange-500'
                        : 'bg-gradient-to-r from-emerald-500 to-teal-500'"
                     class="px-6 pt-6 pb-8 text-white">
                    <div class="flex items-start justify-between mb-4">
                        <div :class="confirmToggle?.isLock ? 'bg-white/20' : 'bg-white/20'"
                             class="w-12 h-12 rounded-xl flex items-center justify-center">
                            <template x-if="confirmToggle?.isLock">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </template>
                            <template x-if="!confirmToggle?.isLock">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                            </template>
                        </div>
                        <button @click="closeToggle()" type="button"
                                class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <h3 class="text-lg font-bold text-white" x-text="confirmToggle?.isLock ? 'Khóa tài khoản' : 'Mở khóa tài khoản'"></h3>
                    <p class="text-sm text-white/75 mt-1"
                       x-text="confirmToggle?.isLock ? 'Tài khoản sẽ bị vô hiệu hóa' : 'Tài khoản sẽ được kích hoạt lại'"></p>
                </div>

                <!-- Body -->
                <div class="px-6 py-5">
                    <!-- User info -->
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50 dark:bg-slate-700/60 border border-gray-100 dark:border-slate-600 mb-5">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-sm font-bold text-emerald-700 dark:text-emerald-400 flex-shrink-0"
                             x-text="confirmToggle?.name ? confirmToggle.name.charAt(0).toUpperCase() : '?'">
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-400 dark:text-slate-500 uppercase tracking-wide">Cư dân</p>
                            <p class="font-semibold text-gray-800 dark:text-white truncate" x-text="confirmToggle?.name"></p>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div :class="confirmToggle?.isLock
                            ? 'bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800'
                            : 'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800'"
                         class="flex items-start gap-2.5 p-3.5 rounded-xl border text-sm">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0"
                             :class="confirmToggle?.isLock ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p :class="confirmToggle?.isLock ? 'text-amber-700 dark:text-amber-400' : 'text-emerald-700 dark:text-emerald-400'"
                           x-text="confirmToggle?.isLock
                               ? 'Cư dân sẽ không thể đăng nhập vào hệ thống sau khi bị khóa.'
                               : 'Cư dân sẽ có thể đăng nhập và sử dụng hệ thống sau khi mở khóa.'">
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 mt-5">
                        <button @click="closeToggle()" type="button"
                                class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                            Hủy bỏ
                        </button>
                        <form :action="confirmToggle?.action" method="POST" class="flex-1">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    :class="confirmToggle?.isLock
                                        ? 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-300'
                                        : 'bg-emerald-500 hover:bg-emerald-600 focus:ring-emerald-300'"
                                    class="w-full py-2.5 text-white text-sm font-semibold rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                                    x-text="confirmToggle?.isLock ? 'Khóa tài khoản' : 'Mở khóa'">
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
