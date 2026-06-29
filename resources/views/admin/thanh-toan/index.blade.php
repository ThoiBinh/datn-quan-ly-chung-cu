@extends('layouts.admin')
@section('title', 'Lịch sử thanh toán')
@section('page-title', 'Lịch sử thanh toán')

@section('content')
<div class="space-y-5">

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Lịch sử thanh toán</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Tổng doanh thu: <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($tongDoanhThu ?? 0, 0, ',', '.') }}đ</span></p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.thanh-toan.index') }}" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1.5">Phương thức</label>
                <input type="text" name="phuong_thuc" value="{{ request('phuong_thuc') }}" placeholder="Tìm phương thức..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1.5">Từ ngày</label>
                <input type="date" name="tu_ngay" value="{{ request('tu_ngay') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1.5">Đến ngày</label>
                <input type="date" name="den_ngay" value="{{ request('den_ngay') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">Lọc</button>
                @if(request()->hasAny(['phuong_thuc','tu_ngay','den_ngay','hoa_don_id']))
                <a href="{{ route('admin.thanh-toan.index') }}" class="py-2 px-3 border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">Xóa lọc</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        @if($lichSuList->isNotEmpty())
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 w-10">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Hóa đơn</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Căn hộ</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Chủ hộ</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Ngày TT</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400">Số tiền</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Phương thức</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Nguồn</th>
                    <th class="px-4 py-3 w-12"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                @foreach($lichSuList as $idx => $ls)
                @php
                $canHo = $ls->hoaDon?->canHo;
                $chuHo = $canHo?->chuHo?->cuDan;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3 text-gray-400 dark:text-slate-500 text-xs">{{ $lichSuList->firstItem() + $idx }}</td>
                    <td class="px-4 py-3">
                        @if($ls->hoaDon)
                        <a href="{{ route('admin.hoa-don.show', $ls->hoaDon) }}"
                           class="font-mono text-xs text-indigo-600 dark:text-indigo-400 hover:underline">{{ $ls->hoaDon->ma_thanh_toan }}</a>
                        @else<span class="text-gray-400">—</span>@endif
                    </td>
                    <td class="px-4 py-3 text-gray-700 dark:text-slate-300">{{ $canHo?->so_can_ho ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-700 dark:text-slate-300">{{ $chuHo?->ho_ten ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-slate-400 whitespace-nowrap">{{ $ls->ngay_thanh_toan?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums whitespace-nowrap">{{ number_format($ls->so_tien ?? 0, 0, ',', '.') }}đ</td>
                    <td class="px-4 py-3">
                        @if($ls->phuong_thuc_thanh_toan)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400">{{ $ls->phuong_thuc_thanh_toan }}</span>
                        @else<span class="text-gray-400">—</span>@endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-slate-400">{{ $ls->nguonTao?->ten_nguon_tao ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.thanh-toan.show', $ls) }}"
                           class="p-1.5 rounded text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors inline-flex"
                           title="Xem chi tiết">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        {{-- Pagination --}}
        @if($lichSuList->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-slate-700">
            {{ $lichSuList->links() }}
        </div>
        @endif
        @else
        <div class="px-5 py-12 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <p class="text-sm text-gray-500 dark:text-slate-400">Chưa có giao dịch thanh toán nào</p>
        </div>
        @endif
    </div>

</div>
@endsection
