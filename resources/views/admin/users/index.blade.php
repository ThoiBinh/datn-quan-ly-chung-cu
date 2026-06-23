@extends('layouts.admin')
@section('title', 'Quản lý tài khoản')
@section('page-title', 'Quản lý tài khoản')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm" x-data="{
    confirmToggle: null,
    openToggle(name, action) { this.confirmToggle = { name, action }; },
    closeToggle() { this.confirmToggle = null; }
}">

    <!-- Header: Search + Filter + Add -->
    <div class="p-5 border-b border-gray-200">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <!-- Search -->
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tìm theo tên, email, SĐT, CCCD..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Filter loại -->
            <select name="type" class="px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">Tất cả loại</option>
                <option value="nhan_vien" {{ request('type') === 'nhan_vien' ? 'selected' : '' }}>Nhân viên</option>
                <option value="cu_dan"    {{ request('type') === 'cu_dan'    ? 'selected' : '' }}>Cư dân</option>
            </select>

            <!-- Filter trạng thái -->
            <select name="status" class="px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">Tất cả trạng thái</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
            </select>

            <button type="submit"
                    class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors whitespace-nowrap">
                Lọc
            </button>

            @if(request()->hasAny(['search','type','status']))
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition-colors whitespace-nowrap">
                Xóa lọc
            </a>
            @endif
        </form>
    </div>

    <!-- Sub-header: total + add button -->
    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50/50">
        <p class="text-sm text-gray-500">Tổng: <span class="font-semibold text-gray-700">{{ $users->total() }}</span> tài khoản</p>
        <a href="{{ route('admin.users.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm tài khoản
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Họ tên</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">SĐT</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">CCCD</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Loại TK</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                    <th class="px-4 py-3 w-28"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $item)
                <tr class="hover:bg-gray-50/70 transition-colors">
                    <!-- ID -->
                    <td class="px-4 py-3.5 text-xs text-gray-400 font-mono">{{ $item->id }}</td>

                    <!-- Họ tên -->
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                                {{ $item->src_type === 'nhan_vien' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ mb_strtoupper(mb_substr($item->ho_ten, 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-800">{{ $item->ho_ten }}</span>
                        </div>
                    </td>

                    <!-- Email -->
                    <td class="px-4 py-3.5 text-gray-600 text-xs">{{ $item->email ?: '—' }}</td>

                    <!-- SĐT -->
                    <td class="px-4 py-3.5 text-gray-600">{{ $item->sdt ?: '—' }}</td>

                    <!-- CCCD -->
                    <td class="px-4 py-3.5 text-gray-600 font-mono text-xs">{{ $item->cccd ?: '—' }}</td>

                    <!-- Loại TK -->
                    <td class="px-4 py-3.5">
                        @if($item->src_type === 'nhan_vien')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                Nhân viên
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                Cư dân
                            </span>
                        @endif
                    </td>

                    <!-- Trạng thái -->
                    <td class="px-4 py-3.5">
                        @if($item->trang_thai == 1)
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Đã khóa
                            </span>
                        @endif
                    </td>

                    <!-- Ngày tạo -->
                    <td class="px-4 py-3.5 text-gray-500 text-xs whitespace-nowrap">
                        {{ $item->created_at?->format('d/m/Y') ?? '—' }}
                    </td>

                    <!-- Thao tác -->
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-1.5 justify-end">
                            <!-- Xem -->
                            <a href="{{ route('admin.users.show', ['type' => $item->url_type, 'id' => $item->id]) }}"
                               title="Xem chi tiết"
                               class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            <!-- Sửa -->
                            <a href="{{ route('admin.users.edit', ['type' => $item->url_type, 'id' => $item->id]) }}"
                               title="Chỉnh sửa"
                               class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            <!-- Khóa/Mở khóa -->
                            @if(!$item->is_self)
                            <button type="button"
                                    title="{{ $item->trang_thai == 1 ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}"
                                    @click="openToggle('{{ addslashes($item->ho_ten) }}', '{{ route('admin.users.toggle-status', ['type' => $item->url_type, 'id' => $item->id]) }}')"
                                    class="p-1.5 rounded-md transition-colors {{ $item->trang_thai == 1 ? 'text-gray-400 hover:text-amber-600 hover:bg-amber-50' : 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50' }}">
                                @if($item->trang_thai == 1)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                                @endif
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-5 py-14 text-center text-gray-400">
                        <svg class="w-14 h-14 mx-auto mb-3 opacity-25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm">Không tìm thấy tài khoản nào</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
    @endif

    <!-- Modal xác nhận toggle status -->
    <div x-show="confirmToggle !== null"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
         @click.self="closeToggle()"
         style="display:none">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Xác nhận thay đổi trạng thái</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Tài khoản: <span class="font-medium text-gray-700" x-text="confirmToggle?.name"></span></p>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button @click="closeToggle()" type="button"
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Hủy
                </button>
                <form :action="confirmToggle?.action" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                        Xác nhận
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
