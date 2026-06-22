@extends('layouts.manager')
@section('title', $thongBao->tieu_de)
@section('page-title', 'Chi tiết thông báo')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $thongBao->tieu_de }}</h2>
        <p class="text-xs text-gray-500 mb-6">{{ $thongBao->nguoiTao?->name }} · {{ $thongBao->created_at?->format('d/m/Y H:i') }}</p>
        <div class="prose prose-sm text-gray-700 whitespace-pre-line">{{ $thongBao->noi_dung }}</div>
        <div class="mt-6 flex gap-3">
            <a href="{{ route('manager.thong-bao.edit', $thongBao) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Sửa</a>
            <a href="{{ route('manager.thong-bao.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Quay lại</a>
        </div>
    </div>
</div>
@endsection
