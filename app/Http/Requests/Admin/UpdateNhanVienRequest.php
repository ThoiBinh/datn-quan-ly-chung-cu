<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesCccd;
use App\Rules\SoDienThoaiVietNam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNhanVienRequest extends FormRequest
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
        $id = $this->route('nhanVien')?->id;

        return [
            'ho_ten'        => 'required|string|max:150',
            'chuc_vu'       => 'required|integer|exists:chuc_vu,id',
            'sdt'           => ['nullable', 'string', new SoDienThoaiVietNam()],
            'email'         => [
                'required', 'email', 'max:150',
                Rule::unique('nhan_vien', 'email')->ignore($id),
            ],
            'mat_khau'      => 'nullable|string|min:8|confirmed',
            'trang_thai'    => 'required|in:0,1',
            'ma_nhan_vien'  => [
                'nullable', 'string', 'max:50',
                Rule::unique('nhan_vien', 'ma_nhan_vien')->ignore($id),
            ],
            'cccd'          => $this->cccdRules(true, 'nhan_vien', $id),
            'ngay_sinh'     => 'nullable|date',
            'ngay_vao_lam'  => 'nullable|date',
            'ngay_nghi_lam' => 'nullable|date',
            'ghi_chu'       => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return array_merge($this->cccdMessages(), [
            'ho_ten.required'     => 'Vui lòng nhập họ tên.',
            'chuc_vu.required'    => 'Vui lòng chọn chức vụ.',
            'chuc_vu.exists'      => 'Chức vụ không tồn tại.',
            'email.required'      => 'Vui lòng nhập email.',
            'email.email'         => 'Email không đúng định dạng.',
            'email.unique'        => 'Email đã được sử dụng.',
            'mat_khau.min'        => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed'  => 'Xác nhận mật khẩu không khớp.',
            'ma_nhan_vien.unique' => 'Mã nhân viên đã tồn tại.',
        ]);
    }
}
