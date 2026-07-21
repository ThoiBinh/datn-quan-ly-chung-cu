<?php

namespace App\Imports;

use App\Models\PhiDichVu;

/**
 * Sheet "PhiDichVu" — cột: ten_phi_dich_vu, ten_loai_phi_dich_vu (-> loai_phi_dich_vu),
 * don_gia, ten_don_vi_tinh (-> don_vi_tinh_phi_dich_vu), ten_loai_tinh_phi
 * (-> loai_tinh_phi_dich_vu). Hai bảng đơn vị tính/loại tính phí không nằm trong
 * danh sách sheet được import (ngoài phạm vi yêu cầu) — chỉ TRA CỨU theo tên, phải
 * được khởi tạo sẵn trong hệ thống trước khi import. Validate giống StorePhiDichVuRequest
 * (không có ràng buộc unique tên phí — hệ thống hiện tại cũng không có).
 */
class PhiDichVuImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['ten_phi_dich_vu', 'ten_loai_phi_dich_vu', 'don_gia', 'ten_don_vi_tinh', 'ten_loai_tinh_phi'];
    }

    public static function sampleRow(): array
    {
        return ['Phí quản lý chung cư', 'Phí quản lý', 5000, 'm2', 'Theo diện tích'];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ten_loai_phi_dich_vu'] = trim((string) ($row['ten_loai_phi_dich_vu'] ?? ''));
        $row['ten_don_vi_tinh'] = trim((string) ($row['ten_don_vi_tinh'] ?? ''));
        $row['ten_loai_tinh_phi'] = trim((string) ($row['ten_loai_tinh_phi'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'ten_phi_dich_vu' => 'required|string|max:150',
            'don_gia' => 'required|numeric|min:0|max:999999999999999',
            'ten_loai_phi_dich_vu' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('loai_phi_dich_vu', 'ten_loai_phi_dich_vu', $value) === null) {
                        $fail('Loại phí dịch vụ không tồn tại.');
                    }
                },
            ],
            'ten_don_vi_tinh' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('don_vi_tinh_phi_dich_vu', 'don_vi', $value) === null) {
                        $fail('Đơn vị tính không tồn tại.');
                    }
                },
            ],
            'ten_loai_tinh_phi' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('loai_tinh_phi_dich_vu', 'ten_loai', $value) === null) {
                        $fail('Loại tính phí không tồn tại.');
                    }
                },
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'ten_phi_dich_vu.required' => 'Thiếu tên phí dịch vụ.',
            'don_gia.required' => 'Thiếu đơn giá.',
        ];
    }

    public function model(array $row)
    {
        $this->successCount++;

        return new PhiDichVu([
            'ten_phi_dich_vu' => $row['ten_phi_dich_vu'],
            'don_gia' => $row['don_gia'],
            'loai_phi_dich_vu' => $this->context->lookupByColumn('loai_phi_dich_vu', 'ten_loai_phi_dich_vu', $row['ten_loai_phi_dich_vu']),
            'don_vi_tinh' => $this->context->lookupByColumn('don_vi_tinh_phi_dich_vu', 'don_vi', $row['ten_don_vi_tinh']),
            'loai_tinh_phi' => $this->context->lookupByColumn('loai_tinh_phi_dich_vu', 'ten_loai', $row['ten_loai_tinh_phi']),
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);
    }
}
