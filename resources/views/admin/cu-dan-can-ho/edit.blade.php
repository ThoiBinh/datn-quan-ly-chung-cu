@extends('layouts.admin')
@section('title', 'Sửa cư trú #' . $cuDanCanHo->id)

@section('content')
<div class="max-w-2xl">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-5">
        <a href="{{ route('admin.cu-dan-can-ho.index') }}" class="hover:text-indigo-600 transition-colors">Cư dân - Căn hộ</a>
        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('admin.cu-dan-can-ho.show', $cuDanCanHo) }}" class="hover:text-indigo-600 transition-colors">#{{ $cuDanCanHo->id }}</a>
        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 font-medium">Chỉnh sửa</span>
    </nav>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100">
            <h1 class="text-lg font-bold text-gray-800">Chỉnh sửa thông tin cư trú</h1>
            <p class="text-sm text-gray-500 mt-0.5">Bản ghi #{{ $cuDanCanHo->id }}</p>
        </div>

        <form method="POST" action="{{ route('admin.cu-dan-can-ho.update', $cuDanCanHo) }}" class="px-6 py-5 space-y-5">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                    @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Cư dân --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Cư dân <span class="text-red-500">*</span>
                </label>
                <select name="cu_dan" required
                        class="w-full px-3 py-2.5 border {{ $errors->has('cu_dan') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">-- Chọn cư dân --</option>
                    @foreach($dsCuDan as $cd)
                    <option value="{{ $cd->id }}" {{ old('cu_dan', $cuDanCanHo->cu_dan) == $cd->id ? 'selected' : '' }}>
                        {{ $cd->ho_ten }} — {{ $cd->cccd ?? $cd->email }}
                    </option>
                    @endforeach
                </select>
                @error('cu_dan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Căn hộ --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Căn hộ <span class="text-red-500">*</span>
                </label>
                <select name="can_ho" required
                        class="w-full px-3 py-2.5 border {{ $errors->has('can_ho') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">-- Chọn căn hộ --</option>
                    @foreach($dsCanHo as $ch)
                    <option value="{{ $ch->id }}" {{ old('can_ho', $cuDanCanHo->can_ho) == $ch->id ? 'selected' : '' }}>
                        {{ $ch->so_can_ho }}{{ $ch->toaNha ? ' — ' . $ch->toaNha->ten_toa_nha : '' }}
                    </option>
                    @endforeach
                </select>
                @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Vai trò --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Vai trò trong căn hộ</label>
                <select name="vai_tro"
                        class="w-full px-3 py-2.5 border {{ $errors->has('vai_tro') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">-- Không xác định --</option>
                    @foreach($dsVaiTro as $vt)
                    <option value="{{ $vt->id }}" {{ old('vai_tro', $cuDanCanHo->vai_tro) == $vt->id ? 'selected' : '' }}>
                        {{ $vt->vai_tro }}
                    </option>
                    @endforeach
                </select>
                @error('vai_tro')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Ngày chuyển --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày chuyển đến</label>
                    <input type="date" name="ngay_chuyen_den"
                           value="{{ old('ngay_chuyen_den', $cuDanCanHo->ngay_chuyen_den?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 border {{ $errors->has('ngay_chuyen_den') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"/>
                    @error('ngay_chuyen_den')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày chuyển đi</label>
                    <input type="date" name="ngay_chuyen_di"
                           value="{{ old('ngay_chuyen_di', $cuDanCanHo->ngay_chuyen_di?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 border {{ $errors->has('ngay_chuyen_di') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"/>
                    @error('ngay_chuyen_di')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Trạng thái <span class="text-red-500">*</span>
                </label>
                <select name="trang_thai" required
                        class="w-full px-3 py-2.5 border {{ $errors->has('trang_thai') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @foreach($dsTrangThai as $val => $label)
                    <option value="{{ $val }}" {{ old('trang_thai', $cuDanCanHo->trang_thai) == $val ? 'selected' : '' }}>
                        {{ $label['text'] }}
                    </option>
                    @endforeach
                </select>
                @error('trang_thai')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Lưu thay đổi
                </button>
                <a href="{{ route('admin.cu-dan-can-ho.show', $cuDanCanHo) }}"
                   class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
