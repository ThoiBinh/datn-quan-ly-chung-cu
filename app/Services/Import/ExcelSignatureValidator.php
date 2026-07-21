<?php

namespace App\Services\Import;

/**
 * Kiểm tra magic bytes (file signature) thật của file, không dựa vào phần mở rộng
 * hay MIME type do trình duyệt gửi lên (dễ giả mạo) — chặn các file .php/.exe/.js/
 * .bat/.sh/.zip/.rar đổi đuôi thành .xlsx/.xls.
 */
class ExcelSignatureValidator
{
    // .xlsx (và mọi định dạng Office mới) thực chất là file ZIP.
    private const XLSX_SIGNATURE = "PK\x03\x04";

    // .xls (định dạng OLE Compound File nhị phân cũ).
    private const XLS_SIGNATURE = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";

    public function isValid(string $path): bool
    {
        $handle = fopen($path, 'rb');
        if (!$handle) {
            return false;
        }

        $bytes = fread($handle, 8);
        fclose($handle);

        if ($bytes === false) {
            return false;
        }

        return str_starts_with($bytes, self::XLSX_SIGNATURE) || str_starts_with($bytes, self::XLS_SIGNATURE);
    }
}
