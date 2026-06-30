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
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
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
        </div>
    </div>

    <!-- Thông tin căn hộ -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
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

    <!-- Cư dân đang ở -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">
                Cư dân đang ở ({{ $canHo->cuDanHienTai->count() }})
            </h3>
        </div>
        @if($canHo->cuDanHienTai->isEmpty())
        <div class="py-8 text-center text-sm text-gray-400 dark:text-slate-500">Chưa có cư dân</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Họ tên</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Vai trò</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Ngày chuyển đến</th>
                        <th class="px-4 py-3 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($canHo->cuDanHienTai as $pivot)
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $pivot->cuDan?->ho_ten ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $pivot->vaiTro?->vai_tro === 'Chủ hộ' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                {{ $pivot->vaiTro?->vai_tro ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-slate-400 text-xs whitespace-nowrap">
                            {{ $pivot->ngay_chuyen_den?->format('d/m/Y') ?? '—' }}
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

    <!-- Thuộc tính -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">
                Thuộc tính ({{ $canHo->thuocTinh->count() }})
            </h3>
            <a href="{{ route('admin.can-ho.edit', $canHo) }}"
               class="text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 transition-colors">
                Chỉnh sửa
            </a>
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

    <!-- Phương tiện -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">
                Phương tiện ({{ $canHo->phuongTien->count() }})
            </h3>
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Hóa đơn gần nhất -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">
                Hóa đơn ({{ $canHo->hoaDon->count() }})
            </h3>
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
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-300">
                                {{ $hd->trang_thai_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-slate-400 whitespace-nowrap">
                            {{ $hd->han_thanh_toan?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.hoa-don.show', $hd) }}"
                               class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors inline-flex">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.hoa-don.edit', $hd) }}"
                               class="p-1.5 rounded-md text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors inline-flex">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
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
