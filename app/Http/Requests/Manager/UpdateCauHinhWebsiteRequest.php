<?php

namespace App\Http\Requests\Manager;

use App\Models\CauHinhWebsite;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCauHinhWebsiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mo_ta'           => 'nullable|string|max:500',
            'placeholder'     => 'nullable|string|max:255',
            'thu_tu'          => 'nullable|integer|min:0',
            'la_bao_mat'      => 'nullable|boolean',
            'duoc_chinh_sua'  => 'nullable|boolean',
            'trang_thai'      => 'nullable|boolean',
            'gia_tri'         => $this->giaTriRule(),
            'gia_tri_file'    => $this->giaTriFileRule(),
        ];
    }

    protected function kieuDuLieu(): ?string
    {
        /** @var CauHinhWebsite|null $cauHinhWebsite */
        $cauHinhWebsite = $this->route('cauHinhWebsite');
        return $cauHinhWebsite?->kieu_du_lieu;
    }

    protected function giaTriRule(): string
    {
        return match ($this->kieuDuLieu()) {
            'email'    => 'nullable|email|max:255',
            'url'      => 'nullable|url|max:255',
            'number'   => 'nullable|numeric',
            'boolean'  => 'nullable|in:0,1',
            'password' => 'nullable|string|max:255',
            'json'     => 'nullable|json',
            'phone'    => 'nullable|string|max:20',
            default    => 'nullable|string',
        };
    }

    protected function giaTriFileRule(): string
    {
        return match ($this->kieuDuLieu()) {
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,ico|max:2048',
            'file'  => 'nullable|file|max:5120',
            default => 'nullable',
        };
    }

    public function messages(): array
    {
        return [
            'gia_tri.email'      => 'Giá trị phải đúng định dạng email.',
            'gia_tri.url'        => 'Giá trị phải đúng định dạng URL.',
            'gia_tri.numeric'    => 'Giá trị phải là số.',
            'gia_tri.date'       => 'Giá trị phải đúng định dạng ngày.',
            'gia_tri.json'       => 'Giá trị phải đúng định dạng JSON.',
            'gia_tri_file.image' => 'File phải là hình ảnh.',
            'gia_tri_file.mimes' => 'Chỉ chấp nhận định dạng jpg, jpeg, png, webp, gif, ico.',
            'gia_tri_file.max'   => 'Dung lượng file vượt quá giới hạn cho phép.',
        ];
    }
}
