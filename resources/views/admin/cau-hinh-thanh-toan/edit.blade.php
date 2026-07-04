@extends('layouts.admin')
@section('title', 'Chỉnh sửa cấu hình thanh toán')
@section('page-title', 'Chỉnh sửa cấu hình thanh toán')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700">{{ $cauHinhThanhToan->ten_thuoc_tinh }}</h3>
            <span class="text-xs text-gray-400 font-mono">{{ $cauHinhThanhToan->ma_thuoc_tinh }}</span>
        </div>
        <form action="{{ route('admin.cau-hinh-thanh-toan.update', $cauHinhThanhToan) }}" method="POST" class="p-6 space-y-5">
            @csrf @method('PUT')
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif

            @if($cauHinhThanhToan->mo_ta)
            <p class="text-sm text-gray-500">{{ $cauHinhThanhToan->mo_ta }}</p>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Giá trị</label>
                @if($cauHinhThanhToan->kieu_du_lieu === 'boolean')
                <label class="inline-flex items-center gap-2 mt-1">
                    <input type="hidden" name="gia_tri" value="0">
                    <input type="checkbox" name="gia_tri" value="1" {{ old('gia_tri', $cauHinhThanhToan->gia_tri) == '1' ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-600">Bật</span>
                </label>
                @elseif($cauHinhThanhToan->kieu_du_lieu === 'textarea')
                <textarea name="gia_tri" rows="3"
                          placeholder="{{ $cauHinhThanhToan->placeholder }}"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('gia_tri', $cauHinhThanhToan->gia_tri) }}</textarea>
                @else
                <input type="text" name="gia_tri" value="{{ old('gia_tri', $cauHinhThanhToan->gia_tri) }}"
                       placeholder="{{ $cauHinhThanhToan->placeholder }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                @endif
                @if($cauHinhThanhToan->la_bao_mat)
                <p class="mt-1 text-xs text-amber-600">Trường bảo mật — giá trị sẽ được ẩn bớt khi hiển thị.</p>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <label class="inline-flex items-center gap-2">
                    <input type="hidden" name="trang_thai" value="0">
                    <input type="checkbox" name="trang_thai" value="1" {{ old('trang_thai', $cauHinhThanhToan->trang_thai) ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Kích hoạt cấu hình này</span>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">Lưu thay đổi</button>
                <a href="{{ route('admin.cau-hinh-thanh-toan.show', $cauHinhThanhToan) }}" class="flex-1 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 text-center transition-colors">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
