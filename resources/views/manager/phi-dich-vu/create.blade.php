@extends('layouts.manager')
@section('title', 'Thêm phí dịch vụ')
@section('page-title', 'Thêm phí dịch vụ')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <nav class="flex items-center gap-1.5 text-sm text-gray-500">
        <a href="{{ route('manager.phi-dich-vu.index') }}" class="hover:text-indigo-600 transition-colors">Phí dịch vụ</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700">Thêm mới</span>
    </nav>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('manager.phi-dich-vu.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-800">Thông tin phí dịch vụ</h2>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Tên phí dịch vụ <span class="text-red-500">*</span></label>
                    <input type="text" name="ten_phi_dich_vu"
                           value="{{ old('ten_phi_dich_vu') }}" maxlength="150"
                           placeholder="Nhập tên phí dịch vụ..."
                           class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('ten_phi_dich_vu') ? 'border-red-400' : 'border-gray-300' }}">
                    @error('ten_phi_dich_vu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Đơn giá (VNĐ) <span class="text-red-500">*</span></label>
                    <input type="number" name="don_gia"
                           value="{{ old('don_gia', 0) }}" min="0" step="1000"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 tabular-nums {{ $errors->has('don_gia') ? 'border-red-400' : 'border-gray-300' }}">
                    @error('don_gia')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Loại phí dịch vụ <span class="text-red-500">*</span></label>
                    <select name="loai_phi_dich_vu"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('loai_phi_dich_vu') ? 'border-red-400' : 'border-gray-300' }}">
                        <option value="">-- Chọn loại phí --</option>
                        @foreach($dsLoaiPhi as $loai)
                        <option value="{{ $loai->id }}" {{ old('loai_phi_dich_vu') == $loai->id ? 'selected' : '' }}>{{ $loai->ten_loai_phi_dich_vu }}</option>
                        @endforeach
                    </select>
                    @error('loai_phi_dich_vu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Đơn vị tính <span class="text-red-500">*</span></label>
                    <select name="don_vi_tinh"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('don_vi_tinh') ? 'border-red-400' : 'border-gray-300' }}">
                        <option value="">-- Chọn đơn vị tính --</option>
                        @foreach($dsDonViTinh as $dv)
                        <option value="{{ $dv->id }}" {{ old('don_vi_tinh') == $dv->id ? 'selected' : '' }}>{{ $dv->don_vi }}</option>
                        @endforeach
                    </select>
                    @error('don_vi_tinh')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Loại tính phí <span class="text-red-500">*</span></label>
                    <select name="loai_tinh_phi"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('loai_tinh_phi') ? 'border-red-400' : 'border-gray-300' }}">
                        <option value="">-- Chọn loại tính phí --</option>
                        @foreach($dsLoaiTinhPhi as $ltp)
                        <option value="{{ $ltp->id }}" {{ old('loai_tinh_phi') == $ltp->id ? 'selected' : '' }}>{{ $ltp->ten_loai }}</option>
                        @endforeach
                    </select>
                    @error('loai_tinh_phi')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('manager.phi-dich-vu.index') }}"
               class="px-4 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Hủy
            </a>
            <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Thêm phí dịch vụ
            </button>
        </div>
    </form>
</div>
@endsection
