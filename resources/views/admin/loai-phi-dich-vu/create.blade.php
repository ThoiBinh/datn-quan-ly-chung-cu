@extends('layouts.admin')
@section('title', 'Thêm loại phí dịch vụ')
@section('page-title', 'Thêm loại phí dịch vụ')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Thông tin loại phí dịch vụ</h2>
            <a href="{{ route('admin.loai-phi-dich-vu.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại
            </a>
        </div>

        <form action="{{ route('admin.loai-phi-dich-vu.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Tên loại phí dịch vụ <span class="text-red-500">*</span>
                </label>
                <input type="text" name="ten_loai_phi_dich_vu" value="{{ old('ten_loai_phi_dich_vu') }}"
                       placeholder="Ví dụ: Phí quản lý, Phí điện, Phí nước, Phí gửi xe..."
                       class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                              {{ $errors->has('ten_loai_phi_dich_vu') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                       autofocus/>
                @error('ten_loai_phi_dich_vu')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Tối đa 150 ký tự. Tên phải là duy nhất trong hệ thống.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Lưu loại phí
                </button>
                <a href="{{ route('admin.loai-phi-dich-vu.index') }}"
                   class="px-5 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
