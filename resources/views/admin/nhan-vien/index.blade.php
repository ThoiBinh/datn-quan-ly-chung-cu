@extends('layouts.admin')
@section('title', 'Quản lý nhân viên')
@section('page-title', 'Quản lý nhân viên')

@section('content')
<div class="space-y-4">

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
            $statCards = [
                ['label' => 'Tổng nhân viên',  'value' => $stats['tong'],            'color' => 'indigo'],
                ['label' => 'Đang làm việc',    'value' => $stats['dang_lam'],        'color' => 'emerald'],
                ['label' => 'Đã nghỉ việc',     'value' => $stats['da_nghi'],         'color' => 'gray'],
                ['label' => 'Hóa đơn đã lập',  'value' => $stats['tong_hoa_don'],    'color' => 'blue'],
                ['label' => 'Thanh toán xử lý', 'value' => $stats['tong_thanh_toan'], 'color' => 'purple'],
                ['label' => 'Yêu cầu xử lý',    'value' => $stats['tong_yeu_cau'],    'color' => 'amber'],
            ];
            $colorMap = [
                'indigo'  => 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-700 text-indigo-700 dark:text-indigo-300',
                'emerald' => 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300',
                'gray'    => 'bg-gray-50 dark:bg-slate-700/40 border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300',
                'blue'    => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300',
                'purple'  => 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-700 text-purple-700 dark:text-purple-300',
                'amber'   => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-300',
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="rounded-xl border p-3.5 text-center {{ $colorMap[$card['color']] }}">
            <p class="text-2xl font-bold">{{ number_format($card['value']) }}</p>
            <p class="text-xs mt-0.5 font-medium">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Search + Filter -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.nhan-vien.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="relative flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Tìm kiếm</label>
                <svg class="absolute left-3 top-1/2 translate-y-[3px] w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Mã NV, họ tên, CCCD, email, SĐT..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Chức vụ</label>
                <select name="chuc_vu"
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Tất cả</option>
                    @foreach($dsChucVu as $cv)
                        <option value="{{ $cv->id }}" @selected(request('chuc_vu') == $cv->id)>{{ $cv->chuc_vu }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-[150px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Trạng thái</label>
                <select name="trang_thai"
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Tất cả</option>
                    <option value="1" @selected(request('trang_thai') === '1')>Đang làm việc</option>
                    <option value="0" @selected(request('trang_thai') === '0')>Đã nghỉ</option>
                </select>
            </div>

            <div class="min-w-[150px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Sắp xếp theo</label>
                <select name="sort"
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="createdAt"    @selected($sort === 'createdAt')>Ngày tạo</option>
                    <option value="ho_ten"       @selected($sort === 'ho_ten')>Họ tên</option>
                    <option value="ma_nhan_vien" @selected($sort === 'ma_nhan_vien')>Mã nhân viên</option>
                    <option value="ngay_vao_lam" @selected($sort === 'ngay_vao_lam')>Ngày vào làm</option>
                    <option value="trang_thai"   @selected($sort === 'trang_thai')>Trạng thái</option>
                </select>
            </div>

            <div class="min-w-[110px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Thứ tự</label>
                <select name="direction"
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="desc" @selected($direction === 'desc')>Mới nhất</option>
                    <option value="asc"  @selected($direction === 'asc')>Cũ nhất</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">Lọc</button>
            @if(request()->hasAny(['search','chuc_vu','trang_thai','sort','direction']))
            <a href="{{ route('admin.nhan-vien.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition-colors">Xóa lọc</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden"
     x-data="{
        confirmToggle: null,
        openToggle(id, name, action, isLock) {
            this.confirmToggle = { id, name, action, isLock };
        },
        closeToggle() {
            this.confirmToggle = null;
        }
     }">

    <!-- HEADER -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800">
        <div class="flex md:flex-row md:items-center md:justify-between gap-3">

            <p class="text-sm text-gray-500 dark:text-slate-400">
                Tổng:
                <span class="font-semibold text-gray-800 dark:text-white">
                    {{ $nhanVien->total() }}
                </span>
                nhân viên
            </p>
            <div class="flex flex-wrap gap-2 items-center ml-auto">
                    <a href="{{ route('admin.chuc-vu.index') }}" 
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl
                  text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white shadow-md hover:shadow-lg transition">
                        Quản lý chức vụ
                    
                 </a>
                <a href="{{ route('admin.nhan-vien.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl
                  text-sm font-medium bg-emerald-600 hover:bg-emerald-700 text-white
                  shadow-md hover:shadow-lg transition ml-auto">
                    + Thêm nhân viên
                </a>
            </div>
           

        </div>
    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">

            <thead class="bg-gray-50 dark:bg-slate-700/40 border-b border-gray-200 dark:border-slate-700">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nhân viên</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Liên hệ</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Chức vụ </th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Thống kê</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-4 py-3"></th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">

            @forelse($nhanVien as $nv)
                <tr class="group hover:bg-gray-50/60 dark:hover:bg-slate-700/30 transition">

                    <!-- NHÂN VIÊN -->
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600
                                        text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ mb_strtoupper(mb_substr($nv->ho_ten, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-800 dark:text-white truncate">{{ $nv->ho_ten }}</p>
                                <p class="text-xs text-gray-400 dark:text-slate-500 font-mono">{{ $nv->ma_nhan_vien ?? '—' }}</p>
                                <p class="text-xs text-gray-400 dark:text-slate-500 font-mono">CCCD: {{ $nv->cccd ?: '—' }}</p>
                            </div>
                        </div>
                    </td>

                    <!-- LIÊN HỆ -->
                    <td class="px-4 py-3">
                        <p class="text-gray-600 dark:text-slate-300 text-xs">{{ $nv->email }}</p>
                        <p class="text-gray-500 dark:text-slate-400 text-xs">{{ $nv->sdt ?: '—' }}</p>
                        @if($nv->ngay_sinh)
                        <p class="text-gray-400 dark:text-slate-500 text-xs">{{ $nv->ngay_sinh->format('d/m/Y') }}</p>
                        @endif
                    </td>

                    <!-- CHỨC VỤ / VAI TRÒ -->
                    <td class="px-4 py-3">
                        <div class="space-y-1">
                            <span class="inline-flex px-2 py-1 text-xs rounded-full
                                         bg-indigo-100 text-indigo-700
                                         dark:bg-indigo-900/40 dark:text-indigo-300">
                                {{ $nv->chucVu?->chuc_vu ?? '—' }}
                            </span>
                           
                        </div>
                    </td>

                    <!-- THỐNG KÊ -->
                    <td class="px-4 py-3">
                        <div class="grid grid-cols-2 gap-x-3 gap-y-1 text-xs text-center">
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $nv->hoa_don_count }}</span>
                                <p class="text-gray-400 dark:text-slate-500">Hóa đơn</p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $nv->lich_su_thanh_toan_count }}</span>
                                <p class="text-gray-400 dark:text-slate-500">Thanh toán</p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $nv->yeu_cau_xu_ly_count }}</span>
                                <p class="text-gray-400 dark:text-slate-500">Yêu cầu</p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $nv->thong_bao_count }}</span>
                                <p class="text-gray-400 dark:text-slate-500">Thông báo</p>
                            </div>
                        </div>
                    </td>

                    <!-- TRẠNG THÁI -->
                    <td class="px-4 py-3">
                        @if($nv->trang_thai == 1)
                            <span class="inline-flex items-center gap-1 text-xs text-emerald-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Hoạt động
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs text-red-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Đã khóa
                            </span>
                        @endif
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">{{ $nv->created_at?->format('d/m/Y') ?? '—' }}</p>
                    </td>

                    <!-- ACTION -->
                    <td class="px-4 py-3">
                        <div class="flex justify-end items-center gap-1 opacity-60 group-hover:opacity-100 transition">

                            <a href="{{ route('admin.nhan-vien.show', $nv) }}"
                               class="p-2 rounded-lg hover:bg-indigo-50 text-gray-400 hover:text-indigo-600">
                                👁
                            </a>

                            <a href="{{ route('admin.nhan-vien.edit', $nv) }}"
                               class="p-2 rounded-lg hover:bg-blue-50 text-gray-400 hover:text-blue-600">
                                ✏️
                            </a>

                            @if($nv->id !== auth('nhanvien')->id())
                                <button @click="openToggle({{ $nv->id }}, '{{ addslashes($nv->ho_ten) }}', '{{ route('admin.nhan-vien.toggle-status', $nv) }}', {{ $nv->trang_thai == 1 ? 'true' : 'false' }})"
                                        class="p-2 rounded-lg hover:bg-amber-50 text-gray-400 hover:text-amber-600">
                                    🔒
                                </button>

                                <form method="POST" action="{{ route('admin.nhan-vien.destroy', $nv) }}"
                                      x-data
                                      @submit.prevent="if(confirm('Xóa nhân viên «{{ addslashes($nv->ho_ten) }}»?\nNhân viên đã phát sinh dữ liệu sẽ không thể xóa.')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-600">
                                        🗑
                                    </button>
                                </form>
                            @endif

                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-16 text-gray-400">
                        Không có nhân viên
                    </td>
                </tr>
            @endforelse

            </tbody>

        </table>
    </div>

    <!-- PAGINATION -->
    @if($nhanVien->hasPages())
        <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $nhanVien->links() }}
        </div>
    @endif


    <!-- MODAL -->
    <template x-teleport="body">
        <div x-show="confirmToggle"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md p-4"
             @click.self="closeToggle()">

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6">

                <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                    Xác nhận thao tác
                </h2>

                <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                    Nhân viên: <span x-text="confirmToggle?.name" class="font-semibold"></span>
                </p>

                <div class="mt-6 flex gap-3">

                    <button @click="closeToggle()"
                            class="flex-1 px-4 py-2 rounded-xl border border-gray-200 dark:border-slate-600">
                        Hủy
                    </button>

                    <form :action="confirmToggle?.action" method="POST" class="flex-1">
                        @csrf @method('PATCH')

                        <button type="submit"
                                class="w-full px-4 py-2 rounded-xl text-white"
                                :class="confirmToggle?.isLock ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600'">
                            Xác nhận
                        </button>

                    </form>

                </div>

            </div>

        </div>
    </template>

</div>
</div>
@endsection
