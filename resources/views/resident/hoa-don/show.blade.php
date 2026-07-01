@extends('layouts.resident')
@section('title', 'Chi tiết hóa đơn')
@section('page-title', 'Chi tiết hóa đơn')

@section('content')
@php
    $isCanTT = in_array($hoaDon->trang_thai, [1, 3]) && $conNo > 0;
@endphp

<div class="max-w-2xl space-y-5"
     x-data="{
         showModal: false,
         loading: false,
         phuongThuc: 'momo',
         soTien: {{ (int) $conNo }},
         conNo: {{ (float) $conNo }},
         tongTien: {{ (float) $hoaDon->tong_tien }},
         daTT: {{ (float) $hoaDon->so_tien_da_thanh_toan }},
         error: '',

         momoUrl: '{{ route('resident.hoa-don.momo', $hoaDon) }}',
         vnpayUrl: '{{ route('resident.hoa-don.vnpay', $hoaDon) }}',

         validate() {
             if (!this.soTien || this.soTien <= 0) { this.error = 'Số tiền phải lớn hơn 0.'; return false; }
             if (this.soTien > this.conNo) { this.error = 'Số tiền không được vượt quá ' + this.fmtMoney(this.conNo) + '.'; return false; }
             this.error = '';
             return true;
         },
         submit() {
             if (!this.validate()) return;
             this.loading = true;
             this.$refs.payForm.action = this.phuongThuc === 'momo' ? this.momoUrl : this.vnpayUrl;
             this.$refs.payForm.submit();
         },
         fmtMoney(n) { return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + 'đ'; }
     }">

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3 text-emerald-700 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-red-700 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-gray-900">Hóa đơn tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }}</h2>
                <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $hoaDon->ma_thanh_toan }}</p>
            </div>
            @php
                $badge = match($hoaDon->trang_thai) {
                    1 => ['bg-amber-100 text-amber-700', 'Chưa thanh toán'],
                    2 => ['bg-emerald-100 text-emerald-700', 'Đã thanh toán'],
                    3 => ['bg-red-100 text-red-700', 'Quá hạn'],
                    4 => ['bg-gray-100 text-gray-500', 'Đã hủy'],
                    default => ['bg-gray-100 text-gray-500', 'N/A'],
                };
            @endphp
            <span class="shrink-0 px-3 py-1 rounded-full text-xs font-semibold {{ $badge[0] }}">{{ $badge[1] }}</span>
        </div>

        {{-- Summary bar --}}
        <div class="grid grid-cols-3 divide-x divide-gray-100 bg-gray-50/60">
            <div class="px-4 py-3 text-center">
                <p class="text-xs text-gray-400 mb-0.5">Tổng tiền</p>
                <p class="font-bold text-gray-900 text-sm tabular-nums">{{ number_format($hoaDon->tong_tien, 0, ',', '.') }}đ</p>
            </div>
            <div class="px-4 py-3 text-center">
                <p class="text-xs text-gray-400 mb-0.5">Đã thanh toán</p>
                <p class="font-bold text-emerald-600 text-sm tabular-nums">{{ number_format($hoaDon->so_tien_da_thanh_toan, 0, ',', '.') }}đ</p>
            </div>
            <div class="px-4 py-3 text-center">
                <p class="text-xs text-gray-400 mb-0.5">Còn phải trả</p>
                <p class="font-bold {{ $conNo > 0 ? 'text-red-500' : 'text-gray-400' }} text-sm tabular-nums">
                    {{ $conNo > 0 ? number_format($conNo, 0, ',', '.') . 'đ' : '—' }}
                </p>
            </div>
        </div>

        {{-- Chi tiết khoản thu --}}
        <div class="p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Chi tiết khoản thu</p>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left pb-2 text-gray-500 font-medium text-xs">Khoản phí</th>
                        <th class="text-right pb-2 text-gray-500 font-medium text-xs">Đơn giá</th>
                        <th class="text-right pb-2 text-gray-500 font-medium text-xs">SL</th>
                        <th class="text-right pb-2 text-gray-500 font-medium text-xs">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($hoaDon->chiTiet as $ct)
                    <tr>
                        <td class="py-2.5 text-gray-700">{{ $ct->ten_phi_dich_vu ?? 'Khoản phí' }}</td>
                        <td class="py-2.5 text-right text-gray-500 tabular-nums">{{ number_format($ct->don_gia, 0, ',', '.') }}đ</td>
                        <td class="py-2.5 text-right text-gray-500 tabular-nums">{{ $ct->so_luong }}</td>
                        <td class="py-2.5 text-right font-semibold text-gray-800 tabular-nums">{{ number_format($ct->thanh_tien, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200">
                        <td colspan="3" class="pt-3 font-bold text-gray-900">Tổng cộng</td>
                        <td class="pt-3 text-right font-bold text-base text-gray-900 tabular-nums">{{ number_format($hoaDon->tong_tien, 0, ',', '.') }}đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($hoaDon->han_thanh_toan)
        <div class="px-5 pb-4 text-xs text-gray-400">
            Hạn thanh toán: <strong class="text-gray-600">{{ $hoaDon->han_thanh_toan->format('d/m/Y') }}</strong>
        </div>
        @endif
    </div>

    {{-- Nút thanh toán --}}
    @if($isCanTT)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-gray-800">Còn phải thanh toán</p>
                <p class="text-xl font-bold text-red-500 tabular-nums mt-0.5">{{ number_format($conNo, 0, ',', '.') }}đ</p>
            </div>
            <button @click="showModal = true; soTien = conNo; error = ''"
                    type="button"
                    class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                           bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Thanh toán ngay
            </button>
        </div>
    </div>
    @endif

    {{-- Lịch sử thanh toán --}}
    @if($hoaDon->lichSuThanhToan->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="font-semibold text-gray-800 text-sm">Lịch sử thanh toán</p>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($hoaDon->lichSuThanhToan->sortByDesc('ngay_thanh_toan') as $ls)
            <div class="flex items-center justify-between px-5 py-3.5 text-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">{{ $ls->phuong_thuc_thanh_toan }}</span>
                        @if($ls->nguonTao)
                        <span class="px-1.5 py-0.5 text-xs bg-gray-100 text-gray-500 rounded-md">
                            {{ $ls->nguonTao->ten_nguon_tao ?? '' }}
                        </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $ls->ngay_thanh_toan }}</p>
                    @if($ls->ma_giao_dich)
                    <p class="text-xs text-gray-300 font-mono mt-0.5">{{ $ls->ma_giao_dich }}</p>
                    @endif
                </div>
                <p class="font-bold text-emerald-600 tabular-nums">{{ number_format($ls->so_tien, 0, ',', '.') }}đ</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <a href="{{ route('resident.hoa-don.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Quay lại danh sách
    </a>

    {{-- Modal thanh toán --}}
    @if($isCanTT)
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
                <div>
                    <h3 class="text-base font-bold text-gray-900">Thanh toán hóa đơn</h3>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $hoaDon->ma_thanh_toan }}</p>
                </div>
                <button @click="showModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Content (cuộn được) --}}
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">

                {{-- Summary --}}
                <div class="bg-gray-50 rounded-xl px-4 py-3 grid grid-cols-3 gap-2 text-center">
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

                {{-- Chọn phương thức --}}
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Phương thức thanh toán</p>
                    <div class="grid grid-cols-2 gap-3">
                        {{-- MoMo --}}
                        <button type="button" @click="phuongThuc = 'momo'"
                                :class="phuongThuc === 'momo' ? 'ring-2 ring-pink-300' : 'border-gray-200 hover:border-pink-200'"
                                :style="phuongThuc === 'momo' ? 'border-color:#a50064; background:#fdf2f8;' : 'background:#fff;'"
                                class="relative flex flex-col items-center justify-center gap-1.5 rounded-xl border-2 px-3 py-3.5 transition-all cursor-pointer">
                            {{-- Icon dùng style inline để chắc chắn có màu --}}
                            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                                 style="background: linear-gradient(135deg, #a50064, #d82d8b)">
                                <span class="text-white font-black text-sm leading-none">M</span>
                            </div>
                            <span class="text-sm font-semibold"
                                  :style="phuongThuc === 'momo' ? 'color:#a50064' : 'color:#374151'">MoMo</span>
                            {{-- Checkmark --}}
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

                {{-- Hidden form --}}
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
    @endif

</div>
@endsection
