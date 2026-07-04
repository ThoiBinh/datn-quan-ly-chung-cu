<?php

namespace App\Http\Requests\Manager;

use App\Models\CauHinhWebsite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCauHinhThanhToanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_thuoc_tinh'  => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'unique:cau_hinh_website,ma_thuoc_tinh'],
            'ten_thuoc_tinh' => 'required|string|max:255',
            'kieu_du_lieu'   => ['required', Rule::in(CauHinhWebsite::KIEU_DU_LIEU_OPTIONS)],
            'mo_ta'          => 'nullable|string|max:500',
            'placeholder'    => 'nullable|string|max:255',
            'thu_tu'         => 'nullable|integer|min:0',
            'la_bao_mat'     => 'nullable|boolean',
            'trang_thai'     => 'nullable|boolean',
            'gia_tri'        => $this->giaTriRule(),
        ];
    }

    protected function giaTriRule(): string
    {
        return match ($this->input('kieu_du_lieu')) {
            'email'    => 'nullable|email|max:255',
            'url'      => 'nullable|url|max:255',
            'number'   => 'nullable|numeric',
            'boolean'  => 'nullable|in:0,1',
            'password' => 'nullable|string|max:255',
            'json'     => 'nullable|json',
            'phone'    => 'nullable|string|max:20',
            default    => 'nullable|string|max:255',
        };
    }

    public function messages(): array
    {
        return [
            'ma_thuoc_tinh.required'  => 'Vui lòng nhập mã thuộc tính.',
            'ma_thuoc_tinh.regex'     => 'Mã thuộc tính chỉ gồm chữ thường, số và dấu gạch dưới.',
            'ma_thuoc_tinh.unique'    => 'Mã thuộc tính đã tồn tại.',
            'ten_thuoc_tinh.required' => 'Vui lòng nhập tên thuộc tính.',
            'kieu_du_lieu.required'   => 'Vui lòng chọn kiểu dữ liệu.',
            'kieu_du_lieu.in'         => 'Kiểu dữ liệu không hợp lệ.',
            'gia_tri.email'           => 'Giá trị phải đúng định dạng email.',
            'gia_tri.url'             => 'Giá trị phải đúng định dạng URL.',
            'gia_tri.numeric'         => 'Giá trị phải là số.',
            'gia_tri.json'            => 'Giá trị phải đúng định dạng JSON.',
        ];
    }
}
