<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThanhToanHoaDonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'so_tien'                => ['required', 'numeric', 'min:1'],
            'ngay_thanh_toan'        => ['required', 'date', 'before_or_equal:today'],
            'phuong_thuc_thanh_toan' => ['required', 'string', 'max:100'],
            'nguoi_thanh_toan'       => ['nullable', 'integer', 'exists:cu_dan,id'],
            'ghi_chu'                => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'so_tien.required'                => 'Vui lòng nhập số tiền thanh toán.',
            'so_tien.numeric'                 => 'Số tiền phải là số.',
            'so_tien.min'                     => 'Số tiền thanh toán phải lớn hơn 0.',
            'ngay_thanh_toan.required'        => 'Vui lòng chọn ngày thanh toán.',
            'ngay_thanh_toan.date'            => 'Ngày thanh toán không hợp lệ.',
            'ngay_thanh_toan.before_or_equal' => 'Ngày thanh toán không được là ngày trong tương lai.',
            'phuong_thuc_thanh_toan.required' => 'Vui lòng chọn phương thức thanh toán.',
            'phuong_thuc_thanh_toan.max'      => 'Phương thức thanh toán không được quá 100 ký tự.',
            'ghi_chu.max'                     => 'Ghi chú không được quá 500 ký tự.',
        ];
    }
}
