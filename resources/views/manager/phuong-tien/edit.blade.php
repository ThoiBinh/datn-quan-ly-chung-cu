@extends('layouts.manager')
@section('title', 'Sửa phương tiện')
@section('page-title', 'Sửa phương tiện')

@php
    $residentsMap = $dsCanHo->mapWithKeys(fn($ch) => [
        $ch->id => $ch->cuDanHienTai->map(fn($cdch) => [
            'ho_ten' => $cdch->cuDan?->ho_ten,
            'cccd'   => $cdch->cuDan?->cccd,
        ])->filter(fn($r) => $r['ho_ten'])->values(),
    ]);
@endphp

@section('content')
<div class="max-w-2xl" x-data="{
        canHo: '{{ old('can_ho', $phuongTien->can_ho) }}',
        trangThai: '{{ old('trang_thai', $phuongTien->trang_thai) }}',
        residents: {{ Js::from($residentsMap) }},
    }">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100 font-mono">{{ $phuongTien->bien_so }}</h2>
        </div>
        <form method="POST" action="{{ route('manager.phuong-tien.update', $phuongTien) }}" class="p-5 space-y-4">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                    @foreach($errors->all() as $e)<p class="text-red-700 dark:text-red-300 text-sm">{{ $e }}</p>@endforeach
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Căn hộ <span class="text-red-500">*</span></label>
                    <select name="can_ho" x-model="canHo" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($dsCanHo as $ch)
                        <option value="{{ $ch->id }}">{{ $ch->so_can_ho }} - {{ $ch->toaNha?->ten_toa_nha }} ({{ $ch->loaiCanHo?->ten_loai_can_ho }})</option>
                        @endforeach
                    </select>

                    <div class="mt-2 text-xs" x-show="canHo">
                        <template x-if="residents[canHo] && residents[canHo].length">
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="cd in residents[canHo]" :key="cd.cccd">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800">
                                        <span x-text="cd.ho_ten"></span>
                                        <span class="text-indigo-400 dark:text-indigo-500" x-text="cd.cccd ? '· ' + cd.cccd : ''"></span>
                                    </span>
                                </template>
                            </div>
                        </template>
                        <template x-if="!residents[canHo] || !residents[canHo].length">
                            <p class="text-gray-400 dark:text-gray-500">Căn hộ chưa có cư dân nào.</p>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Biển số xe <span class="text-red-500">*</span></label>
                    <input type="text" name="bien_so" value="{{ old('bien_so', $phuongTien->bien_so) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loại phương tiện <span class="text-red-500">*</span></label>
                    <select name="loai_phuong_tien" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($dsLoai as $l)
                        <option value="{{ $l->id }}" {{ old('loai_phuong_tien', $phuongTien->loai_phuong_tien) == $l->id ? 'selected' : '' }}>{{ $l->ten_loai_phuong_tien }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tên / Hãng xe</label>
                    <input type="text" name="ten_phuong_tien" value="{{ old('ten_phuong_tien', $phuongTien->ten_phuong_tien) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ngày đăng ký</label>
                    <input type="date" name="ngay_dang_ky" value="{{ old('ngay_dang_ky', $phuongTien->ngay_dang_ky?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trạng thái</label>
                    <select name="trang_thai" x-model="trangThai"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="1">Đang sử dụng</option>
                        <option value="0">Đã hủy</option>
                    </select>
                </div>
                <div x-show="trangThai == '0'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ngày hủy</label>
                    <input type="date" name="ngay_huy" value="{{ old('ngay_huy', $phuongTien->ngay_huy?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">Lưu</button>
                <a href="{{ route('manager.phuong-tien.show', $phuongTien) }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
