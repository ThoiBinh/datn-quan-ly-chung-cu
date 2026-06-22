@extends('layouts.admin')
@section('title', $type === 'nhan-vien' ? 'Sửa nhân viên' : 'Sửa cư dân')
@section('page-title', $type === 'nhan-vien' ? 'Chỉnh sửa nhân viên' : 'Chỉnh sửa cư dân')

@section('content')
@php
    $isNV  = $type === 'nhan-vien';
    $hoTen = $isNV ? $user->ho_ten : trim(($user->ho_ten_dem ?? '') . ' ' . ($user->ten ?? ''));
@endphp

<div class="max-w-3xl">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-5">
        <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600 transition-colors">Tài khoản</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('admin.users.show', ['type' => $type, 'id' => $user->id]) }}"
           class="hover:text-blue-600 transition-colors truncate max-w-xs">{{ $hoTen }}</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700">Chỉnh sửa</span>
    </div>

    <!-- Lỗi validation -->
    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="flex items-start gap-2">
            <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <ul class="text-red-700 text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- ===================== FORM NHÂN VIÊN ===================== -->
    @if($isNV)
    <form method="POST" action="{{ route('admin.users.update', ['type' => $type, 'id' => $user->id]) }}"
          class="bg-white rounded-xl border border-gray-200 shadow-sm">
        @csrf @method('PUT')

        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800">{{ $user->ho_ten }}</h2>
            </div>
            <span class="text-xs text-gray-400 font-mono">ID #{{ $user->id }}</span>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

            <!-- Họ tên -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Họ tên <span class="text-red-500">*</span></label>
                <input type="text" name="ho_ten" value="{{ old('ho_ten', $user->ho_ten) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Chức vụ -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Chức vụ <span class="text-red-500">*</span></label>
                <select name="chuc_vu" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">-- Chọn chức vụ --</option>
                    @foreach($chucVu as $cv)
                        <option value="{{ $cv->id }}" {{ old('chuc_vu', $user->chuc_vu) == $cv->id ? 'selected' : '' }}>
                            {{ $cv->chuc_vu }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Mã nhân viên -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mã nhân viên</label>
                <input type="text" name="ma_nhan_vien" value="{{ old('ma_nhan_vien', $user->ma_nhan_vien) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- SĐT -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Số điện thoại</label>
                <input type="text" name="sdt" value="{{ old('sdt', $user->sdt) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- CCCD -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">CCCD</label>
                <input type="text" name="cccd" value="{{ old('cccd', $user->cccd) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
            </div>

            <!-- Ngày sinh -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày sinh</label>
                <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh', $user->ngay_sinh?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Ngày vào làm -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày vào làm</label>
                <input type="date" name="ngay_vao_lam" value="{{ old('ngay_vao_lam', $user->ngay_vao_lam?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Ngày nghỉ làm -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày nghỉ làm</label>
                <input type="date" name="ngay_nghi_lam" value="{{ old('ngay_nghi_lam', $user->ngay_nghi_lam?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Trạng thái -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Trạng thái <span class="text-red-500">*</span></label>
                <select name="trang_thai" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="1" {{ old('trang_thai', $user->trang_thai) == 1 ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ old('trang_thai', $user->trang_thai) == 0 ? 'selected' : '' }}>Không hoạt động</option>
                </select>
            </div>

            <!-- Divider mật khẩu -->
            <div class="sm:col-span-2">
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-400">Đổi mật khẩu — để trống nếu không thay đổi</p>
                </div>
            </div>

            <!-- Mật khẩu mới -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu mới</label>
                <input type="password" name="mat_khau" minlength="8"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Tối thiểu 8 ký tự">
            </div>

            <!-- Xác nhận mật khẩu -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Xác nhận mật khẩu</label>
                <input type="password" name="mat_khau_confirmation"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nhập lại mật khẩu mới">
            </div>

            <!-- Ghi chú -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ghi chú</label>
                <textarea name="ghi_chu" rows="3"
                          class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('ghi_chu', $user->ghi_chu) }}</textarea>
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Lưu thay đổi
            </button>
            <a href="{{ route('admin.users.show', ['type' => $type, 'id' => $user->id]) }}"
               class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Hủy
            </a>
        </div>
    </form>

    <!-- ===================== FORM CƯ DÂN ===================== -->
    @else
    <form method="POST" action="{{ route('admin.users.update', ['type' => $type, 'id' => $user->id]) }}"
          class="bg-white rounded-xl border border-gray-200 shadow-sm">
        @csrf @method('PUT')

        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800">{{ $hoTen }}</h2>
            </div>
            <span class="text-xs text-gray-400 font-mono">ID #{{ $user->id }}</span>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

            <!-- Họ tên đệm -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Họ tên đệm <span class="text-red-500">*</span></label>
                <input type="text" name="ho_ten_dem" value="{{ old('ho_ten_dem', $user->ho_ten_dem) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>

            <!-- Tên -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tên <span class="text-red-500">*</span></label>
                <input type="text" name="ten" value="{{ old('ten', $user->ten) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- SĐT -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Số điện thoại</label>
                <input type="text" name="sdt" value="{{ old('sdt', $user->sdt) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- CCCD -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">CCCD</label>
                <input type="text" name="cccd" value="{{ old('cccd', $user->cccd) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Ngày sinh -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày sinh</label>
                <input type="date" name="ngay_sinh"
                       value="{{ old('ngay_sinh', $user->ngay_sinh ? \Carbon\Carbon::parse($user->ngay_sinh)->format('Y-m-d') : '') }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Giới tính -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Giới tính</label>
                <select name="gioi_tinh"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="1" {{ old('gioi_tinh', $user->gioi_tinh) == '1' ? 'selected' : '' }}>Nam</option>
                    <option value="0" {{ old('gioi_tinh', $user->gioi_tinh) === 0 || old('gioi_tinh', $user->gioi_tinh) === '0' ? 'selected' : '' }}>Nữ</option>
                </select>
            </div>

            <!-- Tỉnh -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tỉnh / Thành phố</label>
                <input type="text" name="tinh" value="{{ old('tinh', $user->tinh) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Xã -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Xã / Phường</label>
                <input type="text" name="xa" value="{{ old('xa', $user->xa) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Địa chỉ -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Địa chỉ chi tiết</label>
                <input type="text" name="dia_chi" value="{{ old('dia_chi', $user->dia_chi) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Trạng thái -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Trạng thái <span class="text-red-500">*</span></label>
                <select name="trang_thai" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                    <option value="1" {{ old('trang_thai', $user->trang_thai) == 1 ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ old('trang_thai', $user->trang_thai) == 0 ? 'selected' : '' }}>Không hoạt động</option>
                </select>
            </div>

            <!-- Divider mật khẩu -->
            <div class="sm:col-span-2">
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-400">Đổi mật khẩu — để trống nếu không thay đổi</p>
                </div>
            </div>

            <!-- Mật khẩu mới -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu mới</label>
                <input type="password" name="mat_khau" minlength="8"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Tối thiểu 8 ký tự">
            </div>

            <!-- Xác nhận mật khẩu -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Xác nhận mật khẩu</label>
                <input type="password" name="mat_khau_confirmation"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Nhập lại mật khẩu mới">
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                Lưu thay đổi
            </button>
            <a href="{{ route('admin.users.show', ['type' => $type, 'id' => $user->id]) }}"
               class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Hủy
            </a>
        </div>
    </form>
    @endif

</div>
@endsection
