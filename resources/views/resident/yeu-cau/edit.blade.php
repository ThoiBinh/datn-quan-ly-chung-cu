@extends('layouts.resident')
@section('title', 'Chỉnh sửa yêu cầu')

@section('content')
<div class="max-w-lg space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-800">Chỉnh sửa yêu cầu</h1>
        <p class="text-sm text-gray-500 mt-0.5">Yêu cầu #{{ $yeuCau->id }} — chỉ chỉnh sửa được khi ở trạng thái Mới</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Nội dung yêu cầu</h2>
        </div>
        <form method="POST" action="{{ route('resident.yeu-cau.update', $yeuCau) }}" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 space-y-1">
                @foreach($errors->all() as $e)
                <p class="text-red-700 text-sm">{{ $e }}</p>
                @endforeach
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Tiêu đề <span class="text-red-500">*</span>
                </label>
                <input type="text" name="tieu_de" value="{{ old('tieu_de', $yeuCau->tieu_de) }}" required
                       placeholder="Mô tả ngắn gọn vấn đề..."
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
            </div>

            @if($loaiYeuCau->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Loại yêu cầu</label>
                <select name="loai_yeu_cau"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                    <option value="">-- Không chọn --</option>
                    @foreach($loaiYeuCau as $loai)
                    <option value="{{ $loai->id }}"
                            {{ old('loai_yeu_cau', $yeuCau->loai_yeu_cau) == $loai->id ? 'selected' : '' }}>
                        {{ $loai->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Mức độ ưu tiên <span class="text-red-500">*</span>
                </label>
                <select name="muc_do" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                    @foreach($dsMucDo as $val => $label)
                    <option value="{{ $val }}"
                            {{ old('muc_do', $yeuCau->muc_do_uu_tien) == $val ? 'selected' : '' }}>
                        {{ $label['text'] }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nội dung chi tiết <span class="text-red-500">*</span>
                </label>
                <textarea name="noi_dung" rows="6" required
                          placeholder="Mô tả chi tiết vấn đề bạn gặp phải..."
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white resize-none">{{ old('noi_dung', $yeuCau->noi_dung) }}</textarea>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Lưu thay đổi
                </button>
                <a href="{{ route('resident.yeu-cau.show', $yeuCau) }}"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
