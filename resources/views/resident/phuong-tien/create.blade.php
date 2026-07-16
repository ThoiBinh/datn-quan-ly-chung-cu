@extends('layouts.resident')
@section('title', 'Đăng ký xe')
@section('page-title', 'Đăng ký phương tiện mới')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">Thông tin phương tiện</h2></div>
        <form method="POST" action="{{ route('resident.phuong-tien.store') }}" class="p-5 space-y-4">
            @csrf
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div class="grid grid-cols-2 gap-4">
                @if($dsCanHo->count() > 1)
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Căn hộ <span class="text-red-500">*</span></label>
                    <select name="can_ho" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Chọn căn hộ --</option>
                        @foreach($dsCanHo as $ch)
                        <option value="{{ $ch->id }}" {{ (string) old('can_ho') === (string) $ch->id ? 'selected' : '' }}>
                            {{ $ch->so_can_ho }} — {{ $ch->toaNha?->ten_toa_nha }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="can_ho" value="{{ $dsCanHo->first()->id }}">
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biển số xe <span class="text-red-500">*</span></label>
                    <input type="text" name="bien_so" value="{{ old('bien_so') }}" required placeholder="51A-12345"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại phương tiện <span class="text-red-500">*</span></label>
                    <select name="loai_phuong_tien" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Chọn loại --</option>
                        @foreach($loaiPhuongTien as $l)<option value="{{ $l->id }}" {{ old('loai_phuong_tien') == $l->id ? 'selected' : '' }}>{{ $l->ten_loai_phuong_tien }}</option>@endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên / Hãng xe</label>
                    <input type="text" name="ten_phuong_tien" value="{{ old('ten_phuong_tien') }}" placeholder="Honda Wave, Toyota Vios..."
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Màu sắc</label>
                    <input type="text" name="mau_sac" value="{{ old('mau_sac') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Năm sản xuất</label>
                    <input type="number" name="nam_san_xuat" value="{{ old('nam_san_xuat') }}" min="2000" max="{{ date('Y') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-700">
                Sau khi đăng ký, Ban quản lý sẽ xét duyệt và cấp thẻ xe cho bạn.
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">Đăng ký</button>
                <a href="{{ route('resident.phuong-tien.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
