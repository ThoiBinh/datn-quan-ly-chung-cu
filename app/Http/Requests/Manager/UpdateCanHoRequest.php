<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCanHoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'toa_nha'              => 'required|integer|exists:toa_nha,id',
            'so_can_ho'            => 'required|string|max:50',
            'tang'                 => 'required|integer|min:1',
            'gia'                  => 'nullable|numeric|min:0',
            'loai_can_ho'          => 'required|integer|exists:loai_can_ho,id',
            'trang_thai'           => 'required|integer|exists:trang_thai_can_ho,id',
            'thuoc_tinh'           => 'nullable|array',
            'thuoc_tinh.*.gia_tri' => 'nullable|string|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'toa_nha.required'         => 'Vui lòng chọn tòa nhà.',
            'toa_nha.exists'           => 'Tòa nhà không tồn tại.',
            'so_can_ho.required'       => 'Vui lòng nhập số căn hộ.',
            'so_can_ho.max'            => 'Số căn hộ không quá 50 ký tự.',
            'tang.required'            => 'Vui lòng nhập tầng.',
            'tang.integer'             => 'Tầng phải là số nguyên.',
            'tang.min'                 => 'Tầng phải từ 1 trở lên.',
            'loai_can_ho.required'     => 'Vui lòng chọn loại căn hộ.',
            'loai_can_ho.exists'       => 'Loại căn hộ không tồn tại.',
            'trang_thai.required'      => 'Vui lòng chọn trạng thái.',
            'trang_thai.exists'        => 'Trạng thái không tồn tại.',
            'thuoc_tinh.*.gia_tri.max' => 'Giá trị thuộc tính không được vượt quá 150 ký tự.',
        ];
    }
}
