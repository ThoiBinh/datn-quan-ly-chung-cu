@extends('layouts.manager')
@section('title', 'Sửa hóa đơn')
@section('page-title', 'Sửa hóa đơn')

@section('content')
<div class="max-w-lg">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 mb-5">
        <a href="{{ route('manager.hoa-don.index') }}" class="hover:text-indigo-600 transition-colors">Hóa đơn</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('manager.hoa-don.show', $hoaDon) }}" class="hover:text-indigo-600 transition-colors font-mono">{{ $hoaDon->ma_thanh_toan }}</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700">Chỉnh sửa</span>
    </nav>

    <!-- Summary -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-indigo-600 font-medium">{{ $hoaDon->ma_thanh_toan }}</p>
                <p class="text-sm font-semibold text-gray-800 mt-0.5">
                    Tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }} — {{ $hoaDon->canHo?->so_can_ho ?? '—' }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400">Tổng tiền</p>
                <p class="text-lg font-bold text-gray-900 tabular-nums">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <h2 class="text-sm font-semibold text-gray-800">Cập nhật hóa đơn</h2>
        </div>

        <form action="{{ route('manager.hoa-don.update', $hoaDon) }}" method="POST" class="p-5 space-y-5">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Hạn thanh toán</label>
                <input type="date" name="han_thanh_toan"
                       value="{{ old('han_thanh_toan', $hoaDon->han_thanh_toan ? $hoaDon->han_thanh_toan->format('Y-m-d') : '') }}"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                    Trạng thái <span class="text-red-500">*</span>
                </label>
                <select name="trang_thai"
                        class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('trang_thai') ? 'border-red-400' : 'border-gray-300' }}">
                    <option value="{{ \App\Models\HoaDon::TRANG_THAI_CHUA_THANH_TOAN }}"
                            {{ old('trang_thai', $hoaDon->trang_thai) == \App\Models\HoaDon::TRANG_THAI_CHUA_THANH_TOAN ? 'selected' : '' }}>
                        Chưa thanh toán
                    </option>
                    <option value="{{ \App\Models\HoaDon::TRANG_THAI_DA_THANH_TOAN }}"
                            {{ old('trang_thai', $hoaDon->trang_thai) == \App\Models\HoaDon::TRANG_THAI_DA_THANH_TOAN ? 'selected' : '' }}>
                        Đã thanh toán
                    </option>
                    <option value="{{ \App\Models\HoaDon::TRANG_THAI_QUA_HAN }}"
                            {{ old('trang_thai', $hoaDon->trang_thai) == \App\Models\HoaDon::TRANG_THAI_QUA_HAN ? 'selected' : '' }}>
                        Quá hạn
                    </option>
                    <option value="{{ \App\Models\HoaDon::TRANG_THAI_DA_HUY }}"
                            {{ old('trang_thai', $hoaDon->trang_thai) == \App\Models\HoaDon::TRANG_THAI_DA_HUY ? 'selected' : '' }}>
                        Đã hủy
                    </option>
                </select>
                @error('trang_thai')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('manager.hoa-don.show', $hoaDon) }}"
                   class="px-4 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Hủy
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
