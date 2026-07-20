@extends('layouts.manager')
@section('title', 'Thêm phí dịch vụ căn hộ')
@section('page-title', 'Thêm phí dịch vụ căn hộ')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('manager.can-ho-phi-dich-vu.index') }}" class="hover:text-indigo-600 transition-colors">Phí dịch vụ căn hộ</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-gray-200">Thêm mới</span>
    </nav>

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li class="text-sm text-red-600 dark:text-red-400">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('manager.can-ho-phi-dich-vu.store') }}"
          x-data="{
              canHoId: '{{ old('can_ho') }}',
              phiDvId: '{{ old('phi_dich_vu') }}',
              donGia: '{{ old('don_gia') }}',
              canHoData: {{ $canHoJson }},
              phiDvData: {{ $phiDvJson }},
              get selectedCanHo() { return this.canHoData.find(c => c.id == this.canHoId) },
              get selectedPhiDv() { return this.phiDvData.find(p => p.id == this.phiDvId) },
              onPhiDvChange() {
                  let p = this.selectedPhiDv;
                  if (p) this.donGia = p.don_gia;
              }
          }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Chọn căn hộ --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Chọn căn hộ</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Căn hộ <span class="text-red-500">*</span></label>
                        <select name="can_ho" x-model="canHoId"
                                class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('can_ho') border-red-400 @enderror">
                            <option value="">-- Chọn căn hộ --</option>
                            @foreach($dsCanHo as $ch)
                            <option value="{{ $ch->id }}">
                                {{ $ch->toaNha->ten_toa_nha ?? '' }} - Căn {{ $ch->so_can_ho }} (Tầng {{ $ch->tang }})
                            </option>
                            @endforeach
                        </select>
                        @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Thông tin căn hộ đã chọn --}}
                    <template x-if="selectedCanHo">
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-lg p-3 space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Tòa nhà</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedCanHo.toa_nha?.ten_toa_nha ?? selectedCanHo.toa_nha_ten ?? '—'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Số căn hộ</span>
                                <span class="font-medium text-indigo-600 dark:text-indigo-400" x-text="selectedCanHo.so_can_ho"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Tầng</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedCanHo.tang"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Chọn phí dịch vụ --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Chọn phí dịch vụ</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Phí dịch vụ <span class="text-red-500">*</span></label>
                        <select name="phi_dich_vu" x-model="phiDvId" @change="onPhiDvChange()"
                                class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('phi_dich_vu') border-red-400 @enderror">
                            <option value="">-- Chọn dịch vụ --</option>
                            @foreach($dsPhiDV as $pdv)
                            <option value="{{ $pdv->id }}">
                                {{ $pdv->ten_phi_dich_vu }} — {{ number_format((float)$pdv->don_gia,0,',','.') }}đ/{{ $pdv->donViTinh?->don_vi ?? '?' }}
                            </option>
                            @endforeach
                        </select>
                        @error('phi_dich_vu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Thông tin dịch vụ đã chọn --}}
                    <template x-if="selectedPhiDv">
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-lg p-3 space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Tên phí</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedPhiDv.ten_phi_dich_vu"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Loại phí</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedPhiDv.loai ?? '—'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Đơn vị tính</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedPhiDv.don_vi ?? '—'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Đơn giá gốc</span>
                                <span class="font-semibold text-emerald-700 dark:text-emerald-400" x-text="Number(selectedPhiDv.don_gia).toLocaleString('vi-VN') + 'đ'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Loại tính phí</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedPhiDv.loai_tinh ?? '—'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        {{-- Đơn giá áp dụng --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Đơn giá áp dụng</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Để trống sẽ lấy theo đơn giá mặc định của dịch vụ</p>
            </div>
            <div class="p-5">
                <div class="max-w-xs">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Đơn giá (VNĐ)</label>
                    <input type="number" name="don_gia" x-model="donGia" min="0" step="0.01"
                           placeholder="Nhập đơn giá hoặc để trống"
                           class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('don_gia') border-red-400 @enderror">
                    @error('don_gia')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('manager.can-ho-phi-dich-vu.index') }}"
               class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Hủy
            </a>
            <button type="submit"
                    class="px-5 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                Lưu áp dụng dịch vụ
            </button>
        </div>

    </form>
</div>
@endsection
