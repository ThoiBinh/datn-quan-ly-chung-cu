@extends('layouts.resident')
@section('title', $thongBao->tieu_de)
@section('page-title', 'Thông báo')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900 text-lg">{{ $thongBao->tieu_de }}</h2>
            <p class="text-xs text-gray-500 mt-1">{{ $thongBao->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="p-5">
            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $thongBao->noi_dung }}</div>
        </div>
    </div>
    <div class="mt-4">
        <a href="{{ route('resident.thong-bao.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Quay lại danh sách</a>
    </div>
</div>
@endsection
