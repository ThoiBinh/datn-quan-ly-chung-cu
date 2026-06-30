<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHoaDonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'han_thanh_toan' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'han_thanh_toan.date' => 'Hạn thanh toán không đúng định dạng ngày.',
        ];
    }
}
