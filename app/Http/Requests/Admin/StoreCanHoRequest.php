<?php

namespace App\Http\Requests\Admin;

use App\Models\CanHo;
use App\Models\ToaNha;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreCanHoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'toa_nha'              => 'required|integer|exists:toa_nha,id',
            'tang'                 => 'required|integer|min:1',
            'so_phong'             => 'required|integer|min:1|max:999',
            'gia'                  => 'nullable|numeric|min:0',
            'loai_can_ho'          => 'required|integer|exists:loai_can_ho,id',
            'trang_thai'           => 'required|integer|exists:trang_thai_can_ho,id',
            'thuoc_tinh'           => 'nullable|array',
            'thuoc_tinh.*.gia_tri' => 'nullable|string|max:150',
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $toaNha = ToaNha::find($this->input('toa_nha'));
            if (!$toaNha) {
                return;
            }

            $soCanHo = CanHo::sinhSoCanHo($toaNha, (int) $this->input('tang'), (int) $this->input('so_phong'));

            $daTonTai = CanHo::where('so_can_ho', $soCanHo)->whereNull('deletedAt')->exists();
            if ($daTonTai) {
                $validator->errors()->add('so_phong', 'Số căn hộ đã tồn tại.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'toa_nha.required'         => 'Vui lòng chọn tòa nhà.',
            'toa_nha.exists'           => 'Tòa nhà không tồn tại.',
            'tang.required'            => 'Vui lòng nhập tầng.',
            'tang.integer'             => 'Tầng phải là số nguyên.',
            'tang.min'                 => 'Tầng phải từ 1 trở lên.',
            'so_phong.required'        => 'Vui lòng nhập số phòng.',
            'so_phong.integer'         => 'Số phòng phải là số nguyên.',
            'so_phong.min'             => 'Số phòng phải từ 1 trở lên.',
            'so_phong.max'             => 'Số phòng không được vượt quá 999.',
            'loai_can_ho.required'     => 'Vui lòng chọn loại căn hộ.',
            'loai_can_ho.exists'       => 'Loại căn hộ không tồn tại.',
            'trang_thai.required'      => 'Vui lòng chọn trạng thái.',
            'trang_thai.exists'        => 'Trạng thái không tồn tại.',
            'thuoc_tinh.*.gia_tri.max' => 'Giá trị thuộc tính không được vượt quá 150 ký tự.',
        ];
    }
}
