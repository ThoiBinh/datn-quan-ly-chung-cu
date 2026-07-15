<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesCccd;
use App\Rules\SoDienThoaiVietNam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    use ValidatesCccd;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->prepareCccdForValidation();
    }

    public function rules(): array
    {
        if ($this->input('type') === 'cu_dan') {
            return [
                'ho_ten_dem' => 'required|string|max:255',
                'ten'        => 'required|string|max:100',
                'sdt'        => ['nullable', 'string', new SoDienThoaiVietNam()],
                'cccd'       => $this->cccdRules(true, 'cu_dan'),
                'email'      => 'nullable|email|unique:cu_dan,email',
                'mat_khau'   => 'required|min:8|confirmed',
                'ngay_sinh'  => 'nullable|date',
                'gioi_tinh'  => 'nullable|in:0,1',
                'tinh'       => 'nullable|string|max:100',
                'xa'         => 'nullable|string|max:100',
                'dia_chi'    => 'nullable|string|max:255',
                'trang_thai' => 'required|in:0,1',
            ];
        }

        return [
            'ho_ten'        => 'required|string|max:255',
            'chuc_vu'       => 'required|exists:chuc_vu,id',
            'sdt'           => ['nullable', 'string', new SoDienThoaiVietNam()],
            'email'         => [
                'required', 'email',
                Rule::unique('nhan_vien', 'email')->whereNull('deletedAt'),
            ],
            'mat_khau'      => 'required|min:8|confirmed',
            'ma_nhan_vien'  => 'nullable|string|max:50',
            'cccd'          => $this->cccdRules(true, 'nhan_vien'),
            'ngay_sinh'     => 'nullable|date',
            'ngay_vao_lam'  => 'nullable|date',
            'ngay_nghi_lam' => 'nullable|date',
            'ghi_chu'       => 'nullable|string|max:1000',
            'trang_thai'    => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return array_merge($this->cccdMessages(), [
            'ho_ten.required'      => 'Vui lòng nhập họ tên.',
            'ho_ten_dem.required'  => 'Vui lòng nhập họ tên đệm.',
            'ten.required'         => 'Vui lòng nhập tên.',
            'chuc_vu.required'     => 'Vui lòng chọn chức vụ.',
            'email.required'       => 'Vui lòng nhập email.',
            'email.unique'         => 'Email đã tồn tại trong hệ thống.',
            'mat_khau.required'    => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min'         => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed'   => 'Xác nhận mật khẩu không khớp.',
        ]);
    }
}
