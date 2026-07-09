@extends('layouts.admin')
@section('title', 'Sửa căn hộ')
@section('page-title', 'Chỉnh sửa căn hộ')

@section('content')
<div class="max-w-3xl">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400 mb-5">
        <a href="{{ route('admin.can-ho.index') }}" class="hover:text-emerald-600 transition-colors">Căn hộ</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.can-ho.show', $canHo) }}" class="hover:text-emerald-600 transition-colors font-mono">{{ $canHo->so_can_ho }}</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Chỉnh sửa</span>
    </nav>

    @if($errors->any())
    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <ul class="text-red-700 dark:text-red-400 text-sm space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.can-ho.update', $canHo) }}" class="space-y-5">
        @csrf @method('PUT')

        {{-- Thông tin căn hộ --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span class="font-semibold text-gray-800 dark:text-white font-mono">{{ $canHo->so_can_ho }}</span>
                </div>
                <span class="text-xs text-gray-400 dark:text-slate-500 font-mono">ID #{{ $canHo->id }}</span>
            </div>

            <div class="p-6 space-y-5"
                 x-data="{
                    toaNhaMap: @json($dsToaNha->pluck('tien_to', 'id')),
                    toaNha: '{{ old('toa_nha', $canHo->toa_nha) }}',
                    tang: '{{ old('tang', $canHo->tang) }}',
                    soPhong: '{{ old('so_phong', $currentSoPhong) }}',
                    get soCanHoPreview() {
                        const prefix = this.toaNhaMap[this.toaNha];
                        if (!prefix || !this.tang || !this.soPhong) return '—';
                        const tangStr = String(this.tang).padStart(2, '0');
                        const phongStr = String(this.soPhong).padStart(3, '0');
                        return String(prefix).toUpperCase() + tangStr + phongStr;
                    }
                 }">

                <!-- Tòa nhà -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Tòa nhà <span class="text-red-500">*</span>
                    </label>
                    <select name="toa_nha" required x-model="toaNha"
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('toa_nha') border-red-400 @enderror">
                        @foreach($dsToaNha as $tn)
                        <option value="{{ $tn->id }}">{{ $tn->ten_toa_nha }} ({{ $tn->tien_to }})</option>
                        @endforeach
                    </select>
                    @error('toa_nha')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- Tầng + Số phòng -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                            Tầng <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="tang" x-model="tang" required min="1"
                               class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('tang') border-red-400 @enderror">
                        @error('tang')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                            Số phòng <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="so_phong" x-model="soPhong" required min="1" max="999"
                               class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('so_phong') border-red-400 @enderror">
                        @error('so_phong')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Số căn hộ (preview, tự động sinh) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Số căn hộ (tự động sinh)
                    </label>
                    <div class="w-full px-3.5 py-2.5 border border-dashed border-gray-300 dark:border-slate-600 dark:bg-slate-700/50 rounded-lg text-sm font-mono font-semibold text-gray-700 dark:text-slate-200"
                         x-text="soCanHoPreview"></div>
                    <p class="mt-1 text-xs text-gray-400 dark:text-slate-500">Được sinh tự động từ Tiền tố tòa nhà + Tầng + Số phòng.</p>
                </div>

                <!-- Loại căn hộ -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Loại căn hộ <span class="text-red-500">*</span>
                    </label>
                    <select name="loai_can_ho" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('loai_can_ho') border-red-400 @enderror">
                        @foreach($dsLoaiCanHo as $loai)
                        <option value="{{ $loai->id }}" @selected(old('loai_can_ho', $canHo->loai_can_ho) == $loai->id)>{{ $loai->ten_loai_can_ho }}</option>
                        @endforeach
                    </select>
                    @error('loai_can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- Trạng thái -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Trạng thái <span class="text-red-500">*</span>
                    </label>
                    <select name="trang_thai" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('trang_thai') border-red-400 @enderror">
                        @foreach($dsTrangThai as $tt)
                        <option value="{{ $tt->id }}" @selected(old('trang_thai', $canHo->trang_thai) == $tt->id)>{{ $tt->ten_trang_thai }}</option>
                        @endforeach
                    </select>
                    @error('trang_thai')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- Giá -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Giá (VNĐ)</label>
                    <input type="number" name="gia" value="{{ old('gia', $canHo->gia) }}" min="0" step="1000"
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

            </div>
        </div>

        {{-- Thuộc tính căn hộ --}}
        @if($dsThuocTinh->isNotEmpty())
        @php $hasOldInput = old('toa_nha') !== null; @endphp
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 dark:text-white">Thuộc tính căn hộ</h2>
                    <p class="text-xs text-gray-400 dark:text-slate-500">Bỏ chọn để xóa thuộc tính. Tích chọn và nhập giá trị để thêm mới.</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($dsThuocTinh as $tt)
                @php
                    $ttChecked = $hasOldInput
                        ? (bool) old('thuoc_tinh.'.$tt->id.'.active')
                        : $currentThuocTinh->has($tt->id);
                    $ttValue = old(
                        'thuoc_tinh.'.$tt->id.'.gia_tri',
                        $currentThuocTinh->get($tt->id)?->pivot->gia_tri_thuoc_tinh ?? ''
                    );
                @endphp
                <div x-data="{ checked: {{ $ttChecked ? 'true' : 'false' }} }"
                     class="flex flex-col gap-2 p-3.5 border rounded-lg transition-colors"
                     :class="checked ? 'border-purple-300 dark:border-purple-700 bg-purple-50/50 dark:bg-purple-900/10' : 'border-gray-200 dark:border-slate-600'">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox"
                               x-model="checked"
                               name="thuoc_tinh[{{ $tt->id }}][active]"
                               value="1"
                               class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-slate-300">{{ $tt->ten_thuoc_tinh }}</span>
                        @if($currentThuocTinh->has($tt->id))
                        <span class="text-xs text-purple-500 dark:text-purple-400">(đang có)</span>
                        @endif
                    </label>
                    <div x-show="checked" x-transition class="pl-6">
                        <input type="text"
                               name="thuoc_tinh[{{ $tt->id }}][gia_tri]"
                               value="{{ $ttValue }}"
                               placeholder="Nhập giá trị..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                Lưu thay đổi
            </button>
            <a href="{{ route('admin.can-ho.show', $canHo) }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
