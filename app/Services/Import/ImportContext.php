<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\DB;

/**
 * Tra cứu khóa tự nhiên (business key) -> id, dùng để các sheet sau resolve khóa
 * ngoại tới dữ liệu do sheet trước tạo ra trong CÙNG một lượt import (kể cả khi
 * chưa commit — cùng transaction/connection nên đọc được dữ liệu vừa insert).
 *
 * - lookupByColumn(): dùng cho khóa tự nhiên là cột thật trong DB (tien_to,
 *   ten_loai_can_ho, ma_nhan_vien, so_can_ho, ...). Luôn truy vấn lại DB (không
 *   cache giữa các sheet) để thấy được các dòng vừa được sheet trước insert.
 * - remember()/recall(): dùng cho khóa tự nhiên CHỈ tồn tại trong file Excel, không
 *   có cột tương ứng trong DB (ví dụ ma_cu_dan ở sheet CuDan) — phải ghi nhớ thủ
 *   công ngay sau khi insert từng dòng.
 */
class ImportContext
{
    /** @var array<string, array<string, int|null>> */
    private array $memory = [];

    public function lookupByColumn(string $table, string $keyColumn, string $keyValue, bool $withSoftDeletes = true): ?int
    {
        if ($keyValue === '' || $keyValue === null) {
            return null;
        }

        $query = DB::table($table)->where($keyColumn, $keyValue);

        if ($withSoftDeletes) {
            $query->whereNull('deletedAt');
        }

        $id = $query->value('id');

        return $id !== null ? (int) $id : null;
    }

    public function remember(string $namespace, string $key, int $id): void
    {
        $this->memory[$namespace][$key] = $id;
    }

    public function recall(string $namespace, string $key): ?int
    {
        return $this->memory[$namespace][$key] ?? null;
    }
}
