<?php

namespace App\Imports;

use App\Models\TienIch;
use Illuminate\Validation\Rule;

/**
 * Sheet "TienIch" — cột: ten_tien_ich, ten_loai_tien_ich (-> loai_tien_ich), tien_to
 * (-> toa_nha, có thể để trống), mo_ta, vi_tri, suc_chua, gio_mo_cua, gio_dong_cua
 * (dạng chữ "HH:mm"), phi_su_dung, can_dat_truoc, trang_thai.
 * Validate/unique giống StoreTienIchRequest (unique tên tiện ích theo phạm vi toà nhà).
 */
class TienIchImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return [
            'ten_tien_ich', 'ten_loai_tien_ich', 'tien_to', 'mo_ta', 'vi_tri',
            'suc_chua', 'gio_mo_cua', 'gio_dong_cua', 'phi_su_dung', 'can_dat_truoc', 'trang_thai',
        ];
    }

    public static function sampleRow(): array
    {
        return ['Hồ bơi tầng 1', 'Hồ bơi', 'A', 'Hồ bơi ngoài trời', 'Tầng 1', 50, '06:00', '22:00', 50000, 1, 1];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ten_tien_ich'] = trim((string) ($row['ten_tien_ich'] ?? ''));
        $row['ten_loai_tien_ich'] = trim((string) ($row['ten_loai_tien_ich'] ?? ''));
        $row['tien_to'] = strtoupper(trim((string) ($row['tien_to'] ?? '')));

        return $row;
    }

    private function resolveToaNhaId(string $tienTo): ?int
    {
        return $tienTo !== '' ? $this->context->lookupByColumn('toa_nha', 'tien_to', $tienTo) : null;
    }

    public function rules(): array
    {
        return [
            // Kiểm tra trùng tên tiện ích theo phạm vi toà nhà nằm ở withValidator()
            // vì cần đọc thêm cột "tien_to" cùng dòng.
            'ten_tien_ich' => 'required|string|max:255',
            'ten_loai_tien_ich' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('loai_tien_ich', 'ten_loai_tien_ich', $value) === null) {
                        $fail('Loại tiện ích không tồn tại.');
                    }
                },
            ],
            'tien_to' => [
                'nullable', 'string',
                function ($attribute, $value, $fail) {
                    if ($value !== '' && $this->resolveToaNhaId($value) === null) {
                        $fail('Tòa nhà không tồn tại.');
                    }
                },
            ],
            'mo_ta' => 'nullable|string',
            'vi_tri' => 'nullable|string|max:255',
            'suc_chua' => 'nullable|integer|min:1',
            'gio_mo_cua' => 'nullable|date_format:H:i',
            'gio_dong_cua' => 'nullable|date_format:H:i|after:gio_mo_cua',
            'phi_su_dung' => 'required|numeric|min:0|max:999999999999999',
            'can_dat_truoc' => 'nullable|boolean',
            'trang_thai' => 'required|boolean',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'ten_tien_ich.required' => 'Thiếu tên tiện ích.',
            'phi_su_dung.required' => 'Thiếu giá sử dụng.',
        ];
    }

    /**
     * Maatwebsite validate cả 1 batch nhiều dòng cùng lúc (xem
     * AbstractSheetImport::checkDuplicateInBatch()) nên phải tự lặp theo từng dòng
     * trong batch ở đây, không thể coi $validator->getData() là dữ liệu của 1 dòng.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($validator->getData() as $rowIndex => $data) {
                if (!is_array($data) || $validator->errors()->has("{$rowIndex}.*")) {
                    continue;
                }

                $key = ($data['tien_to'] ?? '') . '|' . ($data['ten_tien_ich'] ?? '');

                if ($this->isDuplicateInFile('ten_tien_ich', $key)) {
                    $validator->errors()->add("{$rowIndex}.ten_tien_ich", 'Tòa nhà này đã có tiện ích trùng tên (trùng trong file).');

                    continue;
                }

                $toaNhaId = $this->resolveToaNhaId((string) ($data['tien_to'] ?? ''));

                $exists = TienIch::where('ten_tien_ich', $data['ten_tien_ich'])
                    ->where('toa_nha', $toaNhaId)
                    ->whereNull('deletedAt')
                    ->exists();

                if ($exists) {
                    $validator->errors()->add("{$rowIndex}.ten_tien_ich", 'Tòa nhà này đã có tiện ích trùng tên.');

                    continue;
                }

                $this->markSeenInFile('ten_tien_ich', $key);
            }
        });
    }

    public function model(array $row)
    {
        $this->successCount++;

        return new TienIch([
            'ten_tien_ich' => $row['ten_tien_ich'],
            'loai_tien_ich' => $this->context->lookupByColumn('loai_tien_ich', 'ten_loai_tien_ich', $row['ten_loai_tien_ich']),
            'toa_nha' => $this->resolveToaNhaId($row['tien_to']),
            'mo_ta' => $row['mo_ta'] ?: null,
            'vi_tri' => $row['vi_tri'] ?: null,
            'suc_chua' => $row['suc_chua'] !== '' && $row['suc_chua'] !== null ? (int) $row['suc_chua'] : null,
            'gio_mo_cua' => $row['gio_mo_cua'] ?: null,
            'gio_dong_cua' => $row['gio_dong_cua'] ?: null,
            'phi_su_dung' => $row['phi_su_dung'],
            'can_dat_truoc' => (bool) ($row['can_dat_truoc'] ?? false),
            'trang_thai' => (int) $row['trang_thai'],
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);
    }
}
