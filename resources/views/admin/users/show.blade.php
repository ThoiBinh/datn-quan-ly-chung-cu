@extends('layouts.admin')
@section('title', $type === 'nhan-vien' ? 'Chi tiết nhân viên' : 'Chi tiết cư dân')
@section('page-title', $type === 'nhan-vien' ? 'Chi tiết nhân viên' : 'Chi tiết cư dân')

@section('content')
@php
    $isNV     = $type === 'nhan-vien';
    $accent   = $isNV ? 'blue' : 'emerald';
    $loai     = $isNV ? 'Nhân viên' : 'Cư dân';
    $hoTen    = $isNV ? $user->ho_ten : trim(($user->ho_ten_dem ?? '') . ' ' . ($user->ten ?? ''));
    $isActive = $user->trang_thai == 1;
@endphp

<div class="max-w-3xl" x-data="{
    confirmToggle: false,
    confirmAction: '{{ route('admin.users.toggle-status', ['type' => $type, 'id' => $user->id]) }}'
}">

    <!-- Header Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-5">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-4">
            <!-- Avatar -->
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-xl font-bold flex-shrink-0
                {{ $isNV ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                {{ mb_strtoupper(mb_substr($hoTen, 0, 1)) }}
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-gray-800">{{ $hoTen }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $isNV ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $loai }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">{{ $user->email ?: '—' }}</p>
                @if($isNV)
                <p class="text-xs text-gray-400 mt-0.5">{{ $user->chucVu?->chuc_vu ?? '—' }} · Mã: {{ $user->ma_nhan_vien ?? '—' }}</p>
                @endif
            </div>

            <!-- Trạng thái badge -->
            <div class="flex-shrink-0">
                @if($isActive)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Hoạt động
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-xs font-semibold text-red-600">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    Đã khóa
                </span>
                @endif
            </div>
        </div>

        <!-- Action buttons -->
        <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap gap-3">
            <a href="{{ route('admin.users.edit', ['type' => $type, 'id' => $user->id]) }}"
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Chỉnh sửa
            </a>

            @if(!($isNV && $user->id === auth('nhanvien')->id()))
            <button type="button" @click="confirmToggle = true"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors
                        {{ $isActive ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-emerald-500 hover:bg-emerald-600 text-white' }}">
                @if($isActive)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Khóa tài khoản
                @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
                Mở khóa
                @endif
            </button>
            @endif

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-emerald-700 text-sm">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-red-700 text-sm">{{ session('error') }}</p>
    </div>
    @endif

    <!-- ===== CHI TIẾT NHÂN VIÊN ===== -->
    @if($isNV)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Thông tin chi tiết</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            @php
            $fields = [
                ['label' => 'Mã nhân viên', 'value' => $user->ma_nhan_vien, 'mono' => true],
                ['label' => 'Họ tên',       'value' => $user->ho_ten],
                ['label' => 'Chức vụ',      'value' => $user->chucVu?->chuc_vu],
                ['label' => 'Email',         'value' => $user->email],
                ['label' => 'Số điện thoại','value' => $user->sdt],
                ['label' => 'CCCD',          'value' => $user->cccd, 'mono' => true],
                ['label' => 'Ngày sinh',     'value' => $user->ngay_sinh?->format('d/m/Y')],
                ['label' => 'Ngày vào làm', 'value' => $user->ngay_vao_lam?->format('d/m/Y')],
                ['label' => 'Ngày nghỉ làm','value' => $user->ngay_nghi_lam?->format('d/m/Y')],
                ['label' => 'Ngày tạo',     'value' => $user->created_at?->format('d/m/Y H:i')],
            ];
            @endphp

            @foreach($fields as $field)
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">{{ $field['label'] }}</dt>
                <dd class="text-sm font-semibold text-gray-700 {{ isset($field['mono']) && $field['mono'] ? 'font-mono' : '' }}">
                    {{ $field['value'] ?: '—' }}
                </dd>
            </div>
            @endforeach

            <!-- Trạng thái -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">Trạng thái</dt>
                <dd>
                    @if($isActive)
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Hoạt động
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-500">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Đã khóa
                    </span>
                    @endif
                </dd>
            </div>

            <!-- Ghi chú -->
            @if($user->ghi_chu)
            <div class="sm:col-span-2 lg:col-span-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">Ghi chú</dt>
                <dd class="text-sm text-gray-700 whitespace-pre-wrap">{{ $user->ghi_chu }}</dd>
            </div>
            @endif

        </div>
    </div>

    <!-- ===== CHI TIẾT CƯ DÂN ===== -->
    @else
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Thông tin chi tiết</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            @php
            $gioiTinhMap = [1 => 'Nam', 0 => 'Nữ'];
            $fields = [
                ['label' => 'Họ tên đệm',   'value' => $user->ho_ten_dem],
                ['label' => 'Tên',           'value' => $user->ten],
                ['label' => 'Email',         'value' => $user->email],
                ['label' => 'Số điện thoại','value' => $user->sdt],
                ['label' => 'CCCD',          'value' => $user->cccd, 'mono' => true],
                ['label' => 'Ngày sinh',     'value' => $user->ngay_sinh?->format('d/m/Y')],
                ['label' => 'Giới tính',     'value' => isset($user->gioi_tinh) ? ($gioiTinhMap[$user->gioi_tinh] ?? '—') : '—'],
                ['label' => 'Tỉnh / TP',    'value' => $user->tinh],
                ['label' => 'Xã / Phường',  'value' => $user->xa],
                ['label' => 'Địa chỉ',      'value' => $user->dia_chi],
                ['label' => 'Ngày tạo',     'value' => $user->created_at?->format('d/m/Y H:i')],
            ];
            @endphp

            @foreach($fields as $field)
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">{{ $field['label'] }}</dt>
                <dd class="text-sm font-semibold text-gray-700 {{ isset($field['mono']) && $field['mono'] ? 'font-mono' : '' }}">
                    {{ $field['value'] ?: '—' }}
                </dd>
            </div>
            @endforeach

            <!-- Trạng thái -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">Trạng thái</dt>
                <dd>
                    @if($isActive)
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Hoạt động
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-500">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Đã khóa
                    </span>
                    @endif
                </dd>
            </div>

        </div>
    </div>
    @endif

    <!-- Modal xác nhận toggle status -->
    <div x-show="confirmToggle"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
         @click.self="confirmToggle = false"
         style="display:none">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Xác nhận thay đổi trạng thái</h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $isActive ? 'Khóa' : 'Mở khóa' }} tài khoản <span class="font-medium text-gray-700">{{ $hoTen }}</span>?
                    </p>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button @click="confirmToggle = false" type="button"
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Hủy
                </button>
                <form :action="confirmAction" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 {{ $isActive ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600' }} text-white text-sm font-medium rounded-lg transition-colors">
                        Xác nhận
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
