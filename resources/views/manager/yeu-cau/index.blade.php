@extends('layouts.manager')
@section('title', 'Yêu cầu cư dân')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Yêu cầu cư dân</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tổng: {{ $yeuCau->total() }} yêu cầu</p>
        </div>
        <div class="flex justify-end">
        <a href="{{ route('manager.loai-yeu-cau.index') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
             Thêm loại yêu cầu
        </a>
    </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('manager.yeu-cau.index') }}"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm px-5 py-4 flex flex-wrap gap-2">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Tìm theo tiêu đề..."
                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"/>
        </div>
        <select name="trang_thai"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Tất cả trạng thái</option>
            @foreach($dsTrangThai as $val => $label)
            <option value="{{ $val }}" {{ request('trang_thai') == $val ? 'selected' : '' }}>
                {{ $label['text'] }}
            </option>
            @endforeach
        </select>
        <select name="muc_do"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Tất cả mức độ</option>
            @foreach($dsMucDo as $val => $label)
            <option value="{{ $val }}" {{ request('muc_do') == $val ? 'selected' : '' }}>
                {{ $label['text'] }}
            </option>
            @endforeach
        </select>
        @if($dsLoaiYeuCau->isNotEmpty())
        <select name="loai_yeu_cau"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Tất cả loại</option>
            @foreach($dsLoaiYeuCau as $loai)
            <option value="{{ $loai->id }}" {{ request('loai_yeu_cau') == $loai->id ? 'selected' : '' }}>
                {{ $loai->name }}
            </option>
            @endforeach
        </select>
        @endif
        <button type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            Lọc
        </button>
        @if(request()->hasAny(['search','trang_thai','muc_do','loai_yeu_cau']))
        <a href="{{ route('manager.yeu-cau.index') }}"
           class="px-4 py-2 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50 transition-colors">
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
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Cư dân</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Căn hộ / Tòa nhà</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Mức độ</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Trạng thái</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">NV xử lý</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Biển số</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Ngày gửi</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($yeuCau as $yc)
                    @php
                        $canHo  = $yc->cuDan?->canHoHienTai?->canHo;
                        $toaNha = $canHo?->toaNha;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 text-xs text-gray-400 font-mono">#{{ $yc->id }}</td>
                        <td class="px-5 py-4 font-medium text-gray-800 max-w-[180px]">
                            <p class="truncate">{{ $yc->tieu_de }}</p>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500">{{ $yc->loaiYeuCau?->name ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-gray-700">{{ $yc->cuDan?->ho_ten ?? '—' }}</td>
                        <td class="px-5 py-4 text-xs text-gray-500">
                            @if($canHo)
                            <span class="font-medium text-gray-700">{{ $canHo->so_can_ho }}</span>
                            @if($toaNha)<span class="text-gray-400"> / {{ $toaNha->ten_toa_nha }}</span>@endif
                            @else —
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $yc->muc_do_label['class'] }}">
                                {{ $yc->muc_do_label['text'] }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $yc->trang_thai_label['class'] }}">
                                {{ $yc->trang_thai_label['text'] }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500">{{ $yc->nhanVienXuLy?->ho_ten ?? '—' }}</td>
                        <td class="px-5 py-4 text-xs font-mono text-gray-600">
                            @if($loaiDangKyPTId && $yc->loai_yeu_cau == $loaiDangKyPTId)
                                @php preg_match('/Biển số:\s*([^\n]+)/ui', $yc->noi_dung ?? '', $_m); @endphp
                                {{ strtoupper(trim($_m[1] ?? '—')) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $yc->ngay_gui?->format('d/m/Y H:i') ?? $yc->created_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('manager.yeu-cau.show', $yc) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Xem</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-5 py-16 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm text-gray-500">Không có yêu cầu nào</p>
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
