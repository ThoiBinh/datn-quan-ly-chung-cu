@extends('layouts.resident')
@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="space-y-6">

    {{-- Hero Header --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="h-24 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 -mt-10">
                <div class="flex items-end gap-4">
                  

                    <div class="pb-1">
                        <h1 class="text-xl font-bold text-gray-900">{{ $cuDan->ho_ten }}</h1>
                        <div class="flex items-center gap-2 mt-1">
                            @php $ttLabel = $cuDan->trang_thai_label; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ttLabel['class'] }}">
                                {{ $ttLabel['text'] }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('resident.profile.edit') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Chỉnh sửa
                    </a>
                    <a href="{{ route('resident.profile.change-password') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Đổi mật khẩu
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Personal Info --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Thông tin cá nhân --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-800">Thông tin cá nhân</h2>
                    </div>
                    <a href="{{ route('resident.profile.edit') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Chỉnh sửa</a>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Họ tên đệm</p>
                        <p class="text-gray-800">{{ $cuDan->ho_ten_dem ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tên</p>
                        <p class="text-gray-800 font-medium">{{ $cuDan->ten ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</p>
                        <p class="text-gray-800">{{ $cuDan->email ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Số điện thoại</p>
                        <p class="text-gray-800">{{ $cuDan->sdt ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">CCCD/CMND</p>
                        <p class="text-gray-800">{{ $cuDan->cccd ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ngày sinh</p>
                        <p class="text-gray-800">{{ $cuDan->ngay_sinh?->format('d/m/Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Giới tính</p>
                        <p class="text-gray-800">{{ $cuDan->gioi_tinh_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tỉnh/Thành</p>
                        <p class="text-gray-800">{{ $cuDan->tinh ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Xã/Phường</p>
                        <p class="text-gray-800">{{ $cuDan->xa ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Địa chỉ</p>
                        <p class="text-gray-800">{{ $cuDan->dia_chi ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ngày tạo tài khoản</p>
                        <p class="text-gray-800">{{ $cuDan->created_at?->format('d/m/Y H:i') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Trạng thái</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ttLabel['class'] }}">
                            {{ $ttLabel['text'] }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Thông tin căn hộ --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800">Lịch sử cư trú</h2>
                    <span class="ml-auto text-xs text-gray-400">{{ $cuDan->cuDanCanHo->count() }} căn hộ</span>
                </div>

                @if($cuDan->cuDanCanHo->isEmpty())
                <div class="p-8 text-center text-gray-400 text-sm">Chưa có thông tin cư trú.</div>
                @else
                <div class="divide-y divide-gray-100">
                    @foreach($cuDan->cuDanCanHo as $record)
                    @php $ch = $record->canHo; @endphp
                    <div class="p-5 flex items-start gap-4 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-xl {{ $record->trang_thai == 1 ? 'bg-emerald-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 {{ $record->trang_thai == 1 ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <div class="flex-1 min-w-0 grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            <div>
                                <p class="text-xs text-gray-400">Tòa nhà</p>
                                <p class="font-medium text-gray-800">{{ $ch?->toaNha?->ten_toa_nha ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Căn hộ</p>
                                <p class="font-medium text-gray-800">{{ $ch?->so_can_ho ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Loại căn hộ</p>
                                <p class="text-gray-600">{{ $ch?->loaiCanHo?->ten_loai_can_ho ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Tầng</p>
                                <p class="text-gray-600">{{ $ch?->tang ? 'Tầng '.$ch->tang : '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Vai trò</p>
                                <p class="text-gray-600">{{ $record->vaiTro?->vai_tro ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Trạng thái</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $record->trang_thai == 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $record->trang_thai == 1 ? 'Đang ở' : 'Đã kết thúc' }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Ngày vào ở</p>
                                <p class="text-gray-600">{{ $record->ngay_chuyen_den?->format('d/m/Y') ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Ngày kết thúc</p>
                                <p class="text-gray-600">{{ $record->ngay_chuyen_di?->format('d/m/Y') ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Phương tiện --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l1 1h11M13 16l1-4 3-1 3 4-1 1H14zM9 11H4"/></svg>
                    </div>
                    <h2 class="text-sm font-semibold text-gray-800">Phương tiện</h2>
                    <span class="ml-auto text-xs text-gray-400">{{ $phuongTiens->count() }} xe</span>
                    @if($canHo)
                    <a href="{{ route('resident.phuong-tien.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Quản lý</a>
                    @endif
                </div>

                @if($phuongTiens->isEmpty())
                <div class="p-8 text-center text-gray-400 text-sm">
                    @if($canHo)
                        Chưa có phương tiện đăng ký.
                        <a href="{{ route('resident.phuong-tien.create') }}" class="text-emerald-600 hover:underline ml-1">Đăng ký ngay</a>
                    @else
                        Chưa có thông tin căn hộ.
                    @endif
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Biển số</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tên phương tiện</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Loại</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($phuongTiens as $pt)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-semibold text-gray-800">{{ $pt->bien_so }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $pt->ten_phuong_tien ?: '—' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $pt->loaiPhuongTien?->ten_loai_phuong_tien ?? '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pt->trang_thai == 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $pt->trang_thai == 1 ? 'Hoạt động' : 'Đã hủy' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

        </div>

        {{-- Right: Stats Cards --}}
        <div class="space-y-5">

            {{-- Hóa đơn --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-800">Hóa đơn</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Tổng số</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $hoaDonStats['tong'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Chưa thanh toán</span>
                        <span class="text-sm font-semibold {{ $hoaDonStats['chua_thanh_toan'] > 0 ? 'text-amber-600' : 'text-gray-800' }}">{{ $hoaDonStats['chua_thanh_toan'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Đã thanh toán</span>
                        <span class="text-sm font-semibold text-emerald-600">{{ $hoaDonStats['da_thanh_toan'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Quá hạn</span>
                        <span class="text-sm font-semibold {{ $hoaDonStats['qua_han'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $hoaDonStats['qua_han'] }}</span>
                    </div>
                </div>
                @if($canHo)
                <div class="px-5 pb-5">
                    <a href="{{ route('resident.hoa-don.index') }}"
                       class="block text-center text-sm text-emerald-600 hover:text-emerald-700 font-medium py-2 rounded-xl border border-emerald-200 hover:bg-emerald-50 transition-colors">
                        Xem chi tiết hóa đơn →
                    </a>
                </div>
                @endif
            </div>

            {{-- Yêu cầu --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-800">Yêu cầu / Phản ánh</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Tổng số</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $yeuCauStats['tong'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Đang xử lý</span>
                        <span class="text-sm font-semibold {{ $yeuCauStats['dang_xu_ly'] > 0 ? 'text-amber-600' : 'text-gray-800' }}">{{ $yeuCauStats['dang_xu_ly'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Hoàn thành</span>
                        <span class="text-sm font-semibold text-emerald-600">{{ $yeuCauStats['hoan_thanh'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Đã từ chối</span>
                        <span class="text-sm font-semibold text-gray-600">{{ $yeuCauStats['da_huy'] }}</span>
                    </div>
                </div>
                <div class="px-5 pb-5">
                    <a href="{{ route('resident.yeu-cau.index') }}"
                       class="block text-center text-sm text-emerald-600 hover:text-emerald-700 font-medium py-2 rounded-xl border border-emerald-200 hover:bg-emerald-50 transition-colors">
                        Xem yêu cầu →
                    </a>
                </div>
            </div>

            {{-- Thông báo --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-rose-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-800">Thông báo</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Tổng thông báo</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $thongBaoStats['tong'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Chưa đọc</span>
                        <span class="inline-flex items-center gap-1 text-sm font-semibold {{ $thongBaoStats['chua_doc'] > 0 ? 'text-rose-600' : 'text-gray-800' }}">
                            @if($thongBaoStats['chua_doc'] > 0)
                            <span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span>
                            @endif
                            {{ $thongBaoStats['chua_doc'] }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Đã đọc</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $thongBaoStats['da_doc'] }}</span>
                    </div>
                </div>
                <div class="px-5 pb-5">
                    <a href="{{ route('resident.thong-bao.index') }}"
                       class="block text-center text-sm text-emerald-600 hover:text-emerald-700 font-medium py-2 rounded-xl border border-emerald-200 hover:bg-emerald-50 transition-colors">
                        Xem thông báo →
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
