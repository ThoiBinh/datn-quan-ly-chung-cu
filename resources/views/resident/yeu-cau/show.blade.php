@extends('layouts.resident')
@section('title', 'Chi tiết yêu cầu')
@section('page-title', 'Chi tiết yêu cầu')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200 flex items-start justify-between">
            <div>
                <h2 class="font-semibold text-gray-800">{{ $yeuCau->tieu_de }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $yeuCau->loai_yeu_cau) }}</span>
                    <span class="text-gray-300">•</span>
                    <span class="text-xs text-gray-500">{{ $yeuCau->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full text-xs {{ $yeuCau->muc_do_label['class'] }}">{{ $yeuCau->muc_do_label['text'] }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs {{ $yeuCau->trang_thai_label['class'] }}">{{ $yeuCau->trang_thai_label['text'] }}</span>
            </div>
        </div>
        <div class="p-5">
            <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap">{{ $yeuCau->noi_dung }}</div>
        </div>
        @if($yeuCau->phan_hoi)
        <div class="mx-5 mb-5 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-xs font-semibold text-blue-600 uppercase mb-2">Phản hồi từ Ban quản lý</p>
            <p class="text-sm text-blue-800 whitespace-pre-wrap">{{ $yeuCau->phan_hoi }}</p>
            @if($yeuCau->ngay_xu_ly)
            <p class="text-xs text-blue-500 mt-2">{{ $yeuCau->ngay_xu_ly->format('d/m/Y H:i') }}</p>
            @endif
        </div>
        @endif
    </div>
    <a href="{{ route('resident.yeu-cau.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Quay lại danh sách</a>
</div>
@endsection
