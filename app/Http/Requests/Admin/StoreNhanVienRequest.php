<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreNhanVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ho_ten'        => 'required|string|max:150',
            'chuc_vu'       => 'required|integer|exists:chuc_vu,id',
            'sdt'           => 'nullable|string|max:15',
            'email'         => 'required|email|max:150|unique:nhan_vien,email',
            'mat_khau'      => 'required|string|min:8|confirmed',
            'trang_thai'    => 'required|in:0,1',
            'ma_nhan_vien'  => 'nullable|string|max:50|unique:nhan_vien,ma_nhan_vien',
            'cccd'          => 'required|string|max:20|unique:nhan_vien,cccd',
            'ngay_sinh'     => 'nullable|date',
            'ngay_vao_lam'  => 'nullable|date',
            'ngay_nghi_lam' => 'nullable|date',
            'ghi_chu'       => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required'     => 'Vui lòng nhập họ tên.',
            'chuc_vu.required'    => 'Vui lòng chọn chức vụ.',
            'chuc_vu.exists'      => 'Chức vụ không tồn tại.',
            'email.required'      => 'Vui lòng nhập email.',
            'email.email'         => 'Email không đúng định dạng.',
            'email.unique'        => 'Email đã tồn tại trong hệ thống.',
            'mat_khau.required'   => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min'        => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed'  => 'Xác nhận mật khẩu không khớp.',
            'ma_nhan_vien.unique' => 'Mã nhân viên đã tồn tại.',
            'cccd.required'       => 'Vui lòng nhập CCCD.',
            'cccd.unique'         => 'CCCD đã được sử dụng.',
        ];
    }
}
