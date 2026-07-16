@extends('layouts.resident')
@section('title', 'Trang chủ')
@section('page-title', 'Trang chủ')

@section('content')
<div class="space-y-6">
    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-6 text-white shadow-sm">
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
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Hóa đơn chưa TT</p>
            <p class="text-2xl font-bold text-red-600 mt-1.5">{{ $soHoaDonChuaThanhToan }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Tổng nợ</p>
            <p class="text-2xl font-bold text-orange-600 mt-1.5">{{ number_format($tongNo / 1000) }}K</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Yêu cầu mở</p>
            <p class="text-2xl font-bold text-blue-600 mt-1.5">{{ $soYeuCauMo }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Phương tiện</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1.5">{{ $soPhuongTien }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Unpaid Invoices --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Hóa đơn cần thanh toán</h3>
                <a href="{{ route('resident.hoa-don.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Xem tất cả</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($hoaDonChuaThanhToan as $hd)
                <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition-colors duration-200">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Tháng {{ $hd->thang }}/{{ $hd->nam }}</p>
                        <p class="text-xs text-gray-500">HĐ #{{ $hd->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-red-600">{{ number_format($hd->tong_tien-$hd->so_tien_da_thanh_toan) }}đ</p>
                        <a href="{{ route('resident.hoa-don.show', $hd) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Thanh toán</a>
                    </div>
                </div>
                @empty
                <p class="px-5 py-8 text-center text-sm text-gray-400">Không có hóa đơn cần thanh toán</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Notifications --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Thông báo mới nhất</h3>
                <a href="{{ route('resident.thong-bao.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Xem tất cả</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($thongBaoMoi as $tb)
                <a href="{{ route('resident.thong-bao.show', $tb) }}" class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors duration-200 block">
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
