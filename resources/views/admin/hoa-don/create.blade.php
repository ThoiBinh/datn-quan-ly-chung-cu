@extends('layouts.admin')
@section('title', 'Tạo hóa đơn')
@section('page-title', 'Tạo hóa đơn')

@section('content')
<div class="max-w-lg mx-auto space-y-5">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.hoa-don.index') }}" class="hover:text-violet-600 transition-colors">Hóa đơn</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Tạo mới</span>
    </nav>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Thông tin hóa đơn</h2>
        </div>

        <div class="px-5 py-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-800">
            <p class="text-xs text-blue-700 dark:text-blue-300">
                Hệ thống sẽ tự động tạo chi tiết khoản thu từ các phí dịch vụ đang áp dụng cho căn hộ.
            </p>
        </div>

        <form action="{{ route('admin.hoa-don.store') }}" method="POST" class="p-5 space-y-5">
            @csrf

            @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <ul class="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                    Căn hộ <span class="text-red-500">*</span>
                </label>
                <select name="can_ho"
                        class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400 {{ $errors->has('can_ho') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    <option value="">-- Chọn căn hộ --</option>
                    @foreach($dsCanHo as $canHo)
                    <option value="{{ $canHo->id }}" {{ old('can_ho') == $canHo->id ? 'selected' : '' }}>
                        {{ $canHo->so_can_ho }} — {{ $canHo->toaNha?->ten_toa_nha ?? '?' }}
                    </option>
                    @endforeach
                </select>
                @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Tháng <span class="text-red-500">*</span>
                    </label>
                    <select name="thang"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400 {{ $errors->has('thang') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Tháng --</option>
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ old('thang', (int)date('m')) == $m ? 'selected' : '' }}>
                            Tháng {{ $m }}
                        </option>
                        @endfor
                    </select>
                    @error('thang')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Năm <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="nam" value="{{ old('nam', date('Y')) }}"
                           min="2020" max="{{ date('Y') + 1 }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400 {{ $errors->has('nam') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('nam')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.hoa-don.index') }}"
                   class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Tạo hóa đơn
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
