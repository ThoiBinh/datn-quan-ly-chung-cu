@extends('layouts.admin')
@section('title', 'Quản lý hóa đơn')
@section('page-title', 'Quản lý hóa đơn')

@section('content')
@php
$sortUrl = fn($col) => request()->fullUrlWithQuery([
    'sort' => $col,
    'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc',
]);
$sortIcon = function($col) use ($sort, $direction) {
    if ($sort !== $col) return '<svg class="w-3.5 h-3.5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>';
    return $direction === 'asc'
        ? '<svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>'
        : '<svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
};
$statusBadge = function($tt) {
    return match($tt) {
        1 => '<span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-600 dark:text-amber-400"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Chưa TT</span>',
        2 => '<span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Đã TT</span>',
        3 => '<span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-600 dark:text-red-400"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Trễ hạn</span>',
        default => '<span class="text-gray-400">—</span>',
    };
};
@endphp

<div class="space-y-4">

    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm rounded-xl px-4 py-3">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm rounded-xl px-4 py-3">
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats Dashboard --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
            <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">Tổng hóa đơn</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['tong']) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-amber-200 dark:border-amber-800/50 shadow-sm p-4">
            <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">Chưa thanh toán</p>
            <p class="text-2xl font-bold text-amber-700 dark:text-amber-400 mt-1">{{ number_format($stats['chua_tt']) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-emerald-200 dark:border-emerald-800/50 shadow-sm p-4">
            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Đã thanh toán</p>
            <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400 mt-1">{{ number_format($stats['da_tt']) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-red-200 dark:border-red-800/50 shadow-sm p-4">
            <p class="text-xs text-red-600 dark:text-red-400 font-medium">Quá hạn</p>
            <p class="text-2xl font-bold text-red-700 dark:text-red-400 mt-1">{{ number_format($stats['qua_han']) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
            <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">Tổng doanh thu</p>
            <p class="text-lg font-bold text-violet-700 dark:text-violet-400 mt-1 tabular-nums leading-tight">
                {{ number_format($stats['doanh_thu'], 0, ',', '.') }}đ
            </p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-orange-200 dark:border-orange-800/50 shadow-sm p-4">
            <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">Tổng công nợ</p>
            <p class="text-lg font-bold text-orange-700 dark:text-orange-400 mt-1 tabular-nums leading-tight">
                {{ number_format($stats['cong_no'], 0, ',', '.') }}đ
            </p>
        </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                Hiển thị <span class="font-semibold text-gray-700 dark:text-slate-200">{{ $hoaDon->count() }}</span> / <span class="font-semibold text-gray-700 dark:text-slate-200">{{ $hoaDon->total() }}</span> hóa đơn
            </p>
        </div>
        <a href="{{ route('admin.hoa-don.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tạo hóa đơn
        </a>
    </div>

    <!-- Filter bar -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.hoa-don.index') }}" class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Mã HĐ, căn hộ, tòa nhà, chủ hộ..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400">
            </div>
            <select name="toa_nha" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-400">
                <option value="">Tất cả tòa nhà</option>
                @foreach($dsToaNha as $tn)
                <option value="{{ $tn->id }}" {{ request('toa_nha') == $tn->id ? 'selected' : '' }}>{{ $tn->ten_toa_nha }}</option>
                @endforeach
            </select>
            <select name="thang" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-400">
                <option value="">Tất cả tháng</option>
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ request('thang') == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
            <input type="number" name="nam" value="{{ request('nam') }}" placeholder="Năm"
                   class="w-24 px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400">
            <select name="trang_thai" class="px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-400">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="2" {{ request('trang_thai') === '2' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="3" {{ request('trang_thai') === '3' ? 'selected' : '' }}>Trễ hạn</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-colors">
                Lọc
            </button>
            @if(request()->hasAny(['search','toa_nha','thang','nam','trang_thai']))
            <a href="{{ route('admin.hoa-don.index') }}" class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Xóa lọc
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('id') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-slate-200">
                                # {!! $sortIcon('id') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Mã hóa đơn</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Căn hộ / Chủ hộ</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('thang') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-slate-200">
                                Kỳ thu {!! $sortIcon('thang') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('tong_tien') }}" class="inline-flex items-center gap-1 justify-end hover:text-gray-700 dark:hover:text-slate-200">
                                Tổng tiền {!! $sortIcon('tong_tien') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Còn nợ</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('han_thanh_toan') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-slate-200">
                                Hạn TT {!! $sortIcon('han_thanh_toan') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Trạng thái</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">
                            <a href="{{ $sortUrl('createdAt') }}" class="inline-flex items-center gap-1 justify-end hover:text-gray-700 dark:hover:text-slate-200">
                                Ngày tạo {!! $sortIcon('createdAt') !!}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide text-xs">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    @forelse($hoaDon as $hd)
                    @php $conNo = max(0, ($hd->tong_tien ?? 0) - ($hd->so_tien_da_thanh_toan ?? 0)); @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3.5 text-xs text-gray-400 dark:text-slate-500 font-mono">{{ $hd->id }}</td>
                        <td class="px-4 py-3.5 font-mono font-semibold text-violet-600 dark:text-violet-400 whitespace-nowrap text-xs">
                            {{ $hd->ma_thanh_toan }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($hd->canHo)
                            <div>
                                <a href="{{ route('admin.can-ho.show', $hd->canHo) }}"
                                   class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $hd->canHo->so_can_ho }}
                                </a>
                                <p class="text-xs text-gray-400 dark:text-slate-500">{{ $hd->canHo->toaNha?->ten_toa_nha ?? '—' }}</p>
                                @if($hd->canHo->chuHo?->cuDan)
                                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $hd->canHo->chuHo->cuDan->ho_ten }}</p>
                                @endif
                            </div>
                            @else
                            <span class="text-gray-400 dark:text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-slate-300 whitespace-nowrap text-sm">
                            Tháng {{ $hd->thang }}/{{ $hd->nam }}
                        </td>
                        <td class="px-4 py-3.5 text-right font-semibold text-gray-800 dark:text-slate-100 whitespace-nowrap tabular-nums">
                            {{ number_format($hd->tong_tien ?? 0, 0, ',', '.') }}đ
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap tabular-nums">
                            @if($conNo > 0)
                            <span class="text-red-600 dark:text-red-400 font-medium">{{ number_format($conNo, 0, ',', '.') }}đ</span>
                            @else
                            <span class="text-emerald-600 dark:text-emerald-400 font-medium">Đủ</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 dark:text-slate-400 whitespace-nowrap text-xs">
                            {{ $hd->han_thanh_toan?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            {!! $statusBadge($hd->trang_thai) !!}
                            
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-400 dark:text-slate-500 whitespace-nowrap text-right">
                            {{ $hd->createdAt?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="{{ route('admin.hoa-don.show', $hd) }}" title="Xem chi tiết"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($hd->lich_su_thanh_toan_count > 0)
                                <span title="Không thể sửa hóa đơn đã phát sinh thanh toán.">
                                    <button type="button" disabled
                                            class="p-1.5 rounded-md text-gray-300 dark:text-slate-600 opacity-60 cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </span>
                                @else
                                <a href="{{ route('admin.hoa-don.edit', $hd) }}" title="Chỉnh sửa"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-14 text-center">
                            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm text-gray-400 dark:text-slate-500">Không tìm thấy hóa đơn nào</p>
                            @if(request()->hasAny(['search','toa_nha','thang','nam','trang_thai']))
                            <a href="{{ route('admin.hoa-don.index') }}" class="mt-2 inline-block text-sm text-violet-600 hover:underline">Xóa bộ lọc</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($hoaDon->hasPages())
        <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $hoaDon->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
