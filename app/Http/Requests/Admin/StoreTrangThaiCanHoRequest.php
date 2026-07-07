<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrangThaiCanHoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_trang_thai' => [
                'required', 'string', 'max:100',
                Rule::unique('trang_thai_can_ho', 'ten_trang_thai')->whereNull('deletedAt'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_trang_thai.required' => 'Vui lòng nhập tên trạng thái căn hộ.',
            'ten_trang_thai.unique'   => 'Tên trạng thái căn hộ đã tồn tại.',
            'ten_trang_thai.max'      => 'Tên trạng thái căn hộ không được vượt quá 100 ký tự.',
        ];
    }
}
