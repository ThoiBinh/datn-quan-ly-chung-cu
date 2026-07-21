<?php

namespace App\Jobs;

use App\Services\AuditLogService;
use App\Services\UniversalImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

/**
 * Chạy UniversalImportService::run() ở nền khi file có nhiều dòng (xem
 * config('import.queue_threshold')). Cố tình bọc TOÀN BỘ lượt import (mọi sheet)
 * trong 1 job duy nhất — không dùng cơ chế ShouldQueue của Maatwebsite cho từng
 * sheet — vì 1 DB::transaction() không thể trải dài qua nhiều queue job khác nhau,
 * mà yêu cầu là "rollback toàn bộ file nếu có lỗi ở bất kỳ đâu".
 */
class ProcessUniversalImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 3600;

    /** @param string $fullPath Đường dẫn tuyệt đối trên đĩa (không phải path tương đối theo disk). */
    public function __construct(
        private readonly string $fullPath,
        private readonly string $token,
        private readonly bool $dryRun,
        private readonly int $nhanVienId,
        private readonly string $fileName,
        private readonly int $fileSize,
        private readonly string $ip,
        private readonly string $userAgent,
    ) {
    }

    public function handle(UniversalImportService $service): void
    {
        // Job chạy nền, không có phiên đăng nhập — đăng nhập tạm cho đúng nhân viên
        // đã bấm Import để auth('nhanvien')->id() (dùng trong các Import class và
        // AuditLogService) trả về đúng giá trị như khi chạy đồng bộ.
        Auth::guard('nhanvien')->onceUsingId($this->nhanVienId);

        $report = $service->run($this->fullPath, $this->token, $this->dryRun, [
            'file_name' => $this->fileName,
            'file_size' => $this->fileSize,
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
            'queued' => true,
        ]);

        if (!$this->dryRun) {
            AuditLogService::log('IMPORT', 'import_excel', 0, null, $report->toAuditArray());
        }

        File::delete($this->fullPath);
    }

    public function failed(\Throwable $exception): void
    {
        File::delete($this->fullPath);
    }
}
