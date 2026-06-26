@extends('layouts.admin')
@section('title', 'Chi tiết loại căn hộ')
@section('page-title', 'Chi tiết loại căn hộ')

@section('content')
@php
    $canHos = $loaiCanHo->canHo;
@endphp

<div class="space-y-5 max-w-5xl">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">{{ $loaiCanHo->ten_loai_can_ho }}</h2>
                    <p class="text-xs text-gray-400">ID: {{ $loaiCanHo->id }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.loai-can-ho.edit', $loaiCanHo) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Chỉnh sửa
                </a>
                <a href="{{ route('admin.loai-can-ho.index') }}" class="flex items-center gap-1 px-3 py-1.5 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Danh sách
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tên loại căn hộ</p>
                    <p class="font-semibold text-gray-800">{{ $loaiCanHo->ten_loai_can_ho }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tổng số căn hộ</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-semibold
                        {{ $canHos->count() > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $canHos->count() }} căn hộ
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $canHos->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Tổng căn hộ</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">
                {{ $canHos->filter(fn($c) => $c->gia)->count() }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Căn hộ có giá</p>
        </div>
    </div>

    {{-- Danh sách căn hộ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">
                Căn hộ thuộc loại này
                <span class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                    {{ $canHos->count() }}
                </span>
            </h3>
        </div>

        @if($canHos->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-sm">
            Chưa có căn hộ nào thuộc loại này.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Số căn hộ</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tầng</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tòa nhà</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Giá (VNĐ)</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($canHos as $canHo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-800">{{ $canHo->so_can_ho }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">Tầng {{ $canHo->tang }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $canHo->toaNha?->ten_toa_nha ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">
                            {{ $canHo->gia ? number_format($canHo->gia, 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $canHo->trangThai?->ten_trang_thai ?? '—' }}
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
