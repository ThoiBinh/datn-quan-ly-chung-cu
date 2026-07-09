@extends('layouts.manager')
@section('title', 'Chi tiết tòa nhà')
@section('page-title', 'Chi tiết tòa nhà')

@section('content')
@if(session('success'))
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
    {{ session('error') }}
</div>
@endif

<div class="space-y-5">

    <!-- Header -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-gray-800">{{ $toaNha->ten_toa_nha }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 font-mono">
                        {{ $toaNha->tien_to }}
                    </span>
                </div>
                @if($toaNha->dia_chi)
                <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $toaNha->dia_chi }}
                </p>
                @endif
                @if($toaNha->so_tang)
                <p class="text-xs text-gray-400 mt-0.5">{{ $toaNha->so_tang }} tầng</p>
                @endif
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap gap-3">
            <a href="{{ route('manager.toa-nha.edit', $toaNha) }}"
               class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('manager.toa-nha.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
            @if($toaNha->canHo->count() === 0)
            <form method="POST" action="{{ route('manager.toa-nha.destroy', $toaNha) }}"
                  onsubmit="return confirm('Xóa tòa nhà {{ addslashes($toaNha->ten_toa_nha) }}?')" class="ml-auto">
                @csrf @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 border border-red-200 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Xóa
                </button>
            </form>
            @else
            <span title="Không thể xóa — đang có căn hộ"
                  class="flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-300 text-sm font-medium rounded-lg cursor-not-allowed ml-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Xóa
            </span>
            @endif
        </div>
    </div>

    <!-- Thống kê -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Tổng căn hộ</p>
            <p class="text-2xl font-bold text-gray-800">{{ $toaNha->canHo->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Tổng cư dân</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $totalCuDan }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Tổng tiện ích</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $toaNha->tien_ich_count }}</p>
        </div>
        @foreach($statsByTrangThai as $trangThai => $soLuong)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ $trangThai }}</p>
            <p class="text-2xl font-bold text-gray-700">{{ $soLuong }}</p>
        </div>
        @endforeach
    </div>

    <!-- Thông tin chi tiết -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Thông tin tòa nhà</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
                ['Tên tòa nhà', $toaNha->ten_toa_nha, false],
                ['Tiền tố', $toaNha->tien_to, false],
                ['Số tầng', $toaNha->so_tang ? $toaNha->so_tang . ' tầng' : '—', false],
                ['Địa chỉ', $toaNha->dia_chi, false],
                ['Ngày tạo', $toaNha->createdAt?->format('d/m/Y H:i'), false],
                ['Cập nhật', $toaNha->updatedAt?->format('d/m/Y H:i'), false],
            ] as [$label, $value, $mono])
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">{{ $label }}</dt>
                <dd class="text-sm font-semibold text-gray-700 {{ $mono ? 'font-mono' : '' }}">{{ $value ?: '—' }}</dd>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Danh sách căn hộ -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">
                Danh sách căn hộ ({{ $toaNha->canHo->count() }})
            </h3>
        </div>
        @if($toaNha->canHo->isEmpty())
        <div class="py-10 text-center text-sm text-gray-400">Chưa có căn hộ nào</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Số căn hộ</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tầng</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Loại</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Chủ hộ</th>
                        <th class="px-4 py-3 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($toaNha->canHo->sortBy('so_can_ho') as $ch)
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        <td class="px-4 py-3 font-semibold text-gray-800 font-mono">{{ $ch->so_can_ho }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $ch->tang }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $ch->loaiCanHo?->ten_loai_can_ho ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ $ch->trangThai?->ten_trang_thai ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">
                            {{ $ch->chuHo?->cuDan?->ho_ten ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('manager.can-ho.show', $ch) }}"
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
</div>
@endsection
