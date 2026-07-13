@extends('layouts.admin')
@section('title', 'Thêm đặt lịch tiện ích')
@section('page-title', 'Thêm đặt lịch tiện ích')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.dat-lich-tien-ich.index') }}" class="hover:text-indigo-600 transition-colors">Đặt lịch tiện ích</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Thêm mới</span>
    </nav>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden"
         x-data="datLichForm({
             tienIchInfo: {
                @foreach($dsTienIch as $ti)
                {{ $ti->id }}: { phi: {{ (float) $ti->phi_su_dung }}, sucChua: {{ (int) ($ti->suc_chua ?? 0) }}, gioHoatDong: '{{ $ti->gio_hoat_dong ?? '' }}', canDatTruoc: {{ $ti->can_dat_truoc ? 'true' : 'false' }} },
                @endforeach
             },
             tienIch: '{{ old('tien_ich') }}',
             batDau: '{{ old('thoi_gian_bat_dau') }}',
             ketThuc: '{{ old('thoi_gian_ket_thuc') }}',
             soNguoi: {{ (int) old('so_nguoi', 1) }},
         })" x-init="initRealtime()">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thông tin đặt lịch tiện ích</h2>
        </div>

        <!-- Ghi chú quy trình tự động -->
        <div class="mx-5 mt-5 flex items-start gap-2.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 px-4 py-3">
            <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                Mã đặt lịch được hệ thống tự sinh, phí sử dụng được tự động tính theo <strong>số người/sân × đơn giá tiện ích × số giờ sử dụng</strong>.
                Lượt đặt sẽ luôn ở trạng thái <strong>Chờ duyệt</strong> khi tạo mới; hệ thống tự động duyệt theo đúng thứ tự đặt trước (FIFO) và sức chứa còn lại mỗi phút, hoặc nhân viên có thể duyệt tay ngay tại đây.
            </p>
        </div>

        <form action="{{ route('admin.dat-lich-tien-ich.store') }}" method="POST" class="p-5 space-y-5" @submit="submitting = true">
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

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Cư dân <span class="text-red-500">*</span>
                    </label>
                    <select name="cu_dan"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('cu_dan') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Chọn cư dân --</option>
                        @foreach($dsCuDan as $cd)
                        <option value="{{ $cd->id }}" {{ old('cu_dan') == $cd->id ? 'selected' : '' }}>
                            {{ $cd->ho_ten }} @if($cd->email) — {{ $cd->email }} @endif
                        </option>
                        @endforeach
                    </select>
                    @error('cu_dan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Căn hộ</label>
                    <select name="can_ho"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('can_ho') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Không chọn --</option>
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
                            · Đơn giá: <span x-text="formatTien(tienIchDaChon.phi)" class="font-medium"></span>/giờ/người
                        </p>
                    </template>
                    <!-- Cập nhật real-time qua Reverb/Echo (kênh public tien-ich.{id}): không cần refresh -->
                    <template x-if="conLaiRealtime !== null">
                        <p class="mt-1 text-xs flex items-center gap-1"
                           :class="hetChoNgay ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400'">
                            <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                            <span x-show="!hetChoNgay">Còn <span x-text="conLaiRealtime" class="font-semibold"></span> chỗ cho khung giờ này (cập nhật trực tiếp)</span>
                            <span x-show="hetChoNgay">Vừa hết chỗ cho khung giờ này — lượt đặt sẽ vào hàng chờ</span>
                        </p>
                    </template>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Thời gian bắt đầu <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="thoi_gian_bat_dau" x-model="batDau"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('thoi_gian_bat_dau') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('thoi_gian_bat_dau')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Thời gian kết thúc <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="thoi_gian_ket_thuc" x-model="ketThuc"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('thoi_gian_ket_thuc') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('thoi_gian_ket_thuc')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Số người/Sân <span class="text-red-500">*</span>
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
                              placeholder="Ghi chú thêm cho lượt đặt lịch..."
                              class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none overflow-hidden">{{ old('ghi_chu') }}</textarea>
                    @error('ghi_chu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.dat-lich-tien-ich.index') }}"
                   class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy
                </a>
                <button type="submit" :disabled="submitting"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="submitting ? 'Đang lưu...' : 'Thêm đặt lịch'"></span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
function datLichForm({ tienIchInfo, tienIch, batDau, ketThuc, soNguoi }) {
    return {
        tienIchInfo,
        tienIch,
        batDau,
        ketThuc,
        soNguoi,
        submitting: false,
        get tienIchDaChon() {
            return this.tienIchInfo[this.tienIch] ?? null;
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

        // ── Realtime (Reverb/Echo, kênh public "tien-ich.{id}") ──
        // Không polling: chỉ cập nhật khi server thật sự broadcast lúc có
        // booking khác vừa được duyệt/hủy/từ chối/hoàn thành ảnh hưởng sức chứa.
        realtimeChannelId: null,
        realtimeSlot: null,
        initRealtime() {
            this.$watch('tienIch', (value) => this.subscribeTienIch(value));
            if (this.tienIch) this.subscribeTienIch(this.tienIch);
        },
        subscribeTienIch(tienIchId) {
            if (this.realtimeChannelId && window.Echo) {
                window.Echo.leaveChannel('tien-ich.' + this.realtimeChannelId);
            }
            this.realtimeSlot = null;
            this.realtimeChannelId = tienIchId || null;
            if (!tienIchId || !window.Echo) return;
            window.Echo.channel('tien-ich.' + tienIchId)
                .listen('.booking.slot-updated', (e) => { this.realtimeSlot = e; });
        },
        get conLaiRealtime() {
            if (!this.realtimeSlot || !this.batDau || !this.ketThuc) return null;
            const s1 = new Date(this.batDau), e1 = new Date(this.ketThuc);
            const s2 = new Date(this.realtimeSlot.thoi_gian_bat_dau), e2 = new Date(this.realtimeSlot.thoi_gian_ket_thuc);
            const giaoNhau = s1 < e2 && e1 > s2; // cùng công thức giao nhau dùng ở BookingCapacityService
            if (!giaoNhau || this.realtimeSlot.con_lai === null) return null;
            return this.realtimeSlot.con_lai;
        },
        get hetChoNgay() {
            return this.conLaiRealtime !== null && this.conLaiRealtime <= 0;
        },
    };
}
</script>
@endpush
@endsection
