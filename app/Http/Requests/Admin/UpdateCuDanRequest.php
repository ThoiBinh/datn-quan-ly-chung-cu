<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCuDanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cuDan = $this->route('cuDan');
        $id    = $cuDan?->id;

        return [
            'ho_ten_dem'      => 'required|string|max:150',
            'ten'             => 'required|string|max:50',
            'sdt'             => 'nullable|string|max:15',
            'cccd'            => 'required|string|max:20|unique:cu_dan,cccd,' . $id,
            'email'           => 'nullable|email|max:150|unique:cu_dan,email,' . $id,
            'mat_khau'        => 'nullable|string|min:8|confirmed',
            'ngay_sinh'       => 'nullable|date',
            'gioi_tinh'       => 'nullable|in:0,1',
            'tinh'            => 'nullable|string|max:100',
            'xa'              => 'nullable|string|max:100',
            'dia_chi'         => 'nullable|string|max:255',
            'trang_thai'      => 'required|in:0,1,2,3',
            'can_ho'          => 'nullable|integer|exists:can_ho,id',
            'vai_tro'         => 'nullable|integer|exists:vai_tro,id',
            'ngay_chuyen_den' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten_dem.required' => 'Vui lòng nhập họ tên đệm.',
            'ten.required'        => 'Vui lòng nhập tên.',
            'email.email'         => 'Email không đúng định dạng.',
            'email.unique'        => 'Email đã được sử dụng.',
            'cccd.required'       => 'Vui lòng nhập CCCD.',
            'cccd.unique'         => 'CCCD đã được sử dụng.',
            'mat_khau.min'        => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed'  => 'Xác nhận mật khẩu không khớp.',
            'can_ho.exists'       => 'Căn hộ không tồn tại.',
            'vai_tro.exists'      => 'Vai trò không tồn tại.',
        ];
    }
}
