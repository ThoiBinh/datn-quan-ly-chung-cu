<?php

namespace App\Services\Import;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Bọc 1 Sheet Import cụ thể để Maatwebsite chỉ đọc đúng sheet có tên $sheetName
 * trong workbook nhiều sheet, bất kể sheet đó nằm ở vị trí vật lý nào trong file.
 * UniversalImportService gọi Excel::import() một lần cho MỖI sheet (theo đúng thứ
 * tự "order" trong config, không theo thứ tự vật lý trong file) — dùng wrapper này
 * để tránh phụ thuộc vào cách Maatwebsite tự sắp xếp khi truyền nhiều sheet cùng lúc.
 */
class SingleSheetImport implements WithMultipleSheets
{
    public function __construct(
        private readonly string $sheetName,
        private readonly object $sheetImport,
    ) {
    }

    public function sheets(): array
    {
        return [$this->sheetName => $this->sheetImport];
    }
}
