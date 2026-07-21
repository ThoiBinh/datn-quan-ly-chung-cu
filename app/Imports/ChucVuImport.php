<?php

namespace App\Imports;

use App\Models\ChucVu;
use Illuminate\Validation\Rule;

/**
 * Sheet "ChucVu" — cột: chuc_vu.
 * Validate/unique giống hệt ChucVuController::store() (app/Http/Controllers/Admin/ChucVuController.php:47).
 * Phải xử lý TRƯỚC sheet NhanVien (xem config/import.php: order=35 so với NhanVien=40)
 * vì NhanVienImport tra cứu "ten_chuc_vu" qua ImportContext::lookupByColumn() —
 * không cần sửa gì ở NhanVienImport, chỉ cần ChucVu được insert trước trong cùng
 * transaction là NhanVien tự thấy được (đọc DB trực tiếp, không cache).
 */
class ChucVuImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['chuc_vu'];
    }

    public static function sampleRow(): array
    {
        return ['Kế toán'];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['chuc_vu'] = trim((string) ($row['chuc_vu'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'chuc_vu' => [
                'required', 'string', 'max:100',
                Rule::unique('chuc_vu', 'chuc_vu'),
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'chuc_vu.required' => 'Vui lòng nhập tên chức vụ.',
            'chuc_vu.unique' => 'Tên chức vụ đã tồn tại.',
            'chuc_vu.max' => 'Tên chức vụ không được vượt quá 100 ký tự.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkDuplicateInBatch(
                $validator,
                'chuc_vu',
                'chuc_vu',
                fn ($row) => $row['chuc_vu'] ?? null,
                'Tên chức vụ bị trùng trong file.'
            );
        });
    }

    public function model(array $row)
    {
        $this->successCount++;

        return new ChucVu([
            'chuc_vu' => $row['chuc_vu'],
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);
    }
}
