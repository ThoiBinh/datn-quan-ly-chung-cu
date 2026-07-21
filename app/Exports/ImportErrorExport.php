<?php

namespace App\Exports;

use App\Exports\Sheets\ImportTemplateSheet;
use App\Services\Import\ImportRunReport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * File "import_error.xlsx": giữ nguyên dữ liệu gốc của các dòng lỗi + thêm cột
 * "Lỗi" mô tả nguyên nhân — 1 sheet cho mỗi sheet gốc có lỗi (dữ liệu hoặc header).
 */
class ImportErrorExport implements WithMultipleSheets
{
    public function __construct(private readonly ImportRunReport $report)
    {
    }

    public function sheets(): array
    {
        $sheets = [];
        $configured = config('import.sheets', []);

        foreach ($this->report->sheetsWithErrors() as $sheetKey) {
            $sheet = $this->report->sheets[$sheetKey];

            if ($sheet['header_error'] !== null) {
                $sheets[$sheetKey] = new ImportTemplateSheet($sheetKey, ['Lỗi'], [[$sheet['header_error']]]);

                continue;
            }

            /** @var class-string<\App\Imports\AbstractSheetImport>|null $class */
            $class = $configured[$sheetKey]['class'] ?? null;
            $headers = $class ? $class::expectedHeaders() : [];

            $rows = array_map(function ($failure) use ($headers) {
                $values = array_map(fn ($h) => $failure['data'][$h] ?? '', $headers);
                $values[] = $failure['message'];

                return $values;
            }, $sheet['failures']);

            $sheets[$sheetKey] = new ImportTemplateSheet($sheetKey, [...$headers, 'Lỗi'], $rows);
        }

        return $sheets;
    }
}
