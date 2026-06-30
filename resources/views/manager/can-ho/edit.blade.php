@extends('layouts.manager')
@section('title', 'Sửa căn hộ ' . $canHo->so_can_ho)
@section('page-title', 'Sửa căn hộ ' . $canHo->so_can_ho)

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('manager.can-ho.update', $canHo) }}" class="space-y-5">
        @csrf @method('PUT')

        @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <p class="text-sm font-semibold text-red-700 dark:text-red-300 mb-2">Vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)
                <li class="text-sm text-red-600 dark:text-red-400">{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Info banner --}}
        <div class="flex items-start gap-3 px-4 py-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl text-sm text-blue-700 dark:text-blue-300">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Chỉnh sửa thông tin căn hộ <strong>{{ $canHo->so_can_ho }}</strong>. Để thay đổi cư dân, hãy sử dụng chức năng Quản lý Cư dân.</span>
        </div>

        {{-- Thông tin căn hộ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Thông tin căn hộ</h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Tòa nhà --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Tòa nhà <span class="text-red-500">*</span>
                    </label>
                    <select name="toa_nha" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500
                                   @error('toa_nha') border-red-400 @enderror">
                        <option value="">— Chọn tòa nhà —</option>
                        @foreach($toaNha as $tn)
                        <option value="{{ $tn->id }}" @selected(old('toa_nha', $canHo->toa_nha) == $tn->id)>{{ $tn->ten_toa_nha }}</option>
                        @endforeach
                    </select>
                    @error('toa_nha')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Số căn hộ --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Số căn hộ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="so_can_ho" value="{{ old('so_can_ho', $canHo->so_can_ho) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('so_can_ho') border-red-400 @enderror">
                    @error('so_can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Tầng --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Tầng <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="tang" value="{{ old('tang', $canHo->tang) }}" required min="1"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('tang') border-red-400 @enderror">
                    @error('tang')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Loại căn hộ --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Loại căn hộ <span class="text-red-500">*</span>
                    </label>
                    <select name="loai_can_ho" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500
                                   @error('loai_can_ho') border-red-400 @enderror">
                        <option value="">— Chọn loại —</option>
                        @foreach($loaiCanHo as $loai)
                        <option value="{{ $loai->id }}" @selected(old('loai_can_ho', $canHo->loai_can_ho) == $loai->id)>{{ $loai->ten_loai_can_ho }}</option>
                        @endforeach
                    </select>
                    @error('loai_can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Trạng thái --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Trạng thái <span class="text-red-500">*</span>
                    </label>
                    <select name="trang_thai" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500
                                   @error('trang_thai') border-red-400 @enderror">
                        <option value="">— Chọn trạng thái —</option>
                        @foreach($trangThai as $tt)
                        <option value="{{ $tt->id }}" @selected(old('trang_thai', $canHo->trang_thai) == $tt->id)>{{ $tt->ten_trang_thai }}</option>
                        @endforeach
                    </select>
                    @error('trang_thai')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Giá --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Giá (VND)
                    </label>
                    <input type="number" name="gia" value="{{ old('gia', $canHo->gia) }}" min="0" step="1000"
                           placeholder="Để trống nếu chưa xác định"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('gia') border-red-400 @enderror">
                    @error('gia')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Thuộc tính căn hộ --}}
        @if($dsThuocTinh->isNotEmpty())
        @php $hasOldInput = old('toa_nha') !== null; @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 dark:text-gray-200">Thuộc tính căn hộ</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Bỏ chọn để xóa thuộc tính. Tích chọn và nhập giá trị để thêm mới.</p>
                </div>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($dsThuocTinh as $tt)
                @php
                    $ttChecked = $hasOldInput
                        ? (bool) old('thuoc_tinh.'.$tt->id.'.active')
                        : $currentThuocTinh->has($tt->id);
                    $ttValue = old(
                        'thuoc_tinh.'.$tt->id.'.gia_tri',
                        $currentThuocTinh->get($tt->id)?->pivot->gia_tri_thuoc_tinh ?? ''
                    );
                @endphp
                <div x-data="{ checked: {{ $ttChecked ? 'true' : 'false' }} }"
                     class="flex flex-col gap-2 p-3.5 border rounded-lg transition-colors"
                     :class="checked ? 'border-purple-300 dark:border-purple-700 bg-purple-50/50 dark:bg-purple-900/10' : 'border-gray-200 dark:border-gray-600'">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox"
                               x-model="checked"
                               name="thuoc_tinh[{{ $tt->id }}][active]"
                               value="1"
                               class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $tt->ten_thuoc_tinh }}</span>
                        @if($currentThuocTinh->has($tt->id))
                        <span class="text-xs text-purple-500 dark:text-purple-400">(đang có)</span>
                        @endif
                    </label>
                    <div x-show="checked" x-transition class="pl-6">
                        <input type="text"
                               name="thuoc_tinh[{{ $tt->id }}][gia_tri]"
                               value="{{ $ttValue }}"
                               placeholder="Nhập giá trị..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                      bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                      focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Thông tin hiện tại (readonly) --}}
        @if($canHo->cuDanHienTai->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Cư dân hiện tại</h3>
            <div class="space-y-2">
                @foreach($canHo->cuDanHienTai as $cdch)
                <div class="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-700 dark:text-emerald-400 font-semibold text-sm">
                        {{ mb_strtoupper(mb_substr($cdch->cuDan?->ho_ten ?? 'N', -1, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $cdch->cuDan?->ho_ten ?? '–' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cdch->vaiTro?->vai_tro ?? '–' }} · Chuyển đến {{ $cdch->ngay_chuyen_den?->format('d/m/Y') ?? '–' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                Lưu thay đổi
            </button>
            <a href="{{ route('manager.can-ho.show', $canHo) }}"
               class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
