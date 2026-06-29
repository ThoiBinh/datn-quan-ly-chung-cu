@extends('layouts.admin')
@section('title', 'Chi tiết hóa đơn')
@section('page-title', 'Hóa đơn ' . $hoaDon->ma_thanh_toan)

@section('content')
@php
$conNo    = max(0, (float)($hoaDon->tong_tien ?? 0) - (float)($hoaDon->so_tien_da_thanh_toan ?? 0));
$chuHo    = $hoaDon->canHo?->chuHo?->cuDan;
$isDaTT   = $hoaDon->trang_thai === \App\Models\HoaDon::TRANG_THAI_DA_THANH_TOAN;
$isCanTT  = in_array($hoaDon->trang_thai, [
    \App\Models\HoaDon::TRANG_THAI_CHUA_THANH_TOAN,
    \App\Models\HoaDon::TRANG_THAI_QUA_HAN,
]) && $conNo > 0;

$statusConfig = match($hoaDon->trang_thai) {
    1 => ['label' => 'Chưa thanh toán', 'dot' => 'bg-amber-500',   'text' => 'text-amber-600 dark:text-amber-400',   'bg' => 'bg-amber-50 dark:bg-amber-900/20'],
    2 => ['label' => 'Đã thanh toán',   'dot' => 'bg-emerald-500', 'text' => 'text-emerald-600 dark:text-emerald-400','bg' => 'bg-emerald-50 dark:bg-emerald-900/20'],
    3 => ['label' => 'Trễ hạn',         'dot' => 'bg-red-500',     'text' => 'text-red-600 dark:text-red-400',        'bg' => 'bg-red-50 dark:bg-red-900/20'],
    default => ['label'=>'—','dot'=>'bg-gray-400','text'=>'text-gray-500','bg'=>'bg-gray-50'],
};
@endphp

<div class="space-y-5" x-data="{
    chiTietToDelete: null,
    chiTietDeleteUrl: '',
    deletingChiTiet: false,
    tongTienHoaDon: {{ (float)($hoaDon->tong_tien ?? 0) }},
    daTTHoaDon: {{ (float)($hoaDon->so_tien_da_thanh_toan ?? 0) }},
    chiTietCount: {{ $hoaDon->chiTiet->count() }},

    showThanhToan: false,
    thanhToanSoTien: {{ $conNo }},
    phuongThucSelected: '{{ $phuongThuc->first()?->loai_phuong_thuc ?? '' }}',

    fmtMoney(n) { return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + 'đ'; },
    getConNo() { return Math.max(0, this.tongTienHoaDon - this.daTTHoaDon); },
    todayStr() { return new Date().toISOString().split('T')[0]; },

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

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm rounded-xl px-4 py-3">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Breadcrumb + actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
            <a href="{{ route('admin.hoa-don.index') }}" class="hover:text-indigo-600 transition-colors">Hóa đơn</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="font-mono font-semibold text-gray-700 dark:text-slate-200">{{ $hoaDon->ma_thanh_toan }}</span>
        </nav>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Nút thanh toán —— chỉ hiện status 1,2,3; ẩn status 4 (đã hủy) --}}
            @if(in_array($hoaDon->trang_thai, [1, 2, 3]))
                @if($isDaTT)
                <span title="Hóa đơn đã thanh toán đầy đủ.">
                    <button disabled class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium bg-indigo-50 dark:bg-indigo-900/20 text-indigo-400 border border-indigo-200 dark:border-indigo-800 opacity-60 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Thanh toán
                    </button>
                </span>
                @else
                <button @click="showThanhToan = true" type="button"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Thanh toán
                </button>
                @endif
            @endif

            <a href="{{ route('admin.hoa-don.edit', $hoaDon) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>

        </div>
    </div>

    {{-- Hero summary --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Mã hóa đơn</p>
                <p class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $hoaDon->ma_thanh_toan }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Kỳ</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-slate-200">Tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Tổng tiền</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums" x-text="fmtMoney(tongTienHoaDon)">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Trạng thái</p>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                    {{ $statusConfig['label'] }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Main column --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Chi tiết khoản thu --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-slate-200">Chi tiết khoản thu</h2>
                    <span class="ml-auto text-xs text-gray-400 dark:text-slate-500" x-text="chiTietCount + ' khoản'">{{ $hoaDon->chiTiet->count() }} khoản</span>
                </div>
                @if($hoaDon->chiTiet->isNotEmpty())
                <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Tên dịch vụ</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-slate-400">Đơn giá</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-slate-400">Số lượng</th>
                            <th class="px-5 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-slate-400">Thành tiền</th>
                            @if(!$isDaTT)<th class="w-10"></th>@endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                        @foreach($hoaDon->chiTiet as $ct)
                        <tr id="chi-tiet-row-{{ $ct->id }}" class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                            <td class="px-5 py-3 text-gray-700 dark:text-slate-300">{{ $ct->ten_phi_dich_vu }}</td>
                            <td class="px-4 py-3 text-right text-gray-500 dark:text-slate-400 tabular-nums">{{ number_format($ct->don_gia ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-500 dark:text-slate-400 tabular-nums">
                                @if($ct->chi_so_moi && $ct->chi_so_cu)
                                {{ $ct->chi_so_moi - $ct->chi_so_cu }}
                                @else
                                {{ $ct->so_luong ?: 1 }}
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800 dark:text-slate-200 tabular-nums">{{ number_format($ct->thanh_tien ?? 0, 0, ',', '.') }}đ</td>
                            @if(!$isDaTT)
                            <td class="px-3 py-3">
                                <button type="button"
                                        @click="chiTietToDelete = {{ $ct->id }}; chiTietDeleteUrl = '{{ route('admin.hoa-don.chi-tiet.destroy', [$hoaDon, $ct]) }}'"
                                        class="p-1.5 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <td colspan="{{ !$isDaTT ? 3 : 2 }}" class="px-5 py-3 text-sm font-semibold text-gray-700 dark:text-slate-300 text-right">Tổng cộng</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-900 dark:text-white tabular-nums" x-text="fmtMoney(tongTienHoaDon)">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</td>
                            @if(!$isDaTT)<td></td>@endif
                        </tr>
                    </tfoot>
                </table>
                </div>
                @else
                <div class="px-5 py-8 text-center text-sm text-gray-400 dark:text-slate-500">Chưa có chi tiết khoản thu</div>
                @endif
            </div>

            {{-- Tổng thanh toán --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Tổng tiền HĐ</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white tabular-nums" x-text="fmtMoney(tongTienHoaDon)">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-emerald-200 dark:border-emerald-800 shadow-sm p-4 text-center">
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mb-1">Đã thanh toán</p>
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 tabular-nums" x-text="fmtMoney(daTTHoaDon)">{{ number_format($hoaDon->so_tien_da_thanh_toan ?? 0, 0, ',', '.') }}đ</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-4 text-center border"
                     :class="getConNo() > 0 ? 'border-red-200 dark:border-red-800' : 'border-emerald-200 dark:border-emerald-800'">
                    <p class="text-xs mb-1" :class="getConNo() > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'">Còn nợ</p>
                    <p class="text-lg font-bold tabular-nums"
                       :class="getConNo() > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'"
                       x-text="getConNo() > 0 ? fmtMoney(getConNo()) : 'Đã đủ'">
                        {{ $conNo > 0 ? number_format($conNo, 0, ',', '.') . 'đ' : 'Đã đủ' }}
                    </p>
                </div>
            </div>

            {{-- Lịch sử thanh toán (READ-ONLY) --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-slate-200">Lịch sử thanh toán</h2>
                    <span class="ml-auto text-xs text-gray-400 dark:text-slate-500">{{ $hoaDon->lichSuThanhToan->count() }} giao dịch</span>
                    <a href="{{ route('admin.thanh-toan.index', ['hoa_don_id' => $hoaDon->id]) }}"
                       class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Xem tất cả</a>
                </div>
                @if($hoaDon->lichSuThanhToan->isNotEmpty())
                <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-slate-400 w-10">STT</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Ngày TT</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-slate-400">Số tiền</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Phương thức</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Nguồn</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Ghi chú</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-slate-400 w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                        @foreach($hoaDon->lichSuThanhToan as $idx => $ls)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                            <td class="px-4 py-3 text-gray-400 dark:text-slate-500 text-xs">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-slate-300 whitespace-nowrap">{{ $ls->ngay_thanh_toan?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums whitespace-nowrap">{{ number_format($ls->so_tien ?? 0, 0, ',', '.') }}đ</td>
                            <td class="px-4 py-3">
                                @if($ls->phuong_thuc_thanh_toan)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400">{{ $ls->phuong_thuc_thanh_toan }}</span>
                                @else<span class="text-gray-400">—</span>@endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-slate-400">
                                {{ $ls->nguoiThanhToan?->ho_ten ?? $ls->nguonTao?->ten_nguon_tao ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-slate-400 max-w-[120px] truncate" title="{{ $ls->ghi_chu }}">{{ $ls->ghi_chu ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.thanh-toan.show', $ls) }}"
                                   class="p-1.5 rounded text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors inline-flex"
                                   title="Xem chi tiết">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-xs font-semibold text-gray-700 dark:text-slate-300 text-right">Tổng đã thanh toán</td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-600 dark:text-emerald-400 tabular-nums" x-text="fmtMoney(daTTHoaDon)">{{ number_format($hoaDon->so_tien_da_thanh_toan ?? 0, 0, ',', '.') }}đ</td>
                        </tr>
                    </tfoot>
                </table>
                </div>
                @else
                <div class="px-5 py-8 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <p class="text-sm text-gray-400 dark:text-slate-500">Chưa có giao dịch thanh toán nào</p>
                    @if($isCanTT)
                    <button @click="showThanhToan = true" type="button"
                            class="mt-3 inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ghi nhận thanh toán đầu tiên
                    </button>
                    @endif
                </div>
                @endif
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Căn hộ --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-slate-200">Căn hộ</h2>
                </div>
                @if($hoaDon->canHo)
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Số căn hộ</dt>
                        <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200">{{ $hoaDon->canHo->so_can_ho }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Tòa nhà</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-300">{{ $hoaDon->canHo->toaNha?->ten_toa_nha ?? '—' }}</dd>
                    </div>
                    @if($hoaDon->han_thanh_toan)
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Hạn TT</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-300">{{ $hoaDon->han_thanh_toan->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                </dl>
                @endif
            </div>

            {{-- Chủ hộ --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-slate-200">Chủ hộ</h2>
                </div>
                @if($chuHo)
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ strtoupper(substr($chuHo->ho_ten ?? '?', 0, 1)) }}</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-slate-200">{{ $chuHo->ho_ten }}</p>
                    </div>
                    @if($chuHo->sdt)<p class="text-xs text-gray-500 dark:text-slate-400 mt-1">SĐT: {{ $chuHo->sdt }}</p>@endif
                    @if($chuHo->email)<p class="text-xs text-gray-500 dark:text-slate-400 mt-1 break-all">Email: {{ $chuHo->email }}</p>@endif
                </div>
                @else
                <div class="px-5 py-4 text-sm text-gray-400 dark:text-slate-500">Chưa có thông tin chủ hộ</div>
                @endif
            </div>

            {{-- Người cập nhật --}}
            @if($hoaDon->nguoiCapNhat)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-slate-200">Người cập nhật</h2>
                </div>
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Họ tên</dt>
                        <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200">{{ $hoaDon->nguoiCapNhat->ho_ten }}</dd>
                    </div>
                    @if($hoaDon->nguoiCapNhat->chucVu)
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Chức vụ</dt>
                        <dd class="text-sm text-gray-600 dark:text-slate-300">{{ $hoaDon->nguoiCapNhat->chucVu->chuc_vu }}</dd>
                    </div>
                    @endif
                    @if($hoaDon->updatedAt)
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Cập nhật</dt>
                        <dd class="text-xs text-gray-500 dark:text-slate-400">{{ $hoaDon->updatedAt->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

        </div>
    </div>

    {{-- ===================== MODALS ===================== --}}

    {{-- Modal: Ghi nhận thanh toán --}}
    @if($isCanTT)
    <template x-teleport="body">
    <div x-show="showThanhToan" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-150" x-transition:leave-end="opacity-0"
         @click.self="showThanhToan = false">
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
             x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             @click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Ghi nhận thanh toán</h3>
                </div>
                <button @click="showThanhToan = false" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Invoice summary --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700/50 border-b border-gray-100 dark:border-slate-700">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-0.5">Mã hóa đơn</p>
                        <p class="font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $hoaDon->ma_thanh_toan }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-0.5">Căn hộ</p>
                        <p class="font-semibold text-gray-800 dark:text-slate-200">{{ $hoaDon->canHo?->so_can_ho ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-0.5">Chủ hộ</p>
                        <p class="font-semibold text-gray-800 dark:text-slate-200">{{ $chuHo?->ho_ten ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-0.5">Tổng tiền HĐ</p>
                        <p class="font-semibold text-gray-900 dark:text-white tabular-nums" x-text="fmtMoney(tongTienHoaDon)">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-slate-600 grid grid-cols-2 gap-3">
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg px-3 py-2">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 mb-0.5">Đã thanh toán</p>
                        <p class="font-bold text-emerald-700 dark:text-emerald-300 tabular-nums" x-text="fmtMoney(daTTHoaDon)">{{ number_format($hoaDon->so_tien_da_thanh_toan ?? 0, 0, ',', '.') }}đ</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg px-3 py-2">
                        <p class="text-xs text-red-600 dark:text-red-400 mb-0.5">Còn nợ</p>
                        <p class="font-bold text-red-700 dark:text-red-300 tabular-nums" x-text="fmtMoney(getConNo())">{{ number_format($conNo, 0, ',', '.') }}đ</p>
                    </div>
                </div>
            </div>

            {{-- Payment form --}}
            <form method="POST" action="{{ route('admin.thanh-toan.store', $hoaDon) }}" class="px-6 py-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Số tiền (đ) <span class="text-red-500">*</span></label>
                        <input type="number" name="so_tien"
                               x-model.number="thanhToanSoTien"
                               :max="getConNo()"
                               min="1" step="1" required
                               class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 tabular-nums">
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Tối đa: <span class="font-medium text-red-600 dark:text-red-400" x-text="fmtMoney(getConNo())">{{ number_format($conNo, 0, ',', '.') }}đ</span></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày thanh toán <span class="text-red-500">*</span></label>
                        <input type="date" name="ngay_thanh_toan"
                               :max="todayStr()"
                               value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Phương thức thanh toán <span class="text-red-500">*</span></label>
                    @if($phuongThuc->isNotEmpty())
                    <select name="phuong_thuc_thanh_toan" required x-model="phuongThucSelected"
                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach($phuongThuc as $pt)
                        <option value="{{ $pt->loai_phuong_thuc }}">{{ $pt->loai_phuong_thuc }}</option>
                        @endforeach
                    </select>
                    @else
                    <input type="text" name="phuong_thuc_thanh_toan" required maxlength="100"
                           x-model="phuongThucSelected"
                           placeholder="VD: Tiền mặt, Chuyển khoản..."
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @endif
                </div>
                <div x-show="phuongThucSelected === 'Tiền mặt'" x-cloak>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Người thanh toán</label>
                    <select name="nguoi_thanh_toan"
                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">— Chọn người thanh toán —</option>
                        @foreach($hoaDon->canHo?->cuDanHienTai ?? [] as $rel)
                            @if($rel->cuDan)
                            <option value="{{ $rel->cuDan->id }}"
                                {{ $rel->cuDan->id === ($chuHo?->id) ? 'selected' : '' }}>
                                {{ $rel->cuDan->ho_ten }}{{ $rel->cuDan->id === ($chuHo?->id) ? ' (Chủ hộ)' : '' }}
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ghi chú</label>
                    <textarea name="ghi_chu" rows="2" maxlength="500"
                              placeholder="Ghi chú thanh toán (tùy chọn)"
                              class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
                </div>
                <div class="flex items-center gap-3 pt-1">
                    <button type="button" @click="showThanhToan = false"
                            class="flex-1 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        Xác nhận thanh toán
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>
    @endif

    {{-- Modal: Xóa chi tiết khoản thu --}}
    @if(!$isDaTT)
    <template x-teleport="body">
    <div x-show="chiTietToDelete !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0">
        <div class="absolute inset-0 bg-black/50" @click="chiTietToDelete = null; chiTietDeleteUrl = ''"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6"
             x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-slate-200">Xóa khoản phí này?</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Dịch vụ sẽ bị loại khỏi hóa đơn và tổng tiền sẽ được cập nhật.</p>
                </div>
                <div class="flex items-center gap-3 w-full">
                    <button type="button" @click="chiTietToDelete = null; chiTietDeleteUrl = ''"
                            class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-300 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
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


</div>
@endsection
