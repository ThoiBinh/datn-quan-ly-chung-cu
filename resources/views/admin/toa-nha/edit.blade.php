@extends('layouts.admin')
@section('title', 'Sửa tòa nhà')
@section('page-title', 'Chỉnh sửa tòa nhà')

@section('content')
<div class="max-w-2xl">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400 mb-5">
        <a href="{{ route('admin.toa-nha.index') }}" class="hover:text-blue-600 transition-colors">Tòa nhà</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.toa-nha.show', $toaNha) }}" class="hover:text-blue-600 transition-colors truncate max-w-xs">{{ $toaNha->ten_toa_nha }}</a>
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

    <form method="POST" action="{{ route('admin.toa-nha.update', $toaNha) }}"
          class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        @csrf @method('PUT')

        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $toaNha->ten_toa_nha }}</span>
            </div>
            <span class="text-xs text-gray-400 dark:text-slate-500 font-mono">ID #{{ $toaNha->id }}</span>
        </div>

        <div class="p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                    Tên tòa nhà <span class="text-red-500">*</span>
                </label>
                <input type="text" name="ten_toa_nha" value="{{ old('ten_toa_nha', $toaNha->ten_toa_nha) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                    Tiền tố <span class="text-red-500">*</span>
                </label>
                <input type="text" name="tien_to" value="{{ old('tien_to', $toaNha->tien_to) }}" required maxlength="10"
                       oninput="this.value = this.value.toUpperCase()"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                    Địa chỉ <span class="text-red-500">*</span>
                </label>
                <input type="text" name="dia_chi" value="{{ old('dia_chi', $toaNha->dia_chi) }}" required
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                    Số tầng <span class="text-red-500">*</span>
                </label>
                <input type="number" name="so_tang" value="{{ old('so_tang', $toaNha->so_tang) }}" required min="1"
                       class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Lưu thay đổi
            </button>
            <a href="{{ route('admin.toa-nha.show', $toaNha) }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
