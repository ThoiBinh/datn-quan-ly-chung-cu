<?php

namespace App\Imports;

use App\Models\ToaNha;
use Illuminate\Validation\Rule;

/**
 * Sheet "ToaNha" — cột: tien_to, ten_toa_nha, dia_chi, so_tang.
 * Validate/unique giống hệt StoreToaNhaRequest (app/Http/Requests/Admin/StoreToaNhaRequest.php).
 */
class ToaNhaImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['tien_to', 'ten_toa_nha', 'dia_chi', 'so_tang'];
    }

    public static function sampleRow(): array
    {
        return ['A', 'Tòa A', '123 Đường Lê Lợi, Quận 1, TP.HCM', 15];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['tien_to'] = strtoupper(trim((string) ($row['tien_to'] ?? '')));

        return $row;
    }

    public function rules(): array
    {
        return [
            'tien_to' => [
                'required', 'string', 'max:10',
                Rule::unique('toa_nha', 'tien_to'),
            ],
            'ten_toa_nha' => 'required|string|max:255',
            'dia_chi' => 'required|string|max:255',
            'so_tang' => 'required|integer|min:1',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'tien_to.required' => 'Thiếu tiền tố tòa nhà.',
            'tien_to.unique' => 'Tiền tố tòa nhà đã tồn tại trong hệ thống.',
            'ten_toa_nha.required' => 'Thiếu tên tòa nhà.',
            'dia_chi.required' => 'Thiếu địa chỉ.',
            'so_tang.required' => 'Thiếu số tầng.',
            'so_tang.integer' => 'Số tầng phải là số nguyên.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkDuplicateInBatch(
                $validator,
                'tien_to',
                'tien_to',
                fn ($row) => $row['tien_to'] ?? null,
                'Tiền tố tòa nhà bị trùng trong file.'
            );
        });
    }

    public function model(array $row)
    {
        $tienTo = $row['tien_to'];
        $this->successCount++;

        return new ToaNha([
            'tien_to' => $tienTo,
            'ten_toa_nha' => $row['ten_toa_nha'],
            'dia_chi' => $row['dia_chi'],
            'so_tang' => (int) $row['so_tang'],
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);
    }
}
