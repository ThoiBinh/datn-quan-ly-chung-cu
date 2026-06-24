@extends('layouts.manager')
@section('title', 'Chi tiết yêu cầu #' . $yeuCau->id)

@section('content')
<div class="max-w-4xl space-y-5">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('manager.yeu-cau.index') }}" class="hover:text-indigo-600 transition-colors">Yêu cầu cư dân</a>
        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 font-medium">#{{ $yeuCau->id }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Left: main content --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- CARD 1 — Nội dung yêu cầu --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-start justify-between gap-3">
                        <h1 class="text-lg font-bold text-gray-900 leading-snug">{{ $yeuCau->tieu_de }}</h1>
                        <div class="flex flex-col gap-1.5 items-end flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $yeuCau->trang_thai_label['class'] }}">
                                {{ $yeuCau->trang_thai_label['text'] }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $yeuCau->muc_do_label['class'] }}">
                                {{ $yeuCau->muc_do_label['text'] }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $yeuCau->noi_dung }}</p>
                </div>
            </div>

            {{-- CARD 2 — Thông tin cư dân --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin cư dân</h2>
                </div>
                <div class="px-6 py-5">
                    @if($yeuCau->cuDan)
                    @php
                        $cuDan  = $yeuCau->cuDan;
                        $canHo  = $cuDan->canHoHienTai?->canHo;
                        $toaNha = $canHo?->toaNha;
                    @endphp
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-0.5">Họ tên</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $cuDan->ho_ten }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-0.5">CCCD</p>
                            <p class="text-sm font-semibold text-gray-700 font-mono">{{ $cuDan->cccd ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-0.5">Email</p>
                            <p class="text-sm font-medium text-gray-700">{{ $cuDan->email ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-0.5">SĐT</p>
                            <p class="text-sm font-medium text-gray-700">{{ $cuDan->sdt ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-0.5">Căn hộ</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $canHo?->so_can_ho ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-0.5">Tòa nhà</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $toaNha?->ten_toa_nha ?? '—' }}</p>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-400">Không tìm thấy thông tin cư dân.</p>
                    @endif
                </div>
            </div>

            {{-- CARD 3 — NV xử lý --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Nhân viên xử lý hiện tại</h2>
                </div>
                <div class="px-6 py-5">
                    @if($yeuCau->nhanVienXuLy)
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold flex-shrink-0">
                            {{ strtoupper(substr($yeuCau->nhanVienXuLy->ho_ten, 0, 1)) }}
                        </div>
                        <div class="grid grid-cols-2 gap-3 flex-1">
                            <div>
                                <p class="text-xs text-gray-400">Họ tên</p>
                                <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $yeuCau->nhanVienXuLy->ho_ten }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Chức vụ</p>
                                <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $yeuCau->nhanVienXuLy->chucVu?->chuc_vu ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Email</p>
                                <p class="text-sm text-gray-700 mt-0.5">{{ $yeuCau->nhanVienXuLy->email ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">SĐT</p>
                                <p class="text-sm text-gray-700 mt-0.5">{{ $yeuCau->nhanVienXuLy->sdt ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-400">Chưa phân công nhân viên xử lý.</p>
                    @endif
                </div>
            </div>

            {{-- CARD 4 — Phương tiện đăng ký (nếu có) --}}
            @if($phuongTienLienQuan)
            <div class="bg-white rounded-2xl border border-amber-200 shadow-sm">
                <div class="px-6 py-4 border-b border-amber-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-amber-700 uppercase tracking-wide">Phương tiện đăng ký</h2>
                    @php
                        $ptTrangThai = $phuongTienLienQuan->trang_thai
                            ? ['text' => 'Đang hoạt động', 'class' => 'bg-green-100 text-green-700']
                            : ['text' => 'Đã hủy', 'class' => 'bg-red-100 text-red-700'];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ptTrangThai['class'] }}">
                        {{ $ptTrangThai['text'] }}
                    </span>
                </div>
                <div class="px-6 py-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-amber-50 rounded-xl p-3 border border-amber-100">
                            <p class="text-xs text-gray-400 mb-0.5">Biển số</p>
                            <p class="text-sm font-bold text-gray-800 font-mono">{{ $phuongTienLienQuan->bien_so }}</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-3 border border-amber-100">
                            <p class="text-xs text-gray-400 mb-0.5">Loại</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $phuongTienLienQuan->loaiPhuongTien?->ten_loai_phuong_tien ?? '—' }}</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-3 border border-amber-100">
                            <p class="text-xs text-gray-400 mb-0.5">Tên phương tiện</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $phuongTienLienQuan->ten_phuong_tien ?: '—' }}</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-3 border border-amber-100">
                            <p class="text-xs text-gray-400 mb-0.5">Ngày đăng ký</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $phuongTienLienQuan->ngay_dang_ky?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right: metadata + update form --}}
        <div class="space-y-5">

            {{-- Metadata --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin</h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div>
                        <p class="text-xs text-gray-400">Mã yêu cầu</p>
                        <p class="text-sm font-semibold text-gray-700 font-mono mt-0.5">#{{ $yeuCau->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Loại yêu cầu</p>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $yeuCau->loaiYeuCau?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Ngày gửi</p>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $yeuCau->ngay_gui?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Ngày hoàn thành</p>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $yeuCau->ngay_hoan_thanh?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Cập nhật lần cuối</p>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $yeuCau->updatedAt?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Update form --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Cập nhật</h2>
                </div>
                <form method="POST" action="{{ route('manager.yeu-cau.update', $yeuCau) }}" class="px-5 py-4 space-y-4">
                    @csrf @method('PUT')

                    @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 text-xs rounded-lg px-3 py-2">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-3 py-2">
                        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                    </div>
                    @endif

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Trạng thái</label>
                        <select name="trang_thai"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            @foreach($dsTrangThai as $val => $label)
                            <option value="{{ $val }}" {{ $yeuCau->trang_thai == $val ? 'selected' : '' }}>
                                {{ $label['text'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Phân công nhân viên</label>
                        <select name="nhan_vien_xu_ly"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">— Không phân công —</option>
                            @foreach($dsNhanVien as $nv)
                            <option value="{{ $nv->id }}"
                                    {{ $yeuCau->nhan_vien_xu_ly == $nv->id ? 'selected' : '' }}>
                                {{ $nv->ho_ten }}
                                @if($nv->chucVu) ({{ $nv->chucVu->chuc_vu }})@endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        Lưu cập nhật
                    </button>
                </form>
            </div>

            <a href="{{ route('manager.yeu-cau.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại danh sách
            </a>
        </div>
    </div>
</div>
@endsection
