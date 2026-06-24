@extends('layouts.admin')
@section('title', 'Chi tiết phương tiện')
@section('page-title', 'Chi tiết phương tiện')

@section('content')
@php
$isActive = $phuongTien->trang_thai == 1;
@endphp

<div class="space-y-5" x-data="{ confirmToggle: false }">

    <!-- Breadcrumb + actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
            <a href="{{ route('admin.phuong-tien.index') }}" class="hover:text-orange-600 transition-colors">Phương tiện</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="font-mono font-semibold text-gray-700 dark:text-slate-200">{{ $phuongTien->bien_so ?: '#'.$phuongTien->id }}</span>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.phuong-tien.edit', $phuongTien) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            @if($isActive)
            <button @click="confirmToggle = true" type="button"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Khóa phương tiện
            </button>
            @else
            <button @click="confirmToggle = true" type="button"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                Mở khóa
            </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Cột chính -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Thông tin phương tiện -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                            <svg class="w-4.5 h-4.5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thông tin phương tiện</h2>
                    </div>
                    @if($isActive)
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-500 dark:text-red-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Đã khóa
                    </span>
                    @endif
                </div>
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-40 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Biển số</dt>
                        <dd class="font-mono font-bold text-lg text-orange-600 dark:text-orange-400">{{ $phuongTien->bien_so ?: '—' }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-40 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Tên phương tiện</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $phuongTien->ten_phuong_tien ?: '—' }}</dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-40 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Loại</dt>
                        <dd>
                            @if($phuongTien->loaiPhuongTien)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                                {{ $phuongTien->loaiPhuongTien->ten_loai_phuong_tien }}
                            </span>
                            @else
                            <span class="text-gray-400 dark:text-slate-500 text-sm">—</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-40 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Ngày đăng ký</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $phuongTien->ngay_dang_ky?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    @if($phuongTien->ngay_huy)
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-40 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Ngày hủy</dt>
                        <dd class="text-sm text-red-600 dark:text-red-400">{{ $phuongTien->ngay_huy->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Thông tin căn hộ -->
            @if($phuongTien->canHo)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thông tin căn hộ</h2>
                </div>
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-40 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Số căn hộ</dt>
                        <dd>
                            <a href="{{ route('admin.can-ho.show', $phuongTien->canHo) }}"
                               class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $phuongTien->canHo->so_can_ho }}
                            </a>
                        </dd>
                    </div>
                    @if(isset($phuongTien->canHo->tang))
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-40 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Tầng</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $phuongTien->canHo->tang }}</dd>
                    </div>
                    @endif
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-40 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Tòa nhà</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $phuongTien->canHo->toaNha?->ten_toa_nha ?? '—' }}</dd>
                    </div>
                    @if($phuongTien->canHo->toaNha?->dia_chi)
                    <div class="flex items-baseline px-5 py-3.5">
                        <dt class="w-40 text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">Địa chỉ</dt>
                        <dd class="text-sm text-gray-800 dark:text-slate-100">{{ $phuongTien->canHo->toaNha->dia_chi }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

        </div>

        <!-- Cột phụ: Chủ sở hữu -->
        <div class="space-y-5">
            @php $chuHo = $phuongTien->canHo?->chuHo?->cuDan; @endphp
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Chủ sở hữu (Chủ hộ)</h2>
                </div>
                @if($chuHo)
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                {{ strtoupper(substr($chuHo->ho_ten ?? '?', 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $chuHo->ho_ten }}</p>
                            <p class="text-xs text-gray-400 dark:text-slate-500">Chủ hộ</p>
                        </div>
                    </div>
                    <dl class="space-y-2.5">
                        @if($chuHo->sdt)
                        <div>
                            <dt class="text-xs text-gray-400 dark:text-slate-500 mb-0.5">Điện thoại</dt>
                            <dd class="text-sm text-gray-700 dark:text-slate-200">{{ $chuHo->sdt }}</dd>
                        </div>
                        @endif
                        @if($chuHo->email)
                        <div>
                            <dt class="text-xs text-gray-400 dark:text-slate-500 mb-0.5">Email</dt>
                            <dd class="text-sm text-gray-700 dark:text-slate-200 break-all">{{ $chuHo->email }}</dd>
                        </div>
                        @endif
                        @if($chuHo->cccd)
                        <div>
                            <dt class="text-xs text-gray-400 dark:text-slate-500 mb-0.5">CCCD</dt>
                            <dd class="text-sm text-gray-700 dark:text-slate-200 font-mono">{{ $chuHo->cccd }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @else
                <div class="p-5 text-center">
                    <p class="text-sm text-gray-400 dark:text-slate-500">Chưa có thông tin chủ hộ</p>
                </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Modal xác nhận toggle -->
    <template x-teleport="body">
    <div x-show="confirmToggle"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="confirmToggle = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden" @click.stop>

            <!-- Close -->
            <div class="flex justify-end px-4 pt-4">
                <button @click="confirmToggle = false" type="button"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Icon + title -->
            <div class="px-6 pt-2 pb-5 text-center">
                @if($isActive)
                <div class="bg-amber-100 dark:bg-amber-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Khóa phương tiện</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Phương tiện sẽ bị vô hiệu hóa trong hệ thống.</p>
                @else
                <div class="bg-emerald-100 dark:bg-emerald-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Mở khóa phương tiện</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Phương tiện sẽ được kích hoạt trở lại.</p>
                @endif
            </div>

            <!-- Info card: biển số -->
            <div class="mx-6 mb-5 flex items-center gap-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                <div class="w-9 h-9 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 dark:text-slate-500">Biển số</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white font-mono truncate">{{ $phuongTien->bien_so }}</p>
                </div>
            </div>

            <!-- Buttons: form dùng flex-1 (block), button dùng block w-full — không dùng display:contents -->
            <div class="flex gap-3 px-6 pb-6">
                <button @click="confirmToggle = false" type="button"
                        class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy bỏ
                </button>
                <form action="{{ route('admin.phuong-tien.toggle-status', $phuongTien) }}" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    @if($isActive)
                    <button type="submit" class="block w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-gray-900 text-sm font-semibold rounded-xl transition-colors">
                        Khóa phương tiện
                    </button>
                    @else
                    <button type="submit" class="block w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">
                        Mở khóa
                    </button>
                    @endif
                </form>
            </div>

        </div>
    </div>
    </template>

</div>
@endsection
