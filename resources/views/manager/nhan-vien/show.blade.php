@extends('layouts.manager')
@section('title', $nhanVien->ho_ten)
@section('page-title', 'Chi tiết nhân viên')

@section('content')

{{-- Header Card --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-5">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        {{-- Avatar --}}
        <div class="w-20 h-20 rounded-2xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
            <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-300">
                {{ strtoupper(mb_substr($nhanVien->ho_ten, 0, 1)) }}
            </span>
        </div>
        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $nhanVien->ho_ten }}</h1>
                @if($nhanVien->trang_thai == 1)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Đang làm việc
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Đã nghỉ
                </span>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $nhanVien->ma_nhan_vien }}
                @if($nhanVien->chucVu)
                · <span class="text-indigo-600 dark:text-indigo-400">{{ $nhanVien->chucVu->chuc_vu }}</span>
                @endif
                · {{ $nhanVien->email }}
            </p>
            {{-- Stats inline --}}
            <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                <span><strong class="text-gray-800 dark:text-gray-200">{{ $nhanVien->hoa_don_count }}</strong> hóa đơn</span>
                <span><strong class="text-gray-800 dark:text-gray-200">{{ $nhanVien->yeu_cau_xu_ly_count }}</strong> yêu cầu</span>
                <span><strong class="text-gray-800 dark:text-gray-200">{{ $nhanVien->thong_bao_count }}</strong> thông báo</span>
                <span><strong class="text-gray-800 dark:text-gray-200">{{ $nhanVien->bang_tin_count }}</strong> bản tin</span>
            </div>
        </div>
        {{-- Actions --}}
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('manager.nhan-vien.edit', $nhanVien) }}"
               class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('manager.nhan-vien.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Quay lại
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Cột trái: 3 card thông tin --}}
    <div class="lg:col-span-1 space-y-5">

        {{-- CARD 1: Thông tin cá nhân --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200">Thông tin cá nhân</h3>
            </div>
            <div class="p-4 space-y-3">
                @php
                    $infoRows = [
                        ['label' => 'Mã nhân viên',   'value' => $nhanVien->ma_nhan_vien],
                        ['label' => 'CCCD',            'value' => $nhanVien->cccd],
                        ['label' => 'Số điện thoại',  'value' => $nhanVien->sdt ?: '—'],
                        ['label' => 'Email',           'value' => $nhanVien->email],
                        ['label' => 'Ngày sinh',       'value' => $nhanVien->ngay_sinh?->format('d/m/Y') ?? '—'],
                        ['label' => 'Ngày vào làm',   'value' => $nhanVien->ngay_vao_lam?->format('d/m/Y') ?? '—'],
                        ['label' => 'Ngày nghỉ làm',  'value' => $nhanVien->ngay_nghi_lam?->format('d/m/Y') ?? '—'],
                    ];
                @endphp
                @foreach($infoRows as $row)
                <div class="flex justify-between gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $row['label'] }}</span>
                    <span class="text-xs font-medium text-gray-800 dark:text-gray-200 text-right">{{ $row['value'] }}</span>
                </div>
                @endforeach
                @if($nhanVien->ghi_chu)
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Ghi chú</span>
                    <p class="text-xs text-gray-700 dark:text-gray-300 mt-1 bg-gray-50 dark:bg-gray-700/50 rounded p-2">{{ $nhanVien->ghi_chu }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- CARD 2: Thông tin tài khoản --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200">Thông tin tài khoản</h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex justify-between gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Email đăng nhập</span>
                    <span class="text-xs font-medium text-gray-800 dark:text-gray-200 text-right">{{ $nhanVien->email }}</span>
                </div>
                
                <div class="flex justify-between gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Trạng thái</span>
                    <span class="text-xs">
                        @if($nhanVien->trang_thai == 1)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">Hoạt động</span>
                        @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">Đã nghỉ</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Ngày tạo</span>
                    <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $nhanVien->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Cập nhật lần cuối</span>
                    <span class="text-xs font-medium text-gray-800 dark:text-gray-200">
                        {{ isset($nhanVien->attributes['updatedAt']) ? \Carbon\Carbon::parse($nhanVien->attributes['updatedAt'])->format('d/m/Y H:i') : '—' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- CARD 3: Thông tin chức vụ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200">Thông tin chức vụ</h3>
            </div>
            <div class="p-4 space-y-3">
                @if($nhanVien->chucVu)
                <div class="flex justify-between gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Tên chức vụ</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                        {{ $nhanVien->chucVu->chuc_vu }}
                    </span>
                </div>
                @else
                <p class="text-xs text-gray-400 dark:text-gray-500 text-center py-2">Chưa có chức vụ</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Cột phải: lịch sử --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- CARD 4: Yêu cầu cư dân đã xử lý --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200">Lịch sử xử lý yêu cầu cư dân</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $nhanVien->yeuCauXuLy->count() }} yêu cầu</span>
            </div>
            @if($nhanVien->yeuCauXuLy->isEmpty())
            <div class="py-8 text-center">
                <p class="text-xs text-gray-400 dark:text-gray-500">Chưa xử lý yêu cầu nào</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-2 text-left">Tiêu đề</th>
                            <th class="px-3 py-2 text-left">Cư dân</th>
                            <th class="px-3 py-2 text-left">Loại</th>
                            <th class="px-3 py-2 text-left">Trạng thái</th>
                            <th class="px-3 py-2 text-left">Ngày</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($nhanVien->yeuCauXuLy as $yc)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20">
                            <td class="px-3 py-2 max-w-[160px] truncate text-gray-800 dark:text-gray-200">{{ $yc->tieu_de }}</td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $yc->cuDan?->ho_ten ?? '—' }}</td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $yc->loaiYeuCau?->name ?? '—' }}</td>
                            <td class="px-3 py-2">
                                @php $ttLabel = $yc->trang_thai_label; @endphp
                                <span class="px-1.5 py-0.5 rounded text-xs {{ $ttLabel['class'] }}">{{ $ttLabel['text'] }}</span>
                            </td>
                            <td class="px-3 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $yc->created_at?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- CARD 5: Lịch sử lập hóa đơn --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200">Lịch sử lập hóa đơn</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $nhanVien->hoaDon->count() }} hóa đơn</span>
            </div>
            @if($nhanVien->hoaDon->isEmpty())
            <div class="py-8 text-center">
                <p class="text-xs text-gray-400 dark:text-gray-500">Chưa lập hóa đơn nào</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-2 text-left">Mã hóa đơn</th>
                            <th class="px-3 py-2 text-left">Căn hộ</th>
                            <th class="px-3 py-2 text-left">Tháng/Năm</th>
                            <th class="px-3 py-2 text-right">Tổng tiền</th>
                            <th class="px-3 py-2 text-left">Trạng thái</th>
                            <th class="px-3 py-2 text-left">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($nhanVien->hoaDon as $hd)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20">
                            <td class="px-3 py-2 font-mono text-gray-700 dark:text-gray-300">{{ $hd->ma_thanh_toan }}</td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $hd->canHo?->so_can_ho ?? '—' }}</td>
                            <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $hd->thang }}/{{ $hd->nam }}</td>
                            <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($hd->tong_tien) }}đ</td>
                            <td class="px-3 py-2">
                                @php
                                    $hdColors = [1=>'bg-yellow-100 text-yellow-700', 2=>'bg-emerald-100 text-emerald-700', 3=>'bg-red-100 text-red-700', 4=>'bg-gray-100 text-gray-500'];
                                @endphp
                                <span class="px-1.5 py-0.5 rounded text-xs {{ $hdColors[$hd->trang_thai] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ $hd->trang_thai_label }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $hd->created_at?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- CARD 6: Lịch sử thu tiền (qua hoa_don) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200">Lịch sử thu tiền</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $nhanVien->lichSuThanhToan->count() }} giao dịch</span>
            </div>
            @if($nhanVien->lichSuThanhToan->isEmpty())
            <div class="py-8 text-center">
                <p class="text-xs text-gray-400 dark:text-gray-500">Chưa có giao dịch nào</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-2 text-left">Mã giao dịch</th>
                            <th class="px-3 py-2 text-left">Hóa đơn</th>
                            <th class="px-3 py-2 text-left">Căn hộ</th>
                            <th class="px-3 py-2 text-left">Cư dân</th>
                            <th class="px-3 py-2 text-right">Số tiền</th>
                            <th class="px-3 py-2 text-left">Phương thức</th>
                            <th class="px-3 py-2 text-left">Ngày TT</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($nhanVien->lichSuThanhToan as $ls)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20">
                            <td class="px-3 py-2 font-mono text-gray-700 dark:text-gray-300">{{ $ls->ma_giao_dich ?: '—' }}</td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $ls->hoaDon?->ma_thanh_toan ?? '—' }}</td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $ls->hoaDon?->canHo?->so_can_ho ?? '—' }}</td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $ls->nguoiThanhToan?->ho_ten ?? '—' }}</td>
                            <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($ls->so_tien) }}đ</td>
                            <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $ls->phuong_thuc_thanh_toan ?: '—' }}</td>
                            <td class="px-3 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $ls->ngay_thanh_toan?->format('d/m/Y') ?? '—' }}</td>
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
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200">Thông báo đã tạo</h3>
                    <span class="text-xs text-gray-400">{{ $nhanVien->thongBao->count() }}</span>
                </div>
                @if($nhanVien->thongBao->isEmpty())
                <div class="py-6 text-center">
                    <p class="text-xs text-gray-400 dark:text-gray-500">Chưa tạo thông báo nào</p>
                </div>
                @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-700 max-h-64 overflow-y-auto">
                    @foreach($nhanVien->thongBao as $tb)
                    <li class="px-4 py-2.5">
                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate">{{ $tb->tieu_de }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $tb->created_at?->format('d/m/Y') ?? '—' }}</p>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>

            {{-- CARD 8: Bản tin đã tạo --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200">Bản tin đã tạo</h3>
                    <span class="text-xs text-gray-400">{{ $nhanVien->bangTin->count() }}</span>
                </div>
                @if($nhanVien->bangTin->isEmpty())
                <div class="py-6 text-center">
                    <p class="text-xs text-gray-400 dark:text-gray-500">Chưa tạo bản tin nào</p>
                </div>
                @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-700 max-h-64 overflow-y-auto">
                    @foreach($nhanVien->bangTin as $bt)
                    <li class="px-4 py-2.5">
                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate">{{ $bt->tieu_de }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $bt->created_at?->format('d/m/Y') ?? '—' }}</p>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
