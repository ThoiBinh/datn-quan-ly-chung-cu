@extends('layouts.admin')
@section('title', 'Sửa nhân viên')
@section('page-title', 'Chỉnh sửa nhân viên')

@section('content')
<div class="max-w-4xl">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400 mb-5">
        <a href="{{ route('admin.nhan-vien.index') }}" class="hover:text-blue-600 transition-colors">Nhân viên</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.nhan-vien.show', $nhanVien) }}" class="hover:text-blue-600 transition-colors truncate max-w-xs">{{ $nhanVien->ho_ten }}</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Chỉnh sửa</span>
    </nav>

    <form method="POST" action="{{ route('admin.nhan-vien.update', $nhanVien) }}" class="space-y-5">
        @csrf @method('PUT')

        @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <p class="text-sm font-semibold text-red-700 dark:text-red-300 mb-2">Vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)
                <li class="text-sm text-red-600 dark:text-red-400">{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Header nhân viên --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center flex-shrink-0">
                <span class="text-lg font-bold text-blue-600 dark:text-blue-300">
                    {{ mb_strtoupper(mb_substr($nhanVien->ho_ten, 0, 1)) }}
                </span>
            </div>
            <div>
                <p class="font-semibold text-blue-800 dark:text-blue-200">{{ $nhanVien->ho_ten }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400">{{ $nhanVien->ma_nhan_vien }} · {{ $nhanVien->chucVu?->chuc_vu ?? '—' }}</p>
            </div>
        </div>

        {{-- Thông tin cá nhân --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-white">Thông tin cá nhân</h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Họ tên --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Họ và tên <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="ho_ten" value="{{ old('ho_ten', $nhanVien->ho_ten) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('ho_ten') border-red-400 @enderror">
                    @error('ho_ten')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Mã nhân viên --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Mã nhân viên</label>
                    <input type="text" name="ma_nhan_vien" value="{{ old('ma_nhan_vien', $nhanVien->ma_nhan_vien) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('ma_nhan_vien') border-red-400 @enderror">
                    @error('ma_nhan_vien')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- CCCD --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        CCCD <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="cccd" value="{{ old('cccd', $nhanVien->cccd) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('cccd') border-red-400 @enderror">
                    @error('cccd')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- SĐT --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Số điện thoại</label>
                    <input type="text" name="sdt" value="{{ old('sdt', $nhanVien->sdt) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('sdt') border-red-400 @enderror">
                    @error('sdt')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Ngày sinh --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày sinh</label>
                    <input type="date" name="ngay_sinh"
                           value="{{ old('ngay_sinh', $nhanVien->ngay_sinh?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('ngay_sinh') border-red-400 @enderror">
                    @error('ngay_sinh')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Ngày vào làm --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày vào làm</label>
                    <input type="date" name="ngay_vao_lam"
                           value="{{ old('ngay_vao_lam', $nhanVien->ngay_vao_lam?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('ngay_vao_lam') border-red-400 @enderror">
                    @error('ngay_vao_lam')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Ngày nghỉ làm --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày nghỉ làm</label>
                    <input type="date" name="ngay_nghi_lam"
                           value="{{ old('ngay_nghi_lam', $nhanVien->ngay_nghi_lam?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('ngay_nghi_lam') border-red-400 @enderror">
                    @error('ngay_nghi_lam')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Ghi chú --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ghi chú</label>
                    <textarea name="ghi_chu" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500
                                     @error('ghi_chu') border-red-400 @enderror">{{ old('ghi_chu', $nhanVien->ghi_chu) }}</textarea>
                    @error('ghi_chu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Thông tin tài khoản --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-white">Thông tin tài khoản</h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Email đăng nhập <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $nhanVien->email) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Trạng thái --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Trạng thái <span class="text-red-500">*</span>
                    </label>
                    <select name="trang_thai" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500
                                   @error('trang_thai') border-red-400 @enderror">
                        <option value="1" @selected(old('trang_thai', $nhanVien->trang_thai) == 1)>Đang làm việc</option>
                        <option value="0" @selected(old('trang_thai', $nhanVien->trang_thai) == 0)>Đã nghỉ</option>
                    </select>
                    @error('trang_thai')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Mật khẩu mới --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Mật khẩu mới
                        <span class="text-gray-400 font-normal">(để trống nếu không đổi)</span>
                    </label>
                    <input type="password" name="mat_khau" minlength="8"
                           placeholder="Tối thiểu 8 ký tự"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('mat_khau') border-red-400 @enderror">
                    @error('mat_khau')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Xác nhận mật khẩu --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Xác nhận mật khẩu mới</label>
                    <input type="password" name="mat_khau_confirmation" minlength="8"
                           placeholder="Nhập lại mật khẩu mới"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Chức vụ --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-white">Chức vụ</h2>
            </div>
            <div class="p-5">
                <div class="max-w-sm">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Chức vụ <span class="text-red-500">*</span>
                    </label>
                    <select name="chuc_vu" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500
                                   @error('chuc_vu') border-red-400 @enderror">
                        <option value="">-- Chọn chức vụ --</option>
                        @foreach($dsChucVu as $cv)
                        <option value="{{ $cv->id }}" @selected(old('chuc_vu', $nhanVien->chuc_vu) == $cv->id)>{{ $cv->chuc_vu }}</option>
                        @endforeach
                    </select>
                    @error('chuc_vu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
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
