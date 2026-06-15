@extends('layouts.resident')
@section('title', 'Yêu cầu & Phản ánh')
@section('page-title', 'Yêu cầu & Phản ánh')

@section('content')
<div class="space-y-4">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('resident.yeu-cau.create') }}" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Gửi yêu cầu mới
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tiêu đề</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Loại</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Mức độ</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Ngày gửi</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($yeuCau as $yc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 font-medium text-gray-800 max-w-xs truncate">{{ $yc->tieu_de }}</td>
                        <td class="px-5 py-4 text-gray-600 capitalize">{{ $yc->loai_yeu_cau }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $yc->muc_do_label['class'] }}">{{ $yc->muc_do_label['text'] }}</span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500">{{ $yc->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $yc->trang_thai_label['class'] }}">{{ $yc->trang_thai_label['text'] }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('resident.yeu-cau.show', $yc) }}" class="text-emerald-600 hover:underline text-xs">Xem chi tiết</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Chưa có yêu cầu nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($yeuCau->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">{{ $yeuCau->links() }}</div>
        @endif
    </div>
</div>
@endsection
