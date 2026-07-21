<?php

namespace App\Services;

use App\Services\Import\ImportContext;
use App\Services\Import\ImportRunReport;
use App\Services\Import\RowImageExtractor;
use App\Services\Import\SheetInspector;
use App\Services\Import\SheetResolver;
use App\Services\Import\SingleSheetImport;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Điều phối toàn bộ 1 lượt Universal Import: xác định sheet cần xử lý + thứ tự
 * (SheetResolver), kiểm tra header từng sheet trước khi giao cho Maatwebsite,
 * chạy TOÀN BỘ trong 1 DB::transaction() — đọc hết mọi sheet, gom hết lỗi, chỉ
 * rollback/commit MỘT LẦN ở cuối. Dry-run dùng lại đúng luồng này, chỉ khác ở
 * bước cuối luôn rollback dù không có lỗi.
 */
class UniversalImportService
{
    public function __construct(
        private readonly SheetResolver $resolver,
        private readonly SheetInspector $inspector,
        private readonly RowImageExtractor $imageExtractor,
    ) {
    }

    /** Tổng số dòng dữ liệu (mọi sheet sẽ được xử lý) — dùng để quyết định chạy đồng bộ hay đẩy vào queue. */
    public function estimateTotalRows(string $path): int
    {
        $total = 0;
        foreach ($this->resolver->resolve($path) as $sheetPlan) {
            $total += $this->inspector->inspect($path, $sheetPlan['key'])['rowCount'];
        }

        return $total;
    }

    /** @param array{file_name?: string, file_size?: int, ip?: string, user_agent?: string, queued?: bool} $meta */
    public function run(string $path, string $token, bool $dryRun, array $meta = []): ImportRunReport
    {
        $report = new ImportRunReport($token);
        $report->dryRun = $dryRun;
        $report->fileName = $meta['file_name'] ?? '';
        $report->fileSize = $meta['file_size'] ?? 0;
        $report->ip = $meta['ip'] ?? '';
        $report->userAgent = $meta['user_agent'] ?? '';
        $report->queued = $meta['queued'] ?? false;
        $this->updateProgress($token, $report, 0);

        $plan = $this->resolver->resolve($path);
        $context = new ImportContext();

        DB::beginTransaction();

        try {
            foreach ($plan as $i => $sheetPlan) {
                $this->processSheet($path, $sheetPlan, $context, $report);
                $this->updateProgress($token, $report, (int) round((($i + 1) / max(1, count($plan))) * 100));
            }

            if ($dryRun || $report->hasErrors()) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $report->finish('failed');
            $this->updateProgress($token, $report, 100);

            throw $e;
        }

        $report->finish($report->hasErrors() ? 'completed_with_errors' : 'completed');
        $this->updateProgress($token, $report, 100);

        // Lưu bản đầy đủ (kèm dữ liệu dòng lỗi, dạng mảng thuần — KHÔNG cache thẳng
        // object, xem ImportRunReport::toFullArray()) để sinh import_error.xlsx sau
        // này qua route riêng; cache "progress" ở trên chỉ chứa bản tóm tắt cho polling.
        Cache::put("import_report:{$token}", $report->toFullArray(), config('import.progress_ttl', 3600));

        return $report;
    }

    public function getCachedReport(string $token): ?ImportRunReport
    {
        $data = Cache::get("import_report:{$token}");

        return $data ? ImportRunReport::fromArray($data) : null;
    }

    private function processSheet(string $path, array $sheetPlan, ImportContext $context, ImportRunReport $report): void
    {
        $sheetKey = $sheetPlan['key'];
        /** @var class-string<\App\Imports\AbstractSheetImport> $class */
        $class = $sheetPlan['class'];

        $inspected = $this->inspector->inspect($path, $sheetKey);
        $missing = array_diff($class::expectedHeaders(), $inspected['headers']);

        if (!empty($missing)) {
            $report->markHeaderError($sheetKey, 'Thiếu cột: ' . implode(', ', $missing));

            return;
        }

        $report->initSheet($sheetKey);

        $import = new $class($context);

        // Sheet có cột ảnh (ví dụ BangTin.hinh_url) cho phép chèn ảnh trực tiếp
        // (Insert > Picture) thay vì dán URL — trích xuất TRƯỚC khi giao cho
        // Maatwebsite, vì đọc theo dòng (WithHeadingRow/ToModel) không thấy được
        // ảnh nhúng (chỉ có trong pass đọc đầy đủ, không phải readDataOnly).
        if ($class::imageColumn() !== null) {
            $import->setRowImages($this->imageExtractor->extract($path, $sheetKey, $class::imageStoragePrefix()));
        }

        Excel::import(new SingleSheetImport($sheetKey, $import), $path);

        $report->addSuccesses($sheetKey, $import->getSuccessCount());

        // Maatwebsite phát sinh 1 Failure RIÊNG cho MỖI cột lỗi (không phải 1 Failure/dòng)
        // — gộp lại theo số dòng để mỗi dòng lỗi chỉ xuất hiện đúng 1 lần trong báo cáo.
        $byRow = [];
        foreach ($import->failures() as $failure) {
            $byRow[$failure->row()]['messages'][] = implode(' ', $failure->errors());
            $byRow[$failure->row()]['data'] = $failure->values();
        }

        foreach ($byRow as $row => $info) {
            $report->recordFailure($sheetKey, $row, implode('; ', array_filter($info['messages'])), $info['data']);
        }
    }

    public function updateProgress(string $token, ImportRunReport $report, int $percent): void
    {
        Cache::put(
            "import_progress:{$token}",
            array_merge($report->toArray(), ['percent' => $percent]),
            config('import.progress_ttl', 3600)
        );
    }
}
