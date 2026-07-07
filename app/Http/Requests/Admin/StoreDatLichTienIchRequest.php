<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesKhungGioTienIch;
use App\Models\TienIch;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDatLichTienIchRequest extends FormRequest
{
    use ValidatesKhungGioTienIch;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cu_dan' => ['required', 'integer', 'exists:cu_dan,id'],
            'can_ho' => [
                'nullable',
                'integer',
                Rule::exists('can_ho', 'id')->whereNull('deletedAt'),
            ],
            'tien_ich' => [
                'required',
                'integer',
                Rule::exists('tien_ich', 'id')->where(function ($query) {
                    $query->where('trang_thai', TienIch::TRANG_THAI_HOAT_DONG)
                        ->whereNull('deletedAt');
                }),
            ],
            'thoi_gian_bat_dau' => ['required', 'date'],
            'thoi_gian_ket_thuc' => ['required', 'date', 'after:thoi_gian_bat_dau'],
            'so_nguoi' => ['required', 'integer', 'min:1'],
            'ghi_chu' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'cu_dan.required' => 'Vui lòng chọn cư dân.',
            'cu_dan.exists' => 'Cư dân không tồn tại.',
            'can_ho.exists' => 'Căn hộ không tồn tại.',
            'tien_ich.required' => 'Vui lòng chọn tiện ích.',
            'tien_ich.exists' => 'Tiện ích không tồn tại hoặc hiện không hoạt động.',
            'thoi_gian_bat_dau.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'thoi_gian_bat_dau.date' => 'Thời gian bắt đầu không hợp lệ.',
            'thoi_gian_ket_thuc.required' => 'Vui lòng chọn thời gian kết thúc.',
            'thoi_gian_ket_thuc.date' => 'Thời gian kết thúc không hợp lệ.',
            'thoi_gian_ket_thuc.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'so_nguoi.required' => 'Vui lòng nhập số người.',
            'so_nguoi.min' => 'Số người phải lớn hơn 0.',
            'ghi_chu.max' => 'Ghi chú không quá 500 ký tự.',
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $this->validateCungNgay($validator);
            $this->validateTrongGioMoCua($validator);
        });
    }
}
