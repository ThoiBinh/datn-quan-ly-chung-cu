<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles, WithEvents, WithStrictNullComparison
{
    /** @var array<int, array{cells: array, type: string}> */
    private array $layout = [];

    public function __construct(
        private string $sheetTitle,
        private array  $headings,
        private array  $rows,
        private string $headerColor = '1E3A5F',
        private array  $companyInfo = [],
        private ?string $reportTitle = null,
        private array  $meta = [],
    ) {
        $this->layout = $this->buildLayout();
    }

    public function array(): array
    {
        return array_map(fn ($row) => $row['cells'], $this->layout);
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    /**
     * Dựng toàn bộ layout: letterhead công ty (nếu có) → tiêu đề báo cáo → meta → header cột → data.
     * Cột A được chừa trống ở các dòng letterhead để có chỗ chèn logo (không đè lên chữ),
     * nội dung letterhead bắt đầu từ cột B. Header cột và data giữ nguyên bắt đầu từ cột A như cũ.
     */
    private function buildLayout(): array
    {
        $layout = [];
        $hasCompanyBlock = false;

        if (!empty($this->companyInfo['ten_chung_cu'])) {
            $layout[] = ['cells' => ['', $this->companyInfo['ten_chung_cu']], 'type' => 'company_name'];
            $hasCompanyBlock = true;
        }

        if (!empty($this->companyInfo['dia_chi'])) {
            $layout[] = ['cells' => ['', 'Địa chỉ: ' . $this->companyInfo['dia_chi']], 'type' => 'company_sub'];
            $hasCompanyBlock = true;
        }

        $contactParts = array_filter([
            !empty($this->companyInfo['hotline'])
                ? 'Hotline: ' . $this->companyInfo['hotline']
                : (!empty($this->companyInfo['so_dien_thoai']) ? 'SĐT: ' . $this->companyInfo['so_dien_thoai'] : null),
            !empty($this->companyInfo['email']) ? 'Email: ' . $this->companyInfo['email'] : null,
            !empty($this->companyInfo['website']) ? 'Website: ' . $this->companyInfo['website'] : null,
        ]);
        if (!empty($contactParts)) {
            $layout[] = ['cells' => ['', implode('     |     ', $contactParts)], 'type' => 'company_sub'];
            $hasCompanyBlock = true;
        }

        if ($hasCompanyBlock) {
            $layout[] = ['cells' => [''], 'type' => 'blank'];
        }

        $layout[] = ['cells' => ['', $this->reportTitle ?? mb_strtoupper($this->sheetTitle)], 'type' => 'title'];

        $metaParts = array_filter([
            !empty($this->meta['khoang_thoi_gian']) ? 'Kỳ báo cáo: ' . $this->meta['khoang_thoi_gian'] : null,
            !empty($this->meta['thoi_gian_xuat']) ? 'Xuất lúc: ' . $this->meta['thoi_gian_xuat'] : null,
            !empty($this->meta['nguoi_xuat']) ? 'Người xuất: ' . $this->meta['nguoi_xuat'] : null,
        ]);
        if (!empty($metaParts)) {
            $layout[] = ['cells' => ['', implode('     |     ', $metaParts)], 'type' => 'meta'];
        }

        $layout[] = ['cells' => [''], 'type' => 'blank'];
        $layout[] = ['cells' => $this->headings, 'type' => 'heading'];

        foreach ($this->rows as $row) {
            $layout[] = ['cells' => $row, 'type' => 'data'];
        }

        return $layout;
    }

    public function styles(Worksheet $sheet): void
    {
        $lastCol = $this->columnLetter(count($this->headings));
        $lastRow = count($this->layout);
        $dataParity = 0;

        foreach ($this->layout as $i => $entry) {
            $rowNum = $i + 1;

            switch ($entry['type']) {
                case 'company_name':
                    $sheet->mergeCells("B{$rowNum}:{$lastCol}{$rowNum}");
                    $sheet->getStyle("B{$rowNum}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 18, 'color' => ['rgb' => $this->headerColor]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($rowNum)->setRowHeight(26);
                    break;

                case 'company_sub':
                    $sheet->mergeCells("B{$rowNum}:{$lastCol}{$rowNum}");
                    $sheet->getStyle("B{$rowNum}")->applyFromArray([
                        'font'      => ['size' => 10, 'color' => ['rgb' => '475569']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ]);
                    break;

                case 'title':
                    $sheet->mergeCells("B{$rowNum}:{$lastCol}{$rowNum}");
                    $sheet->getStyle("B{$rowNum}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E293B']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($rowNum)->setRowHeight(24);
                    break;

                case 'meta':
                    $sheet->mergeCells("B{$rowNum}:{$lastCol}{$rowNum}");
                    $sheet->getStyle("B{$rowNum}")->applyFromArray([
                        'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '64748B']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    break;

                case 'heading':
                    $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $this->headerColor]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($rowNum)->setRowHeight(22);
                    $sheet->freezePane("A" . ($rowNum + 1));
                    $sheet->setAutoFilter("A{$rowNum}:{$lastCol}{$rowNum}");
                    break;

                case 'data':
                    if ($dataParity % 2 === 1) {
                        $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F8FAFC');
                    }
                    $dataParity++;
                    break;
            }
        }

        // Border quanh toàn bộ vùng header cột + dữ liệu (không bao gồm letterhead phía trên).
        $headingRow = $this->findRowNumberByType('heading');
        if ($headingRow !== null && $lastRow >= $headingRow) {
            $sheet->getStyle("A{$headingRow}:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $logoPath = $this->companyInfo['logo_path'] ?? null;

                if (!$logoPath || !is_readable($logoPath) || @getimagesize($logoPath) === false) {
                    return;
                }

                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath($logoPath);
                $drawing->setHeight(50);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(4);
                $drawing->setOffsetY(4);
                $drawing->setWorksheet($event->sheet->getDelegate());
            },
        ];
    }

    private function findRowNumberByType(string $type): ?int
    {
        foreach ($this->layout as $i => $entry) {
            if ($entry['type'] === $type) {
                return $i + 1;
            }
        }

        return null;
    }

    private function columnLetter(int $n): string
    {
        $letters = '';
        while ($n > 0) {
            $n--;
            $letters = chr(65 + ($n % 26)) . $letters;
            $n = intdiv($n, 26);
        }
        return $letters;
    }
}
