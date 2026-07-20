@extends('layouts.manager')
@section('title', 'Sửa thuộc tính')
@section('page-title', 'Sửa thuộc tính')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Chỉnh sửa thuộc tính</h2>
            <a href="{{ route('manager.thuoc-tinh.show', $thuocTinh) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại
            </a>
        </div>

        <form action="{{ route('manager.thuoc-tinh.update', $thuocTinh) }}" method="POST" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Tên thuộc tính <span class="text-red-500">*</span>
                </label>
                <input type="text" name="ten_thuoc_tinh" value="{{ old('ten_thuoc_tinh', $thuocTinh->ten_thuoc_tinh) }}"
                       class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500
                              {{ $errors->has('ten_thuoc_tinh') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                       autofocus/>
                @error('ten_thuoc_tinh')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Tối đa 150 ký tự. Tên phải là duy nhất trong hệ thống.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Lưu thay đổi
                </button>
                <a href="{{ route('manager.thuoc-tinh.show', $thuocTinh) }}"
                   class="px-5 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
