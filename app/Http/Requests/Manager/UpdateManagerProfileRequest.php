<?php

namespace App\Http\Requests\Manager;

use App\Rules\SoDienThoaiVietNam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateManagerProfileRequest extends FormRequest
{
    /**
     * Manager luôn chỉ được cập nhật tài khoản của chính mình — không có tham số
     * ID nào trong route/request được dùng để xác định bản ghi, nên không có gì
     * để kiểm tra quyền sở hữu ở đây ngoài việc đã qua middleware 'manager'.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->sdt)) {
            $this->merge(['sdt' => trim($this->sdt)]);
        }
    }

    public function rules(): array
    {
        return [
            'sdt' => [
                'required', 'string', new SoDienThoaiVietNam(),
                Rule::unique('nhan_vien', 'sdt')->ignore(auth('nhanvien')->id()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'sdt.required' => 'Vui lòng nhập số điện thoại.',
            'sdt.unique'   => 'Số điện thoại đã được sử dụng bởi nhân viên khác.',
        ];
    }
}
