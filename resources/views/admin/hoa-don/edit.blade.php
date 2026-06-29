@extends('layouts.admin')
@section('title', 'Chỉnh sửa hóa đơn')
@section('page-title', 'Chỉnh sửa hóa đơn')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.hoa-don.index') }}" class="hover:text-violet-600 transition-colors">Hóa đơn</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.hoa-don.show', $hoaDon) }}" class="hover:text-violet-600 transition-colors font-mono">{{ $hoaDon->ma_thanh_toan }}</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Chỉnh sửa</span>
    </nav>

    @if($isDaTT)
    <!-- Warning banner: DA_THANH_TOAN -->
    <div class="flex items-center gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
        <svg class="w-5 h-5 text-amber-500 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">Hóa đơn đã thanh toán</p>
            <p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">Không thể chỉnh sửa hoặc xóa hóa đơn đã thanh toán. Chỉ xem thông tin.</p>
        </div>
    </div>
    @endif

    <!-- Summary -->
    <div class="bg-violet-50 dark:bg-violet-900/20 rounded-xl border border-violet-200 dark:border-violet-800 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-violet-600 dark:text-violet-400 font-medium">{{ $hoaDon->ma_thanh_toan }}</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-slate-100 mt-0.5">
                    Tháng {{ $hoaDon->thang }}/{{ $hoaDon->nam }}
                    — {{ $hoaDon->canHo?->so_can_ho ?? '—' }}
                    ({{ $hoaDon->canHo?->toaNha?->ten_toa_nha ?? '?' }})
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 dark:text-slate-500">Tổng tiền</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white tabular-nums">
                    {{ number_format($hoaDon->tong_tien ?? 0, 0, ',', '.') }}đ
                </p>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-sm text-red-600 dark:text-red-400">{{ session('error') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <ul class="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form id="update-form" action="{{ route('admin.hoa-don.update', $hoaDon) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        <!-- Status & deadline -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden {{ $isDaTT ? 'opacity-70' : '' }}">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Trạng thái & hạn thanh toán</h2>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Hạn thanh toán</label>
                    <input type="date" name="han_thanh_toan"
                           value="{{ old('han_thanh_toan', $hoaDon->han_thanh_toan ? $hoaDon->han_thanh_toan->format('Y-m-d') : '') }}"
                           {{ $isDaTT ? 'disabled' : '' }}
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400 {{ $isDaTT ? 'cursor-not-allowed' : '' }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="trang_thai"
                            {{ $isDaTT ? 'disabled' : '' }}
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400 {{ $isDaTT ? 'cursor-not-allowed' : '' }} {{ $errors->has('trang_thai') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="{{ \App\Models\HoaDon::TRANG_THAI_CHUA_THANH_TOAN }}" {{ old('trang_thai', $hoaDon->trang_thai) == \App\Models\HoaDon::TRANG_THAI_CHUA_THANH_TOAN ? 'selected' : '' }}>Chưa thanh toán</option>
                        <option value="{{ \App\Models\HoaDon::TRANG_THAI_DA_THANH_TOAN }}"  {{ old('trang_thai', $hoaDon->trang_thai) == \App\Models\HoaDon::TRANG_THAI_DA_THANH_TOAN  ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="{{ \App\Models\HoaDon::TRANG_THAI_QUA_HAN }}"        {{ old('trang_thai', $hoaDon->trang_thai) == \App\Models\HoaDon::TRANG_THAI_QUA_HAN        ? 'selected' : '' }}>Quá hạn</option>
                        <option value="{{ \App\Models\HoaDon::TRANG_THAI_DA_HUY }}"         {{ old('trang_thai', $hoaDon->trang_thai) == \App\Models\HoaDon::TRANG_THAI_DA_HUY         ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    @error('trang_thai')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        @php
            $chiTietChiSo = $hoaDon->chiTiet->filter(fn($ct) => $ct->chi_so_cu !== null);
            $chiTietKhac  = $hoaDon->chiTiet->filter(fn($ct) => $ct->chi_so_cu === null);
        @endphp

        @if($hoaDon->trang_thai == \App\Models\HoaDon::TRANG_THAI_CHUA_THANH_TOAN && $chiTietChiSo->isNotEmpty())
        <!-- Chi so editing (CHUA_THANH_TOAN only) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-blue-200 dark:border-blue-800 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-blue-100 dark:border-blue-800 bg-blue-50/60 dark:bg-blue-900/20">
                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-blue-800 dark:text-blue-200">Chỉ số đồng hồ</h2>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">Cập nhật chỉ số để tính lại thành tiền điện/nước</p>
                </div>
            </div>
            <div class="p-5 space-y-4">
                @foreach($chiTietChiSo as $ct)
                <div class="border border-gray-200 dark:border-slate-600 rounded-lg overflow-hidden">
                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $ct->ten_phi_dich_vu }}</span>
                        <span class="text-xs text-gray-400 dark:text-slate-500">{{ number_format($ct->don_gia, 0, ',', '.') }}đ/đơn vị</span>
                    </div>
                    <div class="px-4 py-3 grid grid-cols-3 gap-3 items-end">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Chỉ số cũ</label>
                            <input type="number"
                                   name="chi_tiet[{{ $ct->id }}][chi_so_cu]"
                                   value="{{ old("chi_tiet.{$ct->id}.chi_so_cu", $ct->chi_so_cu) }}"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400 tabular-nums">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Chỉ số mới</label>
                            <input type="number"
                                   name="chi_tiet[{{ $ct->id }}][chi_so_moi]"
                                   value="{{ old("chi_tiet.{$ct->id}.chi_so_moi", $ct->chi_so_moi) }}"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400 tabular-nums">
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-slate-500 mb-1">Hiện tại</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">
                                {{ number_format($ct->thanh_tien, 0, ',', '.') }}đ
                            </p>
                            <p class="text-xs text-gray-400 dark:text-slate-500">({{ number_format($ct->so_luong, 0, ',', '.') }} đvị)</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($chiTietKhac->isNotEmpty())
        <!-- Other fee items (read-only) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-slate-300">Các khoản phí khác</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                @foreach($chiTietKhac as $ct)
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-sm text-gray-700 dark:text-slate-300">{{ $ct->ten_phi_dich_vu }}</span>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900 dark:text-white tabular-nums">{{ number_format($ct->thanh_tien, 0, ',', '.') }}đ</p>
                        <p class="text-xs text-gray-400 dark:text-slate-500">{{ number_format($ct->so_luong, 0, ',', '.') }} × {{ number_format($ct->don_gia, 0, ',', '.') }}đ</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </form>

    <!-- Actions row (outside form to allow separate delete form) -->
    <div class="flex items-center justify-between">

        {{-- Nút xóa --}}
        @if($isDaTT)
        <div class="relative group">
            <button type="button" disabled
                    class="px-4 py-2.5 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900 text-red-400 dark:text-red-600 text-sm font-medium rounded-lg opacity-60 cursor-not-allowed">
                Xóa hóa đơn
            </button>
            <div class="absolute bottom-full left-0 mb-2 px-3 py-2 bg-gray-900 dark:bg-slate-700 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                Hóa đơn đã thanh toán nên không thể xóa.
                <div class="absolute top-full left-4 -mt-0.5 w-2 h-2 bg-gray-900 dark:bg-slate-700 rotate-45"></div>
            </div>
        </div>
        @else
        <form action="{{ route('admin.hoa-don.destroy', $hoaDon) }}" method="POST"
              onsubmit="return confirm('Xóa hóa đơn {{ $hoaDon->ma_thanh_toan }}? Thao tác này không thể hoàn tác.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="px-4 py-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                Xóa hóa đơn
            </button>
        </form>
        @endif

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.hoa-don.show', $hoaDon) }}"
               class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Hủy
            </a>
            @if(!$isDaTT)
            <button type="submit" form="update-form"
                    class="px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Lưu thay đổi
            </button>
            @else
            <button type="button" disabled
                    class="px-5 py-2.5 bg-violet-300 dark:bg-violet-900/30 text-white text-sm font-semibold rounded-lg cursor-not-allowed opacity-50"
                    title="Không thể chỉnh sửa hóa đơn đã thanh toán">
                Lưu thay đổi
            </button>
            @endif
        </div>

    </div>
</div>
@endsection
