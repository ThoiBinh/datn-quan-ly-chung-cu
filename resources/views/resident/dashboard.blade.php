@extends('layouts.resident')
@section('title', 'Trang chủ')
@section('page-title', 'Trang chủ')

@section('content')
<div class="space-y-6">
    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-6 text-white">
        <h2 class="text-xl font-bold">Xin chào, {{ $cuDan?->name ?? auth('cudan')->user()?->name }}!</h2>
        <p class="text-emerald-100 text-sm mt-1">
            @if($cuDan && $canHo)
                Căn hộ <strong class="text-white">{{ $canHo->so_can_ho }}</strong> — {{ $canHo->toaNha?->ten_toa_nha }}
            @else
                Chào mừng bạn đến với hệ thống quản lý chung cư.
            @endif
        </p>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-semibold">Hóa đơn chưa TT</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $soHoaDonChuaThanhToan }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-semibold">Tổng nợ</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($tongNo / 1000) }}K</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-semibold">Yêu cầu mở</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $soYeuCauMo }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-semibold">Phương tiện</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $soPhuongTien }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Unpaid Invoices --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between p-5 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Hóa đơn cần thanh toán</h3>
                <a href="{{ route('resident.hoa-don.index') }}" class="text-xs text-emerald-600 hover:underline">Xem tất cả</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($hoaDonChuaThanhToan as $hd)
                <div class="flex items-center justify-between px-5 py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Tháng {{ $hd->thang }}/{{ $hd->nam }}</p>
                        <p class="text-xs text-gray-500">HĐ #{{ $hd->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-red-600">{{ number_format($hd->tong_tien) }}đ</p>
                        <a href="{{ route('resident.hoa-don.show', $hd) }}" class="text-xs text-emerald-600 hover:underline">Thanh toán</a>
                    </div>
                </div>
                @empty
                <p class="px-5 py-8 text-center text-sm text-gray-400">Không có hóa đơn cần thanh toán</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Notifications --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between p-5 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Thông báo mới nhất</h3>
                <a href="{{ route('resident.thong-bao.index') }}" class="text-xs text-emerald-600 hover:underline">Xem tất cả</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($thongBaoMoi as $tb)
                <a href="{{ route('resident.thong-bao.show', $tb) }}" class="flex items-start gap-3 px-5 py-3 hover:bg-gray-50 block">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full mt-1.5 flex-shrink-0"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $tb->tieu_de }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $tb->created_at?->diffForHumans() }}</p>
                    </div>
                </a>
                @empty
                <p class="px-5 py-8 text-center text-sm text-gray-400">Chưa có thông báo</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
