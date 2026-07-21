<?php

namespace App\Imports;

use App\Models\PhuongTien;
use Illuminate\Validation\Rule;

/**
 * Sheet "PhuongTien" — cột: bien_so, ten_phuong_tien, ten_loai_phuong_tien
 * (-> loai_phuong_tien), so_can_ho (-> can_ho), ngay_dang_ky, trang_thai.
 * Validate/unique giống StorePhuongTienRequest.
 */
class PhuongTienImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['bien_so', 'ten_phuong_tien', 'ten_loai_phuong_tien', 'so_can_ho', 'ngay_dang_ky', 'trang_thai'];
    }

    public static function sampleRow(): array
    {
        return ['59A1-12345', 'Xe máy Honda', 'Xe máy', 'A05101', '2023-01-01', 1];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['bien_so'] = trim((string) ($row['bien_so'] ?? ''));
        $row['ten_loai_phuong_tien'] = trim((string) ($row['ten_loai_phuong_tien'] ?? ''));
        $row['so_can_ho'] = strtoupper(trim((string) ($row['so_can_ho'] ?? '')));

        return $row;
    }

    public function rules(): array
    {
        return [
            'ten_phuong_tien' => 'nullable|string|max:150',
            'bien_so' => [
                'required', 'string', 'max:20',
                Rule::unique('phuong_tien', 'bien_so')->whereNull('deletedAt'),
            ],
            'ten_loai_phuong_tien' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('loai_phuong_tien', 'ten_loai_phuong_tien', $value) === null) {
                        $fail('Loại phương tiện không tồn tại.');
                    }
                },
            ],
            'so_can_ho' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('can_ho', 'so_can_ho', $value) === null) {
                        $fail('Căn hộ không tồn tại.');
                    }
                },
            ],
            'ngay_dang_ky' => 'nullable|date',
            'trang_thai' => 'nullable|in:0,1',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'bien_so.required' => 'Thiếu biển số xe.',
            'bien_so.unique' => 'Biển số xe đã tồn tại.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkDuplicateInBatch($validator, 'bien_so', 'bien_so', fn ($row) => $row['bien_so'] ?? null, 'Biển số xe bị trùng trong file.');
        });
    }

    public function model(array $row)
    {
        $this->successCount++;

        return new PhuongTien([
            'ten_phuong_tien' => $row['ten_phuong_tien'] ?: null,
            'bien_so' => $row['bien_so'],
            'loai_phuong_tien' => $this->context->lookupByColumn('loai_phuong_tien', 'ten_loai_phuong_tien', $row['ten_loai_phuong_tien']),
            'can_ho' => $this->context->lookupByColumn('can_ho', 'so_can_ho', $row['so_can_ho']),
            'ngay_dang_ky' => $row['ngay_dang_ky'] ?: null,
            'trang_thai' => $row['trang_thai'] !== '' && $row['trang_thai'] !== null ? (int) $row['trang_thai'] : 1,
        ]);
    }
}
