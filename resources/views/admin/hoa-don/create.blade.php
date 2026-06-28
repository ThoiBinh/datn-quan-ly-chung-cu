@extends('layouts.admin')
@section('title', 'Tạo hóa đơn')
@section('page-title', 'Tạo hóa đơn mới')

@section('content')
<div class="max-w-3xl mx-auto space-y-5"
     x-data="{
         canHo: '{{ old('can_ho', '') }}',
         thang: {{ old('thang', now()->month) }},
         nam: {{ old('nam', now()->year) }},
         fees: null,
         loading: false,
         fetchError: null,
         oldChiSo: {{ Js::from(old('chi_so', [])) }},
         soLuongMap: {},
         phuongTienInfo: [],

         async fetchPreview() {
             if (!this.canHo || !this.thang || !this.nam) { this.fees = null; this.phuongTienInfo = []; return; }
             this.loading = true;
             this.fetchError = null;
             try {
                 const url = '{{ route('admin.hoa-don.preview-phi') }}?can_ho=' + this.canHo + '&thang=' + this.thang + '&nam=' + this.nam;
                 const r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                 const data = await r.json();
                 if (data.error) { this.fetchError = data.error; this.fees = null; this.phuongTienInfo = []; }
                 else {
                     this.fees = data.fees;
                     this.phuongTienInfo = data.phuong_tien_info || [];
                     data.fees.forEach(fee => {
                         if (fee.billing_type === 'fixed') {
                             const id = String(fee.phi_dich_vu_id);
                             if (!(id in this.soLuongMap)) {
                                 const oldVal = (this.oldChiSo[id] || {}).so_luong;
                                 this.soLuongMap[id] = oldVal ? parseInt(oldVal) : 1;
                             }
                         }
                     });
                 }
             } catch(e) { this.fetchError = 'Không thể tải dữ liệu phí dịch vụ.'; this.fees = null; this.phuongTienInfo = []; }
             this.loading = false;
         },

         fmtMoney(n) {
             if (n === null || n === undefined) return '—';
             return new Intl.NumberFormat('vi-VN').format(n) + 'đ';
         },

         getFeeTotal(fee) {
             if (fee.billing_type === 'fixed') {
                 const sl = parseInt(this.soLuongMap[String(fee.phi_dich_vu_id)] || 1);
                 return sl * fee.don_gia;
             }
             return fee.thanh_tien;
         },

         previewTotal() {
             if (!this.fees) return 0;
             return this.fees.reduce((s, f) => s + (this.getFeeTotal(f) || 0), 0);
         }
     }"
     x-init="$nextTick(() => { if (canHo) fetchPreview(); })">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.hoa-don.index') }}" class="hover:text-violet-600 transition-colors">Hóa đơn</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Tạo mới</span>
    </nav>

    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-sm text-red-600 dark:text-red-400">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <ul class="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.hoa-don.store') }}" class="space-y-5">
        @csrf

        <!-- Basic info -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thông tin hóa đơn</h2>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Căn hộ <span class="text-red-500">*</span></label>
                    <select name="can_ho" x-model="canHo" @change="fetchPreview()"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400 {{ $errors->has('can_ho') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Chọn căn hộ --</option>
                        @foreach($dsCanHo as $ch)
                        <option value="{{ $ch->id }}" {{ old('can_ho') == $ch->id ? 'selected' : '' }}>
                            {{ $ch->so_can_ho }} — {{ $ch->toaNha?->ten_toa_nha ?? '?' }}
                        </option>
                        @endforeach
                    </select>
                    @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tháng <span class="text-red-500">*</span></label>
                        <select name="thang" x-model="thang" @change="fetchPreview()"
                                class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400">
                            @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('thang', now()->month) == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Năm <span class="text-red-500">*</span></label>
                        <input type="number" name="nam" x-model="nam" @change="fetchPreview()"
                               min="2020" max="{{ now()->year + 2 }}"
                               class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400">
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee preview -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Chi tiết phí dịch vụ</h2>
                </div>
                <div x-show="loading" class="flex items-center gap-1.5 text-xs text-blue-600 dark:text-blue-400">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Đang tải...
                </div>
            </div>

            <div class="p-5">
                <div x-show="!canHo && !loading" class="text-center py-8 text-sm text-gray-400 dark:text-slate-500">
                    Chọn căn hộ, tháng và năm để xem chi tiết phí.
                </div>
                <div x-show="fetchError && !loading" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 text-sm text-red-600 dark:text-red-400" x-text="fetchError"></div>
                <div x-show="fees !== null && fees.length === 0 && !loading" class="text-center py-8 text-sm text-amber-600 dark:text-amber-400">
                    Căn hộ này chưa được gán phí dịch vụ nào.
                </div>

                <template x-if="fees && fees.length > 0">
                    <div class="space-y-3">

                        {{-- Tóm tắt phương tiện được tính trong tháng --}}
                        <template x-if="phuongTienInfo && phuongTienInfo.length > 0">
                            <div class="p-3 bg-green-50/70 dark:bg-green-900/10 border border-green-100 dark:border-green-800/30 rounded-lg">
                                <p class="text-xs font-semibold text-green-700 dark:text-green-400 mb-2">Phương tiện được tính trong tháng</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="pt in phuongTienInfo" :key="pt.ten_loai">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white dark:bg-slate-700 border border-green-200 dark:border-green-700 rounded-md text-xs">
                                            <span class="text-gray-700 dark:text-slate-300" x-text="pt.ten_loai"></span>
                                            <span class="font-semibold text-green-700 dark:text-green-400" x-text="pt.so_luong + ' chiếc'"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-for="fee in fees" :key="fee.phi_dich_vu_id">
                            <div class="border border-gray-200 dark:border-slate-600 rounded-lg overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-slate-700/50">
                                    <span class="text-sm font-medium text-gray-800 dark:text-white" x-text="fee.ten_phi_dich_vu"></span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums"
                                          x-text="fee.billing_type === 'meter' ? '(nhập chỉ số)' : fmtMoney(getFeeTotal(fee))"></span>
                                </div>

                                {{-- meter: chi_so inputs --}}
                                <template x-if="fee.billing_type === 'meter'">
                                    <div class="px-4 py-3 grid grid-cols-2 gap-3 bg-blue-50/60 dark:bg-blue-900/10">
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Chỉ số cũ</label>
                                            <input type="number"
                                                   :name="'chi_so[' + fee.phi_dich_vu_id + '][cu]'"
                                                   :value="(oldChiSo[String(fee.phi_dich_vu_id)] || {}).cu || 0"
                                                   min="0"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Chỉ số mới</label>
                                            <input type="number"
                                                   :name="'chi_so[' + fee.phi_dich_vu_id + '][moi]'"
                                                   :value="(oldChiSo[String(fee.phi_dich_vu_id)] || {}).moi || 0"
                                                   min="0"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        </div>
                                        <p class="col-span-2 text-xs text-blue-600 dark:text-blue-400">
                                            Sản lượng = Chỉ số mới − Chỉ số cũ × Đơn giá: <span x-text="fee.don_gia_fmt"></span>
                                        </p>
                                    </div>
                                </template>

                                {{-- vehicle: breakdown by type --}}
                                <template x-if="fee.billing_type === 'vehicle' && fee.preview_rows && fee.preview_rows.length > 0">
                                    <div class="px-4 py-3 bg-green-50/50 dark:bg-green-900/10">
                                        <table class="w-full text-xs text-gray-600 dark:text-slate-400">
                                            <template x-for="row in fee.preview_rows" :key="row.ten">
                                                <tr>
                                                    <td class="py-0.5" x-text="row.ten"></td>
                                                    <td class="text-center py-0.5 tabular-nums" x-text="row.so_luong + ' xe'"></td>
                                                    <td class="text-center py-0.5" x-text="'× ' + row.don_gia"></td>
                                                    <td class="text-right py-0.5 font-medium tabular-nums" x-text="row.thanh_tien"></td>
                                                </tr>
                                            </template>
                                        </table>
                                    </div>
                                </template>
                                <template x-if="fee.billing_type === 'vehicle' && (!fee.preview_rows || fee.preview_rows.length === 0)">
                                    <div class="px-4 py-2 text-xs text-gray-400 dark:text-slate-500 bg-gray-50/50 dark:bg-slate-700/20">
                                        Không có phương tiện nào trong tháng này.
                                    </div>
                                </template>

                                {{-- area: diện tích × đơn giá (readonly) --}}
                                <template x-if="fee.billing_type === 'area'">
                                    <div class="px-4 py-3 bg-violet-50/40 dark:bg-violet-900/10">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <span class="text-xs text-gray-500 dark:text-slate-400">Diện tích căn hộ:</span>
                                            <span class="text-xs font-medium text-violet-700 dark:text-violet-400" x-text="fee.so_luong + ' m²'"></span>
                                            <span class="text-xs text-gray-400 dark:text-slate-500">×</span>
                                            <span class="text-xs text-gray-600 dark:text-slate-300" x-text="fee.don_gia_fmt"></span>
                                            <span class="text-xs text-gray-400 dark:text-slate-500">=</span>
                                            <span class="text-sm font-semibold text-violet-700 dark:text-violet-400 tabular-nums" x-text="fmtMoney(fee.thanh_tien)"></span>
                                        </div>
                                    </div>
                                </template>

                                {{-- fixed: nhập số lượng --}}
                                <template x-if="fee.billing_type === 'fixed'">
                                    <div class="px-4 py-3 bg-indigo-50/30 dark:bg-indigo-900/10">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <label class="text-xs text-gray-500 dark:text-slate-400">Số lượng:</label>
                                            <input type="number"
                                                   :name="'chi_so[' + fee.phi_dich_vu_id + '][so_luong]'"
                                                   min="1"
                                                   :value="soLuongMap[String(fee.phi_dich_vu_id)] ?? 1"
                                                   @input="soLuongMap[String(fee.phi_dich_vu_id)] = Math.max(1, parseInt($event.target.value) || 1)"
                                                   class="w-20 px-2 py-1.5 border border-indigo-300 dark:border-indigo-600 rounded-lg text-sm text-center bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                            <span class="text-xs text-gray-400 dark:text-slate-500">×</span>
                                            <span class="text-xs text-gray-600 dark:text-slate-300" x-text="fee.don_gia_fmt"></span>
                                            <span class="text-xs text-gray-400 dark:text-slate-500">=</span>
                                            <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-400 tabular-nums"
                                                  x-text="fmtMoney((soLuongMap[String(fee.phi_dich_vu_id)] ?? 1) * fee.don_gia)"></span>
                                        </div>
                                    </div>
                                </template>
                        </template>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-slate-600">
                            <span class="text-sm font-medium text-gray-600 dark:text-slate-300">Dự kiến tổng tiền <span class="text-xs text-gray-400">(chưa gồm điện/nước)</span></span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white tabular-nums" x-text="fmtMoney(previewTotal())"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.hoa-don.index') }}"
               class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Hủy
            </a>
            <button type="submit"
                    class="px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Tạo hóa đơn
            </button>
        </div>
    </form>
</div>
@endsection
