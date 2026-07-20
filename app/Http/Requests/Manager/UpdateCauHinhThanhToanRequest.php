<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCauHinhThanhToanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gia_tri'    => $this->giaTriRule(),
            'trang_thai' => 'nullable|boolean',
        ];
    }

    protected function giaTriRule(): string
    {
        $kieuDuLieu = $this->route('cauHinhThanhToan')?->kieu_du_lieu;

        return match ($kieuDuLieu) {
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
            'gia_tri.email'   => 'Giá trị phải đúng định dạng email.',
            'gia_tri.url'     => 'Giá trị phải đúng định dạng URL.',
            'gia_tri.numeric' => 'Giá trị phải là số.',
            'gia_tri.json'    => 'Giá trị phải đúng định dạng JSON.',
        ];
    }
}
