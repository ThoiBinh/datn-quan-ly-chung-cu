@extends('layouts.admin')
@section('title', 'Thêm cư dân')
@section('page-title', 'Thêm cư dân mới')

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

    <form method="POST" action="{{ route('admin.cu-dan.store') }}" class="space-y-5">
        @csrf

        {{-- Thông tin cư dân --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-white">Thông tin cư dân</h2>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Họ tên đệm <span class="text-red-500">*</span></label>
                    <input type="text" name="ho_ten_dem" value="{{ old('ho_ten_dem') }}" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Nguyễn Văn">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tên <span class="text-red-500">*</span></label>
                    <input type="text" name="ten" value="{{ old('ten') }}" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="An">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Số điện thoại</label>
                    <input type="text" name="sdt" value="{{ old('sdt') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="0912 345 678">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">CCCD <span class="text-red-500">*</span></label>
                    <input type="text" name="cccd" value="{{ old('cccd') }}" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono"
                           placeholder="012345678901">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="cudan@email.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày sinh</label>
                    <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Giới tính</label>
                    <select name="gioi_tinh"
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white dark:bg-slate-700">
                        <option value="">-- Chọn giới tính --</option>
                        <option value="1" {{ old('gioi_tinh') === '1' ? 'selected' : '' }}>Nam</option>
                        <option value="0" {{ old('gioi_tinh') === '0' ? 'selected' : '' }}>Nữ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="trang_thai" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white dark:bg-slate-700">
                        <option value="1" {{ old('trang_thai', '1') == '1' ? 'selected' : '' }}>Đang cư trú</option>
                        <option value="2" {{ old('trang_thai') == '2' ? 'selected' : '' }}>Tạm vắng</option>
                        <option value="3" {{ old('trang_thai') == '3' ? 'selected' : '' }}>Đã chuyển đi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tỉnh / Thành phố</label>
                    <input type="text" name="tinh" value="{{ old('tinh') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Hà Nội">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Xã / Phường</label>
                    <input type="text" name="xa" value="{{ old('xa') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Phường Trúc Bạch">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Địa chỉ chi tiết</label>
                    <input type="text" name="dia_chi" value="{{ old('dia_chi') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Số nhà, tên đường...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="mat_khau" required minlength="8"
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Tối thiểu 8 ký tự">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="mat_khau_confirmation" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Nhập lại mật khẩu">
                </div>
            </div>
        </div>

        {{-- Phân công căn hộ --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm"
             x-data="{ toaNhaChon: '' }">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-white">Phân công căn hộ <span class="text-xs text-gray-400 font-normal">(tùy chọn)</span></h2>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tòa nhà</label>
                    <select x-model="toaNhaChon"
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">Tất cả tòa</option>
                        @foreach($toaNha as $tn)
                        <option value="{{ $tn->id }}">{{ $tn->ten_toa_nha }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Căn hộ</label>
                    <select name="can_ho"
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">— Không phân công —</option>
                        @foreach($canHoList as $ch)
                        <option value="{{ $ch->id }}"
                                x-show="toaNhaChon === '' || toaNhaChon === '{{ $ch->toa_nha }}'"
                                @selected(old('can_ho') == $ch->id)>
                            {{ $ch->toaNha?->ten_toa_nha }} — {{ $ch->so_can_ho }}
                        </option>
                        @endforeach
                    </select>
                    @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Vai trò</label>
                    <select name="vai_tro"
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">— Chọn vai trò —</option>
                        @foreach($vaiTro as $vt)
                        <option value="{{ $vt->id }}" @selected(old('vai_tro') == $vt->id)>{{ $vt->vai_tro }}</option>
                        @endforeach
                    </select>
                    @error('vai_tro')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày chuyển đến</label>
                    <input type="date" name="ngay_chuyen_den" value="{{ old('ngay_chuyen_den', date('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                Thêm cư dân
            </button>
            <a href="{{ route('admin.cu-dan.index') }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
