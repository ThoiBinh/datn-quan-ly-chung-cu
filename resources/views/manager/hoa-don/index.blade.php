@extends('layouts.manager')
@section('title', 'Quản lý hóa đơn')
@section('page-title', 'Quản lý hóa đơn')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 border-b border-gray-200">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Mã thanh toán..."
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-44">
            <select name="trang_thai" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('trang_thai') == '1' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="2" {{ request('trang_thai') == '2' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="3" {{ request('trang_thai') == '3' ? 'selected' : '' }}>Quá hạn</option>
            </select>
            <select name="thang" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Tháng</option>
                @for($i=1;$i<=12;$i++)<option value="{{ $i }}" {{ request('thang') == $i ? 'selected' : '' }}>Tháng {{ $i }}</option>@endfor
            </select>
            <input type="number" name="nam" value="{{ request('nam', now()->year) }}" placeholder="Năm"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-24">
            <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Lọc</button>
        </form>
        <a href="{{ route('manager.hoa-don.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tạo hóa đơn
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Mã HĐ</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Căn hộ</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tháng/Năm</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tổng tiền</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Đã thanh toán</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($hoaDon as $hd)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 font-mono text-xs text-gray-600">{{ $hd->ma_thanh_toan }}</td>
                    <td class="px-5 py-4 font-medium text-gray-800">
                        {{ $hd->canHo?->so_can_ho }} <span class="text-xs text-gray-400">{{ $hd->canHo?->toaNha?->ten_toa_nha }}</span>
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $hd->thang }}/{{ $hd->nam }}</td>
                    <td class="px-5 py-4 text-right font-medium text-gray-800">{{ number_format($hd->tong_tien) }}đ</td>
                    <td class="px-5 py-4 text-right text-emerald-600">{{ number_format($hd->so_tien_da_thanh_toan) }}đ</td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $hd->trang_thai == 2 ? 'bg-green-100 text-green-700' : ($hd->trang_thai == 3 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $hd->trang_thai_label }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('manager.hoa-don.show', $hd) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Chi tiết</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">Không có hóa đơn nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($hoaDon->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">{{ $hoaDon->links() }}</div>
    @endif
</div>
@endsection
