@extends('layouts.admin')
@section('title', 'Thêm căn hộ')
@section('page-title', 'Thêm căn hộ mới')

@section('content')
<div class="max-w-2xl">

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

    <form method="POST" action="{{ route('admin.can-ho.store') }}"
          class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        @csrf

        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <h2 class="font-semibold text-gray-800 dark:text-white">Thông tin căn hộ</h2>
        </div>

        <div class="p-6 space-y-5">

            <!-- Tòa nhà -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                    Tòa nhà <span class="text-red-500">*</span>
                </label>
                <select name="toa_nha" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Chọn tòa nhà --</option>
                    @foreach($dsToaNha as $tn)
                    <option value="{{ $tn->id }}"
                            @selected(old('toa_nha', request('toa_nha')) == $tn->id)>
                        {{ $tn->ten_toa_nha }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Số căn hộ + Tầng (grid) -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Số căn hộ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="so_can_ho" value="{{ old('so_can_ho') }}" required
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="VD: A101">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Tầng <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="tang" value="{{ old('tang') }}" required min="1"
                           class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="VD: 1">
                </div>
            </div>

            <!-- Loại căn hộ -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                    Loại căn hộ <span class="text-red-500">*</span>
                </label>
                <select name="loai_can_ho" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Chọn loại căn hộ --</option>
                    @foreach($dsLoaiCanHo as $loai)
                    <option value="{{ $loai->id }}" @selected(old('loai_can_ho') == $loai->id)>
                        {{ $loai->ten_loai_can_ho }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Trạng thái -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                    Trạng thái <span class="text-red-500">*</span>
                </label>
                <select name="trang_thai" required
                        class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Chọn trạng thái --</option>
                    @foreach($dsTrangThai as $tt)
                    <option value="{{ $tt->id }}" @selected(old('trang_thai') == $tt->id)>
                        {{ $tt->ten_trang_thai }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Giá -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Giá (VNĐ)</label>
                <input type="number" name="gia" value="{{ old('gia') }}" min="0" step="1000"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="VD: 5000000">
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                Thêm căn hộ
            </button>
            <a href="{{ route('admin.can-ho.index') }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
