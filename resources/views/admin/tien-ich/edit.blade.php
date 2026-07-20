@extends('layouts.admin')
@section('title', 'Sửa tiện ích')
@section('page-title', 'Sửa tiện ích')

@section('content')
<div class="max-w-2xl mx-auto space-y-5" x-data="{ preview: {{ $tienIch->hinh_url ? \Illuminate\Support\Js::from(asset('storage/' . $tienIch->hinh_url)) : 'null' }}, removeImg: false }">

    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('admin.tien-ich.index') }}" class="hover:text-indigo-600 transition-colors">Tiện ích</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.tien-ich.show', $tienIch) }}" class="hover:text-indigo-600 transition-colors truncate max-w-xs">{{ $tienIch->ten_tien_ich }}</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Chỉnh sửa</span>
    </nav>

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
        <ul class="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form id="update-form" action="{{ route('admin.tien-ich.update', $tienIch) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Chỉnh sửa thông tin</h2>
            </div>

            <div class="p-5 space-y-4">
                {{-- Ảnh --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Hình ảnh</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-xl border border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <template x-if="!preview || removeImg">
                                <svg class="w-8 h-8 text-gray-300 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </template>
                            <img x-show="preview && !removeImg" :src="preview" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 space-y-2">
                            <input type="file" name="hinh_anh" accept="image/*"
                                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview; removeImg = false"
                                   class="block w-full text-sm text-gray-600 dark:text-slate-300 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                            @if($tienIch->hinh_url)
                            <label class="flex items-center gap-2 text-xs text-red-500 cursor-pointer">
                                <input type="checkbox" name="xoa_hinh" value="1" x-model="removeImg" class="w-3.5 h-3.5 rounded border-gray-300 text-red-600 focus:ring-red-400">
                                Xóa ảnh hiện tại
                            </label>
                            @endif
                        </div>
                    </div>
                    @error('hinh_anh')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Tên tiện ích --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Tên tiện ích <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="ten_tien_ich" value="{{ old('ten_tien_ich', $tienIch->ten_tien_ich) }}" maxlength="255"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('ten_tien_ich') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('ten_tien_ich')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Loại tiện ích --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                            Loại tiện ích <span class="text-red-500">*</span>
                        </label>
                        <select name="loai_tien_ich"
                                class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('loai_tien_ich') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                            <option value="">-- Chọn loại --</option>
                            @foreach($dsLoaiTienIch as $loai)
                            <option value="{{ $loai->id }}" {{ old('loai_tien_ich', $tienIch->loai_tien_ich) == $loai->id ? 'selected' : '' }}>{{ $loai->ten_loai_tien_ich }}</option>
                            @endforeach
                        </select>
                        @error('loai_tien_ich')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Tòa nhà --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tòa nhà</label>
                        <select name="toa_nha"
                                class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('toa_nha') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                            <option value="">-- Toàn khu (không thuộc riêng tòa nào) --</option>
                            @foreach($dsToaNha as $tn)
                            <option value="{{ $tn->id }}" {{ old('toa_nha', $tienIch->toa_nha) == $tn->id ? 'selected' : '' }}>{{ $tn->ten_toa_nha }}</option>
                            @endforeach
                        </select>
                        @error('toa_nha')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Vị trí --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Vị trí</label>
                    <input type="text" name="vi_tri" value="{{ old('vi_tri', $tienIch->vi_tri) }}" maxlength="255"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('vi_tri') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('vi_tri')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Mô tả --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Mô tả</label>
                    <textarea name="mo_ta" rows="3"
                              class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('mo_ta') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">{{ old('mo_ta', $tienIch->mo_ta) }}</textarea>
                    @error('mo_ta')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    {{-- Sức chứa --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Sức chứa</label>
                        <input type="number" name="suc_chua" value="{{ old('suc_chua', $tienIch->suc_chua) }}" min="1"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('suc_chua') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                        @error('suc_chua')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    {{-- Giờ mở cửa --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Giờ mở cửa</label>
                        <input type="time" name="gio_mo_cua" value="{{ old('gio_mo_cua', $tienIch->gio_mo_cua ? substr($tienIch->gio_mo_cua, 0, 5) : '') }}"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('gio_mo_cua') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                        @error('gio_mo_cua')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    {{-- Giờ đóng cửa --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Giờ đóng cửa</label>
                        <input type="time" name="gio_dong_cua" value="{{ old('gio_dong_cua', $tienIch->gio_dong_cua ? substr($tienIch->gio_dong_cua, 0, 5) : '') }}"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('gio_dong_cua') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                        @error('gio_dong_cua')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Giá sử dụng --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                            Giá sử dụng (VNĐ) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="phi_su_dung" value="{{ old('phi_su_dung', $tienIch->phi_su_dung) }}" min="0" step="1000"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 tabular-nums {{ $errors->has('phi_su_dung') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                        @error('phi_su_dung')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    {{-- Trạng thái --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                            Trạng thái <span class="text-red-500">*</span>
                        </label>
                        <select name="trang_thai"
                                class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('trang_thai') ? 'border-red-400' : 'border-gray-300 dark:border-slate-600' }}">
                            @foreach(\App\Models\TienIch::dsTrangThai() as $id => $label)
                            <option value="{{ $id }}" {{ (string) old('trang_thai', $tienIch->trang_thai) === (string) $id ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('trang_thai')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Cần đặt trước --}}
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="hidden" name="can_dat_truoc" value="0">
                    <input type="checkbox" name="can_dat_truoc" value="1" {{ old('can_dat_truoc', $tienIch->can_dat_truoc) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-400">
                    <span class="text-sm text-gray-700 dark:text-slate-300">Yêu cầu đặt lịch trước khi sử dụng</span>
                </label>
            </div>
        </div>
    </form>

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.tien-ich.show', $tienIch) }}"
           class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
            Hủy
        </a>
        <button type="submit" form="update-form"
                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
            Lưu thay đổi
        </button>
    </div>
</div>
@endsection
