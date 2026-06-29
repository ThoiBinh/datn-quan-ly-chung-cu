@extends('layouts.admin')
@section('title', 'Chi tiết thanh toán')
@section('page-title', 'Giao dịch thanh toán')

@section('content')
@php
$hoaDon = $lichSu->hoaDon;
$canHo  = $hoaDon?->canHo;
$chuHo  = $canHo?->chuHo?->cuDan;
@endphp

<div class="space-y-5">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.thanh-toan.index') }}" class="hover:text-indigo-600 transition-colors">Lịch sử thanh toán</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200 font-mono font-semibold">#{{ $lichSu->id }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Thông tin giao dịch --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-slate-200">Thông tin giao dịch</h2>
                    <span class="ml-auto text-xs font-mono text-gray-400 dark:text-slate-500">#{{ $lichSu->id }}</span>
                </div>
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Số tiền</dt>
                        <dd class="text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ number_format($lichSu->so_tien ?? 0, 0, ',', '.') }}đ</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Ngày thanh toán</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-300">{{ $lichSu->ngay_thanh_toan?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Phương thức</dt>
                        <dd>
                            @if($lichSu->phuong_thuc_thanh_toan)
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400">{{ $lichSu->phuong_thuc_thanh_toan }}</span>
                            @else<span class="text-gray-400">—</span>@endif
                        </dd>
                    </div>
                    @if($lichSu->ma_giao_dich)
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Mã giao dịch</dt>
                        <dd class="text-sm font-mono text-gray-700 dark:text-slate-300">{{ $lichSu->ma_giao_dich }}</dd>
                    </div>
                    @endif
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Nguồn tạo</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-300">{{ $lichSu->nguonTao?->ten_nguon_tao ?? '—' }}</dd>
                    </div>
                    @if($lichSu->nguoiThanhToan)
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Người thanh toán</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-300">{{ $lichSu->nguoiThanhToan->ho_ten }}</dd>
                    </div>
                    @endif
                    @if($lichSu->ghi_chu)
                    <div class="flex items-start px-5 py-3.5">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0 pt-0.5">Ghi chú</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-300">{{ $lichSu->ghi_chu }}</dd>
                    </div>
                    @endif
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Ngày ghi nhận</dt>
                        <dd class="text-xs text-gray-500 dark:text-slate-400">{{ $lichSu->createdAt?->format('d/m/Y H:i:s') ?? '—' }}</dd>
                    </div>
                </dl>
                <div class="px-5 py-4 bg-amber-50 dark:bg-amber-900/10 border-t border-amber-100 dark:border-amber-900/30">
                    <p class="text-xs text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Giao dịch thanh toán là bất biến — không thể chỉnh sửa hoặc xóa.
                    </p>
                </div>
            </div>

            {{-- Hóa đơn liên kết --}}
            @if($hoaDon)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-slate-200">Hóa đơn liên kết</h2>
                    <a href="{{ route('admin.hoa-don.show', $hoaDon) }}"
                       class="ml-auto text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                        Xem hóa đơn
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Mã hóa đơn</dt>
                        <dd class="font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $hoaDon->ma_thanh_toan }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Kỳ</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-300">Tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Tổng tiền HĐ</dt>
                        <dd class="text-sm font-semibold text-gray-800 dark:text-slate-200 tabular-nums">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-36 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Đã thanh toán</dt>
                        <dd class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ number_format($hoaDon->so_tien_da_thanh_toan ?? 0, 0, ',', '.') }}đ</dd>
                    </div>
                </dl>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Căn hộ --}}
            @if($canHo)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-slate-200">Căn hộ</h2>
                </div>
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Số căn hộ</dt>
                        <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200">{{ $canHo->so_can_ho }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Tòa nhà</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-300">{{ $canHo->toaNha?->ten_toa_nha ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
            @endif

            {{-- Chủ hộ --}}
            @if($chuHo)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-slate-200">Chủ hộ</h2>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ strtoupper(substr($chuHo->ho_ten ?? '?', 0, 1)) }}</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-slate-200">{{ $chuHo->ho_ten }}</p>
                    </div>
                    @if($chuHo->sdt)<p class="text-xs text-gray-500 dark:text-slate-400 mt-1">SĐT: {{ $chuHo->sdt }}</p>@endif
                    @if($chuHo->email)<p class="text-xs text-gray-500 dark:text-slate-400 mt-1 break-all">Email: {{ $chuHo->email }}</p>@endif
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
