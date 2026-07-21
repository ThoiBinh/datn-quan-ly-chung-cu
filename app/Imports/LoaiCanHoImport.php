<?php

namespace App\Imports;

use App\Models\LoaiCanHo;
use Illuminate\Validation\Rule;

/**
 * Sheet "LoaiCanHo" — cột: ten_loai_can_ho.
 * Validate/unique giống LoaiCanHoController::store() (app/Http/Controllers/Admin/LoaiCanHoController.php).
 */
class LoaiCanHoImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['ten_loai_can_ho'];
    }

    public static function sampleRow(): array
    {
        return ['Căn hộ 2 phòng ngủ'];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ten_loai_can_ho'] = trim((string) ($row['ten_loai_can_ho'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'ten_loai_can_ho' => [
                'required', 'string', 'max:100',
                Rule::unique('loai_can_ho', 'ten_loai_can_ho')->whereNull('deletedAt'),
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'ten_loai_can_ho.required' => 'Thiếu tên loại căn hộ.',
            'ten_loai_can_ho.unique' => 'Tên loại căn hộ đã tồn tại.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkDuplicateInBatch(
                $validator,
                'ten_loai_can_ho',
                'ten_loai_can_ho',
                fn ($row) => $row['ten_loai_can_ho'] ?? null,
                'Tên loại căn hộ bị trùng trong file.'
            );
        });
    }

    public function model(array $row)
    {
        $this->successCount++;

        return new LoaiCanHo([
            'ten_loai_can_ho' => $row['ten_loai_can_ho'],
            'deletedAt' => null,
        ]);
    }
}
