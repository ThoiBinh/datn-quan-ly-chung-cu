@extends('layouts.admin')
@section('title', 'Đăng bài')
@section('page-title', 'Đăng bài mới')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700">Nội dung bài đăng</h3>
        </div>
        <form action="{{ route('admin.bang-tin.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" name="tieu_de" value="{{ old('tieu_de') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    URL hình ảnh
                    <span class="text-xs text-gray-400 font-normal ml-1">(tùy chọn)</span>
                </label>
                <input type="url" name="hinh_url" value="{{ old('hinh_url') }}" placeholder="https://..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('hinh_url') ? 'border-red-400' : '' }}"/>
                @error('hinh_url')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nội dung <span class="text-red-500">*</span></label>
                <textarea name="noi_dung" rows="12" required
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('noi_dung') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">Đăng bài</button>
                <a href="{{ route('admin.bang-tin.index') }}" class="flex-1 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 text-center transition-colors">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
