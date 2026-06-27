@extends('layouts.resident')
@section('title', 'Chỉnh sửa hồ sơ')

@section('content')
<div class="max-w-2xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-5">
        <a href="{{ route('resident.profile.show') }}" class="hover:text-gray-600">Hồ sơ cá nhân</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-600 font-medium">Chỉnh sửa</span>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h1 class="text-base font-semibold text-gray-800">Chỉnh sửa thông tin cá nhân</h1>
            <p class="text-xs text-gray-400 mt-0.5">Chỉ những trường được phép mới có thể chỉnh sửa.</p>
        </div>

        <form method="POST" action="{{ route('resident.profile.update') }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                @foreach($errors->all() as $e)
                <p class="text-red-700 text-sm">• {{ $e }}</p>
                @endforeach
            </div>
            @endif

            {{-- Họ & Tên --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Họ tên đệm</label>
                    <input type="text" name="ho_ten_dem" value="{{ old('ho_ten_dem', $cuDan->ho_ten_dem) }}"
                           placeholder="Nguyễn Văn"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500
                                  {{ $errors->has('ho_ten_dem') ? 'border-red-400 bg-red-50' : '' }}"/>
                    @error('ho_ten_dem')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tên <span class="text-red-500">*</span></label>
                    <input type="text" name="ten" value="{{ old('ten', $cuDan->ten) }}"
                           placeholder="An"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500
                                  {{ $errors->has('ten') ? 'border-red-400 bg-red-50' : '' }}"/>
                    @error('ten')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Email & SĐT --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $cuDan->email) }}"
                           placeholder="example@gmail.com"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500
                                  {{ $errors->has('email') ? 'border-red-400 bg-red-50' : '' }}"/>
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Số điện thoại</label>
                    <input type="tel" name="sdt" value="{{ old('sdt', $cuDan->sdt) }}"
                           placeholder="0912345678"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500
                                  {{ $errors->has('sdt') ? 'border-red-400 bg-red-50' : '' }}"/>
                    @error('sdt')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Ngày sinh & Giới tính --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày sinh</label>
                    <input type="date" name="ngay_sinh"
                           value="{{ old('ngay_sinh', $cuDan->ngay_sinh?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500
                                  {{ $errors->has('ngay_sinh') ? 'border-red-400 bg-red-50' : '' }}"/>
                    @error('ngay_sinh')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Giới tính</label>
                    <select name="gioi_tinh" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">— Chọn giới tính —</option>
                        <option value="1" {{ old('gioi_tinh', $cuDan->gioi_tinh) == 1 ? 'selected' : '' }}>Nam</option>
                        <option value="0" {{ old('gioi_tinh', $cuDan->gioi_tinh) == 0 ? 'selected' : '' }}>Nữ</option>
                    </select>
                    @error('gioi_tinh')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Tỉnh & Xã --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tỉnh/Thành phố</label>
                    <input type="text" name="tinh" value="{{ old('tinh', $cuDan->tinh) }}"
                           placeholder="Hà Nội"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500
                                  {{ $errors->has('tinh') ? 'border-red-400 bg-red-50' : '' }}"/>
                    @error('tinh')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Xã/Phường</label>
                    <input type="text" name="xa" value="{{ old('xa', $cuDan->xa) }}"
                           placeholder="Phường Tây Hồ"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500
                                  {{ $errors->has('xa') ? 'border-red-400 bg-red-50' : '' }}"/>
                    @error('xa')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Địa chỉ --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Địa chỉ</label>
                <input type="text" name="dia_chi" value="{{ old('dia_chi', $cuDan->dia_chi) }}"
                       placeholder="123 Đường Láng, Đống Đa"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500
                              {{ $errors->has('dia_chi') ? 'border-red-400 bg-red-50' : '' }}"/>
                @error('dia_chi')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Trường readonly --}}
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Thông tin không thể chỉnh sửa</p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">CCCD/CMND</p>
                        <p class="text-gray-600 font-medium">{{ $cuDan->cccd ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Trạng thái tài khoản</p>
                        @php $ttLabel = $cuDan->trang_thai_label; @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ttLabel['class'] }}">
                            {{ $ttLabel['text'] }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
                    Lưu thay đổi
                </button>
                <a href="{{ route('resident.profile.show') }}"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
