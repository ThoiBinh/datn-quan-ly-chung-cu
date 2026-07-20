@extends('layouts.manager')
@section('title', 'Chi tiết loại yêu cầu')
@section('page-title', 'Chi tiết loại yêu cầu')

@section('content')
@php
    $yeuCaus      = $loaiYeuCau->yeuCau;
    $demMoi        = $yeuCaus->where('trang_thai', 1)->count();
    $demDangXuLy   = $yeuCaus->where('trang_thai', 2)->count();
    $demHoanThanh  = $yeuCaus->where('trang_thai', 3)->count();
    $demTuChoi     = $yeuCaus->where('trang_thai', 4)->count();
@endphp

<div class="space-y-5 max-w-5xl">

    {{-- Header card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg {{ $loaiYeuCau->deletedAt ? 'bg-gray-100' : 'bg-indigo-100' }} flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $loaiYeuCau->deletedAt ? 'text-gray-400' : 'text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">{{ $loaiYeuCau->name }}</h2>
                    <p class="text-xs text-gray-400">ID: {{ $loaiYeuCau->id }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if(!$loaiYeuCau->deletedAt)
                <a href="{{ route('manager.loai-yeu-cau.edit', $loaiYeuCau) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Chỉnh sửa
                </a>
                @else
                <form action="{{ route('manager.loai-yeu-cau.restore', $loaiYeuCau->id) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Khôi phục
                    </button>
                </form>
                @endif
                <a href="{{ route('manager.loai-yeu-cau.index') }}" class="flex items-center gap-1 px-3 py-1.5 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Danh sách
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tên loại yêu cầu</p>
                    <p class="font-semibold text-gray-800">{{ $loaiYeuCau->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Trạng thái</p>
                    @if($loaiYeuCau->deletedAt)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Đã xóa</span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Hoạt động</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Người cập nhật</p>
                    <p class="text-gray-700">{{ $loaiYeuCau->nguoiCapNhat?->ho_ten ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Ngày tạo</p>
                    <p class="text-gray-600">{{ $loaiYeuCau->createdAt ? $loaiYeuCau->createdAt->format('d/m/Y H:i') : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Cập nhật lần cuối</p>
                    <p class="text-gray-600">{{ $loaiYeuCau->updatedAt ? \Carbon\Carbon::parse($loaiYeuCau->updatedAt)->format('d/m/Y H:i') : '—' }}</p>
                </div>
                @if($loaiYeuCau->deletedAt)
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Ngày xóa</p>
                    <p class="text-red-500">{{ \Carbon\Carbon::parse($loaiYeuCau->deletedAt)->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats yêu cầu --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $yeuCaus->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Tổng yêu cầu</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $demMoi }}</p>
            <p class="text-xs text-gray-500 mt-1">Mới</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $demDangXuLy }}</p>
            <p class="text-xs text-gray-500 mt-1">Đang xử lý</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $demHoanThanh }}</p>
            <p class="text-xs text-gray-500 mt-1">Hoàn thành</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $demTuChoi }}</p>
            <p class="text-xs text-gray-500 mt-1">Từ chối</p>
        </div>
    </div>

    {{-- Danh sách yêu cầu --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">
                Yêu cầu thuộc loại này
                <span class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                    {{ $yeuCaus->count() }}
                </span>
            </h3>
        </div>

        @if($yeuCaus->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-sm">
            Chưa có yêu cầu nào thuộc loại này.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tiêu đề</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Cư dân</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">NV xử lý</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Ngày gửi</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Mức độ</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($yeuCaus as $yc)
                    @php
                        $tt = $yc->trang_thai_label;
                        $md = $yc->muc_do_label;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 truncate max-w-xs">{{ $yc->tieu_de }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $yc->cuDan?->ho_ten ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $yc->nhanVienXuLy?->ho_ten ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $yc->ngay_gui ? $yc->ngay_gui->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $md['class'] }}">
                                {{ $md['text'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $tt['class'] }}">
                                {{ $tt['text'] }}
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
