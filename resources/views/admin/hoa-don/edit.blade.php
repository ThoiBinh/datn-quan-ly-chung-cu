@extends('layouts.admin')
@section('title', 'Chỉnh sửa hóa đơn')
@section('page-title', 'Chỉnh sửa hóa đơn')

@section('content')
<div class="max-w-3xl mx-auto space-y-5"
     x-data="{
        dsPhiMoi: @js($dsPhiDichVuMoi),
        checkedMoi: [],
        existingRows: @js($existingRows),
        removedIds: [],
        get selectedMoi() {
            return this.dsPhiMoi.filter(s => this.checkedMoi.includes(String(s.phi_dich_vu_id)));
        },
        get visibleRows() {
            return this.existingRows.filter(r => !this.removedIds.includes(r.chi_tiet_id));
        },
        soLuongMoi(svc) {
            if (svc.billing_type === 'meter') {
                const cu = parseInt(svc.chi_so_cu) || 0;
                const moi = parseInt(svc.chi_so_moi) || 0;
                return Math.max(0, moi - cu);
            }
            return parseFloat(svc.so_luong) || 0;
        },
        thanhTienMoi(svc) { return this.soLuongMoi(svc) * parseFloat(svc.don_gia || 0); },
        tieuThuRow(row) {
            const cu = parseInt(row.chi_so_cu) || 0;
            const moi = parseInt(row.chi_so_moi) || 0;
            return Math.max(0, moi - cu);
        },
        thanhTienRow(row) {
            const donGia = parseFloat(row.don_gia) || 0;
            return row.is_meter ? (this.tieuThuRow(row) * donGia) : ((parseFloat(row.so_luong) || 0) * donGia);
        },
        rowSoLuongHopLe(row) { return row.is_meter || row.billing_type === 'area' || parseFloat(row.so_luong) > 0; },
        xoaDong(row) {
            if (!confirm('Xóa dịch vụ \'' + row.ten_phi_dich_vu + '\' khỏi hóa đơn?')) return;
            this.removedIds.push(row.chi_tiet_id);
        },
        get existingTotal() { return this.visibleRows.reduce((sum, r) => sum + this.thanhTienRow(r), 0); },
        get tongThem() { return this.selectedMoi.reduce((sum, s) => sum + this.thanhTienMoi(s), 0); },
        get grandTotal() { return this.existingTotal + this.tongThem; },
        fmtTien(n) { return Math.round(n || 0).toLocaleString('vi-VN') + 'đ'; },
        soLuongHopLe(svc) { return svc.billing_type === 'meter' || svc.billing_type === 'area' || parseFloat(svc.so_luong) > 0; },
        coSoLuongKhongHopLe() {
            return this.selectedMoi.some(s => !this.soLuongHopLe(s)) || this.visibleRows.some(r => !this.rowSoLuongHopLe(r));
        },
        chanNopNeuLoi(e) {
            if (this.coSoLuongKhongHopLe()) {
                e.preventDefault();
                alert('Số lượng dịch vụ phải lớn hơn 0.');
            }
        }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.hoa-don.index') }}" class="hover:text-violet-600 transition-colors">Hóa đơn</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.hoa-don.show', $hoaDon) }}" class="hover:text-violet-600 transition-colors font-mono">{{ $hoaDon->ma_thanh_toan }}</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Chỉnh sửa</span>
    </nav>

    @if($isDaTT)
    <!-- Warning banner: DA_THANH_TOAN -->
    <div class="flex items-center gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
        <svg class="w-5 h-5 text-amber-500 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">Hóa đơn đã thanh toán</p>
            <p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">Không thể chỉnh sửa hoặc xóa hóa đơn đã thanh toán. Chỉ xem thông tin.</p>
        </div>
    </div>
    @endif

    <!-- Summary -->
    <div class="bg-violet-50 dark:bg-violet-900/20 rounded-xl border border-violet-200 dark:border-violet-800 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-violet-600 dark:text-violet-400 font-medium">{{ $hoaDon->ma_thanh_toan }}</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-slate-100 mt-0.5">
                    Tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }}
                    — {{ $hoaDon->canHo?->so_can_ho ?? '—' }}
                    ({{ $hoaDon->canHo?->toaNha?->ten_toa_nha ?? '?' }})
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 dark:text-slate-500">Tổng tiền</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white tabular-nums" x-text="fmtTien(grandTotal)">
                    {{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ
                </p>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-sm text-red-600 dark:text-red-400">{{ session('error') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <ul class="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form id="update-form" action="{{ route('admin.hoa-don.update', $hoaDon) }}" method="POST" class="space-y-5" @submit="chanNopNeuLoi($event)">
        @csrf @method('PUT')

        <!-- Status & deadline -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden {{ $isDaTT ? 'opacity-70' : '' }}">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Hạn thanh toán</h2>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Hạn thanh toán</label>
                    <input type="date" name="han_thanh_toan"
                           value="{{ old('han_thanh_toan', $hoaDon->han_thanh_toan ? $hoaDon->han_thanh_toan->format('Y-m-d') : '') }}"
                           {{ $isDaTT ? 'disabled' : '' }}
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400 {{ $isDaTT ? 'cursor-not-allowed' : '' }}">
                    <p class="mt-1.5 text-xs text-gray-400 dark:text-slate-500">Trạng thái hóa đơn được tính tự động dựa trên hạn này và số tiền đã thanh toán.</p>
                </div>
            </div>
        </div>

        @if(!$isDaTT)
        <!-- Quick-add fee services via checkbox -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thêm phí dịch vụ</h2>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">Tick chọn dịch vụ chưa có trong hóa đơn để thêm nhanh</p>
                </div>
            </div>

            <div class="p-5 space-y-4">
                <template x-if="dsPhiMoi.length === 0">
                    <p class="text-sm text-gray-400 dark:text-slate-500">Không còn dịch vụ nào khác để thêm.</p>
                </template>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <template x-for="svc in dsPhiMoi" :key="svc.phi_dich_vu_id">
                        <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-colors border-gray-200 dark:border-slate-600 hover:border-emerald-300 dark:hover:border-emerald-700">
                            <input type="checkbox" name="phi_dich_vu_ids[]" :value="String(svc.phi_dich_vu_id)" x-model="checkedMoi"
                                   class="w-4 h-4 rounded border-gray-300 dark:border-slate-500 text-emerald-600 focus:ring-emerald-400">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="svc.ten_phi_dich_vu"></p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white" x-text="svc.don_gia_fmt"></span>
                                <p class="text-xs text-gray-400 dark:text-slate-500" x-text="svc.don_vi_tinh ? ('/' + svc.don_vi_tinh) : ''"></p>
                            </div>
                        </label>
                    </template>
                </div>

                <template x-if="selectedMoi.length > 0">
                    <div class="border border-emerald-200 dark:border-emerald-800 rounded-lg overflow-hidden divide-y divide-emerald-100 dark:divide-emerald-800">
                        <template x-for="svc in selectedMoi" :key="'row-' + svc.phi_dich_vu_id">
                            <div class="px-4 py-3 bg-emerald-50/40 dark:bg-emerald-900/10">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-800 dark:text-white" x-text="svc.ten_phi_dich_vu"></span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums" x-text="fmtTien(thanhTienMoi(svc))"></span>
                                </div>
                                <template x-if="svc.billing_type === 'meter'">
                                    <div class="grid grid-cols-3 gap-3 mt-2">
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Chỉ số cũ</label>
                                            <input type="number" min="0" x-model.number="svc.chi_so_cu"
                                                   :name="'chi_so_moi_dich_vu[' + svc.phi_dich_vu_id + '][chi_so_cu]'"
                                                   class="w-full px-2 py-1.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white tabular-nums">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Chỉ số mới</label>
                                            <input type="number" min="0" x-model.number="svc.chi_so_moi"
                                                   :name="'chi_so_moi_dich_vu[' + svc.phi_dich_vu_id + '][chi_so_moi]'"
                                                   class="w-full px-2 py-1.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white tabular-nums">
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Tiêu thụ</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white tabular-nums" x-text="soLuongMoi(svc)"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="svc.billing_type === 'area'">
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">Diện tích: <span x-text="svc.so_luong"></span> m² (lấy từ thuộc tính căn hộ, không thể sửa)</p>
                                </template>
                                <template x-if="svc.billing_type !== 'meter' && svc.billing_type !== 'area'">
                                    <div class="mt-2 max-w-[140px]">
                                        <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Số lượng</label>
                                        <input type="number" min="0" step="any" x-model.number="svc.so_luong"
                                               :name="'so_luong_dich_vu[' + svc.phi_dich_vu_id + ']'"
                                               :class="soLuongHopLe(svc) ? 'border-gray-300 dark:border-slate-600' : 'border-red-400 dark:border-red-600'"
                                               class="w-full px-2 py-1.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white tabular-nums">
                                        <template x-if="!soLuongHopLe(svc)">
                                            <p class="text-xs text-red-500 dark:text-red-400 mt-1">Số lượng phải lớn hơn 0</p>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <div class="px-4 py-2.5 flex items-center justify-between bg-emerald-100/50 dark:bg-emerald-900/20">
                            <span class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Cộng thêm vào hóa đơn</span>
                            <span class="text-sm font-bold text-emerald-800 dark:text-emerald-300 tabular-nums" x-text="fmtTien(tongThem)"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        @endif

        @if($hoaDon->trang_thai == \App\Models\HoaDon::TRANG_THAI_CHUA_THANH_TOAN)
        <!-- Unified editable chi tiết hóa đơn (edit qty/đơn giá/chỉ số, xóa dòng) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-slate-300">Chi tiết hóa đơn</h2>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">Chỉnh sửa số lượng, đơn giá, chỉ số hoặc xóa dịch vụ khỏi hóa đơn</p>
            </div>

            <template x-if="visibleRows.length === 0">
                <p class="text-sm text-gray-400 dark:text-slate-500 px-5 py-4">Hóa đơn chưa có dịch vụ nào.</p>
            </template>

            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                <template x-for="row in visibleRows" :key="row.chi_tiet_id">
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <span class="text-sm font-medium text-gray-800 dark:text-white" x-text="row.ten_phi_dich_vu"></span>
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">Đơn giá: <span x-text="fmtTien(row.don_gia)"></span></p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums" x-text="fmtTien(thanhTienRow(row))"></span>
                                <button type="button" @click="xoaDong(row)" title="Xóa dịch vụ"
                                        class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        <template x-if="row.is_meter">
                            <div class="grid grid-cols-3 gap-3 mt-2 max-w-sm">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Chỉ số cũ</label>
                                    <input type="number" min="0" x-model.number="row.chi_so_cu"
                                           :name="'chi_tiet[' + row.chi_tiet_id + '][chi_so_cu]'"
                                           class="w-full px-2 py-1.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white tabular-nums">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Chỉ số mới</label>
                                    <input type="number" min="0" x-model.number="row.chi_so_moi"
                                           :name="'chi_tiet[' + row.chi_tiet_id + '][chi_so_moi]'"
                                           class="w-full px-2 py-1.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white tabular-nums">
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Tiêu thụ</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white tabular-nums" x-text="tieuThuRow(row)"></p>
                                </div>
                            </div>
                        </template>

                        <template x-if="!row.is_meter && row.billing_type === 'area'">
                            <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">Diện tích: <span x-text="row.so_luong"></span> m² (lấy từ thuộc tính căn hộ, không thể sửa)</p>
                        </template>

                        <template x-if="!row.is_meter && row.billing_type !== 'area'">
                            <div class="mt-2 max-w-[160px]">
                                <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Số lượng</label>
                                <input type="number" min="0" step="any" x-model.number="row.so_luong"
                                       :name="'chi_tiet[' + row.chi_tiet_id + '][so_luong]'"
                                       :class="rowSoLuongHopLe(row) ? 'border-gray-300 dark:border-slate-600' : 'border-red-400 dark:border-red-600'"
                                       class="w-full px-2 py-1.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white tabular-nums">
                                <template x-if="!rowSoLuongHopLe(row)">
                                    <p class="text-xs text-red-500 dark:text-red-400 mt-1">Số lượng phải lớn hơn 0</p>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <template x-for="id in removedIds" :key="'rm-' + id">
            <input type="hidden" name="remove_chi_tiet[]" :value="id">
        </template>
        @else
            @php
                $chiTietKhac = $hoaDon->chiTiet->filter(fn($ct) => $ct->chi_so_cu === null);
            @endphp
            @if($chiTietKhac->isNotEmpty())
            <!-- Other fee items (read-only; meter items not editable outside CHUA_THANH_TOAN) -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-slate-300">Các khoản phí khác</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($chiTietKhac as $ct)
                    <div class="flex items-center justify-between px-5 py-3">
                        <span class="text-sm text-gray-700 dark:text-slate-300">{{ $ct->ten_phi_dich_vu }}</span>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900 dark:text-white tabular-nums">{{ number_format($ct->thanh_tien, 0, ',', '.') }}đ</p>
                            <p class="text-xs text-gray-400 dark:text-slate-500">{{ number_format($ct->so_luong, 0, ',', '.') }} × {{ number_format($ct->don_gia, 0, ',', '.') }}đ</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif

    </form>

    <!-- Actions row (outside form to allow separate delete form) -->
    <div class="flex items-center justify-between">

        {{-- Nút xóa --}}
        @if($isDaTT)
        <div class="relative group">
            <button type="button" disabled
                    class="px-4 py-2.5 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900 text-red-400 dark:text-red-600 text-sm font-medium rounded-lg opacity-60 cursor-not-allowed">
                Xóa hóa đơn
            </button>
            <div class="absolute bottom-full left-0 mb-2 px-3 py-2 bg-gray-900 dark:bg-slate-700 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                Hóa đơn đã thanh toán nên không thể xóa.
                <div class="absolute top-full left-4 -mt-0.5 w-2 h-2 bg-gray-900 dark:bg-slate-700 rotate-45"></div>
            </div>
        </div>
        @else
        <form action="{{ route('admin.hoa-don.destroy', $hoaDon) }}" method="POST"
              onsubmit="return confirm('Xóa hóa đơn {{ $hoaDon->ma_thanh_toan }}? Thao tác này không thể hoàn tác.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="px-4 py-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                Xóa hóa đơn
            </button>
        </form>
        @endif

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.hoa-don.show', $hoaDon) }}"
               class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Hủy
            </a>
            @if(!$isDaTT)
            <button type="submit" form="update-form"
                    class="px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Lưu thay đổi
            </button>
            @else
            <button type="button" disabled
                    class="px-5 py-2.5 bg-violet-300 dark:bg-violet-900/30 text-white text-sm font-semibold rounded-lg cursor-not-allowed opacity-50"
                    title="Không thể chỉnh sửa hóa đơn đã thanh toán">
                Lưu thay đổi
            </button>
            @endif
        </div>

    </div>
</div>
@endsection
