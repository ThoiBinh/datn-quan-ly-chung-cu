@extends('layouts.manager')
@section('title', 'Đăng bài bảng tin')
@section('page-title', 'Đăng bài bảng tin')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">Nội dung bài đăng</h2></div>
        <form method="POST" action="{{ route('manager.bang-tin.store') }}" class="p-5 space-y-4">
            @csrf
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" name="tieu_de" value="{{ old('tieu_de') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL hình ảnh</label>
                <input type="url" name="hinh_url" value="{{ old('hinh_url') }}"
                       placeholder="https://..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-400">Nhập URL ảnh bìa cho bài đăng (không bắt buộc)</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung <span class="text-red-500">*</span></label>
                <textarea name="noi_dung" rows="10" required
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y">{{ old('noi_dung') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">Đăng bài</button>
                <a href="{{ route('manager.bang-tin.index') }}" class="flex-1 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 text-center">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
