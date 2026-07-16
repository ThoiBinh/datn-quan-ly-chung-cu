@extends('layouts.resident')
@section('title', 'Đặt lịch tiện ích')
@section('page-title', 'Đặt lịch tiện ích')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('resident.tien-ich.index') }}" class="hover:text-emerald-600 transition-colors">Tiện ích</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Đặt lịch</span>
    </nav>

    @if($dsCanHo->isEmpty())
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-8 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-gray-600 dark:text-slate-300 font-medium">Bạn chưa được gán căn hộ nào đang cư trú</p>
        <p class="text-sm text-gray-400 dark:text-slate-500 mt-1">Vui lòng liên hệ ban quản lý để được hỗ trợ trước khi đặt lịch tiện ích.</p>
    </div>
    @else
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
                },
                @endforeach
             },
             tienIch: '{{ old('tien_ich', $tienIchDaChon) }}',
             batDau: '{{ old('thoi_gian_bat_dau') }}',
             ketThuc: '{{ old('thoi_gian_ket_thuc') }}',
             soNguoi: {{ (int) old('so_nguoi', 1) }},
             sucChuaUrlTemplate: {{ \Illuminate\Support\Js::from(route('resident.dat-lich-tien-ich.suc-chua', ['tienIch' => '__TIEN_ICH__'])) }},
         })" x-init="initRealtime()">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thông tin đặt lịch</h2>
        </div>

        <div class="mx-5 mt-5 flex items-start gap-2.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 px-4 py-3">
            <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                Mã đặt lịch và phí sử dụng được hệ thống tự tính. Lịch của bạn sẽ ở trạng thái <strong>Chờ duyệt</strong> trước, hệ thống sẽ tự động duyệt
                theo đúng thứ tự ai đặt trước (kể cả khi bạn không mở app) ngay khi tiện ích còn đủ chỗ, hoặc ban quản lý có thể duyệt sớm hơn.
            </p>
        </div>

        <form action="{{ route('resident.dat-lich-tien-ich.store') }}" method="POST" class="p-5 space-y-5" @submit="submitting = true">
            @csrf

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

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Tiện ích <span class="text-red-500">*</span>
                    </label>
                    <select name="tien_ich" x-model="tienIch"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400 {{ $errors->has('tien_ich') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
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
                        Căn hộ <span class="text-red-500">*</span>
                    </label>
                    <select name="can_ho"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400 {{ $errors->has('can_ho') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Chọn căn hộ --</option>
                        @foreach($dsCanHo as $ch)
                        <option value="{{ $ch->id }}" {{ old('can_ho') == $ch->id ? 'selected' : '' }}>
                            {{ $ch->so_can_ho }} — {{ $ch->toaNha?->ten_toa_nha ?? '?' }}
                        </option>
                        @endforeach
                    </select>
                    @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Ngày sử dụng <span class="text-red-500">*</span>
                    </label>
                    <input type="date" x-model="ngay" min="{{ now()->format('Y-m-d') }}"
                           class="w-full sm:w-56 px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400 {{ ($errors->has('thoi_gian_bat_dau') || $errors->has('thoi_gian_ket_thuc')) ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
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
                                class="w-full sm:w-56 px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
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
                                class="w-full sm:w-56 px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
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

                <!-- Card sức chứa: cập nhật AJAX ngay khi đổi tiện ích/ngày/giờ, và real-time qua Reverb/Echo khi người khác đặt/hủy/duyệt/từ chối/hoàn thành -->
                <template x-if="tienIch && batDau && ketThuc">
                <div class="sm:col-span-2 rounded-xl border shadow-md p-4 transition-colors"
                     :class="{
                        'border-emerald-200 bg-emerald-50 dark:bg-emerald-900/10 dark:border-emerald-800': capacity && (capacityTrangThai === 'con-cho' || capacityTrangThai === 'khong-gioi-han'),
                        'border-amber-200 bg-amber-50 dark:bg-amber-900/10 dark:border-amber-800': capacity && capacityTrangThai === 'gan-day',
                        'border-red-200 bg-red-50 dark:bg-red-900/10 dark:border-red-800': capacity && capacityTrangThai === 'day',
                        'border-gray-200 bg-gray-50 dark:bg-slate-700/30 dark:border-slate-600': !capacity,
                     }">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-8a4 4 0 110 8 4 4 0 010-8zm-6 8a4 4 0 118 0v2H7v-2z"/></svg>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Sức chứa</h3>
                        <svg x-show="capacityLoading" class="w-3.5 h-3.5 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>

                    <template x-if="!capacity">
                        <p class="text-xs text-gray-400 dark:text-slate-500 italic">Đang tải thông tin sức chứa...</p>
                    </template>

                    <template x-if="capacity && capacityTrangThai === 'khong-gioi-han'">
                        <p class="text-sm text-gray-600 dark:text-slate-300">Tiện ích này không giới hạn sức chứa.</p>
                    </template>

                    <template x-if="capacity && capacityTrangThai !== 'khong-gioi-han'">
                        <div class="space-y-1">
                            <p class="text-sm text-gray-700 dark:text-slate-200">
                                Đã đặt: <span class="font-semibold" x-text="capacity.daDuyet"></span> / <span class="font-semibold" x-text="capacity.sucChua"></span> người
                            </p>
                            <p class="text-sm text-gray-700 dark:text-slate-200">
                                Còn lại: <span class="font-semibold" x-text="Math.max(0, capacity.conLai)"></span> người
                            </p>
                            <p class="text-xs font-medium flex items-center gap-1"
                               :class="{
                                  'text-emerald-600 dark:text-emerald-400': capacityTrangThai === 'con-cho',
                                  'text-amber-600 dark:text-amber-400': capacityTrangThai === 'gan-day',
                                  'text-red-600 dark:text-red-400': capacityTrangThai === 'day',
                               }">
                                <span x-show="capacityTrangThai === 'con-cho'">🟢 Còn chỗ</span>
                                <span x-show="capacityTrangThai === 'gan-day'">🟡 Còn <span x-text="Math.max(0, capacity.conLai)"></span> chỗ</span>
                                <span x-show="capacityTrangThai === 'day'">🔴 Đã đầy — lượt đặt sẽ được xếp vào hàng chờ</span>
                            </p>
                        </div>
                    </template>
                </div>
                </template>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Số người <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="so_nguoi" min="1" x-model.number="soNguoi"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400 {{ $errors->has('so_nguoi') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('so_nguoi')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Phí dự kiến</label>
                    <div class="w-full px-3 py-2.5 border border-dashed border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-gray-50 dark:bg-slate-700/50 text-gray-700 dark:text-slate-200 font-semibold">
                        <span x-text="formatTien(phiDuKien)"></span>
                        <span class="ml-1 text-xs font-normal text-gray-400 dark:text-slate-500">(tạm tính)</span>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ghi chú</label>
                    <textarea name="ghi_chu" rows="2" maxlength="500"
                              x-data x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                              placeholder="Ghi chú thêm cho lượt đặt lịch..."
                              class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400 resize-none overflow-hidden">{{ old('ghi_chu') }}</textarea>
                    @error('ghi_chu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('resident.tien-ich.index') }}"
                   class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy
                </a>
                <button type="submit" :disabled="submitting || !!loiBatDau || !!loiKetThuc"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="submitting ? 'Đang gửi...' : (capacityTrangThai === 'day' ? 'Vào hàng chờ' : 'Đặt lịch')"></span>
                </button>
            </div>
        </form>
    </div>
    @endif

</div>

<script>
function datLichForm({ tienIchInfo, tienIch, batDau: batDauInit, ketThuc: ketThucInit, soNguoi, sucChuaUrlTemplate }) {
    return {
        tienIchInfo,
        tienIch,
        sucChuaUrlTemplate,
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

        // ── Card sức chứa: baseline lấy qua AJAX ngay khi đổi tiện ích/ngày/giờ,
        // sau đó cập nhật tiếp real-time qua Reverb/Echo (kênh public "tien-ich.{id}")
        // khi người khác đặt/hủy/duyệt/từ chối/hoàn thành ảnh hưởng đúng khung giờ này.
        // Không polling — chỉ fetch khi người dùng đổi lựa chọn, và chỉ nhận thêm
        // cập nhật khi server thật sự broadcast.
        capacity: null,
        capacityLoading: false,
        capacityDebounceTimer: null,
        capacityFetchToken: 0,
        realtimeChannelId: null,
        get capacityTrangThai() {
            if (!this.capacity) return null;
            if (!this.capacity.sucChua) return 'khong-gioi-han';
            if (this.capacity.day || this.capacity.conLai <= 0) return 'day';
            const nguong = Math.max(1, Math.ceil(this.capacity.sucChua * 0.2));
            return this.capacity.conLai <= nguong ? 'gan-day' : 'con-cho';
        },
        applyCapacity(payload) {
            this.capacity = {
                sucChua: payload.suc_chua,
                daDuyet: payload.da_duyet,
                conLai: payload.suc_chua ? Math.max(0, payload.con_lai) : null,
                day: !!payload.day,
            };
        },
        requestCapacity() {
            clearTimeout(this.capacityDebounceTimer);
            if (!this.tienIch || !this.batDau || !this.ketThuc) {
                this.capacity = null;
                return;
            }
            this.capacityDebounceTimer = setTimeout(() => this.fetchCapacity(), 300);
        },
        async fetchCapacity() {
            const tienIchId = this.tienIch, batDau = this.batDau, ketThuc = this.ketThuc;
            const token = ++this.capacityFetchToken;
            this.capacityLoading = true;
            try {
                const url = new URL(this.sucChuaUrlTemplate.replace('__TIEN_ICH__', tienIchId), window.location.origin);
                url.searchParams.set('thoi_gian_bat_dau', batDau);
                url.searchParams.set('thoi_gian_ket_thuc', ketThuc);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok || token !== this.capacityFetchToken) return;
                this.applyCapacity(await res.json());
            } catch (e) {
                // Im lặng bỏ qua lỗi mạng — không chặn người dùng tiếp tục điền form.
            } finally {
                if (token === this.capacityFetchToken) this.capacityLoading = false;
            }
        },
        initRealtime() {
            this.$watch('tienIch', (value) => {
                this.subscribeTienIch(value);
                if (this.batDauGio && !this.danhSachGioBatDauKhaDung.includes(this.batDauGio)) {
                    this.batDauGio = '';
                    this.ketThucGio = '';
                }
                this.requestCapacity();
            });
            this.$watch('ngay', () => this.requestCapacity());
            this.$watch('batDauGio', () => this.requestCapacity());
            this.$watch('ketThucGio', () => this.requestCapacity());
            if (this.tienIch) this.subscribeTienIch(this.tienIch);
            this.requestCapacity();
        },
        subscribeTienIch(tienIchId) {
            if (this.realtimeChannelId && window.Echo) {
                window.Echo.leaveChannel('tien-ich.' + this.realtimeChannelId);
            }
            this.realtimeChannelId = tienIchId || null;
            if (!tienIchId || !window.Echo) return;
            window.Echo.channel('tien-ich.' + tienIchId)
                .listen('.booking.slot-updated', (e) => {
                    if (!this.batDau || !this.ketThuc) return;
                    const s1 = new Date(this.batDau), e1 = new Date(this.ketThuc);
                    const s2 = new Date(e.thoi_gian_bat_dau), e2 = new Date(e.thoi_gian_ket_thuc);
                    const giaoNhau = s1 < e2 && e1 > s2; // cùng công thức giao nhau dùng ở BookingCapacityService
                    if (!giaoNhau) return;
                    this.applyCapacity(e);
                });
        },
    };
}
</script>
@endsection
