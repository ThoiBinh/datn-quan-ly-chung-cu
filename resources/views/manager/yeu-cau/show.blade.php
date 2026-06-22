@extends('layouts.manager')
@section('title', 'Chi tiết phản ánh')
@section('page-title', 'Chi tiết phản ánh')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">{{ $yeuCau->tieu_de }}</h2>
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $yeuCau->muc_do_label['class'] }}">
                {{ $yeuCau->muc_do_label['text'] }}
            </span>
        </div>
        <div class="prose prose-sm text-gray-700 mb-6">{{ $yeuCau->noi_dung }}</div>
        <dl class="grid grid-cols-2 gap-3 text-sm mb-6 bg-gray-50 rounded-lg p-4">
            <div><dt class="text-gray-500">Cư dân</dt><dd class="font-medium mt-0.5">{{ $yeuCau->cuDan?->ho_ten }}</dd></div>
            <div><dt class="text-gray-500">Ngày gửi</dt><dd class="font-medium mt-0.5">{{ $yeuCau->ngay_gui?->format('d/m/Y H:i') }}</dd></div>
            <div><dt class="text-gray-500">Trạng thái</dt><dd class="mt-0.5"><span class="px-2 py-0.5 rounded text-xs font-medium {{ $yeuCau->trang_thai_label['class'] }}">{{ $yeuCau->trang_thai_label['text'] }}</span></dd></div>
            <div><dt class="text-gray-500">NV xử lý</dt><dd class="font-medium mt-0.5">{{ $yeuCau->nhanVienXuLy?->ho_ten ?? 'Chưa phân công' }}</dd></div>
        </dl>
        <form method="POST" action="{{ route('manager.yeu-cau.update', $yeuCau) }}" class="flex items-center gap-3">
            @csrf @method('PUT')
            <select name="trang_thai" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="1" {{ $yeuCau->trang_thai == 1 ? 'selected' : '' }}>Mới</option>
                <option value="2" {{ $yeuCau->trang_thai == 2 ? 'selected' : '' }}>Đang xử lý</option>
                <option value="3" {{ $yeuCau->trang_thai == 3 ? 'selected' : '' }}>Hoàn thành</option>
                <option value="4" {{ $yeuCau->trang_thai == 4 ? 'selected' : '' }}>Từ chối</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Cập nhật</button>
            <a href="{{ route('manager.yeu-cau.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Quay lại</a>
        </form>
    </div>
</div>
@endsection
