@extends('layouts.admin')
@section('title', 'Chi tiết nhân viên')
@section('page-title', 'Chi tiết nhân viên')

@section('content')
@php $isActive = $nhanVien->trang_thai == 1; @endphp

<div class="max-w-4xl space-y-5"
     x-data="{
         confirmToggle: false,
         confirmAction: '{{ route('admin.nhan-vien.toggle-status', $nhanVien) }}'
     }">

    <!-- Header card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-xl font-bold text-blue-700 dark:text-blue-400 flex-shrink-0">
                {{ mb_strtoupper(mb_substr($nhanVien->ho_ten, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $nhanVien->ho_ten }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                        {{ $nhanVien->chucVu?->chuc_vu ?? 'Chưa phân công' }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400">{{ $nhanVien->email }}</p>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5 font-mono">{{ $nhanVien->ma_nhan_vien ?? 'Chưa có mã' }}</p>
            </div>
            @if($isActive)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400 flex-shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Hoạt động
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-xs font-semibold text-red-600 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 flex-shrink-0">
                <span class="w-2 h-2 rounded-full bg-red-500"></span> Đã khóa
            </span>
            @endif
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex flex-wrap gap-3">
            <a href="{{ route('admin.nhan-vien.edit', $nhanVien) }}"
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            @if($nhanVien->id !== auth('nhanvien')->id())
            @if($isActive)
            <button type="button" @click="confirmToggle = true"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-amber-500 hover:bg-amber-600 text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Khóa tài khoản
            </button>
            @else
            <button type="button" @click="confirmToggle = true"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-emerald-500 hover:bg-emerald-600 text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                Mở khóa
            </button>
            @endif
            @endif
            <a href="{{ route('admin.nhan-vien.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Detail cards -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Thông tin chi tiết</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            @php
            $fields = [
                ['label' => 'ID', 'value' => $nhanVien->id, 'mono' => true],
                ['label' => 'Mã nhân viên', 'value' => $nhanVien->ma_nhan_vien, 'mono' => true],
                ['label' => 'Họ tên', 'value' => $nhanVien->ho_ten],
                ['label' => 'Chức vụ', 'value' => $nhanVien->chucVu?->chuc_vu],
                ['label' => 'Email', 'value' => $nhanVien->email],
                ['label' => 'Số điện thoại', 'value' => $nhanVien->sdt],
                ['label' => 'CCCD', 'value' => $nhanVien->cccd, 'mono' => true],
                ['label' => 'Ngày sinh', 'value' => $nhanVien->ngay_sinh?->format('d/m/Y')],
                ['label' => 'Ngày vào làm', 'value' => $nhanVien->ngay_vao_lam?->format('d/m/Y')],
                ['label' => 'Ngày nghỉ làm', 'value' => $nhanVien->ngay_nghi_lam?->format('d/m/Y')],
                ['label' => 'Ngày tạo', 'value' => $nhanVien->created_at?->format('d/m/Y H:i')],
                ['label' => 'Cập nhật bởi', 'value' => $nhanVien->nguoi_cap_nhat],
            ];
            @endphp

            @foreach($fields as $f)
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">{{ $f['label'] }}</dt>
                <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200 {{ ($f['mono'] ?? false) ? 'font-mono' : '' }}">
                    {{ $f['value'] ?: '—' }}
                </dd>
            </div>
            @endforeach

            <!-- Trạng thái -->
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">Trạng thái</dt>
                <dd>
                    @if($isActive)
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Hoạt động
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-500 dark:text-red-400">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Đã khóa
                    </span>
                    @endif
                </dd>
            </div>

            @if($nhanVien->ghi_chu)
            <div class="sm:col-span-2 lg:col-span-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">Ghi chú</dt>
                <dd class="text-sm text-gray-700 dark:text-slate-200 whitespace-pre-wrap">{{ $nhanVien->ghi_chu }}</dd>
            </div>
            @endif

        </div>
    </div>

    <!-- Modal xác nhận toggle -->
    <template x-teleport="body">
    <div x-show="confirmToggle"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="confirmToggle = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden" @click.stop>

            <!-- Close -->
            <div class="flex justify-end px-4 pt-4">
                <button @click="confirmToggle = false" type="button"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Icon + title -->
            <div class="px-6 pt-2 pb-5 text-center">
                @if($isActive)
                <div class="bg-amber-100 dark:bg-amber-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Khóa tài khoản</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Tài khoản sẽ bị vô hiệu hóa, không thể đăng nhập.</p>
                @else
                <div class="bg-emerald-100 dark:bg-emerald-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Mở khóa tài khoản</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Tài khoản sẽ được kích hoạt, có thể đăng nhập trở lại.</p>
                @endif
            </div>

            <!-- User info -->
            <div class="mx-6 mb-5 flex items-center gap-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                @if($isActive)
                <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                @else
                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                @endif
                    {{ mb_strtoupper(mb_substr($nhanVien->ho_ten, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 dark:text-slate-500">Nhân viên</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $nhanVien->ho_ten }}</p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 px-6 pb-6">
                <button @click="confirmToggle = false" type="button"
                        class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy bỏ
                </button>
                <form :action="confirmAction" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    @if($isActive)
                    <button type="submit" class="block w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-gray-900 text-sm font-semibold rounded-xl transition-colors">
                        Khóa tài khoản
                    </button>
                    @else
                    <button type="submit" class="block w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">
                        Mở khóa
                    </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection
