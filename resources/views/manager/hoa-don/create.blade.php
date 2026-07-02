@extends('layouts.manager')
@section('title', 'Tạo hóa đơn')
@section('page-title', 'Tạo hóa đơn mới')

@section('content')
<div class="max-w-3xl space-y-5"
     x-data="{
         canHo: '{{ old('can_ho', '') }}',
         thang: {{ old('thang', now()->month) }},
         nam: {{ old('nam', now()->year) }},
         fees: null,
         cachedFees: null,
         loading: false,
         fetchError: null,
         oldChiSo: {{ Js::from(old('chi_so', [])) }},
         soLuongMap: {},
         phuongTienInfo: [],
         removedFeeIds: [],

         serviceModal: false,
         modalLoading: false,
         modalSaving: false,
         modalServices: [],
         selectedInModal: [],

         confirmDeleteFeeId: null,

         async fetchPreview() {
             if (!this.canHo || !this.thang || !this.nam) {
                 this.fees = null; this.cachedFees = null;
                 this.phuongTienInfo = []; this.removedFeeIds = [];
                 return;
             }
             this.loading = true;
             this.fetchError = null;
             try {
                 const url = '{{ route('manager.hoa-don.preview-phi') }}?can_ho=' + this.canHo + '&thang=' + this.thang + '&nam=' + this.nam;
                 const r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                 const data = await r.json();
                 if (data.error) {
                     this.fetchError = data.error; this.fees = null; this.cachedFees = null; this.phuongTienInfo = [];
                 } else {
                     this.fees = data.fees.map(f => ({...f}));
                     this.cachedFees = data.fees.map(f => ({...f}));
                     this.removedFeeIds = [];
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
             } catch(e) {
                 this.fetchError = 'Không thể tải dữ liệu phí.'; this.fees = null; this.cachedFees = null; this.phuongTienInfo = [];
             }
             this.loading = false;
         },

         removeFee(phiId) {
             this.fees = this.fees.filter(f => f.phi_dich_vu_id !== phiId);
             if (!this.removedFeeIds.includes(phiId)) this.removedFeeIds.push(phiId);
             this.confirmDeleteFeeId = null;
         },

         async openServiceModal() {
             if (!this.canHo) return;
             this.serviceModal = true;
             this.selectedInModal = [];
             this.modalLoading = true;
             try {
                 const url = '{{ route('manager.hoa-don.can-ho-services') }}?can_ho=' + this.canHo;
                 const r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                 const data = await r.json();
                 this.modalServices = data.services || [];
                 this.selectedInModal = this.modalServices
                     .filter(svc => svc.selected)
                     .map(svc => String(svc.phi_dich_vu_id));
             } catch(e) { this.modalServices = []; }
             this.modalLoading = false;
         },

         async addSelectedToFees() {
             if (!this.canHo || this.modalSaving) return;
             this.modalSaving = true;
             try {
                 const url = '{{ route('manager.hoa-don.can-ho-services.sync') }}';
                 const r = await fetch(url, {
                     method: 'POST',
                     headers: {
                         'Accept': 'application/json',
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                     },
                     body: JSON.stringify({
                         can_ho: this.canHo,
                         phi_dich_vu_ids: this.selectedInModal.map(id => parseInt(id)),
                     }),
                 });
                 const data = await r.json();
                 if (data.error) {
                     this.fetchError = data.error;
                 } else {
                     this.removedFeeIds = [];
                     await this.fetchPreview();
                     this.serviceModal = false;
                 }
             } catch (e) {
                 this.fetchError = 'Không thể lưu dịch vụ căn hộ.';
             }
             this.modalSaving = false;
         },

         fmtMoney(n) {
             if (n === null || n === undefined) return '—';
             return new Intl.NumberFormat('vi-VN').format(n) + 'đ';
         },

         getFeeTotal(fee) {
             if (fee.billing_type === 'fixed') {
                 const sl = parseInt(this.soLuongMap[String(fee.phi_dich_vu_id)] ?? 1);
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

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-600">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('manager.hoa-don.store') }}" class="space-y-5">
        @csrf

        <!-- Hidden excluded_services inputs -->
        <template x-for="id in removedFeeIds" :key="id">
            <input type="hidden" name="excluded_services[]" :value="id">
        </template>

        <!-- Basic info -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-800">Thông tin hóa đơn</h2>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Căn hộ <span class="text-red-500">*</span></label>
                    <select name="can_ho" x-model="canHo" @change="fetchPreview()"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('can_ho') ? 'border-red-400' : 'border-gray-300' }}">
                        <option value="">-- Chọn căn hộ --</option>
                        @foreach($dsCanHo as $ch)
                        <option value="{{ $ch->id }}" {{ old('can_ho') == $ch->id ? 'selected' : '' }}>
                            {{ $ch->so_can_ho }} — {{ $ch->toaNha?->ten_toa_nha ?? '?' }}
                        </option>
                        @endforeach
                    </select>
                    @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- "Thêm dịch vụ căn hộ" button -->
                <div x-show="canHo && fees !== null" x-cloak>
                    <button type="button" @click="openServiceModal()"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Thêm dịch vụ căn hộ
                        <template x-if="removedFeeIds.length > 0">
                            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full bg-indigo-600 text-white" x-text="removedFeeIds.length"></span>
                        </template>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Tháng <span class="text-red-500">*</span></label>
                        <select name="thang" x-model="thang" @change="fetchPreview()"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('thang', now()->month) == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Năm <span class="text-red-500">*</span></label>
                        <input type="number" name="nam" x-model="nam" @change="fetchPreview()"
                               min="2020" max="{{ now()->year + 2 }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee preview -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">Chi tiết phí dịch vụ</h2>
                        <template x-if="fees && fees.length > 0">
                            <p class="text-xs text-gray-400 mt-0.5" x-text="fees.length + ' dịch vụ'"></p>
                        </template>
                    </div>
                </div>
                <div x-show="loading" class="flex items-center gap-1.5 text-xs text-indigo-600">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Đang tải...
                </div>
            </div>

            <div class="p-5">
                <div x-show="!canHo && !loading" class="text-center py-8 text-sm text-gray-400">
                    Chọn căn hộ, tháng và năm để xem chi tiết phí.
                </div>
                <div x-show="fetchError && !loading" class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-600" x-text="fetchError"></div>
                <div x-show="fees !== null && fees.length === 0 && !loading" class="text-center py-8 text-sm text-amber-600">
                    Căn hộ này chưa có dịch vụ nào trong hóa đơn. Nhấn "Thêm dịch vụ căn hộ" để thêm.
                </div>

                <template x-if="fees && fees.length > 0">
                    <div class="space-y-3">

                        <template x-if="phuongTienInfo && phuongTienInfo.length > 0">
                            <div class="p-3 bg-green-50/70 border border-green-100 rounded-lg">
                                <p class="text-xs font-semibold text-green-700 mb-2">Phương tiện được tính trong tháng</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="pt in phuongTienInfo" :key="pt.ten_loai">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-green-200 rounded-md text-xs">
                                            <span class="text-gray-700" x-text="pt.ten_loai"></span>
                                            <span class="font-semibold text-green-700" x-text="pt.so_luong + ' chiếc'"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-for="fee in fees" :key="fee.phi_dich_vu_id">
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 bg-gray-50">
                                    <span class="text-sm font-medium text-gray-800" x-text="fee.ten_phi_dich_vu"></span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-semibold text-gray-900 tabular-nums"
                                              x-text="fee.billing_type === 'meter' ? '(nhập chỉ số)' : fmtMoney(getFeeTotal(fee))"></span>
                                        <button type="button" @click="confirmDeleteFeeId = fee.phi_dich_vu_id"
                                                title="Loại dịch vụ này khỏi hóa đơn"
                                                class="p-1 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <template x-if="fee.billing_type === 'meter'">
                                    <div class="px-4 py-3 grid grid-cols-2 gap-3 bg-blue-50/60">
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Chỉ số cũ</label>
                                            <input type="number"
                                                   :name="'chi_so[' + fee.phi_dich_vu_id + '][cu]'"
                                                   :value="(oldChiSo[String(fee.phi_dich_vu_id)] || {}).cu || 0"
                                                   min="0"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Chỉ số mới</label>
                                            <input type="number"
                                                   :name="'chi_so[' + fee.phi_dich_vu_id + '][moi]'"
                                                   :value="(oldChiSo[String(fee.phi_dich_vu_id)] || {}).moi || 0"
                                                   min="0"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                        </div>
                                        <p class="col-span-2 text-xs text-blue-600">
                                            Sản lượng = Chỉ số mới − Chỉ số cũ × Đơn giá: <span x-text="fee.don_gia_fmt"></span>
                                        </p>
                                    </div>
                                </template>

                                <template x-if="fee.billing_type === 'vehicle' && fee.preview_rows && fee.preview_rows.length > 0">
                                    <div class="px-4 py-3 bg-green-50/50">
                                        <table class="w-full text-xs text-gray-600">
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
                                    <div class="px-4 py-2 text-xs text-gray-400 bg-gray-50/50">Không có phương tiện nào trong tháng này.</div>
                                </template>

                                <template x-if="fee.billing_type === 'area'">
                                    <div class="px-4 py-3 bg-indigo-50/40">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <span class="text-xs text-gray-500">Diện tích căn hộ:</span>
                                            <span class="text-xs font-medium text-indigo-700" x-text="fee.so_luong + ' m²'"></span>
                                            <span class="text-xs text-gray-400">×</span>
                                            <span class="text-xs text-gray-600" x-text="fee.don_gia_fmt"></span>
                                            <span class="text-xs text-gray-400">=</span>
                                            <span class="text-sm font-semibold text-indigo-700 tabular-nums" x-text="fmtMoney(fee.thanh_tien)"></span>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="fee.billing_type === 'fixed'">
                                    <div class="px-4 py-3 bg-indigo-50/20">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <label class="text-xs text-gray-500">Số lượng:</label>
                                            <input type="number"
                                                   :name="'chi_so[' + fee.phi_dich_vu_id + '][so_luong]'"
                                                   min="0"
                                                   :value="soLuongMap[String(fee.phi_dich_vu_id)] ?? 1"
                                                   @input="soLuongMap[String(fee.phi_dich_vu_id)] = Math.max(0, parseInt($event.target.value) || 0)"
                                                   class="w-20 px-2 py-1.5 border border-indigo-300 rounded-lg text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                            <span class="text-xs text-gray-400">×</span>
                                            <span class="text-xs text-gray-600" x-text="fee.don_gia_fmt"></span>
                                            <span class="text-xs text-gray-400">=</span>
                                            <span class="text-sm font-semibold text-indigo-700 tabular-nums"
                                                  x-text="fmtMoney((soLuongMap[String(fee.phi_dich_vu_id)] ?? 1) * fee.don_gia)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                            <span class="text-sm font-medium text-gray-600">Dự kiến tổng tiền <span class="text-xs text-gray-400">(chưa gồm điện/nước)</span></span>
                            <span class="text-lg font-bold text-gray-900 tabular-nums" x-text="fmtMoney(previewTotal())"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('manager.hoa-don.index') }}"
               class="px-4 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Hủy
            </a>
            <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Tạo hóa đơn
            </button>
        </div>
    </form>

    <!-- Modal: Thêm dịch vụ căn hộ -->
    <template x-teleport="body">
        <div x-show="serviceModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-black/50" @click="serviceModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[85vh] flex flex-col"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Thêm dịch vụ căn hộ</h3>
                            <p class="text-xs text-gray-400">Chọn dịch vụ áp dụng cho căn hộ</p>
                        </div>
                    </div>
                    <button type="button" @click="serviceModal = false"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-2">
                    <template x-if="modalLoading">
                        <div class="text-center py-10">
                            <svg class="w-6 h-6 animate-spin text-indigo-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <p class="text-sm text-gray-400 mt-2">Đang tải...</p>
                        </div>
                    </template>
                    <template x-if="!modalLoading && modalServices.length === 0">
                        <div class="text-center py-10 text-sm text-gray-400">Chưa có dịch vụ nào trong hệ thống.</div>
                    </template>
                    <template x-if="!modalLoading && modalServices.length > 0">
                        <div class="space-y-2">
                            <template x-for="svc in modalServices" :key="svc.phi_dich_vu_id">
                                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl transition-colors cursor-pointer hover:bg-indigo-50 bg-white">
                                    <div class="flex-shrink-0">
                                        <input type="checkbox"
                                               :value="String(svc.phi_dich_vu_id)"
                                               x-model="selectedInModal"
                                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800" x-text="svc.ten_phi_dich_vu"></p>
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5">
                                            <span class="text-xs text-gray-500" x-text="svc.loai_phi_dich_vu"></span>
                                            <span class="text-xs text-gray-300">•</span>
                                            <span class="text-xs text-gray-500" x-text="svc.loai_tinh_phi"></span>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <span class="text-sm font-semibold text-gray-800" x-text="svc.don_gia_fmt"></span>
                                        <p class="text-xs text-gray-400" x-text="'/' + svc.don_vi_tinh"></p>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </template>
                </div>
                <div class="flex items-center justify-between gap-3 px-5 py-4 border-t border-gray-100 flex-shrink-0">
                    <span class="text-xs text-gray-500" x-text="selectedInModal.length + ' đã chọn'"></span>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="serviceModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Hủy
                        </button>
                        <button type="button" @click="addSelectedToFees()"
                                :disabled="modalSaving"
                                :class="modalSaving ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg transition-colors">
                            <span x-text="modalSaving ? 'Đang lưu...' : 'Lưu'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Modal: Xác nhận xóa dịch vụ -->
    <template x-teleport="body">
        <div x-show="confirmDeleteFeeId !== null" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="absolute inset-0 bg-black/50" @click="confirmDeleteFeeId = null"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Loại dịch vụ khỏi hóa đơn?</h3>
                        <p class="text-sm text-gray-500 mt-1">Nếu xóa, dịch vụ sẽ bị loại khỏi hóa đơn này. Bạn có thể thêm lại qua nút "Thêm dịch vụ căn hộ".</p>
                    </div>
                    <div class="flex items-center gap-3 w-full">
                        <button type="button" @click="confirmDeleteFeeId = null"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Giữ lại
                        </button>
                        <button type="button" @click="removeFee(confirmDeleteFeeId)"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                            Loại bỏ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
