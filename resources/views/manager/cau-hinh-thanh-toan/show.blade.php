@extends('layouts.manager')
@section('title', 'Chi tiết cấu hình thanh toán')
@section('page-title', 'Chi tiết cấu hình thanh toán')

@section('content')
<div class="max-w-2xl space-y-5" x-data="{ confirmToggle: false }">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6 flex items-start gap-5">
            <div class="w-14 h-14 rounded-xl flex-shrink-0 flex items-center justify-center {{ $cauHinhThanhToan->trang_thai == 1 ? 'bg-emerald-100' : 'bg-gray-100' }}">
                <svg class="w-7 h-7 {{ $cauHinhThanhToan->trang_thai == 1 ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-gray-800">{{ $cauHinhThanhToan->ten_nha_cung_cap ?? $cauHinhThanhToan->loai_phuong_thuc }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $cauHinhThanhToan->loai_phuong_thuc }}</span>
                </div>
                @if($cauHinhThanhToan->trang_thai == 1)
                <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Đang hoạt động</span>
                @else
                <span class="text-xs font-semibold text-red-500 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Vô hiệu hóa</span>
                @endif
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap gap-3">
            <a href="{{ route('manager.cau-hinh-thanh-toan.edit', $cauHinhThanhToan) }}"
               class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <button type="button" @click="confirmToggle = true"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $cauHinhThanhToan->trang_thai == 1 ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-emerald-500 hover:bg-emerald-600 text-white' }}">
                {{ $cauHinhThanhToan->trang_thai == 1 ? 'Vô hiệu hóa' : 'Kích hoạt' }}
            </button>
            <a href="{{ route('manager.cau-hinh-thanh-toan.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Thông tin cấu hình</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            @php
            $fields = [
                ['label' => 'Loại phương thức', 'value' => $cauHinhThanhToan->loai_phuong_thuc],
                ['label' => 'Ngân hàng / Nhà cung cấp', 'value' => $cauHinhThanhToan->ten_nha_cung_cap],
                ['label' => 'Số TK / Định danh thu hưởng', 'value' => $cauHinhThanhToan->dinh_danh_thu_huong, 'mono' => true],
                ['label' => 'Mã nhận diện (QR)', 'value' => $cauHinhThanhToan->ma_nhan_dien, 'mono' => true],
                ['label' => 'Chủ tài khoản', 'value' => $cauHinhThanhToan->ten_chu_tai_khoan],
                ['label' => 'Người cập nhật', 'value' => $cauHinhThanhToan->nguoiCapNhat?->ho_ten],
                ['label' => 'Ngày tạo', 'value' => $cauHinhThanhToan->createdAt?->format('d/m/Y H:i')],
                ['label' => 'Cập nhật lần cuối', 'value' => $cauHinhThanhToan->updatedAt?->format('d/m/Y H:i')],
            ];
            @endphp
            @foreach($fields as $f)
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">{{ $f['label'] }}</dt>
                <dd class="text-sm font-semibold text-gray-700 break-all {{ ($f['mono'] ?? false) ? 'font-mono' : '' }}">{{ $f['value'] ?? '—' }}</dd>
            </div>
            @endforeach
        </div>
    </div>

    <template x-teleport="body">
    <div x-show="confirmToggle"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="confirmToggle = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-5">
                <p class="font-semibold text-gray-800">{{ $cauHinhThanhToan->trang_thai == 1 ? 'Vô hiệu hóa cấu hình?' : 'Kích hoạt cấu hình?' }}</p>
            </div>
            <div class="flex gap-3">
                <button @click="confirmToggle = false" class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50">Hủy</button>
                <form action="{{ route('manager.cau-hinh-thanh-toan.toggle-status', $cauHinhThanhToan) }}" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    @if($cauHinhThanhToan->trang_thai == 1)
                    <button type="submit" class="block w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition-colors">Vô hiệu hóa</button>
                    @else
                    <button type="submit" class="block w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">Kích hoạt</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection
