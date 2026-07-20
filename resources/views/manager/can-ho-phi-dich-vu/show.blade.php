@extends('layouts.manager')
@section('title', 'Chi tiết phí dịch vụ căn hộ')
@section('page-title', 'Chi tiết phí dịch vụ căn hộ')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('manager.can-ho-phi-dich-vu.index') }}" class="hover:text-indigo-600 transition-colors">Phí dịch vụ căn hộ</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-gray-200 truncate max-w-xs">
            {{ $canHoPhiDichVu->canHo->so_can_ho ?? '?' }} — {{ $canHoPhiDichVu->phiDichVu->ten_phi_dich_vu ?? '?' }}
        </span>
    </nav>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
         class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 rounded-xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <h1 class="text-base font-bold text-gray-900 dark:text-gray-100">
                    Căn {{ $canHoPhiDichVu->canHo->so_can_ho ?? '—' }}
                    <span class="text-gray-400 font-normal mx-1">·</span>
                    {{ $canHoPhiDichVu->phiDichVu->ten_phi_dich_vu ?? '—' }}
                </h1>
                <p class="text-sm text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5 tabular-nums">
                    {{ number_format((float)$canHoPhiDichVu->don_gia, 0, ',', '.') }}đ / {{ $canHoPhiDichVu->phiDichVu->donViTinh?->don_vi ?? '?' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('manager.can-ho-phi-dich-vu.edit', $canHoPhiDichVu) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            @if(!$coHoaDon)
            <button x-data @click="if(confirm('Xác nhận xóa áp dụng dịch vụ này?')) $refs.delForm.submit()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg transition-colors border border-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Xóa
            </button>
            <form x-ref="delForm" action="{{ route('manager.can-ho-phi-dich-vu.destroy', $canHoPhiDichVu) }}" method="POST" class="hidden">
                @csrf @method('DELETE')
            </form>
            @else
            <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-50 text-amber-700 text-xs font-medium rounded-lg border border-amber-200" title="Dịch vụ đã phát sinh hóa đơn nên không thể xóa">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Có hóa đơn
            </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Card 1: Thông tin căn hộ --}}
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Thông tin căn hộ</h2>
            </div>
            @php $canHo = $canHoPhiDichVu->canHo; @endphp
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Tòa nhà</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $canHo->toaNha->ten_toa_nha ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Số căn hộ</dt>
                    <dd class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $canHo->so_can_ho ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Tầng</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $canHo->tang ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Loại căn hộ</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $canHo->loaiCanHo->ten_loai_can_ho ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Trạng thái</dt>
                    <dd><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $canHo->trangThai->ten_trang_thai ?? '—' }}</span></dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Chủ hộ</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $canHo->chuHo?->cuDan?->ho_ten ?? '—' }}</dd>
                </div>
            </dl>

            {{-- Thuộc tính căn hộ --}}
            @if($canHo->thuocTinhCanHo->isNotEmpty())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Thuộc tính căn hộ</p>
                <div class="space-y-1.5">
                    @foreach($canHo->thuocTinhCanHo as $tt)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $tt->thuocTinh?->ten_thuoc_tinh ?? '—' }}</span>
                        <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $tt->gia_tri_thuoc_tinh ?? '—' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Danh sách cư dân --}}
            @if($canHo->cuDanHienTai->isNotEmpty())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Cư dân đang ở ({{ $canHo->cuDanHienTai->count() }} người)</p>
                <div class="space-y-1.5">
                    @foreach($canHo->cuDanHienTai as $cdch)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-800 dark:text-gray-200">{{ $cdch->cuDan?->ho_ten ?? '—' }}</span>
                        <span class="text-xs text-gray-400">{{ $cdch->vaiTro?->vai_tro ?? '' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Card 2: Thông tin dịch vụ --}}
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Thông tin dịch vụ</h2>
            </div>
            @php $pdv = $canHoPhiDichVu->phiDichVu; @endphp
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Tên dịch vụ</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200 text-right max-w-[160px]">{{ $pdv->ten_phi_dich_vu ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Loại phí</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $pdv->loaiPhiDichVu?->ten_loai_phi_dich_vu ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Đơn vị tính</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $pdv->donViTinh?->don_vi ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Đơn giá gốc</dt>
                    <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200 tabular-nums">{{ number_format((float)($pdv->don_gia ?? 0), 0, ',', '.') }}đ</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Loại tính phí</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $pdv->loaiTinhPhi?->ten_loai ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Có hóa đơn</dt>
                    <dd>
                        @if($coHoaDon)
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Đã phát sinh</span>
                        @else
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">Chưa</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Card 3: Thông tin áp dụng --}}
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Thông tin áp dụng</h2>
            </div>
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Đơn giá áp dụng</dt>
                    <dd class="text-sm font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">
                        {{ number_format((float)$canHoPhiDichVu->don_gia, 0, ',', '.') }}đ
                    </dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Người cập nhật</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200">{{ $canHoPhiDichVu->nguoiCapNhat?->ho_ten ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Ngày tạo</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200 tabular-nums">
                        {{ $canHoPhiDichVu->createdAt ? \Carbon\Carbon::parse($canHoPhiDichVu->createdAt)->format('d/m/Y H:i') : '—' }}
                    </dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Cập nhật lần cuối</dt>
                    <dd class="text-sm text-gray-800 dark:text-gray-200 tabular-nums">
                        {{ $canHoPhiDichVu->updatedAt ? \Carbon\Carbon::parse($canHoPhiDichVu->updatedAt)->format('d/m/Y H:i') : '—' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
