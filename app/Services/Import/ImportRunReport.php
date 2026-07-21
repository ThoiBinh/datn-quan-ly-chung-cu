<?php

namespace App\Services\Import;

use Carbon\Carbon;

/**
 * Kết quả của một lượt chạy Universal Import: số liệu theo từng sheet + tổng hợp,
 * dùng để trả JSON cho AJAX, ghi vào Nhật ký hệ thống (audit log) và sinh file
 * import_error.xlsx.
 */
class ImportRunReport
{
    /**
     * @var array<string, array{
     *     total: int, success: int, fail: int, header_error: ?string,
     *     failures: array<int, array{row: int, message: string, data: array}>
     * }>
     */
    public array $sheets = [];

    public string $token;
    public bool $dryRun = false;
    public Carbon $startedAt;
    public ?Carbon $finishedAt = null;
    public string $fileName = '';
    public int $fileSize = 0;
    public string $ip = '';
    public string $userAgent = '';
    public bool $queued = false;
    public string $status = 'running'; // running|completed|completed_with_errors|failed

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->startedAt = now();
    }

    /**
     * Bản đầy đủ (kèm dữ liệu dòng lỗi) dạng mảng thuần — dùng để cache thay vì
     * cache thẳng object, vì config('cache.stores.file.serializable_classes') mặc
     * định là false nên Illuminate\Cache\FileStore sẽ biến MỌI object cache thành
     * __PHP_Incomplete_Class khi đọc lại (chỉ mảng/scalar mới an toàn).
     */
    public function toFullArray(): array
    {
        return array_merge($this->toAuditArray(), [
            'sheets_full' => $this->sheets,
        ]);
    }

    public static function fromArray(array $data): self
    {
        $report = new self($data['token']);
        $report->status = $data['status'];
        $report->dryRun = $data['dry_run'];
        $report->queued = $data['queued'];
        $report->fileName = $data['file_name'];
        $report->fileSize = $data['file_size'];
        $report->ip = $data['ip'] ?? '';
        $report->userAgent = $data['user_agent'] ?? '';
        $report->startedAt = Carbon::parse($data['started_at']);
        $report->finishedAt = $data['finished_at'] ? Carbon::parse($data['finished_at']) : null;
        $report->sheets = $data['sheets_full'] ?? [];

        return $report;
    }

    public function initSheet(string $sheetKey): void
    {
        $this->sheets[$sheetKey] ??= [
            'total' => 0,
            'success' => 0,
            'fail' => 0,
            'header_error' => null,
            'failures' => [],
        ];
    }

    public function markHeaderError(string $sheetKey, string $message): void
    {
        $this->initSheet($sheetKey);
        $this->sheets[$sheetKey]['header_error'] = $message;
    }

    public function addSuccesses(string $sheetKey, int $count): void
    {
        $this->initSheet($sheetKey);
        $this->sheets[$sheetKey]['total'] += $count;
        $this->sheets[$sheetKey]['success'] += $count;
    }

    public function recordFailure(string $sheetKey, int $row, string $message, array $rawRow = []): void
    {
        $this->initSheet($sheetKey);
        $this->sheets[$sheetKey]['total']++;
        $this->sheets[$sheetKey]['fail']++;
        $this->sheets[$sheetKey]['failures'][] = ['row' => $row, 'message' => $message, 'data' => $rawRow];
    }

    public function hasErrors(): bool
    {
        foreach ($this->sheets as $sheet) {
            if ($sheet['fail'] > 0 || $sheet['header_error'] !== null) {
                return true;
            }
        }

        return false;
    }

    /** @return string[] Sheet keys có ít nhất 1 lỗi (dữ liệu hoặc header). */
    public function sheetsWithErrors(): array
    {
        return array_keys(array_filter(
            $this->sheets,
            fn ($s) => $s['fail'] > 0 || $s['header_error'] !== null
        ));
    }

    public function totalSuccess(): int
    {
        return array_sum(array_column($this->sheets, 'success'));
    }

    public function totalFail(): int
    {
        return array_sum(array_column($this->sheets, 'fail'));
    }

    public function finish(string $status): void
    {
        $this->status = $status;
        $this->finishedAt = now();
    }

    public function durationMs(): int
    {
        if (!$this->finishedAt) {
            return 0;
        }

        return (int) $this->startedAt->diffInMilliseconds($this->finishedAt);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'status' => $this->status,
            'dry_run' => $this->dryRun,
            'queued' => $this->queued,
            'file_name' => $this->fileName,
            'file_size' => $this->fileSize,
            'started_at' => $this->startedAt->toDateTimeString(),
            'finished_at' => $this->finishedAt?->toDateTimeString(),
            'duration_ms' => $this->durationMs(),
            'total_success' => $this->totalSuccess(),
            'total_fail' => $this->totalFail(),
            'has_errors' => $this->hasErrors(),
            'sheets' => array_map(fn ($s) => [
                'total' => $s['total'],
                'success' => $s['success'],
                'fail' => $s['fail'],
                'header_error' => $s['header_error'],
                'errors' => array_map(fn ($f) => ['row' => $f['row'], 'message' => $f['message']], $s['failures']),
            ], $this->sheets),
        ];
    }

    /** Payload gọn để lưu vào NhatKyHeThong.gia_tri_moi (không kèm dữ liệu dòng gốc). */
    public function toAuditArray(): array
    {
        return array_merge($this->toArray(), [
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
        ]);
    }
}
