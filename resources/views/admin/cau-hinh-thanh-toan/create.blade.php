@extends('layouts.admin')
@section('title', 'Thêm cấu hình thanh toán')
@section('page-title', 'Thêm cấu hình thanh toán')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700">Thông tin cấu hình</h3>
            <p class="text-xs text-gray-400 mt-1">Thêm một thuộc tính thanh toán mới (nhóm: Thanh toán).</p>
        </div>
        <form action="{{ route('admin.cau-hinh-thanh-toan.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mã thuộc tính <span class="text-red-500">*</span></label>
                <input type="text" name="ma_thuoc_tinh" value="{{ old('ma_thuoc_tinh') }}" required
                       placeholder="VD: momo_partner_code, vnp_tmn_code, qr_ngan_hang..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                <p class="mt-1 text-xs text-gray-400">Chỉ gồm chữ thường, số, dấu gạch dưới. Không thể đổi sau khi tạo.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tên thuộc tính <span class="text-red-500">*</span></label>
                <input type="text" name="ten_thuoc_tinh" value="{{ old('ten_thuoc_tinh') }}" required
                       placeholder="VD: MoMo Partner Code"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kiểu dữ liệu <span class="text-red-500">*</span></label>
                <select name="kieu_du_lieu" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Models\CauHinhWebsite::KIEU_DU_LIEU_OPTIONS as $kieu)
                    <option value="{{ $kieu }}" {{ old('kieu_du_lieu') === $kieu ? 'selected' : '' }}>{{ $kieu }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Giá trị</label>
                <input type="text" name="gia_tri" value="{{ old('gia_tri') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mô tả</label>
                <input type="text" name="mo_ta" value="{{ old('mo_ta') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Thứ tự</label>
                    <input type="number" name="thu_tu" value="{{ old('thu_tu', 0) }}" min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div class="flex items-end gap-4 pb-1">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="la_bao_mat" value="1" {{ old('la_bao_mat') ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Bảo mật</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="hidden" name="trang_thai" value="0">
                        <input type="checkbox" name="trang_thai" value="1" {{ old('trang_thai', true) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Kích hoạt</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">Lưu cấu hình</button>
                <a href="{{ route('admin.cau-hinh-thanh-toan.index') }}" class="flex-1 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 text-center transition-colors">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
