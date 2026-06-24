@extends('layouts.admin')
@section('title', 'Chi tiết cư trú #' . $cuDanCanHo->id)

@section('content')
<div class="max-w-5xl space-y-5">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.cu-dan-can-ho.index') }}" class="hover:text-indigo-600 transition-colors">Cư dân - Căn hộ</a>
        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 font-medium">#{{ $cuDanCanHo->id }}</span>
    </nav>

    {{-- Top actions --}}
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('admin.cu-dan-can-ho.edit', $cuDanCanHo) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Chỉnh sửa
        </a>
        <form method="POST" action="{{ route('admin.cu-dan-can-ho.toggle-status', $cuDanCanHo) }}"
              onsubmit="return confirm('Xác nhận thay đổi trạng thái?')">
            @csrf @method('PATCH')
            @if($cuDanCanHo->trang_thai == 1)
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-xl transition-colors">
                Đánh dấu Đã chuyển đi
            </button>
            @else
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
                Kích hoạt lại
            </button>
            @endif
        </form>
        @php $ts = $dsTrangThai[$cuDanCanHo->trang_thai] ?? ['text'=>'?','class'=>'bg-gray-100 text-gray-500']; @endphp
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $ts['class'] }}">
            {{ $ts['text'] }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Left 2/3 --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- CARD: Thông tin cư dân --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin cư dân</h2>
                </div>
                <div class="px-6 py-5">
                    @if($cuDanCanHo->cuDan)
                    @php $cd = $cuDanCanHo->cuDan; @endphp
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg flex-shrink-0">
                            {{ strtoupper(mb_substr($cd->ten ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $cd->ho_ten }}</p>
                            <p class="text-sm text-gray-500">{{ $cd->email ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-0.5">CCCD</p>
                            <p class="text-sm font-semibold text-gray-700 font-mono">{{ $cd->cccd ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-0.5">Số điện thoại</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $cd->sdt ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-0.5">Ngày sinh</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $cd->ngay_sinh?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-0.5">Giới tính</p>
                            <p class="text-sm font-semibold text-gray-700">
                                @if($cd->gioi_tinh === 1) Nam
                                @elseif($cd->gioi_tinh === 0) Nữ
                                @else —
                                @endif
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 col-span-2">
                            <p class="text-xs text-gray-400 mb-0.5">Địa chỉ</p>
                            <p class="text-sm font-semibold text-gray-700">
                                {{ collect([$cd->dia_chi, $cd->xa, $cd->tinh])->filter()->implode(', ') ?: '—' }}
                            </p>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-400">Không tìm thấy thông tin cư dân.</p>
                    @endif
                </div>
            </div>

            {{-- CARD: Thông tin căn hộ + tòa nhà --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin căn hộ</h2>
                </div>
                <div class="px-6 py-5">
                    @if($cuDanCanHo->canHo)
                    @php $ch = $cuDanCanHo->canHo; $tn = $ch->toaNha; @endphp
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                            <p class="text-xs text-gray-400 mb-0.5">Mã căn hộ</p>
                            <p class="text-sm font-bold text-gray-800">{{ $ch->so_can_ho ?? '—' }}</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                            <p class="text-xs text-gray-400 mb-0.5">Tầng</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $ch->tang ?? '—' }}</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                            <p class="text-xs text-gray-400 mb-0.5">Loại căn hộ</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $ch->loaiCanHo?->ten_loai_can_ho ?? '—' }}</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                            <p class="text-xs text-gray-400 mb-0.5">Trạng thái căn hộ</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $ch->trangThai?->ten_trang_thai ?? '—' }}</p>
                        </div>
                        @if($ch->dien_tich)
                        <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                            <p class="text-xs text-gray-400 mb-0.5">Diện tích</p>
                            <p class="text-sm font-semibold text-gray-700">{{ number_format($ch->dien_tich, 1) }} m²</p>
                        </div>
                        @endif
                        @if($ch->gia)
                        <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                            <p class="text-xs text-gray-400 mb-0.5">Giá</p>
                            <p class="text-sm font-semibold text-gray-700">{{ number_format($ch->gia, 0, ',', '.') }} đ</p>
                        </div>
                        @endif
                    </div>
                    @if($tn)
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-400 mb-2 uppercase tracking-wide font-medium">Tòa nhà</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                <p class="text-xs text-gray-400 mb-0.5">Tên tòa nhà</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $tn->ten_toa_nha }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                <p class="text-xs text-gray-400 mb-0.5">Số tầng</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $tn->so_tang ?? '—' }}</p>
                            </div>
                            @if($tn->dia_chi)
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 col-span-2">
                                <p class="text-xs text-gray-400 mb-0.5">Địa chỉ tòa nhà</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $tn->dia_chi }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    @else
                    <p class="text-sm text-gray-400">Không tìm thấy thông tin căn hộ.</p>
                    @endif
                </div>
            </div>

            {{-- CARD: Phương tiện --}}
            @if($cuDanCanHo->canHo?->phuongTien->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Phương tiện
                        <span class="ml-2 text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-normal">
                            {{ $cuDanCanHo->canHo->phuongTien->count() }}
                        </span>
                    </h2>
                </div>
                <div class="px-6 py-4 space-y-2">
                    @foreach($cuDanCanHo->canHo->phuongTien as $pt)
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="text-sm font-bold font-mono text-gray-800">{{ $pt->bien_so }}</div>
                            <div class="text-xs text-gray-500">{{ $pt->loaiPhuongTien?->ten_loai_phuong_tien ?? '—' }}</div>
                            @if($pt->ten_phuong_tien)
                            <div class="text-xs text-gray-400">{{ $pt->ten_phuong_tien }}</div>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pt->trang_thai ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $pt->trang_thai ? 'Hoạt động' : 'Đã hủy' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- CARD: Dịch vụ --}}
            @if($cuDanCanHo->canHo?->phiDichVu->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Dịch vụ sử dụng</h2>
                </div>
                <div class="px-6 py-4 space-y-2">
                    @foreach($cuDanCanHo->canHo->phiDichVu as $dv)
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                        <span class="text-sm text-gray-700">{{ $dv->ten_phi_dich_vu }}</span>
                        <span class="text-sm font-semibold text-gray-800">
                            {{ number_format($dv->pivot->don_gia ?? $dv->don_gia, 0, ',', '.') }} đ
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right 1/3 --}}
        <div class="space-y-5">

            {{-- CARD: Thông tin cư trú --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin cư trú</h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div>
                        <p class="text-xs text-gray-400">Mã bản ghi</p>
                        <p class="text-sm font-semibold text-gray-700 font-mono mt-0.5">#{{ $cuDanCanHo->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Vai trò trong căn hộ</p>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $cuDanCanHo->vaiTro?->vai_tro ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Ngày chuyển đến</p>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $cuDanCanHo->ngay_chuyen_den?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Ngày chuyển đi</p>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $cuDanCanHo->ngay_chuyen_di?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Trạng thái</p>
                        @php $ts = $dsTrangThai[$cuDanCanHo->trang_thai] ?? ['text'=>'?','class'=>'bg-gray-100 text-gray-500']; @endphp
                        <span class="inline-flex mt-1 items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ts['class'] }}">
                            {{ $ts['text'] }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Ngày tạo</p>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $cuDanCanHo->createdAt?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Cập nhật lần cuối</p>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $cuDanCanHo->updatedAt?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- CARD: Hóa đơn gần nhất --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Hóa đơn gần nhất</h2>
                </div>
                <div class="px-5 py-4">
                    @if($hoaDonGanNhat)
                    @php
                        $hdClass = match($hoaDonGanNhat->trang_thai) {
                            1 => 'bg-yellow-100 text-yellow-700',
                            2 => 'bg-emerald-100 text-emerald-700',
                            3 => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-500',
                        };
                    @endphp
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Tháng/Năm</span>
                            <span class="text-sm font-semibold text-gray-700">{{ $hoaDonGanNhat->thang }}/{{ $hoaDonGanNhat->nam }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Tổng tiền</span>
                            <span class="text-sm font-semibold text-gray-700">{{ number_format($hoaDonGanNhat->tong_tien, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Đã thanh toán</span>
                            <span class="text-sm font-semibold text-gray-700">{{ number_format($hoaDonGanNhat->so_tien_da_thanh_toan ?? 0, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Hạn TT</span>
                            <span class="text-sm font-semibold text-gray-700">{{ $hoaDonGanNhat->han_thanh_toan?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-1">
                            <span class="text-xs text-gray-400">Trạng thái</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $hdClass }}">
                                {{ $hoaDonGanNhat->trang_thai_label }}
                            </span>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-400">Chưa có hóa đơn.</p>
                    @endif
                </div>
            </div>

            {{-- CARD: Yêu cầu cư dân --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Yêu cầu</h2>
                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-semibold">{{ $tongYeuCau }}</span>
                </div>
                <div class="px-5 py-4">
                    @if($yeuCauGanNhat)
                    <div class="space-y-2">
                        <p class="text-xs text-gray-400">Yêu cầu gần nhất</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $yeuCauGanNhat->tieu_de }}</p>
                        <p class="text-xs text-gray-500">{{ $yeuCauGanNhat->loaiYeuCau?->name ?? '—' }}</p>
                        <div class="flex items-center justify-between pt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $yeuCauGanNhat->trang_thai_label['class'] }}">
                                {{ $yeuCauGanNhat->trang_thai_label['text'] }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $yeuCauGanNhat->ngay_gui?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-400">Chưa có yêu cầu nào.</p>
                    @endif
                </div>
            </div>

            <a href="{{ route('admin.cu-dan-can-ho.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại danh sách
            </a>
        </div>
    </div>
</div>
@endsection
