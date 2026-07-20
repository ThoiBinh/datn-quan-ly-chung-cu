<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoaiTienIchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_loai_tien_ich' => [
                'required', 'string', 'max:100',
                Rule::unique('loai_tien_ich', 'ten_loai_tien_ich')->whereNull('deletedAt'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_loai_tien_ich.required' => 'Vui lòng nhập tên loại tiện ích.',
            'ten_loai_tien_ich.max'      => 'Tên loại tiện ích không được vượt quá 100 ký tự.',
            'ten_loai_tien_ich.unique'   => 'Tên loại tiện ích đã tồn tại.',
        ];
    }
}
