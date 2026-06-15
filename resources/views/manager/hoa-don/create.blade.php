@extends('layouts.manager')
@section('title', 'Tạo hóa đơn')
@section('page-title', 'Tạo hóa đơn mới')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">Tạo hóa đơn tháng</h2></div>
        <form method="POST" action="{{ route('manager.hoa-don.store') }}" class="p-5 space-y-4">
            @csrf
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Căn hộ <span class="text-red-500">*</span></label>
                <select name="can_ho" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Chọn căn hộ --</option>
                    @foreach($canHo as $ch)
                    <option value="{{ $ch->id }}" {{ old('can_ho') == $ch->id ? 'selected' : '' }}>
                        {{ $ch->toaNha?->ten_toa_nha }} - {{ $ch->so_can_ho }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tháng <span class="text-red-500">*</span></label>
                    <select name="thang" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @for($i=1;$i<=12;$i++)
                        <option value="{{ $i }}" {{ old('thang', now()->month) == $i ? 'selected' : '' }}>Tháng {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Năm <span class="text-red-500">*</span></label>
                    <input type="number" name="nam" value="{{ old('nam', now()->year) }}" required min="2020"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-700">
                Hệ thống sẽ tự động tính tổng phí dựa trên các phí dịch vụ đã gán cho căn hộ.
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Tạo hóa đơn</button>
                <a href="{{ route('manager.hoa-don.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
