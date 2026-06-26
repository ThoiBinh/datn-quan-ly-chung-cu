@extends('layouts.admin')
@section('title', 'Chi tiết loại phương tiện')
@section('page-title', 'Chi tiết loại phương tiện')

@section('content')
@php
    $tongHoatDong = $loaiPhuongTien->phuongTien->where('trang_thai', 1)->count();
    $tongDaHuy    = $loaiPhuongTien->phuongTien->where('trang_thai', '!=', 1)->count();
@endphp

<div class="space-y-5 max-w-4xl">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">{{ $loaiPhuongTien->ten_loai_phuong_tien }}</h2>
                    <p class="text-xs text-gray-400">ID: {{ $loaiPhuongTien->id }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.loai-phuong-tien.edit', $loaiPhuongTien) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Chỉnh sửa
                </a>
                <a href="{{ route('admin.loai-phuong-tien.index') }}" class="flex items-center gap-1 px-3 py-1.5 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50">
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
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tên loại phương tiện</p>
                    <p class="font-semibold text-gray-800">{{ $loaiPhuongTien->ten_loai_phuong_tien }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tổng số phương tiện</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-semibold
                        {{ $loaiPhuongTien->phuongTien->count() > 0 ? 'bg-sky-100 text-sky-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $loaiPhuongTien->phuongTien->count() }} phương tiện
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Thống kê --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $loaiPhuongTien->phuongTien->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Tổng phương tiện</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $tongHoatDong }}</p>
            <p class="text-xs text-gray-500 mt-1">Đang hoạt động</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $tongDaHuy }}</p>
            <p class="text-xs text-gray-500 mt-1">Đã hủy đăng ký</p>
        </div>
    </div>

    {{-- Danh sách phương tiện --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">
                Phương tiện thuộc loại này
                <span class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-sky-100 text-sky-700">
                    {{ $loaiPhuongTien->phuongTien->count() }}
                </span>
            </h3>
        </div>

        @if($loaiPhuongTien->phuongTien->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-sm">
            Chưa có phương tiện nào thuộc loại này.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Biển số</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tên phương tiện</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Căn hộ</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tòa nhà</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Ngày đăng ký</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($loaiPhuongTien->phuongTien as $pt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-mono font-semibold text-gray-800 tracking-wider">{{ $pt->bien_so }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $pt->ten_phuong_tien ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $pt->canHo->so_can_ho ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $pt->canHo?->toaNha?->ten_toa_nha ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $pt->ngay_dang_ky ? $pt->ngay_dang_ky->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($pt->trang_thai == 1)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Hoạt động</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Đã hủy</span>
                            @endif
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
