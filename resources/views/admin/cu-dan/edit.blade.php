@extends('layouts.admin')
@section('title', 'Sửa cư dân')
@section('page-title', 'Chỉnh sửa cư dân')

@section('content')
@php $hoTen = trim(($cuDan->ho_ten_dem ?? '') . ' ' . ($cuDan->ten ?? '')); @endphp

<div class="max-w-3xl">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400 mb-5">
        <a href="{{ route('admin.cu-dan.index') }}" class="hover:text-emerald-600 transition-colors">Cư dân</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.cu-dan.show', $cuDan) }}" class="hover:text-emerald-600 transition-colors truncate max-w-xs">{{ $hoTen }}</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Chỉnh sửa</span>
    </nav>

    @if($errors->any())
    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <ul class="text-red-700 dark:text-red-400 text-sm space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.cu-dan.update', $cuDan) }}"
          class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        @csrf @method('PUT')

        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-sm font-bold text-emerald-700 dark:text-emerald-400">
                    {{ mb_strtoupper(mb_substr($hoTen, 0, 1)) }}
                </div>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $hoTen }}</span>
            </div>
            <span class="text-xs text-gray-400 dark:text-slate-500 font-mono">ID #{{ $cuDan->id }}</span>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Họ tên đệm --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Họ tên đệm <span class="text-red-500">*</span></label>
                <input type="text" name="ho_ten_dem" value="{{ old('ho_ten_dem', $cuDan->ho_ten_dem) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            {{-- Tên --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tên <span class="text-red-500">*</span></label>
                <input type="text" name="ten" value="{{ old('ten', $cuDan->ten) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            {{-- SĐT --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Số điện thoại</label>
                <input type="text" name="sdt" value="{{ old('sdt', $cuDan->sdt) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            {{-- CCCD --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">CCCD <span class="text-red-500">*</span></label>
                <input type="text" name="cccd" value="{{ old('cccd', $cuDan->cccd) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $cuDan->email) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            {{-- Ngày sinh --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày sinh</label>
                <input type="date" name="ngay_sinh"
                       value="{{ old('ngay_sinh', $cuDan->ngay_sinh ? \Carbon\Carbon::parse($cuDan->ngay_sinh)->format('Y-m-d') : '') }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            {{-- Giới tính --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Giới tính</label>
                <select name="gioi_tinh"
                        class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white dark:bg-slate-700">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="1" {{ old('gioi_tinh', $cuDan->gioi_tinh) == '1' ? 'selected' : '' }}>Nam</option>
                    <option value="0" {{ old('gioi_tinh', (string)$cuDan->gioi_tinh) === '0' ? 'selected' : '' }}>Nữ</option>
                </select>
            </div>

            {{-- Tỉnh --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tỉnh / Thành phố</label>
                <input type="text" name="tinh" value="{{ old('tinh', $cuDan->tinh) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            {{-- Xã --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Xã / Phường</label>
                <input type="text" name="xa" value="{{ old('xa', $cuDan->xa) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            {{-- Địa chỉ --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Địa chỉ chi tiết</label>
                <input type="text" name="dia_chi" value="{{ old('dia_chi', $cuDan->dia_chi) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Trạng thái <span class="text-red-500">*</span></label>
                <select name="trang_thai" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white dark:bg-slate-700">
                    <option value="1" {{ old('trang_thai', $cuDan->trang_thai) == 1 ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ old('trang_thai', $cuDan->trang_thai) == 0 && old('trang_thai', $cuDan->trang_thai) !== '' ? 'selected' : '' }}>Không hoạt động</option>
                </select>
            </div>

            {{-- Divider đổi mật khẩu --}}
            <div class="sm:col-span-2 pt-2 border-t border-gray-100 dark:border-slate-700">
                <p class="text-xs text-gray-400 dark:text-slate-500">Đổi mật khẩu — để trống nếu không thay đổi</p>
            </div>

            {{-- Mật khẩu mới --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Mật khẩu mới</label>
                <input type="password" name="mat_khau" minlength="8"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Tối thiểu 8 ký tự">
            </div>

            {{-- Xác nhận mật khẩu --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Xác nhận mật khẩu</label>
                <input type="password" name="mat_khau_confirmation"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Nhập lại mật khẩu mới">
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                Lưu thay đổi
            </button>
            <a href="{{ route('admin.cu-dan.show', $cuDan) }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
