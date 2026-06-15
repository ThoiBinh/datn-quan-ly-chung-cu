@extends('layouts.manager')
@section('title', 'Chi tiết căn hộ')
@section('page-title', 'Căn hộ ' . $canHo->so_can_ho)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Thông tin chính -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Thông tin căn hộ</h3>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">Tòa nhà</dt><dd class="font-medium mt-0.5">{{ $canHo->toaNha?->ten_toa_nha }}</dd></div>
                <div><dt class="text-gray-500">Số căn hộ</dt><dd class="font-medium mt-0.5">{{ $canHo->so_can_ho }}</dd></div>
                <div><dt class="text-gray-500">Tầng</dt><dd class="font-medium mt-0.5">{{ $canHo->tang }}</dd></div>
                <div><dt class="text-gray-500">Diện tích</dt><dd class="font-medium mt-0.5">{{ $canHo->dien_tich ? number_format($canHo->dien_tich, 1) . ' m²' : '-' }}</dd></div>
                <div><dt class="text-gray-500">Giá</dt><dd class="font-medium mt-0.5">{{ $canHo->gia ? number_format($canHo->gia) . 'đ' : '-' }}</dd></div>
                <div><dt class="text-gray-500">Trạng thái</dt><dd class="mt-0.5"><span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $canHo->trangThai?->ten_trang_thai }}</span></dd></div>
                <div><dt class="text-gray-500">Loại căn hộ</dt><dd class="font-medium mt-0.5">{{ $canHo->loaiCanHo?->ten_loai_can_ho ?? '-' }}</dd></div>
            </dl>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('manager.can-ho.edit', $canHo) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">Sửa căn hộ</a>
            </div>
        </div>

        <!-- Cư dân -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Cư dân hiện tại</h3>
            @forelse($canHo->cuDanHienTai as $cdch)
            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 mb-2">
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold">
                    {{ strtoupper(substr($cdch->cuDan?->ho_ten ?? 'N', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $cdch->cuDan?->ho_ten }}</p>
                    <p class="text-xs text-gray-500">{{ $cdch->vaiTro?->vai_tro }} · {{ $cdch->ngay_chuyen_den?->format('d/m/Y') }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Chưa có cư dân</p>
            @endforelse
        </div>
    </div>

    <div class="space-y-6">
        <!-- Phương tiện -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Phương tiện ({{ count($canHo->phuongTien) }})</h3>
            @forelse($canHo->phuongTien as $pt)
            <div class="text-sm py-1.5 border-b border-gray-100 last:border-0">
                <span class="font-medium text-gray-800">{{ $pt->bien_so }}</span>
                <span class="text-gray-500 ml-2">{{ $pt->loaiPhuongTien?->ten_loai_phuong_tien }}</span>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Chưa đăng ký xe</p>
            @endforelse
        </div>

        <!-- Phí dịch vụ -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Phí dịch vụ</h3>
            @forelse($canHo->phiDichVu as $phi)
            <div class="text-sm py-1.5 border-b border-gray-100 last:border-0 flex justify-between">
                <span class="text-gray-700">{{ $phi->ten_phi_dich_vu }}</span>
                <span class="font-medium text-gray-800">{{ number_format($phi->pivot->don_gia ?? $phi->don_gia) }}đ</span>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Chưa có phí</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
