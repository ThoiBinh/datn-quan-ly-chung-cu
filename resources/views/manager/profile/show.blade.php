@extends('layouts.manager')
@section('title', 'Hồ sơ cá nhân')
@section('page-title', 'Hồ sơ cá nhân')

@section('content')

{{-- Header Card --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-5">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <div class="w-20 h-20 rounded-2xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
            <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-300">
                {{ strtoupper(mb_substr($nhanVien->ho_ten, 0, 1)) }}
            </span>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $nhanVien->ho_ten }}</h1>
                @if($nhanVien->trang_thai == 1)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Đang làm việc
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Đã nghỉ
                </span>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $nhanVien->ma_nhan_vien }}
                @if($nhanVien->chucVu)
                · <span class="text-indigo-600 dark:text-indigo-400">{{ $nhanVien->chucVu->chuc_vu }}</span>
                @endif
                · {{ $nhanVien->email }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- CARD 1: Thông tin cá nhân (chỉ xem) --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200">Thông tin cá nhân</h3>
            </div>
            <div class="p-4 space-y-3">
                @php
                    $infoRows = [
                        ['label' => 'Mã nhân viên',  'value' => $nhanVien->ma_nhan_vien],
                        ['label' => 'Họ tên',         'value' => $nhanVien->ho_ten],
                        ['label' => 'Email',          'value' => $nhanVien->email],
                        ['label' => 'Chức vụ',        'value' => $nhanVien->chucVu?->chuc_vu ?? '—'],
                        ['label' => 'CCCD',           'value' => $nhanVien->cccd],
                        ['label' => 'Ngày sinh',      'value' => $nhanVien->ngay_sinh?->format('d/m/Y') ?? '—'],
                        ['label' => 'Ngày vào làm',  'value' => $nhanVien->ngay_vao_lam?->format('d/m/Y') ?? '—'],
                        ['label' => 'Ngày nghỉ làm', 'value' => $nhanVien->ngay_nghi_lam?->format('d/m/Y') ?? '—'],
                        ['label' => 'Ngày tạo',       'value' => $nhanVien->created_at?->format('d/m/Y H:i') ?? '—'],
                        ['label' => 'Cập nhật lần cuối', 'value' => isset($nhanVien->attributes['updatedAt']) ? \Carbon\Carbon::parse($nhanVien->attributes['updatedAt'])->format('d/m/Y H:i') : '—'],
                    ];
                @endphp
                @foreach($infoRows as $row)
                <div class="flex justify-between gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $row['label'] }}</span>
                    <span class="text-xs font-medium text-gray-800 dark:text-gray-200 text-right">{{ $row['value'] }}</span>
                </div>
                @endforeach
                <div class="flex justify-between gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Trạng thái</span>
                    @if($nhanVien->trang_thai == 1)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">Hoạt động</span>
                    @else
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">Đã nghỉ</span>
                    @endif
                </div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 pt-2 border-t border-gray-100 dark:border-gray-700">
                    Các thông tin trên chỉ có thể xem, không thể chỉnh sửa tại đây.
                </p>
            </div>
        </div>
    </div>

    {{-- Cột phải: 2 form chỉnh sửa --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- CARD 2: Cập nhật thông tin liên hệ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Cập nhật thông tin liên hệ</h2>
            </div>
            <form method="POST" action="{{ route('manager.profile.update') }}" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div class="max-w-sm">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Số điện thoại <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="sdt" value="{{ old('sdt', $nhanVien->sdt) }}" required
                           placeholder="09xxxxxxxx"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('sdt') border-red-400 @enderror">
                    @error('sdt')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>

        {{-- CARD 3: Đổi mật khẩu --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Đổi mật khẩu</h2>
            </div>
            <form method="POST" action="{{ route('manager.profile.password') }}" class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                @method('PUT')

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Mật khẩu hiện tại <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('current_password') border-red-400 @enderror">
                    @error('current_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Mật khẩu mới <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="mat_khau" minlength="8" required
                           placeholder="Tối thiểu 8 ký tự"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('mat_khau') border-red-400 @enderror">
                    @error('mat_khau')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Xác nhận mật khẩu mới <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="mat_khau_confirmation" minlength="8" required
                           placeholder="Nhập lại mật khẩu mới"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="sm:col-span-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Đổi mật khẩu
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
