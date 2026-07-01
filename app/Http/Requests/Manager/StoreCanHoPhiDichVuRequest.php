<?php

namespace App\Http\Requests\Manager;

use App\Models\CanHoPhiDichVu;
use Illuminate\Foundation\Http\FormRequest;

class StoreCanHoPhiDichVuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'can_ho'      => 'required|integer|exists:can_ho,id',
            'phi_dich_vu' => [
                'required', 'integer', 'exists:phi_dich_vu,id',
                function ($attribute, $value, $fail) {
                    $exists = CanHoPhiDichVu::where('can_ho', $this->can_ho)
                        ->where('phi_dich_vu', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Dịch vụ này đã được áp dụng cho căn hộ.');
                    }
                },
            ],
            'don_gia' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'can_ho.required'      => 'Vui lòng chọn căn hộ.',
            'can_ho.exists'        => 'Căn hộ không tồn tại.',
            'phi_dich_vu.required' => 'Vui lòng chọn phí dịch vụ.',
            'phi_dich_vu.exists'   => 'Phí dịch vụ không tồn tại.',
            'don_gia.numeric'      => 'Đơn giá phải là số.',
            'don_gia.min'          => 'Đơn giá không được âm.',
        ];
    }
}
