@extends('layouts.admin')
@section('title', 'Sửa nhân viên')
@section('page-title', 'Chỉnh sửa nhân viên')

@section('content')
<div class="max-w-3xl">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400 mb-5">
        <a href="{{ route('admin.nhan-vien.index') }}" class="hover:text-blue-600 transition-colors">Nhân viên</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.nhan-vien.show', $nhanVien) }}" class="hover:text-blue-600 transition-colors truncate max-w-xs">{{ $nhanVien->ho_ten }}</a>
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

    <form method="POST" action="{{ route('admin.nhan-vien.update', $nhanVien) }}"
          class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        @csrf @method('PUT')

        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-sm font-bold text-blue-700 dark:text-blue-400">
                    {{ mb_strtoupper(mb_substr($nhanVien->ho_ten, 0, 1)) }}
                </div>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $nhanVien->ho_ten }}</span>
            </div>
            <span class="text-xs text-gray-400 dark:text-slate-500 font-mono">ID #{{ $nhanVien->id }}</span>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Họ tên --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Họ tên <span class="text-red-500">*</span></label>
                <input type="text" name="ho_ten" value="{{ old('ho_ten', $nhanVien->ho_ten) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Chức vụ --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Chức vụ <span class="text-red-500">*</span></label>
                <select name="chuc_vu" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-slate-700">
                    <option value="">-- Chọn chức vụ --</option>
                    @foreach($chucVu as $cv)
                        <option value="{{ $cv->id }}" {{ old('chuc_vu', $nhanVien->chuc_vu) == $cv->id ? 'selected' : '' }}>{{ $cv->chuc_vu }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mã nhân viên --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Mã nhân viên</label>
                <input type="text" name="ma_nhan_vien" value="{{ old('ma_nhan_vien', $nhanVien->ma_nhan_vien) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $nhanVien->email) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- SĐT --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Số điện thoại</label>
                <input type="text" name="sdt" value="{{ old('sdt', $nhanVien->sdt) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- CCCD --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">CCCD <span class="text-red-500">*</span></label>
                <input type="text" name="cccd" value="{{ old('cccd', $nhanVien->cccd) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
            </div>

            {{-- Ngày sinh --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày sinh</label>
                <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh', $nhanVien->ngay_sinh?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Ngày vào làm --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày vào làm</label>
                <input type="date" name="ngay_vao_lam" value="{{ old('ngay_vao_lam', $nhanVien->ngay_vao_lam?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Ngày nghỉ làm --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày nghỉ làm</label>
                <input type="date" name="ngay_nghi_lam" value="{{ old('ngay_nghi_lam', $nhanVien->ngay_nghi_lam?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Trạng thái <span class="text-red-500">*</span></label>
                <select name="trang_thai" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-slate-700">
                    <option value="1" {{ old('trang_thai', $nhanVien->trang_thai) == 1 ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ old('trang_thai', $nhanVien->trang_thai) == 0 && old('trang_thai', $nhanVien->trang_thai) !== '' ? 'selected' : '' }}>Không hoạt động</option>
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
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Tối thiểu 8 ký tự">
            </div>

            {{-- Xác nhận mật khẩu --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Xác nhận mật khẩu</label>
                <input type="password" name="mat_khau_confirmation"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nhập lại mật khẩu mới">
            </div>

            {{-- Ghi chú --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ghi chú</label>
                <textarea name="ghi_chu" rows="3"
                          class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('ghi_chu', $nhanVien->ghi_chu) }}</textarea>
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Lưu thay đổi
            </button>
            <a href="{{ route('admin.nhan-vien.show', $nhanVien) }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
