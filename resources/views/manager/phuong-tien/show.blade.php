@extends('layouts.manager')
@section('title', 'Phương tiện ' . $phuongTien->bien_so)
@section('page-title', 'Phương tiện ' . $phuongTien->bien_so)

@php
    $chuHo = $phuongTien->canHo?->chuHo?->cuDan;
    $dienTich = $phuongTien->canHo?->thuocTinh->first(fn($tt) => str_starts_with($tt->ten_thuoc_tinh, 'Diện tích'));
@endphp

@section('content')
<div class="space-y-5">

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 dark:text-emerald-300">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                    <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 17h8m-8-4h8M5 21h14a2 2 0 002-2V7l-5-5H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 font-mono">{{ $phuongTien->bien_so }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $phuongTien->loaiPhuongTien?->ten_loai_phuong_tien ?? '–' }} · {{ $phuongTien->ten_phuong_tien ?? 'Chưa rõ tên xe' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $phuongTien->trang_thai_label['class'] }}">{{ $phuongTien->trang_thai_label['text'] }}</span>
                <a href="{{ route('manager.phuong-tien.edit', $phuongTien) }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Chỉnh sửa
                </a>
                <a href="{{ route('manager.phuong-tien.index') }}"
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Card 1: Thông tin phương tiện --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Thông tin phương tiện</h3>
            </div>
            <dl class="grid grid-cols-2 gap-4 p-5">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Biển số</dt>
                    <dd class="text-sm font-semibold font-mono text-gray-800 dark:text-gray-200">{{ $phuongTien->bien_so }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Loại phương tiện</dt>
                    <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $phuongTien->loaiPhuongTien?->ten_loai_phuong_tien ?? '–' }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 col-span-2">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Tên / Hãng xe</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $phuongTien->ten_phuong_tien ?? '–' }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Trạng thái</dt>
                    <dd><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $phuongTien->trang_thai_label['class'] }}">{{ $phuongTien->trang_thai_label['text'] }}</span></dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Ngày đăng ký</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $phuongTien->ngay_dang_ky?->format('d/m/Y') ?? '–' }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Ngày hủy</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $phuongTien->ngay_huy?->format('d/m/Y') ?? '–' }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Người cập nhật</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $phuongTien->nguoiCapNhat?->ho_ten ?? '–' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Card 2: Thông tin cư dân (chủ hộ của căn hộ) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Thông tin cư dân (chủ hộ)</h3>
            </div>
            @if($chuHo)
            <dl class="grid grid-cols-2 gap-4 p-5">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 col-span-2">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Họ tên</dt>
                    <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        <a href="{{ route('manager.cu-dan.show', $chuHo) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $chuHo->ho_ten }}</a>
                    </dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">CCCD</dt>
                    <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $chuHo->cccd ?? '–' }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Điện thoại</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $chuHo->sdt ?? '–' }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Email</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $chuHo->email ?? '–' }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Địa chỉ</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $chuHo->dia_chi ?? '–' }}</dd>
                </div>
            </dl>
            @else
            <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Căn hộ chưa xác định chủ hộ.</p>
            @endif
        </div>

        {{-- Card 3: Thông tin căn hộ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Thông tin căn hộ</h3>
            </div>
            @if($phuongTien->canHo)
            <dl class="grid grid-cols-2 gap-4 p-5">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Tòa nhà</dt>
                    <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $phuongTien->canHo->toaNha?->ten_toa_nha ?? '–' }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Số căn hộ</dt>
                    <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        <a href="{{ route('manager.can-ho.show', $phuongTien->canHo) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $phuongTien->canHo->so_can_ho }}</a>
                    </dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Loại căn hộ</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $phuongTien->canHo->loaiCanHo?->ten_loai_can_ho ?? '–' }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Diện tích</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $dienTich?->pivot?->gia_tri_thuoc_tinh ?? '–' }}</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 col-span-2">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Trạng thái căn hộ</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $phuongTien->canHo->trangThai?->ten_trang_thai ?? '–' }}</dd>
                </div>
            </dl>
            @else
            <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Không có thông tin căn hộ.</p>
            @endif
        </div>

        {{-- Card 4: Thuộc tính căn hộ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Thuộc tính căn hộ</h3>
            </div>
            @if($phuongTien->canHo && $phuongTien->canHo->thuocTinh->count() > 0)
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Thuộc tính</th>
                                <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Giá trị</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($phuongTien->canHo->thuocTinh as $tt)
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-200">{{ $tt->ten_thuoc_tinh }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $tt->pivot->gia_tri_thuoc_tinh ?? '–' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Chưa có thuộc tính nào.</p>
            @endif
        </div>

        {{-- Card 5: Thông tin loại phương tiện --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Thông tin loại phương tiện</h3>
            </div>
            @if($phuongTien->loaiPhuongTien)
            <dl class="grid grid-cols-1 gap-4 p-5 max-w-sm">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Tên loại</dt>
                    <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $phuongTien->loaiPhuongTien->ten_loai_phuong_tien }}</dd>
                </div>
            </dl>
            @else
            <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Không có thông tin loại phương tiện.</p>
            @endif
        </div>

        {{-- Card 6: Lịch sử ra/vào --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Lịch sử ra/vào</h3>
            </div>
            <p class="text-sm text-gray-400 dark:text-gray-500 py-8 text-center">Hệ thống hiện chưa có bảng lưu lịch sử ra/vào cho phương tiện.</p>
        </div>
    </div>
</div>
@endsection
