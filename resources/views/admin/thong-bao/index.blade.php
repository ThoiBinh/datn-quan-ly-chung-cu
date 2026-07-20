@extends('layouts.admin')
@section('title', 'Thông báo')
@section('page-title', 'Quản lý thông báo')

@section('content')
<div class="space-y-5">

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $tongTatCa }}</p>
                <p class="text-xs text-gray-500">Tổng thông báo</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $tongHien }}</p>
                <p class="text-xs text-gray-500">Đang hiển thị</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $tongAn }}</p>
                <p class="text-xs text-gray-500">Đang ẩn</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form method="GET" action="{{ route('admin.thong-bao.index') }}" class="flex flex-wrap gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tiêu đề, nội dung..."
                           class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-60"/>
                </div>
                <select name="trang_thai" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả</option>
                    <option value="hien" {{ request('trang_thai') === 'hien' ? 'selected' : '' }}>Đang hiển thị</option>
                    <option value="an" {{ request('trang_thai') === 'an' ? 'selected' : '' }}>Đang ẩn</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">Lọc</button>
                @if(request()->hasAny(['search', 'trang_thai']))
                <a href="{{ route('admin.thong-bao.index') }}" class="px-3 py-2 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50">Xóa lọc</a>
                @endif
            </form>
            <a href="{{ route('admin.thong-bao.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Đăng thông báo
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            <a href="{{ route('admin.thong-bao.index', array_merge(request()->query(), ['sort'=>'tieu_de','direction'=>$sort==='tieu_de'&&$direction==='asc'?'desc':'asc'])) }}" class="flex items-center gap-1 hover:text-gray-800">
                                Tiêu đề @if($sort==='tieu_de')<span>{{ $direction==='asc'?'↑':'↓' }}</span>@endif
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Người tạo</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            <a href="{{ route('admin.thong-bao.index', array_merge(request()->query(), ['sort'=>'createdAt','direction'=>$sort==='createdAt'&&$direction==='asc'?'desc':'asc'])) }}" class="flex items-center gap-1 hover:text-gray-800">
                                Ngày tạo @if($sort==='createdAt')<span>{{ $direction==='asc'?'↑':'↓' }}</span>@endif
                            </a>
                        </th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Trạng thái</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dsThongBao as $tb)
                    <tr class="hover:bg-gray-50 transition-colors {{ $tb->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 line-clamp-1">{{ $tb->tieu_de }}</p>
                            <p class="text-xs text-gray-400 line-clamp-1 mt-0.5">{{ Str::limit(strip_tags($tb->noi_dung), 60) }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $tb->nguoiTao?->ho_ten ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $tb->getCreatedAtAttribute()?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($tb->trashed())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Đã ẩn</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Hiển thị</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                @if(!$tb->trashed())
                                <a href="{{ route('admin.thong-bao.show', $tb) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Xem">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.thong-bao.edit', $tb) }}" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg" title="Sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button type="button"
                                        @click="$dispatch('open-confirm', { url: '{{ route('admin.thong-bao.toggle-hide', $tb) }}', action: 'an' })"
                                        class="p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 rounded-lg" title="Ẩn">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                                @else
                                <button type="button"
                                        @click="$dispatch('open-confirm', { url: '{{ route('admin.thong-bao.restore', $tb->id) }}', action: 'restore' })"
                                        class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg" title="Khôi phục">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400 text-sm">Không tìm thấy thông báo nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dsThongBao->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $dsThongBao->links() }}</div>
        @endif
    </div>

    <!-- Modal xác nhận -->
    <template x-teleport="body">
    <div x-data="{ open: false, url: '', action: '' }"
         @open-confirm.window="open = true; url = $event.detail.url; action = $event.detail.action"
         x-show="open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="open = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-4">
                <template x-if="action === 'an'">
                    <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029"/></svg>
                    </div>
                </template>
                <template x-if="action === 'restore'">
                    <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                </template>
                <p x-text="action === 'an' ? 'Ẩn thông báo này?' : 'Khôi phục thông báo này?'" class="font-semibold text-gray-800"></p>
                <p x-text="action === 'an' ? 'Thông báo sẽ không hiển thị với cư dân.' : 'Thông báo sẽ được hiển thị trở lại.'" class="text-sm text-gray-500 mt-1"></p>
            </div>
            <div class="flex gap-3">
                <button @click="open = false" class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50">Hủy</button>
                <form :action="url" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit" class="block w-full py-2.5 text-sm font-semibold rounded-xl transition-colors"
                            :class="action === 'an' ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-emerald-500 hover:bg-emerald-600 text-white'"
                            x-text="action === 'an' ? 'Ẩn' : 'Khôi phục'"></button>
                </form>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection
