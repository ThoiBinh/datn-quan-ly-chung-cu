@extends('layouts.admin')
@section('title', 'Nhật ký hệ thống')
@section('page-title', 'Nhật ký hệ thống')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="p-5 border-b border-gray-200">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="hanh_dong" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả hành động</option>
                <option value="INSERT" {{ request('hanh_dong') == 'INSERT' ? 'selected' : '' }}>INSERT</option>
                <option value="UPDATE" {{ request('hanh_dong') == 'UPDATE' ? 'selected' : '' }}>UPDATE</option>
                <option value="DELETE" {{ request('hanh_dong') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
            </select>
            <select name="bang_tac_dong" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả bảng</option>
                @foreach($bangList as $bang)
                    <option value="{{ $bang }}" {{ request('bang_tac_dong') == $bang ? 'selected' : '' }}>{{ $bang }}</option>
                @endforeach
            </select>
            <input type="date" name="tu_ngay" value="{{ request('tu_ngay') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="date" name="den_ngay" value="{{ request('den_ngay') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Lọc</button>
            <a href="{{ route('admin.audit-logs.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors">Reset</a>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Thời gian</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Người thực hiện</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Hành động</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Bảng</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">ID bản ghi</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-600 text-xs">{{ $log->thoi_gian?->format('d/m/Y H:i:s') }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ $log->nguoiThucHien?->name ?? 'Hệ thống' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            {{ $log->hanh_dong === 'INSERT' ? 'bg-green-100 text-green-700' :
                               ($log->hanh_dong === 'UPDATE' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700') }}">
                            {{ $log->hanh_dong }}
                        </span>
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $log->bang_tac_dong }}</td>
                    <td class="px-5 py-3 text-gray-600">#{{ $log->id_ban_ghi }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.audit-logs.show', $log) }}" class="text-blue-600 hover:text-blue-800 text-xs">Chi tiết</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Không có nhật ký nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
