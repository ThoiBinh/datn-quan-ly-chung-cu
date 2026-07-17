@extends('layouts.admin')
@section('title', 'Chỉnh sửa đặt lịch tiện ích')
@section('page-title', 'Chỉnh sửa đặt lịch tiện ích')

@section('content')
@php $coTheSua = (int) $datLichTienIch->trang_thai === \App\Models\DatLichTienIch::TRANG_THAI_CHO_DUYET; @endphp
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.dat-lich-tien-ich.index') }}" class="hover:text-indigo-600 transition-colors">Đặt lịch tiện ích</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.dat-lich-tien-ich.show', $datLichTienIch) }}" class="hover:text-indigo-600 transition-colors font-mono">{{ $datLichTienIch->ma_dat_lich }}</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Chỉnh sửa</span>
    </nav>

    @unless($coTheSua)
    <div class="flex items-start gap-2.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-4 py-3">
        <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm text-amber-700 dark:text-amber-300">
            Lượt đặt lịch này đang ở trạng thái <strong>{{ $datLichTienIch->trang_thai_label['text'] }}</strong> nên không thể chỉnh sửa nội dung.
            Chỉ có thể sửa khi đang <strong>Chờ duyệt</strong>. Dùng chức năng Hủy ở trang chi tiết nếu cần dừng lượt đặt này.
        </p>
    </div>
    @endunless

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden"
         x-data="datLichForm({
             tienIchInfo: {
                @foreach($dsTienIch as $ti)
                {{ $ti->id }}: {
                    phi: {{ (float) $ti->phi_su_dung }},
                    sucChua: {{ (int) ($ti->suc_chua ?? 0) }},
                    gioHoatDong: '{{ $ti->gio_hoat_dong ?? '' }}',
                    gioMoCua: '{{ $ti->gio_mo_cua ? substr($ti->gio_mo_cua, 0, 5) : '' }}',
                    gioDongCua: '{{ $ti->gio_dong_cua ? substr($ti->gio_dong_cua, 0, 5) : '' }}',
                    canDatTruoc: {{ $ti->can_dat_truoc ? 'true' : 'false' }},
                },
                @endforeach
             },
             tienIch: '{{ old('tien_ich', $datLichTienIch->tien_ich) }}',
             batDau: '{{ old('thoi_gian_bat_dau', $datLichTienIch->thoi_gian_bat_dau?->format('Y-m-d\TH:i')) }}',
             ketThuc: '{{ old('thoi_gian_ket_thuc', $datLichTienIch->thoi_gian_ket_thuc?->format('Y-m-d\TH:i')) }}',
             soNguoi: {{ (int) old('so_nguoi', $datLichTienIch->so_nguoi) }},
         })" x-init="initSlotWatch()">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Cập nhật đặt lịch tiện ích</h2>
        </div>

        <fieldset :disabled="!{{ $coTheSua ? 'true' : 'false' }}" :class="{ 'opacity-60': !{{ $coTheSua ? 'true' : 'false' }} }">
        <form action="{{ route('admin.dat-lich-tien-ich.update', $datLichTienIch) }}" method="POST" class="p-5 space-y-5" @submit="submitting = true">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <ul class="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Cư dân</label>
                    <div class="w-full px-3 py-2.5 border border-dashed border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-gray-50 dark:bg-slate-700/50 text-gray-600 dark:text-slate-300">
                        {{ $datLichTienIch->cuDan?->ho_ten ?? '—' }}
                        <span class="text-xs text-gray-400 dark:text-slate-500">(không thể đổi cư dân của lượt đặt)</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Căn hộ</label>
                    <select name="can_ho"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('can_ho') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Không chọn --</option>
                        @foreach($dsCanHo as $ch)
                        <option value="{{ $ch->id }}" {{ old('can_ho', $datLichTienIch->can_ho) == $ch->id ? 'selected' : '' }}>
                            {{ $ch->so_can_ho }} — {{ $ch->toaNha?->ten_toa_nha ?? '?' }}
                        </option>
                        @endforeach
                    </select>
                    @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Tiện ích <span class="text-red-500">*</span>
                    </label>
                    <select name="tien_ich" x-model="tienIch"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('tien_ich') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Chọn tiện ích --</option>
                        @foreach($dsTienIch as $ti)
                        <option value="{{ $ti->id }}">
                            {{ $ti->ten_tien_ich }} @if($ti->loaiTienIch) ({{ $ti->loaiTienIch->ten_loai_tien_ich }}) @endif
                        </option>
                        @endforeach
                    </select>
                    @error('tien_ich')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    <template x-if="tienIchDaChon">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-slate-400">
                            Giờ hoạt động: <span x-text="tienIchDaChon.gioHoatDong || 'cả ngày'" class="font-medium"></span>
                            <template x-if="tienIchDaChon.sucChua > 0"> · Sức chứa tối đa: <span x-text="tienIchDaChon.sucChua" class="font-medium"></span> người</template>
                            · Đơn giá: <span x-text="formatTien(tienIchDaChon.phi)" class="font-medium"></span>/giờ/người(sân)
                        </p>
                    </template>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Ngày sử dụng <span class="text-red-500">*</span>
                    </label>
                    <input type="date" x-model="ngay"
                           class="w-full sm:w-56 px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ ($errors->has('thoi_gian_bat_dau') || $errors->has('thoi_gian_ket_thuc')) ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Giờ bắt đầu <span class="text-red-500">*</span>
                    </label>

                    <template x-if="!tienIch">
                        <p class="text-xs text-gray-400 dark:text-slate-500 italic">Vui lòng chọn tiện ích để xem khung giờ khả dụng.</p>
                    </template>
                    <template x-if="tienIch && !ngay">
                        <p class="text-xs text-gray-400 dark:text-slate-500 italic">Vui lòng chọn ngày sử dụng trước.</p>
                    </template>
                    <template x-if="tienIch && ngay">
                        <select x-model="batDauGio" @change="onGioBatDauChange()"
                                class="w-full sm:w-56 px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="">-- Chọn thời gian bắt đầu --</option>
                            <template x-for="slot in danhSachGioBatDauKhaDung" :key="slot">
                                <option :value="slot" x-text="slot"></option>
                            </template>
                        </select>
                    </template>

                    <input type="hidden" name="thoi_gian_bat_dau" :value="batDau">
                    @error('thoi_gian_bat_dau')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    <template x-if="loiBatDau"><p class="mt-1.5 text-xs text-red-500" x-text="loiBatDau"></p></template>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Giờ kết thúc <span class="text-red-500">*</span>
                    </label>

                    <template x-if="!batDauGio">
                        <p class="text-xs text-gray-400 dark:text-slate-500 italic">Vui lòng chọn giờ bắt đầu trước.</p>
                    </template>
                    <template x-if="batDauGio && danhSachGioKetThuc.length === 0">
                        <p class="text-xs text-amber-600 dark:text-amber-400 italic">Không còn khung giờ kết thúc phù hợp, vui lòng chọn giờ bắt đầu sớm hơn.</p>
                    </template>
                    <template x-if="batDauGio && danhSachGioKetThuc.length > 0">
                        <select x-model="ketThucGio"
                                class="w-full sm:w-56 px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="">-- Chọn thời gian kết thúc --</option>
                            <template x-for="slot in danhSachGioKetThuc" :key="slot">
                                <option :value="slot" x-text="slot"></option>
                            </template>
                        </select>
                    </template>

                    <input type="hidden" name="thoi_gian_ket_thuc" :value="ketThuc">
                    @error('thoi_gian_ket_thuc')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    <template x-if="loiKetThuc"><p class="mt-1.5 text-xs text-red-500" x-text="loiKetThuc"></p></template>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Số người <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="so_nguoi" min="1" x-model.number="soNguoi"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('so_nguoi') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('so_nguoi')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Phí dự kiến</label>
                    <div class="w-full px-3 py-2.5 border border-dashed border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-gray-50 dark:bg-slate-700/50 text-gray-700 dark:text-slate-200 font-semibold">
                        <span x-text="formatTien(phiDuKien)"></span>
                        <span class="ml-1 text-xs font-normal text-gray-400 dark:text-slate-500">(tạm tính, hệ thống sẽ tính lại chính xác)</span>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ghi chú</label>
                    <textarea name="ghi_chu" rows="2" maxlength="500"
                              x-data x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                              class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none overflow-hidden">{{ old('ghi_chu', $datLichTienIch->ghi_chu) }}</textarea>
                    @error('ghi_chu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.dat-lich-tien-ich.show', $datLichTienIch) }}"
                   class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy
                </a>
                <button type="submit" :disabled="submitting || !!loiBatDau || !!loiKetThuc"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="submitting ? 'Đang lưu...' : 'Lưu thay đổi'"></span>
                </button>
            </div>
        </form>
        </fieldset>
    </div>

</div>

@push('scripts')
<script>
function datLichForm({ tienIchInfo, tienIch, batDau: batDauInit, ketThuc: ketThucInit, soNguoi }) {
    return {
        tienIchInfo,
        tienIch,
        ngay: batDauInit ? batDauInit.slice(0, 10) : (ketThucInit ? ketThucInit.slice(0, 10) : ''),
        batDauGio: batDauInit ? batDauInit.slice(11, 16) : '',
        ketThucGio: ketThucInit ? ketThucInit.slice(11, 16) : '',
        soNguoi,
        submitting: false,
        get tienIchDaChon() {
            return this.tienIchInfo[this.tienIch] ?? null;
        },
        get batDau() {
            return this.ngay && this.batDauGio ? `${this.ngay}T${this.batDauGio}` : '';
        },
        get ketThuc() {
            return this.ngay && this.ketThucGio ? `${this.ngay}T${this.ketThucGio}` : '';
        },

        // ── Danh sách mốc giờ 00/30 phút sinh động từ giờ mở/đóng cửa tiện ích ──
        get danhSachGio() {
            const info = this.tienIchDaChon;
            if (!info || !window.DatLichTimeSlots) return [];
            return window.DatLichTimeSlots.taoDanhSachGio(info.gioMoCua, info.gioDongCua);
        },
        get danhSachGioKetThuc() {
            if (!window.DatLichTimeSlots) return [];
            return window.DatLichTimeSlots.locDanhSachGioKetThuc(this.danhSachGio, this.batDauGio);
        },
        coTheChonLamBatDau(slot) {
            const info = this.tienIchDaChon;
            if (!info || !window.DatLichTimeSlots) return true;
            return window.DatLichTimeSlots.coTheLaGioBatDau(slot, info.gioDongCua);
        },
        // Chỉ hiển thị các mốc bắt đầu còn đủ thời lượng tối thiểu trước giờ đóng cửa.
        get danhSachGioBatDauKhaDung() {
            return this.danhSachGio.filter((slot) => this.coTheChonLamBatDau(slot));
        },
        // Khi đổi giờ bắt đầu: danh sách kết thúc tự sinh lại (reactive getter),
        // tự động chọn Option đầu tiên nếu có.
        onGioBatDauChange() {
            const list = this.danhSachGioKetThuc;
            this.ketThucGio = list.length > 0 ? list[0] : '';
        },
        initSlotWatch() {
            this.$watch('tienIch', () => {
                if (this.batDauGio && !this.danhSachGioBatDauKhaDung.includes(this.batDauGio)) {
                    this.batDauGio = '';
                    this.ketThucGio = '';
                }
            });
        },

        get soGioUocTinh() {
            if (!this.batDau || !this.ketThuc) return 0;
            const start = new Date(this.batDau);
            const end = new Date(this.ketThuc);
            const diffMinutes = Math.floor((end - start) / 60000);
            return diffMinutes > 0 ? diffMinutes / 60 : 0;
        },
        get phiDuKien() {
            const info = this.tienIchDaChon;
            if (!info || !this.soNguoi) return 0;
            return (this.soNguoi * info.phi * this.soGioUocTinh);
        },
        formatTien(value) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(value || 0)) + ' đ';
        },

        // ── Validate khung giờ tức thời (gương với ValidatesKhungGioTienIch backend) ──
        get loiKhungGio() {
            return window.DatLichTimeValidation
                ? window.DatLichTimeValidation.validate(this.batDau, this.ketThuc)
                : { batDau: '', ketThuc: '' };
        },
        get loiBatDau() {
            return this.loiKhungGio.batDau;
        },
        get loiKetThuc() {
            return this.loiKhungGio.ketThuc;
        },
    };
}
</script>
@endpush
@endsection
