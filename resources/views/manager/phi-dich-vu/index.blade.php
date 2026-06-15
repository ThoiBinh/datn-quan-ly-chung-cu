@extends('layouts.manager')
@section('title', 'Phí dịch vụ')
@section('page-title', 'Quản lý phí dịch vụ')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="flex items-center justify-between p-5 border-b border-gray-200">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên phí..."
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-56">
            <select name="loai" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Tất cả loại</option>
                @foreach($loaiPhiDichVu as $l)<option value="{{ $l->id }}" {{ request('loai') == $l->id ? 'selected' : '' }}>{{ $l->ten_loai_phi_dich_vu }}</option>@endforeach
            </select>
            <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Lọc</button>
        </form>
        <a href="{{ route('manager.phi-dich-vu.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm phí
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tên phí dịch vụ</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Loại</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Đơn giá</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Đơn vị tính</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Cách tính</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($phiDichVu as $phi)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 font-medium text-gray-800">{{ $phi->ten_phi_dich_vu }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $phi->loaiPhiDichVu?->ten_loai_phi_dich_vu ?? '-' }}</td>
                    <td class="px-5 py-4 text-right font-medium text-gray-800">{{ number_format($phi->don_gia) }}đ</td>
                    <td class="px-5 py-4 text-gray-600">{{ $phi->donViTinh?->don_vi ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $phi->loaiTinhPhi?->ten_loai ?? '-' }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('manager.phi-dich-vu.edit', $phi) }}" class="text-gray-400 hover:text-blue-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                            <form method="POST" action="{{ route('manager.phi-dich-vu.destroy', $phi) }}" onsubmit="return confirm('Xóa phí này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Chưa có phí dịch vụ nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($phiDichVu->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">{{ $phiDichVu->links() }}</div>
    @endif
</div>
@endsection
