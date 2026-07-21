<?php

namespace App\Imports;

use App\Models\BangTin;

/**
 * Sheet "BangTin" — cột: tieu_de, noi_dung, hinh_url, ma_nhan_vien.
 * bang_tin.nguoi_tao/nguoi_cap_nhat là FK bắt buộc (NOT NULL) tới nhan_vien nhưng
 * không được nhập trực tiếp ở nơi nào khác trong hệ thống (luôn lấy từ nhân viên
 * đang đăng nhập) — khi import, cả 2 cột đều được gán theo "ma_nhan_vien" trên
 * cùng dòng. Cột hinh_url nhận 1 trong 2 cách: dán URL/text sẵn có, HOẶC chèn ảnh
 * trực tiếp (Insert > Picture) vào ô của dòng đó — UniversalImportService dùng
 * RowImageExtractor trích ảnh ra trước khi giao cho Maatwebsite xử lý (xem
 * imageColumn()/imageStoragePrefix() bên dưới), ảnh chèn trực tiếp được ưu tiên
 * hơn nếu dòng có cả hai.
 */
class BangTinImport extends AbstractSheetImport
{
    public static function expectedHeaders(): array
    {
        return ['tieu_de', 'noi_dung', 'hinh_url', 'ma_nhan_vien'];
    }

    public static function imageColumn(): ?string
    {
        return 'hinh_url';
    }

    public static function imageStoragePrefix(): string
    {
        return 'bang-tin';
    }

    public static function sampleRow(): array
    {
        return ['Thông báo bảo trì thang máy', 'Thang máy tòa A sẽ bảo trì từ 8h-10h ngày 01/01.', '', 'NV001'];
    }

    public function prepareForValidation($row, $index): array
    {
        $row['ma_nhan_vien'] = trim((string) ($row['ma_nhan_vien'] ?? ''));

        return $row;
    }

    public function rules(): array
    {
        return [
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'hinh_url' => 'nullable|string|max:2000',
            'ma_nhan_vien' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if ($this->context->lookupByColumn('nhan_vien', 'ma_nhan_vien', $value) === null) {
                        $fail('Nhân viên (mã nhân viên) không tồn tại.');
                    }
                },
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'tieu_de.required' => 'Thiếu tiêu đề.',
            'noi_dung.required' => 'Thiếu nội dung.',
        ];
    }

    public function model(array $row)
    {
        $this->successCount++;
        $nhanVienId = $this->context->lookupByColumn('nhan_vien', 'ma_nhan_vien', $row['ma_nhan_vien']);

        return new BangTin([
            'tieu_de' => $row['tieu_de'],
            'noi_dung' => $row['noi_dung'],
            'hinh_url' => $this->resolveRowImage() ?: ($row['hinh_url'] ?: null),
            'nguoi_tao' => $nhanVienId,
            'nguoi_cap_nhat' => $nhanVienId,
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);
    }
}
