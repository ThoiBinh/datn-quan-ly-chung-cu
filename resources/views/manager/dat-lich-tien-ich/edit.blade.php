@extends('layouts.manager')
@section('title', 'Chỉnh sửa đặt lịch tiện ích')
@section('page-title', 'Chỉnh sửa đặt lịch tiện ích')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-slate-400">
        <a href="{{ route('manager.dat-lich-tien-ich.index') }}" class="hover:text-indigo-600 transition-colors">Đặt lịch tiện ích</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('manager.dat-lich-tien-ich.show', $datLichTienIch) }}" class="hover:text-indigo-600 transition-colors font-mono">{{ $datLichTienIch->ma_dat_lich }}</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 dark:text-slate-200">Chỉnh sửa</span>
    </nav>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Cập nhật đặt lịch tiện ích</h2>
        </div>

        <form action="{{ route('manager.dat-lich-tien-ich.update', $datLichTienIch) }}" method="POST" class="p-5 space-y-5"
              x-data="{ phiTienIch: {@foreach($dsTienIch as $ti){{ $ti->id }}: {{ $ti->phi_su_dung ?? 0 }}, @endforeach} }">
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
                        Mã đặt lịch <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="ma_dat_lich" value="{{ old('ma_dat_lich', $datLichTienIch->ma_dat_lich) }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm font-mono bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('ma_dat_lich') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('ma_dat_lich')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Cư dân <span class="text-red-500">*</span>
                    </label>
                    <select name="cu_dan"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('cu_dan') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Chọn cư dân --</option>
                        @foreach($dsCuDan as $cd)
                        <option value="{{ $cd->id }}" {{ old('cu_dan', $datLichTienIch->cu_dan) == $cd->id ? 'selected' : '' }}>
                            {{ $cd->ho_ten }} @if($cd->email) — {{ $cd->email }} @endif
                        </option>
                        @endforeach
                    </select>
                    @error('cu_dan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Căn hộ</label>
                    <select name="can_ho"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('can_ho') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Không chọn --</option>
                        @foreach($dsCanHo as $ch)
                        <option value="{{ $ch->id }}" {{ old('can_ho', $datLichTienIch->can_ho) == $ch->id ? 'selected' : '' }}>
                            {{ $ch->so_can_ho }} — {{ $ch->toaNha?->ten_toa_nha ?? '?' }}
                        </option>
                        @endforeach
                    </select>
                    @error('can_ho')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Tiện ích <span class="text-red-500">*</span>
                    </label>
                    <select name="tien_ich" x-on:change="$refs.phiSuDung.value = phiTienIch[$event.target.value] ?? 0"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('tien_ich') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Chọn tiện ích --</option>
                        @foreach($dsTienIch as $ti)
                        <option value="{{ $ti->id }}" {{ old('tien_ich', $datLichTienIch->tien_ich) == $ti->id ? 'selected' : '' }}>
                            {{ $ti->ten_tien_ich }} @if($ti->loaiTienIch) ({{ $ti->loaiTienIch->ten_loai_tien_ich }}) @endif
                        </option>
                        @endforeach
                    </select>
                    @error('tien_ich')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Ngày sử dụng <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="ngay_su_dung" value="{{ old('ngay_su_dung', $datLichTienIch->thoi_gian_bat_dau?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('ngay_su_dung') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('ngay_su_dung')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Số người</label>
                    <input type="number" name="so_nguoi" min="1" value="{{ old('so_nguoi', $datLichTienIch->so_nguoi) }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('so_nguoi') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('so_nguoi')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Giờ bắt đầu <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="gio_bat_dau" value="{{ old('gio_bat_dau', $datLichTienIch->thoi_gian_bat_dau?->format('H:i')) }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('gio_bat_dau') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('gio_bat_dau')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Giờ kết thúc <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="gio_ket_thuc" value="{{ old('gio_ket_thuc', $datLichTienIch->thoi_gian_ket_thuc?->format('H:i')) }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('gio_ket_thuc') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('gio_ket_thuc')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Phí sử dụng (VNĐ)</label>
                    <input type="number" step="0.01" min="0" name="phi_su_dung" x-ref="phiSuDung" value="{{ old('phi_su_dung', $datLichTienIch->phi_su_dung) }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('phi_su_dung') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                    @error('phi_su_dung')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Trạng thái <span class="text-red-500">*</span>
                    </label>
                    <select name="trang_thai"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('trang_thai') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        @foreach($dsTrangThai as $id => $label)
                        <option value="{{ $id }}" {{ old('trang_thai', $datLichTienIch->trang_thai) == $id ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('trang_thai')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nhân viên duyệt</label>
                    <select name="nhan_vien_duyet"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('nhan_vien_duyet') ? 'border-red-400 dark:border-red-600' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="">-- Không chọn --</option>
                        @foreach($dsNhanVien as $nv)
                        <option value="{{ $nv->id }}" {{ old('nhan_vien_duyet', $datLichTienIch->nhan_vien_duyet) == $nv->id ? 'selected' : '' }}>{{ $nv->ho_ten }}</option>
                        @endforeach
                    </select>
                    @error('nhan_vien_duyet')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày duyệt</label>
                    <input type="datetime-local" name="ngay_duyet"
                           value="{{ old('ngay_duyet', $datLichTienIch->ngay_duyet?->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @error('ngay_duyet')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ngày hủy</label>
                    <input type="datetime-local" name="ngay_huy"
                           value="{{ old('ngay_huy', $datLichTienIch->ngay_huy?->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @error('ngay_huy')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Ghi chú</label>
                    <textarea name="ghi_chu" rows="2" maxlength="500"
                              x-data x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                              class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none overflow-hidden">{{ old('ghi_chu', $datLichTienIch->ghi_chu) }}</textarea>
                    @error('ghi_chu')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Lý do hủy / từ chối</label>
                    <textarea name="ly_do_huy" rows="2" maxlength="500"
                              x-data x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                              class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none overflow-hidden">{{ old('ly_do_huy', $datLichTienIch->ly_do_huy) }}</textarea>
                    @error('ly_do_huy')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('manager.dat-lich-tien-ich.show', $datLichTienIch) }}"
                   class="px-4 py-2.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Hủy
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
