@extends('layouts.admin')
@section('title', 'Cấu hình thanh toán')
@section('page-title', 'Cấu hình thanh toán')

@section('content')
<div class="space-y-5">

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $tongHoatDong }}</p>
                <p class="text-xs text-gray-500">Đang hoạt động</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $tongVoHieuHoa }}</p>
                <p class="text-xs text-gray-500">Vô hiệu hóa</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" action="{{ route('admin.cau-hinh-thanh-toan.index') }}" class="flex flex-wrap gap-2">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm ngân hàng, chủ TK, partner code, TMN code..."
                       class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-80"/>
            </div>
            <select name="trang_thai" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Vô hiệu hóa</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">Lọc</button>
            @if(request()->hasAny(['search','trang_thai']))
            <a href="{{ route('admin.cau-hinh-thanh-toan.index') }}" class="px-3 py-2 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50">Xóa lọc</a>
            @endif
        </form>
    </div>

    @php
    $nhoms = [
        ['key' => 'momo',  'title' => 'MoMo',           'badge' => 'bg-pink-100 text-pink-700',       'data' => $nhomMomo],
        ['key' => 'vnpay', 'title' => 'VNPay',          'badge' => 'bg-blue-100 text-blue-700',       'data' => $nhomVnpay],
        ['key' => 'qr',    'title' => 'QR chuyển khoản','badge' => 'bg-emerald-100 text-emerald-700', 'data' => $nhomQr],
        ['key' => 'khac',  'title' => 'Khác',           'badge' => 'bg-gray-100 text-gray-700',       'data' => $nhomKhac],
    ];
    @endphp

    @foreach($nhoms as $nhom)
        @continue($nhom['data']->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $nhom['badge'] }}">
                    {{ $nhom['title'] }}
                </span>
                <span class="text-xs text-gray-400">{{ $nhom['data']->count() }} cấu hình</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tên thuộc tính</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Giá trị</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Trạng thái</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($nhom['data'] as $ch)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $ch->ten_thuoc_tinh }}</td>
                            <td class="px-4 py-3 text-gray-600 font-mono text-xs break-all">{{ $ch->gia_tri_hien_thi ?: '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($ch->trang_thai)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Hoạt động</span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Vô hiệu</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.cau-hinh-thanh-toan.show', $ch) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Xem">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.cau-hinh-thanh-toan.edit', $ch) }}" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg" title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if($ch->duoc_chinh_sua)
                                    <button type="button"
                                            @click="$dispatch('open-toggle', { url: '{{ route('admin.cau-hinh-thanh-toan.toggle-status', $ch) }}', isActive: {{ $ch->trang_thai ? 'true' : 'false' }} })"
                                            class="p-1.5 rounded-lg transition-colors {{ $ch->trang_thai ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}"
                                            title="{{ $ch->trang_thai ? 'Vô hiệu hóa' : 'Kích hoạt' }}">
                                        @if($ch->trang_thai)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                                        @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    @if($nhomMomo->isEmpty() && $nhomVnpay->isEmpty() && $nhomQr->isEmpty() && $nhomKhac->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center text-gray-400 text-sm">
        Không tìm thấy cấu hình thanh toán nào phù hợp.
    </div>
    @endif

    <!-- Modal toggle status -->
    <template x-teleport="body">
    <div x-data="{ open: false, url: '', active: false }"
         @open-toggle.window="open = true; url = $event.detail.url; active = $event.detail.isActive"
         x-show="open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="open = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-auto p-6" @click.stop>
            <div class="text-center mb-5">
                <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3"
                     :class="active ? 'bg-red-100' : 'bg-emerald-100'">
                    <svg class="w-7 h-7" :class="active ? 'text-red-500' : 'text-emerald-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              x-bind:d="active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636' : 'M5 13l4 4L19 7'"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-800" x-text="active ? 'Vô hiệu hóa cấu hình?' : 'Kích hoạt cấu hình?'"></p>
                <p class="text-sm text-gray-500 mt-1" x-text="active ? 'Cấu hình sẽ không được sử dụng.' : 'Cấu hình sẽ được kích hoạt.'"></p>
            </div>
            <div class="flex gap-3">
                <button @click="open = false" class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50">Hủy</button>
                <form :action="url" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit" class="block w-full py-2.5 text-white text-sm font-semibold rounded-xl transition-colors"
                            :class="active ? 'bg-red-500 hover:bg-red-600' : 'bg-emerald-500 hover:bg-emerald-600'"
                            x-text="active ? 'Vô hiệu hóa' : 'Kích hoạt'"></button>
                </form>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection
