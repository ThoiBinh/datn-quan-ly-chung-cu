@extends('layouts.manager')
@section('title', 'Ghi nhận thanh toán')
@section('page-title', 'Ghi nhận thanh toán')

@section('content')
@php
$chuHo = $hoaDon->canHo?->chuHo?->cuDan;
@endphp

<div class="max-w-2xl mx-auto space-y-5">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-sm text-gray-500">
        <a href="{{ route('manager.hoa-don.index') }}" class="hover:text-indigo-600 transition-colors">Hóa đơn</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('manager.hoa-don.show', $hoaDon) }}" class="font-mono hover:text-indigo-600 transition-colors">{{ $hoaDon->ma_thanh_toan }}</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700">Thanh toán</span>
    </nav>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Invoice summary --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mb-4">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Mã hóa đơn</p>
                <p class="font-mono font-bold text-indigo-600">{{ $hoaDon->ma_thanh_toan }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Kỳ</p>
                <p class="font-semibold text-gray-800">Tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Căn hộ</p>
                <p class="font-semibold text-gray-800">{{ $hoaDon->canHo?->so_can_ho ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Chủ hộ</p>
                <p class="font-semibold text-gray-800">{{ $chuHo?->ho_ten ?? '—' }}</p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3 pt-4 border-t border-gray-100">
            <div class="rounded-lg bg-gray-50 p-3 text-center">
                <p class="text-xs text-gray-400 mb-0.5">Tổng tiền HĐ</p>
                <p class="font-bold text-gray-900 tabular-nums text-sm">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</p>
            </div>
            <div class="rounded-lg bg-emerald-50 p-3 text-center">
                <p class="text-xs text-emerald-600 mb-0.5">Đã thanh toán</p>
                <p class="font-bold text-emerald-700 tabular-nums text-sm">{{ number_format($hoaDon->so_tien_da_thanh_toan ?? 0, 0, ',', '.') }}đ</p>
            </div>
            <div class="rounded-lg bg-red-50 p-3 text-center">
                <p class="text-xs text-red-600 mb-0.5">Còn nợ</p>
                <p class="font-bold text-red-700 tabular-nums text-sm">{{ number_format($conNo, 0, ',', '.') }}đ</p>
            </div>
        </div>
    </div>

    {{-- Payment form --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <h2 class="text-sm font-semibold text-gray-800">Thông tin thanh toán</h2>
        </div>
        <form method="POST" action="{{ route('manager.thanh-toan.store', $hoaDon) }}" class="px-5 py-5 space-y-4"
              x-data="{ phuongThucSelected: '{{ old('phuong_thuc_thanh_toan', $phuongThuc->first()?->loai_phuong_thuc ?? '') }}' }">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Số tiền (đ) <span class="text-red-500">*</span></label>
                    <input type="number" name="so_tien" value="{{ old('so_tien', $conNo) }}"
                           min="1" max="{{ $conNo }}" step="1" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 tabular-nums">
                    <p class="text-xs text-gray-400 mt-1">Tối đa: <span class="font-medium text-red-600">{{ number_format($conNo, 0, ',', '.') }}đ</span></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Ngày thanh toán <span class="text-red-500">*</span></label>
                    <input type="date" name="ngay_thanh_toan" value="{{ old('ngay_thanh_toan', date('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Phương thức thanh toán <span class="text-red-500">*</span></label>
                @if($phuongThuc->isNotEmpty())
                <select name="phuong_thuc_thanh_toan" required x-model="phuongThucSelected"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @foreach($phuongThuc as $pt)
                    <option value="{{ $pt->loai_phuong_thuc }}" {{ old('phuong_thuc_thanh_toan') === $pt->loai_phuong_thuc ? 'selected' : '' }}>{{ $pt->loai_phuong_thuc }}</option>
                    @endforeach
                </select>
                @else
                <input type="text" name="phuong_thuc_thanh_toan" value="{{ old('phuong_thuc_thanh_toan') }}" required maxlength="100"
                       x-model="phuongThucSelected"
                       placeholder="VD: Tiền mặt, Chuyển khoản..."
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @endif
            </div>
            <div x-show="phuongThucSelected === 'Tiền mặt'" x-cloak>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Người thanh toán</label>
                <select name="nguoi_thanh_toan"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">— Chọn người thanh toán —</option>
                    @foreach($hoaDon->canHo?->cuDanHienTai ?? [] as $rel)
                        @if($rel->cuDan)
                        @php $cd = $rel->cuDan; $isChuHo = $cd->id === ($hoaDon->canHo?->chuHo?->cuDan?->id); @endphp
                        <option value="{{ $cd->id }}"
                            {{ old('nguoi_thanh_toan') == $cd->id || (!old('nguoi_thanh_toan') && $isChuHo) ? 'selected' : '' }}>
                            {{ $cd->ho_ten }}{{ $isChuHo ? ' (Chủ hộ)' : '' }}
                        </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Ghi chú</label>
                <textarea name="ghi_chu" rows="3" maxlength="500"
                          placeholder="Ghi chú thanh toán (tùy chọn)"
                          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">{{ old('ghi_chu') }}</textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('manager.hoa-don.show', $hoaDon) }}"
                   class="flex-1 text-center py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Xác nhận thanh toán
                </button>
            </div>
        </form>
    </div>

    {{-- Lịch sử thanh toán trước đó --}}
    @if($hoaDon->lichSuThanhToan->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Giao dịch trước đó</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($hoaDon->lichSuThanhToan as $ls)
            <div class="px-5 py-3 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-emerald-600 tabular-nums">{{ number_format($ls->so_tien ?? 0, 0, ',', '.') }}đ</p>
                    <p class="text-xs text-gray-400">{{ $ls->ngay_thanh_toan?->format('d/m/Y') ?? '—' }}
                        @if($ls->phuong_thuc_thanh_toan) · {{ $ls->phuong_thuc_thanh_toan }}@endif
                    </p>
                </div>
                <span class="text-xs text-gray-400">{{ $ls->nguonTao?->ten_nguon_tao ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
