@extends('layouts.manager')
@section('title', $cuDan->ho_ten)
@section('page-title', 'Chi tiết cư dân')

@section('content')
@php
    $allPhuongTien = $cuDan->cuDanCanHo->flatMap(fn($c) => $c->canHo?->phuongTien ?? collect())->unique('id');
    $allHoaDon     = $cuDan->cuDanCanHo->flatMap(fn($c) => $c->canHo?->hoaDon ?? collect())->unique('id');
@endphp

{{-- Header --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        {{-- Avatar --}}
        <div class="flex-shrink-0">
            @if($cuDan->avatar_url)
                <img src="{{ $cuDan->avatar_url }}" alt="{{ $cuDan->ho_ten }}"
                     class="w-20 h-20 rounded-2xl object-cover ring-4 ring-indigo-100 dark:ring-indigo-900">
            @else
                <div class="w-20 h-20 rounded-2xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                    <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-300">
                        {{ strtoupper(mb_substr($cuDan->ten, 0, 1)) }}
                    </span>
                </div>
            @endif
        </div>
        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $cuDan->ho_ten }}</h1>
            <div class="flex flex-wrap items-center gap-3 mt-2">
                @php $ttLabel = $cuDan->trang_thai_label; @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ttLabel['class'] }}">
                    {{ $ttLabel['text'] }}
                </span>
                @if($cuDan->email)
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $cuDan->email }}</span>
                @endif
                @if($cuDan->sdt)
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $cuDan->sdt }}</span>
                @endif
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                Mã cư dân: #{{ $cuDan->id }} · Tạo ngày {{ $cuDan->created_at?->format('d/m/Y') ?? '—' }}
            </p>
        </div>
        {{-- Actions --}}
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('manager.cu-dan.edit', $cuDan) }}"
               class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('manager.cu-dan.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Quay lại
            </a>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div x-data="{ tab: 'thongtin' }">
    {{-- Tab Nav --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-4 overflow-x-auto">
        <div class="flex min-w-max border-b border-gray-200 dark:border-gray-700">
            @php
                $tabs = [
                    ['key' => 'thongtin',   'label' => 'Thông tin'],
                    ['key' => 'taikhoan',   'label' => 'Tài khoản'],
                    ['key' => 'canho',      'label' => 'Căn hộ (' . $cuDan->cuDanCanHo->count() . ')'],
                    ['key' => 'phuongtien', 'label' => 'Phương tiện (' . $allPhuongTien->count() . ')'],
                    ['key' => 'hoadon',     'label' => 'Hóa đơn (' . $allHoaDon->count() . ')'],
                    ['key' => 'thanhtoan',  'label' => 'Thanh toán (' . $cuDan->lichSuThanhToan->count() . ')'],
                    ['key' => 'yeucau',     'label' => 'Yêu cầu (' . $cuDan->yeuCau->count() . ')'],
                    ['key' => 'thongbao',   'label' => 'Thông báo (' . $cuDan->thongBaoDaDoc->count() . ')'],
                ];
            @endphp
            @foreach($tabs as $t)
            <button @click="tab = '{{ $t['key'] }}'"
                    :class="tab === '{{ $t['key'] }}' ? 'border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                    class="px-5 py-3 text-sm whitespace-nowrap transition-colors">
                {{ $t['label'] }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Tab: Thông tin cá nhân --}}
    <div x-show="tab === 'thongtin'" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Thông tin cá nhân</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @php
                    $fields = [
                        ['label' => 'Họ và tên',    'value' => $cuDan->ho_ten],
                        ['label' => 'CCCD/CMND',    'value' => $cuDan->cccd, 'mono' => true],
                        ['label' => 'Giới tính',    'value' => $cuDan->gioi_tinh_label],
                        ['label' => 'Ngày sinh',    'value' => $cuDan->ngay_sinh?->format('d/m/Y')],
                        ['label' => 'Số điện thoại','value' => $cuDan->sdt],
                        ['label' => 'Email',        'value' => $cuDan->email],
                        ['label' => 'Tỉnh/Thành',  'value' => $cuDan->tinh],
                        ['label' => 'Xã/Phường',   'value' => $cuDan->xa],
                        ['label' => 'Địa chỉ',     'value' => $cuDan->dia_chi, 'span2' => true],
                    ];
                @endphp
                @foreach($fields as $field)
                <div class="{{ ($field['span2'] ?? false) ? 'sm:col-span-2' : '' }}">
                    <dt class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">{{ $field['label'] }}</dt>
                    <dd class="mt-1 text-sm {{ ($field['mono'] ?? false) ? 'font-mono' : 'font-medium' }} text-gray-800 dark:text-gray-200">
                        {{ $field['value'] ?? '—' }}
                    </dd>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tab: Tài khoản --}}
    <div x-show="tab === 'taikhoan'" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Thông tin tài khoản</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Email đăng nhập</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $cuDan->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Vai trò hệ thống</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                            Cư dân
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Trạng thái tài khoản</dt>
                    <dd class="mt-1">
                        @php $ttLabel = $cuDan->trang_thai_label; @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ttLabel['class'] }}">
                            {{ $ttLabel['text'] }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Ngày tạo tài khoản</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ $cuDan->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Cập nhật lần cuối</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ isset($cuDan->attributes['updatedAt']) ? \Carbon\Carbon::parse($cuDan->attributes['updatedAt'])->format('d/m/Y H:i') : '—' }}
                    </dd>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab: Căn hộ --}}
    <div x-show="tab === 'canho'" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Lịch sử căn hộ</h3>
            </div>
            @if($cuDan->cuDanCanHo->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tòa nhà</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Căn hộ</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Loại căn hộ</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Vai trò</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ngày đến</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ngày đi</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($cuDan->cuDanCanHo as $cdch)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">
                                {{ $cdch->canHo?->toaNha?->ten_toa_nha ?? '—' }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-indigo-600 dark:text-indigo-400">
                                {{ $cdch->canHo?->so_can_ho ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                {{ $cdch->canHo?->loaiCanHo?->ten_loai_can_ho ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                    {{ $cdch->vaiTro?->vai_tro ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs">
                                {{ $cdch->ngay_chuyen_den?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs">
                                {{ $cdch->ngay_chuyen_di?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($cdch->trang_thai == 1)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">Đang ở</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">Đã chuyển đi</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm">Chưa có lịch sử căn hộ</div>
            @endif
        </div>
    </div>

    {{-- Tab: Phương tiện --}}
    <div x-show="tab === 'phuongtien'" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Phương tiện</h3>
            </div>
            @if($allPhuongTien->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Biển số</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tên phương tiện</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Loại phương tiện</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Căn hộ</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ngày đăng ký</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($allPhuongTien as $pt)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-3 font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $pt->bien_so }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $pt->ten_phuong_tien ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $pt->loaiPhuongTien?->ten_loai_phuong_tien ?? '—' }}</td>
                            <td class="px-4 py-3 text-indigo-600 dark:text-indigo-400 font-medium">{{ $pt->canHo?->so_can_ho ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs">{{ $pt->ngay_dang_ky?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($pt->trang_thai == 1)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">Hoạt động</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">Đã hủy</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm">Chưa có phương tiện</div>
            @endif
        </div>
    </div>

    {{-- Tab: Hóa đơn --}}
    <div x-show="tab === 'hoadon'" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Hóa đơn căn hộ</h3>
            </div>
            @if($allHoaDon->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mã hóa đơn</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Kỳ thu</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tổng tiền</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Đã trả</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Còn nợ</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($allHoaDon->sortByDesc('createdAt') as $hd)
                        @php $conNo = $hd->conNo(); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $hd->ma_thanh_toan }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">T{{ $hd->thang }}/{{ $hd->nam }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($hd->tong_tien) }}đ</td>
                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400">{{ number_format($hd->so_tien_da_thanh_toan) }}đ</td>
                            <td class="px-4 py-3 text-right {{ $conNo > 0 ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-400' }}">
                                {{ number_format($conNo) }}đ
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $hdColors = [
                                        1 => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                        2 => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                        3 => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                                        4 => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $hdColors[$hd->trang_thai] ?? $hdColors[4] }}">
                                    {{ $hd->trang_thai_label }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm">Chưa có hóa đơn</div>
            @endif
        </div>
    </div>

    {{-- Tab: Lịch sử thanh toán --}}
    <div x-show="tab === 'thanhtoan'" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Lịch sử thanh toán</h3>
            </div>
            @if($cuDan->lichSuThanhToan->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ngày thanh toán</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Hóa đơn</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Số tiền</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Phương thức</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mã giao dịch</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($cuDan->lichSuThanhToan->sortByDesc('ngay_thanh_toan') as $ls)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-3 text-gray-700 dark:text-gray-300 text-xs whitespace-nowrap">
                                {{ $ls->ngay_thanh_toan?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                                {{ $ls->hoaDon?->ma_thanh_toan ?? '—' }}
                                <span class="text-gray-400 ml-1">· {{ $ls->hoaDon?->canHo?->so_can_ho }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($ls->so_tien) }}đ
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $ls->phuong_thuc_thanh_toan ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $ls->ma_giao_dich ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $ls->ghi_chu ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm">Chưa có lịch sử thanh toán</div>
            @endif
        </div>
    </div>

    {{-- Tab: Yêu cầu --}}
    <div x-show="tab === 'yeucau'" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Yêu cầu cư dân</h3>
            </div>
            @if($cuDan->yeuCau->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Loại yêu cầu</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tiêu đề</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mức độ</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Người xử lý</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ngày gửi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($cuDan->yeuCau->sortByDesc('createdAt') as $yc)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-3 text-gray-700 dark:text-gray-300">{{ $yc->loaiYeuCau?->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200 max-w-xs truncate">{{ $yc->tieu_de }}</td>
                            <td class="px-4 py-3">
                                @php $mdLabel = $yc->muc_do_label; @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $mdLabel['class'] }}">{{ $mdLabel['text'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @php $ycLabel = $yc->trang_thai_label; @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $ycLabel['class'] }}">{{ $ycLabel['text'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $yc->nhanVienXuLy?->ho_ten ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                {{ $yc->created_at?->format('d/m/Y') ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm">Chưa có yêu cầu nào</div>
            @endif
        </div>
    </div>

    {{-- Tab: Thông báo đã đọc --}}
    <div x-show="tab === 'thongbao'" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Thông báo đã đọc</h3>
            </div>
            @if($cuDan->thongBaoDaDoc->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tiêu đề thông báo</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Thời gian đọc</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($cuDan->thongBaoDaDoc->sortByDesc('read_at') as $tbdd)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">
                                {{ $tbdd->thongBao?->tieu_de ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                {{ $tbdd->read_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm">Chưa có thông báo nào được đọc</div>
            @endif
        </div>
    </div>
</div>
@endsection
