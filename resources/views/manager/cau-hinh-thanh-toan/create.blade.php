@extends('layouts.manager')
@section('title', 'Thêm cấu hình thanh toán')
@section('page-title', 'Thêm cấu hình thanh toán')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200"><h3 class="font-semibold text-gray-700">Thông tin cấu hình</h3></div>
        <form action="{{ route('manager.cau-hinh-thanh-toan.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Loại phương thức <span class="text-red-500">*</span></label>
                <input type="text" name="loai_phuong_thuc" value="{{ old('loai_phuong_thuc') }}"
                       list="ds-loai" required
                       placeholder="VD: Chuyển khoản, Ví điện tử..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                <datalist id="ds-loai">
                    @foreach($dsLoai as $loai)<option value="{{ $loai }}">@endforeach
                </datalist>
                <p class="mt-1 text-xs text-gray-400">Gõ mới hoặc chọn từ danh sách</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngân hàng / Nhà cung cấp</label>
                <input type="text" name="ten_nha_cung_cap" value="{{ old('ten_nha_cung_cap') }}"
                       placeholder="VD: Vietcombank, MoMo..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Số tài khoản / Định danh thu hưởng</label>
                <input type="text" name="dinh_danh_thu_huong" value="{{ old('dinh_danh_thu_huong') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mã nhận diện (QR)</label>
                <input type="text" name="ma_nhan_dien" value="{{ old('ma_nhan_dien') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Chủ tài khoản</label>
                <input type="text" name="ten_chu_tai_khoan" value="{{ old('ten_chu_tai_khoan') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">Lưu cấu hình</button>
                <a href="{{ route('manager.cau-hinh-thanh-toan.index') }}" class="flex-1 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 text-center transition-colors">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
