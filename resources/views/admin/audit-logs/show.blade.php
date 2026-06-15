@extends('layouts.admin')
@section('title', 'Chi tiết nhật ký')
@section('page-title', 'Chi tiết nhật ký')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Thời gian:</span> <span class="font-medium ml-2">{{ $nhatKy->thoi_gian?->format('d/m/Y H:i:s') }}</span></div>
            <div><span class="text-gray-500">Người thực hiện:</span> <span class="font-medium ml-2">{{ $nhatKy->nguoiThucHien?->name ?? 'Hệ thống' }}</span></div>
            <div><span class="text-gray-500">Hành động:</span>
                <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium {{ $nhatKy->hanh_dong === 'INSERT' ? 'bg-green-100 text-green-700' : ($nhatKy->hanh_dong === 'UPDATE' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700') }}">{{ $nhatKy->hanh_dong }}</span>
            </div>
            <div><span class="text-gray-500">Bảng:</span> <code class="ml-2 text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $nhatKy->bang_tac_dong }}</code></div>
            <div><span class="text-gray-500">ID bản ghi:</span> <span class="font-medium ml-2">#{{ $nhatKy->id_ban_ghi }}</span></div>
        </div>
        @if($nhatKy->gia_tri_cu)
        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">Giá trị cũ:</p>
            <pre class="bg-red-50 border border-red-200 rounded-lg p-4 text-xs overflow-auto">{{ json_encode(json_decode($nhatKy->gia_tri_cu), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif
        @if($nhatKy->gia_tri_moi)
        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">Giá trị mới:</p>
            <pre class="bg-green-50 border border-green-200 rounded-lg p-4 text-xs overflow-auto">{{ json_encode(json_decode($nhatKy->gia_tri_moi), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif
        <div class="pt-2">
            <a href="{{ route('admin.audit-logs.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">← Quay lại</a>
        </div>
    </div>
</div>
@endsection
