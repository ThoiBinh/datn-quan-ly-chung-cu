@extends('layouts.admin')
@section('title', $bangTin->tieu_de)
@section('page-title', 'Chi tiết bảng tin')

@section('content')
<div class="max-w-3xl space-y-5">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        @if($bangTin->hinh_url_full)
        <img src="{{ $bangTin->hinh_url_full }}" alt="{{ $bangTin->tieu_de }}" class="w-full h-48 object-cover rounded-t-xl">
        @endif
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-3">{{ $bangTin->tieu_de }}</h2>
            <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-4">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ $bangTin->nguoiTao?->ho_ten ?? 'Không rõ' }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ optional($bangTin->createdAt)->format('d/m/Y H:i') ?? '—' }}
                </span>
                @if($bangTin->nguoiCapNhat && $bangTin->nguoi_cap_nhat !== $bangTin->nguoi_tao)
                <span class="text-xs text-gray-400">Cập nhật bởi {{ $bangTin->nguoiCapNhat?->ho_ten }}</span>
                @endif
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap gap-3">
            <a href="{{ route('admin.bang-tin.edit', $bangTin) }}"
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
            <form action="{{ route('admin.bang-tin.toggle-hide', $bangTin) }}" method="POST" onsubmit="return confirm('Ẩn bài đăng này?')">
                @csrf @method('PATCH')
                <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7"/></svg>
                    Ẩn bài
                </button>
            </form>
            <a href="{{ route('admin.bang-tin.index') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Nội dung</h3>
        </div>
        <div class="p-6 prose prose-sm max-w-none text-gray-700 whitespace-pre-line">{{ $bangTin->noi_dung }}</div>
    </div>
</div>
@endsection
