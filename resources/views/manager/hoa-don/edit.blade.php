@extends('layouts.manager')
@section('title', 'Sửa hóa đơn')
@section('page-title', 'Sửa hóa đơn')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800">Hóa đơn #{{ $hoaDon->id }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">Tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }} — {{ $hoaDon->canHo?->so_can_ho }}</p>
        </div>
        <form method="POST" action="{{ route('manager.hoa-don.update', $hoaDon) }}" class="p-5 space-y-4">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tháng <span class="text-red-500">*</span></label>
                    <select name="thang" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @for($i = 1; $i <= 12; $i++)<option value="{{ $i }}" {{ $hoaDon->thang == $i ? 'selected' : '' }}>Tháng {{ $i }}</option>@endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Năm <span class="text-red-500">*</span></label>
                    <select name="nam" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @for($y = date('Y')-1; $y <= date('Y')+1; $y++)<option value="{{ $y }}" {{ $hoaDon->nam == $y ? 'selected' : '' }}>{{ $y }}</option>@endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn</label>
                    <input type="date" name="han_thanh_toan" value="{{ old('han_thanh_toan', $hoaDon->han_thanh_toan?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="trang_thai" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="1" {{ $hoaDon->trang_thai == 1 ? 'selected' : '' }}>Chưa thanh toán</option>
                        <option value="2" {{ $hoaDon->trang_thai == 2 ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="3" {{ $hoaDon->trang_thai == 3 ? 'selected' : '' }}>Quá hạn</option>
                        <option value="4" {{ $hoaDon->trang_thai == 4 ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea name="ghi_chu" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('ghi_chu', $hoaDon->ghi_chu) }}</textarea>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Chi tiết hóa đơn</p>
                @foreach($hoaDon->chiTiet as $ct)
                <div class="flex justify-between items-center py-1.5 text-sm">
                    <span class="text-gray-600">{{ $ct->phiDichVu?->ten_phi_dich_vu ?? 'Khoản phí' }}</span>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-500 text-xs">{{ number_format($ct->don_gia) }}đ × {{ $ct->so_luong }}</span>
                        <input type="number" name="chi_tiet[{{ $ct->id }}][thanh_tien]" value="{{ old('chi_tiet.'.$ct->id.'.thanh_tien', $ct->thanh_tien) }}"
                               class="w-28 px-2 py-1 border border-gray-300 rounded text-sm text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                @endforeach
                <div class="border-t border-gray-200 mt-3 pt-3 flex justify-between items-center">
                    <span class="font-semibold text-gray-700">Tổng cộng:</span>
                    <span class="font-bold text-gray-900">{{ number_format($hoaDon->tong_tien) }}đ</span>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Lưu</button>
                <a href="{{ route('manager.hoa-don.show', $hoaDon) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
