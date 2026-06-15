@extends('layouts.resident')
@section('title', 'Chi tiết hóa đơn')
@section('page-title', 'Chi tiết hóa đơn')

@section('content')
<div class="max-w-2xl space-y-6">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-gray-800">Hóa đơn tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">Căn hộ {{ $hoaDon->canHo?->so_can_ho }}</p>
            </div>
            @php
                $badge = match($hoaDon->trang_thai) {
                    1 => ['bg-yellow-100 text-yellow-700', 'Chưa thanh toán'],
                    2 => ['bg-green-100 text-green-700', 'Đã thanh toán'],
                    3 => ['bg-red-100 text-red-700', 'Quá hạn'],
                    4 => ['bg-gray-100 text-gray-600', 'Đã hủy'],
                    default => ['bg-gray-100 text-gray-600', 'N/A']
                };
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badge[0] }}">{{ $badge[1] }}</span>
        </div>
        <div class="p-5">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100">
                    <th class="text-left py-2 text-gray-500 font-medium">Khoản phí</th>
                    <th class="text-right py-2 text-gray-500 font-medium">Đơn giá</th>
                    <th class="text-right py-2 text-gray-500 font-medium">SL</th>
                    <th class="text-right py-2 text-gray-500 font-medium">Thành tiền</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($hoaDon->chiTiet as $ct)
                    <tr>
                        <td class="py-2.5 text-gray-700">{{ $ct->phiDichVu?->ten_phi_dich_vu ?? 'Khoản phí' }}</td>
                        <td class="py-2.5 text-right text-gray-600">{{ number_format($ct->don_gia) }}đ</td>
                        <td class="py-2.5 text-right text-gray-600">{{ $ct->so_luong }}</td>
                        <td class="py-2.5 text-right font-medium text-gray-800">{{ number_format($ct->thanh_tien) }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot><tr class="border-t-2 border-gray-200">
                    <td colspan="3" class="py-3 font-bold text-gray-800">Tổng cộng</td>
                    <td class="py-3 text-right font-bold text-lg text-gray-900">{{ number_format($hoaDon->tong_tien) }}đ</td>
                </tr></tfoot>
            </table>
        </div>
        @if($hoaDon->han_thanh_toan)
        <div class="px-5 pb-4 text-xs text-gray-500">Hạn thanh toán: <strong>{{ $hoaDon->han_thanh_toan->format('d/m/Y') }}</strong></div>
        @endif
    </div>

    @if(in_array($hoaDon->trang_thai, [1, 3]))
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Phương thức thanh toán</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <form method="POST" action="{{ route('resident.hoa-don.momo', $hoaDon) }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-3 border-2 border-pink-200 bg-pink-50 hover:bg-pink-100 rounded-xl text-sm font-medium text-pink-700 transition">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" fill="white" font-size="8" font-weight="bold">M</text></svg>
                    Thanh toán MoMo
                </button>
            </form>
            <form method="POST" action="{{ route('resident.hoa-don.vnpay', $hoaDon) }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-3 border-2 border-blue-200 bg-blue-50 hover:bg-blue-100 rounded-xl text-sm font-medium text-blue-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Thanh toán VNPay QR
                </button>
            </form>
        </div>
    </div>
    @endif

    @if($hoaDon->trang_thai == 2)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-3">Lịch sử thanh toán</h3>
        @foreach($hoaDon->lichSuThanhToan as $ls)
        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0 text-sm">
            <div>
                <p class="text-gray-700">{{ $ls->phuong_thuc_thanh_toan }}</p>
                <p class="text-xs text-gray-400">{{ $ls->createdAt?->format('d/m/Y H:i') }}</p>
            </div>
            <p class="font-semibold text-green-600">{{ number_format($ls->so_tien) }}đ</p>
        </div>
        @endforeach
    </div>
    @endif

    <div>
        <a href="{{ route('resident.hoa-don.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Quay lại danh sách</a>
    </div>
</div>
@endsection
