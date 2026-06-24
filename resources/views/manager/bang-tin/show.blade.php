@extends('layouts.manager')
@section('title', $bangTin->tieu_de)
@section('page-title', 'Chi tiết bảng tin')

@section('content')
<div class="max-w-2xl space-y-5">
    @if($bangTin->hinh_url)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <img src="{{ $bangTin->hinh_url }}" alt="{{ $bangTin->tieu_de }}" class="w-full max-h-72 object-cover" onerror="this.parentElement.style.display='none'">
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-3">{{ $bangTin->tieu_de }}</h2>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
                <span>Đăng bởi: <span class="text-gray-700 font-medium">{{ $bangTin->nguoiTao?->ho_ten ?? '—' }}</span></span>
                <span>·</span>
                <span>{{ optional($bangTin->createdAt)->format('d/m/Y H:i') ?? '—' }}</span>
                @if($bangTin->nguoiCapNhat)
                <span>· Sửa bởi: <span class="text-gray-700">{{ $bangTin->nguoiCapNhat?->ho_ten }}</span></span>
                @endif
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap gap-3">
            <a href="{{ route('manager.bang-tin.edit', $bangTin) }}"
               class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <form action="{{ route('manager.bang-tin.toggle-hide', $bangTin) }}" method="POST" onsubmit="return confirm('Ẩn bài này?')">
                @csrf @method('PATCH')
                <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7"/></svg>
                    Ẩn bài
                </button>
            </form>
            <a href="{{ route('manager.bang-tin.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-gray-700 whitespace-pre-line text-sm leading-relaxed">
        {{ $bangTin->noi_dung }}
    </div>
</div>
@endsection
