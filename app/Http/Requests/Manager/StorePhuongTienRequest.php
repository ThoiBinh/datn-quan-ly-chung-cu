<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class StorePhuongTienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_phuong_tien'  => 'nullable|string|max:150',
            'bien_so'          => 'required|string|max:20|unique:phuong_tien,bien_so',
            'loai_phuong_tien' => 'required|integer|exists:loai_phuong_tien,id',
            'can_ho'           => 'required|integer|exists:can_ho,id',
            'ngay_dang_ky'     => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'bien_so.required'          => 'Vui lòng nhập biển số xe.',
            'bien_so.unique'            => 'Biển số xe đã tồn tại.',
            'bien_so.max'               => 'Biển số xe không quá 20 ký tự.',
            'loai_phuong_tien.required' => 'Vui lòng chọn loại phương tiện.',
            'loai_phuong_tien.exists'   => 'Loại phương tiện không tồn tại.',
            'can_ho.required'           => 'Vui lòng chọn căn hộ.',
            'can_ho.exists'             => 'Căn hộ không tồn tại.',
            'ngay_dang_ky.date'         => 'Ngày đăng ký không hợp lệ.',
        ];
    }
}
