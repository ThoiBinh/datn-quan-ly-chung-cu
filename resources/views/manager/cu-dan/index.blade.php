@extends('layouts.manager')
@section('title', 'Quản lý cư dân')
@section('page-title', 'Quản lý cư dân')

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
    @php
        $statCards = [
            ['label' => 'Tổng cư dân',      'value' => $stats['tong'],             'color' => 'indigo'],
            ['label' => 'Đang cư trú',       'value' => $stats['dang_cu_tru'],      'color' => 'emerald'],
            ['label' => 'Tạm vắng',          'value' => $stats['tam_vang'],         'color' => 'amber'],
            ['label' => 'Đã chuyển đi',      'value' => $stats['da_chuyen_di'],     'color' => 'gray'],
            ['label' => 'Chủ hộ',            'value' => $stats['chu_ho'],           'color' => 'blue'],
            ['label' => 'Thành viên',        'value' => $stats['thanh_vien'],       'color' => 'purple'],
            ['label' => 'Phương tiện',       'value' => $stats['tong_phuong_tien'], 'color' => 'orange'],
            ['label' => 'Căn hộ đang ở',    'value' => $stats['can_ho_dang_o'],    'color' => 'teal'],
        ];
        $colorMap = [
            'indigo'  => 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-700 text-indigo-700 dark:text-indigo-300',
            'emerald' => 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300',
            'amber'   => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-300',
            'gray'    => 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400',
            'blue'    => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300',
            'purple'  => 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-700 text-purple-700 dark:text-purple-300',
            'orange'  => 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-700 text-orange-700 dark:text-orange-300',
            'teal'    => 'bg-teal-50 dark:bg-teal-900/20 border-teal-200 dark:border-teal-700 text-teal-700 dark:text-teal-300',
        ];
    @endphp
    @foreach($statCards as $card)
    <div class="rounded-xl border p-3 text-center {{ $colorMap[$card['color']] }}">
        <p class="text-2xl font-bold">{{ number_format($card['value']) }}</p>
        <p class="text-xs mt-0.5 font-medium">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Search & Filter --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-4">
    <form method="GET" action="{{ route('manager.cu-dan.index') }}" class="p-4">
        <div class="flex flex-wrap gap-3 items-end">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Họ tên, CCCD, Email, SĐT..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                              bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            {{-- Tòa nhà --}}
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tòa nhà</label>
                <select name="toa_nha"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                               bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả</option>
                    @foreach($toaNha as $tn)
                        <option value="{{ $tn->id }}" @selected(request('toa_nha') == $tn->id)>{{ $tn->ten_toa_nha }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Giới tính --}}
            <div class="min-w-[120px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Giới tính</label>
                <select name="gioi_tinh"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                               bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả</option>
                    <option value="1" @selected(request('gioi_tinh') === '1')>Nam</option>
                    <option value="0" @selected(request('gioi_tinh') === '0')>Nữ</option>
                </select>
            </div>
            {{-- Trạng thái --}}
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Trạng thái</label>
                <select name="trang_thai"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                               bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả</option>
                    <option value="1" @selected(request('trang_thai') == '1')>Đang cư trú</option>
                    <option value="2" @selected(request('trang_thai') == '2')>Tạm vắng</option>
                    <option value="3" @selected(request('trang_thai') == '3')>Đã chuyển đi</option>
                </select>
            </div>
            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Lọc
                </button>
                <a href="{{ route('manager.cu-dan.index') }}"
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Reset
                </a>
            </div>
        </div>
        {{-- Keep sort params --}}
        @if(request('sort_by'))<input type="hidden" name="sort_by" value="{{ request('sort_by') }}">@endif
        @if(request('sort_dir'))<input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">@endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Tổng <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $cuDanList->total() }}</span> cư dân
        </p>
        <a href="{{ route('manager.cu-dan.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm cư dân
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    @php
                        function sortUrl($col, $label) {
                            $cur = request('sort_by', 'createdAt');
                            $dir = request('sort_dir', 'desc');
                            $newDir = ($cur === $col && $dir === 'asc') ? 'desc' : 'asc';
                            $params = array_merge(request()->except(['sort_by','sort_dir','page']),
                                ['sort_by' => $col, 'sort_dir' => $newDir]);
                            return request()->url() . '?' . http_build_query($params);
                        }
                    @endphp
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <a href="{{ sortUrl('ho_ten_dem', 'Họ tên') }}" class="flex items-center gap-1 hover:text-indigo-600">
                            Họ tên
                            @if(request('sort_by') === 'ho_ten_dem')
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ request('sort_dir') === 'asc' ? 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' : 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' }}"/></svg>
                            @endif
                        </a>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">CCCD</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Giới tính</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email / SĐT</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tòa nhà / Căn hộ</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vai trò</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Trạng thái</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <a href="{{ sortUrl('createdAt', 'Ngày tạo') }}" class="flex items-center gap-1 hover:text-indigo-600">
                            Ngày tạo
                            @if(request('sort_by', 'createdAt') === 'createdAt')
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ request('sort_dir', 'desc') === 'asc' ? 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' : 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' }}"/></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($cuDanList as $cd)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    {{-- Avatar + Tên --}}
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($cd->avatar_url)
                                <img src="{{ $cd->avatar_url }}" alt="{{ $cd->ho_ten }}"
                                     class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-700">
                            @else
                                <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-indigo-700 dark:text-indigo-300">
                                        {{ strtoupper(mb_substr($cd->ten, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm leading-tight">{{ $cd->ho_ten }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $cd->ngay_sinh?->format('d/m/Y') ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    {{-- CCCD --}}
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs text-gray-600 dark:text-gray-300">{{ $cd->cccd ?? '—' }}</span>
                    </td>
                    {{-- Giới tính --}}
                    <td class="px-4 py-3">
                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ $cd->gioi_tinh_label }}</span>
                    </td>
                    {{-- Email / SĐT --}}
                    <td class="px-4 py-3">
                        <p class="text-sm text-gray-700 dark:text-gray-200">{{ $cd->email ?? '—' }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $cd->sdt ?? '' }}</p>
                    </td>
                    {{-- Tòa nhà / Căn hộ --}}
                    <td class="px-4 py-3">
                        @if($cd->cuDanCanHo->isNotEmpty())
                            @foreach($cd->cuDanCanHo as $cdch)
                            <div class="flex items-center gap-1 {{ !$loop->first ? 'mt-1' : '' }}">
                                <span class="font-medium text-indigo-600 dark:text-indigo-400 text-sm">{{ $cdch->canHo?->so_can_ho }}</span>
                                <span class="text-xs text-gray-400">· {{ $cdch->canHo?->toaNha?->ten_toa_nha }}</span>
                            </div>
                            @endforeach
                        @else
                            <span class="text-xs text-gray-400 italic">Chưa phân công</span>
                        @endif
                    </td>
                    {{-- Vai trò --}}
                    <td class="px-4 py-3">
                        @foreach($cd->cuDanCanHo as $cdch)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 {{ !$loop->first ? 'mt-1' : '' }}">
                            {{ $cdch->vaiTro?->vai_tro ?? '—' }}
                        </span>
                        @endforeach
                    </td>
                    {{-- Trạng thái --}}
                    <td class="px-4 py-3">
                        @php $ttLabel = $cd->trang_thai_label; @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ttLabel['class'] }}">
                            {{ $ttLabel['text'] }}
                        </span>
                    </td>
                    {{-- Ngày tạo --}}
                    <td class="px-4 py-3 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                        {{ $cd->created_at?->format('d/m/Y') ?? '—' }}
                    </td>
                    {{-- Actions --}}
                    <td class="px-4 py-3">
                        <div x-data="{ open: false }" class="relative flex justify-end">
                            <button @click="open = !open" @click.outside="open = false"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute right-0 top-8 z-10 w-40 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg py-1">
                                <a href="{{ route('manager.cu-dan.show', $cd) }}"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Chi tiết
                                </a>
                                <a href="{{ route('manager.cu-dan.edit', $cd) }}"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Chỉnh sửa
                                </a>
                                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                <form method="POST" action="{{ route('manager.cu-dan.destroy', $cd) }}"
                                      @submit.prevent="if(confirm('Xóa cư dân {{ addslashes($cd->ho_ten) }}?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-gray-400 dark:text-gray-500">Không có cư dân nào phù hợp</p>
                            @if(request()->anyFilled(['search','trang_thai','gioi_tinh','toa_nha']))
                            <a href="{{ route('manager.cu-dan.index') }}" class="text-indigo-600 text-sm hover:underline">Xóa bộ lọc</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($cuDanList->hasPages())
    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $cuDanList->links() }}
    </div>
    @endif
</div>
@endsection
