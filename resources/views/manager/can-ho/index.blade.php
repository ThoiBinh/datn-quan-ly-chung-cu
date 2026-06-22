@extends('layouts.manager')
@section('title', 'Quản lý căn hộ')
@section('page-title', 'Quản lý căn hộ')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 border-b border-gray-200">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Số căn hộ..."
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-40">
            <select name="toa_nha" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Tất cả tòa</option>
                @foreach($toaNha as $tn)<option value="{{ $tn->id }}" {{ request('toa_nha') == $tn->id ? 'selected' : '' }}>{{ $tn->ten_toa_nha }}</option>@endforeach
            </select>
            <select name="trang_thai" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Tất cả trạng thái</option>
                @foreach($trangThai as $tt)<option value="{{ $tt->id }}" {{ request('trang_thai') == $tt->id ? 'selected' : '' }}>{{ $tt->ten_trang_thai }}</option>@endforeach
            </select>
            <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Lọc</button>
        </form>
        <a href="{{ route('manager.can-ho.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm căn hộ
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Căn hộ</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tòa nhà</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tầng</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Giá</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($canHo as $ch)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 font-semibold text-gray-800">{{ $ch->so_can_ho }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $ch->toaNha?->ten_toa_nha ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $ch->tang }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $ch->gia ? number_format($ch->gia) . 'đ' : '-' }}</td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $ch->trang_thai == 1 ? 'bg-green-100 text-green-700' : ($ch->trang_thai == 2 ? 'bg-gray-100 text-gray-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $ch->trangThai?->ten_trang_thai ?? '-' }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('manager.can-ho.show', $ch) }}" class="text-gray-400 hover:text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                            <a href="{{ route('manager.can-ho.edit', $ch) }}" class="text-gray-400 hover:text-blue-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                            <form method="POST" action="{{ route('manager.can-ho.destroy', $ch) }}" onsubmit="return confirm('Xóa căn hộ này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Chưa có căn hộ nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($canHo->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">{{ $canHo->links() }}</div>
    @endif
</div>
@endsection
