@extends('layouts.admin')
@section('title', $phiDichVu->ten_phi_dich_vu)
@section('page-title', 'Chi tiết phí dịch vụ')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.phi-dich-vu.index') }}" class="hover:text-violet-600 transition-colors">Phí dịch vụ</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200 truncate max-w-xs">{{ $phiDichVu->ten_phi_dich_vu }}</span>
    </nav>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-violet-50 dark:bg-violet-900/20 rounded-xl border border-violet-200 dark:border-violet-800 p-5 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900 dark:text-white">{{ $phiDichVu->ten_phi_dich_vu }}</h1>
                <p class="text-sm text-violet-600 dark:text-violet-400 font-semibold mt-0.5">
                    {{ number_format((float)$phiDichVu->don_gia, 0, ',', '.') }}đ / {{ $phiDichVu->donViTinh?->don_vi ?? '—' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.phi-dich-vu.edit', $phiDichVu) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Thông tin cơ bản --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thông tin cơ bản</h2>
            </div>
            <dl class="divide-y divide-gray-100 dark:divide-slate-700">
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">ID</dt>
                    <dd class="text-sm font-mono text-gray-800 dark:text-white">#{{ $phiDichVu->id }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">Tên phí dịch vụ</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-white">{{ $phiDichVu->ten_phi_dich_vu }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">Đơn giá</dt>
                    <dd class="text-sm font-bold text-gray-800 dark:text-white tabular-nums">{{ number_format((float)$phiDichVu->don_gia, 0, ',', '.') }}đ</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">Người cập nhật</dt>
                    <dd class="text-sm text-gray-700 dark:text-slate-200">{{ $phiDichVu->nguoiCapNhat?->ho_ten ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">Ngày tạo</dt>
                    <dd class="text-sm text-gray-700 dark:text-slate-200">{{ $phiDichVu->createdAt ? \Carbon\Carbon::parse($phiDichVu->createdAt)->format('d/m/Y H:i') : '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-slate-400">Cập nhật lần cuối</dt>
                    <dd class="text-sm text-gray-700 dark:text-slate-200">{{ $phiDichVu->updatedAt ? \Carbon\Carbon::parse($phiDichVu->updatedAt)->format('d/m/Y H:i') : '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Thông tin phân loại + thống kê --}}
        <div class="space-y-5">
            {{-- Loại phí --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Phân loại</h2>
                </div>
                <dl class="divide-y divide-gray-100 dark:divide-slate-700">
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-slate-400">Loại phí dịch vụ</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                {{ $phiDichVu->loaiPhiDichVu?->ten_loai_phi_dich_vu ?? '—' }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-slate-400">Đơn vị tính</dt>
                        <dd class="text-sm font-medium text-gray-700 dark:text-slate-200">{{ $phiDichVu->donViTinh?->don_vi ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-slate-400">Loại tính phí</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400">
                                {{ $phiDichVu->loaiTinhPhi?->ten_loai ?? '—' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Thống kê --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thống kê sử dụng</h2>
                </div>
                <div class="p-5 grid grid-cols-1 gap-3">
                    <div class="flex items-center justify-between bg-violet-50 dark:bg-violet-900/20 rounded-lg px-4 py-3">
                        <span class="text-sm text-gray-600 dark:text-slate-300">Số căn hộ đang áp dụng</span>
                        <span class="text-lg font-bold text-violet-700 dark:text-violet-400">{{ $soCanHo }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách căn hộ đang áp dụng --}}
    @if($phiDichVu->canHo->isNotEmpty())
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
                Căn hộ đang áp dụng
                <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400">{{ $soCanHo }}</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase">Căn hộ</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase">Tòa nhà</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase">Đơn giá áp dụng</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-slate-300 text-xs uppercase">Trạng thái căn hộ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($phiDichVu->canHo as $canHo)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $canHo->so_can_ho }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $canHo->toaNha?->ten_toa_nha ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white tabular-nums">
                            {{ number_format((float)($canHo->pivot->don_gia ?? $phiDichVu->don_gia), 0, ',', '.') }}đ
                        </td>
                        <td class="px-4 py-3">
                            @if($canHo->trangThai)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                {{ $canHo->trangThai->ten_trang_thai ?? 'Hoạt động' }}
                            </span>
                            @else
                            <span class="text-gray-400 dark:text-slate-500 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center justify-between">
        @if($phiDichVu->canHo->isEmpty())
        <button type="button"
                @click="$dispatch('open-delete', { url: '{{ route('admin.phi-dich-vu.destroy', $phiDichVu) }}', name: '{{ addslashes($phiDichVu->ten_phi_dich_vu) }}' })"
                class="px-4 py-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
            Xóa phí dịch vụ
        </button>
        @else
        <div class="relative group">
            <button type="button" disabled
                    class="px-4 py-2.5 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900 text-red-400 dark:text-red-700 text-sm font-medium rounded-lg opacity-60 cursor-not-allowed">
                Xóa phí dịch vụ
            </button>
            <div class="absolute bottom-full left-0 mb-2 px-3 py-2 bg-gray-900 dark:bg-slate-700 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                Phí dịch vụ đã được sử dụng, không thể xóa.
            </div>
        </div>
        @endif

        <a href="{{ route('admin.phi-dich-vu.index') }}"
           class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
            Quay lại
        </a>
    </div>
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
@endsection
