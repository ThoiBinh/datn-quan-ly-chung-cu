@extends('layouts.admin')
@section('title', 'Chi tiết hóa đơn')
@section('page-title', 'Chi tiết hóa đơn')

@section('content')
@php
$conNo    = max(0, ($hoaDon->tong_tien ?? 0) - ($hoaDon->so_tien_da_thanh_toan ?? 0));
$isCanHuy = $hoaDon->trang_thai != \App\Models\HoaDon::TRANG_THAI_DA_HUY;
$statusConfig = match($hoaDon->trang_thai) {
    1 => ['label' => 'Chưa thanh toán', 'dot' => 'bg-amber-500', 'text' => 'text-amber-600 dark:text-amber-400', 'bg' => 'bg-amber-50 dark:bg-amber-900/20'],
    2 => ['label' => 'Đã thanh toán',   'dot' => 'bg-emerald-500', 'text' => 'text-emerald-600 dark:text-emerald-400', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20'],
    3 => ['label' => 'Quá hạn',         'dot' => 'bg-red-500',     'text' => 'text-red-600 dark:text-red-400',         'bg' => 'bg-red-50 dark:bg-red-900/20'],
    4 => ['label' => 'Đã hủy',          'dot' => 'bg-gray-400',    'text' => 'text-gray-500 dark:text-slate-400',       'bg' => 'bg-gray-50 dark:bg-slate-700/50'],
    default => ['label' => '—',         'dot' => 'bg-gray-400',    'text' => 'text-gray-500',                           'bg' => 'bg-gray-50'],
};
@endphp

<div class="space-y-5" x-data="{ confirmToggle: false }">

    <!-- Breadcrumb + actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
            <a href="{{ route('admin.hoa-don.index') }}" class="hover:text-violet-600 transition-colors">Hóa đơn</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="font-mono font-semibold text-gray-700 dark:text-slate-200">{{ $hoaDon->ma_thanh_toan }}</span>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.hoa-don.edit', $hoaDon) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            @if(in_array($hoaDon->trang_thai, [1, 3, 4]))
            @if($isCanHuy)
            <button @click="confirmToggle = true" type="button"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                Hủy hóa đơn
            </button>
            @else
            <button @click="confirmToggle = true" type="button"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Khôi phục
            </button>
            @endif
            @endif
        </div>
    </div>

    <!-- Hero card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Mã hóa đơn</p>
                    <p class="font-mono font-bold text-violet-600 dark:text-violet-400">{{ $hoaDon->ma_thanh_toan }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Kỳ</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-slate-100">Tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Tổng tiền</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Trạng thái</p>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                        {{ $statusConfig['label'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Cột chính -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Chi tiết khoản thu -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Chi tiết khoản thu</h2>
                    <span class="ml-auto text-xs text-gray-400 dark:text-slate-500">{{ $hoaDon->chiTiet->count() }} khoản</span>
                </div>
                @if($hoaDon->chiTiet->isNotEmpty())
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-slate-400">Tên dịch vụ</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-slate-400">Đơn giá</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-slate-400">Số lượng</th>
                            <th class="px-5 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-slate-400">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                        @foreach($hoaDon->chiTiet as $ct)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                            <td class="px-5 py-3 text-gray-700 dark:text-slate-200">{{ $ct->ten_phi_dich_vu }}</td>
                            <td class="px-4 py-3 text-right text-gray-500 dark:text-slate-400 tabular-nums">{{ number_format($ct->don_gia ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-500 dark:text-slate-400 tabular-nums">
                                @if($ct->chi_so_moi && $ct->chi_so_cu)
                                    {{ $ct->chi_so_moi - $ct->chi_so_cu }}
                                @elseif($ct->so_luong)
                                    {{ $ct->so_luong }}
                                @else
                                    1
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800 dark:text-slate-100 tabular-nums">
                                {{ number_format($ct->thanh_tien ?? 0, 0, ',', '.') }}đ
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-700/30">
                        <tr>
                            <td colspan="3" class="px-5 py-3 text-sm font-semibold text-gray-700 dark:text-slate-200 text-right">Tổng cộng</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-900 dark:text-white tabular-nums">
                                {{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ
                            </td>
                        </tr>
                        @if($hoaDon->chi_phi)
                        <tr>
                            <td colspan="3" class="px-5 py-2 text-xs text-gray-500 dark:text-slate-400 text-right">Chi phí khác</td>
                            <td class="px-5 py-2 text-right text-xs text-gray-600 dark:text-slate-300 tabular-nums">
                                {{ number_format($hoaDon->chi_phi, 0, ',', '.') }}đ
                            </td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
                @else
                <div class="px-5 py-8 text-center text-sm text-gray-400 dark:text-slate-500">Chưa có chi tiết khoản thu</div>
                @endif
            </div>

            <!-- Thanh toán -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Tổng tiền</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white tabular-nums">{{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Đã thanh toán</p>
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ number_format($hoaDon->so_tien_da_thanh_toan ?? 0, 0, ',', '.') }}đ</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 text-center">
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Còn nợ</p>
                    @if($conNo > 0)
                    <p class="text-lg font-bold text-red-600 dark:text-red-400 tabular-nums">
                    @else
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                    @endif
                        {{ $conNo > 0 ? number_format($conNo, 0, ',', '.') . 'đ' : 'Đã đủ' }}
                    </p>
                </div>
            </div>

            <!-- Lịch sử thanh toán -->
            @if($hoaDon->lichSuThanhToan->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Lịch sử thanh toán</h2>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    @foreach($hoaDon->lichSuThanhToan as $ls)
                    <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-slate-100">
                                {{ number_format($ls->so_tien ?? 0, 0, ',', '.') }}đ
                            </p>
                            <div class="flex items-center gap-3 mt-0.5">
                                <p class="text-xs text-gray-400 dark:text-slate-500">
                                    {{ $ls->ngay_thanh_toan ? \Carbon\Carbon::parse($ls->ngay_thanh_toan)->format('d/m/Y') : '—' }}
                                </p>
                                @if($ls->phuong_thuc_thanh_toan)
                                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300">
                                    {{ $ls->phuong_thuc_thanh_toan }}
                                </span>
                                @endif
                                @if($ls->nguonTao)
                                <span class="text-xs text-gray-400 dark:text-slate-500">{{ $ls->nguonTao->ten_nguon_tao ?? '' }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            @if($ls->nguoiThanhToan)
                            <p class="text-xs text-gray-500 dark:text-slate-400">{{ $ls->nguoiThanhToan->ho_ten }}</p>
                            @endif
                            @if($ls->ma_giao_dich)
                            <p class="text-xs font-mono text-gray-400 dark:text-slate-500">{{ $ls->ma_giao_dich }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="space-y-5">

            <!-- Thông tin căn hộ -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Căn hộ</h2>
                </div>
                @if($hoaDon->canHo)
                <dl class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Số căn hộ</dt>
                        <dd>
                            <a href="{{ route('admin.can-ho.show', $hoaDon->canHo) }}"
                               class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $hoaDon->canHo->so_can_ho }}
                            </a>
                        </dd>
                    </div>
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Tòa nhà</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-200">{{ $hoaDon->canHo->toaNha?->ten_toa_nha ?? '—' }}</dd>
                    </div>
                    @if($hoaDon->han_thanh_toan)
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Hạn TT</dt>
                        <dd class="text-sm text-gray-700 dark:text-slate-200">
                            {{ \Carbon\Carbon::parse($hoaDon->han_thanh_toan)->format('d/m/Y') }}
                        </dd>
                    </div>
                    @endif
                    @if($hoaDon->createdAt)
                    <div class="flex items-baseline px-5 py-3">
                        <dt class="w-24 text-xs text-gray-400 dark:text-slate-500 flex-shrink-0">Ngày tạo</dt>
                        <dd class="text-xs text-gray-500 dark:text-slate-400">{{ $hoaDon->createdAt->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                </dl>
                @else
                <div class="px-5 py-4 text-sm text-gray-400 dark:text-slate-500">—</div>
                @endif
            </div>

            <!-- Chủ hộ -->
            @php $chuHo = $hoaDon->canHo?->chuHo?->cuDan; @endphp
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Chủ hộ</h2>
                </div>
                @if($chuHo)
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ strtoupper(substr($chuHo->ho_ten ?? '?', 0, 1)) }}</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-slate-100">{{ $chuHo->ho_ten }}</p>
                    </div>
                    @if($chuHo->sdt)
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">📞 {{ $chuHo->sdt }}</p>
                    @endif
                    @if($chuHo->email)
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 break-all">✉ {{ $chuHo->email }}</p>
                    @endif
                </div>
                @else
                <div class="px-5 py-4 text-sm text-gray-400 dark:text-slate-500">Chưa có thông tin chủ hộ</div>
                @endif
            </div>

        </div>

    </div>

    <!-- Modal xác nhận toggle -->
    @if(in_array($hoaDon->trang_thai, [1, 3, 4]))
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
                @if($isCanHuy)
                <div class="bg-red-100 dark:bg-red-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Hủy hóa đơn</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Hóa đơn sẽ được chuyển sang trạng thái đã hủy.</p>
                @else
                <div class="bg-emerald-100 dark:bg-emerald-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Khôi phục hóa đơn</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Hóa đơn sẽ được khôi phục về trạng thái chưa thanh toán.</p>
                @endif
            </div>

            <!-- Info card: mã hóa đơn -->
            <div class="mx-6 mb-5 flex items-center gap-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                <div class="w-9 h-9 rounded-full bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 dark:text-slate-500">Mã hóa đơn</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white font-mono truncate">{{ $hoaDon->ma_thanh_toan }}</p>
                </div>
            </div>

            <!-- Buttons: form dùng flex-1 (block), button dùng block w-full — không dùng display:contents -->
            <div class="flex gap-3 px-6 pb-6">
                <button @click="confirmToggle = false" type="button"
                        class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy bỏ
                </button>
                <form action="{{ route('admin.hoa-don.toggle-status', $hoaDon) }}" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    @if($isCanHuy)
                    <button type="submit" class="block w-full py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">
                        Hủy hóa đơn
                    </button>
                    @else
                    <button type="submit" class="block w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">
                        Khôi phục
                    </button>
                    @endif
                </form>
            </div>

        </div>
    </div>
    </template>
    @endif

</div>
@endsection
