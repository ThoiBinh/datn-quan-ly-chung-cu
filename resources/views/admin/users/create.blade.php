@extends('layouts.admin')
@section('title', 'Thêm tài khoản')
@section('page-title', 'Thêm tài khoản mới')

@section('content')
<div class="max-w-3xl"
     x-data="{ tab: '{{ old('type', $typeTab) }}' }">

    <!-- Tab chọn loại tài khoản -->
    <div class="flex gap-2 mb-5">
        <button type="button" @click="tab = 'nhan_vien'"
                :class="tab === 'nhan_vien'
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Nhân viên
        </button>
        <button type="button" @click="tab = 'cu_dan'"
                :class="tab === 'cu_dan'
                    ? 'bg-emerald-600 text-white shadow-sm'
                    : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Cư dân
        </button>
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
    <div x-show="tab === 'nhan_vien'" x-transition>
        <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
            @csrf
            <input type="hidden" name="type" value="nhan_vien">

            <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800">Thông tin nhân viên</h2>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

                <!-- Họ tên -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Họ tên <span class="text-red-500">*</span></label>
                    <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nguyễn Văn A">
                </div>

                <!-- Chức vụ -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Chức vụ <span class="text-red-500">*</span></label>
                    <select name="chuc_vu" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Chọn chức vụ --</option>
                        @foreach($chucVu as $cv)
                            <option value="{{ $cv->id }}" {{ old('chuc_vu') == $cv->id ? 'selected' : '' }}>
                                {{ $cv->chuc_vu }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mã nhân viên -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mã nhân viên</label>
                    <input type="text" name="ma_nhan_vien" value="{{ old('ma_nhan_vien') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Tự động nếu bỏ trống">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="example@email.com">
                </div>

                <!-- SĐT -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Số điện thoại</label>
                    <input type="text" name="sdt" value="{{ old('sdt') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0912 345 678">
                </div>

                <!-- CCCD -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">CCCD <span class="text-red-500">*</span></label>
                    <input type="text" name="cccd" value="{{ old('cccd') }}" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                           placeholder="012345678901">
                </div>

                <!-- Ngày sinh -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày sinh</label>
                    <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Ngày vào làm -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày vào làm</label>
                    <input type="date" name="ngay_vao_lam" value="{{ old('ngay_vao_lam') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Ngày nghỉ làm -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày nghỉ làm</label>
                    <input type="date" name="ngay_nghi_lam" value="{{ old('ngay_nghi_lam') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Trạng thái -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="trang_thai" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="1" {{ old('trang_thai', '1') == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ old('trang_thai') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>

                <!-- Mật khẩu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="mat_khau" required minlength="8"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Tối thiểu 8 ký tự">
                </div>

                <!-- Xác nhận mật khẩu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="mat_khau_confirmation" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Nhập lại mật khẩu">
                </div>

                <!-- Ghi chú -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ghi chú</label>
                    <textarea name="ghi_chu" rows="3"
                              class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                              placeholder="Ghi chú thêm...">{{ old('ghi_chu') }}</textarea>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex gap-3">
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Tạo nhân viên
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>

    <!-- ===================== FORM CƯ DÂN ===================== -->
    <div x-show="tab === 'cu_dan'" x-transition>
        <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
            @csrf
            <input type="hidden" name="type" value="cu_dan">

            <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800">Thông tin cư dân</h2>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

                <!-- Họ tên đệm -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Họ tên đệm <span class="text-red-500">*</span></label>
                    <input type="text" name="ho_ten_dem" value="{{ old('ho_ten_dem') }}" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Nguyễn Văn">
                </div>

                <!-- Tên -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tên <span class="text-red-500">*</span></label>
                    <input type="text" name="ten" value="{{ old('ten') }}" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="An">
                </div>

                <!-- SĐT -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Số điện thoại</label>
                    <input type="text" name="sdt" value="{{ old('sdt') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="0912 345 678">
                </div>

                <!-- CCCD -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">CCCD <span class="text-red-500">*</span></label>
                    <input type="text" name="cccd" value="{{ old('cccd') }}" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono"
                           placeholder="012345678901">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="example@email.com">
                </div>

                <!-- Ngày sinh -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày sinh</label>
                    <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Giới tính -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Giới tính</label>
                    <select name="gioi_tinh"
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">-- Chọn giới tính --</option>
                        <option value="1" {{ old('gioi_tinh') == '1' ? 'selected' : '' }}>Nam</option>
                        <option value="0" {{ old('gioi_tinh') == '0' ? 'selected' : '' }}>Nữ</option>
                    </select>
                </div>

                <!-- Tỉnh / Thành phố -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tỉnh / Thành phố</label>
                    <input type="text" name="tinh" value="{{ old('tinh') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Hà Nội">
                </div>

                <!-- Xã / Phường -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Xã / Phường</label>
                    <input type="text" name="xa" value="{{ old('xa') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Phường Trúc Bạch">
                </div>

                <!-- Địa chỉ -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Địa chỉ chi tiết</label>
                    <input type="text" name="dia_chi" value="{{ old('dia_chi') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Số nhà, tên đường...">
                </div>

                <!-- Trạng thái -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="trang_thai" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="1" {{ old('trang_thai', '1') == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ old('trang_thai') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>

                <!-- Mật khẩu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="mat_khau" required minlength="8"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Tối thiểu 8 ký tự">
                </div>

                <!-- Xác nhận mật khẩu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="mat_khau_confirmation" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Nhập lại mật khẩu">
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex gap-3">
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Tạo cư dân
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
