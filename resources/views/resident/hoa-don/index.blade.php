@extends('layouts.resident')
@section('title', 'Hóa đơn')
@section('page-title', 'Hóa đơn của tôi')

@section('content')
<div x-data="{
    showModal: false,
    loading: false,
    phuongThuc: 'momo',
    hoaDonId: null,
    maTT: '',
    soCanHo: '',
    thang: 0,
    nam: 0,
    tongTien: 0,
    daTT: 0,
    conNo: 0,
    soTien: 0,
    error: '',
    baseUrl: '{{ url('/resident/hoa-don') }}',

    openModal(hd) {
        this.hoaDonId = hd.id;
        this.maTT     = hd.ma;
        this.soCanHo  = hd.canHo;
        this.thang    = hd.thang;
        this.nam      = hd.nam;
        this.tongTien = hd.tongTien;
        this.daTT     = hd.daTT;
        this.conNo    = hd.conNo;
        this.soTien   = hd.conNo;
        this.phuongThuc = 'momo';
        this.error    = '';
        this.loading  = false;
        this.showModal = true;
    },

    getPayUrl() {
        return this.baseUrl + '/' + this.hoaDonId + (this.phuongThuc === 'momo' ? '/momo' : '/vnpay');
    },

    validate() {
        if (!this.soTien || this.soTien <= 0) {
            this.error = 'Số tiền phải lớn hơn 0.';
            return false;
        }
        if (this.soTien > this.conNo) {
            this.error = 'Số tiền không được vượt quá ' + this.fmtMoney(this.conNo) + '.';
            return false;
        }
        this.error = '';
        return true;
    },

    submit() {
        if (!this.validate()) return;
        this.loading = true;
        this.$refs.payForm.action = this.getPayUrl();
        this.$refs.payForm.submit();
    },

    fmtMoney(n) { return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + 'đ'; }
}">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-green-700 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-red-700 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        {{-- Filter bar --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <form method="GET" class="flex items-center gap-2">
                <select name="trang_thai"
                        class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 text-gray-700">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('trang_thai') == 1 ? 'selected' : '' }}>Chưa thanh toán</option>
                    <option value="2" {{ request('trang_thai') == 2 ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="3" {{ request('trang_thai') == 3 ? 'selected' : '' }}>Quá hạn</option>
                </select>
                <button type="submit"
                        class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 transition-colors font-medium">
                    Lọc
                </button>
                @if(request('trang_thai'))
                <a href="{{ route('resident.hoa-don.index') }}"
                   class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Xóa lọc</a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Mã HĐ</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kỳ</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Hạn TT</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tổng tiền</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Đã TT</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Còn nợ</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Trạng thái</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($hoaDon as $hd)
                    @php
                        $conNo = max(0, (float) $hd->tong_tien - (float) $hd->so_tien_da_thanh_toan);
                        $badge = match($hd->trang_thai) {
                            1 => ['bg-amber-100 text-amber-700', 'Chưa TT'],
                            2 => ['bg-emerald-100 text-emerald-700', 'Đã TT'],
                            3 => ['bg-red-100 text-red-700', 'Quá hạn'],
                            4 => ['bg-gray-100 text-gray-500', 'Đã hủy'],
                            default => ['bg-gray-100 text-gray-500', 'N/A'],
                        };
                        $isCanTT = in_array($hd->trang_thai, [1, 3]) && $conNo > 0;
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('resident.hoa-don.show', $hd) }}"
                               class="font-mono text-xs text-emerald-600 hover:text-emerald-800 hover:underline font-medium">
                                {{ $hd->ma_thanh_toan }}
                            </a>
                        </td>
                        <td class="px-5 py-4 font-medium text-gray-800 whitespace-nowrap">
                            Tháng {{ $hd->thang }}/{{ $hd->nam }}
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500 whitespace-nowrap hidden sm:table-cell">
                            {{ $hd->han_thanh_toan?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-right font-semibold text-gray-800 whitespace-nowrap tabular-nums">
                            {{ number_format($hd->tong_tien, 0, ',', '.') }}đ
                        </td>
                        <td class="px-5 py-4 text-right text-emerald-600 whitespace-nowrap tabular-nums hidden md:table-cell">
                            {{ number_format($hd->so_tien_da_thanh_toan, 0, ',', '.') }}đ
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap tabular-nums hidden md:table-cell
                            {{ $conNo > 0 ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                            {{ $conNo > 0 ? number_format($conNo, 0, ',', '.') . 'đ' : '—' }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge[0] }}">
                                {{ $badge[1] }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($isCanTT)
                                <button type="button"
                                        @click="openModal({
                                            id: {{ $hd->id }},
                                            ma: '{{ $hd->ma_thanh_toan }}',
                                            canHo: '{{ $hd->canHo?->so_can_ho }}',
                                            thang: {{ $hd->thang }},
                                            nam: {{ $hd->nam }},
                                            tongTien: {{ (float) $hd->tong_tien }},
                                            daTT: {{ (float) $hd->so_tien_da_thanh_toan }},
                                            conNo: {{ $conNo }}
                                        })"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                                               bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors
                                               focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    Thanh toán
                                </button>
                                @endif
                                <a href="{{ route('resident.hoa-don.show', $hd) }}"
                                   class="text-xs font-medium text-gray-500 hover:text-gray-800 transition-colors">
                                    Xem
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm">Chưa có hóa đơn nào</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($hoaDon) && method_exists($hoaDon, 'hasPages') && $hoaDon->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $hoaDon->links() }}</div>
        @endif
    </div>

    {{-- Modal thanh toán --}}
    <template x-teleport="body">
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-150" x-transition:leave-end="opacity-0"
         @click.self="showModal = false">

        {{-- Modal box: flex-col để header + footer luôn visible, chỉ content cuộn --}}
        <div class="flex flex-col bg-white w-full sm:max-w-md sm:rounded-2xl shadow-2xl
                    rounded-t-2xl max-h-[90dvh] sm:max-h-[85vh]"
             x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             @click.stop>

            {{-- Header (không cuộn) --}}
            <div class="shrink-0 flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Thanh toán hóa đơn</h3>
                <button @click="showModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Content (cuộn được) --}}
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">

                {{-- Thông tin hóa đơn --}}
                <div class="bg-gray-50 rounded-xl p-3.5 space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Mã hóa đơn</span>
                        <span class="font-mono font-semibold text-gray-800" x-text="maTT"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Căn hộ</span>
                        <span class="font-semibold text-gray-800" x-text="soCanHo"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Kỳ thanh toán</span>
                        <span class="font-semibold text-gray-800">Tháng <span x-text="thang"></span>/<span x-text="nam"></span></span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 grid grid-cols-3 gap-2 text-center">
                        <div>
                            <p class="text-xs text-gray-400">Tổng tiền</p>
                            <p class="font-semibold text-gray-800 text-xs tabular-nums mt-0.5" x-text="fmtMoney(tongTien)"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Đã TT</p>
                            <p class="font-semibold text-emerald-600 text-xs tabular-nums mt-0.5" x-text="fmtMoney(daTT)"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Còn nợ</p>
                            <p class="font-semibold text-red-500 text-xs tabular-nums mt-0.5" x-text="fmtMoney(conNo)"></p>
                        </div>
                    </div>
                </div>

                {{-- Chọn phương thức --}}
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Phương thức thanh toán</p>
                    <div class="grid grid-cols-2 gap-3">
                        {{-- MoMo --}}
                        <button type="button" @click="phuongThuc = 'momo'"
                                :class="phuongThuc === 'momo' ? 'ring-2 ring-pink-300' : 'border-gray-200 hover:border-pink-200'"
                                :style="phuongThuc === 'momo' ? 'border-color:#a50064; background:#fdf2f8;' : 'background:#fff;'"
                                class="relative flex flex-col items-center justify-center gap-1.5 rounded-xl border-2 px-3 py-3.5 transition-all cursor-pointer">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                                 style="background: linear-gradient(135deg, #a50064, #d82d8b)">
                                <span class="text-white font-black text-sm leading-none">M</span>
                            </div>
                            <span class="text-sm font-semibold"
                                  :style="phuongThuc === 'momo' ? 'color:#a50064' : 'color:#374151'">MoMo</span>
                            <div x-show="phuongThuc === 'momo'"
                                 class="absolute top-2 right-2 w-4 h-4 rounded-full flex items-center justify-center"
                                 style="background:#a50064">
                                <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                        </button>

                        {{-- VNPay --}}
                        <button type="button" @click="phuongThuc = 'vnpay'"
                                :class="phuongThuc === 'vnpay'
                                    ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-200'
                                    : 'border-gray-200 bg-white hover:border-blue-200 hover:bg-blue-50/50'"
                                class="relative flex flex-col items-center justify-center gap-1.5 rounded-xl border-2 px-3 py-3.5 transition-all cursor-pointer">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center bg-blue-600 shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <span class="text-sm font-semibold"
                                  :class="phuongThuc === 'vnpay' ? 'text-blue-700' : 'text-gray-700'">VNPay</span>
                            <div x-show="phuongThuc === 'vnpay'"
                                 class="absolute top-2 right-2 w-4 h-4 rounded-full bg-blue-600 flex items-center justify-center">
                                <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                        </button>
                    </div>
                </div>

                {{-- Số tiền --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Số tiền thanh toán (đ) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" x-model.number="soTien"
                           @input="validate()"
                           :max="conNo" min="1" step="1"
                           :disabled="loading"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white tabular-nums
                                  focus:outline-none focus:ring-2 focus:ring-emerald-400
                                  disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                           :class="error ? 'border-red-400' : 'border-gray-300'">
                    <p x-show="error" x-text="error" class="mt-1.5 text-xs text-red-500"></p>
                    <p x-show="!error" class="mt-1 text-xs text-gray-400">
                        Tối đa: <span class="font-medium text-gray-600" x-text="fmtMoney(conNo)"></span>
                    </p>
                    <div class="flex gap-2 mt-2">
                        <button type="button" @click="soTien = conNo; validate()" :disabled="loading"
                                class="flex-1 py-1.5 text-xs font-medium rounded-lg border border-gray-200
                                       text-gray-600 hover:bg-gray-50 disabled:opacity-50 transition-colors">
                            Toàn bộ nợ
                        </button>
                        <button type="button" @click="soTien = Math.floor(conNo / 2); validate()" :disabled="loading"
                                class="flex-1 py-1.5 text-xs font-medium rounded-lg border border-gray-200
                                       text-gray-600 hover:bg-gray-50 disabled:opacity-50 transition-colors">
                            Nửa số nợ
                        </button>
                    </div>
                </div>

                {{-- Form ẩn gửi lên server --}}
                <form x-ref="payForm" method="POST" action="" class="hidden">
                    @csrf
                    <input type="hidden" name="so_tien" :value="soTien">
                </form>
            </div>

            {{-- Footer actions (luôn visible, không cuộn) --}}
            <div class="shrink-0 flex gap-3 px-5 py-4 border-t border-gray-100 bg-white">
                <button type="button" @click="showModal = false"
                        :disabled="loading"
                        class="flex-1 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-xl
                               hover:bg-gray-50 disabled:opacity-50 transition-colors">
                    Hủy
                </button>
                <button type="button" @click="submit()"
                        :disabled="loading || !!error"
                        :style="phuongThuc === 'momo'
                            ? 'background: linear-gradient(135deg, #a50064, #d82d8b);'
                            : ''"
                        :class="phuongThuc !== 'momo' ? 'bg-blue-600 hover:bg-blue-700' : ''"
                        class="flex-1 py-2.5 text-sm font-semibold rounded-xl text-white
                               focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-400
                               disabled:opacity-60 disabled:cursor-not-allowed transition-all">
                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Thanh toán ngay
                    </span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Đang tạo giao dịch...
                    </span>
                </button>
            </div>
        </div>
    </div>
    </template>

</div>
@endsection
