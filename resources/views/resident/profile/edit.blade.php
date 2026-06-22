@extends('layouts.resident')
@section('title', 'Chỉnh sửa hồ sơ')
@section('page-title', 'Chỉnh sửa hồ sơ')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">Thông tin cá nhân</h2></div>
        <form method="POST" action="{{ route('resident.profile.update') }}" class="p-5 space-y-4">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên đệm</label>
                <input type="text" name="ho_ten_dem" value="{{ old('ho_ten_dem', $cuDan->ho_ten_dem) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên <span class="text-red-500">*</span></label>
                <input type="text" name="ten" value="{{ old('ten', $cuDan->ten) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                <input type="tel" name="sdt" value="{{ old('sdt', $cuDan->sdt) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành</label>
                <input type="text" name="tinh" value="{{ old('tinh', $cuDan->tinh) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                <input type="text" name="dia_chi" value="{{ old('dia_chi', $cuDan->dia_chi) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">Lưu thay đổi</button>
                <a href="{{ route('resident.profile.show') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
