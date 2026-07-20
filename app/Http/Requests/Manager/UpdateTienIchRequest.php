<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTienIchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_tien_ich' => [
                'required', 'string', 'max:255',
                Rule::unique('tien_ich', 'ten_tien_ich')
                    ->where(fn ($q) => $q->where('toa_nha', $this->input('toa_nha')))
                    ->whereNull('deletedAt')
                    ->ignore($this->route('tienIch')),
            ],
            'loai_tien_ich'  => 'required|integer|exists:loai_tien_ich,id',
            'toa_nha'        => 'nullable|integer|exists:toa_nha,id',
            'mo_ta'          => 'nullable|string',
            'vi_tri'         => 'nullable|string|max:255',
            'suc_chua'       => 'nullable|integer|min:1',
            'gio_mo_cua'     => 'nullable|date_format:H:i',
            'gio_dong_cua'   => 'nullable|date_format:H:i|after:gio_mo_cua',
            'phi_su_dung'    => 'required|numeric|min:0|max:999999999999999',
            'can_dat_truoc'  => 'nullable|boolean',
            'hinh_anh'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'xoa_hinh'       => 'nullable|boolean',
            'trang_thai'     => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_tien_ich.required'   => 'Vui lòng nhập tên tiện ích.',
            'ten_tien_ich.max'        => 'Tên tiện ích không được vượt quá 255 ký tự.',
            'ten_tien_ich.unique'     => 'Tòa nhà này đã có tiện ích trùng tên.',
            'loai_tien_ich.required'  => 'Vui lòng chọn loại tiện ích.',
            'loai_tien_ich.exists'    => 'Loại tiện ích không tồn tại.',
            'toa_nha.exists'          => 'Tòa nhà không tồn tại.',
            'vi_tri.max'              => 'Vị trí không được vượt quá 255 ký tự.',
            'suc_chua.integer'        => 'Sức chứa phải là số nguyên.',
            'suc_chua.min'            => 'Sức chứa phải lớn hơn 0.',
            'gio_mo_cua.date_format'  => 'Giờ mở cửa không hợp lệ (định dạng HH:mm).',
            'gio_dong_cua.date_format' => 'Giờ đóng cửa không hợp lệ (định dạng HH:mm).',
            'gio_dong_cua.after'      => 'Giờ đóng cửa phải sau giờ mở cửa.',
            'phi_su_dung.required'    => 'Vui lòng nhập giá sử dụng.',
            'phi_su_dung.numeric'     => 'Giá sử dụng phải là số.',
            'phi_su_dung.min'         => 'Giá sử dụng không được âm.',
            'hinh_anh.image'          => 'Tệp tải lên phải là hình ảnh.',
            'hinh_anh.mimes'          => 'Ảnh phải có định dạng jpeg, png, jpg hoặc webp.',
            'hinh_anh.max'            => 'Dung lượng ảnh không được vượt quá 2MB.',
            'trang_thai.required'     => 'Vui lòng chọn trạng thái.',
        ];
    }
}
