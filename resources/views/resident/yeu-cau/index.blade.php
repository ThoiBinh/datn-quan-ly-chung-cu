@extends('layouts.resident')
@section('title', 'Yêu cầu & Phản ánh')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Yêu cầu & Phản ánh</h1>
            <p class="text-sm text-gray-500 mt-0.5">Danh sách yêu cầu bạn đã gửi</p>
        </div>
        <a href="{{ route('resident.yeu-cau.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Gửi yêu cầu mới
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('resident.yeu-cau.index') }}"
          class="flex flex-col sm:flex-row gap-2">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Tìm theo tiêu đề..."
                   class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white"/>
        </div>
        <select name="trang_thai"
                class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
            <option value="">Tất cả trạng thái</option>
            @foreach($dsTrangThai as $val => $label)
            <option value="{{ $val }}" {{ request('trang_thai') == $val ? 'selected' : '' }}>
                {{ $label['text'] }}
            </option>
            @endforeach
        </select>
        <button type="submit"
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
            Lọc
        </button>
        @if(request()->hasAny(['search', 'trang_thai']))
        <a href="{{ route('resident.yeu-cau.index') }}"
           class="px-4 py-2.5 border border-gray-200 text-gray-500 text-sm rounded-xl hover:bg-gray-50 text-center transition-colors">
            Xóa lọc
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tiêu đề</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Loại</th>
                        
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Ngày gửi</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Trạng thái</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">NV xử lý</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($yeuCau as $yc)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 text-xs text-gray-400 font-mono">#{{ $yc->id }}</td>
                        <td class="px-5 py-4 font-medium text-gray-800 max-w-xs">
                            <p class="truncate max-w-[200px]">{{ $yc->tieu_de }}</p>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500">{{ $yc->loaiYeuCau?->name ?? '—' }}</td>
                        
                        <td class="px-5 py-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $yc->ngay_gui?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $yc->trang_thai_label['class'] }}">
                                {{ $yc->trang_thai_label['text'] }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500">{{ $yc->nhanVienXuLy?->ho_ten ?? '—' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 justify-end">
                                <a href="{{ route('resident.yeu-cau.show', $yc) }}"
                                   class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">Xem</a>
                                @if($yc->trang_thai == \App\Models\YeuCauCuDan::TRANG_THAI_MOI)
                                <a href="{{ route('resident.yeu-cau.edit', $yc) }}"
                                   class="text-blue-600 hover:text-blue-800 text-xs font-medium">Sửa</a>
                                <form method="POST" action="{{ route('resident.yeu-cau.huy', $yc) }}"
                                      onsubmit="return confirm('Bạn có chắc muốn hủy yêu cầu này?')">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="text-red-500 hover:text-red-700 text-xs font-medium">Hủy</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-500">Chưa có yêu cầu nào</p>
                            @if(request()->hasAny(['search','trang_thai']))
                            <a href="{{ route('resident.yeu-cau.index') }}"
                               class="inline-block mt-2 text-sm text-emerald-600 hover:underline">Xóa bộ lọc</a>
                            @else
                            <a href="{{ route('resident.yeu-cau.create') }}"
                               class="inline-block mt-2 text-sm text-emerald-600 hover:underline">Gửi yêu cầu đầu tiên</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($yeuCau->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">{{ $yeuCau->links() }}</div>
        @endif
    </div>
</div>
@endsection
