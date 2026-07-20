<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateToaNhaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tien_to' => strtoupper(trim((string) $this->tien_to)),
        ]);
    }

    public function rules(): array
    {
        return [
            'ten_toa_nha' => 'required|string|max:255',
            'tien_to'     => [
                'required', 'string', 'max:10',
                Rule::unique('toa_nha', 'tien_to')
                    ->ignore($this->route('toa_nha')),
            ],
            'dia_chi'     => 'required|string|max:255',
            'so_tang'     => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_toa_nha.required' => 'Vui lòng nhập tên tòa nhà.',
            'ten_toa_nha.max'      => 'Tên tòa nhà không được vượt quá 255 ký tự.',
            'tien_to.required'     => 'Vui lòng nhập tiền tố.',
            'tien_to.max'          => 'Tiền tố không được vượt quá 10 ký tự.',
            'tien_to.unique'       => 'Tiền tố này đã được sử dụng.',
            'dia_chi.required'     => 'Vui lòng nhập địa chỉ.',
            'dia_chi.max'          => 'Địa chỉ không được vượt quá 255 ký tự.',
            'so_tang.required'     => 'Vui lòng nhập số tầng.',
            'so_tang.integer'      => 'Số tầng phải là số nguyên.',
            'so_tang.min'          => 'Số tầng phải lớn hơn 0.',
        ];
    }
}
