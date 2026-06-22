@extends('layouts.manager')
@section('title', 'Sửa cư dân')
@section('page-title', 'Sửa thông tin cư dân')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">{{ $cuDan->ho_ten }}</h2></div>
        <form method="POST" action="{{ route('manager.cu-dan.update', $cuDan) }}" class="p-5 space-y-4">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên đệm</label>
                    <input type="text" name="ho_ten_dem" value="{{ old('ho_ten_dem', $cuDan->ho_ten_dem) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên <span class="text-red-500">*</span></label>
                    <input type="text" name="ten" value="{{ old('ten', $cuDan->ten) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="text" name="sdt" value="{{ old('sdt', $cuDan->sdt) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CCCD/CMND</label>
                    <input type="text" name="cccd" value="{{ old('cccd', $cuDan->cccd) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $cuDan->email) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                    <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh', $cuDan->ngay_sinh?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                    <select name="gioi_tinh" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Chọn --</option>
                        <option value="1" @selected((string)old('gioi_tinh', $cuDan->gioi_tinh) === '1')>Nam</option>
                        <option value="0" @selected((string)old('gioi_tinh', $cuDan->gioi_tinh) === '0' && old('gioi_tinh', $cuDan->gioi_tinh) !== '')>Nữ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành</label>
                    <input type="text" name="tinh" value="{{ old('tinh', $cuDan->tinh) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <input type="text" name="dia_chi" value="{{ old('dia_chi', $cuDan->dia_chi) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            @php $canHoHienTai = $cuDan->canHoHienTai; @endphp
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Phân công căn hộ</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Căn hộ</label>
                        <select name="can_ho" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Không phân công --</option>
                            @foreach($canHo as $ch)
                                <option value="{{ $ch->id }}" {{ old('can_ho', $canHoHienTai?->can_ho) == $ch->id ? 'selected' : '' }}>
                                    {{ $ch->toaNha?->ten_toa_nha }} - {{ $ch->so_can_ho }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                        <select name="vai_tro" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Chọn vai trò --</option>
                            @foreach($vaiTro as $vt)
                                <option value="{{ $vt->id }}" {{ old('vai_tro', $canHoHienTai?->vai_tro) == $vt->id ? 'selected' : '' }}>
                                    {{ $vt->vai_tro }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày chuyển đến</label>
                        <input type="date" name="ngay_chuyen_den"
                               value="{{ old('ngay_chuyen_den', $canHoHienTai?->ngay_chuyen_den?->format('Y-m-d') ?? date('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Lưu</button>
                <a href="{{ route('manager.cu-dan.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
