@extends('layouts.resident')
@section('title', 'Hóa đơn')
@section('page-title', 'Hóa đơn của tôi')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="flex items-center gap-3 p-5 border-b border-gray-200">
        <form method="GET" class="flex gap-2">
            <select name="trang_thai" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Tất cả</option>
                <option value="1" {{ request('trang_thai') == 1 ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="2" {{ request('trang_thai') == 2 ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="3" {{ request('trang_thai') == 3 ? 'selected' : '' }}>Quá hạn</option>
            </select>
            <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Lọc</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Kỳ</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Căn hộ</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Số tiền</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Hạn TT</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($hoaDon as $hd)
                @php
                    $badge = match($hd->trang_thai) {
                        1 => ['bg-yellow-100 text-yellow-700', 'Chưa TT'],
                        2 => ['bg-green-100 text-green-700', 'Đã TT'],
                        3 => ['bg-red-100 text-red-700', 'Quá hạn'],
                        4 => ['bg-gray-100 text-gray-600', 'Đã hủy'],
                        default => ['bg-gray-100 text-gray-600', 'N/A']
                    };
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 font-medium text-gray-800">Tháng {{ $hd->thang }}/{{ $hd->nam }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $hd->canHo?->so_can_ho }}</td>
                    <td class="px-5 py-4 text-right font-semibold text-gray-800">{{ number_format($hd->tong_tien) }}đ</td>
                    <td class="px-5 py-4 text-xs text-gray-500">{{ $hd->han_thanh_toan?->format('d/m/Y') }}</td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $badge[0] }}">{{ $badge[1] }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('resident.hoa-don.show', $hd) }}" class="text-emerald-600 hover:underline text-xs font-medium">
                            {{ in_array($hd->trang_thai, [1, 3]) ? 'Thanh toán' : 'Xem' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Chưa có hóa đơn nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($hoaDon->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">{{ $hoaDon->links() }}</div>
    @endif
</div>
@endsection
