<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreHoaDonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'can_ho'       => 'required|integer|exists:can_ho,id',
            'thang'        => 'required|integer|min:1|max:12',
            'nam'          => 'required|integer|min:2020|max:' . (date('Y') + 2),
            'chi_so'       => 'nullable|array',
            'chi_so.*'     => 'array',
            'chi_so.*.cu'  => 'nullable|integer|min:0',
            'chi_so.*.moi' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'can_ho.required'   => 'Vui lòng chọn căn hộ.',
            'can_ho.exists'     => 'Căn hộ không tồn tại.',
            'thang.required'    => 'Vui lòng chọn tháng.',
            'thang.min'         => 'Tháng phải từ 1 đến 12.',
            'thang.max'         => 'Tháng phải từ 1 đến 12.',
            'nam.required'      => 'Vui lòng nhập năm.',
            'nam.min'           => 'Năm không hợp lệ (tối thiểu 2020).',
            'chi_so.*.cu.min'   => 'Chỉ số cũ không được âm.',
            'chi_so.*.moi.min'  => 'Chỉ số mới không được âm.',
        ];
    }
}
