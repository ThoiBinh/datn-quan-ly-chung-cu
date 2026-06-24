@extends('layouts.admin')
@section('title', 'Đăng thông báo')
@section('page-title', 'Đăng thông báo mới')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700">Nội dung thông báo</h3>
        </div>
        <form action="{{ route('admin.thong-bao.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" name="tieu_de" value="{{ old('tieu_de') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('tieu_de') ? 'border-red-400' : '' }}"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nội dung <span class="text-red-500">*</span></label>
                <textarea name="noi_dung" rows="10" required
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y {{ $errors->has('noi_dung') ? 'border-red-400' : '' }}">{{ old('noi_dung') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Đăng thông báo
                </button>
                <a href="{{ route('admin.thong-bao.index') }}"
                   class="flex-1 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 text-center transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
