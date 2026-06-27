@extends('layouts.manager')
@section('title', 'Quản lý hóa đơn')
@section('page-title', 'Quản lý hóa đơn')

@section('content')
@php
$sortUrl = fn($col) => request()->fullUrlWithQuery([
    'sort' => $col,
    'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc',
]);
$sortIcon = function($col) use ($sort, $direction) {
    if ($sort !== $col) return '<svg class="w-3 h-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>';
    return $direction === 'asc'
        ? '<svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>'
        : '<svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
};
@endphp

<div class="space-y-4" x-data="{
    confirmToggle: null,
    openToggle(ma, action, isCancel) { this.confirmToggle = { ma, action, isCancel }; },
    closeToggle() { this.confirmToggle = null; }
}">

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    {{-- Stats Dashboard --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium">Tổng hóa đơn</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['tong']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-amber-200 shadow-sm p-4">
            <p class="text-xs text-amber-600 font-medium">Chưa thanh toán</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ number_format($stats['chua_tt']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-emerald-200 shadow-sm p-4">
            <p class="text-xs text-emerald-600 font-medium">Đã thanh toán</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ number_format($stats['da_tt']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-red-200 shadow-sm p-4">
            <p class="text-xs text-red-600 font-medium">Quá hạn</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ number_format($stats['qua_han']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-4">
            <p class="text-xs text-indigo-600 font-medium">Tổng doanh thu</p>
            <p class="text-base font-bold text-indigo-700 mt-1 tabular-nums leading-tight">
                {{ number_format($stats['doanh_thu'], 0, ',', '.') }}đ
            </p>
        </div>
        <div class="bg-white rounded-xl border border-orange-200 shadow-sm p-4">
            <p class="text-xs text-orange-600 font-medium">Tổng công nợ</p>
            <p class="text-base font-bold text-orange-700 mt-1 tabular-nums leading-tight">
                {{ number_format($stats['cong_no'], 0, ',', '.') }}đ
            </p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-gray-500">
            Hiển thị <span class="font-semibold text-gray-700">{{ $hoaDon->count() }}</span> / <span class="font-semibold text-gray-700">{{ $hoaDon->total() }}</span> hóa đơn
        </p>
        <a href="{{ route('manager.hoa-don.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tạo hóa đơn
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('manager.hoa-don.index') }}" class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-44">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Mã HĐ, căn hộ, tòa nhà, chủ hộ..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <select name="toa_nha" class="px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Tất cả tòa nhà</option>
                @foreach($dsToaNha as $tn)
                <option value="{{ $tn->id }}" {{ request('toa_nha') == $tn->id ? 'selected' : '' }}>{{ $tn->ten_toa_nha }}</option>
                @endforeach
            </select>
            <select name="thang" class="px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Tất cả tháng</option>
                @for($m=1; $m<=12; $m++)
                <option value="{{ $m }}" {{ request('thang') == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
            <input type="number" name="nam" value="{{ request('nam') }}" placeholder="Năm"
                   class="w-24 px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <select name="trang_thai" class="px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="2" {{ request('trang_thai') === '2' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="3" {{ request('trang_thai') === '3' ? 'selected' : '' }}>Quá hạn</option>
                <option value="4" {{ request('trang_thai') === '4' ? 'selected' : '' }}>Đã hủy</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">Lọc</button>
            @if(request()->hasAny(['search','toa_nha','thang','nam','trang_thai']))
            <a href="{{ route('manager.hoa-don.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors">Xóa lọc</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            <a href="{{ $sortUrl('id') }}" class="inline-flex items-center gap-1 hover:text-gray-700"># {!! $sortIcon('id') !!}</a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mã hóa đơn</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Căn hộ / Chủ hộ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            <a href="{{ $sortUrl('thang') }}" class="inline-flex items-center gap-1 hover:text-gray-700">Kỳ {!! $sortIcon('thang') !!}</a>
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                            <a href="{{ $sortUrl('tong_tien') }}" class="inline-flex items-center gap-1 justify-end hover:text-gray-700">Tổng tiền {!! $sortIcon('tong_tien') !!}</a>
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Còn nợ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            <a href="{{ $sortUrl('han_thanh_toan') }}" class="inline-flex items-center gap-1 hover:text-gray-700">Hạn TT {!! $sortIcon('han_thanh_toan') !!}</a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                            <a href="{{ $sortUrl('createdAt') }}" class="inline-flex items-center gap-1 justify-end hover:text-gray-700">Ngày tạo {!! $sortIcon('createdAt') !!}</a>
                        </th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($hoaDon as $hd)
                    @php
                        $conNo = max(0, ($hd->tong_tien ?? 0) - ($hd->so_tien_da_thanh_toan ?? 0));
                        $statusCls = match($hd->trang_thai) {
                            1 => 'bg-amber-100 text-amber-700',
                            2 => 'bg-emerald-100 text-emerald-700',
                            3 => 'bg-red-100 text-red-700',
                            4 => 'bg-gray-100 text-gray-500',
                            default => 'bg-gray-100 text-gray-500',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5 text-xs text-gray-400 font-mono">{{ $hd->id }}</td>
                        <td class="px-4 py-3.5 font-mono font-semibold text-indigo-600 whitespace-nowrap text-xs">
                            {{ $hd->ma_thanh_toan }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <div>
                                <span class="text-sm font-medium text-gray-800">{{ $hd->canHo?->so_can_ho ?? '—' }}</span>
                                <span class="text-xs text-gray-400 ml-1">{{ $hd->canHo?->toaNha?->ten_toa_nha }}</span>
                                @if($hd->canHo?->chuHo?->cuDan)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $hd->canHo->chuHo->cuDan->ho_ten }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 whitespace-nowrap">{{ $hd->thang }}/{{ $hd->nam }}</td>
                        <td class="px-4 py-3.5 text-right font-semibold text-gray-800 whitespace-nowrap tabular-nums">
                            {{ number_format($hd->tong_tien ?? 0, 0, ',', '.') }}đ
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap tabular-nums">
                            @if($conNo > 0)
                            <span class="text-red-600 font-medium">{{ number_format($conNo, 0, ',', '.') }}đ</span>
                            @else
                            <span class="text-emerald-600 font-medium">Đủ</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 whitespace-nowrap text-xs">
                            {{ $hd->han_thanh_toan?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusCls }}">
                                {{ $hd->trang_thai_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-400 whitespace-nowrap text-right">
                            {{ $hd->createdAt?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="{{ route('manager.hoa-don.show', $hd) }}" title="Xem chi tiết"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('manager.hoa-don.edit', $hd) }}" title="Chỉnh sửa"
                                   class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @if(in_array($hd->trang_thai, [1, 3, 4]))
                                <button type="button"
                                        title="{{ $hd->trang_thai == 4 ? 'Khôi phục' : 'Hủy hóa đơn' }}"
                                        @click="openToggle('{{ addslashes($hd->ma_thanh_toan) }}', '{{ route('manager.hoa-don.toggle-status', $hd) }}', {{ $hd->trang_thai != 4 ? 'true' : 'false' }})"
                                        class="p-1.5 rounded-md transition-colors {{ $hd->trang_thai == 4 ? 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50' : 'text-gray-400 hover:text-red-600 hover:bg-red-50' }}">
                                    @if($hd->trang_thai == 4)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    @endif
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-14 text-center">
                            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm text-gray-400">Không tìm thấy hóa đơn nào</p>
                            @if(request()->hasAny(['search','toa_nha','thang','nam','trang_thai']))
                            <a href="{{ route('manager.hoa-don.index') }}" class="mt-2 inline-block text-sm text-indigo-600 hover:underline">Xóa bộ lọc</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($hoaDon->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">{{ $hoaDon->links() }}</div>
        @endif
    </div>

    {{-- Modal toggle --}}
    <template x-teleport="body">
    <div x-show="confirmToggle !== null"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="closeToggle()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden" @click.stop>
            <div class="flex justify-end px-4 pt-4">
                <button @click="closeToggle()" class="w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div x-show="confirmToggle?.isCancel" class="px-6 pt-2 pb-5 text-center">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900">Hủy hóa đơn</h3>
                <p class="text-sm text-gray-500 mt-1">Hóa đơn sẽ được chuyển sang trạng thái đã hủy.</p>
            </div>
            <div x-show="confirmToggle && !confirmToggle.isCancel" class="px-6 pt-2 pb-5 text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900">Khôi phục hóa đơn</h3>
                <p class="text-sm text-gray-500 mt-1">Hóa đơn sẽ được khôi phục về trạng thái chưa thanh toán.</p>
            </div>
            <div class="mx-6 mb-5 flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400">Mã hóa đơn</p>
                    <p class="text-sm font-semibold text-gray-800 font-mono truncate" x-text="confirmToggle?.ma"></p>
                </div>
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button @click="closeToggle()" class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">Hủy bỏ</button>
                <form :action="confirmToggle?.action" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    <button x-show="confirmToggle?.isCancel" type="submit" class="block w-full py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">Hủy hóa đơn</button>
                    <button x-show="confirmToggle && !confirmToggle.isCancel" type="submit" class="block w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">Khôi phục</button>
                </form>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection
