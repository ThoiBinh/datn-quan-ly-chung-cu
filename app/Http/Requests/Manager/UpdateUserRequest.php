<?php

namespace App\Http\Requests\Manager;

use App\Rules\SoDienThoaiVietNam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('user')?->id;

        return [
            'name'     => 'required|string|max:255',
            'email'    => [
                'required', 'email',
                Rule::unique('nhan_vien', 'email')->whereNull('deletedAt')->ignore($id),
            ],
            'phone'    => ['nullable', 'string', new SoDienThoaiVietNam()],
            'password' => 'nullable|min:8|confirmed',
            'status'   => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Vui lòng nhập họ tên.',
            'email.required'      => 'Vui lòng nhập email.',
            'email.unique'        => 'Email đã tồn tại.',
            'password.min'        => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed'  => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
