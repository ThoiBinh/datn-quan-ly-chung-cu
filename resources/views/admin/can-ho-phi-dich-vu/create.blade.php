@extends('layouts.admin')
@section('title', 'Thêm dịch vụ căn hộ')
@section('page-title', 'Thêm dịch vụ căn hộ')

@section('content')
<div class="max-w-2xl" x-data="formCanHoPhiDV()">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700">Thông tin dịch vụ căn hộ</h3>
        </div>

        <form action="{{ route('admin.can-ho-phi-dich-vu.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <!-- Chọn căn hộ -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Căn hộ <span class="text-red-500">*</span>
                </label>
                <select name="can_ho" x-model="canHoId" @change="onCanHoChange()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('can_ho') ? 'border-red-400' : '' }}">
                    <option value="">-- Chọn căn hộ --</option>
                    @foreach($dsCanHo as $ch)
                    <option value="{{ $ch->id }}" {{ old('can_ho') == $ch->id ? 'selected' : '' }}>
                        {{ $ch->so_can_ho }} — {{ $ch->toaNha?->ten_toa_nha ?? '?' }}, Tầng {{ $ch->tang }}
                    </option>
                    @endforeach
                </select>
                @error('can_ho')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Chọn dịch vụ -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Dịch vụ <span class="text-red-500">*</span>
                </label>
                <select name="phi_dich_vu" x-model="phiDVId" @change="onPhiDVChange()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('phi_dich_vu') ? 'border-red-400' : '' }}">
                    <option value="">-- Chọn dịch vụ --</option>
                    @foreach($dsPhiDV as $dv)
                    <option value="{{ $dv->id }}"
                            data-don-gia="{{ $dv->don_gia }}"
                            data-don-vi="{{ $dv->donViTinh?->don_vi }}"
                            data-loai-tinh="{{ $dv->loaiTinhPhi?->ten_loai }}"
                            {{ old('phi_dich_vu') == $dv->id ? 'selected' : '' }}>
                        {{ $dv->ten_phi_dich_vu }}
                        @if($dv->loaiPhiDichVu) ({{ $dv->loaiPhiDichVu->ten_loai_phi_dich_vu }}) @endif
                    </option>
                    @endforeach
                </select>
                @error('phi_dich_vu')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Thông tin dịch vụ gốc (hiển thị khi chọn) -->
            <div x-show="selectedDV.donGia !== null" x-transition
                 class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Thông tin dịch vụ gốc</p>
                <div class="grid grid-cols-3 gap-3 text-sm">
                    <div>
                        <span class="text-xs text-blue-400">Đơn giá gốc</span>
                        <p class="font-semibold text-blue-700" x-text="selectedDV.donGiaFormatted"></p>
                    </div>
                    <div>
                        <span class="text-xs text-blue-400">Đơn vị</span>
                        <p class="font-semibold text-blue-700" x-text="selectedDV.donVi || '—'"></p>
                    </div>
                    <div>
                        <span class="text-xs text-blue-400">Loại tính phí</span>
                        <p class="font-semibold text-blue-700" x-text="selectedDV.loaiTinh || '—'"></p>
                    </div>
                </div>
            </div>

            <!-- Đơn giá riêng -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Đơn giá riêng
                    <span class="text-xs text-gray-400 font-normal ml-1">(để trống = dùng đơn giá dịch vụ gốc)</span>
                </label>
                <div class="relative">
                    <input type="number" name="don_gia" value="{{ old('don_gia') }}" min="0" step="1000"
                           placeholder="Nhập đơn giá riêng nếu có..."
                           class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('don_gia') ? 'border-red-400' : '' }}"/>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">đ</span>
                </div>
                @error('don_gia')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Lưu dịch vụ căn hộ
                </button>
                <a href="{{ route('admin.can-ho-phi-dich-vu.index') }}"
                   class="flex-1 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 text-center transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function formCanHoPhiDV() {
    return {
        canHoId: '{{ old('can_ho') ?? '' }}',
        phiDVId: '{{ old('phi_dich_vu') ?? '' }}',
        selectedDV: { donGia: null, donGiaFormatted: '', donVi: '', loaiTinh: '' },

        onCanHoChange() {},
        onPhiDVChange() {
            const sel = document.querySelector(`select[name=phi_dich_vu] option[value="${this.phiDVId}"]`);
            if (!sel || !this.phiDVId) {
                this.selectedDV = { donGia: null, donGiaFormatted: '', donVi: '', loaiTinh: '' };
                return;
            }
            const gia = sel.dataset.donGia;
            this.selectedDV = {
                donGia: gia !== '' ? gia : null,
                donGiaFormatted: gia ? Number(gia).toLocaleString('vi-VN') + ' đ' : 'Chưa có',
                donVi: sel.dataset.donVi || '',
                loaiTinh: sel.dataset.loaiTinh || '',
            };
        },

        init() {
            if (this.phiDVId) this.onPhiDVChange();
        }
    };
}
</script>
@endpush
@endsection
