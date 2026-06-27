@extends('layouts.resident')
@section('title', 'Gửi yêu cầu mới')

@section('content')
<div class="max-w-lg space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-800">Gửi yêu cầu mới</h1>
        <p class="text-sm text-gray-500 mt-0.5">Mô tả vấn đề bạn cần hỗ trợ</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Nội dung yêu cầu</h2>
        </div>
        <form method="POST" action="{{ route('resident.yeu-cau.store') }}"
              x-data="{ selectedLoai: '{{ old('loai_yeu_cau', '') }}' }"
              class="px-6 py-5 space-y-4">
            @csrf
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
                <input type="text" name="tieu_de" value="{{ old('tieu_de') }}" required
                       placeholder="Mô tả ngắn gọn vấn đề..."
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
            </div>

            @if($loaiYeuCau->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Loại yêu cầu</label>
                <select name="loai_yeu_cau" x-model="selectedLoai"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                    <option value="">-- Chọn loại yêu cầu --</option>
                    @foreach($loaiYeuCau as $loai)
                    <option value="{{ $loai->id }}" {{ old('loai_yeu_cau') == $loai->id ? 'selected' : '' }}>
                        {{ $loai->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($loaiDangKyPTId)
            {{-- Vehicle registration section --}}
            <div x-show="selectedLoai == '{{ $loaiDangKyPTId }}'" x-cloak
                 class="bg-emerald-50 rounded-xl border border-emerald-200 p-4 space-y-3">
                <h3 class="text-sm font-semibold text-emerald-700">Thông tin phương tiện đăng ký</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Biển số xe <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bien_so" value="{{ old('bien_so') }}"
                           :required="selectedLoai == '{{ $loaiDangKyPTId }}'"
                           placeholder="Ví dụ: 51A-12345"
                           style="text-transform:uppercase"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Loại phương tiện <span class="text-red-500">*</span>
                    </label>
                    <select name="loai_phuong_tien"
                            :required="selectedLoai == '{{ $loaiDangKyPTId }}'"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                        <option value="">-- Chọn loại --</option>
                        @foreach($dsLoaiPhuongTien as $loaiPT)
                        <option value="{{ $loaiPT->id }}"
                                {{ old('loai_phuong_tien') == $loaiPT->id ? 'selected' : '' }}>
                            {{ $loaiPT->ten_loai_phuong_tien }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hãng xe</label>
                        <input type="text" name="hang_xe" value="{{ old('hang_xe') }}"
                               placeholder="Ví dụ: Honda, Toyota..."
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Màu xe</label>
                        <input type="text" name="mau_xe" value="{{ old('mau_xe') }}"
                               placeholder="Ví dụ: Đỏ, Trắng..."
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                    </div>
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    @if($loaiDangKyPTId)
                        <span x-show="selectedLoai != '{{ $loaiDangKyPTId }}'">Nội dung chi tiết <span class="text-red-500">*</span></span>
                        <span x-show="selectedLoai == '{{ $loaiDangKyPTId }}'" x-cloak>Ghi chú thêm <span class="text-gray-400 text-xs font-normal">(không bắt buộc)</span></span>
                    @else
                        Nội dung chi tiết <span class="text-red-500">*</span>
                    @endif
                </label>
                <textarea name="noi_dung" rows="5"
                          @if($loaiDangKyPTId) :required="selectedLoai != '{{ $loaiDangKyPTId }}'" @else required @endif
                          placeholder="Mô tả chi tiết vấn đề bạn gặp phải..."
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white resize-none">{{ old('noi_dung') }}</textarea>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Gửi yêu cầu
                </button>
                <a href="{{ route('resident.yeu-cau.index') }}"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
