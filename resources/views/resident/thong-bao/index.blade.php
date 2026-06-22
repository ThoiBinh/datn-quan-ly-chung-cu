@extends('layouts.resident')
@section('title', 'Thông báo')
@section('page-title', 'Thông báo')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="divide-y divide-gray-100">
        @forelse($thongBao as $tb)
        <a href="{{ route('resident.thong-bao.show', $tb) }}" class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 block">
            <div class="w-2 h-2 bg-emerald-500 rounded-full mt-2 flex-shrink-0"></div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ $tb->tieu_de }}</p>
                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ Str::limit($tb->noi_dung, 120) }}</p>
            </div>
            <p class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0">{{ $tb->created_at?->diffForHumans() }}</p>
        </a>
        @empty
        <div class="px-5 py-16 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <p class="text-sm">Chưa có thông báo nào</p>
        </div>
        @endforelse
    </div>
    @if($thongBao->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">{{ $thongBao->links() }}</div>
    @endif
</div>
@endsection
