@extends('layouts.manager')
@section('title', 'Bảng tin')
@section('page-title', 'Quản lý bảng tin')

@section('content')
<div class="space-y-5">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form method="GET" action="{{ route('manager.bang-tin.index') }}" class="flex flex-wrap gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tiêu đề..."
                           class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 w-52"/>
                </div>
                <select name="trang_thai" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả</option>
                    <option value="hien" {{ request('trang_thai') === 'hien' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="an" {{ request('trang_thai') === 'an' ? 'selected' : '' }}>Đã ẩn</option>
                </select>
                <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">Lọc</button>
                @if(request()->hasAny(['search','trang_thai']))<a href="{{ route('manager.bang-tin.index') }}" class="px-3 py-2 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50">×</a>@endif
            </form>
            <a href="{{ route('manager.bang-tin.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Đăng bài
            </a>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($dsBangTin as $bt)
            <div class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors {{ $bt->trashed() ? 'opacity-60' : '' }}">
                @if($bt->hinh_url_full)
                <img src="{{ $bt->hinh_url_full }}" alt="" class="w-12 h-12 rounded-lg object-cover bg-gray-100 flex-shrink-0">
                @else
                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800 truncate">{{ $bt->tieu_de }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $bt->nguoiTao?->ho_ten ?? '—' }} · {{ optional($bt->createdAt)->format('d/m/Y') ?? '—' }}</p>
                </div>
                @if($bt->trashed())
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 flex-shrink-0">Đã ẩn</span>
                @endif
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    @if(!$bt->trashed())
                    <a href="{{ route('manager.bang-tin.show', $bt) }}" class="text-gray-400 hover:text-indigo-600 p-1" title="Xem">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="{{ route('manager.bang-tin.edit', $bt) }}" class="text-gray-400 hover:text-blue-600 p-1" title="Sửa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form action="{{ route('manager.bang-tin.toggle-hide', $bt) }}" method="POST" onsubmit="return confirm('Ẩn bài này?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-gray-400 hover:text-amber-500 p-1" title="Ẩn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59"/></svg>
                        </button>
                    </form>
                    @else
                    <form action="{{ route('manager.bang-tin.restore', $bt->id) }}" method="POST" onsubmit="return confirm('Khôi phục bài này?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-gray-400 hover:text-emerald-600 p-1" title="Khôi phục">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-400 text-sm">Chưa có bài đăng nào</div>
            @endforelse
        </div>
        @if($dsBangTin->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $dsBangTin->links() }}</div>
        @endif
    </div>
</div>
@endsection
