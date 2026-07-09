@extends('layouts.manager')
@section('title', 'Sửa bảng tin')
@section('page-title', 'Sửa bài bảng tin')

@section('content')
<div class="max-w-2xl" x-data="{ preview: {{ $bangTin->hinh_url_full ? \Illuminate\Support\Js::from($bangTin->hinh_url_full) : 'null' }}, removeImg: false }">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200"><h2 class="font-semibold text-gray-800">Chỉnh sửa bài đăng</h2></div>
        <form method="POST" action="{{ route('manager.bang-tin.update', $bangTin) }}" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf @method('PUT')
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">@foreach($errors->all() as $e)<p class="text-red-700 text-sm">{{ $e }}</p>@endforeach</div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" name="tieu_de" value="{{ old('tieu_de', $bangTin->tieu_de) }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh <span class="text-xs text-gray-400 font-normal">(tùy chọn)</span></label>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                        <template x-if="!preview || removeImg">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </template>
                        <img x-show="preview && !removeImg" :src="preview" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 space-y-2">
                        <input type="file" name="hinh_anh" accept="image/*"
                               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview; removeImg = false"
                               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @if($bangTin->hinh_url_full)
                        <label class="flex items-center gap-2 text-xs text-red-500 cursor-pointer">
                            <input type="checkbox" name="xoa_hinh" value="1" x-model="removeImg" class="w-3.5 h-3.5 rounded border-gray-300 text-red-600 focus:ring-red-400">
                            Xóa ảnh hiện tại
                        </label>
                        @endif
                    </div>
                </div>
                @error('hinh_anh')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung <span class="text-red-500">*</span></label>
                <textarea name="noi_dung" rows="10" required
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y">{{ old('noi_dung', $bangTin->noi_dung) }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">Lưu</button>
                <a href="{{ route('manager.bang-tin.show', $bangTin) }}" class="flex-1 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 text-center">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
