@extends('layouts.manager')
@section('title', 'Chi tiết căn hộ ' . $canHo->so_can_ho)
@section('page-title', 'Căn hộ ' . $canHo->so_can_ho)

@section('content')
<div x-data="{ tab: 'thongtin' }" class="space-y-5">

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $canHo->so_can_ho }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $canHo->toaNha?->ten_toa_nha ?? '–' }} · Tầng {{ $canHo->tang }} · {{ $canHo->loaiCanHo?->ten_loai_can_ho ?? '–' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @php
                    $ttLabel = $canHo->trangThai?->ten_trang_thai ?? '–';
                    $ttClass = match($ttLabel) {
                        'Đang sử dụng' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                        'Còn trống'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                        'Đang bảo trì' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                        default        => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $ttClass }}">{{ $ttLabel }}</span>
                <a href="{{ route('manager.can-ho.edit', $canHo) }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Chỉnh sửa
                </a>
                <a href="{{ route('manager.can-ho.index') }}"
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
            <nav class="flex gap-1 px-4 pt-3 min-w-max">
                @php
                    $tabs = [
                        'thongtin' => 'Thông tin chung',
                        'toa_nha'  => 'Tòa nhà',
                        'loai'     => 'Loại căn hộ',
                        'cu_dan'   => 'Cư dân (' . $canHo->cuDanHienTai->count() . ')',
                        'dich_vu'  => 'Dịch vụ (' . $canHo->phiDichVu->count() . ')',
                        'thuoc_tinh'=> 'Thuộc tính (' . $canHo->thuocTinh->count() . ')',
                        'xe'       => 'Phương tiện (' . $canHo->phuongTien->count() . ')',
                        'hoa_don'   => 'Hóa đơn (' . $canHo->hoaDon->count() . ')',
                        'thanh_toan'=> 'Lịch sử TT (' . $canHo->hoaDon->sum(fn($hd) => $hd->lichSuThanhToan->count()) . ')',
                        'lich_su'   => 'Lịch sử cư dân (' . $canHo->cuDanCanHo->count() . ')',
                    ];
                @endphp
                @foreach($tabs as $key => $label)
                <button @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}'
                            ? 'border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400 font-semibold'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border-b-2 border-transparent'"
                        class="pb-3 px-2 text-sm whitespace-nowrap transition-colors">
                    {{ $label }}
                </button>
                @endforeach
            </nav>
        </div>

        <div class="p-5">

            {{-- Tab: Thông tin chung --}}
            <div x-show="tab === 'thongtin'">
                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Số căn hộ</dt>
                        <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $canHo->so_can_ho }}</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Tầng</dt>
                        <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $canHo->tang }}</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Giá</dt>
                        <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ $canHo->gia ? number_format($canHo->gia) . ' đ' : '–' }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Trạng thái</dt>
                        <dd>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ttClass }}">
                                {{ $ttLabel }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Tòa nhà</dt>
                        <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $canHo->toaNha?->ten_toa_nha ?? '–' }}</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Loại căn hộ</dt>
                        <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $canHo->loaiCanHo?->ten_loai_can_ho ?? '–' }}</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Ngày tạo</dt>
                        <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $canHo->createdAt?->format('d/m/Y H:i') ?? '–' }}</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Cập nhật lần cuối</dt>
                        <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $canHo->updatedAt?->format('d/m/Y H:i') ?? '–' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Tab: Tòa nhà --}}
            <div x-show="tab === 'toa_nha'" x-cloak>
                @if($canHo->toaNha)
                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Tên tòa nhà</dt>
                        <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $canHo->toaNha->ten_toa_nha }}</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Địa chỉ</dt>
                        <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $canHo->toaNha->dia_chi ?? '–' }}</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Số tầng</dt>
                        <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $canHo->toaNha->so_tang }}</dd>
                    </div>
                </dl>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 py-6 text-center">Không có thông tin tòa nhà.</p>
                @endif
            </div>

            {{-- Tab: Loại căn hộ --}}
            <div x-show="tab === 'loai'" x-cloak>
                @if($canHo->loaiCanHo)
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-md">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Mã loại</dt>
                        <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $canHo->loaiCanHo->id }}</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Tên loại căn hộ</dt>
                        <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $canHo->loaiCanHo->ten_loai_can_ho }}</dd>
                    </div>
                </dl>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 py-6 text-center">Không có thông tin loại căn hộ.</p>
                @endif
            </div>

            {{-- Tab: Cư dân hiện tại --}}
            <div x-show="tab === 'cu_dan'" x-cloak>
                @if($canHo->cuDanHienTai->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Họ tên</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">CCCD</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">SĐT</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Vai trò</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ngày chuyển đến</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($canHo->cuDanHienTai as $cdch)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <a href="{{ route('manager.cu-dan.show', $cdch->cuDan) }}"
                                       class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $cdch->cuDan?->ho_ten ?? '–' }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $cdch->cuDan?->cccd ?? '–' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $cdch->cuDan?->sdt ?? '–' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                        {{ $cdch->vaiTro?->vai_tro ?? '–' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                    {{ $cdch->ngay_chuyen_den?->format('d/m/Y') ?? '–' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Căn hộ hiện chưa có cư dân.</p>
                @endif
            </div>

            {{-- Tab: Dịch vụ --}}
            <div x-show="tab === 'dich_vu'" x-cloak>
                @if($canHo->phiDichVu->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tên dịch vụ</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Loại phí</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Đơn vị tính</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Đơn giá gốc</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Đơn giá áp dụng</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($canHo->phiDichVu as $phi)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $phi->ten_phi_dich_vu }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $phi->loaiPhiDichVu?->ten_loai_phi_dich_vu ?? '–' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $phi->donViTinh?->don_vi ?? '–' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">{{ number_format($phi->don_gia) }}đ</td>
                                <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200 font-mono text-xs">{{ number_format($phi->pivot->don_gia) }}đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Chưa đăng ký phí dịch vụ.</p>
                @endif
            </div>

            {{-- Tab: Thuộc tính --}}
            <div x-show="tab === 'thuoc_tinh'" x-cloak>
                @if($canHo->thuocTinh->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Thuộc tính</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Giá trị</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Kiểu dữ liệu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($canHo->thuocTinh as $tt)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $tt->ten_thuoc_tinh }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $tt->pivot->gia_tri_thuoc_tinh ?? '–' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                                    @switch($tt->pivot->kieu_du_lieu)
                                        @case(1) Số nguyên @break
                                        @case(2) Văn bản @break
                                        @case(3) Ngày giờ @break
                                        @default – @endswitch
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Chưa có thuộc tính nào.</p>
                @endif
            </div>

            {{-- Tab: Phương tiện --}}
            <div x-show="tab === 'xe'" x-cloak>
                @if($canHo->phuongTien->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Biển số</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tên phương tiện</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Loại</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ngày đăng ký</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($canHo->phuongTien as $pt)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3 font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $pt->bien_so }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $pt->ten_phuong_tien ?? '–' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $pt->loaiPhuongTien?->ten_loai_phuong_tien ?? '–' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $pt->ngay_dang_ky?->format('d/m/Y') ?? '–' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $pt->trang_thai == 1 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ $pt->trang_thai == 1 ? 'Đang sử dụng' : 'Ngưng' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Chưa đăng ký phương tiện.</p>
                @endif
            </div>

            {{-- Tab: Hóa đơn --}}
            <div x-show="tab === 'hoa_don'" x-cloak>
                @if($canHo->hoaDon->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mã HĐ</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Kỳ</th>
                                <th class="text-right px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tổng tiền</th>
                                <th class="text-right px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Đã thanh toán</th>
                                <th class="text-right px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Còn nợ</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Hạn TT</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($canHo->hoaDon->sortByDesc('createdAt') as $hd)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $hd->ma_thanh_toan ?? '–' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">T{{ $hd->thang }}/{{ $hd->nam }}</td>
                                <td class="px-4 py-3 text-right font-mono text-xs text-gray-800 dark:text-gray-200">{{ number_format($hd->tong_tien) }}đ</td>
                                <td class="px-4 py-3 text-right font-mono text-xs text-emerald-600 dark:text-emerald-400">{{ number_format($hd->so_tien_da_thanh_toan) }}đ</td>
                                <td class="px-4 py-3 text-right font-mono text-xs font-semibold
                                    {{ $hd->conNo() > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ number_format($hd->conNo()) }}đ
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                    {{ $hd->han_thanh_toan?->format('d/m/Y') ?? '–' }}
                                </td>
                                
                                <td class="px-4 py-3">
                                    @php
                                        $hdClass = match($hd->trang_thai) {
                                            1 => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                            2 => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            3 => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $hdClass }}">
                                        {{ $hd->trangThaiLabel }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Chưa có hóa đơn nào.</p>
                @endif
            </div>

            {{-- Tab: Lịch sử thanh toán --}}
            <div x-show="tab === 'thanh_toan'" x-cloak>
                @php $hasLSTT = $canHo->hoaDon->some(fn($hd) => $hd->lichSuThanhToan->isNotEmpty()); @endphp
                @if($hasLSTT)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ngày TT</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mã hóa đơn</th>
                                <th class="text-right px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Số tiền</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Phương thức</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mã giao dịch</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($canHo->hoaDon as $hd)
                                @foreach($hd->lichSuThanhToan->sortByDesc('ngay_thanh_toan') as $ls)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">
                                        {{ $ls->ngay_thanh_toan?->format('d/m/Y H:i') ?? '–' }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">
                                        {{ $hd->ma_thanh_toan ?? '–' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold text-xs text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($ls->so_tien) }}đ
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                        {{ $ls->phuong_thuc_thanh_toan ?? '–' }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">
                                        {{ $ls->ma_giao_dich ?? '–' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                        {{ $ls->ghi_chu ?? '–' }}
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Chưa có lịch sử thanh toán.</p>
                @endif
            </div>

            {{-- Tab: Lịch sử cư dân --}}
            <div x-show="tab === 'lich_su'" x-cloak>
                @if($canHo->cuDanCanHo->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Họ tên</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Vai trò</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ngày chuyển đến</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ngày chuyển đi</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($canHo->cuDanCanHo->sortByDesc('ngay_chuyen_den') as $cdch)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    @if($cdch->cuDan)
                                    <a href="{{ route('manager.cu-dan.show', $cdch->cuDan) }}"
                                       class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $cdch->cuDan->ho_ten }}
                                    </a>
                                    @else
                                    <span class="text-gray-400">–</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $cdch->vaiTro?->vai_tro ?? '–' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $cdch->ngay_chuyen_den?->format('d/m/Y') ?? '–' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $cdch->ngay_chuyen_di?->format('d/m/Y') ?? '–' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $cdch->trang_thai == 1 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ $cdch->trang_thai == 1 ? 'Đang ở' : 'Đã chuyển đi' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Chưa có lịch sử cư dân.</p>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
