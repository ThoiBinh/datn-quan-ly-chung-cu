<?php

namespace App\Imports;

use App\Models\LoaiPhuongTien;
use Illuminate\Validation\Rule;

/**
 * Sheet "LoaiPhuongTien" — cột: ten_loai_phuong_tien.
 * Validate/unique giống LoaiPhuongTienController::store().
 */
class LoaiPhuongTienImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['ten_loai_phuong_tien'];
    }

    public static function sampleRow(): array
    {
        return ['Xe máy'];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ten_loai_phuong_tien'] = trim((string) ($row['ten_loai_phuong_tien'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'ten_loai_phuong_tien' => [
                'required', 'string', 'max:100',
                Rule::unique('loai_phuong_tien', 'ten_loai_phuong_tien')->whereNull('deletedAt'),
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'ten_loai_phuong_tien.required' => 'Thiếu tên loại phương tiện.',
            'ten_loai_phuong_tien.unique' => 'Tên loại phương tiện đã tồn tại.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkDuplicateInBatch(
                $validator,
                'ten_loai_phuong_tien',
                'ten_loai_phuong_tien',
                fn ($row) => $row['ten_loai_phuong_tien'] ?? null,
                'Tên loại phương tiện bị trùng trong file.'
            );
        });
    }

    public function model(array $row)
    {
        $this->successCount++;

        return new LoaiPhuongTien([
            'ten_loai_phuong_tien' => $row['ten_loai_phuong_tien'],
        ]);
    }
}
