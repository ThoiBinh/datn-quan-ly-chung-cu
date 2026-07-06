<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDatLichTienIchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_dat_lich'     => [
                'required', 'string', 'max:50',
                Rule::unique('dat_lich_tien_ich', 'ma_dat_lich')->whereNull('deletedAt'),
            ],
            'cu_dan'          => 'required|integer|exists:cu_dan,id',
            'can_ho'          => 'nullable|integer|exists:can_ho,id',
            'tien_ich'        => 'required|integer|exists:tien_ich,id',
            'ngay_su_dung'    => 'required|date',
            'gio_bat_dau'     => 'required|date_format:H:i',
            'gio_ket_thuc'    => 'required|date_format:H:i|after:gio_bat_dau',
            'so_nguoi'        => 'required|integer|min:1',
            'phi_su_dung'     => 'nullable|numeric|min:0',
            'ghi_chu'         => 'nullable|string|max:500',
            'trang_thai'      => 'required|integer|in:1,2,3,4,5',
            'nhan_vien_duyet' => 'nullable|integer|exists:nhan_vien,id',
            'ngay_duyet'      => 'nullable|date',
            'ngay_huy'        => 'nullable|date',
            'ly_do_huy'       => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'ma_dat_lich.required'     => 'Vui lòng nhập mã đặt lịch.',
            'ma_dat_lich.max'          => 'Mã đặt lịch không quá 50 ký tự.',
            'ma_dat_lich.unique'       => 'Mã đặt lịch đã tồn tại.',
            'cu_dan.required'          => 'Vui lòng chọn cư dân.',
            'cu_dan.exists'            => 'Cư dân không tồn tại.',
            'can_ho.exists'            => 'Căn hộ không tồn tại.',
            'tien_ich.required'        => 'Vui lòng chọn tiện ích.',
            'tien_ich.exists'          => 'Tiện ích không tồn tại.',
            'ngay_su_dung.required'    => 'Vui lòng chọn ngày sử dụng.',
            'ngay_su_dung.date'        => 'Ngày sử dụng không hợp lệ.',
            'gio_bat_dau.required'     => 'Vui lòng nhập giờ bắt đầu.',
            'gio_bat_dau.date_format'  => 'Giờ bắt đầu không hợp lệ.',
            'gio_ket_thuc.required'    => 'Vui lòng nhập giờ kết thúc.',
            'gio_ket_thuc.date_format' => 'Giờ kết thúc không hợp lệ.',
            'gio_ket_thuc.after'       => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'so_nguoi.required'       => 'Vui lòng nhập số người.',
            'so_nguoi.min'            => 'Số người phải lớn hơn 0.',
            'phi_su_dung.numeric'     => 'Phí sử dụng phải là số.',
            'phi_su_dung.min'         => 'Phí sử dụng không được âm.',
            'ghi_chu.max'             => 'Ghi chú không quá 500 ký tự.',
            'trang_thai.required'     => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'          => 'Trạng thái không hợp lệ.',
            'nhan_vien_duyet.exists' => 'Nhân viên duyệt không tồn tại.',
            'ngay_duyet.date'        => 'Ngày duyệt không hợp lệ.',
            'ngay_huy.date'          => 'Ngày hủy không hợp lệ.',
            'ly_do_huy.max'          => 'Lý do hủy không quá 500 ký tự.',
        ];
    }
}
