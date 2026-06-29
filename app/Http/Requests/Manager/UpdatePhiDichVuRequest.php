<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhiDichVuRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'ten_phi_dich_vu'  => 'required|string|max:150',
            'don_gia'          => 'required|numeric|min:0|max:999999999999999',
            'loai_phi_dich_vu' => 'required|integer|exists:loai_phi_dich_vu,id',
            'don_vi_tinh'      => 'required|integer|exists:don_vi_tinh_phi_dich_vu,id',
            'loai_tinh_phi'    => 'required|integer|exists:loai_tinh_phi_dich_vu,id',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_phi_dich_vu.required'  => 'Vui lòng nhập tên phí dịch vụ.',
            'ten_phi_dich_vu.max'       => 'Tên phí dịch vụ không được vượt quá 150 ký tự.',
            'don_gia.required'          => 'Vui lòng nhập đơn giá.',
            'don_gia.numeric'           => 'Đơn giá phải là số.',
            'don_gia.min'               => 'Đơn giá không được âm.',
            'loai_phi_dich_vu.required' => 'Vui lòng chọn loại phí dịch vụ.',
            'loai_phi_dich_vu.exists'   => 'Loại phí dịch vụ không tồn tại.',
            'don_vi_tinh.required'      => 'Vui lòng chọn đơn vị tính.',
            'don_vi_tinh.exists'        => 'Đơn vị tính không tồn tại.',
            'loai_tinh_phi.required'    => 'Vui lòng chọn loại tính phí.',
            'loai_tinh_phi.exists'      => 'Loại tính phí không tồn tại.',
        ];
    }
}
