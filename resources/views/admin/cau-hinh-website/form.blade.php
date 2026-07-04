@php
    $isEdit = isset($cauHinhWebsite);
@endphp
<div x-data="{
        kieuDuLieu: '{{ old('kieu_du_lieu', $cauHinhWebsite->kieu_du_lieu ?? 'text') }}',
        giaTriBoolean: {{ old('gia_tri', $cauHinhWebsite->gia_tri ?? '0') == '1' ? 'true' : 'false' }},
        trangThai: {{ old('trang_thai', $isEdit ? ($cauHinhWebsite->trang_thai ? '1' : '0') : '1') == '1' ? 'true' : 'false' }},
        laBaoMat: {{ old('la_bao_mat', $isEdit ? ($cauHinhWebsite->la_bao_mat ? '1' : '0') : '0') == '1' ? 'true' : 'false' }},
        duocChinhSua: {{ old('duoc_chinh_sua', $isEdit ? ($cauHinhWebsite->duoc_chinh_sua ? '1' : '0') : '1') == '1' ? 'true' : 'false' }},
        inputType() {
            return { email: 'email', url: 'url', number: 'number', phone: 'tel', password: 'password' }[this.kieuDuLieu] ?? 'text';
        }
    }"
    class="space-y-5">
    @csrf
    @if($isEdit) @method('PUT') @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Mã thuộc tính <span class="text-red-500">*</span></label>
            <input type="text" name="ma_thuoc_tinh" value="{{ old('ma_thuoc_tinh', $cauHinhWebsite->ma_thuoc_tinh ?? '') }}"
                   maxlength="100" placeholder="VD: ten_chung_cu"
                   readonly
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none"/>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tên thuộc tính <span class="text-red-500">*</span></label>
            <input type="text" name="ten_thuoc_tinh" value="{{ old('ten_thuoc_tinh', $cauHinhWebsite->ten_thuoc_tinh ?? '') }}"
                   maxlength="255" placeholder="VD: Tên chung cư"
                   readonly
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none"/>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nhóm <span class="text-red-500">*</span></label>
            <select name="ma_nhom" disabled class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none">
                <option value="">-- Chọn nhóm --</option>
                @foreach(\App\Models\CauHinhWebsite::NHOM_OPTIONS as $maNhom => $tenNhom)
                <option value="{{ $maNhom }}" {{ old('ma_nhom', $cauHinhWebsite->ma_nhom ?? '') === $maNhom ? 'selected' : '' }}>{{ $tenNhom }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kiểu dữ liệu <span class="text-red-500">*</span></label>
            <select name="kieu_du_lieu" x-model="kieuDuLieu" disabled class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none">
                @foreach(\App\Models\CauHinhWebsite::KIEU_DU_LIEU_OPTIONS as $k)
                <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Giá trị</label>

        <textarea x-show="kieuDuLieu === 'textarea' || kieuDuLieu === 'json'" name="gia_tri" rows="4"
                  placeholder="{{ old('placeholder', $cauHinhWebsite->placeholder ?? '') }}"
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('gia_tri', $cauHinhWebsite->gia_tri ?? '') }}</textarea>

        <div x-show="kieuDuLieu === 'boolean'" class="flex items-center gap-3">
            <input type="hidden" name="gia_tri" :value="giaTriBoolean ? '1' : '0'">
            <button type="button" @click="giaTriBoolean = !giaTriBoolean"
                    :class="giaTriBoolean ? 'bg-blue-600' : 'bg-gray-300'"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="giaTriBoolean ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
            </button>
            <span class="text-sm text-gray-500" x-text="giaTriBoolean ? 'Bật (1)' : 'Tắt (0)'"></span>
        </div>

        <div x-show="['image','file'].includes(kieuDuLieu)" class="space-y-2">
            @if($isEdit && $cauHinhWebsite->gia_tri)
                @if($cauHinhWebsite->gia_tri_url)
                <img src="{{ $cauHinhWebsite->gia_tri_url }}" alt="" class="h-16 w-16 rounded-lg object-cover border border-gray-200">
                @endif
                <p class="text-xs text-gray-500">File hiện tại: <span class="font-mono">{{ $cauHinhWebsite->gia_tri }}</span></p>
            @endif
            <input type="file" name="gia_tri_file"
                   accept="{{ '' }}"
                   class="w-full text-sm border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            <p class="text-xs text-gray-400">Để trống nếu không muốn thay đổi file hiện tại.</p>
        </div>

        <input x-show="!['textarea','json','boolean','image','file'].includes(kieuDuLieu)"
               :type="inputType()" name="gia_tri" value="{{ old('gia_tri', $cauHinhWebsite->gia_tri ?? '') }}"
               placeholder="{{ old('placeholder', $cauHinhWebsite->placeholder ?? '') }}"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mô tả</label>
        <textarea name="mo_ta" rows="2" maxlength="500"
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('mo_ta', $cauHinhWebsite->mo_ta ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Placeholder</label>
            <input type="text" name="placeholder" value="{{ old('placeholder', $cauHinhWebsite->placeholder ?? '') }}"
                   maxlength="255"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Thứ tự hiển thị</label>
            <input type="number" name="thu_tu" min="0" value="{{ old('thu_tu', $cauHinhWebsite->thu_tu ?? 0) }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
        <div class="flex items-center gap-3">
            <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
            <input type="hidden" name="trang_thai" :value="trangThai ? '1' : '0'">
            <button type="button" @click="trangThai = !trangThai"
                    :class="trangThai ? 'bg-emerald-600' : 'bg-gray-300'"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="trangThai ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
            </button>
            <span class="text-sm text-gray-500" x-text="trangThai ? 'Hoạt động' : 'Vô hiệu hóa'"></span>
        </div>

        <div class="flex items-center gap-3">
            <label class="block text-sm font-medium text-gray-700">Bảo mật</label>
            <input type="hidden" name="la_bao_mat" :value="laBaoMat ? '1' : '0'">
            <button type="button" @click="laBaoMat = !laBaoMat"
                    :class="laBaoMat ? 'bg-amber-500' : 'bg-gray-300'"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="laBaoMat ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
            </button>
            <span class="text-sm text-gray-500" x-text="laBaoMat ? 'Có' : 'Không'"></span>
        </div>

        <div class="flex items-center gap-3">
            <label class="block text-sm font-medium text-gray-700">Được chỉnh sửa</label>
            <input type="hidden" name="duoc_chinh_sua" :value="duocChinhSua ? '1' : '0'">
            <button type="button" @click="duocChinhSua = !duocChinhSua"
                    :class="duocChinhSua ? 'bg-emerald-600' : 'bg-gray-300'"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="duocChinhSua ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
            </button>
            <span class="text-sm text-gray-500" x-text="duocChinhSua ? 'Có' : 'Không'"></span>
        </div>
    </div>

    <div class="flex gap-3 pt-2">
        <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
            {{ $isEdit ? 'Cập nhật cấu hình' : 'Lưu cấu hình' }}
        </button>
        <a href="{{ route('admin.cau-hinh-website.index') }}" class="flex-1 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 text-center transition-colors">Hủy</a>
    </div>
</div>
