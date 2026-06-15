@extends('layouts.manager')
@section('title', 'Chi tiết hóa đơn')
@section('page-title', 'Hóa đơn ' . $hoaDon->ma_thanh_toan)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Chi tiết -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Chi tiết hóa đơn</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $hoaDon->trang_thai == 2 ? 'bg-green-100 text-green-700' : ($hoaDon->trang_thai == 3 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ $hoaDon->trang_thai_label }}
                </span>
            </div>
            <dl class="grid grid-cols-2 gap-3 text-sm mb-4">
                <div><dt class="text-gray-500">Mã HĐ</dt><dd class="font-mono font-medium mt-0.5">{{ $hoaDon->ma_thanh_toan }}</dd></div>
                <div><dt class="text-gray-500">Căn hộ</dt><dd class="font-medium mt-0.5">{{ $hoaDon->canHo?->so_can_ho }}</dd></div>
                <div><dt class="text-gray-500">Tháng/Năm</dt><dd class="font-medium mt-0.5">{{ $hoaDon->thang }}/{{ $hoaDon->nam }}</dd></div>
                <div><dt class="text-gray-500">Hạn thanh toán</dt><dd class="font-medium mt-0.5">{{ $hoaDon->han_thanh_toan?->format('d/m/Y') ?? '-' }}</dd></div>
            </dl>

            <table class="w-full text-sm border-t border-gray-100 pt-4">
                <thead><tr class="text-xs text-gray-500 uppercase">
                    <th class="text-left py-2">Phí dịch vụ</th>
                    <th class="text-right py-2">Đơn giá</th>
                    <th class="text-right py-2">SL</th>
                    <th class="text-right py-2">Thành tiền</th>
                </tr></thead>
                <tbody>
                @foreach($hoaDon->chiTiet as $ct)
                <tr class="border-t border-gray-50">
                    <td class="py-2 text-gray-700">{{ $ct->phiDichVu?->ten_phi_dich_vu }}</td>
                    <td class="py-2 text-right text-gray-600">{{ number_format($ct->don_gia) }}đ</td>
                    <td class="py-2 text-right text-gray-600">{{ $ct->so_luong }}</td>
                    <td class="py-2 text-right font-medium text-gray-800">{{ number_format($ct->thanh_tien) }}đ</td>
                </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="border-t-2 border-gray-200">
                    <td colspan="3" class="py-3 font-semibold text-gray-800">Tổng cộng</td>
                    <td class="py-3 text-right font-bold text-indigo-600 text-base">{{ number_format($hoaDon->tong_tien) }}đ</td>
                </tr>
                <tr>
                    <td colspan="3" class="pb-2 text-sm text-gray-500">Đã thanh toán</td>
                    <td class="pb-2 text-right text-emerald-600 font-medium">{{ number_format($hoaDon->so_tien_da_thanh_toan) }}đ</td>
                </tr>
                <tr>
                    <td colspan="3" class="pb-2 text-sm font-semibold text-red-600">Còn lại</td>
                    <td class="pb-2 text-right font-bold text-red-600">{{ number_format($hoaDon->conNo()) }}đ</td>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Lịch sử thanh toán -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Lịch sử thanh toán</h3>
            @forelse($hoaDon->lichSuThanhToan as $ls)
            <div class="flex items-center justify-between py-2.5 border-b border-gray-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ number_format($ls->so_tien) }}đ</p>
                    <p class="text-xs text-gray-500">{{ $ls->phuong_thuc_thanh_toan }} · {{ $ls->ngay_thanh_toan?->format('d/m/Y H:i') }}</p>
                </div>
                <span class="text-xs text-gray-500 font-mono">{{ $ls->ma_giao_dich }}</span>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Chưa có thanh toán</p>
            @endforelse
        </div>
    </div>

    <!-- Ghi nhận TT -->
    <div>
        @if($hoaDon->trang_thai != 2 && $hoaDon->conNo() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Ghi nhận thanh toán</h3>
            <form method="POST" action="{{ route('manager.hoa-don.ghi-nhan-thanh-toan', $hoaDon) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tiền (đ) <span class="text-red-500">*</span></label>
                    <input type="number" name="so_tien" value="{{ $hoaDon->conNo() }}" required min="1000" step="1000"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phương thức</label>
                    <select name="phuong_thuc_thanh_toan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Tiền mặt">Tiền mặt</option>
                        <option value="Chuyển khoản">Chuyển khoản</option>
                        <option value="MoMo">MoMo</option>
                        <option value="VNPay">VNPay</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã giao dịch</label>
                    <input type="text" name="ma_giao_dich"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">
                    Xác nhận thanh toán
                </button>
            </form>
        </div>
        @endif
        <div class="mt-4">
            <a href="{{ route('manager.hoa-don.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm">← Quay lại danh sách</a>
        </div>
    </div>
</div>
@endsection
