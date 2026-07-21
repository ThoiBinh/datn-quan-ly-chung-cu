<?php

namespace App\Imports;

use App\Models\CuDanCanHo;
use App\Rules\ChuHoDuyNhat;

/**
 * Sheet "CuDanCanHo" — cột: ma_cu_dan (tham chiếu sheet CuDan, xem CuDanImport),
 * so_can_ho (tham chiếu sheet CanHo), ten_vai_tro (-> vai_tro), ngay_chuyen_den,
 * ngay_chuyen_di, trang_thai. Áp dụng lại quy tắc "mỗi căn hộ chỉ 1 Chủ hộ đang cư
 * trú" của App\Rules\ChuHoDuyNhat (app/Rules/ChuHoDuyNhat.php) cho cả so với DB lẫn
 * trong-file.
 */
class CuDanCanHoImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['ma_cu_dan', 'so_can_ho', 'ten_vai_tro', 'ngay_chuyen_den', 'ngay_chuyen_di', 'trang_thai'];
    }

    public static function sampleRow(): array
    {
        return ['CD001', 'A05101', 'Chủ sở hữu', '2023-01-01', '', 1];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ma_cu_dan'] = trim((string) ($row['ma_cu_dan'] ?? ''));
        $row['so_can_ho'] = strtoupper(trim((string) ($row['so_can_ho'] ?? '')));
        $row['ten_vai_tro'] = trim((string) ($row['ten_vai_tro'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'ma_cu_dan' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->recall('CuDan', $value) === null) {
                        $fail('Cư dân (mã cư dân) không tồn tại.');
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
            'ten_vai_tro' => [
                'nullable', 'string',
                function ($attribute, $value, $fail) {
                    if ($value !== '' && $this->context->lookupByColumn('vai_tro', 'vai_tro', $value) === null) {
                        $fail('Vai trò không tồn tại.');
                    }
                },
            ],
            'ngay_chuyen_den' => 'nullable|date',
            'ngay_chuyen_di' => 'nullable|date|after_or_equal:ngay_chuyen_den',
            'trang_thai' => 'required|in:0,1',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'ngay_chuyen_di.after_or_equal' => 'Ngày chuyển đi phải sau hoặc bằng ngày chuyển đến.',
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

                if (empty($data['ten_vai_tro']) || (int) ($data['trang_thai'] ?? 0) !== 1) {
                    continue;
                }

                $vaiTroId = $this->context->lookupByColumn('vai_tro', 'vai_tro', $data['ten_vai_tro']);
                $chuHoId = ChuHoDuyNhat::chuHoVaiTroId();
                if ($vaiTroId === null || $chuHoId === null || $vaiTroId !== $chuHoId) {
                    continue;
                }

                $canHoId = $this->context->lookupByColumn('can_ho', 'so_can_ho', (string) $data['so_can_ho']);
                if ($canHoId === null) {
                    continue;
                }

                if ($this->isDuplicateInFile('chu_ho', (string) $canHoId)) {
                    $validator->errors()->add("{$rowIndex}.ten_vai_tro", 'Căn hộ này đã có Chủ hộ (trùng trong file).');

                    continue;
                }

                if (ChuHoDuyNhat::daCoChuHo($canHoId, $chuHoId)) {
                    $validator->errors()->add("{$rowIndex}.ten_vai_tro", 'Căn hộ này đã có Chủ hộ.');

                    continue;
                }

                $this->markSeenInFile('chu_ho', (string) $canHoId);
            }
        });
    }

    public function model(array $row)
    {
        $canHoId = $this->context->lookupByColumn('can_ho', 'so_can_ho', $row['so_can_ho']);
        $vaiTroId = $row['ten_vai_tro'] !== '' ? $this->context->lookupByColumn('vai_tro', 'vai_tro', $row['ten_vai_tro']) : null;

        $this->successCount++;

        return new CuDanCanHo([
            'cu_dan' => $this->context->recall('CuDan', $row['ma_cu_dan']),
            'can_ho' => $canHoId,
            'vai_tro' => $vaiTroId,
            'ngay_chuyen_den' => $row['ngay_chuyen_den'] ?: null,
            'ngay_chuyen_di' => $row['ngay_chuyen_di'] ?: null,
            'trang_thai' => (int) $row['trang_thai'],
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);
    }
}
