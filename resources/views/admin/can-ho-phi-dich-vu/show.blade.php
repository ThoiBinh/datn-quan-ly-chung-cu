@extends('layouts.admin')
@section('title', 'Chi tiết dịch vụ căn hộ')
@section('page-title', 'Chi tiết dịch vụ căn hộ')

@section('content')
<div class="max-w-4xl space-y-5">

    <!-- Header card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-gray-800">
                    {{ $canHoPhiDichVu->canHo?->so_can_ho ?? '—' }}
                    <span class="text-gray-400 font-normal">·</span>
                    {{ $canHoPhiDichVu->phiDichVu?->ten_phi_dich_vu ?? '—' }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $canHoPhiDichVu->canHo?->toaNha?->ten_toa_nha ?? '—' }}
                    · Tầng {{ $canHoPhiDichVu->canHo?->tang ?? '?' }}
                </p>
                <p class="text-xs text-gray-400 font-mono mt-0.5">ID: {{ $canHoPhiDichVu->id }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Đơn giá</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($canHoPhiDichVu->don_gia, 0, ',', '.') }}<span class="text-sm font-normal text-gray-400 ml-1">đ</span></p>
            </div>
        </div>

        <!-- Stats bar -->
        <div class="border-t border-gray-100 px-6 py-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-800">{{ $soDichVu }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Dịch vụ trong căn hộ</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">{{ number_format($tongPhiDV, 0, ',', '.') }}<span class="text-sm font-normal text-gray-400">đ</span></p>
                <p class="text-xs text-gray-500 mt-0.5">Tổng phí DV căn hộ</p>
            </div>
            <div class="text-center col-span-2 sm:col-span-1">
                <p class="text-sm font-semibold text-gray-700">{{ $canHoPhiDichVu->phiDichVu?->loaiTinhPhi?->ten_loai ?? '—' }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Loại tính phí</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap gap-3">
            <a href="{{ route('admin.can-ho-phi-dich-vu.edit', $canHoPhiDichVu) }}"
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('admin.can-ho-phi-dich-vu.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Căn hộ info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Thông tin căn hộ</h3>
            </div>
            <div class="p-6 space-y-3">
                @php
                $canHo = $canHoPhiDichVu->canHo;
                $canHoFields = [
                    ['label' => 'Số căn hộ', 'value' => $canHo?->so_can_ho, 'mono' => true],
                    ['label' => 'Tòa nhà', 'value' => $canHo?->toaNha?->ten_toa_nha],
                    ['label' => 'Tầng', 'value' => $canHo?->tang],
                    ['label' => 'Loại căn hộ', 'value' => $canHo?->loaiCanHo?->ten_loai_can_ho ?? null],
                    ['label' => 'Trạng thái', 'value' => $canHo?->trangThai?->ten_trang_thai ?? null],
                    ['label' => 'Giá thuê', 'value' => $canHo?->gia ? number_format($canHo->gia, 0, ',', '.') . ' đ' : null],
                ];
                @endphp
                @foreach($canHoFields as $f)
                <div class="flex justify-between items-start py-2 border-b border-gray-50 last:border-0">
                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">{{ $f['label'] }}</span>
                    <span class="text-sm font-semibold text-gray-700 text-right {{ ($f['mono'] ?? false) ? 'font-mono' : '' }}">
                        {{ $f['value'] ?: '—' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Dịch vụ info -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Thông tin dịch vụ</h3>
            </div>
            <div class="p-6 space-y-3">
                @php
                $dv = $canHoPhiDichVu->phiDichVu;
                $dvFields = [
                    ['label' => 'Tên dịch vụ', 'value' => $dv?->ten_phi_dich_vu],
                    ['label' => 'Loại dịch vụ', 'value' => $dv?->loaiPhiDichVu?->ten_loai_phi_dich_vu],
                    ['label' => 'Đơn vị tính', 'value' => $dv?->donViTinh?->don_vi],
                    ['label' => 'Loại tính phí', 'value' => $dv?->loaiTinhPhi?->ten_loai],
                    ['label' => 'Đơn giá gốc DV', 'value' => $dv?->don_gia ? number_format($dv->don_gia, 0, ',', '.') . ' đ' : null],
                    ['label' => 'Đơn giá căn hộ', 'value' => number_format($canHoPhiDichVu->don_gia, 0, ',', '.') . ' đ'],
                ];
                @endphp
                @foreach($dvFields as $f)
                <div class="flex justify-between items-start py-2 border-b border-gray-50 last:border-0">
                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">{{ $f['label'] }}</span>
                    <span class="text-sm font-semibold text-gray-700 text-right">
                        {{ $f['value'] ?: '—' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Metadata -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Thông tin hệ thống</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">Ngày tạo</dt>
                <dd class="text-sm font-semibold text-gray-700">
                    {{ $canHoPhiDichVu->createdAt?->format('d/m/Y H:i') ?? '—' }}
                </dd>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">Cập nhật lần cuối</dt>
                <dd class="text-sm font-semibold text-gray-700">
                    {{ $canHoPhiDichVu->updatedAt?->format('d/m/Y H:i') ?? '—' }}
                </dd>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">Người cập nhật</dt>
                <dd class="text-sm font-semibold text-gray-700">
                    {{ $canHoPhiDichVu->nguoiCapNhat?->ho_ten ?? '—' }}
                </dd>
            </div>
        </div>
    </div>

</div>
@endsection
