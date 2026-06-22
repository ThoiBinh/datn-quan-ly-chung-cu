@extends('layouts.resident')
@section('title', 'Hồ sơ cá nhân')
@section('page-title', 'Hồ sơ cá nhân')

@section('content')
<div class="max-w-2xl space-y-6">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3" x-data x-init="setTimeout(()=>$el.remove(),4000)">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-green-700 text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center">
                <span class="text-2xl font-bold text-emerald-700">{{ strtoupper(substr($cuDan->name, 0, 1)) }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $cuDan->ho_ten }}</h2>
                <p class="text-sm text-gray-500">{{ $cuDan->email }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Họ tên đệm</p>
                <p class="text-gray-800">{{ $cuDan->ho_ten_dem ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Tên</p>
                <p class="text-gray-800">{{ $cuDan->ten ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Email</p>
                <p class="text-gray-800">{{ $cuDan->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Số điện thoại</p>
                <p class="text-gray-800">{{ $cuDan->sdt ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">CMND/CCCD</p>
                <p class="text-gray-800">{{ $cuDan->cccd ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Ngày sinh</p>
                <p class="text-gray-800">{{ $cuDan->ngay_sinh?->format('d/m/Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Tỉnh/Thành</p>
                <p class="text-gray-800">{{ $cuDan->tinh ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Địa chỉ</p>
                <p class="text-gray-800">{{ $cuDan->dia_chi ?? '-' }}</p>
            </div>
            @if($canHo)
            <div class="col-span-2">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Căn hộ hiện tại</p>
                <p class="text-gray-800">Căn hộ <strong>{{ $canHo->so_can_ho }}</strong> — {{ $canHo->toaNha?->ten_toa_nha }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('resident.profile.edit') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">Chỉnh sửa hồ sơ</a>
        <a href="{{ route('resident.change-password') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Đổi mật khẩu</a>
    </div>
</div>
@endsection
