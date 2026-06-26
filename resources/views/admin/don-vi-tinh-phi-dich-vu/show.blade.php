@extends('layouts.admin')
@section('title', 'Chi tiết đơn vị tính')
@section('page-title', 'Chi tiết đơn vị tính phí dịch vụ')

@section('content')
@php
    $phiDichVus = $donViTinhPhiDichVu->phiDichVu;
@endphp

<div class="space-y-5 max-w-5xl">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">{{ $donViTinhPhiDichVu->don_vi }}</h2>
                    <p class="text-xs text-gray-400">ID: {{ $donViTinhPhiDichVu->id }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.don-vi-tinh-phi-dich-vu.edit', $donViTinhPhiDichVu) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Chỉnh sửa
                </a>
                <a href="{{ route('admin.don-vi-tinh-phi-dich-vu.index') }}" class="flex items-center gap-1 px-3 py-1.5 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Danh sách
                </a>
            </div>
        </div>

        <div class="p-6 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tên đơn vị tính</p>
                <p class="font-semibold text-gray-800 text-lg">{{ $donViTinhPhiDichVu->don_vi }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Số phí dịch vụ sử dụng</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-semibold
                    {{ $phiDichVus->count() > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $phiDichVus->count() }} phí dịch vụ
                </span>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $phiDichVus->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Tổng phí dịch vụ</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">
                {{ $phiDichVus->sum(fn($p) => $p->canHo->count()) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Lượt áp dụng căn hộ</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">
                {{ $phiDichVus->count() > 0 ? number_format($phiDichVus->avg('don_gia'), 0, ',', '.') : '0' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Đơn giá TB (VNĐ)</p>
        </div>
    </div>

    {{-- Danh sách phí dịch vụ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">
                Phí dịch vụ dùng đơn vị tính này
                <span class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                    {{ $phiDichVus->count() }}
                </span>
            </h3>
        </div>

        @if($phiDichVus->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-sm">
            Chưa có phí dịch vụ nào sử dụng đơn vị tính này.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tên phí dịch vụ</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Loại phí</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Đơn giá (VNĐ)</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Loại tính phí</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Số căn hộ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($phiDichVus as $phi)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $phi->ten_phi_dich_vu }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $phi->loaiPhiDichVu?->ten_loai_phi_dich_vu ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-blue-700">
                            {{ number_format($phi->don_gia, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $phi->loaiTinhPhi?->ten_loai ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold
                                {{ $phi->canHo->count() > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $phi->canHo->count() }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
