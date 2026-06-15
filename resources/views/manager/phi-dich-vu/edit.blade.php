@extends('layouts.manager')
@section('title', 'Sửa phí dịch vụ')
@section('page-title', 'Sửa phí dịch vụ')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">{{ $phiDichVu->ten_phi_dich_vu }}</h2></div>
        <form method="POST" action="{{ route('manager.phi-dich-vu.update', $phiDichVu) }}" class="p-5 space-y-4">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên phí dịch vụ <span class="text-red-500">*</span></label>
                <input type="text" name="ten_phi_dich_vu" value="{{ old('ten_phi_dich_vu', $phiDichVu->ten_phi_dich_vu) }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại phí</label>
                    <select name="loai_phi_dich_vu" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($loaiPhiDichVu as $l)<option value="{{ $l->id }}" {{ $phiDichVu->loai_phi_dich_vu == $l->id ? 'selected' : '' }}>{{ $l->ten_loai_phi_dich_vu }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đơn giá (đ) <span class="text-red-500">*</span></label>
                    <input type="number" name="don_gia" value="{{ old('don_gia', $phiDichVu->don_gia) }}" required min="0" step="1000"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị tính</label>
                    <select name="don_vi_tinh" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($donViTinh as $dv)<option value="{{ $dv->id }}" {{ $phiDichVu->don_vi_tinh == $dv->id ? 'selected' : '' }}>{{ $dv->don_vi }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cách tính</label>
                    <select name="loai_tinh_phi" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($loaiTinhPhi as $lt)<option value="{{ $lt->id }}" {{ $phiDichVu->loai_tinh_phi == $lt->id ? 'selected' : '' }}>{{ $lt->ten_loai }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Lưu</button>
                <a href="{{ route('manager.phi-dich-vu.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
