<?php

namespace App\Http\Requests\Resident;

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
            'tien_ich' => [
                'required',
                'integer',
                Rule::exists('tien_ich', 'id')->where(function ($query) {
                    $query->where('trang_thai', TienIch::TRANG_THAI_HOAT_DONG)
                        ->where('can_dat_truoc', true)
                        ->whereNull('deletedAt');
                }),
            ],
            'can_ho' => [
                'required',
                'integer',
                Rule::exists('can_ho', 'id')->whereNull('deletedAt'),
                // Căn hộ phải thuộc cư dân đang đăng nhập (còn đang cư trú, trang_thai = 1
                // trên cu_dan_can_ho) — chặn cư dân đặt lịch chỉ định căn hộ của người khác.
                Rule::exists('cu_dan_can_ho', 'can_ho')->where(function ($query) {
                    $query->where('cu_dan', auth('cudan')->id())
                        ->where('trang_thai', 1);
                }),
            ],
            'thoi_gian_bat_dau' => ['required', 'date'],
            'thoi_gian_ket_thuc' => ['required', 'date', 'after:thoi_gian_bat_dau'],
            'so_nguoi' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'tien_ich.required' => 'Vui lòng chọn tiện ích.',
            'tien_ich.integer' => 'Tiện ích không hợp lệ.',
            'tien_ich.exists' => 'Tiện ích không tồn tại, hiện không hoạt động, hoặc không nhận đặt trước.',
            'can_ho.required' => 'Vui lòng chọn căn hộ.',
            'can_ho.integer' => 'Căn hộ không hợp lệ.',
            'can_ho.exists' => 'Căn hộ không tồn tại hoặc không thuộc quyền sử dụng của bạn.',
            'thoi_gian_bat_dau.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'thoi_gian_bat_dau.date' => 'Thời gian bắt đầu không hợp lệ.',
            'thoi_gian_ket_thuc.required' => 'Vui lòng chọn thời gian kết thúc.',
            'thoi_gian_ket_thuc.date' => 'Thời gian kết thúc không hợp lệ.',
            'thoi_gian_ket_thuc.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'so_nguoi.required' => 'Vui lòng nhập số người.',
            'so_nguoi.integer' => 'Số người phải là số nguyên.',
            'so_nguoi.min' => 'Số người phải lớn hơn 0.',
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
