@extends('layouts.manager')
@section('title', 'Thêm cư dân')
@section('page-title', 'Thêm cư dân mới')

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('manager.cu-dan.store') }}" class="space-y-5">
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
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Thông tin cá nhân</h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Họ tên đệm --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Họ tên đệm</label>
                    <input type="text" name="ho_ten_dem" value="{{ old('ho_ten_dem') }}"
                           placeholder="VD: Nguyễn Văn"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                  @error('ho_ten_dem') border-red-400 @enderror">
                    @error('ho_ten_dem')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Tên --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Tên <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="ten" value="{{ old('ten') }}" required
                           placeholder="VD: An"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                  @error('ten') border-red-400 @enderror">
                    @error('ten')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- CCCD --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">CCCD/CMND</label>
                    <input type="text" name="cccd" value="{{ old('cccd') }}"
                           placeholder="Số CCCD hoặc CMND"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-mono
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                  @error('cccd') border-red-400 @enderror">
                    @error('cccd')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Giới tính --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Giới tính</label>
                    <select name="gioi_tinh"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Chọn —</option>
                        <option value="1" @selected(old('gioi_tinh') == '1')>Nam</option>
                        <option value="0" @selected(old('gioi_tinh') === '0')>Nữ</option>
                    </select>
                </div>
                {{-- Ngày sinh --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ngày sinh</label>
                    <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('ngay_sinh') border-red-400 @enderror">
                    @error('ngay_sinh')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Số điện thoại --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Số điện thoại</label>
                    <input type="text" name="sdt" value="{{ old('sdt') }}"
                           placeholder="0xxxxxxxxx"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="example@email.com"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Mật khẩu --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Mật khẩu</label>
                    <input type="password" name="mat_khau"
                           placeholder="Để trống = mặc định 12345678"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('mat_khau') border-red-400 @enderror">
                    @error('mat_khau')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Tỉnh/Thành --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tỉnh/Thành</label>
                    <input type="text" name="tinh" value="{{ old('tinh') }}"
                           placeholder="VD: TP. Hồ Chí Minh"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                {{-- Xã/Phường --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Xã/Phường</label>
                    <input type="text" name="xa" value="{{ old('xa') }}"
                           placeholder="VD: Phường 1"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                {{-- Địa chỉ --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Địa chỉ chi tiết</label>
                    <input type="text" name="dia_chi" value="{{ old('dia_chi') }}"
                           placeholder="Số nhà, đường..."
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Phân công căn hộ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm"
             x-data="{ toaNhaChon: '' }">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Phân công căn hộ
                    <span class="text-xs text-gray-400 font-normal">(tùy chọn)</span>
                </h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Lọc theo tòa --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tòa nhà</label>
                    <select x-model="toaNhaChon"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tất cả tòa</option>
                        @foreach($toaNha as $tn)
                        <option value="{{ $tn->id }}">{{ $tn->ten_toa_nha }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Căn hộ --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Căn hộ</label>
                    <select name="can_ho"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Không phân công —</option>
                        @foreach($canHoList as $ch)
                        <option value="{{ $ch->id }}"
                                data-toa="{{ $ch->toa_nha }}"
                                x-show="toaNhaChon === '' || toaNhaChon === '{{ $ch->toa_nha }}'"
                                @selected(old('can_ho') == $ch->id)>
                            {{ $ch->toaNha?->ten_toa_nha }} — {{ $ch->so_can_ho }}
                        </option>
                        @endforeach
                    </select>
                    @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Vai trò --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vai trò</label>
                    <select name="vai_tro"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Chọn vai trò —</option>
                        @foreach($vaiTro as $vt)
                        <option value="{{ $vt->id }}" @selected(old('vai_tro') == $vt->id)>{{ $vt->vai_tro }}</option>
                        @endforeach
                    </select>
                    @error('vai_tro')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Ngày chuyển đến --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ngày chuyển đến</label>
                    <input type="date" name="ngay_chuyen_den"
                           value="{{ old('ngay_chuyen_den', date('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Thêm cư dân
            </button>
            <a href="{{ route('manager.cu-dan.index') }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
