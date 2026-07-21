<?php

namespace App\Imports\Concerns;

/**
 * Phát hiện trùng lặp khóa tự nhiên NGAY TRONG file đang import (ví dụ 2 dòng cùng
 * "tien_to" trong sheet ToaNha). Rule::unique của DB không bắt được trường hợp này
 * vì chưa dòng nào được ghi vào DB tại thời điểm validate. Đánh dấu "đã thấy" chỉ
 * được gọi trong model() — tức là chỉ SAU khi dòng đã qua toàn bộ validate khác,
 * tránh đánh dấu nhầm một khóa là trùng trong khi dòng sở hữu nó thực ra bị loại
 * vì lý do khác.
 */
trait ResolvesNaturalKeys
{
    /** @var array<string, array<string, true>> */
    protected array $seenKeys = [];

    protected function isDuplicateInFile(string $field, ?string $key): bool
    {
        if ($key === null || $key === '') {
            return false;
        }

        return isset($this->seenKeys[$field][$key]);
    }

    protected function markSeenInFile(string $field, ?string $key): void
    {
        if ($key === null || $key === '') {
            return;
        }

        $this->seenKeys[$field][$key] = true;
    }
}
