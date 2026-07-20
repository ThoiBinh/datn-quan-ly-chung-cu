@extends('layouts.manager')
@section('title', 'Sửa cư dân')
@section('page-title', 'Chỉnh sửa cư dân')

@section('content')
@php $canHoHienTai = $cuDan->canHoHienTai; @endphp

<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-5">
        {{-- Avatar mini --}}
        @if($cuDan->avatar_url)
            <img src="{{ $cuDan->avatar_url }}" alt="" class="w-10 h-10 rounded-xl object-cover">
        @else
            <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-300">{{ strtoupper(mb_substr($cuDan->ten, 0, 1)) }}</span>
            </div>
        @endif
        <div>
            <h1 class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $cuDan->ho_ten }}</h1>
            <p class="text-xs text-gray-400">ID #{{ $cuDan->id }}</p>
        </div>
        <a href="{{ route('manager.cu-dan.show', $cuDan) }}"
           class="ml-auto text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
            Xem chi tiết →
        </a>
    </div>

    <form method="POST" action="{{ route('manager.cu-dan.update', $cuDan) }}" class="space-y-5">
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
                    <input type="text" name="ho_ten_dem" value="{{ old('ho_ten_dem', $cuDan->ho_ten_dem) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('ho_ten_dem') border-red-400 @enderror">
                    @error('ho_ten_dem')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Tên --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Tên <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="ten" value="{{ old('ten', $cuDan->ten) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('ten') border-red-400 @enderror">
                    @error('ten')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- CCCD --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">CCCD/CMND</label>
                    <input type="text" name="cccd" value="{{ old('cccd', $cuDan->cccd) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-mono
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
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
                        <option value="1" @selected((string)old('gioi_tinh', $cuDan->gioi_tinh) === '1')>Nam</option>
                        <option value="0" @selected((string)old('gioi_tinh', $cuDan->gioi_tinh) === '0' && old('gioi_tinh', $cuDan->gioi_tinh) !== null && old('gioi_tinh', $cuDan->gioi_tinh) !== '')>Nữ</option>
                    </select>
                </div>
                {{-- Ngày sinh --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ngày sinh</label>
                    <input type="date" name="ngay_sinh"
                           value="{{ old('ngay_sinh', $cuDan->ngay_sinh?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('ngay_sinh') border-red-400 @enderror">
                    @error('ngay_sinh')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Số điện thoại --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Số điện thoại</label>
                    <input type="text" name="sdt" value="{{ old('sdt', $cuDan->sdt) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $cuDan->email) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Tỉnh/Thành --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tỉnh/Thành</label>
                    <input type="text" name="tinh" value="{{ old('tinh', $cuDan->tinh) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                {{-- Xã/Phường --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Xã/Phường</label>
                    <input type="text" name="xa" value="{{ old('xa', $cuDan->xa) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                {{-- Địa chỉ --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Địa chỉ chi tiết</label>
                    <input type="text" name="dia_chi" value="{{ old('dia_chi', $cuDan->dia_chi) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Phân công căn hộ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm"
             x-data="{ toaNhaChon: '{{ $canHoHienTai?->canHo?->toa_nha ?? '' }}' }">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h2 class="font-semibold text-gray-800 dark:text-gray-200">Phân công căn hộ</h2>
                </div>
                @if($canHoHienTai)
                <span class="text-xs text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded-full">
                    Đang ở: {{ $canHoHienTai->canHo?->toaNha?->ten_toa_nha }} — {{ $canHoHienTai->canHo?->so_can_ho }}
                </span>
                @endif
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
                        <option value="{{ $tn->id }}"
                                @selected($canHoHienTai?->canHo?->toa_nha == $tn->id)>
                            {{ $tn->ten_toa_nha }}
                        </option>
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
                                x-show="toaNhaChon === '' || toaNhaChon === '{{ $ch->toa_nha }}'"
                                @selected(old('can_ho', $canHoHienTai?->can_ho) == $ch->id)>
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
                        <option value="{{ $vt->id }}"
                                @selected(old('vai_tro', $canHoHienTai?->vai_tro) == $vt->id)>
                            {{ $vt->vai_tro }}
                        </option>
                        @endforeach
                    </select>
                    @error('vai_tro')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                {{-- Ngày chuyển đến --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ngày chuyển đến</label>
                    <input type="date" name="ngay_chuyen_den"
                           value="{{ old('ngay_chuyen_den', $canHoHienTai?->ngay_chuyen_den?->format('Y-m-d') ?? date('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="px-5 pb-4">
                <p class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2">
                    <strong>Lưu ý:</strong> Nếu chọn căn hộ khác với căn hộ hiện tại, hệ thống sẽ tự động ghi nhận lịch sử chuyển phòng.
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Lưu thay đổi
            </button>
            <a href="{{ route('manager.cu-dan.show', $cuDan) }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
