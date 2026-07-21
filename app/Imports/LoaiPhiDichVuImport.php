<?php

namespace App\Imports;

use App\Models\LoaiPhiDichVu;
use Illuminate\Validation\Rule;

/**
 * Sheet "LoaiPhiDichVu" — cột: ten_loai_phi_dich_vu.
 * Validate/unique giống LoaiPhiDichVuController::store().
 */
class LoaiPhiDichVuImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['ten_loai_phi_dich_vu'];
    }

    public static function sampleRow(): array
    {
        return ['Phí quản lý'];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ten_loai_phi_dich_vu'] = trim((string) ($row['ten_loai_phi_dich_vu'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'ten_loai_phi_dich_vu' => [
                'required', 'string', 'max:150',
                Rule::unique('loai_phi_dich_vu', 'ten_loai_phi_dich_vu')->whereNull('deletedAt'),
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'ten_loai_phi_dich_vu.required' => 'Thiếu tên loại phí dịch vụ.',
            'ten_loai_phi_dich_vu.unique' => 'Tên loại phí dịch vụ đã tồn tại.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkDuplicateInBatch(
                $validator,
                'ten_loai_phi_dich_vu',
                'ten_loai_phi_dich_vu',
                fn ($row) => $row['ten_loai_phi_dich_vu'] ?? null,
                'Tên loại phí dịch vụ bị trùng trong file.'
            );
        });
    }

    public function model(array $row)
    {
        $this->successCount++;

        return new LoaiPhiDichVu([
            'ten_loai_phi_dich_vu' => $row['ten_loai_phi_dich_vu'],
        ]);
    }
}
