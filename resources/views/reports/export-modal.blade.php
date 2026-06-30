{{--
    Partial: Export Report Modal (dùng @include, không phải x-component)
    Biến truyền vào:
        $filterOptions  - array từ DashboardReportService::getFilterOptions()
        $pdfRoute       - route name string
        $excelRoute     - route name string
--}}
@php
    $__modalId  = 'em_' . substr(md5(uniqid()), 0, 8);
    $__pdfUrl   = route($pdfRoute);
    $__excelUrl = route($excelRoute);
@endphp

{{-- Wrapper KHÔNG có x-cloak — buttons phải luôn hiển thị --}}
<div x-data="{{ $__modalId }}()">

    {{-- Trigger buttons --}}
    <div class="flex items-center gap-2">
        <button @click="open('pdf')" type="button"
                style="background-color:#e11d48;"
                @mouseover="$el.style.backgroundColor='#be123c'"
                @mouseleave="$el.style.backgroundColor='#e11d48'"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold
                       text-white shadow-sm transition-all
                       focus:outline-none focus:ring-2 focus:ring-offset-1"
                onfocus="this.style.boxShadow='0 0 0 2px #fff,0 0 0 4px #fb7185'"
                onblur="this.style.boxShadow=''"
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Xuất PDF
        </button>
        <button @click="open('excel')" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold
                       bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-all
                       focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Xuất Excel
        </button>
    </div>

    {{-- Modal (teleport ra body để thoát khỏi stacking context) --}}
    <template x-teleport="body">
        <div x-show="showModal"
             style="display:none"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0"
             x-transition:leave="transition duration-150" x-transition:leave-end="opacity-0"
             @click.self="showModal = false">

            <div class="flex flex-col bg-white dark:bg-slate-800 w-full max-w-lg rounded-2xl shadow-2xl max-h-[90vh]"
                 x-transition:enter="transition duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 @click.stop>

                {{-- Header --}}
                <div class="shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center"
                             :style="exportType === 'pdf'
                                 ? 'background:#ffe4e6; color:#e11d48;'
                                 : 'background:#d1fae5; color:#059669;'">
                            <template x-if="exportType === 'pdf'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </template>
                            <template x-if="exportType === 'excel'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </template>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white"
                                x-text="exportType === 'pdf' ? 'Xuất báo cáo PDF' : 'Xuất báo cáo Excel'"></h3>
                            <p class="text-xs text-gray-400 dark:text-slate-400">Chọn khoảng thời gian và bộ lọc</p>
                        </div>
                    </div>
                    <button @click="showModal = false"
                            class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400
                                   hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable content --}}
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

                    {{-- Period presets --}}
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Khoảng thời gian</p>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach([
                                ['today',        'Hôm nay'],
                                ['this_week',    'Tuần này'],
                                ['this_month',   'Tháng này'],
                                ['this_quarter', 'Quý này'],
                                ['this_year',    'Năm này'],
                                ['custom',       'Tùy chọn'],
                            ] as [$val, $label])
                            <button type="button"
                                    @click="period = '{{ $val }}'"
                                    :class="period === '{{ $val }}'
                                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-semibold'
                                        : 'border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:border-gray-300'"
                                    class="py-1.5 text-xs border rounded-lg transition-all">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Custom date range --}}
                    <div x-show="period === 'custom'" class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">Từ ngày</label>
                            <input type="date" x-model="dateFrom"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm
                                          bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">Đến ngày</label>
                            <input type="date" x-model="dateTo"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm
                                          bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>

                    {{-- Other filters --}}
                    <div class="space-y-3">
                        <p class="text-sm font-medium text-gray-700 dark:text-slate-300">
                            Bộ lọc khác
                            <span class="text-xs text-gray-400 font-normal">(tùy chọn)</span>
                        </p>

                        @if($filterOptions['toa_nha']->isNotEmpty())
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Tòa nhà</label>
                            <select x-model="toaNha"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm
                                           bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Tất cả tòa nhà</option>
                                @foreach($filterOptions['toa_nha'] as $tn)
                                <option value="{{ $tn->id }}">{{ $tn->ten_toa_nha }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Trạng thái hóa đơn</label>
                            <select x-model="trangThaiHoaDon"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm
                                           bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Tất cả trạng thái</option>
                                @foreach($filterOptions['trang_thai_hoa_don'] as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($filterOptions['phuong_thuc_tt']->isNotEmpty())
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Phương thức thanh toán</label>
                            <select x-model="phuongThucTT"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm
                                           bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Tất cả phương thức</option>
                                @foreach($filterOptions['phuong_thuc_tt'] as $pt)
                                <option value="{{ $pt }}">{{ $pt }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        @if($filterOptions['phi_dich_vu']->isNotEmpty())
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Loại phí dịch vụ</label>
                            <select x-model="phiDichVu"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm
                                           bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Tất cả phí dịch vụ</option>
                                @foreach($filterOptions['phi_dich_vu'] as $phi)
                                <option value="{{ $phi->id }}">{{ $phi->ten_phi_dich_vu }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>

                </div>

                {{-- Footer --}}
                <div class="shrink-0 flex gap-3 px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 rounded-b-2xl">
                    <button type="button" @click="showModal = false"
                            class="flex-1 py-2.5 border border-gray-300 dark:border-slate-600
                                   text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl
                                   hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        Hủy
                    </button>

                    <template x-if="loading">
                        <button type="button" disabled
                                class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-gray-400
                                       flex items-center justify-center gap-2 cursor-not-allowed">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Đang xuất...
                        </button>
                    </template>

                    <template x-if="!loading">
                        <a :href="buildUrl()"
                           @click="onExport()"
                           :style="exportType === 'pdf'
                               ? 'background-color:#e11d48;'
                               : 'background-color:#059669;'"
                           @mouseover="$el.style.backgroundColor = exportType === 'pdf' ? '#be123c' : '#047857'"
                           @mouseleave="$el.style.backgroundColor = exportType === 'pdf' ? '#e11d48' : '#059669'"
                           class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white
                                  flex items-center justify-center gap-2
                                  focus:outline-none transition-all">
                            <template x-if="exportType === 'pdf'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </template>
                            <template x-if="exportType === 'excel'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </template>
                            <span x-text="exportType === 'pdf' ? 'Tải PDF' : 'Tải Excel'"></span>
                        </a>
                    </template>
                </div>

            </div>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('{{ $__modalId }}', () => ({
        showModal:       false,
        exportType:      'pdf',
        loading:         false,
        period:          'this_month',
        dateFrom:        '',
        dateTo:          '',
        toaNha:          '',
        trangThaiHoaDon: '',
        phuongThucTT:    '',
        phiDichVu:       '',
        pdfUrl:          '{{ $__pdfUrl }}',
        excelUrl:        '{{ $__excelUrl }}',

        open(type) {
            this.exportType = type;
            this.loading    = false;
            this.showModal  = true;
        },

        buildUrl() {
            const base = this.exportType === 'pdf' ? this.pdfUrl : this.excelUrl;
            const p    = new URLSearchParams({ period: this.period });
            if (this.period === 'custom') {
                if (this.dateFrom) p.set('date_from', this.dateFrom);
                if (this.dateTo)   p.set('date_to',   this.dateTo);
            }
            if (this.toaNha)          p.set('toa_nha',            this.toaNha);
            if (this.trangThaiHoaDon) p.set('trang_thai_hoa_don', this.trangThaiHoaDon);
            if (this.phuongThucTT)    p.set('phuong_thuc_tt',     this.phuongThucTT);
            if (this.phiDichVu)       p.set('phi_dich_vu',        this.phiDichVu);
            return base + '?' + p.toString();
        },

        onExport() {
            this.loading = true;
            setTimeout(() => { this.loading = false; this.showModal = false; }, 4000);
        },
    }));
});
</script>
