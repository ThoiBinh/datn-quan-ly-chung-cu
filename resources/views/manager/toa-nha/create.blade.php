@extends('layouts.manager')
@section('title', 'Thêm tòa nhà')
@section('page-title', 'Thêm tòa nhà mới')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">Thông tin tòa nhà</h2></div>
        <form method="POST" action="{{ route('manager.toa-nha.store') }}" class="p-5 space-y-4">
            @csrf
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    @foreach($errors->all() as $error)<p class="text-red-700 text-sm">{{ $error }}</p>@endforeach
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên tòa nhà <span class="text-red-500">*</span></label>
                <input type="text" name="ten_toa_nha" value="{{ old('ten_toa_nha') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                <input type="text" name="dia_chi" value="{{ old('dia_chi') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số tầng</label>
                <input type="number" name="so_tang" value="{{ old('so_tang') }}" min="1" max="200"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">Thêm tòa nhà</button>
                <a href="{{ route('manager.toa-nha.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
