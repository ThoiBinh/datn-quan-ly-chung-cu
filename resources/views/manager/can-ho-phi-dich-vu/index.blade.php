@extends('layouts.manager')
@section('title', 'Phí dịch vụ căn hộ')
@section('page-title', 'Phí dịch vụ căn hộ')

@section('content')
<div class="space-y-5">

    {{-- Toast --}}
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

    {{-- Thống kê --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($tongApDung) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tổng áp dụng dịch vụ</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($tongCanHo) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Căn hộ sử dụng dịch vụ</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($tongDichVu) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Loại dịch vụ đang dùng</p>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">

        {{-- Header --}}
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Danh sách áp dụng dịch vụ</h2>
                <div class="flex gap-2">
                <a href="{{ route('manager.phi-dich-vu.index') }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Quản lý dịch vụ
                <a href="{{ route('manager.can-ho-phi-dich-vu.create') }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Thêm mới
                </a>
                </div>
            </div>

            {{-- Search + Filter --}}
            <form method="GET" action="{{ route('manager.can-ho-phi-dich-vu.index') }}" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Tên dịch vụ, số căn hộ, tòa nhà..."
                           class="pl-9 pr-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
                </div>

                <select name="toa_nha" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả tòa nhà</option>
                    @foreach($dsToaNha as $tn)
                    <option value="{{ $tn->id }}" {{ request('toa_nha') == $tn->id ? 'selected' : '' }}>{{ $tn->ten_toa_nha }}</option>
                    @endforeach
                </select>

                <select name="loai_can_ho" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả loại căn hộ</option>
                    @foreach($dsLoaiCanHo as $lch)
                    <option value="{{ $lch->id }}" {{ request('loai_can_ho') == $lch->id ? 'selected' : '' }}>{{ $lch->ten_loai_can_ho }}</option>
                    @endforeach
                </select>

                <select name="loai_phi_dich_vu" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả loại phí</option>
                    @foreach($dsLoaiPhiDichVu as $lpdv)
                    <option value="{{ $lpdv->id }}" {{ request('loai_phi_dich_vu') == $lpdv->id ? 'selected' : '' }}>{{ $lpdv->ten_loai_phi_dich_vu }}</option>
                    @endforeach
                </select>

                <select name="don_vi_tinh" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả đơn vị</option>
                    @foreach($dsDonViTinh as $dvt)
                    <option value="{{ $dvt->id }}" {{ request('don_vi_tinh') == $dvt->id ? 'selected' : '' }}>{{ $dvt->don_vi }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition-colors">Lọc</button>
                @if(request()->hasAny(['search','toa_nha','loai_can_ho','loai_phi_dich_vu','don_vi_tinh']))
                <a href="{{ route('manager.can-ho-phi-dich-vu.index') }}" class="px-3 py-1.5 text-sm text-red-600 hover:text-red-700 transition-colors">Xóa lọc</a>
                @endif

                {{-- Hidden sort fields --}}
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        @php
                            $dirs = ['asc' => 'desc', 'desc' => 'asc'];
                            $nextDir = fn($col) => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
                        @endphp
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            <a href="{{ route('manager.can-ho-phi-dich-vu.index', array_merge(request()->query(), ['sort' => 'toa_nha', 'direction' => $nextDir('toa_nha')])) }}" class="flex items-center gap-1 hover:text-gray-800 dark:hover:text-gray-200">
                                Tòa nhà
                                @if($sort === 'toa_nha')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $direction === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            <a href="{{ route('manager.can-ho-phi-dich-vu.index', array_merge(request()->query(), ['sort' => 'so_can_ho', 'direction' => $nextDir('so_can_ho')])) }}" class="flex items-center gap-1 hover:text-gray-800 dark:hover:text-gray-200">
                                Số căn hộ
                                @if($sort === 'so_can_ho')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $direction === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Loại căn hộ</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Trạng thái căn hộ</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            <a href="{{ route('manager.can-ho-phi-dich-vu.index', array_merge(request()->query(), ['sort' => 'ten_phi_dich_vu', 'direction' => $nextDir('ten_phi_dich_vu')])) }}" class="flex items-center gap-1 hover:text-gray-800 dark:hover:text-gray-200">
                                Tên dịch vụ
                                @if($sort === 'ten_phi_dich_vu')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $direction === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Loại phí</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Loại tính phí</th>
                        <th class="text-right px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            <a href="{{ route('manager.can-ho-phi-dich-vu.index', array_merge(request()->query(), ['sort' => 'don_gia', 'direction' => $nextDir('don_gia')])) }}" class="flex items-center justify-end gap-1 hover:text-gray-800 dark:hover:text-gray-200">
                                Đơn giá
                                @if($sort === 'don_gia')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $direction === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            <a href="{{ route('manager.can-ho-phi-dich-vu.index', array_merge(request()->query(), ['sort' => 'createdAt', 'direction' => $nextDir('createdAt')])) }}" class="flex items-center gap-1 hover:text-gray-800 dark:hover:text-gray-200">
                                Ngày tạo
                                @if($sort === 'createdAt')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $direction === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th class="text-center px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($dsRecord as $rec)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-800 dark:text-gray-200">{{ $rec->canHo->toaNha->ten_toa_nha ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $rec->canHo->so_can_ho ?? '—' }}</span>
                            <span class="text-xs text-gray-400 ml-1">Tầng {{ $rec->canHo->tang ?? '?' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            {{ $rec->canHo->loaiCanHo->ten_loai_can_ho ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @php $tt = $rec->canHo->trangThai?->ten_trang_thai ?? '—'; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $tt }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800 dark:text-gray-100">{{ $rec->phiDichVu->ten_phi_dich_vu ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $rec->phiDichVu->loaiPhiDichVu->ten_loai_phi_dich_vu ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            {{ $rec->phiDichVu->loaiPhiDichVu->ten_loai_phi_dich_vu ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            {{ $rec->phiDichVu->loaiTinhPhi->ten_loai ?? '—' }}
                            <span class="text-xs text-gray-400 block">{{ $rec->phiDichVu->donViTinh->don_vi ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold text-gray-800 dark:text-gray-100 tabular-nums">
                                {{ number_format((float)$rec->don_gia, 0, ',', '.') }}đ
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            {{ $rec->createdAt ? \Carbon\Carbon::parse($rec->createdAt)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('manager.can-ho-phi-dich-vu.show', $rec) }}"
                                   class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors" title="Xem chi tiết">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('manager.can-ho-phi-dich-vu.edit', $rec) }}"
                                   class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors" title="Chỉnh sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button x-data
                                    @click="if(confirm('Bạn có chắc muốn xóa áp dụng dịch vụ này?')) $refs.del{{ $rec->id }}.submit()"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                <form x-ref="del{{ $rec->id }}" action="{{ route('manager.can-ho-phi-dich-vu.destroy', $rec) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Không có dữ liệu</p>
                                <a href="{{ route('manager.can-ho-phi-dich-vu.create') }}" class="text-sm text-indigo-600 hover:underline">Thêm áp dụng dịch vụ</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($dsRecord->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $dsRecord->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
