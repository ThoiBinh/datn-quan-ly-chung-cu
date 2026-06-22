@extends('layouts.admin')
@section('title', 'Chi tiết nhân viên')
@section('page-title', 'Chi tiết nhân viên')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <!-- Header -->
        <div class="p-5 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg">
                    {{ strtoupper(substr($user->ho_ten, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 text-lg">{{ $user->ho_ten }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $user->trang_thai == 1 ? 'text-emerald-600' : 'text-red-600' }}">
                <span class="w-2 h-2 rounded-full {{ $user->trang_thai == 1 ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                {{ $user->trang_thai == 1 ? 'Hoạt động' : 'Đã khóa' }}
            </span>
        </div>

        <!-- Thông tin chi tiết -->
        <div class="p-5 space-y-4">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-green-700 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm">{{ session('error') }}</div>
            @endif

            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <dt class="text-gray-500 text-xs mb-1">Mã nhân viên</dt>
                    <dd class="font-mono font-medium text-gray-800">{{ $user->ma_nhan_vien ?? '-' }}</dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <dt class="text-gray-500 text-xs mb-1">CCCD</dt>
                    <dd class="font-mono font-medium text-gray-800">{{ $user->cccd ?: '-' }}</dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <dt class="text-gray-500 text-xs mb-1">Điện thoại</dt>
                    <dd class="font-medium text-gray-800">{{ $user->sdt ?? '-' }}</dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <dt class="text-gray-500 text-xs mb-1">Vai trò</dt>
                    <dd>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $user->isAdmin() ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $user->isAdmin() ? 'Admin' : 'Quản lý' }}
                        </span>
                    </dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <dt class="text-gray-500 text-xs mb-1">Chức vụ</dt>
                    <dd class="font-medium text-gray-800">{{ $user->chucVu?->chuc_vu ?? '-' }}</dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <dt class="text-gray-500 text-xs mb-1">Ngày tạo</dt>
                    <dd class="font-medium text-gray-800">{{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                </div>
            </dl>

            <!-- Actions -->
            <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Sửa thông tin
                </a>

                @if($user->id !== auth('nhanvien')->id())
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 {{ $user->trang_thai == 1 ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600' }} text-white text-sm font-medium rounded-lg transition-colors">
                        {{ $user->trang_thai == 1 ? 'Khóa tài khoản' : 'Mở khóa' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Xác nhận xóa tài khoản {{ addslashes($user->ho_ten) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Xóa tài khoản
                    </button>
                </form>
                @endif

                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Quay lại
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
