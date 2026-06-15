@extends('layouts.manager')
@section('title', 'Phản ánh cư dân')
@section('page-title', 'Phản ánh cư dân')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="p-5 border-b border-gray-200">
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="trang_thai" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('trang_thai') == '1' ? 'selected' : '' }}>Mới</option>
                <option value="2" {{ request('trang_thai') == '2' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="3" {{ request('trang_thai') == '3' ? 'selected' : '' }}>Hoàn thành</option>
                <option value="4" {{ request('trang_thai') == '4' ? 'selected' : '' }}>Từ chối</option>
            </select>
            <select name="muc_do" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Tất cả mức độ</option>
                <option value="1">Thấp</option>
                <option value="2">Trung bình</option>
                <option value="3">Khẩn cấp</option>
            </select>
            <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Lọc</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tiêu đề</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Cư dân</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Mức độ</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Ngày gửi</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($yeuCau as $yc)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 font-medium text-gray-800">{{ $yc->tieu_de }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $yc->cuDan?->ho_ten }}</td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $yc->muc_do_uu_tien == 3 ? 'bg-red-100 text-red-700' : ($yc->muc_do_uu_tien == 2 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ $yc->muc_do_label }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $yc->trang_thai == 3 ? 'bg-green-100 text-green-700' : ($yc->trang_thai == 1 ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $yc->trang_thai_label }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-500">{{ $yc->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-4">
                        <a href="{{ route('manager.yeu-cau.show', $yc) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Xem</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Không có yêu cầu nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($yeuCau->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">{{ $yeuCau->links() }}</div>
    @endif
</div>
@endsection
