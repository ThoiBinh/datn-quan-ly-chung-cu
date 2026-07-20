@extends('layouts.manager')
@section('title', $thongBao->tieu_de)
@section('page-title', 'Chi tiết thông báo')

@section('content')
<div class="space-y-6">

    {{-- ACTION BAR --}}
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('manager.thong-bao.edit', $thongBao) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Chỉnh sửa
        </a>
        @unless($thongBao->trashed())
        <form action="{{ route('manager.thong-bao.toggle-hide', $thongBao) }}" method="POST" onsubmit="return confirm('Ẩn thông báo này?')">
            @csrf @method('PATCH')
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7"/></svg>
                Ẩn
            </button>
        </form>
        @else
        <form action="{{ route('manager.thong-bao.restore', $thongBao->id) }}" method="POST" onsubmit="return confirm('Khôi phục?')">
            @csrf @method('PATCH')
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Khôi phục
            </button>
        </form>
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Đang ẩn</span>
        @endunless
        <a href="{{ route('manager.thong-bao.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Quay lại
        </a>
    </div>

    {{-- CARD 1 — THÔNG TIN THÔNG BÁO --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Thông tin thông báo</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Tiêu đề</p>
                <p class="text-xl font-bold text-gray-800">{{ $thongBao->tieu_de }}</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-0.5">ID</p>
                    <p class="font-semibold text-gray-700 font-mono text-sm">{{ $thongBao->id }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-0.5">Người tạo</p>
                    <p class="font-semibold text-gray-700 text-sm">{{ $thongBao->nguoiTao?->ho_ten ?? $thongBao->nguoiTao?->name ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-0.5">Ngày tạo</p>
                    <p class="font-semibold text-gray-700 text-sm">{{ $thongBao->getCreatedAtAttribute()?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-0.5">Cập nhật</p>
                    <p class="font-semibold text-gray-700 text-sm">{{ isset($thongBao->attributes['updatedAt']) ? \Carbon\Carbon::parse($thongBao->attributes['updatedAt'])->format('d/m/Y H:i') : '—' }}</p>
                </div>
                @if($thongBao->trashed())
                <div class="bg-amber-50 rounded-xl p-3 border border-amber-100">
                    <p class="text-xs text-amber-400 mb-0.5">Ngày ẩn</p>
                    <p class="font-semibold text-amber-700 text-sm">{{ isset($thongBao->attributes['deletedAt']) ? \Carbon\Carbon::parse($thongBao->attributes['deletedAt'])->format('d/m/Y H:i') : '—' }}</p>
                </div>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Nội dung</p>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $thongBao->noi_dung }}</div>
            </div>
        </div>
    </div>

    {{-- CARD 2 — THỐNG KÊ ĐỌC --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Thống kê đọc thông báo</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 text-center">
                    <p class="text-3xl font-extrabold text-blue-700">{{ $tongCuDan }}</p>
                    <p class="text-xs text-blue-500 mt-1 font-medium">Tổng cư dân</p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100 text-center">
                    <p class="text-3xl font-extrabold text-emerald-700">{{ $tongDaDoc }}</p>
                    <p class="text-xs text-emerald-500 mt-1 font-medium">Đã đọc</p>
                </div>
                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 text-center">
                    <p class="text-3xl font-extrabold text-amber-700">{{ $tongChuaDoc }}</p>
                    <p class="text-xs text-amber-500 mt-1 font-medium">Chưa đọc</p>
                </div>
                <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100 text-center">
                    <p class="text-3xl font-extrabold text-indigo-700">{{ $tyLe }}%</p>
                    <p class="text-xs text-indigo-500 mt-1 font-medium">Tỷ lệ đọc</p>
                </div>
            </div>
            @if($tongCuDan > 0)
            <div class="mt-4">
                <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                    <span>Tiến độ đọc</span>
                    <span>{{ $tongDaDoc }}/{{ $tongCuDan }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-emerald-500 h-2.5 rounded-full transition-all" style="width: {{ $tyLe }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- CARD 3 — DANH SÁCH ĐÃ ĐỌC --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Danh sách đã đọc</h2>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">{{ $tongDaDoc }} người</span>
        </div>
        @if($dsDaDoc->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3 text-left">STT</th>
                        <th class="px-4 py-3 text-left">Cư dân</th>
                        <th class="px-4 py-3 text-left">Email / SĐT</th>
                        <th class="px-4 py-3 text-left">CCCD</th>
                        <th class="px-4 py-3 text-left">Vai trò</th>
                        <th class="px-4 py-3 text-left">Căn hộ</th>
                        <th class="px-4 py-3 text-left">Tòa nhà</th>
                        <th class="px-4 py-3 text-center">Thời gian đọc</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($dsDaDoc as $i => $row)
                    @php
                        $cuDan   = $row->cuDan;
                        $canHoHT = $cuDan?->canHoHienTai;
                        $canHo   = $canHoHT?->canHo;
                        $toaNha  = $canHo?->toaNha;
                        $vaiTro  = $canHoHT?->vaiTro;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $dsDaDoc->firstItem() + $i }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-800">{{ $cuDan?->ho_ten ?? '—' }}</p>
                            <p class="text-xs text-gray-400 font-mono">cu_dan_id: {{ $row->cu_dan_id }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-700 text-xs">{{ $cuDan?->email ?? '—' }}</p>
                            <p class="text-gray-500 text-xs">{{ $cuDan?->sdt ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs font-mono">{{ $cuDan?->cccd ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($vaiTro)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ $vaiTro->vai_tro }}</span>
                            @else<span class="text-gray-400 text-xs">—</span>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-700 text-xs">{{ $canHo?->so_can_ho ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700 text-xs">{{ $toaNha?->ten_toa_nha ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($row->read_at)
                            <div class="inline-flex flex-col items-center gap-0.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Đã đọc</span>
                                <span class="text-xs text-gray-400">{{ $row->read_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @else<span class="text-gray-400 text-xs">—</span>@endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($dsDaDoc->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $dsDaDoc->links() }}</div>
        @endif
        @else
        <div class="p-10 text-center text-gray-400 text-sm">Chưa có cư dân nào đọc thông báo này.</div>
        @endif
    </div>

    {{-- CARD 4 — DANH SÁCH CHƯA ĐỌC --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Danh sách chưa đọc</h2>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">{{ $tongChuaDoc }} người</span>
        </div>
        @if($dsChuaDoc->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3 text-left">STT</th>
                        <th class="px-4 py-3 text-left">Cư dân</th>
                        <th class="px-4 py-3 text-left">Email / SĐT</th>
                        <th class="px-4 py-3 text-left">Vai trò</th>
                        <th class="px-4 py-3 text-left">Căn hộ</th>
                        <th class="px-4 py-3 text-left">Tòa nhà</th>
                        <th class="px-4 py-3 text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($dsChuaDoc as $i => $cuDan)
                    @php
                        $canHoHT = $cuDan->canHoHienTai;
                        $canHo   = $canHoHT?->canHo;
                        $toaNha  = $canHo?->toaNha;
                        $vaiTro  = $canHoHT?->vaiTro;
                    @endphp
                    <tr class="hover:bg-amber-50/40 transition-colors">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $dsChuaDoc->firstItem() + $i }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-800">{{ $cuDan->ho_ten }}</p>
                            <p class="text-xs text-gray-400 font-mono">cu_dan_id: {{ $cuDan->id }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-700 text-xs">{{ $cuDan->email ?? '—' }}</p>
                            <p class="text-gray-500 text-xs">{{ $cuDan->sdt ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($vaiTro)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ $vaiTro->vai_tro }}</span>
                            @else<span class="text-gray-400 text-xs">—</span>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-700 text-xs">{{ $canHo?->so_can_ho ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700 text-xs">{{ $toaNha?->ten_toa_nha ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Chưa đọc</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($dsChuaDoc->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $dsChuaDoc->links() }}</div>
        @endif
        @else
        <div class="p-10 text-center text-emerald-600 text-sm font-medium">Tất cả cư dân đã đọc thông báo này.</div>
        @endif
    </div>

</div>
@endsection
