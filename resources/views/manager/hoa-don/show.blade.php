@extends('layouts.manager')
@section('title', 'Chi tiết hóa đơn')
@section('page-title', 'Hóa đơn ' . $hoaDon->ma_thanh_toan)

@section('content')
@php
$conNo = max(0, ($hoaDon->tong_tien ?? 0) - ($hoaDon->so_tien_da_thanh_toan ?? 0));
$isCanHuy = $hoaDon->trang_thai != \App\Models\HoaDon::TRANG_THAI_DA_HUY;
$statusConfig = match($hoaDon->trang_thai) {
    1 => ['label' => 'Chưa thanh toán', 'dot' => 'bg-amber-500', 'text' => 'text-amber-600', 'bg' => 'bg-amber-50'],
    2 => ['label' => 'Đã thanh toán',   'dot' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
    3 => ['label' => 'Quá hạn',         'dot' => 'bg-red-500',     'text' => 'text-red-600',     'bg' => 'bg-red-50'],
    4 => ['label' => 'Đã hủy',          'dot' => 'bg-gray-400',    'text' => 'text-gray-500',    'bg' => 'bg-gray-50'],
    default => ['label' => '—', 'dot' => 'bg-gray-400', 'text' => 'text-gray-500', 'bg' => 'bg-gray-50'],
};
$chuHo = $hoaDon->canHo?->chuHo?->cuDan;
@endphp

<div class="space-y-5" x-data="{
    confirmToggle: false,
    chiTietToDelete: null,
    chiTietDeleteUrl: '',
    deletingChiTiet: false,
    tongTienHoaDon: {{ (int)($hoaDon->tong_tien ?? 0) }},
    daTTHoaDon: {{ (int)($hoaDon->so_tien_da_thanh_toan ?? 0) }},
    chiTietCount: {{ $hoaDon->chiTiet->count() }},
    fmtMoney(n) { return new Intl.NumberFormat('vi-VN').format(n) + 'đ'; },
    getConNo() { return Math.max(0, this.tongTienHoaDon - this.daTTHoaDon); },
    async xoaChiTiet() {
        if (!this.chiTietToDelete || !this.chiTietDeleteUrl) return;
        this.deletingChiTiet = true;
        try {
            const r = await fetch(this.chiTietDeleteUrl, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await r.json();
            if (data.success) {
                document.getElementById('chi-tiet-row-' + this.chiTietToDelete).remove();
                this.tongTienHoaDon = data.tong_tien;
                this.chiTietCount--;
            } else {
                alert(data.error || 'Có lỗi xảy ra khi xóa.');
            }
        } catch(e) { alert('Có lỗi xảy ra khi xóa.'); }
        this.deletingChiTiet = false;
        this.chiTietToDelete = null;
        this.chiTietDeleteUrl = '';
    }
}">

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    {{-- Breadcrumb + actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <nav class="flex items-center gap-1.5 text-sm text-gray-500">
            <a href="{{ route('manager.hoa-don.index') }}" class="hover:text-indigo-600 transition-colors">Hóa đơn</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="font-mono font-semibold text-gray-700">{{ $hoaDon->ma_thanh_toan }}</span>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('manager.hoa-don.edit', $hoaDon) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            @if(in_array($hoaDon->trang_thai, [1, 3, 4]))
            <button @click="confirmToggle = true" type="button"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ $isCanHuy ? 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}">
                @if($isCanHuy)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                Hủy hóa đơn
                @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Khôi phục
                @endif
            </button>
            @endif
        </div>
    </div>

    {{-- Hero card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-400 mb-1">Mã hóa đơn</p>
                <p class="font-mono font-bold text-indigo-600">{{ $hoaDon->ma_thanh_toan }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Kỳ</p>
                <p class="text-sm font-semibold text-gray-800">Tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Tổng tiền</p>
                <p class="text-sm font-bold text-gray-900">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Trạng thái</p>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                    {{ $statusConfig['label'] }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left: chi tiết + thanh toán --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Chi tiết khoản thu --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800">Chi tiết khoản thu</h2>
                    <span class="ml-auto text-xs text-gray-400" x-text="chiTietCount + ' khoản'">{{ $hoaDon->chiTiet->count() }} khoản</span>
                </div>
                @if($hoaDon->chiTiet->isNotEmpty())
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500">Tên dịch vụ</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500">Đơn giá</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500">Số lượng / Chỉ số</th>
                            <th class="px-5 py-2.5 text-right text-xs font-medium text-gray-500">Thành tiền</th>
                            @if($hoaDon->trang_thai !== \App\Models\HoaDon::TRANG_THAI_DA_THANH_TOAN)
                            <th class="px-3 py-2.5 text-xs font-medium text-gray-500 w-12"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($hoaDon->chiTiet as $ct)
                        <tr id="chi-tiet-row-{{ $ct->id }}" class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-gray-700">{{ $ct->ten_phi_dich_vu }}</td>
                            <td class="px-4 py-3 text-right text-gray-500 tabular-nums">{{ number_format($ct->don_gia ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-500 tabular-nums">
                                @if($ct->chi_so_moi && $ct->chi_so_cu)
                                <span title="Chỉ số {{ $ct->chi_so_cu }} → {{ $ct->chi_so_moi }}">
                                    {{ $ct->chi_so_moi - $ct->chi_so_cu }}
                                    <span class="text-gray-300 text-xs">({{ $ct->chi_so_cu }}→{{ $ct->chi_so_moi }})</span>
                                </span>
                                @else
                                {{ $ct->so_luong ?: 1 }}
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800 tabular-nums">{{ number_format($ct->thanh_tien ?? 0, 0, ',', '.') }}đ</td>
                            @if($hoaDon->trang_thai !== \App\Models\HoaDon::TRANG_THAI_DA_THANH_TOAN)
                            <td class="px-3 py-3">
                                <button type="button"
                                        @click="chiTietToDelete = {{ $ct->id }}; chiTietDeleteUrl = '{{ route('manager.hoa-don.chi-tiet.destroy', [$hoaDon, $ct]) }}'"
                                        title="Xóa khoản này"
                                        class="p-1.5 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-gray-200 bg-gray-50">
                        <tr>
                            <td colspan="{{ $hoaDon->trang_thai !== \App\Models\HoaDon::TRANG_THAI_DA_THANH_TOAN ? 4 : 3 }}" class="px-5 py-3 text-sm font-semibold text-gray-700 text-right">Tổng cộng</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-900 tabular-nums" x-text="fmtMoney(tongTienHoaDon)">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</td>
                        </tr>
                        @if($hoaDon->chi_phi)
                        <tr>
                            <td colspan="3" class="px-5 py-2 text-xs text-gray-500 text-right">Chi phí khác</td>
                            <td class="px-5 py-2 text-right text-xs text-gray-600 tabular-nums">{{ number_format($hoaDon->chi_phi, 0, ',', '.') }}đ</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
                @else
                <div class="px-5 py-8 text-center text-sm text-gray-400">Chưa có chi tiết khoản thu</div>
                @endif
            </div>

            {{-- Thanh toán summary --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-400 mb-1">Tổng tiền</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums"
                       x-text="fmtMoney(tongTienHoaDon)">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-400 mb-1">Đã thanh toán</p>
                    <p class="text-lg font-bold text-emerald-600 tabular-nums">{{ number_format($hoaDon->so_tien_da_thanh_toan ?? 0, 0, ',', '.') }}đ</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-400 mb-1">Còn nợ</p>
                    <p class="text-lg font-bold tabular-nums"
                       :class="getConNo() > 0 ? 'text-red-600' : 'text-emerald-600'"
                       x-text="getConNo() > 0 ? fmtMoney(getConNo()) : 'Đã đủ'">
                        {{ $conNo > 0 ? number_format($conNo, 0, ',', '.') . 'đ' : 'Đã đủ' }}
                    </p>
                </div>
            </div>

            {{-- Lịch sử thanh toán --}}
            @if($hoaDon->lichSuThanhToan->isNotEmpty())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800">Lịch sử thanh toán</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($hoaDon->lichSuThanhToan as $ls)
                    <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ number_format($ls->so_tien ?? 0, 0, ',', '.') }}đ</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <p class="text-xs text-gray-400">{{ $ls->ngay_thanh_toan?->format('d/m/Y H:i') ?? '—' }}</p>
                                @if($ls->phuong_thuc_thanh_toan)
                                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">{{ $ls->phuong_thuc_thanh_toan }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            @if($ls->nguoiThanhToan)
                            <p class="text-xs text-gray-500">{{ $ls->nguoiThanhToan->ho_ten }}</p>
                            @endif
                            @if($ls->ma_giao_dich)
                            <p class="text-xs font-mono text-gray-400">{{ $ls->ma_giao_dich }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-5">

            {{-- Ghi nhận thanh toán --}}
            @if($hoaDon->trang_thai != \App\Models\HoaDon::TRANG_THAI_DA_THANH_TOAN && $conNo > 0)
            <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-indigo-700 mb-4">Ghi nhận thanh toán</h3>
                <form method="POST" action="{{ route('manager.hoa-don.ghi-nhan-thanh-toan', $hoaDon) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Số tiền (đ) <span class="text-red-500">*</span></label>
                        <input type="number" name="so_tien" value="{{ $conNo }}" required min="1000" step="1000"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Phương thức</label>
                        <select name="phuong_thuc_thanh_toan" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="Tiền mặt">Tiền mặt</option>
                            <option value="Chuyển khoản">Chuyển khoản</option>
                            <option value="MoMo">MoMo</option>
                            <option value="VNPay">VNPay</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Mã giao dịch</label>
                        <input type="text" name="ma_giao_dich"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        Xác nhận thanh toán
                    </button>
                </form>
            </div>
            @endif

            {{-- Thông tin căn hộ --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800">Căn hộ</h2>
                </div>
                @if($hoaDon->canHo)
                <dl class="divide-y divide-gray-50">
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 flex-shrink-0">Số căn hộ</dt>
                        <dd class="text-sm font-semibold text-gray-700">{{ $hoaDon->canHo->so_can_ho }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 flex-shrink-0">Tòa nhà</dt>
                        <dd class="text-sm text-gray-700">{{ $hoaDon->canHo->toaNha?->ten_toa_nha ?? '—' }}</dd>
                    </div>
                    @if($hoaDon->han_thanh_toan)
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 flex-shrink-0">Hạn TT</dt>
                        <dd class="text-sm text-gray-700">{{ $hoaDon->han_thanh_toan->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                    @if($hoaDon->createdAt)
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 flex-shrink-0">Ngày tạo</dt>
                        <dd class="text-xs text-gray-500">{{ $hoaDon->createdAt->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                </dl>
                @else
                <div class="px-5 py-4 text-sm text-gray-400">—</div>
                @endif
            </div>

            {{-- Chủ hộ --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800">Chủ hộ</h2>
                </div>
                @if($chuHo)
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-base font-bold text-emerald-600">{{ strtoupper(substr($chuHo->ho_ten ?? '?', 0, 1)) }}</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">{{ $chuHo->ho_ten }}</p>
                    </div>
                    @if($chuHo->sdt)
                    <p class="text-xs text-gray-500 mt-1">SĐT: {{ $chuHo->sdt }}</p>
                    @endif
                    @if($chuHo->email)
                    <p class="text-xs text-gray-500 mt-1 break-all">Email: {{ $chuHo->email }}</p>
                    @endif
                </div>
                @else
                <div class="px-5 py-4 text-sm text-gray-400">Chưa có thông tin chủ hộ</div>
                @endif
            </div>

            {{-- Người cập nhật --}}
            @if($hoaDon->nguoiCapNhat)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800">Người cập nhật</h2>
                </div>
                <dl class="divide-y divide-gray-50">
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 flex-shrink-0">Họ tên</dt>
                        <dd class="text-sm font-semibold text-gray-700">{{ $hoaDon->nguoiCapNhat->ho_ten }}</dd>
                    </div>
                    @if($hoaDon->nguoiCapNhat->chucVu)
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 flex-shrink-0">Chức vụ</dt>
                        <dd class="text-sm text-gray-600">{{ $hoaDon->nguoiCapNhat->chucVu->chuc_vu }}</dd>
                    </div>
                    @endif
                    @if($hoaDon->updatedAt)
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 flex-shrink-0">Cập nhật</dt>
                        <dd class="text-xs text-gray-500">{{ $hoaDon->updatedAt->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

        </div>
    </div>

    {{-- Modal xác nhận xóa chi tiết --}}
    @if($hoaDon->trang_thai !== \App\Models\HoaDon::TRANG_THAI_DA_THANH_TOAN)
    <template x-teleport="body">
        <div x-show="chiTietToDelete !== null" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="absolute inset-0 bg-black/50" @click="chiTietToDelete = null; chiTietDeleteUrl = ''"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Xóa khoản phí này?</h3>
                        <p class="text-sm text-gray-500 mt-1">Nếu xóa, dịch vụ sẽ bị loại khỏi hóa đơn và tổng tiền sẽ được cập nhật.</p>
                    </div>
                    <div class="flex items-center gap-3 w-full">
                        <button type="button" @click="chiTietToDelete = null; chiTietDeleteUrl = ''"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Giữ lại
                        </button>
                        <button type="button" @click="xoaChiTiet()" :disabled="deletingChiTiet"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors disabled:opacity-50">
                            <span x-show="!deletingChiTiet">Xóa</span>
                            <span x-show="deletingChiTiet" class="flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Đang xóa...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    @endif

    {{-- Modal toggle --}}
    @if(in_array($hoaDon->trang_thai, [1, 3, 4]))
    <template x-teleport="body">
    <div x-show="confirmToggle"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="confirmToggle = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden" @click.stop>
            <div class="flex justify-end px-4 pt-4">
                <button @click="confirmToggle = false" class="w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 pt-2 pb-5 text-center">
                @if($isCanHuy)
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900">Hủy hóa đơn</h3>
                <p class="text-sm text-gray-500 mt-1">Hóa đơn sẽ được chuyển sang trạng thái đã hủy.</p>
                @else
                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900">Khôi phục hóa đơn</h3>
                <p class="text-sm text-gray-500 mt-1">Hóa đơn sẽ được khôi phục về trạng thái chưa thanh toán.</p>
                @endif
            </div>
            <div class="mx-6 mb-5 flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Mã hóa đơn</p>
                    <p class="text-sm font-semibold text-gray-800 font-mono">{{ $hoaDon->ma_thanh_toan }}</p>
                </div>
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button @click="confirmToggle = false" class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">Hủy bỏ</button>
                <form action="{{ route('manager.hoa-don.toggle-status', $hoaDon) }}" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    @if($isCanHuy)
                    <button type="submit" class="block w-full py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">Hủy hóa đơn</button>
                    @else
                    <button type="submit" class="block w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">Khôi phục</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
    </template>
    @endif

</div>
@endsection
