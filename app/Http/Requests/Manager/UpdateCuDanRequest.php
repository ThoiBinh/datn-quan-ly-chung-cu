<?php

namespace App\Http\Requests\Manager;

use App\Http\Requests\Concerns\ValidatesCccd;
use App\Rules\ChuHoDuyNhat;
use App\Rules\SoDienThoaiVietNam;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCuDanRequest extends FormRequest
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
        $cuDan = $this->route('cu_dan') ?? $this->route('cuDan');
        $id    = $cuDan?->id;

        return [
            'ho_ten_dem'      => 'nullable|string|max:150',
            'ten'             => 'required|string|max:50',
            'sdt'             => ['nullable', 'string', new SoDienThoaiVietNam()],
            'cccd'            => $this->cccdRules(false, 'cu_dan', $id),
            'email'           => 'nullable|email|max:150|unique:cu_dan,email,' . $id,
            'ngay_sinh'       => 'nullable|date',
            'gioi_tinh'       => 'nullable|in:0,1',
            'tinh'            => 'nullable|string|max:100',
            'xa'              => 'nullable|string|max:100',
            'dia_chi'         => 'nullable|string|max:255',
            'can_ho'          => 'nullable|integer|exists:can_ho,id',
            'vai_tro'         => ['nullable', 'integer', 'exists:vai_tro,id', new ChuHoDuyNhat($this->can_ho, ignoreCuDanId: $id)],
            'ngay_chuyen_den' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return array_merge($this->cccdMessages(), [
            'ten.required'     => 'Vui lòng nhập tên.',
            'ten.max'          => 'Tên không được quá 50 ký tự.',
            'ho_ten_dem.max'   => 'Họ tên đệm không được quá 150 ký tự.',
            'email.unique'     => 'Email đã tồn tại trong hệ thống.',
            'email.email'      => 'Email không hợp lệ.',
            'can_ho.exists'    => 'Căn hộ không tồn tại.',
            'vai_tro.exists'   => 'Vai trò không tồn tại.',
            'ngay_sinh.date'   => 'Ngày sinh không hợp lệ.',
        ]);
    }
}
