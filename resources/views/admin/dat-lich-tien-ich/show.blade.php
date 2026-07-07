@extends('layouts.admin')
@section('title', 'Chi tiết đặt lịch tiện ích')
@section('page-title', 'Chi tiết đặt lịch tiện ích')

@section('content')
@php
    $trangThai = (int) $datLichTienIch->trang_thai;
    $CHO_DUYET = \App\Models\DatLichTienIch::TRANG_THAI_CHO_DUYET;
    $DA_DUYET = \App\Models\DatLichTienIch::TRANG_THAI_DA_DUYET;
    $coTheSua = $trangThai === $CHO_DUYET;
    $coTheDuyetTuChoi = $trangThai === $CHO_DUYET;
    $coTheHuy = in_array($trangThai, [$CHO_DUYET, $DA_DUYET], true);

    $phut = $datLichTienIch->thoi_luong_phut;
    $thoiLuong = null;
    if ($phut !== null) {
        $gio = intdiv($phut, 60);
        $conLai = $phut % 60;
        $thoiLuong = trim(($gio > 0 ? "{$gio} giờ " : '') . ($conLai > 0 ? "{$conLai} phút" : ($gio > 0 ? '' : '0 phút')));
    }
@endphp
<div class="space-y-5" x-data="{ confirmDelete: false, confirmApprove: false, showReject: false, showCancel: false }">

    <!-- Breadcrumb + actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
            <a href="{{ route('admin.dat-lich-tien-ich.index') }}" class="hover:text-indigo-600 transition-colors">Đặt lịch tiện ích</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="font-mono font-semibold text-gray-700 dark:text-slate-200">{{ $datLichTienIch->ma_dat_lich }}</span>
        </nav>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.dat-lich-tien-ich.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>

            @if($coTheDuyetTuChoi)
            <button @click="confirmApprove = true" type="button"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Duyệt
            </button>
            <button @click="showReject = true" type="button"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200 dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Từ chối
            </button>
            @endif

            @if($coTheHuy)
            <button @click="showCancel = true" type="button"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors bg-orange-50 text-orange-700 hover:bg-orange-100 border border-orange-200 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                Hủy lịch
            </button>
            @endif

            @if($coTheSua)
            <a href="{{ route('admin.dat-lich-tien-ich.edit', $datLichTienIch) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            @endif

            <button @click="confirmDelete = true" type="button"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Xóa
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Cột chính -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Thông tin đặt lịch -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <svg class="w-4.5 h-4.5 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thông tin đặt lịch</h2>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $datLichTienIch->trang_thai_label['class'] }}">
                        {{ $datLichTienIch->trang_thai_label['text'] }}
                    </span>
                </div>
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Mã đặt lịch</dt>
                        <dd class="font-mono font-bold text-lg text-indigo-600 dark:text-indigo-400">{{ $datLichTienIch->ma_dat_lich }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Tiện ích</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">
                            {{ $datLichTienIch->tienIch?->ten_tien_ich ?? '—' }}
                            @if($datLichTienIch->tienIch?->loaiTienIch)
                            <span class="ml-1 text-xs text-gray-400 dark:text-slate-500">({{ $datLichTienIch->tienIch->loaiTienIch->ten_loai_tien_ich }})</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Thời gian bắt đầu</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->thoi_gian_bat_dau?->format('H:i, d/m/Y') }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Thời gian kết thúc</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->thoi_gian_ket_thuc?->format('H:i, d/m/Y') }}</dd>
                    </div>
                    @if($thoiLuong)
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Thời lượng sử dụng</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $thoiLuong }}</dd>
                    </div>
                    @endif
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Số người/phòng/sân</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->so_nguoi }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Phí sử dụng</dt>
                        <dd class="text-sm font-semibold text-gray-800 dark:text-slate-100">{{ number_format((float) $datLichTienIch->phi_su_dung, 0, ',', '.') }} đ</dd>
                    </div>
                    @if($datLichTienIch->ghi_chu)
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Ghi chú</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100 whitespace-pre-line">{{ $datLichTienIch->ghi_chu }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Duyệt / hủy -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Duyệt / Hủy</h2>
                </div>
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Nhân viên duyệt</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->nhanVienDuyet?->ho_ten ?? ($datLichTienIch->ngay_duyet ? 'Hệ thống tự động duyệt' : '—') }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Ngày duyệt</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->ngay_duyet?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Ngày hủy / từ chối</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->ngay_huy?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    @if($datLichTienIch->ly_do_huy)
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-44 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Lý do hủy / từ chối</dt>
                        <dd class="text-sm text-red-600 dark:text-red-400 whitespace-pre-line">{{ $datLichTienIch->ly_do_huy }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

        </div>

        <!-- Cột phụ -->
        <div class="space-y-5">

            <!-- Cư dân -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Cư dân đặt lịch</h2>
                </div>
                @if($datLichTienIch->cuDan)
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                {{ strtoupper(substr($datLichTienIch->cuDan->ho_ten ?? '?', 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $datLichTienIch->cuDan->ho_ten }}</p>
                            <p class="text-xs text-gray-400 dark:text-slate-500">Cư dân</p>
                        </div>
                    </div>
                    <dl class="space-y-2.5">
                        @if($datLichTienIch->cuDan->sdt)
                        <div>
                            <dt class="text-xs text-gray-400 dark:text-slate-500 mb-0.5">Điện thoại</dt>
                            <dd class="text-sm text-gray-700 dark:text-slate-200">{{ $datLichTienIch->cuDan->sdt }}</dd>
                        </div>
                        @endif
                        @if($datLichTienIch->cuDan->email)
                        <div>
                            <dt class="text-xs text-gray-400 dark:text-slate-500 mb-0.5">Email</dt>
                            <dd class="text-sm text-gray-700 dark:text-slate-200 break-all">{{ $datLichTienIch->cuDan->email }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @else
                <div class="p-5 text-center">
                    <p class="text-sm text-gray-400 dark:text-slate-500">Không có thông tin cư dân</p>
                </div>
                @endif
            </div>

            <!-- Căn hộ -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Căn hộ</h2>
                </div>
                @if($datLichTienIch->canHo)
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-24 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Số căn hộ</dt>
                        <dd>
                            <a href="{{ route('admin.can-ho.show', $datLichTienIch->canHo) }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $datLichTienIch->canHo->so_can_ho }}
                            </a>
                        </dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-24 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Tòa nhà</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->canHo->toaNha?->ten_toa_nha ?? '—' }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-24 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Loại căn hộ</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->canHo->loaiCanHo?->ten_loai_can_ho ?? '—' }}</dd>
                    </div>
                </dl>
                @else
                <div class="p-5 text-center">
                    <p class="text-sm text-gray-400 dark:text-slate-500">Không gắn với căn hộ cụ thể</p>
                </div>
                @endif
            </div>

            <!-- Hệ thống -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thông tin hệ thống</h2>
                </div>
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-32 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Ngày tạo</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->createdAt?->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-32 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Ngày cập nhật</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->updatedAt?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-32 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Người cập nhật</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $datLichTienIch->nguoiCapNhat?->ho_ten ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

        </div>

    </div>

    <!-- Modal xác nhận xóa -->
    <template x-teleport="body">
    <div x-show="confirmDelete"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="confirmDelete = false" @keydown.escape.window="confirmDelete = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-5">
                <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-800 dark:text-white">Xóa lịch đặt tiện ích?</p>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Mã <span class="font-medium text-gray-700 dark:text-slate-200">«{{ $datLichTienIch->ma_dat_lich }}»</span> sẽ được chuyển vào thùng rác.</p>
            </div>
            <div class="flex gap-3">
                <button @click="confirmDelete = false" type="button" class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">Hủy</button>
                <form action="{{ route('admin.dat-lich-tien-ich.destroy', $datLichTienIch) }}" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="block w-full py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">Xóa</button>
                </form>
            </div>
        </div>
    </div>
    </template>

    <!-- Modal xác nhận duyệt -->
    <template x-teleport="body">
    <div x-show="confirmApprove"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="confirmApprove = false" @keydown.escape.window="confirmApprove = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-5">
                <div class="w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="font-semibold text-gray-800 dark:text-white">Duyệt lượt đặt lịch?</p>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Mã <span class="font-medium text-gray-700 dark:text-slate-200">«{{ $datLichTienIch->ma_dat_lich }}»</span> sẽ chuyển sang trạng thái Đã duyệt.</p>
            </div>
            <div class="flex gap-3">
                <button @click="confirmApprove = false" type="button" class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">Hủy</button>
                <form action="{{ route('admin.dat-lich-tien-ich.approve', $datLichTienIch) }}" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit" class="block w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">Duyệt</button>
                </form>
            </div>
        </div>
    </div>
    </template>

    <!-- Modal từ chối -->
    <template x-teleport="body">
    <div x-show="showReject"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="showReject = false" @keydown.escape.window="showReject = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-4">
                <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-gray-500 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <p class="font-semibold text-gray-800 dark:text-white">Từ chối lượt đặt lịch?</p>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Mã <span class="font-medium text-gray-700 dark:text-slate-200">«{{ $datLichTienIch->ma_dat_lich }}»</span> sẽ chuyển sang trạng thái Từ chối.</p>
            </div>
            <form action="{{ route('admin.dat-lich-tien-ich.reject', $datLichTienIch) }}" method="POST">
                @csrf @method('PATCH')
                <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Lý do từ chối</label>
                <textarea name="ly_do" rows="3" maxlength="500" placeholder="Nhập lý do từ chối (không bắt buộc)..."
                          class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none mb-4"></textarea>
                <div class="flex gap-3">
                    <button @click="showReject = false" type="button" class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">Đóng</button>
                    <button type="submit" class="flex-1 py-2.5 bg-gray-700 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-colors">Từ chối</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    <!-- Modal hủy lịch -->
    <template x-teleport="body">
    <div x-show="showCancel"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="showCancel = false" @keydown.escape.window="showCancel = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-4">
                <div class="w-14 h-14 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <p class="font-semibold text-gray-800 dark:text-white">Hủy lượt đặt lịch?</p>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Mã <span class="font-medium text-gray-700 dark:text-slate-200">«{{ $datLichTienIch->ma_dat_lich }}»</span> sẽ chuyển sang trạng thái Đã hủy.</p>
            </div>
            <form action="{{ route('admin.dat-lich-tien-ich.cancel', $datLichTienIch) }}" method="POST">
                @csrf @method('PATCH')
                <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Lý do hủy</label>
                <textarea name="ly_do" rows="3" maxlength="500" placeholder="Nhập lý do hủy (không bắt buộc)..."
                          class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none mb-4"></textarea>
                <div class="flex gap-3">
                    <button @click="showCancel = false" type="button" class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">Đóng</button>
                    <button type="submit" class="flex-1 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl transition-colors">Hủy lịch</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
