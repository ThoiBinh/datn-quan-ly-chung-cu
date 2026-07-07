@extends('layouts.resident')
@section('title', 'Chi tiết đặt lịch')
@section('page-title', 'Chi tiết đặt lịch')

@section('content')
@php
    $trangThai = (int) $datLichTienIch->trang_thai;
    $CHO_DUYET = \App\Models\DatLichTienIch::TRANG_THAI_CHO_DUYET;
    $DA_DUYET = \App\Models\DatLichTienIch::TRANG_THAI_DA_DUYET;
    $conThoiGianHuy = now()->addHours(2)->lte($datLichTienIch->thoi_gian_bat_dau);
    $coTheHuy = in_array($trangThai, [$CHO_DUYET, $DA_DUYET], true) && $conThoiGianHuy;
    $khongTheHuyDoQuaHan = in_array($trangThai, [$CHO_DUYET, $DA_DUYET], true) && !$conThoiGianHuy;

    $phut = $datLichTienIch->thoi_luong_phut;
    $thoiLuong = null;
    if ($phut !== null) {
        $gio = intdiv($phut, 60);
        $conLai = $phut % 60;
        $thoiLuong = trim(($gio > 0 ? "{$gio} giờ " : '') . ($conLai > 0 ? "{$conLai} phút" : ($gio > 0 ? '' : '0 phút')));
    }
@endphp
<div class="max-w-3xl mx-auto space-y-5" x-data="{ showCancel: false }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
            <a href="{{ route('resident.dat-lich-tien-ich.index') }}" class="hover:text-emerald-600 transition-colors">Lịch sử đặt tiện ích</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="font-mono font-semibold text-gray-700 dark:text-slate-200">{{ $datLichTienIch->ma_dat_lich }}</span>
        </nav>

        @if($coTheHuy)
        <button @click="showCancel = true" type="button"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors bg-orange-50 text-orange-700 hover:bg-orange-100 border border-orange-200 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            Hủy lịch
        </button>
        @endif
    </div>

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <ul class="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($khongTheHuyDoQuaHan)
    <div class="flex items-start gap-2.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 px-4 py-3">
        <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">Đã quá thời hạn hủy lịch (chỉ được hủy khi còn cách giờ sử dụng ít nhất 2 giờ).</p>
    </div>
    @endif

    <!-- Thông tin đặt lịch -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thông tin đặt lịch</h2>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $datLichTienIch->trang_thai_label['class'] }}">
                {{ $datLichTienIch->trang_thai_label['text'] }}
            </span>
        </div>
        <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Mã đặt lịch</dt>
                <dd class="font-mono font-bold text-lg text-emerald-600 dark:text-emerald-400">{{ $datLichTienIch->ma_dat_lich }}</dd>
            </div>
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Tiện ích</dt>
                <dd class="text-sm text-gray-800 dark:text-slate-100">
                    {{ $datLichTienIch->tienIch?->ten_tien_ich ?? '—' }}
                    @if($datLichTienIch->tienIch?->loaiTienIch)
                    <span class="ml-1 text-xs text-gray-400 dark:text-slate-500">({{ $datLichTienIch->tienIch->loaiTienIch->ten_loai_tien_ich }})</span>
                    @endif
                </dd>
            </div>
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Căn hộ</dt>
                <dd class="text-sm text-gray-800 dark:text-slate-100">
                    {{ $datLichTienIch->canHo?->so_can_ho ?? '—' }}
                    @if($datLichTienIch->canHo?->toaNha)
                    <span class="ml-1 text-xs text-gray-400 dark:text-slate-500">({{ $datLichTienIch->canHo->toaNha->ten_toa_nha }})</span>
                    @endif
                </dd>
            </div>
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Thời gian bắt đầu</dt>
                <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->thoi_gian_bat_dau?->format('H:i, d/m/Y') }}</dd>
            </div>
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Thời gian kết thúc</dt>
                <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->thoi_gian_ket_thuc?->format('H:i, d/m/Y') }}</dd>
            </div>
            @if($thoiLuong)
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Thời lượng sử dụng</dt>
                <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $thoiLuong }}</dd>
            </div>
            @endif
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Số người</dt>
                <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->so_nguoi }}</dd>
            </div>
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Phí sử dụng</dt>
                <dd class="text-sm font-semibold text-gray-800 dark:text-slate-100">{{ number_format((float) $datLichTienIch->phi_su_dung, 0, ',', '.') }} đ</dd>
            </div>
            @if($datLichTienIch->ghi_chu)
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Ghi chú</dt>
                <dd class="text-sm text-gray-800 dark:text-slate-100 whitespace-pre-line">{{ $datLichTienIch->ghi_chu }}</dd>
            </div>
            @endif
            @if($datLichTienIch->ngay_huy)
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Ngày hủy / từ chối</dt>
                <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->ngay_huy->format('d/m/Y H:i') }}</dd>
            </div>
            @endif
            @if($datLichTienIch->ly_do_huy)
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Lý do hủy / từ chối</dt>
                <dd class="text-sm text-red-600 dark:text-red-400 whitespace-pre-line">{{ $datLichTienIch->ly_do_huy }}</dd>
            </div>
            @endif
            <div class="flex items-baseline px-5 py-3.5">
                <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Ngày đặt</dt>
                <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->createdAt?->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>
    </div>

    <!-- Modal xác nhận hủy -->
    <template x-teleport="body">
    <div x-show="showCancel"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="showCancel = false" @keydown.escape.window="showCancel = false" x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-5">
                <div class="w-14 h-14 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <p class="font-semibold text-gray-800 dark:text-white">Hủy lịch đặt tiện ích?</p>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Mã <span class="font-medium text-gray-700 dark:text-slate-200">«{{ $datLichTienIch->ma_dat_lich }}»</span> sẽ chuyển sang trạng thái Đã hủy và không thể hoàn tác.</p>
            </div>
            <form action="{{ route('resident.dat-lich-tien-ich.huy', $datLichTienIch) }}" method="POST">
                @csrf @method('PATCH')
                <div class="flex gap-3">
                    <button @click="showCancel = false" type="button" class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">Đóng</button>
                    <button type="submit" class="flex-1 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl transition-colors">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
