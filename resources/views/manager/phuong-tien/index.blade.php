@extends('layouts.manager')
@section('title', 'Quản lý phương tiện')
@section('page-title', 'Quản lý phương tiện')

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
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-{{ 3 + $statsTheoLoai->count() }} gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Tổng phương tiện</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['tong'] }}</p>
        </div>
        @foreach($statsTheoLoai as $l)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium truncate" title="{{ $l->ten_loai_phuong_tien }}">{{ $l->ten_loai_phuong_tien }}</p>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $l->phuong_tien_count }}</p>
        </div>
        @endforeach
        <div class="rounded-xl border p-4 shadow-sm bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800">
            <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Đang sử dụng</p>
            <p class="text-2xl font-bold mt-1 text-emerald-700 dark:text-emerald-400">{{ $stats['dang_hoat_dong'] }}</p>
        </div>
        <div class="rounded-xl border p-4 shadow-sm bg-gray-50 dark:bg-gray-700/40 border-gray-200 dark:border-gray-600">
            <p class="text-xs font-medium text-gray-600 dark:text-gray-300">Đã hủy</p>
            <p class="text-2xl font-bold mt-1 text-gray-700 dark:text-gray-300">{{ $stats['da_khoa'] }}</p>
        </div>
    </div>

    {{-- Filters & table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">

        {{-- Toolbar --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" class="flex flex-wrap gap-2 items-center">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Biển số, tên xe, cư dân, CCCD, căn hộ, tòa nhà..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <select name="toa_nha"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả tòa</option>
                    @foreach($dsToaNha as $tn)
                    <option value="{{ $tn->id }}" @selected(request('toa_nha') == $tn->id)>{{ $tn->ten_toa_nha }}</option>
                    @endforeach
                </select>
                <select name="can_ho"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả căn hộ</option>
                    @foreach($dsCanHo as $ch)
                    <option value="{{ $ch->id }}" @selected(request('can_ho') == $ch->id)>{{ $ch->so_can_ho }} - {{ $ch->toaNha?->ten_toa_nha }}</option>
                    @endforeach
                </select>
                <select name="loai_phuong_tien"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả loại</option>
                    @foreach($dsLoai as $l)
                    <option value="{{ $l->id }}" @selected(request('loai_phuong_tien') == $l->id)>{{ $l->ten_loai_phuong_tien }}</option>
                    @endforeach
                </select>
                <select name="trang_thai"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" @selected(request('trang_thai') === '1')>Đang sử dụng</option>
                    <option value="0" @selected(request('trang_thai') === '0')>Đã hủy</option>
                </select>
                <button type="submit"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Lọc
                </button>
                @if(request()->hasAny(['search','toa_nha','can_ho','loai_phuong_tien','trang_thai']))
                <a href="{{ route('manager.phuong-tien.index') }}"
                   class="px-3 py-2 text-sm text-red-500 dark:text-red-400 hover:underline">Xóa lọc</a>
                @endif
            </form>
        </div>

        {{-- Sort + Add --}}
        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Hiển thị {{ $phuongTien->firstItem() ?? 0 }}–{{ $phuongTien->lastItem() ?? 0 }} / {{ $phuongTien->total() }} phương tiện
            </p>
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-1">
                    @foreach(request()->except(['sort_by','sort_dir','page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <select name="sort_by" onchange="this.form.submit()"
                            class="px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-xs bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="ngay_dang_ky" @selected($sort === 'ngay_dang_ky')>Ngày đăng ký</option>
                        <option value="ngay_huy"     @selected($sort === 'ngay_huy')>Ngày hủy</option>
                        <option value="bien_so"      @selected($sort === 'bien_so')>Biển số</option>
                        <option value="so_can_ho"    @selected($sort === 'so_can_ho')>Số căn hộ</option>
                        <option value="ho_ten"       @selected($sort === 'ho_ten')>Họ tên cư dân</option>
                    </select>
                    <select name="sort_dir" onchange="this.form.submit()"
                            class="px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-xs bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="desc" @selected($direction === 'desc')>Mới nhất / Z-A</option>
                        <option value="asc"  @selected($direction === 'asc')>Cũ nhất / A-Z</option>
                    </select>
                </form>
                <a href="{{ route('manager.loai-phuong-tien.index') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    Quản lý loại xe
                </a>
                <a href="{{ route('manager.phuong-tien.create') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Thêm xe
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Biển số</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Loại / Tên xe</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Chủ hộ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Căn hộ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ngày đăng ký</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Trạng thái</th>
                        <th class="px-4 py-3 w-12"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($phuongTien as $pt)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors" x-data="{ open: false }">
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $pt->bien_so }}</td>
                        <td class="px-4 py-3">
                            <p class="text-gray-800 dark:text-gray-200 text-xs font-medium">{{ $pt->loaiPhuongTien?->ten_loai_phuong_tien ?? '–' }}</p>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">{{ $pt->ten_phuong_tien ?? '–' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($pt->canHo?->chuHo?->cuDan)
                            <a href="{{ route('manager.cu-dan.show', $pt->canHo->chuHo->cuDan) }}"
                               class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline text-xs">
                                {{ $pt->canHo->chuHo->cuDan->ho_ten }}
                            </a>
                            <p class="text-gray-400 dark:text-gray-500 text-xs font-mono">{{ $pt->canHo->chuHo->cuDan->cccd ?? '–' }}</p>
                            @else
                            <span class="text-gray-400 dark:text-gray-600 text-xs">Chưa có chủ hộ</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                            {{ $pt->canHo?->so_can_ho ?? '–' }} · {{ $pt->canHo?->toaNha?->ten_toa_nha ?? '–' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $pt->ngay_dang_ky?->format('d/m/Y') ?? '–' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pt->trang_thai_label['class'] }}">
                                {{ $pt->trang_thai_label['text'] }}
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
                                    <a href="{{ route('manager.phuong-tien.show', $pt) }}"
                                       class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Xem chi tiết
                                    </a>
                                    <a href="{{ route('manager.phuong-tien.edit', $pt) }}"
                                       class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Chỉnh sửa
                                    </a>
                                    @if($pt->trang_thai == 1)
                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                    <form method="POST" action="{{ route('manager.phuong-tien.destroy', $pt) }}"
                                          onsubmit="return confirm('Hủy đăng ký phương tiện {{ addslashes($pt->bien_so) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hủy đăng ký
                                        </button>
                                    </form>
                                    @endif
                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                    <form method="POST" action="{{ route('manager.phuong-tien.xoa-mem', $pt) }}"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa phương tiện {{ addslashes($pt->bien_so) }}?')">
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
                        <td colspan="7" class="px-4 py-16 text-center text-gray-400 dark:text-gray-500">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 17h8m-8-4h8M5 21h14a2 2 0 002-2V7l-5-5H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <p class="text-sm">Chưa có phương tiện nào phù hợp</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($phuongTien->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $phuongTien->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
