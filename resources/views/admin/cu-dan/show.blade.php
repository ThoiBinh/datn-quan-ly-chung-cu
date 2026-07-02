@extends('layouts.admin')
@section('title', 'Chi tiết cư dân')
@section('page-title', 'Chi tiết cư dân')

@section('content')
@php
    $ttLabel = $cuDan->trang_thai_label;
    $diaChi  = collect([$cuDan->dia_chi, $cuDan->xa, $cuDan->tinh])->filter()->implode(', ');
@endphp

<div class="max-w-5xl space-y-5"
     x-data="{
         confirmToggle: false,
         confirmDelete: false,
         toggleAction: '{{ route('admin.cu-dan.toggle-status', $cuDan) }}',
         deleteAction: '{{ route('admin.cu-dan.destroy', $cuDan) }}'
     }">

    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-5">
            @if($cuDan->avatar_url)
            <img src="{{ $cuDan->avatar_url }}" alt="" class="w-16 h-16 rounded-2xl object-cover flex-shrink-0">
            @else
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-xl font-bold text-emerald-700 dark:text-emerald-400 flex-shrink-0">
                {{ mb_strtoupper(mb_substr($cuDan->ho_ten, 0, 1)) }}
            </div>
            @endif
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $cuDan->ho_ten }}</h2>
                    <span class="text-xs text-gray-400 dark:text-slate-500">{{ $cuDan->gioi_tinh_label }}</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400">{{ $cuDan->email ?: 'Chưa có email' }}</p>
                @if($diaChi)
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">{{ $diaChi }}</p>
                @endif
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold flex-shrink-0 {{ $ttLabel['class'] }}">
                <span class="w-2 h-2 rounded-full bg-current"></span> {{ $ttLabel['text'] }}
            </span>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex flex-wrap gap-3">
            <a href="{{ route('admin.cu-dan.edit', $cuDan) }}"
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <button type="button" @click="confirmToggle = true"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $cuDan->trang_thai == 1 ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-emerald-500 hover:bg-emerald-600 text-white' }}">
                @if($cuDan->trang_thai == 1)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Đánh dấu đã chuyển đi
                @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                Đánh dấu đang cư trú
                @endif
            </button>
            <button type="button" @click="confirmDelete = true"
                    class="flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Xóa
            </button>
            <a href="{{ route('admin.cu-dan.index') }}"
               class="ml-auto flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    {{-- CARD 1: Thông tin cá nhân --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Thông tin cá nhân</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
            $fields = [
                ['label' => 'Họ tên đệm', 'value' => $cuDan->ho_ten_dem],
                ['label' => 'Tên', 'value' => $cuDan->ten],
                ['label' => 'CCCD', 'value' => $cuDan->cccd, 'mono' => true],
                ['label' => 'Giới tính', 'value' => $cuDan->gioi_tinh_label],
                ['label' => 'Ngày sinh', 'value' => $cuDan->ngay_sinh?->format('d/m/Y')],
                ['label' => 'Email', 'value' => $cuDan->email],
                ['label' => 'Số điện thoại', 'value' => $cuDan->sdt],
                ['label' => 'Tỉnh / Thành phố', 'value' => $cuDan->tinh],
                ['label' => 'Xã / Phường', 'value' => $cuDan->xa],
                ['label' => 'Địa chỉ chi tiết', 'value' => $cuDan->dia_chi],
                ['label' => 'Ngày tạo', 'value' => $cuDan->created_at?->format('d/m/Y H:i')],
            ];
            @endphp
            @foreach($fields as $f)
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">{{ $f['label'] }}</dt>
                <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200 {{ ($f['mono'] ?? false) ? 'font-mono' : '' }}">
                    {{ $f['value'] ?: '—' }}
                </dd>
            </div>
            @endforeach
        </div>
    </div>

    {{-- CARD 2: Thông tin tài khoản --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Thông tin tài khoản</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">Email đăng nhập</dt>
                <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200">{{ $cuDan->email ?: '—' }}</dd>
            </div>
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">Vai trò hệ thống</dt>
                <dd>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                        Cư dân
                    </span>
                </dd>
            </div>
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">Trạng thái tài khoản</dt>
                <dd>
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold {{ $ttLabel['class'] }} px-2.5 py-0.5 rounded-full">
                        {{ $ttLabel['text'] }}
                    </span>
                </dd>
            </div>
        </div>
    </div>

    {{-- CARD 3: Danh sách căn hộ --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Danh sách căn hộ ({{ $cuDan->cu_dan_can_ho_count }})</h3>
        </div>
        @if($cuDan->cuDanCanHo->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Tòa nhà</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Căn hộ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Loại căn hộ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Vai trò</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Ngày đến</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Ngày đi</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($cuDan->cuDanCanHo as $cdch)
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40">
                        <td class="px-6 py-3 font-medium text-gray-800 dark:text-slate-200">{{ $cdch->canHo?->toaNha?->ten_toa_nha ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold text-emerald-600 dark:text-emerald-400">{{ $cdch->canHo?->so_can_ho ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $cdch->canHo?->loaiCanHo?->ten_loai_can_ho ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                {{ $cdch->vaiTro?->vai_tro ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300 text-xs">{{ $cdch->ngay_chuyen_den?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300 text-xs">{{ $cdch->ngay_chuyen_di?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($cdch->trang_thai == 1)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">Đang ở</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400">Đã chuyển đi</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-10 text-center text-gray-400 dark:text-slate-500 text-sm">Chưa được phân công căn hộ nào</div>
        @endif
    </div>

    {{-- CARD 4: Thuộc tính căn hộ --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Thuộc tính căn hộ</h3>
        </div>
        @php
            $coThuocTinh = $cuDan->cuDanCanHo->contains(fn($c) => $c->canHo?->thuocTinhCanHo->isNotEmpty());
        @endphp
        @if($coThuocTinh)
        <div class="p-6 space-y-5">
            @foreach($cuDan->cuDanCanHo as $cdch)
                @continue(!$cdch->canHo || $cdch->canHo->thuocTinhCanHo->isEmpty())
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wide mb-2">
                        {{ $cdch->canHo->toaNha?->ten_toa_nha }} — Căn hộ {{ $cdch->canHo->so_can_ho }}
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($cdch->canHo->thuocTinhCanHo as $ttch)
                        <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-3.5 border border-gray-100 dark:border-slate-600 flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-slate-400">{{ $ttch->thuocTinh?->ten_thuoc_tinh ?? '—' }}</span>
                            <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ $ttch->gia_tri_thuoc_tinh ?? '—' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-10 text-center text-gray-400 dark:text-slate-500 text-sm">Chưa có dữ liệu thuộc tính căn hộ</div>
        @endif
    </div>

    {{-- CARD 5: Phương tiện --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Phương tiện ({{ $cuDan->phuongTien->count() }})</h3>
        </div>
        @if($cuDan->phuongTien->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Biển số</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Tên phương tiện</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Loại phương tiện</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($cuDan->phuongTien as $pt)
                    @php $ptLabel = $pt->trang_thai_label; @endphp
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40">
                        <td class="px-6 py-3 font-mono font-semibold text-gray-800 dark:text-slate-200">{{ $pt->bien_so }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $pt->ten_phuong_tien ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $pt->loaiPhuongTien?->ten_loai_phuong_tien ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $ptLabel['class'] }}">{{ $ptLabel['text'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-10 text-center text-gray-400 dark:text-slate-500 text-sm">Chưa có phương tiện</div>
        @endif
    </div>

    {{-- CARD 6: Hóa đơn --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Hóa đơn ({{ $cuDan->hoaDon->count() }})</h3>
        </div>
        @if($cuDan->hoaDon->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Mã hóa đơn</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Kỳ thu</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Tổng tiền</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Đã trả</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Còn nợ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($cuDan->hoaDon->sortByDesc('createdAt') as $hd)
                    @php $conNo = $hd->conNo(); @endphp
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40">
                        <td class="px-6 py-3 font-mono text-xs text-gray-700 dark:text-slate-300">{{ $hd->ma_thanh_toan }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">T{{ $hd->thang }}/{{ $hd->nam }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-slate-200">{{ number_format($hd->tong_tien) }}đ</td>
                        <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400">{{ number_format($hd->so_tien_da_thanh_toan) }}đ</td>
                        <td class="px-4 py-3 text-right {{ $conNo > 0 ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-400' }}">{{ number_format($conNo) }}đ</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ match((int)$hd->trang_thai) {
                                    2 => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                    3 => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                                    4 => 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400',
                                    default => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                } }}">
                                {{ $hd->trang_thai_label }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-10 text-center text-gray-400 dark:text-slate-500 text-sm">Chưa có hóa đơn</div>
        @endif
    </div>

    {{-- CARD 7: Lịch sử thanh toán --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Lịch sử thanh toán ({{ $cuDan->lichSuThanhToan->count() }})</h3>
        </div>
        @if($cuDan->lichSuThanhToan->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Mã giao dịch</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Hóa đơn</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Số tiền</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Phương thức</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Ngày thanh toán</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($cuDan->lichSuThanhToan->sortByDesc('ngay_thanh_toan') as $ls)
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40">
                        <td class="px-6 py-3 font-mono text-xs text-gray-500 dark:text-slate-400">{{ $ls->ma_giao_dich ?? '—' }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            {{ $ls->hoaDon?->ma_thanh_toan ?? '—' }}
                            <span class="text-gray-400 ml-1">· {{ $ls->hoaDon?->canHo?->so_can_ho }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($ls->so_tien) }}đ</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $ls->phuong_thuc_thanh_toan ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-slate-300 text-xs whitespace-nowrap">{{ $ls->ngay_thanh_toan?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-10 text-center text-gray-400 dark:text-slate-500 text-sm">Chưa có lịch sử thanh toán</div>
        @endif
    </div>

    {{-- CARD 8: Yêu cầu cư dân --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Yêu cầu cư dân ({{ $cuDan->yeu_cau_count }})</h3>
        </div>
        @if($cuDan->yeuCau->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Loại yêu cầu</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Nội dung</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Trạng thái</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Nhân viên xử lý</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($cuDan->yeuCau->sortByDesc('createdAt') as $yc)
                    @php $ycLabel = $yc->trang_thai_label; @endphp
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40">
                        <td class="px-6 py-3 text-gray-700 dark:text-slate-300">{{ $yc->loaiYeuCau?->name ?? '—' }}</td>
                        <td class="px-4 py-3 max-w-sm">
                            <p class="font-medium text-gray-800 dark:text-slate-200 truncate">{{ $yc->tieu_de }}</p>
                            @if($yc->noi_dung)
                            <p class="text-xs text-gray-400 dark:text-slate-500 truncate">{{ $yc->noi_dung }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $ycLabel['class'] }}">{{ $ycLabel['text'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $yc->nhanVienXuLy?->ho_ten ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-10 text-center text-gray-400 dark:text-slate-500 text-sm">Chưa có yêu cầu nào</div>
        @endif
    </div>

    {{-- CARD 9: Thông báo đã đọc --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Thông báo đã đọc ({{ $cuDan->thongBaoDaDoc->count() }})</h3>
        </div>
        @if($cuDan->thongBaoDaDoc->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Tiêu đề</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Thời gian đọc</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($cuDan->thongBaoDaDoc->sortByDesc('read_at') as $tbdd)
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40">
                        <td class="px-6 py-3 font-medium text-gray-800 dark:text-slate-200">{{ $tbdd->thongBao?->tieu_de ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400 dark:text-slate-500 whitespace-nowrap">{{ $tbdd->read_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-10 text-center text-gray-400 dark:text-slate-500 text-sm">Chưa có thông báo nào được đọc</div>
        @endif
    </div>

    <!-- Modal xác nhận toggle -->
    <template x-teleport="body">
    <div x-show="confirmToggle"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="confirmToggle = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden" @click.stop>
            <div class="flex justify-end px-4 pt-4">
                <button @click="confirmToggle = false" type="button"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 pt-2 pb-5 text-center">
                @if($cuDan->trang_thai == 1)
                <div class="bg-amber-100 dark:bg-amber-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Đánh dấu đã chuyển đi</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Cư dân sẽ không thể đăng nhập cho tới khi được đặt lại trạng thái đang cư trú.</p>
                @else
                <div class="bg-emerald-100 dark:bg-emerald-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Đánh dấu đang cư trú</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Cư dân sẽ được kích hoạt lại, có thể đăng nhập trở lại.</p>
                @endif
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button @click="confirmToggle = false" type="button"
                        class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy bỏ
                </button>
                <form :action="toggleAction" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit" class="block w-full py-2.5 text-sm font-semibold rounded-xl transition-colors {{ $cuDan->trang_thai == 1 ? 'bg-amber-500 hover:bg-amber-600 text-gray-900' : 'bg-emerald-500 hover:bg-emerald-600 text-white' }}">
                        Xác nhận
                    </button>
                </form>
            </div>
        </div>
    </div>
    </template>

    <!-- Modal xác nhận xóa -->
    <template x-teleport="body">
    <div x-show="confirmDelete"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="confirmDelete = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden" @click.stop>
            <div class="flex justify-end px-4 pt-4">
                <button @click="confirmDelete = false" type="button"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 pt-2 pb-5 text-center">
                <div class="bg-red-100 dark:bg-red-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Xóa cư dân</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                    Nếu cư dân đã phát sinh dữ liệu (hóa đơn, thanh toán, phương tiện, yêu cầu), thao tác sẽ bị từ chối.
                </p>
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button @click="confirmDelete = false" type="button"
                        class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy bỏ
                </button>
                <form :action="deleteAction" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="block w-full py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection
