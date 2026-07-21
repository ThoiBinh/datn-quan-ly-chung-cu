<?php

namespace App\Imports;

use App\Models\TrangThaiCanHo;
use Illuminate\Validation\Rule;

/**
 * Sheet "TrangThaiCanHo" — cột: ten_trang_thai.
 * Validate/unique giống StoreTrangThaiCanHoRequest.
 */
class TrangThaiCanHoImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['ten_trang_thai'];
    }

    public static function sampleRow(): array
    {
        return ['Đang sử dụng'];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ten_trang_thai'] = trim((string) ($row['ten_trang_thai'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'ten_trang_thai' => [
                'required', 'string', 'max:100',
                Rule::unique('trang_thai_can_ho', 'ten_trang_thai')->whereNull('deletedAt'),
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'ten_trang_thai.required' => 'Thiếu tên trạng thái căn hộ.',
            'ten_trang_thai.unique' => 'Tên trạng thái căn hộ đã tồn tại.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkDuplicateInBatch(
                $validator,
                'ten_trang_thai',
                'ten_trang_thai',
                fn ($row) => $row['ten_trang_thai'] ?? null,
                'Tên trạng thái căn hộ bị trùng trong file.'
            );
        });
    }

    public function model(array $row)
    {
        $this->successCount++;

        return new TrangThaiCanHo([
            'ten_trang_thai' => $row['ten_trang_thai'],
        ]);
    }
}
