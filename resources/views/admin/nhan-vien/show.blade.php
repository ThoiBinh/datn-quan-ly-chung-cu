@extends('layouts.admin')
@section('title', 'Chi tiết nhân viên')
@section('page-title', 'Chi tiết nhân viên')

@section('content')
@php $isActive = $nhanVien->trang_thai == 1; @endphp

<div class="space-y-5"
     x-data="{
         confirmToggle: false,
         confirmAction: '{{ route('admin.nhan-vien.toggle-status', $nhanVien) }}'
     }">

    <!-- Header card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-xl font-bold text-blue-700 dark:text-blue-400 flex-shrink-0">
                {{ mb_strtoupper(mb_substr($nhanVien->ho_ten, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $nhanVien->ho_ten }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                        {{ $nhanVien->chucVu?->chuc_vu ?? 'Chưa phân công' }}
                    </span>
                    @if($nhanVien->isAdmin())
                    <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Admin</span>
                    @else
                    <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">Manager</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400">{{ $nhanVien->email }}</p>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5 font-mono">{{ $nhanVien->ma_nhan_vien ?? 'Chưa có mã' }}</p>
                <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500 dark:text-slate-400">
                    <span><strong class="text-gray-800 dark:text-slate-200">{{ $nhanVien->hoa_don_count }}</strong> hóa đơn</span>
                    <span><strong class="text-gray-800 dark:text-slate-200">{{ $nhanVien->lich_su_thanh_toan_count }}</strong> thanh toán</span>
                    <span><strong class="text-gray-800 dark:text-slate-200">{{ $nhanVien->yeu_cau_xu_ly_count }}</strong> yêu cầu</span>
                    <span><strong class="text-gray-800 dark:text-slate-200">{{ $nhanVien->thong_bao_count }}</strong> thông báo</span>
                    <span><strong class="text-gray-800 dark:text-slate-200">{{ $nhanVien->bang_tin_count }}</strong> bản tin</span>
                </div>
            </div>
            @if($isActive)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400 flex-shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Hoạt động
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-xs font-semibold text-red-600 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 flex-shrink-0">
                <span class="w-2 h-2 rounded-full bg-red-500"></span> Đã khóa
            </span>
            @endif
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex flex-wrap gap-3">
            <a href="{{ route('admin.nhan-vien.edit', $nhanVien) }}"
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            @if($nhanVien->id !== auth('nhanvien')->id())
            @if($isActive)
            <button type="button" @click="confirmToggle = true"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-amber-500 hover:bg-amber-600 text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Khóa tài khoản
            </button>
            @else
            <button type="button" @click="confirmToggle = true"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-emerald-500 hover:bg-emerald-600 text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                Mở khóa
            </button>
            @endif
            <form method="POST" action="{{ route('admin.nhan-vien.destroy', $nhanVien) }}"
                  x-data
                  @submit.prevent="if(confirm('Xóa nhân viên «{{ addslashes($nhanVien->ho_ten) }}»?\nNhân viên đã phát sinh dữ liệu sẽ không thể xóa.')) $el.submit()">
                @csrf @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 border border-red-300 text-red-600 dark:border-red-800 dark:text-red-400 text-sm font-medium rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Xóa
                </button>
            </form>
            @endif
            <a href="{{ route('admin.nhan-vien.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Cột trái: thông tin -->
        <div class="lg:col-span-1 space-y-5">

            {{-- CARD 1: Thông tin nhân viên --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="font-semibold text-sm text-gray-800 dark:text-slate-200">Thông tin nhân viên</h3>
                </div>
                <div class="p-4 space-y-3">
                    @php
                        $infoRows = [
                            ['label' => 'Mã nhân viên',  'value' => $nhanVien->ma_nhan_vien],
                            ['label' => 'CCCD',          'value' => $nhanVien->cccd],
                            ['label' => 'Số điện thoại', 'value' => $nhanVien->sdt ?: '—'],
                            ['label' => 'Email',         'value' => $nhanVien->email],
                            ['label' => 'Ngày sinh',     'value' => $nhanVien->ngay_sinh?->format('d/m/Y') ?? '—'],
                            ['label' => 'Ngày vào làm',  'value' => $nhanVien->ngay_vao_lam?->format('d/m/Y') ?? '—'],
                            ['label' => 'Ngày nghỉ làm', 'value' => $nhanVien->ngay_nghi_lam?->format('d/m/Y') ?? '—'],
                        ];
                    @endphp
                    @foreach($infoRows as $row)
                    <div class="flex justify-between gap-2">
                        <span class="text-xs text-gray-500 dark:text-slate-400 flex-shrink-0">{{ $row['label'] }}</span>
                        <span class="text-xs font-medium text-gray-800 dark:text-slate-200 text-right">{{ $row['value'] }}</span>
                    </div>
                    @endforeach
                    @if($nhanVien->ghi_chu)
                    <div>
                        <span class="text-xs text-gray-500 dark:text-slate-400">Ghi chú</span>
                        <p class="text-xs text-gray-700 dark:text-slate-300 mt-1 bg-gray-50 dark:bg-slate-700/50 rounded p-2 whitespace-pre-wrap">{{ $nhanVien->ghi_chu }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- CARD 2: Thông tin tài khoản --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="font-semibold text-sm text-gray-800 dark:text-slate-200">Thông tin tài khoản</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between gap-2">
                        <span class="text-xs text-gray-500 dark:text-slate-400">Email đăng nhập</span>
                        <span class="text-xs font-medium text-gray-800 dark:text-slate-200 text-right">{{ $nhanVien->email }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-xs text-gray-500 dark:text-slate-400">Vai trò</span>
                        <span class="text-xs">
                            @if($nhanVien->isAdmin())
                            <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Admin</span>
                            @else
                            <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">Manager</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-xs text-gray-500 dark:text-slate-400">Trạng thái</span>
                        <span class="text-xs">
                            @if($isActive)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">Hoạt động</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">Đã khóa</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-xs text-gray-500 dark:text-slate-400">Ngày tạo</span>
                        <span class="text-xs font-medium text-gray-800 dark:text-slate-200">{{ $nhanVien->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-xs text-gray-500 dark:text-slate-400">Cập nhật lần cuối</span>
                        <span class="text-xs font-medium text-gray-800 dark:text-slate-200">
                            {{ isset($nhanVien->attributes['updatedAt']) ? \Carbon\Carbon::parse($nhanVien->attributes['updatedAt'])->format('d/m/Y H:i') : '—' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- CARD 3: Thông tin chức vụ --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="font-semibold text-sm text-gray-800 dark:text-slate-200">Thông tin chức vụ</h3>
                </div>
                <div class="p-4">
                    @if($nhanVien->chucVu)
                    <div class="flex justify-between gap-2">
                        <span class="text-xs text-gray-500 dark:text-slate-400">Tên chức vụ</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                            {{ $nhanVien->chucVu->chuc_vu }}
                        </span>
                    </div>
                    @else
                    <p class="text-xs text-gray-400 dark:text-slate-500 text-center py-2">Chưa có chức vụ</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cột phải: lịch sử -->
        <div class="lg:col-span-2 space-y-5">

            {{-- CARD 4: Danh sách hóa đơn đã lập --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="font-semibold text-sm text-gray-800 dark:text-slate-200">Danh sách hóa đơn đã lập</h3>
                    <span class="text-xs text-gray-400 dark:text-slate-500">{{ $nhanVien->hoa_don_count }} hóa đơn</span>
                </div>
                @if($nhanVien->hoaDon->isEmpty())
                <div class="py-8 text-center">
                    <p class="text-xs text-gray-400 dark:text-slate-500">Chưa lập hóa đơn nào</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 dark:bg-slate-700/40 text-gray-500 dark:text-slate-400">
                            <tr>
                                <th class="px-3 py-2 text-left">Mã hóa đơn</th>
                                <th class="px-3 py-2 text-left">Căn hộ</th>
                                <th class="px-3 py-2 text-left">Cư dân</th>
                                <th class="px-3 py-2 text-left">Kỳ hóa đơn</th>
                                <th class="px-3 py-2 text-right">Tổng tiền</th>
                                <th class="px-3 py-2 text-left">Trạng thái</th>
                                <th class="px-3 py-2 text-left">Ngày lập</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @php $hdColors = [1=>'bg-yellow-100 text-yellow-700', 2=>'bg-emerald-100 text-emerald-700', 3=>'bg-red-100 text-red-700', 4=>'bg-gray-100 text-gray-500']; @endphp
                            @foreach($nhanVien->hoaDon as $hd)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20">
                                <td class="px-3 py-2 font-mono text-gray-700 dark:text-slate-300">{{ $hd->ma_thanh_toan }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-slate-400">
                                    {{ $hd->canHo?->so_can_ho ?? '—' }}
                                    @if($hd->canHo?->toaNha)<span class="text-gray-400"> · {{ $hd->canHo->toaNha->ten_toa_nha }}</span>@endif
                                </td>
                                <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $hd->canHo?->chuHo?->cuDan?->ho_ten ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-500 dark:text-slate-400">{{ $hd->thang }}/{{ $hd->nam }}</td>
                                <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-slate-200">{{ number_format($hd->tong_tien) }}đ</td>
                                <td class="px-3 py-2">
                                    <span class="px-1.5 py-0.5 rounded text-xs {{ $hdColors[$hd->trang_thai] ?? 'bg-gray-100 text-gray-500' }}">
                                        {{ $hd->trang_thai_label }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-500 dark:text-slate-400 whitespace-nowrap">{{ $hd->created_at?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- CARD 5: Lịch sử thanh toán đã xử lý --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="font-semibold text-sm text-gray-800 dark:text-slate-200">Lịch sử thanh toán đã xử lý</h3>
                    <span class="text-xs text-gray-400 dark:text-slate-500">{{ $nhanVien->lich_su_thanh_toan_count }} giao dịch</span>
                </div>
                @if($nhanVien->lichSuThanhToan->isEmpty())
                <div class="py-8 text-center">
                    <p class="text-xs text-gray-400 dark:text-slate-500">Chưa có giao dịch nào</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 dark:bg-slate-700/40 text-gray-500 dark:text-slate-400">
                            <tr>
                                <th class="px-3 py-2 text-left">Mã giao dịch</th>
                                <th class="px-3 py-2 text-left">Hóa đơn</th>
                                <th class="px-3 py-2 text-left">Căn hộ</th>
                                <th class="px-3 py-2 text-left">Cư dân</th>
                                <th class="px-3 py-2 text-right">Số tiền</th>
                                <th class="px-3 py-2 text-left">Phương thức</th>
                                <th class="px-3 py-2 text-left">Ngày thanh toán</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($nhanVien->lichSuThanhToan as $ls)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20">
                                <td class="px-3 py-2 font-mono text-gray-700 dark:text-slate-300">{{ $ls->ma_giao_dich ?: '—' }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $ls->hoaDon?->ma_thanh_toan ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $ls->hoaDon?->canHo?->so_can_ho ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $ls->nguoiThanhToan?->ho_ten ?? '—' }}</td>
                                <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-slate-200">{{ number_format($ls->so_tien) }}đ</td>
                                <td class="px-3 py-2 text-gray-500 dark:text-slate-400">{{ $ls->phuong_thuc_thanh_toan ?: '—' }}</td>
                                <td class="px-3 py-2 text-gray-500 dark:text-slate-400 whitespace-nowrap">{{ $ls->ngay_thanh_toan?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- CARD 6: Yêu cầu cư dân đã xử lý --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="font-semibold text-sm text-gray-800 dark:text-slate-200">Yêu cầu cư dân đã xử lý</h3>
                    <span class="text-xs text-gray-400 dark:text-slate-500">{{ $nhanVien->yeu_cau_xu_ly_count }} yêu cầu</span>
                </div>
                @if($nhanVien->yeuCauXuLy->isEmpty())
                <div class="py-8 text-center">
                    <p class="text-xs text-gray-400 dark:text-slate-500">Chưa xử lý yêu cầu nào</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 dark:bg-slate-700/40 text-gray-500 dark:text-slate-400">
                            <tr>
                                <th class="px-3 py-2 text-left">Loại yêu cầu</th>
                                <th class="px-3 py-2 text-left">Cư dân</th>
                                <th class="px-3 py-2 text-left">Căn hộ</th>
                                <th class="px-3 py-2 text-left">Trạng thái</th>
                                <th class="px-3 py-2 text-left">Ngày xử lý</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($nhanVien->yeuCauXuLy as $yc)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20">
                                <td class="px-3 py-2 text-gray-800 dark:text-slate-200">
                                    {{ $yc->loaiYeuCau?->name ?? '—' }}
                                    <p class="text-gray-400 truncate max-w-[160px]">{{ $yc->tieu_de }}</p>
                                </td>
                                <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $yc->cuDan?->ho_ten ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $yc->cuDan?->canHoHienTai?->canHo?->so_can_ho ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    @php $ttLabel = $yc->trang_thai_label; @endphp
                                    <span class="px-1.5 py-0.5 rounded text-xs {{ $ttLabel['class'] }}">{{ $ttLabel['text'] }}</span>
                                </td>
                                <td class="px-3 py-2 text-gray-500 dark:text-slate-400 whitespace-nowrap">{{ $yc->ngay_hoan_thanh?->format('d/m/Y') ?? $yc->created_at?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- CARD 7 & 8: Thông báo và Bản tin --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- CARD 7: Thông báo đã tạo --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                        <h3 class="font-semibold text-sm text-gray-800 dark:text-slate-200">Thông báo đã tạo</h3>
                        <span class="text-xs text-gray-400">{{ $nhanVien->thong_bao_count }}</span>
                    </div>
                    @if($nhanVien->thongBao->isEmpty())
                    <div class="py-6 text-center">
                        <p class="text-xs text-gray-400 dark:text-slate-500">Chưa tạo thông báo nào</p>
                    </div>
                    @else
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 max-h-64 overflow-y-auto">
                        @foreach($nhanVien->thongBao as $tb)
                        <li class="px-4 py-2.5">
                            <p class="text-xs font-medium text-gray-800 dark:text-slate-200 truncate">{{ $tb->tieu_de }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 truncate">{{ \Illuminate\Support\Str::limit($tb->noi_dung, 60) }}</p>
                            <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">{{ $tb->created_at?->format('d/m/Y') ?? '—' }}</p>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>

                {{-- CARD 8: Bản tin đã tạo --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                        <h3 class="font-semibold text-sm text-gray-800 dark:text-slate-200">Bản tin đã tạo</h3>
                        <span class="text-xs text-gray-400">{{ $nhanVien->bang_tin_count }}</span>
                    </div>
                    @if($nhanVien->bangTin->isEmpty())
                    <div class="py-6 text-center">
                        <p class="text-xs text-gray-400 dark:text-slate-500">Chưa tạo bản tin nào</p>
                    </div>
                    @else
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 max-h-64 overflow-y-auto">
                        @foreach($nhanVien->bangTin as $bt)
                        <li class="px-4 py-2.5">
                            <p class="text-xs font-medium text-gray-800 dark:text-slate-200 truncate">{{ $bt->tieu_de }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 truncate">{{ \Illuminate\Support\Str::limit($bt->noi_dung, 60) }}</p>
                            <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">{{ $bt->created_at?->format('d/m/Y') ?? '—' }}</p>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
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
                    <svg class="w-8 h-8 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Khóa tài khoản</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Tài khoản sẽ bị vô hiệu hóa, không thể đăng nhập.</p>
                @else
                <div class="bg-emerald-100 dark:bg-emerald-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Mở khóa tài khoản</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Tài khoản sẽ được kích hoạt, có thể đăng nhập trở lại.</p>
                @endif
            </div>

            <!-- User info -->
            <div class="mx-6 mb-5 flex items-center gap-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                @if($isActive)
                <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                @else
                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                @endif
                    {{ mb_strtoupper(mb_substr($nhanVien->ho_ten, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 dark:text-slate-500">Nhân viên</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $nhanVien->ho_ten }}</p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 px-6 pb-6">
                <button @click="confirmToggle = false" type="button"
                        class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy bỏ
                </button>
                <form :action="confirmAction" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    @if($isActive)
                    <button type="submit" class="block w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-gray-900 text-sm font-semibold rounded-xl transition-colors">
                        Khóa tài khoản
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
