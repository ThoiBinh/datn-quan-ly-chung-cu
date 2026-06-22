@extends('layouts.admin')
@section('title', 'Quản lý nhân viên')
@section('page-title', 'Quản lý nhân viên')

@section('content')
@php
    $sortDir = fn($col) => $sort === $col ? ($direction === 'asc' ? 'desc' : 'asc') : 'asc';
    $sortUrl = fn($col) => request()->fullUrlWithQuery(['sort' => $col, 'direction' => $sortDir($col), 'page' => 1]);
    $sortIcon = fn($col) => $sort === $col
        ? ($direction === 'asc'
            ? '<svg class="w-3 h-3 inline ml-1 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>'
            : '<svg class="w-3 h-3 inline ml-1 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>')
        : '<svg class="w-3 h-3 inline ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>';
@endphp

<div class="space-y-4">

    <!-- Search + Filter + Add -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.nhan-vien.index') }}" class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tên, email, SĐT, mã NV, CCCD..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <select name="status"
                    class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">Tất cả trạng thái</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đã khóa</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">Lọc</button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.nhan-vien.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition-colors">Xóa lọc</a>
            @endif
            <div class="ml-auto">
                <a href="{{ route('admin.nhan-vien.create') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Thêm nhân viên
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm"
         x-data="{
             confirmToggle: null,
             openToggle(id, name, action) { this.confirmToggle = { id, name, action }; },
             closeToggle() { this.confirmToggle = null; }
         }">

        <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-slate-400">
                Tổng: <span class="font-semibold text-gray-700 dark:text-white">{{ $nhanVien->total() }}</span> nhân viên
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('ma_nhan_vien') }}" class="hover:text-blue-600 flex items-center gap-1">
                                Mã NV {!! $sortIcon('ma_nhan_vien') !!}
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('ho_ten') }}" class="hover:text-blue-600 flex items-center gap-1">
                                Họ tên {!! $sortIcon('ho_ten') !!}
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            <a href="{{ $sortUrl('email') }}" class="hover:text-blue-600 flex items-center gap-1">
                                Email {!! $sortIcon('email') !!}
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">SĐT</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Chức vụ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">CCCD</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('ngay_sinh') }}" class="hover:text-blue-600 flex items-center gap-1">
                                Ngày sinh {!! $sortIcon('ngay_sinh') !!}
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('ngay_vao_lam') }}" class="hover:text-blue-600 flex items-center gap-1">
                                Vào làm {!! $sortIcon('ngay_vao_lam') !!}
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ $sortUrl('trang_thai') }}" class="hover:text-blue-600 flex items-center gap-1">
                                Trạng thái {!! $sortIcon('trang_thai') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 w-28"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($nhanVien as $nv)
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-4 py-3.5 font-mono text-xs text-gray-500 dark:text-slate-400 whitespace-nowrap">
                            {{ $nv->ma_nhan_vien ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-xs font-bold text-blue-700 dark:text-blue-400 flex-shrink-0">
                                    {{ mb_strtoupper(mb_substr($nv->ho_ten, 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800 dark:text-white whitespace-nowrap">{{ $nv->ho_ten }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 text-xs">{{ $nv->email }}</td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 whitespace-nowrap">{{ $nv->sdt ?: '—' }}</td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                {{ $nv->chucVu?->chuc_vu ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-mono text-xs text-gray-600 dark:text-slate-300 whitespace-nowrap">{{ $nv->cccd ?: '—' }}</td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 whitespace-nowrap text-xs">
                            {{ $nv->ngay_sinh?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 whitespace-nowrap text-xs">
                            {{ $nv->ngay_vao_lam?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($nv->trang_thai == 1)
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
                                <a href="{{ route('admin.nhan-vien.show', $nv) }}" title="Xem chi tiết"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.nhan-vien.edit', $nv) }}" title="Chỉnh sửa"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @if($nv->id !== auth('nhanvien')->id())
                                <button type="button" title="{{ $nv->trang_thai == 1 ? 'Khóa' : 'Mở khóa' }}"
                                        @click="openToggle({{ $nv->id }}, '{{ addslashes($nv->ho_ten) }}', '{{ route('admin.nhan-vien.toggle-status', $nv) }}')"
                                        class="p-1.5 rounded-md transition-colors {{ $nv->trang_thai == 1 ? 'text-gray-400 hover:text-amber-600 hover:bg-amber-50' : 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50' }}">
                                    @if($nv->trang_thai == 1)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    @endif
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-14 text-center">
                            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="text-sm text-gray-400 dark:text-slate-500">Không tìm thấy nhân viên nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($nhanVien->hasPages())
        <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $nhanVien->links() }}
        </div>
        @endif

        <!-- Modal xác nhận toggle -->
        <div x-show="confirmToggle !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
             @click.self="closeToggle()" style="display:none">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-sm mx-4 p-6"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-white">Xác nhận thay đổi trạng thái</h3>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                            Nhân viên: <span class="font-medium text-gray-700 dark:text-slate-200" x-text="confirmToggle?.name"></span>
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end">
                    <button @click="closeToggle()" type="button"
                            class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        Hủy
                    </button>
                    <form :action="confirmToggle?.action" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                            Xác nhận
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
