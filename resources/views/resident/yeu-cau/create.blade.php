@extends('layouts.resident')
@section('title', 'Gửi yêu cầu')
@section('page-title', 'Gửi yêu cầu mới')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">Nội dung yêu cầu</h2></div>
        <form method="POST" action="{{ route('resident.yeu-cau.store') }}" class="p-5 space-y-4">
            @csrf
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" name="tieu_de" value="{{ old('tieu_de') }}" required placeholder="Mô tả ngắn gọn vấn đề..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại yêu cầu <span class="text-red-500">*</span></label>
                    <select name="loai_yeu_cau" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Chọn loại --</option>
                        <option value="sua_chua" {{ old('loai_yeu_cau') == 'sua_chua' ? 'selected' : '' }}>Sửa chữa</option>
                        <option value="khieu_nai" {{ old('loai_yeu_cau') == 'khieu_nai' ? 'selected' : '' }}>Khiếu nại</option>
                        <option value="de_nghi" {{ old('loai_yeu_cau') == 'de_nghi' ? 'selected' : '' }}>Đề nghị</option>
                        <option value="ho_tro" {{ old('loai_yeu_cau') == 'ho_tro' ? 'selected' : '' }}>Hỗ trợ</option>
                        <option value="khac" {{ old('loai_yeu_cau') == 'khac' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mức độ ưu tiên</label>
                    <select name="muc_do" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="1" {{ old('muc_do', '1') == '1' ? 'selected' : '' }}>Thấp</option>
                        <option value="2" {{ old('muc_do') == '2' ? 'selected' : '' }}>Trung bình</option>
                        <option value="3" {{ old('muc_do') == '3' ? 'selected' : '' }}>Cao</option>
                        <option value="4" {{ old('muc_do') == '4' ? 'selected' : '' }}>Khẩn cấp</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung chi tiết <span class="text-red-500">*</span></label>
                <textarea name="noi_dung" rows="6" required placeholder="Mô tả chi tiết vấn đề bạn gặp phải..."
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none">{{ old('noi_dung') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">Gửi yêu cầu</button>
                <a href="{{ route('resident.yeu-cau.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
