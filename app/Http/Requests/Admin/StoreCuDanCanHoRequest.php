<?php

namespace App\Http\Requests\Admin;

use App\Rules\ChuHoDuyNhat;
use Illuminate\Foundation\Http\FormRequest;

class StoreCuDanCanHoRequest extends FormRequest
{
    private const TRANG_THAI = [0, 1];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dangHoatDong = ChuHoDuyNhat::conCuTru($this->ngay_chuyen_di);

        return [
            'cu_dan'          => 'required|integer|exists:cu_dan,id',
            'can_ho'          => 'required|integer|exists:can_ho,id',
            'vai_tro'         => ['nullable', 'integer', 'exists:vai_tro,id', new ChuHoDuyNhat($this->can_ho, $dangHoatDong)],
            'ngay_chuyen_den' => 'nullable|date',
            'ngay_chuyen_di'  => 'nullable|date|after_or_equal:ngay_chuyen_den',
            'trang_thai'      => 'required|in:' . implode(',', self::TRANG_THAI),
        ];
    }

    public function messages(): array
    {
        return [
            'cu_dan.required'               => 'Vui lòng chọn cư dân.',
            'can_ho.required'               => 'Vui lòng chọn căn hộ.',
            'ngay_chuyen_di.after_or_equal' => 'Ngày chuyển đi phải sau hoặc bằng ngày chuyển đến.',
        ];
    }
}
