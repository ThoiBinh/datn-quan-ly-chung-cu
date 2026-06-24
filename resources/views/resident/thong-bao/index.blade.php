@extends('layouts.resident')
@section('title', 'Thông báo')
@section('page-title', 'Thông báo')

@section('content')
<div class="space-y-5">

    {{-- Header + stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Thông báo</h1>
            @if($tongChuaDoc > 0)
            <p class="text-sm text-amber-600 mt-0.5 font-medium">
                Bạn có <span class="font-bold">{{ $tongChuaDoc }}</span> thông báo chưa đọc
            </p>
            @else
            <p class="text-sm text-gray-500 mt-0.5">Tất cả đã được đọc</p>
            @endif
        </div>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('resident.thong-bao.index') }}"
          class="flex flex-col sm:flex-row gap-2">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Tìm kiếm thông báo..."
                   class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white"/>
        </div>
        <select name="trang_thai"
                class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
            <option value="">Tất cả</option>
            <option value="chua_doc" {{ request('trang_thai') === 'chua_doc' ? 'selected' : '' }}>Chưa đọc</option>
            <option value="da_doc"   {{ request('trang_thai') === 'da_doc'   ? 'selected' : '' }}>Đã đọc</option>
        </select>
        <button type="submit"
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
            Lọc
        </button>
        @if(request()->hasAny(['search', 'trang_thai']))
        <a href="{{ route('resident.thong-bao.index') }}"
           class="px-4 py-2.5 border border-gray-200 text-gray-500 text-sm rounded-xl hover:bg-gray-50 text-center transition-colors">
            Xóa lọc
        </a>
        @endif
    </form>

    {{-- Notification list --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @forelse($dsThongBao as $tb)
        @php $baiDoc = $tb->daDoc->first(); @endphp
        <a href="{{ route('resident.thong-bao.show', $tb) }}"
           class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0 block">
            {{-- Dot indicator --}}
            <div class="mt-1 flex-shrink-0">
                @if(!$baiDoc)
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 block ring-2 ring-amber-100"></span>
                @else
                <span class="w-2.5 h-2.5 rounded-full bg-gray-200 block"></span>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-sm font-semibold text-gray-800 line-clamp-1 {{ !$baiDoc ? 'text-gray-900' : 'text-gray-600' }}">
                        {{ $tb->tieu_de }}
                    </p>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if(!$baiDoc)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                            Chưa đọc
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600">
                            Đã đọc
                        </span>
                        @endif
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ Str::limit(strip_tags($tb->noi_dung), 120) }}</p>
                <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-400">
                    <span>{{ $tb->nguoiTao?->ho_ten ?? $tb->nguoiTao?->name ?? 'Hệ thống' }}</span>
                    <span>·</span>
                    <span>{{ $tb->getCreatedAtAttribute()?->diffForHumans() ?? '—' }}</span>
                    @if($baiDoc?->read_at)
                    <span>·</span>
                    <span class="text-emerald-500">Đọc lúc {{ $baiDoc->read_at->format('H:i d/m/Y') }}</span>
                    @endif
                </div>
            </div>

            <svg class="w-4 h-4 text-gray-300 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @empty
        <div class="px-5 py-16 text-center">
            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-sm font-medium text-gray-500">Không có thông báo nào</p>
            @if(request()->hasAny(['search','trang_thai']))
            <a href="{{ route('resident.thong-bao.index') }}" class="inline-block mt-2 text-sm text-emerald-600 hover:underline">Xóa bộ lọc</a>
            @endif
        </div>
        @endforelse
    </div>

    @if($dsThongBao->hasPages())
    <div>{{ $dsThongBao->links() }}</div>
    @endif
</div>
@endsection
