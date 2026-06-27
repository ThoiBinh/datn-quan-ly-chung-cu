<?php

namespace App\Http\Requests\Manager;

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
            'trang_thai'     => 'required|integer|in:1,2,3,4',
        ];
    }

    public function messages(): array
    {
        return [
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'       => 'Trạng thái không hợp lệ.',
            'han_thanh_toan.date' => 'Hạn thanh toán không đúng định dạng ngày.',
        ];
    }
}
