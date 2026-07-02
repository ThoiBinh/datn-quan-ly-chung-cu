@extends('layouts.admin')
@section('title', 'Phí dịch vụ')
@section('page-title', 'Phí dịch vụ')

@section('content')
<div class="space-y-5">

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

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $tongTatCa }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-400">Tổng phí dịch vụ</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $tongDangSuDung }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-400">Đang áp dụng căn hộ</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $tongCanHo }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-400">Căn hộ đang dùng dịch vụ</p>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="p-4 border-b border-gray-100 dark:border-slate-700 flex flex-col gap-3">
            <form method="GET" action="{{ route('admin.phi-dich-vu.index') }}" class="flex flex-wrap items-center gap-2">
                {{-- Search --}}
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Tìm tên, loại, đơn vị..."
                           class="pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white dark:bg-slate-700 text-gray-800 dark:text-white w-52"/>
                </div>

                {{-- Filter: Loại phí --}}
                <select name="loai_phi_dich_vu" class="px-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white dark:bg-slate-700 text-gray-800 dark:text-white">
                    <option value="">Tất cả loại phí</option>
                    @foreach($dsLoaiPhi as $loai)
                    <option value="{{ $loai->id }}" {{ request('loai_phi_dich_vu') == $loai->id ? 'selected' : '' }}>{{ $loai->ten_loai_phi_dich_vu }}</option>
                    @endforeach
                </select>

                {{-- Filter: Đơn vị tính --}}
                <select name="don_vi_tinh" class="px-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white dark:bg-slate-700 text-gray-800 dark:text-white">
                    <option value="">Tất cả đơn vị</option>
                    @foreach($dsDonViTinh as $dv)
                    <option value="{{ $dv->id }}" {{ request('don_vi_tinh') == $dv->id ? 'selected' : '' }}>{{ $dv->don_vi }}</option>
                    @endforeach
                </select>

                {{-- Filter: Loại tính phí --}}
                <select name="loai_tinh_phi" class="px-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white dark:bg-slate-700 text-gray-800 dark:text-white">
                    <option value="">Tất cả loại tính</option>
                    @foreach($dsLoaiTinhPhi as $ltp)
                    <option value="{{ $ltp->id }}" {{ request('loai_tinh_phi') == $ltp->id ? 'selected' : '' }}>{{ $ltp->ten_loai }}</option>
                    @endforeach
                </select>

                {{-- Filter: Ngày tạo --}}
                <input type="date" name="tu_ngay" value="{{ request('tu_ngay') }}"
                       title="Từ ngày"
                       class="px-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white dark:bg-slate-700 text-gray-800 dark:text-white"/>
                <input type="date" name="den_ngay" value="{{ request('den_ngay') }}"
                       title="Đến ngày"
                       class="px-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white dark:bg-slate-700 text-gray-800 dark:text-white"/>

                {{-- Sort --}}
                <select name="sort" class="px-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white dark:bg-slate-700 text-gray-800 dark:text-white">
                    <option value="id" {{ request('sort', 'id') === 'id' ? 'selected' : '' }}>Theo ID</option>
                    <option value="ten_phi_dich_vu" {{ request('sort') === 'ten_phi_dich_vu' ? 'selected' : '' }}>Theo tên</option>
                    <option value="don_gia" {{ request('sort') === 'don_gia' ? 'selected' : '' }}>Theo đơn giá</option>
                    <option value="createdAt" {{ request('sort') === 'createdAt' ? 'selected' : '' }}>Theo ngày tạo</option>
                </select>
                <select name="direction" class="px-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white dark:bg-slate-700 text-gray-800 dark:text-white">
                    <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>Tăng dần</option>
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Giảm dần</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm rounded-lg transition-colors">Lọc</button>
                @if(request()->hasAny(['search','loai_phi_dich_vu','don_vi_tinh','loai_tinh_phi','tu_ngay','den_ngay','sort','direction']))
                <a href="{{ route('admin.phi-dich-vu.index') }}" class="px-3 py-2 border border-gray-200 dark:border-slate-600 text-gray-500 dark:text-slate-400 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Xóa lọc</a>
                @endif
            </form>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.loai-phi-dich-vu.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Loại phí dịch vụ
            </a>
            <a href="{{ route('admin.don-vi-tinh-phi-dich-vu.index') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Đơn vị tính
            </a>
                <a href="{{ route('admin.phi-dich-vu.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Thêm phí dịch vụ
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase tracking-wider">ID</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase tracking-wider">Tên phí dịch vụ</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase tracking-wider">Loại phí</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase tracking-wider">Đơn vị tính</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase tracking-wider">Loại tính phí</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase tracking-wider">Đơn giá</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase tracking-wider">Căn hộ</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase tracking-wider">Người cập nhật</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase tracking-wider">Ngày tạo</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($dsPhi as $phi)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3 text-gray-400 dark:text-slate-500 text-xs font-mono">#{{ $phi->id }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <span class="font-medium text-gray-800 dark:text-white">{{ $phi->ten_phi_dich_vu }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                {{ $phi->loaiPhiDichVu?->ten_loai_phi_dich_vu ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300 text-sm">{{ $phi->donViTinh?->don_vi ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300 text-sm">{{ $phi->loaiTinhPhi?->ten_loai ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white tabular-nums">
                            {{ number_format((float)$phi->don_gia, 0, ',', '.') }}đ
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold
                                {{ $phi->can_ho_count > 0 ? 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400' : 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400' }}">
                                {{ $phi->can_ho_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs">{{ $phi->nguoiCapNhat?->ho_ten ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-400 dark:text-slate-500 text-xs whitespace-nowrap">
                            {{ $phi->createdAt ? \Carbon\Carbon::parse($phi->createdAt)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.phi-dich-vu.show', $phi) }}"
                                   class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Xem">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.phi-dich-vu.edit', $phi) }}"
                                   class="p-1.5 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors" title="Sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @if($phi->can_ho_count === 0)
                                <button type="button"
                                        @click="$dispatch('open-delete', { url: '{{ route('admin.phi-dich-vu.destroy', $phi) }}', name: '{{ addslashes($phi->ten_phi_dich_vu) }}' })"
                                        class="p-1.5 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @else
                                <span class="p-1.5 text-gray-300 dark:text-slate-600 cursor-not-allowed" title="Đang dùng trong {{ $phi->can_ho_count }} căn hộ — không thể xóa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p class="text-sm text-gray-400 dark:text-slate-500">Không tìm thấy phí dịch vụ nào</p>
                                <a href="{{ route('admin.phi-dich-vu.create') }}" class="text-sm text-violet-600 hover:underline">Thêm mới</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dsPhi->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-700">{{ $dsPhi->links() }}</div>
        @endif
    </div>

    {{-- Modal xóa --}}
    <template x-teleport="body">
    <div x-data="{ open: false, url: '', name: '' }"
         @open-delete.window="open = true; url = $event.detail.url; name = $event.detail.name"
         x-show="open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="open = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-5">
                <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <p class="font-semibold text-gray-800 dark:text-white">Xóa phí dịch vụ?</p>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Phí <span class="font-medium text-gray-700 dark:text-slate-200" x-text="`«${name}»`"></span> sẽ bị xóa vĩnh viễn.</p>
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
