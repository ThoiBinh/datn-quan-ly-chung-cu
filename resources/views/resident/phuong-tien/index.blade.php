@extends('layouts.resident')
@section('title', 'Phương tiện')
@section('page-title', 'Phương tiện của tôi')

@section('content')
<div class="space-y-4">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('resident.phuong-tien.create') }}" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Đăng ký xe mới
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Biển số</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Loại xe</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tên xe</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Ngày đăng ký</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Trạng Thái</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($phuongTien as $pt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 font-semibold text-gray-800 font-mono">{{ $pt->bien_so }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $pt->loaiPhuongTien?->ten_loai_phuong_tien }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $pt->ten_phuong_tien ?? '-' }}</td>
                        <td class="px-5 py-4 text-xs text-gray-500">{{ $pt->ngay_dang_ky?->format('d/m/Y') }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $pt->trang_thai == 1 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $pt->trang_thai == 1 ? 'Đang dùng' : 'Đã hủy' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($pt->trang_thai == 1)
                            <form method="POST" action="{{ route('resident.phuong-tien.destroy', $pt) }}" onsubmit="return confirm('Hủy đăng ký xe này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:underline">Hủy đăng ký</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Chưa có phương tiện nào được đăng ký</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
