@extends('layouts.manager')
@section('title', 'Thêm nhân viên')
@section('page-title', 'Thêm nhân viên mới')

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('manager.nhan-vien.store') }}" class="space-y-5">
        @csrf

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

        {{-- Thông tin cá nhân --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Thông tin cá nhân</h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Họ tên --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Họ và tên <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" required
                           placeholder="VD: Nguyễn Văn An"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('ho_ten') border-red-400 @enderror">
                    @error('ho_ten')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Mã nhân viên --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Mã nhân viên</label>
                    <input type="text" name="ma_nhan_vien" value="{{ old('ma_nhan_vien') }}"
                           placeholder="Để trống → tự sinh (NV0001)"
                           readonly
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('ma_nhan_vien') border-red-400 @enderror">
                    @error('ma_nhan_vien')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- CCCD --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        CCCD <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="cccd" value="{{ old('cccd') }}" required
                           placeholder="Số CCCD/CMND"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('cccd') border-red-400 @enderror">
                    @error('cccd')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- SĐT --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Số điện thoại</label>
                    <input type="text" name="sdt" value="{{ old('sdt') }}"
                           placeholder="0901234567"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('sdt') border-red-400 @enderror">
                    @error('sdt')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Ngày sinh --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ngày sinh</label>
                    <input type="date" name="ngay_sinh"
                           value="{{ old('ngay_sinh') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('ngay_sinh') border-red-400 @enderror">
                    @error('ngay_sinh')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Ngày vào làm --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ngày vào làm</label>
                    <input type="date" name="ngay_vao_lam"
                           value="{{ old('ngay_vao_lam', now()->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('ngay_vao_lam') border-red-400 @enderror">
                    @error('ngay_vao_lam')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Ngày nghỉ làm --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ngày nghỉ làm</label>
                    <input type="date" name="ngay_nghi_lam"
                           value="{{ old('ngay_nghi_lam') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('ngay_nghi_lam') border-red-400 @enderror">
                    @error('ngay_nghi_lam')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Ghi chú --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ghi chú</label>
                    <textarea name="ghi_chu" rows="3" placeholder="Ghi chú thêm về nhân viên..."
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                     bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none
                                     @error('ghi_chu') border-red-400 @enderror">{{ old('ghi_chu') }}</textarea>
                    @error('ghi_chu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Thông tin tài khoản --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Thông tin tài khoản</h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Email đăng nhập <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="nhanvien@chungcu.vn"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Trạng thái --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Trạng thái <span class="text-red-500">*</span>
                    </label>
                    <select name="trang_thai" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500
                                   @error('trang_thai') border-red-400 @enderror">
                        <option value="1" @selected(old('trang_thai', '1') === '1')>Đang làm việc</option>
                        <option value="0" @selected(old('trang_thai') === '0')>Đã nghỉ</option>
                    </select>
                    @error('trang_thai')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Mật khẩu --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Mật khẩu <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="mat_khau" required minlength="8"
                           placeholder="Tối thiểu 8 ký tự"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('mat_khau') border-red-400 @enderror">
                    @error('mat_khau')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Xác nhận mật khẩu --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Xác nhận mật khẩu <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="mat_khau_confirmation" required minlength="8"
                           placeholder="Nhập lại mật khẩu"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Chức vụ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Chức vụ</h2>
            </div>
            <div class="p-5">
                <div class="max-w-sm">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Chức vụ <span class="text-red-500">*</span>
                    </label>
                    <select name="chuc_vu" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500
                                   @error('chuc_vu') border-red-400 @enderror">
                        <option value="">-- Chọn chức vụ --</option>
                        @foreach($dsChucVu as $cv)
                        <option value="{{ $cv->id }}" @selected(old('chuc_vu') == $cv->id)>{{ $cv->chuc_vu }}</option>
                        @endforeach
                    </select>
                    @error('chuc_vu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                        Vai trò (Admin/Manager) được xác định tự động theo chức vụ.
                    </p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                Thêm nhân viên
            </button>
            <a href="{{ route('manager.nhan-vien.index') }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
