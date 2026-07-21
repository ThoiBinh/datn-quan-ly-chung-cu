<?php

namespace App\Services\Import;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Đọc nhẹ (không load toàn bộ dữ liệu) tên các sheet, dòng tiêu đề và số dòng dữ liệu
 * của một sheet cụ thể. Dùng để: liệt kê sheet thực tế trong file (SheetResolver), kiểm
 * tra header trước khi giao cho Maatwebsite xử lý, và ước lượng tổng số dòng để quyết
 * định chạy đồng bộ hay đẩy vào queue — tất cả không cần nạp toàn bộ file vào RAM.
 */
class SheetInspector
{
    /** @return string[] */
    public function listSheetNames(string $path): array
    {
        return IOFactory::createReaderForFile($path)->listWorksheetNames($path);
    }

    /**
     * @return array{headers: array<int, string>, rowCount: int}
     */
    public function inspect(string $path, string $sheetName): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly([$sheetName]);
        // PhpSpreadsheet chỉ tính getHighestRow()/getHighestDataRow() dựa trên các ô
        // THỰC SỰ đã đọc — nếu chỉ đọc dòng 1 (header), sheet sẽ bị hiểu nhầm là chỉ
        // có 1 dòng. Vì vậy phải đọc thêm cột A của MỌI dòng (rẻ, chỉ 1 cột) để giữ
        // đúng số dòng thật, đồng thời vẫn tránh phải nạp toàn bộ các cột dữ liệu.
        $reader->setReadFilter(new class implements IReadFilter {
            public function readCell($columnAddress, $row, $worksheetName = ''): bool
            {
                return $row === 1 || $columnAddress === 'A';
            }
        });

        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheetByName($sheetName);

        if (!$sheet) {
            return ['headers' => [], 'rowCount' => 0];
        }

        $highestColumn = $sheet->getHighestColumn();
        $headerRow = $sheet->rangeToArray("A1:{$highestColumn}1", null, false, false)[0] ?? [];
        $headers = array_map(
            fn ($v) => is_string($v) ? trim($v) : $v,
            $headerRow
        );

        $rowCount = max(0, $sheet->getHighestDataRow() - 1);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return ['headers' => $headers, 'rowCount' => $rowCount];
    }
}
