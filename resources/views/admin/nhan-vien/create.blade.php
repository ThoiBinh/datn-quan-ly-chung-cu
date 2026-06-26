@extends('layouts.admin')
@section('title', 'Thêm nhân viên')
@section('page-title', 'Thêm nhân viên mới')

@section('content')
<div class="max-w-3xl">

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

    <form method="POST" action="{{ route('admin.nhan-vien.store') }}"
          class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        @csrf

        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h2 class="font-semibold text-gray-800 dark:text-white">Thông tin nhân viên</h2>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Họ tên --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Họ tên <span class="text-red-500">*</span></label>
                <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nguyễn Văn A">
            </div>

            {{-- Chức vụ --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Chức vụ <span class="text-red-500">*</span></label>
                <select name="chuc_vu" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">-- Chọn chức vụ --</option>
                    @foreach($chucVu as $cv)
                        <option value="{{ $cv->id }}" {{ old('chuc_vu') == $cv->id ? 'selected' : '' }}>{{ $cv->chuc_vu }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mã nhân viên --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5" hidden>Mã nhân viên</label>
                <input type="text" name="ma_nhan_vien" value="{{ old('ma_nhan_vien') }}" hidden
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                       placeholder="Tự động nếu bỏ trống">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="nhanvien@email.com">
            </div>

            {{-- SĐT --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Số điện thoại</label>
                <input type="text" name="sdt" value="{{ old('sdt') }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="0912 345 678">
            </div>

            {{-- CCCD --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">CCCD <span class="text-red-500">*</span></label>
                <input type="text" name="cccd" value="{{ old('cccd') }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                       placeholder="012345678901">
            </div>

            {{-- Ngày sinh --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày sinh</label>
                <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh') }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Ngày vào làm --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày vào làm</label>
                <input type="date" name="ngay_vao_lam" value="{{ old('ngay_vao_lam') }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Ngày nghỉ làm --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày nghỉ làm</label>
                <input type="date" name="ngay_nghi_lam" value="{{ old('ngay_nghi_lam') }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Trạng thái <span class="text-red-500">*</span></label>
                <select name="trang_thai" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="1" {{ old('trang_thai', '1') == '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ old('trang_thai') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                </select>
            </div>

            {{-- Mật khẩu --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Mật khẩu <span class="text-red-500">*</span></label>
                <input type="password" name="mat_khau" required minlength="8"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Tối thiểu 8 ký tự">
            </div>

            {{-- Xác nhận mật khẩu --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                <input type="password" name="mat_khau_confirmation" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nhập lại mật khẩu">
            </div>

            {{-- Ghi chú --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ghi chú</label>
                <textarea name="ghi_chu" rows="3"
                          class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                          placeholder="Ghi chú thêm...">{{ old('ghi_chu') }}</textarea>
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Thêm nhân viên
            </button>
            <a href="{{ route('admin.nhan-vien.index') }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
