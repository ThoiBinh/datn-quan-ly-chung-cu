<?php

namespace App\Imports;

use App\Models\CanHoPhiDichVu;
use Illuminate\Support\Facades\DB;

/**
 * Sheet "CanHoPhiDichVu" — cột: ma_can_ho (-> can_ho.so_can_ho), ma_phi_dich_vu
 * (-> phi_dich_vu.ten_phi_dich_vu), don_gia (tùy chọn).
 *
 * LƯU Ý QUAN TRỌNG (lệch so với đặc tả gốc, đã verify lại theo migration thật
 * database/migrations/2024_01_01_000020_create_can_ho_phi_dich_vu_table.php):
 * bảng can_ho_phi_dich_vu CHỈ có id, can_ho, phi_dich_vu, don_gia, nguoi_cap_nhat,
 * createdAt, updatedAt — KHÔNG có ngay_ap_dung/ngay_ket_thuc/trang_thai/ghi_chu.
 * Vì yêu cầu "không được thêm Migration/đổi Database", sheet này chỉ nhận đúng
 * các cột thật sự tồn tại. don_gia để trống sẽ tự lấy đơn giá gốc của phí dịch vụ
 * — giống hệt hành vi ở CanHoPhiDichVuController::store()
 * (app/Http/Controllers/Admin/CanHoPhiDichVuController.php:102-104).
 */
class CanHoPhiDichVuImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['ma_can_ho', 'ma_phi_dich_vu', 'don_gia'];
    }

    public static function sampleRow(): array
    {
        return ['A05101', 'Phí quản lý chung cư', ''];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ma_can_ho'] = strtoupper(trim((string) ($row['ma_can_ho'] ?? '')));
        $row['ma_phi_dich_vu'] = trim((string) ($row['ma_phi_dich_vu'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'ma_can_ho' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('can_ho', 'so_can_ho', $value) === null) {
                        $fail('Căn hộ không tồn tại.');
                    }
                },
            ],
            'ma_phi_dich_vu' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('phi_dich_vu', 'ten_phi_dich_vu', $value) === null) {
                        $fail('Phí dịch vụ không tồn tại.');
                    }
                },
            ],
            'don_gia' => 'nullable|numeric|min:0',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'ma_can_ho.required' => 'Thiếu mã căn hộ.',
            'ma_phi_dich_vu.required' => 'Thiếu mã phí dịch vụ.',
            'don_gia.numeric' => 'Đơn giá phải là số.',
            'don_gia.min' => 'Đơn giá không được âm.',
        ];
    }

    /**
     * Maatwebsite validate cả 1 batch nhiều dòng cùng lúc (xem
     * AbstractSheetImport::checkDuplicateInBatch()) nên phải tự lặp theo từng dòng
     * trong batch ở đây. Khóa trùng lặp là CẶP (can_ho, phi_dich_vu) — không phải
     * 1 cột đơn — nên không dùng checkDuplicateInBatch() có sẵn mà tự kiểm tra.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($validator->getData() as $rowIndex => $data) {
                if (!is_array($data) || $validator->errors()->has("{$rowIndex}.*")) {
                    continue;
                }

                $canHoId = $this->context->lookupByColumn('can_ho', 'so_can_ho', (string) ($data['ma_can_ho'] ?? ''));
                $phiDichVuId = $this->context->lookupByColumn('phi_dich_vu', 'ten_phi_dich_vu', (string) ($data['ma_phi_dich_vu'] ?? ''));

                if ($canHoId === null || $phiDichVuId === null) {
                    continue;
                }

                $key = $canHoId . '|' . $phiDichVuId;

                if ($this->isDuplicateInFile('can_ho_phi_dich_vu', $key)) {
                    $validator->errors()->add("{$rowIndex}.ma_phi_dich_vu", 'Dịch vụ này đã được áp dụng cho căn hộ (trùng trong file).');

                    continue;
                }

                if (CanHoPhiDichVu::where('can_ho', $canHoId)->where('phi_dich_vu', $phiDichVuId)->exists()) {
                    $validator->errors()->add("{$rowIndex}.ma_phi_dich_vu", 'Dịch vụ này đã được áp dụng cho căn hộ.');

                    continue;
                }

                $this->markSeenInFile('can_ho_phi_dich_vu', $key);
            }
        });
    }

    public function model(array $row)
    {
        $canHoId = $this->context->lookupByColumn('can_ho', 'so_can_ho', $row['ma_can_ho']);
        $phiDichVuId = $this->context->lookupByColumn('phi_dich_vu', 'ten_phi_dich_vu', $row['ma_phi_dich_vu']);

        $donGia = ($row['don_gia'] !== null && $row['don_gia'] !== '')
            ? $row['don_gia']
            : DB::table('phi_dich_vu')->where('id', $phiDichVuId)->value('don_gia');

        $this->successCount++;

        return new CanHoPhiDichVu([
            'can_ho' => $canHoId,
            'phi_dich_vu' => $phiDichVuId,
            'don_gia' => $donGia,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);
    }
}
