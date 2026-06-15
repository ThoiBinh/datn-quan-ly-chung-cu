@extends('layouts.manager')
@section('title', 'Thêm phương tiện')
@section('page-title', 'Thêm phương tiện')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">Thông tin phương tiện</h2></div>
        <form method="POST" action="{{ route('manager.phuong-tien.store') }}" class="p-5 space-y-4">
            @csrf
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Căn hộ <span class="text-red-500">*</span></label>
                    <select name="can_ho" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Chọn căn hộ --</option>
                        @foreach($canHo as $ch)<option value="{{ $ch->id }}" {{ old('can_ho') == $ch->id ? 'selected' : '' }}>{{ $ch->so_can_ho }} - {{ $ch->toaNha?->ten_toa_nha }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biển số xe <span class="text-red-500">*</span></label>
                    <input type="text" name="bien_so" value="{{ old('bien_so') }}" required placeholder="51A-12345"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại phương tiện <span class="text-red-500">*</span></label>
                    <select name="loai_phuong_tien" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Chọn loại --</option>
                        @foreach($loaiPhuongTien as $l)<option value="{{ $l->id }}" {{ old('loai_phuong_tien') == $l->id ? 'selected' : '' }}>{{ $l->ten_loai_phuong_tien }}</option>@endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên / Hãng xe</label>
                    <input type="text" name="ten_phuong_tien" value="{{ old('ten_phuong_tien') }}" placeholder="Honda Wave, Toyota Vios..."
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày đăng ký</label>
                    <input type="date" name="ngay_dang_ky" value="{{ old('ngay_dang_ky', date('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Thêm xe</button>
                <a href="{{ route('manager.phuong-tien.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
