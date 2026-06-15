@extends('layouts.manager')
@section('title', 'Quản lý cư dân')
@section('page-title', 'Quản lý cư dân')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 border-b border-gray-200">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên, SĐT, CCCD..."
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
            <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Tìm</button>
        </form>
        <a href="{{ route('manager.cu-dan.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm cư dân
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Cư dân</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">SĐT</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">CCCD</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Căn hộ</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($cuDan as $cd)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-sm">
                                {{ strtoupper(substr($cd->ho_ten, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $cd->ho_ten }}</p>
                                <p class="text-xs text-gray-500">{{ $cd->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $cd->sdt ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-600 font-mono text-xs">{{ $cd->cccd ?? '-' }}</td>
                    <td class="px-5 py-4">
                        @if($cd->canHoHienTai)
                        <span class="text-sm font-medium text-indigo-600">{{ $cd->canHoHienTai->canHo?->so_can_ho }}</span>
                        <span class="text-xs text-gray-400 ml-1">{{ $cd->canHoHienTai->canHo?->toaNha?->ten_toa_nha }}</span>
                        @else
                        <span class="text-gray-400 text-xs">Chưa phân công</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('manager.cu-dan.show', $cd) }}" class="text-gray-400 hover:text-indigo-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                            <a href="{{ route('manager.cu-dan.edit', $cd) }}" class="text-gray-400 hover:text-blue-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                            <form method="POST" action="{{ route('manager.cu-dan.destroy', $cd) }}" onsubmit="return confirm('Xóa cư dân này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400">Chưa có cư dân nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cuDan->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">{{ $cuDan->links() }}</div>
    @endif
</div>
@endsection
