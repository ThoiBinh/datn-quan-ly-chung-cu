<?php

namespace App\Imports;

use App\Http\Requests\Concerns\ValidatesCccd;
use App\Models\NhanVien;
use App\Rules\SoDienThoaiVietNam;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Sheet "NhanVien" — cột: ma_nhan_vien, ho_ten, ten_chuc_vu (-> chuc_vu, lookup theo
 * tên — bảng chuc_vu không nằm trong danh sách sheet nên chỉ tra cứu, không import),
 * sdt, email, mat_khau, cccd, ngay_sinh, ngay_vao_lam, ngay_nghi_lam, ghi_chu, trang_thai.
 *
 * Tái sử dụng ValidatesCccd::cccdRules() (app/Http/Requests/Concerns/ValidatesCccd.php)
 * và App\Rules\SoDienThoaiVietNam — đúng validation đang dùng ở StoreNhanVienRequest.
 */
class NhanVienImport extends AbstractSheetImport
{
    use ValidatesCccd;

    public static function expectedHeaders(): array
    {
        return [
            'ma_nhan_vien', 'ho_ten', 'ten_chuc_vu', 'sdt', 'email',
            'mat_khau', 'cccd', 'ngay_sinh', 'ngay_vao_lam', 'ngay_nghi_lam',
            'ghi_chu', 'trang_thai',
        ];
    }

    public static function sampleRow(): array
    {
        return [
            'NV001', 'Nguyễn Văn A', 'Quản lý', '0912345678', 'nva@example.com',
            'password123', '012345678901', '1990-01-01', '2023-01-01', '', 'Nhân viên mẫu', 1,
        ];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['cccd'] = trim((string) ($row['cccd'] ?? ''));
        $row['ma_nhan_vien'] = trim((string) ($row['ma_nhan_vien'] ?? ''));
        $row['ten_chuc_vu'] = trim((string) ($row['ten_chuc_vu'] ?? ''));
        $row['email'] = trim((string) ($row['email'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'ho_ten' => 'required|string|max:150',
            'ten_chuc_vu' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('chuc_vu', 'chuc_vu', $value) === null) {
                        $fail('Chức vụ không tồn tại.');
                    }
                },
            ],
            'sdt' => ['nullable', 'string', new SoDienThoaiVietNam()],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('nhan_vien', 'email'),
            ],
            'mat_khau' => 'required|string|min:8',
            'ma_nhan_vien' => [
                'nullable', 'string', 'max:50',
                Rule::unique('nhan_vien', 'ma_nhan_vien'),
            ],
            'cccd' => $this->cccdRules(true, 'nhan_vien'),
            'ngay_sinh' => 'nullable|date',
            'ngay_vao_lam' => 'nullable|date',
            'ngay_nghi_lam' => 'nullable|date',
            'ghi_chu' => 'nullable|string|max:2000',
            'trang_thai' => 'required|in:0,1',
        ];
    }

    public function customValidationMessages(): array
    {
        return array_merge($this->cccdMessages(), [
            'ho_ten.required' => 'Thiếu họ tên.',
            'email.required' => 'Thiếu email.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'mat_khau.required' => 'Thiếu mật khẩu.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'ma_nhan_vien.unique' => 'Mã nhân viên đã tồn tại.',
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkDuplicateInBatch($validator, 'email', 'email', fn ($row) => $row['email'] ?? null, 'Email bị trùng trong file.');
            $this->checkDuplicateInBatch($validator, 'ma_nhan_vien', 'ma_nhan_vien', fn ($row) => $row['ma_nhan_vien'] ?? null, 'Mã nhân viên bị trùng trong file.');
            $this->checkDuplicateInBatch($validator, 'cccd', 'cccd', fn ($row) => $row['cccd'] ?? null, 'Số CCCD bị trùng trong file.');
        });
    }

    public function model(array $row)
    {
        $this->successCount++;

        return new NhanVien([
            'ho_ten' => $row['ho_ten'],
            'chuc_vu' => $this->context->lookupByColumn('chuc_vu', 'chuc_vu', $row['ten_chuc_vu']),
            'sdt' => $row['sdt'] ?: null,
            'email' => $row['email'],
            'mat_khau' => Hash::make((string) $row['mat_khau']),
            'trang_thai' => (int) $row['trang_thai'],
            'ma_nhan_vien' => $row['ma_nhan_vien'] ?: null,
            'cccd' => $row['cccd'],
            'ngay_sinh' => $row['ngay_sinh'] ?: null,
            'ngay_vao_lam' => $row['ngay_vao_lam'] ?: null,
            'ngay_nghi_lam' => $row['ngay_nghi_lam'] ?: null,
            'ghi_chu' => $row['ghi_chu'] ?: null,
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);
    }
}
