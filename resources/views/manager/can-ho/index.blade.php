@extends('layouts.manager')
@section('title', 'Quản lý căn hộ')
@section('page-title', 'Quản lý căn hộ')

@section('content')
<div class="space-y-5">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 dark:text-emerald-300">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-300">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Tổng căn hộ</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['tong'] }}</p>
        </div>
        @foreach($trangThaiStats as $tt)
        @php
            $colorMap = [
                'Đang sử dụng' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800', 'text' => 'text-emerald-700 dark:text-emerald-400'],
                'Còn trống'    => ['bg' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',     'text' => 'text-blue-700 dark:text-blue-400'],
                'Đang bảo trì' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800', 'text' => 'text-amber-700 dark:text-amber-400'],
            ];
            $c = $colorMap[$tt->ten_trang_thai] ?? ['bg' => 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600', 'text' => 'text-gray-700 dark:text-gray-300'];
        @endphp
        <div class="rounded-xl border p-4 shadow-sm {{ $c['bg'] }}">
            <p class="text-xs font-medium {{ $c['text'] }}">{{ $tt->ten_trang_thai }}</p>
            <p class="text-2xl font-bold mt-1 {{ $c['text'] }}">{{ $tt->can_ho_count }}</p>
        </div>
        @endforeach
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Tổng cư dân</p>
            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $stats['tong_cu_dan'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Phương tiện</p>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $stats['tong_phuong_tien'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">HĐ chưa TT</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $stats['tong_hoa_don_chua_tt'] }}</p>
        </div>
    </div>

    {{-- Filters & table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">

        {{-- Toolbar --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" class="flex flex-wrap gap-2 items-center">
                <div class="flex-1 min-w-[180px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Tìm số căn hộ, tòa nhà, cư dân..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <select name="toa_nha"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả tòa</option>
                    @foreach($toaNha as $tn)
                    <option value="{{ $tn->id }}" @selected(request('toa_nha') == $tn->id)>{{ $tn->ten_toa_nha }}</option>
                    @endforeach
                </select>
                <select name="loai_can_ho"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả loại</option>
                    @foreach($loaiCanHo as $loai)
                    <option value="{{ $loai->id }}" @selected(request('loai_can_ho') == $loai->id)>{{ $loai->ten_loai_can_ho }}</option>
                    @endforeach
                </select>
                <select name="trang_thai"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả trạng thái</option>
                    @foreach($trangThai as $tt)
                    <option value="{{ $tt->id }}" @selected(request('trang_thai') == $tt->id)>{{ $tt->ten_trang_thai }}</option>
                    @endforeach
                </select>
                <input type="number" name="tang" value="{{ request('tang') }}" placeholder="Tầng" min="1"
                       class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Lọc
                </button>
                @if(request()->hasAny(['search','toa_nha','loai_can_ho','trang_thai','tang']))
                <a href="{{ route('manager.can-ho.index') }}"
                   class="px-3 py-2 text-sm text-red-500 dark:text-red-400 hover:underline">Xóa lọc</a>
                @endif
            </form>
        </div>

        {{-- Sort + Add --}}
        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Hiển thị {{ $canHo->firstItem() ?? 0 }}–{{ $canHo->lastItem() ?? 0 }} / {{ $canHo->total() }} căn hộ
            </p>
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-1">
                    @foreach(request()->except(['sort_by','sort_dir','page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <select name="sort_by" onchange="this.form.submit()"
                            class="px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-xs bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="createdAt" @selected(request('sort_by','createdAt') === 'createdAt')>Ngày tạo</option>
                        <option value="so_can_ho" @selected(request('sort_by') === 'so_can_ho')>Số căn hộ</option>
                        <option value="tang"      @selected(request('sort_by') === 'tang')>Tầng</option>
                        <option value="gia"       @selected(request('sort_by') === 'gia')>Giá</option>
                    </select>
                    <select name="sort_dir" onchange="this.form.submit()"
                            class="px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-xs bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="desc" @selected(request('sort_dir','desc') === 'desc')>Mới nhất</option>
                        <option value="asc"  @selected(request('sort_dir') === 'asc')>Cũ nhất</option>
                    </select>
                </form>
                <a href="{{ route('manager.loai-can-ho.index') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Thêm loại căn hộ
                </a>
               <a href="{{ route('manager.thuoc-tinh.index') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Thêm Thuộc tính
                </a>
                <a href="{{ route('manager.trang-thai-can-ho.index') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M4 21V8a1 1 0 011-1h5a1 1 0 011 1v13m4-9h4a1 1 0 011 1v8m-5-9V4a1 1 0 011-1h3a1 1 0 011 1v3m-8 5h1m-1 4h1m3-4h1m-1 4h1"/></svg>
                    Trạng thái căn hộ
                </a>
                <a href="{{ route('manager.can-ho.create') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Thêm căn hộ
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Căn hộ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tòa nhà</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tầng</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Loại căn hộ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Giá</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Cư dân</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Xe</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Hóa đơn</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Trạng thái</th>
                        <th class="px-4 py-3 w-12"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($canHo as $ch)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors" x-data="{ open: false }">
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $ch->so_can_ho }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                            {{ $ch->toaNha?->ten_toa_nha ?? '–' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $ch->tang }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                            {{ $ch->loaiCanHo?->ten_loai_can_ho ?? '–' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs font-mono">
                            {{ $ch->gia ? number_format($ch->gia) . 'đ' : '–' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold
                                {{ $ch->cu_dan_hien_tai_count > 0 ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $ch->cu_dan_hien_tai_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold
                                {{ $ch->phuong_tien_count > 0 ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $ch->phuong_tien_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold
                                {{ $ch->hoa_don_count > 0 ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $ch->hoa_don_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $ttLabel = $ch->trangThai?->ten_trang_thai ?? '–';
                                $ttClass = match($ttLabel) {
                                    'Đang sử dụng' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'Còn trống'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'Đang bảo trì' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    default        => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ttClass }}">
                                {{ $ttLabel }}
                            </span>
                        </td>
                        
                        <td class="px-4 py-3">
                            <div class="relative flex justify-end" @click.away="open = false">
                                <button @click="open = !open"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute right-0 top-8 z-10 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg py-1">
                                    <a href="{{ route('manager.can-ho.show', $ch) }}"
                                       class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Xem chi tiết
                                    </a>
                                    <a href="{{ route('manager.can-ho.edit', $ch) }}"
                                       class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Chỉnh sửa
                                    </a>
                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                    <form method="POST" action="{{ route('manager.can-ho.destroy', $ch) }}"
                                          onsubmit="return confirm('Xóa căn hộ {{ addslashes($ch->so_can_ho) }}? Hành động này không thể hoàn tác.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
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
                        <td colspan="11" class="px-4 py-16 text-center text-gray-400 dark:text-gray-500">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <p class="text-sm">Chưa có căn hộ nào phù hợp</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($canHo->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $canHo->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
