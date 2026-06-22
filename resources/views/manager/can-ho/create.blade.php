@extends('layouts.manager')
@section('title', 'Thêm căn hộ')
@section('page-title', 'Thêm căn hộ mới')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">Thông tin căn hộ</h2></div>
        <form method="POST" action="{{ route('manager.can-ho.store') }}" class="p-5 space-y-4">
            @csrf
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tòa nhà <span class="text-red-500">*</span></label>
                    <select name="toa_nha" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Chọn tòa nhà --</option>
                        @foreach($toaNha as $tn)<option value="{{ $tn->id }}" {{ old('toa_nha') == $tn->id ? 'selected' : '' }}>{{ $tn->ten_toa_nha }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số căn hộ <span class="text-red-500">*</span></label>
                    <input type="text" name="so_can_ho" value="{{ old('so_can_ho') }}" required placeholder="Vd: A101"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tầng <span class="text-red-500">*</span></label>
                    <input type="number" name="tang" value="{{ old('tang') }}" required min="1"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá (đ)</label>
                    <input type="number" name="gia" value="{{ old('gia') }}" step="1000" min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại căn hộ <span class="text-red-500">*</span></label>
                    <select name="loai_can_ho" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Chọn loại --</option>
                        @foreach($loaiCanHo as $loai)<option value="{{ $loai->id }}" {{ old('loai_can_ho') == $loai->id ? 'selected' : '' }}>{{ $loai->ten_loai_can_ho }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="trang_thai" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($trangThai as $tt)<option value="{{ $tt->id }}" {{ old('trang_thai') == $tt->id ? 'selected' : '' }}>{{ $tt->ten_trang_thai }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Thêm căn hộ</button>
                <a href="{{ route('manager.can-ho.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
