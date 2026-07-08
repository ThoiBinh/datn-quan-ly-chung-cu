@extends('layouts.manager')
@section('title', 'Quản lý tòa nhà')
@section('page-title', 'Quản lý tòa nhà')

@section('content')
@php
    $sortDir = fn($col) => $sort === $col ? ($direction === 'asc' ? 'desc' : 'asc') : 'asc';
    $sortUrl = fn($col) => request()->fullUrlWithQuery(['sort' => $col, 'direction' => $sortDir($col), 'page' => 1]);
    $sortIcon = fn($col) => $sort === $col ? ($direction === 'asc' ? '▲' : '▼') : '';
@endphp

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

<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 border-b border-gray-200">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên, tiền tố, địa chỉ..."
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-60">
            <input type="number" name="so_tang" value="{{ request('so_tang') }}" min="1" placeholder="Số tầng"
                   class="w-28 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <select name="co_can_ho" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Căn hộ --</option>
                <option value="co" @selected(request('co_can_ho') === 'co')>Có căn hộ</option>
                <option value="khong" @selected(request('co_can_ho') === 'khong')>Không có căn hộ</option>
            </select>
            <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Tìm</button>
            @if(request()->filled('search') || request()->filled('so_tang') || request()->filled('co_can_ho'))
            <a href="{{ route('manager.toa-nha.index') }}" class="px-3 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">Xóa lọc</a>
            @endif
        </form>
        <a href="{{ route('manager.toa-nha.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm tòa nhà
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase w-12">STT</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">
                        <a href="{{ $sortUrl('ten_toa_nha') }}" class="hover:text-indigo-600">Tên tòa nhà {{ $sortIcon('ten_toa_nha') }}</a>
                    </th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">
                        <a href="{{ $sortUrl('tien_to') }}" class="hover:text-indigo-600">Tiền tố {{ $sortIcon('tien_to') }}</a>
                    </th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Địa chỉ</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">
                        <a href="{{ $sortUrl('so_tang') }}" class="hover:text-indigo-600">Số tầng {{ $sortIcon('so_tang') }}</a>
                    </th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Số căn hộ</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">
                        <a href="{{ $sortUrl('createdAt') }}" class="hover:text-indigo-600">Ngày tạo {{ $sortIcon('createdAt') }}</a>
                    </th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Ngày cập nhật</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($toaNha as $tn)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 text-center text-gray-400 text-xs">{{ $toaNha->firstItem() + $loop->index }}</td>
                    <td class="px-5 py-4 font-medium text-gray-800">{{ $tn->ten_toa_nha }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 font-mono">{{ $tn->tien_to }}</span>
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $tn->dia_chi ?: '—' }}</td>
                    <td class="px-5 py-4 text-center text-gray-600">{{ $tn->so_tang ?? '—' }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600">
                            {{ $tn->can_ho_count }} căn
                        </span>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-500 whitespace-nowrap">{{ $tn->createdAt?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-5 py-4 text-xs text-gray-500 whitespace-nowrap">{{ $tn->updatedAt?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('manager.toa-nha.show', $tn) }}" class="text-gray-400 hover:text-indigo-600 transition-colors" title="Xem chi tiết">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('manager.toa-nha.edit', $tn) }}" class="text-gray-400 hover:text-indigo-600 transition-colors" title="Chỉnh sửa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if(($tn->can_ho_count ?? 0) === 0)
                            <form method="POST" action="{{ route('manager.toa-nha.destroy', $tn) }}"
                                  onsubmit="return confirm('Xóa tòa nhà {{ addslashes($tn->ten_toa_nha) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @else
                            <span class="text-gray-200 cursor-not-allowed" title="Không thể xóa — đang có căn hộ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-5 py-12 text-center text-gray-400">Chưa có tòa nhà nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($toaNha->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">{{ $toaNha->links() }}</div>
    @endif
</div>
@endsection
