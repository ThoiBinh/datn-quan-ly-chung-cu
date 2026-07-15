<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class UpdateManagerPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'mat_khau'          => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'mat_khau.required'         => 'Vui lòng nhập mật khẩu mới.',
            'mat_khau.min'              => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed'        => 'Xác nhận mật khẩu không khớp.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $nhanVien = auth('nhanvien')->user();

            if ($nhanVien && $this->filled('current_password')
                && !Hash::check($this->input('current_password'), $nhanVien->mat_khau)) {
                $validator->errors()->add('current_password', 'Mật khẩu hiện tại không đúng.');
            }
        });
    }
}
