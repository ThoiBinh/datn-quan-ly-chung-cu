<?php

namespace App\Imports;

use App\Http\Requests\Concerns\ValidatesCccd;
use App\Models\CuDan;
use App\Rules\SoDienThoaiVietNam;
use Illuminate\Support\Facades\Hash;

/**
 * Sheet "CuDan" — cột: ma_cu_dan, ho_ten_dem, ten, sdt, cccd, email, mat_khau,
 * ngay_sinh, gioi_tinh, tinh, xa, dia_chi, trang_thai.
 *
 * "ma_cu_dan" CHỈ tồn tại trong file Excel (bảng cu_dan không có cột này) — dùng để
 * sheet CuDanCanHo tham chiếu tới đúng cư dân vừa tạo trong CÙNG lượt import. Vì
 * đây là khóa tổng hợp trong bộ nhớ (không truy vấn lại được từ DB như các sheet
 * khác), lớp này KHÔNG dùng đường batch-insert mặc định của AbstractSheetImport:
 * model() tự lưu (save()) ngay để lấy được id thật rồi ghi vào ImportContext, sau đó
 * trả về null để Maatwebsite bỏ qua (không insert lại lần nữa).
 */
class CuDanImport extends AbstractSheetImport
{
    use ValidatesCccd;

    public static function expectedHeaders(): array
    {
        return [
            'ma_cu_dan', 'ho_ten_dem', 'ten', 'sdt', 'cccd', 'email', 'mat_khau',
            'ngay_sinh', 'gioi_tinh', 'tinh', 'xa', 'dia_chi', 'trang_thai',
        ];
    }

    public static function sampleRow(): array
    {
        return [
            'CD001', 'Nguyễn Thị', 'B', '0987654321', '012345678902', 'ntb@example.com',
            'password123', '1995-05-05', 'Nữ', 'TP.HCM', 'Phường 1', '12 Đường ABC', 1,
        ];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ma_cu_dan'] = trim((string) ($row['ma_cu_dan'] ?? ''));
        $row['cccd'] = trim((string) ($row['cccd'] ?? ''));
        $row['gioi_tinh'] = $this->normalizeGioiTinh($row['gioi_tinh'] ?? null);

        return $row;
    }

    /**
     * Cho phép nhập "Nam"/"Nữ" (giống nhãn hiển thị của CuDan::getGioiTinhLabelAttribute,
     * app/Models/CuDan.php) thay vì bắt buộc gõ số 0/1 — giữ nguyên giá trị gốc nếu
     * không nhận diện được để rule "in:0,1" tự báo lỗi rõ ràng.
     */
    private function normalizeGioiTinh(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return match (mb_strtolower(trim((string) $value))) {
            'nam', '1' => 1,
            'nữ', 'nu', '0' => 0,
            default => $value,
        };
    }

    public function rules(): array
    {
        return [
            'ma_cu_dan' => ['required', 'string', 'max:50'],
            'ho_ten_dem' => 'required|string|max:150',
            'ten' => 'required|string|max:50',
            'sdt' => ['nullable', 'string', new SoDienThoaiVietNam()],
            'cccd' => $this->cccdRules(true, 'cu_dan'),
            'email' => ['nullable', 'email', 'max:150', 'unique:cu_dan,email'],
            'mat_khau' => 'required|string|min:8',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|in:0,1',
            'tinh' => 'nullable|string|max:100',
            'xa' => 'nullable|string|max:100',
            'dia_chi' => 'nullable|string|max:255',
            'trang_thai' => 'required|in:0,1,2,3',
        ];
    }

    public function customValidationMessages(): array
    {
        return array_merge($this->cccdMessages(), [
            'ma_cu_dan.required' => 'Thiếu mã cư dân.',
            'ho_ten_dem.required' => 'Thiếu họ tên đệm.',
            'ten.required' => 'Thiếu tên.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'mat_khau.required' => 'Thiếu mật khẩu.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'gioi_tinh.in' => 'Giới tính chỉ nhận giá trị Nam, Nữ (hoặc 0, 1).',
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkDuplicateInBatch($validator, 'ma_cu_dan', 'ma_cu_dan', fn ($row) => $row['ma_cu_dan'] ?? null, 'Mã cư dân bị trùng trong file.');
            $this->checkDuplicateInBatch($validator, 'cccd', 'cccd', fn ($row) => $row['cccd'] ?? null, 'Số CCCD bị trùng trong file.');
            $this->checkDuplicateInBatch($validator, 'email', 'email', fn ($row) => $row['email'] ?? null, 'Email bị trùng trong file.');
        });
    }

    public function model(array $row)
    {
        $this->successCount++;

        $cuDan = new CuDan([
            'ho_ten_dem' => $row['ho_ten_dem'],
            'ten' => $row['ten'],
            'sdt' => $row['sdt'] ?: null,
            'cccd' => $row['cccd'],
            'email' => $row['email'] ?: null,
            'mat_khau' => Hash::make((string) $row['mat_khau']),
            'ngay_sinh' => $row['ngay_sinh'] ?: null,
            'gioi_tinh' => $row['gioi_tinh'] !== '' && $row['gioi_tinh'] !== null ? (int) $row['gioi_tinh'] : null,
            'tinh' => $row['tinh'] ?: null,
            'xa' => $row['xa'] ?: null,
            'dia_chi' => $row['dia_chi'] ?: null,
            'trang_thai' => (int) $row['trang_thai'],
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);
        $cuDan->save();

        $this->context->remember('CuDan', $row['ma_cu_dan'], $cuDan->id);

        return null;
    }
}
