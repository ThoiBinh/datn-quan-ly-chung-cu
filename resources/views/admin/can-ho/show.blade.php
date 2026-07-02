@extends('layouts.admin')
@section('title', 'Chi tiết căn hộ')
@section('page-title', 'Chi tiết căn hộ')

@section('content')
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl shadow-lg">
    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span class="text-sm font-medium">{{ session('success') }}</span>
</div>
@endif

@php
    // Chủ hộ — suy ra từ collection cuDanCanHo đã eager-load (không query mới), cùng điều kiện với CanHo::chuHo()
    $chuHoPivot = $canHo->cuDanCanHo->first(fn($p) => $p->trang_thai == 1 && $p->vaiTro?->vai_tro === 'Chủ hộ');

    // Thống kê nhanh — tính từ các relationship đã được Controller eager-load, không thêm query
    $soCuDanHienTai   = $canHo->cuDanCanHo->where('trang_thai', 1)->count();
    $tongDuNo         = $canHo->hoaDon->sum(fn($hd) => $hd->conNo());
    $tongDaThanhToan  = $canHo->hoaDon->sum('so_tien_da_thanh_toan');
    $dsLichSuThanhToan = $canHo->hoaDon->flatMap->lichSuThanhToan->sortByDesc('ngay_thanh_toan');

    // Quick Action — chỉ hiển thị nếu route tương ứng tồn tại
    $quickActions = collect([
        ['label' => 'Thêm cư dân', 'route' => 'admin.cu-dan-can-ho.create', 'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
        ['label' => 'Thêm phương tiện', 'route' => 'admin.phuong-tien.create', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        ['label' => 'Tạo hóa đơn', 'route' => 'admin.hoa-don.create', 'icon' => 'M12 4v16m8-8H4'],
        ['label' => 'Thêm dịch vụ', 'route' => 'admin.can-ho-phi-dich-vu.create', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ])->filter(fn($a) => \Illuminate\Support\Facades\Route::has($a['route']));
@endphp

<div class="space-y-5">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.toa-nha.index') }}" class="hover:text-emerald-600 transition-colors">Tòa nhà</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @if($canHo->toaNha)
        <a href="{{ route('admin.toa-nha.show', $canHo->toaNha) }}" class="hover:text-emerald-600 transition-colors">{{ $canHo->toaNha->ten_toa_nha }}</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @endif
        <a href="{{ route('admin.can-ho.index') }}" class="hover:text-emerald-600 transition-colors">Căn hộ</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200 font-mono font-semibold">{{ $canHo->so_can_ho }}</span>
    </nav>

    <!-- Header card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white font-mono">{{ $canHo->so_can_ho }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        {{ $canHo->trangThai?->ten_trang_thai ?? '—' }}
                    </span>
                    @if($canHo->loaiCanHo)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300">
                        {{ $canHo->loaiCanHo->ten_loai_can_ho }}
                    </span>
                    @endif
                    @if($chuHoPivot?->cuDan)
                    <a href="{{ route('admin.cu-dan.show', $chuHoPivot->cuDan) }}"
                       class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Chủ hộ: {{ $chuHoPivot->cuDan->ho_ten }}
                    </a>
                    @endif
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400">
                    {{ $canHo->toaNha?->ten_toa_nha ?? '—' }} • Tầng {{ $canHo->tang }}
                    @if($canHo->gia)
                     • <span class="text-emerald-600 font-medium">{{ number_format($canHo->gia, 0, ',', '.') }} ₫</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex flex-wrap gap-3">
            <a href="{{ route('admin.can-ho.edit', $canHo) }}"
               class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('admin.can-ho.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
            <form method="POST" action="{{ route('admin.can-ho.destroy', $canHo) }}" class="ml-auto"
                  onsubmit="return confirm('Căn hộ «{{ addslashes($canHo->so_can_ho) }}» sẽ được chuyển sang trạng thái Trống nếu chưa phát sinh dữ liệu. Tiếp tục?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Xóa
                </button>
            </form>
        </div>

        @if($quickActions->isNotEmpty())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700">
            <p class="text-xs font-semibold text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-3">Thao tác nhanh</p>
            <div class="flex flex-wrap gap-2.5">
                @foreach($quickActions as $qa)
                <a href="{{ route($qa['route']) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-200 dark:border-slate-700 text-sm font-medium text-gray-700 dark:text-slate-200 hover:border-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors duration-200">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $qa['icon'] }}"/>
                    </svg>
                    {{ $qa['label'] }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Thống kê nhanh -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
            <p class="text-xs text-gray-500 dark:text-slate-400 mb-1">Cư dân hiện tại</p>
            <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ $soCuDanHienTai }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
            <p class="text-xs text-gray-500 dark:text-slate-400 mb-1">Phương tiện</p>
            <p class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $canHo->phuongTien->count() }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
            <p class="text-xs text-gray-500 dark:text-slate-400 mb-1">Dịch vụ đăng ký</p>
            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $canHo->canHoPhiDichVu->count() }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
            <p class="text-xs text-gray-500 dark:text-slate-400 mb-1">Tổng dư nợ</p>
            <p class="text-xl font-bold {{ $tongDuNo > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                {{ $tongDuNo > 0 ? number_format($tongDuNo, 0, ',', '.') . '₫' : '0₫' }}
            </p>
        </div>
    </div>

    <!-- Thông tin căn hộ -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Thông tin căn hộ</h3>
        </div>
        <div class="p-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['ID', $canHo->id, true],
                ['Tòa nhà', $canHo->toaNha?->ten_toa_nha, false],
                ['Tầng', $canHo->tang, false],
                ['Loại', $canHo->loaiCanHo?->ten_loai_can_ho, false],
                ['Trạng thái', $canHo->trangThai?->ten_trang_thai, false],
                ['Giá', $canHo->gia ? number_format($canHo->gia, 0, ',', '.') . ' ₫' : null, false],
                ['Ngày tạo', $canHo->createdAt?->format('d/m/Y'), false],
                ['Cập nhật', $canHo->updatedAt?->format('d/m/Y'), false],
            ] as [$label, $value, $mono])
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">{{ $label }}</dt>
                <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200 {{ $mono ? 'font-mono' : '' }}">{{ $value ?: '—' }}</dd>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Thông tin tòa nhà -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Thông tin tòa nhà</h3>
        </div>
        @if($canHo->toaNha)
        <div class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">Tên tòa nhà</dt>
                <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200">
                    <a href="{{ route('admin.toa-nha.show', $canHo->toaNha) }}" class="hover:text-emerald-600 transition-colors">{{ $canHo->toaNha->ten_toa_nha }}</a>
                </dd>
            </div>
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">Địa chỉ</dt>
                <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200">{{ $canHo->toaNha->dia_chi ?: '—' }}</dd>
            </div>
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">Số tầng</dt>
                <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200">{{ $canHo->toaNha->so_tang }}</dd>
            </div>
        </div>
        @else
        <div class="py-8 text-center text-sm text-gray-400 dark:text-slate-500">Không có thông tin tòa nhà</div>
        @endif
    </div>

    <!-- Loại căn hộ -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Loại căn hộ</h3>
        </div>
        @if($canHo->loaiCanHo)
        <div class="p-6">
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600 max-w-xs">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">Tên loại căn hộ</dt>
                <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200">{{ $canHo->loaiCanHo->ten_loai_can_ho }}</dd>
            </div>
        </div>
        @else
        <div class="py-8 text-center text-sm text-gray-400 dark:text-slate-500">Không có thông tin loại căn hộ</div>
        @endif
    </div>

    <!-- Dữ liệu liên quan — Tabs dựa trên Relationship -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm" x-data="{ tab: 'cudan' }">
        <div class="px-4 border-b border-gray-100 dark:border-slate-700 overflow-x-auto">
            <nav class="flex gap-1 min-w-max">
                @php
                    $tabs = [
                        ['key' => 'cudan',     'label' => 'Cư dân',      'count' => $canHo->cuDanCanHo->count()],
                        ['key' => 'thuoctinh', 'label' => 'Thuộc tính',  'count' => $canHo->thuocTinh->count()],
                        ['key' => 'phuongtien','label' => 'Phương tiện','count' => $canHo->phuongTien->count()],
                        ['key' => 'dichvu',    'label' => 'Dịch vụ',     'count' => $canHo->canHoPhiDichVu->count()],
                        ['key' => 'hoadon',    'label' => 'Hóa đơn',     'count' => $canHo->hoaDon->count()],
                        ['key' => 'thanhtoan', 'label' => 'Thanh toán',  'count' => $dsLichSuThanhToan->count()],
                    ];
                @endphp
                @foreach($tabs as $t)
                <button type="button" @click="tab = '{{ $t['key'] }}'"
                        :class="tab === '{{ $t['key'] }}' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200'"
                        class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 transition-colors duration-200 whitespace-nowrap">
                    {{ $t['label'] }}
                    <span :class="tab === '{{ $t['key'] }}' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-400'"
                          class="px-1.5 py-0.5 rounded-full text-xs font-semibold">{{ $t['count'] }}</span>
                </button>
                @endforeach
            </nav>
        </div>

        <!-- Tab: Cư dân -->
        <div x-show="tab === 'cudan'" x-cloak>
            <div class="px-6 py-4 flex items-center justify-between">
                <p class="text-xs text-gray-400 dark:text-slate-500">Danh sách cư dân từng/đang cư trú tại căn hộ này</p>
                @if(\Illuminate\Support\Facades\Route::has('admin.cu-dan-can-ho.index'))
                <a href="{{ route('admin.cu-dan-can-ho.index', ['can_ho' => $canHo->id]) }}" class="text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 font-medium flex-shrink-0">Xem tất cả</a>
                @endif
            </div>
            @if($canHo->cuDanCanHo->isEmpty())
            <div class="py-8 text-center text-sm text-gray-400 dark:text-slate-500">Chưa có cư dân</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Họ tên</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">CCCD</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Email</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Điện thoại</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Vai trò</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Ngày bắt đầu ở</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Ngày kết thúc</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-4 py-3 w-16"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($canHo->cuDanCanHo->sortByDesc('ngay_chuyen_den') as $pivot)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $pivot->cuDan?->ho_ten ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-slate-300">{{ $pivot->cuDan?->cccd ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-slate-300 text-xs">{{ $pivot->cuDan?->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-slate-300 text-xs whitespace-nowrap">{{ $pivot->cuDan?->sdt ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $pivot->vaiTro?->vai_tro === 'Chủ hộ' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                    {{ $pivot->vaiTro?->vai_tro ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs whitespace-nowrap">
                                {{ $pivot->ngay_chuyen_den?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs whitespace-nowrap">
                                {{ $pivot->ngay_chuyen_di?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $pivot->trang_thai == 1 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-400' }}">
                                    {{ $pivot->trang_thai == 1 ? 'Đang cư trú' : 'Đã chuyển đi' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($pivot->cuDan)
                                <a href="{{ route('admin.cu-dan.show', $pivot->cuDan) }}"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors inline-flex">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Tab: Thuộc tính -->
        <div x-show="tab === 'thuoctinh'" x-cloak>
            <div class="px-6 py-4 flex items-center justify-between">
                <p class="text-xs text-gray-400 dark:text-slate-500">Thuộc tính mở rộng của căn hộ</p>
                <a href="{{ route('admin.can-ho.edit', $canHo) }}" class="text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 font-medium flex-shrink-0">Chỉnh sửa</a>
            </div>
            @if($canHo->thuocTinh->isEmpty())
            <div class="py-8 text-center text-sm text-gray-400 dark:text-slate-500">Chưa có thuộc tính nào</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tên thuộc tính</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Giá trị</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Kiểu dữ liệu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($canHo->thuocTinh as $tt)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $tt->ten_thuoc_tinh }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-slate-200">
                                <span class="px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 text-xs font-medium">
                                    {{ $tt->pivot->gia_tri_thuoc_tinh ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs">
                                @switch($tt->pivot->kieu_du_lieu)
                                    @case(1) Số nguyên @break
                                    @case(3) Ngày giờ @break
                                    @default Văn bản
                                @endswitch
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Tab: Phương tiện -->
        <div x-show="tab === 'phuongtien'" x-cloak>
            <div class="px-6 py-4 flex items-center justify-between">
                <p class="text-xs text-gray-400 dark:text-slate-500">Phương tiện đăng ký gửi tại căn hộ</p>
                @if(\Illuminate\Support\Facades\Route::has('admin.phuong-tien.index'))
                <a href="{{ route('admin.phuong-tien.index', ['can_ho' => $canHo->id]) }}" class="text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 font-medium flex-shrink-0">Xem tất cả</a>
                @endif
            </div>
            @if($canHo->phuongTien->isEmpty())
            <div class="py-8 text-center text-sm text-gray-400 dark:text-slate-500">Chưa có phương tiện</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tên</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Biển số</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Loại</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Ngày ĐK</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($canHo->phuongTien as $pt)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-4 py-3 text-gray-700 dark:text-slate-200">{{ $pt->ten_phuong_tien ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono font-semibold text-gray-800 dark:text-white">{{ $pt->bien_so ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs">{{ $pt->loaiPhuongTien?->ten_loai_phuong_tien ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs whitespace-nowrap">
                                {{ $pt->ngay_dang_ky?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pt->trang_thai_label['class'] }}">
                                    {{ $pt->trang_thai_label['text'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Tab: Dịch vụ -->
        <div x-show="tab === 'dichvu'" x-cloak>
            <div class="px-6 py-4 flex items-center justify-between">
                <p class="text-xs text-gray-400 dark:text-slate-500">Dịch vụ đã đăng ký cho căn hộ</p>
                @if(\Illuminate\Support\Facades\Route::has('admin.can-ho-phi-dich-vu.index'))
                <a href="{{ route('admin.can-ho-phi-dich-vu.index', ['can_ho' => $canHo->id]) }}" class="text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 font-medium flex-shrink-0">Xem tất cả</a>
                @endif
            </div>
            @if($canHo->canHoPhiDichVu->isEmpty())
            <div class="py-8 text-center text-sm text-gray-400 dark:text-slate-500">Chưa đăng ký dịch vụ nào</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tên dịch vụ</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Loại phí</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Đơn vị tính</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Loại tính phí</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Đơn giá áp dụng</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($canHo->canHoPhiDichVu as $chpdv)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $chpdv->phiDichVu?->ten_phi_dich_vu ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs">{{ $chpdv->phiDichVu?->loaiPhiDichVu?->ten_loai_phi_dich_vu ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs">{{ $chpdv->phiDichVu?->donViTinh?->don_vi ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs">{{ $chpdv->phiDichVu?->loaiTinhPhi?->ten_loai ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-slate-200">
                                {{ number_format($chpdv->don_gia, 0, ',', '.') }}&nbsp;₫
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Tab: Hóa đơn -->
        <div x-show="tab === 'hoadon'" x-cloak>
            <div class="px-6 py-4 flex items-center justify-between">
                <p class="text-xs text-gray-400 dark:text-slate-500">Hóa đơn phát sinh cho căn hộ (tối đa 20 gần nhất)</p>
                @if(\Illuminate\Support\Facades\Route::has('admin.hoa-don.create'))
                <a href="{{ route('admin.hoa-don.create') }}" class="text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 font-medium flex-shrink-0">+ Tạo hóa đơn</a>
                @endif
            </div>
            @if($canHo->hoaDon->isEmpty())
            <div class="py-8 text-center text-sm text-gray-400 dark:text-slate-500">Chưa có hóa đơn</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Mã</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Kỳ</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tổng tiền</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Đã thanh toán</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Còn nợ</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Trạng thái</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Hạn TT</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($canHo->hoaDon->sortByDesc('nam')->sortByDesc('thang')->take(20) as $hd)
                        @php
                            $ttColors = [1 => 'amber', 2 => 'emerald', 3 => 'red', 4 => 'gray'];
                            $color = $ttColors[$hd->trang_thai] ?? 'gray';
                        @endphp
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-slate-300">{{ $hd->ma_thanh_toan ?? '#'.$hd->id }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-slate-300 text-xs whitespace-nowrap">T{{ $hd->thang }}/{{ $hd->nam }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-700 dark:text-slate-200 whitespace-nowrap">
                                {{ number_format($hd->tong_tien, 0, ',', '.') }}&nbsp;₫
                            </td>
                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                {{ number_format($hd->so_tien_da_thanh_toan, 0, ',', '.') }}&nbsp;₫
                            </td>
                            <td class="px-4 py-3 text-right font-semibold whitespace-nowrap {{ $hd->conNo() > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-slate-500' }}">
                                {{ number_format($hd->conNo(), 0, ',', '.') }}&nbsp;₫
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-300">
                                    {{ $hd->trang_thai_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $hd->han_thanh_toan?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <a href="{{ route('admin.hoa-don.show', $hd) }}"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors inline-flex">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.hoa-don.edit', $hd) }}"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors inline-flex">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Tab: Thanh toán -->
        <div x-show="tab === 'thanhtoan'" x-cloak>
            <div class="px-6 py-4 flex items-center justify-between">
                <p class="text-xs text-gray-400 dark:text-slate-500">
                    Tổng đã thanh toán:
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($tongDaThanhToan, 0, ',', '.') }}&nbsp;₫</span>
                </p>
                @if(\Illuminate\Support\Facades\Route::has('admin.thanh-toan.index'))
                <a href="{{ route('admin.thanh-toan.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 font-medium flex-shrink-0">Xem tất cả giao dịch</a>
                @endif
            </div>
            @if($dsLichSuThanhToan->isEmpty())
            <div class="py-8 text-center text-sm text-gray-400 dark:text-slate-500">Chưa có lịch sử thanh toán</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Mã giao dịch</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Ngày thanh toán</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Số tiền</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Phương thức</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Người thanh toán</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($dsLichSuThanhToan as $ls)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-slate-300">{{ $ls->ma_giao_dich ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs whitespace-nowrap">
                                {{ $ls->ngay_thanh_toan?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                {{ number_format($ls->so_tien, 0, ',', '.') }}&nbsp;₫
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs">{{ $ls->phuong_thuc_thanh_toan ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs">{{ $ls->nguoiThanhToan?->ho_ten ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
