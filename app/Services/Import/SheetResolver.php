<?php

namespace App\Services\Import;

/**
 * Xác định danh sách sheet sẽ được import và thứ tự xử lý, dựa hoàn toàn vào
 * config('import.sheets') — không hard-code tên sheet nào ở đây. Sheet có trong
 * file nhưng không khai báo trong config (hoặc là sheet hướng dẫn) bị bỏ qua.
 * Sheet khai báo trong config nhưng không có trong file cũng tự động bị bỏ qua.
 * Không sheet nào trong 2 trường hợp trên bị coi là lỗi.
 */
class SheetResolver
{
    public function __construct(private readonly SheetInspector $inspector)
    {
    }

    /**
     * @return array<int, array{key: string, class: class-string, order: int}>
     */
    public function resolve(string $path): array
    {
        $sheetsInFile = $this->inspector->listSheetNames($path);
        $configured = config('import.sheets', []);
        $instructionSheet = config('import.instruction_sheet');

        $plan = [];
        foreach ($sheetsInFile as $sheetName) {
            if ($sheetName === $instructionSheet || !isset($configured[$sheetName])) {
                continue;
            }

            $plan[] = [
                'key' => $sheetName,
                'class' => $configured[$sheetName]['class'],
                'order' => $configured[$sheetName]['order'] ?? PHP_INT_MAX,
            ];
        }

        usort($plan, fn ($a, $b) => $a['order'] <=> $b['order']);

        return $plan;
    }
}
