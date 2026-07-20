@extends('layouts.admin')
@section('title', 'Dịch vụ căn hộ')
@section('page-title', 'Dịch vụ căn hộ')

@section('content')
<div class="space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm text-gray-500">Quản lý phí dịch vụ gán cho từng căn hộ</p>
        </div>
        <a href="{{ route('admin.can-ho-phi-dich-vu.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm dịch vụ căn hộ
        </a>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('admin.can-ho-phi-dich-vu.index') }}"
          class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm căn hộ, tòa nhà, dịch vụ..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
            </div>

            <select name="toa_nha" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả tòa nhà</option>
                @foreach($dsToaNha as $tn)
                <option value="{{ $tn->id }}" {{ request('toa_nha') == $tn->id ? 'selected' : '' }}>
                    {{ $tn->ten_toa_nha }}
                </option>
                @endforeach
            </select>

            <select name="phi_dich_vu" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả dịch vụ</option>
                @foreach($dsPhiDV as $dv)
                <option value="{{ $dv->id }}" {{ request('phi_dich_vu') == $dv->id ? 'selected' : '' }}>
                    {{ $dv->ten_phi_dich_vu }}
                </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Lọc
                </button>
                @if(request()->hasAny(['search', 'toa_nha', 'phi_dich_vu', 'can_ho']))
                <a href="{{ route('admin.can-ho-phi-dich-vu.index') }}"
                   class="flex items-center justify-center px-3 py-2 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                    Xóa
                </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider w-10">#</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            <a href="{{ route('admin.can-ho-phi-dich-vu.index', array_merge(request()->query(), ['sort' => 'can_ho', 'direction' => $sort === 'can_ho' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center gap-1 hover:text-gray-800">
                                Căn hộ
                                @if($sort === 'can_ho')<span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>@endif
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            <a href="{{ route('admin.can-ho-phi-dich-vu.index', array_merge(request()->query(), ['sort' => 'phi_dich_vu', 'direction' => $sort === 'phi_dich_vu' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center gap-1 hover:text-gray-800">
                                Dịch vụ
                                @if($sort === 'phi_dich_vu')<span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>@endif
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Loại tính phí</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            <a href="{{ route('admin.can-ho-phi-dich-vu.index', array_merge(request()->query(), ['sort' => 'don_gia', 'direction' => $sort === 'don_gia' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center justify-end gap-1 hover:text-gray-800">
                                Đơn giá
                                @if($sort === 'don_gia')<span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>@endif
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            <a href="{{ route('admin.can-ho-phi-dich-vu.index', array_merge(request()->query(), ['sort' => 'createdAt', 'direction' => $sort === 'createdAt' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center gap-1 hover:text-gray-800">
                                Ngày tạo
                                @if($sort === 'createdAt')<span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>@endif
                            </a>
                        </th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dsRecord as $record)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-400 text-xs font-mono">{{ $record->id }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $record->canHo?->so_can_ho ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $record->canHo?->toaNha?->ten_toa_nha ?? '—' }} · Tầng {{ $record->canHo?->tang ?? '?' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $record->phiDichVu?->ten_phi_dich_vu ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $record->phiDichVu?->loaiPhiDichVu?->ten_loai_phi_dich_vu ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @php $loai = $record->phiDichVu?->loaiTinhPhi?->ten_loai; @endphp
                            @if($loai)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ $loai }}
                            </span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold text-gray-800">{{ number_format($record->don_gia, 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-400 ml-0.5">đ</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $record->createdAt?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.can-ho-phi-dich-vu.show', $record) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Xem chi tiết">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.can-ho-phi-dich-vu.edit', $record) }}"
                                   class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Chỉnh sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm">Không tìm thấy dữ liệu</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dsRecord->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $dsRecord->links() }}
        </div>
        @endif
    </div>

    <!-- Summary -->
    <div class="text-xs text-gray-400 text-right">
        Tổng: {{ $dsRecord->total() }} bản ghi
    </div>
</div>
@endsection
