@extends('layouts.manager')
@section('title', 'Thông báo')
@section('page-title', 'Quản lý thông báo')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="flex items-center justify-between p-5 border-b border-gray-200">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm thông báo..."
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-60">
            <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Tìm</button>
        </form>
        <a href="{{ route('manager.thong-bao.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Đăng thông báo
        </a>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($thongBao as $tb)
        <div class="flex items-center justify-between p-5 hover:bg-gray-50">
            <div class="min-w-0 flex-1">
                <p class="font-medium text-gray-800 truncate">{{ $tb->tieu_de }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $tb->nguoiTao?->name }} · {{ $tb->created_at?->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-2 ml-4">
                <a href="{{ route('manager.thong-bao.show', $tb) }}" class="text-gray-400 hover:text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                <a href="{{ route('manager.thong-bao.edit', $tb) }}" class="text-gray-400 hover:text-blue-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                <form method="POST" action="{{ route('manager.thong-bao.destroy', $tb) }}" onsubmit="return confirm('Xóa thông báo này?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </form>
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-gray-400">Chưa có thông báo nào</div>
        @endforelse
    </div>
    @if($thongBao->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">{{ $thongBao->links() }}</div>
    @endif
</div>
@endsection
