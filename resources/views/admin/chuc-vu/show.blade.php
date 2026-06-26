@extends('layouts.admin')
@section('title', 'Chi tiết chức vụ')
@section('page-title', 'Chi tiết chức vụ')

@section('content')
<div class="space-y-5 max-w-5xl">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">{{ $chucVu->chuc_vu }}</h2>
                    <p class="text-xs text-gray-400">ID: {{ $chucVu->id }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.chuc-vu.edit', $chucVu) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Chỉnh sửa
                </a>
                <a href="{{ route('admin.chuc-vu.index') }}" class="flex items-center gap-1 px-3 py-1.5 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Danh sách
                </a>
            </div>
        </div>

        <div class="p-6 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tên chức vụ</p>
                <p class="font-semibold text-gray-800">{{ $chucVu->chuc_vu }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Trạng thái</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Hoạt động</span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Ngày tạo</p>
                <p class="text-gray-700">{{ $chucVu->createdAt ? \Carbon\Carbon::parse($chucVu->createdAt)->format('d/m/Y H:i') : '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Cập nhật lần cuối</p>
                <p class="text-gray-700">{{ $chucVu->updatedAt ? \Carbon\Carbon::parse($chucVu->updatedAt)->format('d/m/Y H:i') : '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $tongNhanVien }}</p>
            <p class="text-xs text-gray-500 mt-1">Tổng nhân viên</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $tongHoatDong }}</p>
            <p class="text-xs text-gray-500 mt-1">Đang hoạt động</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $tongBiKhoa }}</p>
            <p class="text-xs text-gray-500 mt-1">Bị khóa</p>
        </div>
    </div>

    {{-- Danh sách nhân viên --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">
                Nhân viên thuộc chức vụ này
                <span class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                    {{ $tongNhanVien }}
                </span>
            </h3>
        </div>

        @if($chucVu->nhanVien->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-sm">
            Chưa có nhân viên nào thuộc chức vụ này.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Họ tên</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Mã NV</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Email</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Số điện thoại</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Ngày vào làm</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($chucVu->nhanVien as $nv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $nv->ho_ten }}</td>
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $nv->ma_nhan_vien }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $nv->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $nv->sdt ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $nv->ngay_vao_lam ? \Carbon\Carbon::parse($nv->ngay_vao_lam)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($nv->trang_thai == 1)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Hoạt động</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Bị khóa</span>
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
