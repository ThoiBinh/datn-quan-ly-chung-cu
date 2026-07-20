@extends('layouts.admin')
@section('title', 'Chỉnh sửa phương tiện')
@section('page-title', 'Chỉnh sửa phương tiện')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.phuong-tien.index') }}" class="hover:text-orange-600 transition-colors">Phương tiện</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.phuong-tien.show', $phuongTien) }}" class="hover:text-orange-600 transition-colors font-mono">{{ $phuongTien->bien_so }}</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Chỉnh sửa</span>
    </nav>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Cập nhật thông tin phương tiện</h2>
        </div>

        <form action="{{ route('admin.phuong-tien.update', $phuongTien) }}" method="POST" class="p-5 space-y-5">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <ul class="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Biển số <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bien_so" value="{{ old('bien_so', $phuongTien->bien_so) }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm uppercase font-mono tracking-wider bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-400 {{ $errors->has('bien_so') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('bien_so')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tên phương tiện</label>
                    <input type="text" name="ten_phuong_tien" value="{{ old('ten_phuong_tien', $phuongTien->ten_phuong_tien) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Loại phương tiện <span class="text-red-500">*</span>
                    </label>
                    <select name="loai_phuong_tien"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-400 {{ $errors->has('loai_phuong_tien') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Chọn loại --</option>
                        @foreach($dsLoai as $loai)
                        <option value="{{ $loai->id }}" {{ old('loai_phuong_tien', $phuongTien->loai_phuong_tien) == $loai->id ? 'selected' : '' }}>
                            {{ $loai->ten_loai_phuong_tien }}
                        </option>
                        @endforeach
                    </select>
                    @error('loai_phuong_tien')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Căn hộ <span class="text-red-500">*</span>
                    </label>
                    <select name="can_ho"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-400 {{ $errors->has('can_ho') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Chọn căn hộ --</option>
                        @foreach($dsCanHo as $canHo)
                        <option value="{{ $canHo->id }}" {{ old('can_ho', $phuongTien->can_ho) == $canHo->id ? 'selected' : '' }}>
                            {{ $canHo->so_can_ho }} — {{ $canHo->toaNha?->ten_toa_nha ?? '?' }}
                        </option>
                        @endforeach
                    </select>
                    @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày đăng ký</label>
                    <input type="date" name="ngay_dang_ky"
                           value="{{ old('ngay_dang_ky', $phuongTien->ngay_dang_ky?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Trạng thái <span class="text-red-500">*</span>
                    </label>
                    <select name="trang_thai"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-400 {{ $errors->has('trang_thai') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="1" {{ old('trang_thai', $phuongTien->trang_thai) == 1 ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ old('trang_thai', $phuongTien->trang_thai) == 0 ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-400 dark:text-slate-500">Ngày hủy được hệ thống tự động ghi nhận/xóa theo trạng thái.</p>
                    @error('trang_thai')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.phuong-tien.show', $phuongTien) }}"
                   class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
