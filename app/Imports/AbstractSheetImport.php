<?php

namespace App\Imports;

use App\Imports\Concerns\ResolvesNaturalKeys;
use App\Services\Import\ImportContext;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Nền tảng dùng chung cho mọi Sheet Import: chunk reading + batch insert (đáp ứng
 * yêu cầu hiệu năng 100k dòng), validate từng dòng và TỰ ĐỘNG thu thập lỗi thay vì
 * dừng ngay (SkipsFailures) — khớp với yêu cầu "đọc hết, gom hết lỗi rồi mới quyết
 * định rollback" của UniversalImportService.
 *
 * Rollback/commit KHÔNG được quyết định ở đây: mỗi Import class chỉ lo đọc + ghi
 * dữ liệu hợp lệ, còn toàn bộ lượt import (mọi sheet) chạy trong 1 DB::transaction()
 * do UniversalImportService mở — dry-run hay có lỗi đều rollback ở tầng đó, nên các
 * lớp con không cần biết/rẽ nhánh theo dryRun.
 */
abstract class AbstractSheetImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts,
    WithValidation,
    SkipsOnFailure
{
    use SkipsFailures;
    use ResolvesNaturalKeys;
    use RemembersRowNumber;

    protected int $successCount = 0;

    /** @var array<int, string> [số dòng => đường dẫn ảnh đã trích xuất, xem RowImageExtractor] */
    protected array $rowImages = [];

    public function __construct(protected readonly ImportContext $context)
    {
    }

    public function setRowImages(array $rowImages): void
    {
        $this->rowImages = $rowImages;
    }

    /**
     * Đường dẫn ảnh (đã lưu ở disk "public") được chèn trực tiếp (Insert > Picture)
     * vào ô của dòng đang xử lý, nếu có — dùng cùng với imageColumn().
     */
    protected function resolveRowImage(): ?string
    {
        return $this->rowNumber !== null ? ($this->rowImages[$this->rowNumber] ?? null) : null;
    }

    /**
     * Tên cột (trong expectedHeaders()) có thể được điền bằng ảnh CHÈN TRỰC TIẾP
     * vào Excel thay vì URL/text. Trả về null (mặc định) nếu sheet không hỗ trợ —
     * UniversalImportService chỉ chạy RowImageExtractor cho sheet nào trả về khác null.
     */
    public static function imageColumn(): ?string
    {
        return null;
    }

    /** Thư mục lưu ảnh trích xuất được trên disk "public", chỉ dùng khi imageColumn() != null. */
    public static function imageStoragePrefix(): string
    {
        return 'imports';
    }

    public function chunkSize(): int
    {
        return (int) config('import.chunk_size', 500);
    }

    public function batchSize(): int
    {
        return (int) config('import.batch_size', 500);
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Kiểm tra + đánh dấu trùng lặp khóa tự nhiên NGAY TRONG file cho TOÀN BỘ batch
     * đang được validate cùng lúc. Maatwebsite validate cả 1 batch (mặc định 500
     * dòng, xem WithBatchInserts::batchSize()) trong 1 lượt bằng validation mảng
     * (rules() được áp dụng dạng "*.field"), nên bắt buộc phải tự lặp qua
     * $validator->getData() ở đây (theo đúng thứ tự dòng) thay vì tách rời việc
     * "kiểm tra" ở rules() và "đánh dấu đã thấy" ở model() — nếu không, 2 dòng
     * trùng nhau nằm trong CÙNG 1 batch sẽ không bị bắt (vì model() của dòng trước
     * chưa kịp chạy khi dòng sau đang được validate).
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    protected function checkDuplicateInBatch(
        $validator,
        string $field,
        string $errorField,
        callable $keyExtractor,
        string $message
    ): void {
        foreach ($validator->getData() as $rowIndex => $row) {
            if (!is_array($row) || $validator->errors()->has("{$rowIndex}.*")) {
                continue;
            }

            $key = $keyExtractor($row);
            if ($key === null || $key === '') {
                continue;
            }

            if ($this->isDuplicateInFile($field, $key)) {
                $validator->errors()->add("{$rowIndex}.{$errorField}", $message);

                continue;
            }

            $this->markSeenInFile($field, $key);
        }
    }

    /**
     * Danh sách cột bắt buộc phải có trong header (đã chuẩn hoá theo
     * HeadingRowFormatter: chữ thường, khoảng trắng -> "_"). Dùng để kiểm tra header
     * TRƯỚC khi giao sheet cho Maatwebsite xử lý (xem SheetInspector).
     *
     * @return string[]
     */
    abstract public static function expectedHeaders(): array;

    /**
     * 1 dòng dữ liệu mẫu (cùng thứ tự với expectedHeaders()) để sinh file template —
     * nhờ vậy template luôn khớp với sheet import mà không cần sửa gì ở ImportTemplateExport
     * khi thêm entity mới.
     *
     * @return array<int, string|int|float>
     */
    abstract public static function sampleRow(): array;
}
