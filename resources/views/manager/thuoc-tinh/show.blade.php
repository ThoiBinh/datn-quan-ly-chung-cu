@extends('layouts.manager')
@section('title', 'Chi tiết thuộc tính')
@section('page-title', 'Chi tiết thuộc tính')

@section('content')
@php
    $kieu = [1 => 'Số nguyên', 2 => 'Chuỗi ký tự', 3 => 'Ngày giờ'];
    $kieuColor = [1 => 'bg-blue-100 text-blue-700', 2 => 'bg-purple-100 text-purple-700', 3 => 'bg-amber-100 text-amber-700'];
@endphp

<div class="space-y-5 max-w-3xl">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">{{ $thuocTinh->ten_thuoc_tinh }}</h2>
                    <p class="text-xs text-gray-400">ID: {{ $thuocTinh->id }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('manager.thuoc-tinh.edit', $thuocTinh) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Chỉnh sửa
                </a>
                <a href="{{ route('manager.thuoc-tinh.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 px-3 py-1.5 border border-gray-200 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Danh sách
                </a>
            </div>
        </div>

        <div class="p-6 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tên thuộc tính</p>
                <p class="font-semibold text-gray-800">{{ $thuocTinh->ten_thuoc_tinh }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Trạng thái</p>
                @if($thuocTinh->deletedAt)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Đã xóa</span>
                @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Hoạt động</span>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Ngày tạo</p>
                <p class="text-gray-700">{{ $thuocTinh->createdAt ? \Carbon\Carbon::parse($thuocTinh->createdAt)->format('d/m/Y H:i:s') : '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Cập nhật lần cuối</p>
                <p class="text-gray-700">{{ $thuocTinh->updatedAt ? \Carbon\Carbon::parse($thuocTinh->updatedAt)->format('d/m/Y H:i:s') : '—' }}</p>
            </div>
            @if($thuocTinh->deletedAt)
            <div class="col-span-2">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Ngày xóa</p>
                <p class="text-red-600">{{ \Carbon\Carbon::parse($thuocTinh->deletedAt)->format('d/m/Y H:i:s') }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Căn hộ sử dụng --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">
                Căn hộ đang sử dụng thuộc tính này
                <span class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">{{ $thuocTinh->canHo->count() }}</span>
            </h3>
        </div>

        @if($thuocTinh->canHo->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-sm">
            Chưa có căn hộ nào sử dụng thuộc tính này.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Căn hộ</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Giá trị</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Kiểu dữ liệu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($thuocTinh->canHo as $canHo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('manager.can-ho.show', $canHo) }}" class="font-medium text-indigo-600 hover:underline">
                                {{ $canHo->so_can_ho }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $canHo->pivot->gia_tri_thuoc_tinh ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @php $k = $canHo->pivot->kieu_du_lieu; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $kieuColor[$k] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $kieu[$k] ?? 'Không xác định' }}
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
