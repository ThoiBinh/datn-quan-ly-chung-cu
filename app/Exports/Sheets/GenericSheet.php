<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(
        private string $sheetTitle,
        private array  $headings,
        private array  $rows,
        private string $headerColor = '1E3A5F',
    ) {}

    public function array(): array
    {
        return $this->rows;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet): void
    {
        $lastCol = $this->columnLetter(count($this->headings));
        $lastRow = count($this->rows) + 1;

        // Header style
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $this->headerColor]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Data rows alternating background
        for ($r = 2; $r <= $lastRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:{$lastCol}{$r}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }
        }

        // Border all cells
        if ($lastRow >= 2) {
            $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Auto filter
        if ($lastRow >= 2) {
            $sheet->setAutoFilter("A1:{$lastCol}1");
        }

        // Row height header
        $sheet->getRowDimension(1)->setRowHeight(22);
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
