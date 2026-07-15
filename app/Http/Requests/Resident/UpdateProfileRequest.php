<?php

namespace App\Http\Requests\Resident;

use App\Rules\SoDienThoaiVietNam;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = auth('cudan')->id();

        return [
            'ho_ten_dem' => 'nullable|string|max:150',
            'ten'        => 'required|string|max:50',
            'email'      => 'nullable|email|max:150|unique:cu_dan,email,' . $id,
            'sdt'        => ['nullable', 'string', new SoDienThoaiVietNam()],
            'ngay_sinh'  => 'nullable|date',
            'gioi_tinh'  => 'nullable|integer|in:0,1,2',
            'tinh'       => 'nullable|string|max:100',
            'xa'         => 'nullable|string|max:100',
            'dia_chi'    => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'ten.required'   => 'Vui lòng nhập tên.',
            'ten.max'        => 'Tên không được vượt quá 50 ký tự.',
            'email.email'    => 'Địa chỉ email không hợp lệ.',
            'email.unique'   => 'Email này đã được sử dụng.',
            'ngay_sinh.date' => 'Ngày sinh không hợp lệ.',
            'gioi_tinh.in'   => 'Giới tính không hợp lệ.',
        ];
    }
}
