@extends('layouts.manager')
@section('title', 'Quản lý nhân viên')
@section('page-title', 'Quản lý nhân viên')

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    @php
        $statCards = [
            ['label' => 'Tổng nhân viên',   'value' => $stats['tong'],           'color' => 'indigo'],
            ['label' => 'Đang làm việc',     'value' => $stats['dang_lam'],       'color' => 'emerald'],
            ['label' => 'Đã nghỉ',           'value' => $stats['da_nghi'],        'color' => 'gray'],
            ['label' => 'Hóa đơn xử lý',    'value' => $stats['tong_hoa_don'],   'color' => 'blue'],
            ['label' => 'Giao dịch TT',      'value' => $stats['tong_thanh_toan'],'color' => 'purple'],
            ['label' => 'Yêu cầu xử lý',    'value' => $stats['tong_yeu_cau'],   'color' => 'amber'],
        ];
        $colorMap = [
            'indigo'  => 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-700 text-indigo-700 dark:text-indigo-300',
            'emerald' => 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300',
            'gray'    => 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400',
            'blue'    => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300',
            'purple'  => 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-700 text-purple-700 dark:text-purple-300',
            'amber'   => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-300',
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
    <form method="GET" action="{{ route('manager.nhan-vien.index') }}" class="p-4">
        <div class="flex flex-wrap gap-3 items-end">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Mã NV, họ tên, CCCD, email, SĐT..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                              bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            {{-- Chức vụ --}}
            <div class="min-w-[150px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Chức vụ</label>
                <select name="chuc_vu"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                               bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả</option>
                    @foreach($dsChucVu as $cv)
                        <option value="{{ $cv->id }}" @selected(request('chuc_vu') == $cv->id)>{{ $cv->chuc_vu }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Trạng thái --}}
            <div class="min-w-[130px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Trạng thái</label>
                <select name="trang_thai"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                               bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả</option>
                    <option value="1" @selected(request('trang_thai') === '1')>Đang làm việc</option>
                    <option value="0" @selected(request('trang_thai') === '0')>Đã nghỉ</option>
                </select>
            </div>
            {{-- Sort --}}
            <div class="min-w-[150px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sắp xếp theo</label>
                <select name="sort"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                               bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="createdAt"    @selected($sort === 'createdAt')>Ngày tạo</option>
                    <option value="ho_ten"       @selected($sort === 'ho_ten')>Họ tên</option>
                    <option value="ma_nhan_vien" @selected($sort === 'ma_nhan_vien')>Mã nhân viên</option>
                    <option value="ngay_vao_lam" @selected($sort === 'ngay_vao_lam')>Ngày vào làm</option>
                    <option value="trang_thai"   @selected($sort === 'trang_thai')>Trạng thái</option>
                </select>
            </div>
            <div class="min-w-[110px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Thứ tự</label>
                <select name="direction"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                               bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="desc" @selected($direction === 'desc')>Mới nhất</option>
                    <option value="asc"  @selected($direction === 'asc')>Cũ nhất</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Lọc
                </button>
                @if(request()->hasAny(['search','chuc_vu','trang_thai','sort','direction']))
                <a href="{{ route('manager.nhan-vien.index') }}"
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Xóa lọc
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200">
            Danh sách nhân viên
            <span class="ml-2 text-xs font-normal text-gray-500">({{ $nhanVienList->total() }} kết quả)</span>
        </h2>
        <div class="flex gap-2">
        <a href="{{ route('manager.chuc-vu.index') }}"
        class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm chức vụ
        </a>
        <a href="{{ route('manager.nhan-vien.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm nhân viên
        </a>
        </div>
    </div>

    @if($nhanVienList->isEmpty())
    <div class="py-16 text-center">
        <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p class="text-gray-500 dark:text-gray-400 font-medium">Không có nhân viên nào</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Thử thay đổi bộ lọc hoặc thêm nhân viên mới</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left">Nhân viên</th>
                    <th class="px-4 py-3 text-left">Thông tin liên hệ</th>
                    <th class="px-4 py-3 text-left">Chức vụ / Vai trò</th>
                    <th class="px-4 py-3 text-center">Thống kê</th>
                    <th class="px-4 py-3 text-left">Hệ thống</th>
                    <th class="px-4 py-3 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($nhanVienList as $nv)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    {{-- Nhân viên --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-300">
                                    {{ strtoupper(mb_substr($nv->ho_ten, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $nv->ho_ten }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $nv->ma_nhan_vien }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">CCCD: {{ $nv->cccd }}</p>
                            </div>
                        </div>
                    </td>
                    {{-- Liên hệ --}}
                    <td class="px-4 py-3">
                        <div class="space-y-0.5">
                            <p class="text-gray-700 dark:text-gray-300 text-xs">{{ $nv->email }}</p>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">{{ $nv->sdt ?: '—' }}</p>
                            @if($nv->ngay_sinh)
                            <p class="text-gray-400 dark:text-gray-500 text-xs">{{ $nv->ngay_sinh->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </td>
                    {{-- Chức vụ / Vai trò --}}
                    <td class="px-4 py-3">
                        <div class="space-y-1">
                            @if($nv->chucVu)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                                {{ $nv->chucVu->chuc_vu }}
                            </span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                            
                        </div>
                    </td>
                    {{-- Thống kê --}}
                    <td class="px-4 py-3">
                        <div class="grid grid-cols-2 gap-x-3 gap-y-0.5 text-xs text-center">
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $nv->hoa_don_count }}</span>
                                <p class="text-gray-400 dark:text-gray-500">Hóa đơn</p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $nv->yeu_cau_xu_ly_count }}</span>
                                <p class="text-gray-400 dark:text-gray-500">Yêu cầu</p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $nv->thong_bao_count }}</span>
                                <p class="text-gray-400 dark:text-gray-500">Thông báo</p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $nv->bang_tin_count }}</span>
                                <p class="text-gray-400 dark:text-gray-500">Bản tin</p>
                            </div>
                        </div>
                    </td>
                    {{-- Hệ thống --}}
                    <td class="px-4 py-3">
                        <div class="space-y-1">
                            @if($nv->trang_thai == 1)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Hoạt động
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Đã nghỉ
                            </span>
                            @endif
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $nv->created_at?->format('d/m/Y') ?? '—' }}
                            </p>
                        </div>
                    </td>
                    {{-- Thao tác --}}
                    <td class="px-4 py-3">
                        <div x-data="{ open: false }" class="relative flex justify-center">
                            <button @click="open = !open" @click.away="open = false"
                                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute right-0 top-8 z-10 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1">
                                <a href="{{ route('manager.nhan-vien.show', $nv) }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Chi tiết
                                </a>
                                <a href="{{ route('manager.nhan-vien.edit', $nv) }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Chỉnh sửa
                                </a>
                                @if($nv->id !== auth('nhanvien')->id())
                                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                <form method="POST" action="{{ route('manager.nhan-vien.destroy', $nv) }}"
                                      x-data
                                      @submit.prevent="if(confirm('Xóa nhân viên «{{ $nv->ho_ten }}»?\nNhân viên đã phát sinh dữ liệu sẽ không thể xóa.')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Vô hiệu hóa
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($nhanVienList->hasPages())
    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $nhanVienList->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
