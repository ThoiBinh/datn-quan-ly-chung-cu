<?php

namespace App\Imports;

use App\Models\CanHo;
use App\Models\ToaNha;
use Illuminate\Support\Facades\DB;

/**
 * Sheet "CanHo" — cột: tien_to (-> toa_nha), tang, so_phong, gia, ten_loai_can_ho
 * (-> loai_can_ho), ten_trang_thai (-> trang_thai_can_ho).
 *
 * Nhận tang/so_phong (không nhận thẳng so_can_ho) và dùng đúng
 * CanHo::sinhSoCanHo() (app/Models/CanHo.php) để sinh mã căn hộ — giống hệt luồng
 * tạo thủ công ở StoreCanHoRequest, không phá vỡ quy ước sinh mã hiện có. Mã
 * so_can_ho sinh ra được publish lại cho PhuongTien/CuDanCanHo tham chiếu.
 */
class CanHoImport extends AbstractSheetImport
{
    /** @var array<string, ToaNha> */
    private array $toaNhaCache = [];

    public static function expectedHeaders(): array
    {
        return ['tien_to', 'tang', 'so_phong', 'gia', 'ten_loai_can_ho', 'ten_trang_thai'];
    }

    public static function sampleRow(): array
    {
        return ['A', 5, 101, 1500000, 'Căn hộ 2 phòng ngủ', 'Đang sử dụng'];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['tien_to'] = strtoupper(trim((string) ($row['tien_to'] ?? '')));
        $row['ten_loai_can_ho'] = trim((string) ($row['ten_loai_can_ho'] ?? ''));
        $row['ten_trang_thai'] = trim((string) ($row['ten_trang_thai'] ?? ''));

        return $row;
    }

    private function resolveToaNha(string $tienTo): ?ToaNha
    {
        if (!isset($this->toaNhaCache[$tienTo])) {
            $this->toaNhaCache[$tienTo] = ToaNha::where('tien_to', $tienTo)->whereNull('deletedAt')->first();
        }

        return $this->toaNhaCache[$tienTo];
    }

    public function rules(): array
    {
        return [
            'tien_to' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if (!$this->resolveToaNha($value)) {
                        $fail('Tòa nhà (tiền tố) không tồn tại.');
                    }
                },
            ],
            'tang' => 'required|integer|min:1',
            'so_phong' => 'required|integer|min:1|max:999',
            'gia' => 'nullable|numeric|min:0',
            'ten_loai_can_ho' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('loai_can_ho', 'ten_loai_can_ho', $value) === null) {
                        $fail('Loại căn hộ không tồn tại.');
                    }
                },
            ],
            'ten_trang_thai' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('trang_thai_can_ho', 'ten_trang_thai', $value) === null) {
                        $fail('Trạng thái căn hộ không tồn tại.');
                    }
                },
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'tang.required' => 'Thiếu tầng.',
            'so_phong.required' => 'Thiếu số phòng.',
        ];
    }

    /**
     * Sau khi các cột đơn lẻ hợp lệ, kiểm tra mã căn hộ sinh ra có bị trùng không.
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

                $toaNha = $this->resolveToaNha((string) ($data['tien_to'] ?? ''));
                if (!$toaNha) {
                    continue;
                }

                $soCanHo = CanHo::sinhSoCanHo($toaNha, (int) $data['tang'], (int) $data['so_phong']);

                if ($this->isDuplicateInFile('so_can_ho', $soCanHo)) {
                    $validator->errors()->add("{$rowIndex}.so_phong", 'Số căn hộ đã tồn tại (trùng trong file).');

                    continue;
                }

                if (DB::table('can_ho')->where('so_can_ho', $soCanHo)->whereNull('deletedAt')->exists()) {
                    $validator->errors()->add("{$rowIndex}.so_phong", 'Số căn hộ đã tồn tại.');

                    continue;
                }

                $this->markSeenInFile('so_can_ho', $soCanHo);
            }
        });
    }

    public function model(array $row)
    {
        $toaNha = $this->resolveToaNha($row['tien_to']);
        $soCanHo = CanHo::sinhSoCanHo($toaNha, (int) $row['tang'], (int) $row['so_phong']);
        $this->successCount++;

        return new CanHo([
            'toa_nha' => $toaNha->id,
            'so_can_ho' => $soCanHo,
            'tang' => (int) $row['tang'],
            'trang_thai' => $this->context->lookupByColumn('trang_thai_can_ho', 'ten_trang_thai', $row['ten_trang_thai']),
            'gia' => ($row['gia'] !== null && $row['gia'] !== '') ? $row['gia'] : null,
            'loai_can_ho' => $this->context->lookupByColumn('loai_can_ho', 'ten_loai_can_ho', $row['ten_loai_can_ho']),
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);
    }
}
