<?php

namespace App\Imports;

use App\Models\LoaiTienIch;
use Illuminate\Validation\Rule;

/**
 * Sheet "LoaiTienIch" — cột: ten_loai_tien_ich.
 * Validate/unique giống StoreLoaiTienIchRequest.
 */
class LoaiTienIchImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['ten_loai_tien_ich'];
    }

    public static function sampleRow(): array
    {
        return ['Hồ bơi'];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ten_loai_tien_ich'] = trim((string) ($row['ten_loai_tien_ich'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'ten_loai_tien_ich' => [
                'required', 'string', 'max:100',
                Rule::unique('loai_tien_ich', 'ten_loai_tien_ich')->whereNull('deletedAt'),
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'ten_loai_tien_ich.required' => 'Thiếu tên loại tiện ích.',
            'ten_loai_tien_ich.unique' => 'Tên loại tiện ích đã tồn tại.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkDuplicateInBatch(
                $validator,
                'ten_loai_tien_ich',
                'ten_loai_tien_ich',
                fn ($row) => $row['ten_loai_tien_ich'] ?? null,
                'Tên loại tiện ích bị trùng trong file.'
            );
        });
    }

    public function model(array $row)
    {
        $this->successCount++;

        return new LoaiTienIch([
            'ten_loai_tien_ich' => $row['ten_loai_tien_ich'],
        ]);
    }
}
