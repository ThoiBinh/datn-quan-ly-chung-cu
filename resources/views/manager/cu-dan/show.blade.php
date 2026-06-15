@extends('layouts.manager')
@section('title', 'Chi tiết cư dân')
@section('page-title', $cuDan->ho_ten)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Thông tin cư dân</h3>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">Họ tên</dt><dd class="font-medium mt-0.5">{{ $cuDan->ho_ten }}</dd></div>
                <div><dt class="text-gray-500">SĐT</dt><dd class="font-medium mt-0.5">{{ $cuDan->sdt ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Email</dt><dd class="font-medium mt-0.5">{{ $cuDan->email ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">CCCD</dt><dd class="font-mono text-xs mt-0.5">{{ $cuDan->cccd ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Ngày sinh</dt><dd class="font-medium mt-0.5">{{ $cuDan->nam_sinh?->format('d/m/Y') ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Quê quán</dt><dd class="font-medium mt-0.5">{{ $cuDan->que_quan ?? '-' }}</dd></div>
            </dl>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('manager.cu-dan.edit', $cuDan) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Sửa</a>
            </div>
        </div>
    </div>
    <div class="space-y-4">
        @if($cuDan->canHoHienTai)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Căn hộ hiện tại</h3>
            <p class="text-lg font-bold text-indigo-600">{{ $cuDan->canHoHienTai->canHo?->so_can_ho }}</p>
            <p class="text-sm text-gray-500">{{ $cuDan->canHoHienTai->canHo?->toaNha?->ten_toa_nha }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $cuDan->canHoHienTai->vaiTro?->vai_tro }}</p>
        </div>
        @endif
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Yêu cầu gần đây</h3>
            @forelse($cuDan->yeuCau->take(3) as $yc)
            <div class="text-sm py-1.5 border-b border-gray-100 last:border-0">
                <p class="text-gray-700 truncate">{{ $yc->tieu_de }}</p>
                <p class="text-xs text-gray-400">{{ $yc->trang_thai_label }}</p>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Chưa có yêu cầu</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
