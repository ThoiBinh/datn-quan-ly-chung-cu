@extends('layouts.resident')
@section('title', 'Chi tiết yêu cầu')

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('resident.yeu-cau.index') }}" class="hover:text-emerald-600 transition-colors">Yêu cầu</a>
        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 font-medium truncate max-w-xs">#{{ $yeuCau->id }}</span>
    </nav>

    {{-- CARD 1 — Nội dung yêu cầu --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 pt-6 pb-4 border-b border-gray-100">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <h1 class="text-lg font-bold text-gray-900 leading-snug">{{ $yeuCau->tieu_de }}</h1>
                    <p class="text-xs text-gray-400 mt-1">
                        Gửi lúc {{ $yeuCau->ngay_gui?->format('H:i — d/m/Y') ?? '—' }}
                    </p>
                </div>
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
        @if($yeuCau->trang_thai == \App\Models\YeuCauCuDan::TRANG_THAI_MOI)
        <div class="px-6 pb-5 flex items-center gap-3 border-t border-gray-100 pt-4">
            <a href="{{ route('resident.yeu-cau.edit', $yeuCau) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Chỉnh sửa
            </a>
            <form method="POST" action="{{ route('resident.yeu-cau.huy', $yeuCau) }}"
                  onsubmit="return confirm('Bạn có chắc muốn hủy yêu cầu này?')">
                @csrf @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50 text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Hủy yêu cầu
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- CARD 2 — Thông tin yêu cầu --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin yêu cầu</h2>
        </div>
        <div class="px-6 py-5 grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <p class="text-xs text-gray-400 mb-0.5">Mã yêu cầu</p>
                <p class="text-sm font-semibold text-gray-700 font-mono">#{{ $yeuCau->id }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <p class="text-xs text-gray-400 mb-0.5">Loại yêu cầu</p>
                <p class="text-sm font-semibold text-gray-700">{{ $yeuCau->loaiYeuCau?->name ?? '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <p class="text-xs text-gray-400 mb-0.5">Ngày gửi</p>
                <p class="text-sm font-semibold text-gray-700">{{ $yeuCau->ngay_gui?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <p class="text-xs text-gray-400 mb-0.5">Ngày hoàn thành</p>
                <p class="text-sm font-semibold text-gray-700">{{ $yeuCau->ngay_hoan_thanh?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <p class="text-xs text-gray-400 mb-0.5">Cập nhật lần cuối</p>
                <p class="text-sm font-semibold text-gray-700">{{ $yeuCau->updatedAt?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- CARD 3 — Thông tin xử lý --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin xử lý</h2>
        </div>
        <div class="px-6 py-5">
            @if($yeuCau->nhanVienXuLy)
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold flex-shrink-0">
                    {{ strtoupper(substr($yeuCau->nhanVienXuLy->ho_ten, 0, 1)) }}
                </div>
                <div class="flex-1 grid grid-cols-2 gap-3">
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
                        <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $yeuCau->nhanVienXuLy->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">SĐT</p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $yeuCau->nhanVienXuLy->sdt ?? '—' }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="flex items-center gap-3 text-gray-400">
                <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-sm text-gray-500">Chưa có nhân viên xử lý. Yêu cầu đang chờ tiếp nhận.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Back --}}
    <div>
        <a href="{{ route('resident.yeu-cau.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Quay lại danh sách
        </a>
    </div>
</div>
@endsection
