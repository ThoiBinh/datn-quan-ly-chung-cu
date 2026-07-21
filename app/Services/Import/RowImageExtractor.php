<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

/**
 * Trích xuất ảnh được CHÈN TRỰC TIẾP (Insert > Picture) vào 1 sheet, khớp mỗi ảnh
 * với dòng dữ liệu chứa nó (theo số dòng của ô neo ảnh), lưu vào disk "public" và
 * trả về map [số dòng => đường dẫn đã lưu]. Chỉ dùng cho sheet có khai báo
 * AbstractSheetImport::imageColumn() (ví dụ BangTin) — không áp dụng cho các sheet
 * dữ liệu lớn khác, vì phải tắt readDataOnly để PhpSpreadsheet nạp được drawing,
 * tốn bộ nhớ hơn cách đọc nhẹ thông thường.
 */
class RowImageExtractor
{
    /** @return array<int, string> */
    public function extract(string $path, string $sheetName, string $storagePrefix): array
    {
        $reader = IOFactory::createReaderForFile($path);

        if (method_exists($reader, 'setLoadSheetsOnly')) {
            $reader->setLoadSheetsOnly([$sheetName]);
        }

        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheetByName($sheetName);

        if (!$sheet) {
            return [];
        }

        $map = [];

        foreach ($sheet->getDrawingCollection() as $drawing) {
            if (!$drawing instanceof Drawing) {
                continue;
            }

            // getPath() có thể là 1 chuỗi "zip://...#xl/media/xxx.png" (ảnh gốc nằm
            // ngay trong file xlsx, chưa giải nén ra đĩa) — is_readable()/filesize()
            // không nhận diện đúng loại path này, phải đọc thẳng bằng file_get_contents().
            $content = @file_get_contents($drawing->getPath());
            if ($content === false || $content === '') {
                continue;
            }

            if (!preg_match('/(\d+)$/', $drawing->getCoordinates(), $m)) {
                continue;
            }
            $row = (int) $m[1];

            $extension = strtolower(pathinfo($drawing->getPath(), PATHINFO_EXTENSION)) ?: 'png';
            $filename = $storagePrefix . '/' . Str::random(24) . '.' . $extension;
            Storage::disk('public')->put($filename, $content);

            $map[$row] = $filename;
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $map;
    }
}
