@extends('layouts.admin')
@section('title', 'Chi tiết tòa nhà')
@section('page-title', 'Chi tiết tòa nhà')

@section('content')
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl shadow-lg">
    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span class="text-sm font-medium">{{ session('success') }}</span>
</div>
@endif

<div class="space-y-5">

    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $toaNha->ten_toa_nha }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-mono">
                        {{ $toaNha->tien_to }}
                    </span>
                </div>
                @if($toaNha->dia_chi)
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $toaNha->dia_chi }}
                </p>
                @endif
                @if($toaNha->so_tang)
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">{{ $toaNha->so_tang }} tầng</p>
                @endif
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex flex-wrap gap-3">
            <a href="{{ route('admin.toa-nha.edit', $toaNha) }}"
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('admin.can-ho.create') }}?toa_nha={{ $toaNha->id }}"
               class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm căn hộ
            </a>
            <a href="{{ route('admin.toa-nha.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
            @if($toaNha->canHo->count() === 0)
            <button type="button" x-data @click="$dispatch('open-delete', { url: '{{ route('admin.toa-nha.destroy', $toaNha) }}', name: '{{ addslashes($toaNha->ten_toa_nha) }}' })"
                    class="flex items-center gap-2 px-4 py-2 border border-red-200 dark:border-red-900 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors ml-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Xóa
            </button>
            @else
            <span title="Không thể xóa — đang có căn hộ"
                  class="flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-slate-700 text-gray-300 dark:text-slate-600 text-sm font-medium rounded-lg cursor-not-allowed ml-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Xóa
            </span>
            @endif
        </div>
    </div>

    <!-- Thống kê -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
            <p class="text-xs text-gray-400 dark:text-slate-500 uppercase tracking-wide mb-1">Tổng căn hộ</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $toaNha->canHo->count() }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
            <p class="text-xs text-gray-400 dark:text-slate-500 uppercase tracking-wide mb-1">Tổng cư dân</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $totalCuDan }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
            <p class="text-xs text-gray-400 dark:text-slate-500 uppercase tracking-wide mb-1">Tổng tiện ích</p>
            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $toaNha->tien_ich_count }}</p>
        </div>
        @foreach($statsByTrangThai as $trangThai => $soLuong)
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-5">
            <p class="text-xs text-gray-400 dark:text-slate-500 uppercase tracking-wide mb-1">{{ $trangThai }}</p>
            <p class="text-2xl font-bold text-gray-700 dark:text-slate-200">{{ $soLuong }}</p>
        </div>
        @endforeach
    </div>

    <!-- Thông tin chi tiết -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">Thông tin tòa nhà</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
                ['ID', $toaNha->id, true],
                ['Tên tòa nhà', $toaNha->ten_toa_nha, false],
                ['Tiền tố', $toaNha->tien_to, false],
                ['Số tầng', $toaNha->so_tang ? $toaNha->so_tang . ' tầng' : '—', false],
                ['Địa chỉ', $toaNha->dia_chi, false],
                ['Ngày tạo', $toaNha->createdAt?->format('d/m/Y H:i'), false],
                ['Cập nhật', $toaNha->updatedAt?->format('d/m/Y H:i'), false],
            ] as [$label, $value, $mono])
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 border border-gray-100 dark:border-slate-600">
                <dt class="text-xs font-medium text-gray-400 dark:text-slate-400 uppercase tracking-wide mb-1.5">{{ $label }}</dt>
                <dd class="text-sm font-semibold text-gray-700 dark:text-slate-200 {{ $mono ? 'font-mono' : '' }}">{{ $value ?: '—' }}</dd>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Danh sách căn hộ -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700 dark:text-slate-200 text-sm uppercase tracking-wide">
                Danh sách căn hộ ({{ $toaNha->canHo->count() }})
            </h3>
        </div>
        @if($toaNha->canHo->isEmpty())
        <div class="py-10 text-center text-sm text-gray-400 dark:text-slate-500">Chưa có căn hộ nào</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Số căn hộ</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tầng</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Loại</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Trạng thái</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Chủ hộ</th>
                        <th class="px-4 py-3 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($toaNha->canHo->sortBy('so_can_ho') as $ch)
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white font-mono">{{ $ch->so_can_ho }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-slate-300">{{ $ch->tang }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300 text-xs">{{ $ch->loaiCanHo?->ten_loai_can_ho ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ $ch->trangThai?->ten_trang_thai ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300 text-xs">
                            {{ $ch->chuHo?->cuDan?->ho_ten ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.can-ho.show', $ch) }}"
                               class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors inline-flex">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Modal xóa --}}
    <template x-teleport="body">
    <div x-data="{ open: false, url: '', name: '' }"
         @open-delete.window="open = true; url = $event.detail.url; name = $event.detail.name"
         x-show="open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="open = false">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-5">
                <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-800 dark:text-white">Xóa tòa nhà?</p>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Tòa nhà <span class="font-medium text-gray-700 dark:text-slate-200" x-text="`«${name}»`"></span> sẽ được chuyển vào thùng rác.</p>
            </div>
            <div class="flex gap-3">
                <button @click="open = false" class="flex-1 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">Hủy</button>
                <form :action="url" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="block w-full py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">Xóa</button>
                </form>
            </div>
        </div>
    </div>
    </template>

</div>
@endsection
