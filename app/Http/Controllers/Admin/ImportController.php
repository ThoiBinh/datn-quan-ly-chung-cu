<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ImportErrorExport;
use App\Exports\ImportTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportUploadRequest;
use App\Jobs\ProcessUniversalImportJob;
use App\Models\NhatKyHeThong;
use App\Services\AuditLogService;
use App\Services\UniversalImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Trang "Import dữ liệu" cho Admin — chỉ điều phối HTTP, toàn bộ nghiệp vụ nằm ở
 * UniversalImportService/SheetResolver/Import classes (xem app/Services/Import,
 * app/Imports). Không hard-code entity nào ở đây.
 */
class ImportController extends Controller
{
    public function __construct(private readonly UniversalImportService $importService)
    {
        // Lớp chặn thứ 2 ở tầng Controller (ngoài AdminMiddleware đã áp cho toàn bộ
        // group Route::prefix('admin')) — phòng trường hợp route này lỡ bị đăng ký
        // nhầm ngoài group đó trong tương lai. Không thay đổi logic AdminMiddleware.
        abort_unless(auth('nhanvien')->user()?->isAdmin(), 403);
    }

    public function index()
    {
        $logs = NhatKyHeThong::where('bang_tac_dong', 'import_excel')
            ->where('hanh_dong', 'IMPORT')
            ->with('nguoiThucHien')
            ->orderByDesc('thoi_gian')
            ->paginate(10);

        $logs->getCollection()->transform(function (NhatKyHeThong $log) {
            $log->payload = $log->gia_tri_moi ? json_decode($log->gia_tri_moi, true) : [];

            return $log;
        });

        return view('admin.import.index', [
            'logs' => $logs,
            'maxFileSizeKb' => (int) config('import.max_file_size_kb', 51200),
            'queueThreshold' => (int) config('import.queue_threshold', 5000),
            'sheetKeys' => array_keys(config('import.sheets', [])),
        ]);
    }

    public function downloadTemplate()
    {
        return Excel::download(new ImportTemplateExport(), 'template_import.xlsx');
    }

    public function run(ImportUploadRequest $request)
    {
        $disk = config('import.disk');
        $dryRun = $request->boolean('dry_run');
        $file = $request->file('file');

        $storedPath = $file->store(config('import.upload_path'), $disk);
        $fullPath = Storage::disk($disk)->path($storedPath);

        $token = (string) Str::uuid();
        $meta = [
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'ip' => (string) $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ];

        $totalRows = $this->importService->estimateTotalRows($fullPath);
        $shouldQueue = $totalRows > (int) config('import.queue_threshold', 5000);

        if ($shouldQueue) {
            Cache::put("import_progress:{$token}", array_merge($meta, [
                'token' => $token,
                'status' => 'queued',
                'percent' => 0,
                'dry_run' => $dryRun,
                'queued' => true,
                'total_success' => 0,
                'total_fail' => 0,
                'has_errors' => false,
                'sheets' => [],
            ]), config('import.progress_ttl', 3600));

            ProcessUniversalImportJob::dispatch(
                $fullPath,
                $token,
                $dryRun,
                (int) auth('nhanvien')->id(),
                $meta['file_name'],
                $meta['file_size'],
                $meta['ip'],
                $meta['user_agent'],
            );

            return response()->json(['queued' => true, 'token' => $token]);
        }

        $report = $this->importService->run($fullPath, $token, $dryRun, $meta);

        if (!$dryRun) {
            AuditLogService::log('IMPORT', 'import_excel', 0, null, $report->toAuditArray());
        }

        Storage::disk($disk)->delete($storedPath);

        return response()->json(array_merge($report->toArray(), ['queued' => false]));
    }

    public function status(string $token)
    {
        $progress = Cache::get("import_progress:{$token}");

        if (!$progress) {
            return response()->json(['error' => 'Không tìm thấy tiến trình import.'], 404);
        }

        return response()->json($progress);
    }

    public function downloadErrors(string $token)
    {
        $report = $this->importService->getCachedReport($token);

        if (!$report || !$report->hasErrors()) {
            abort(404, 'Không có file lỗi để tải.');
        }

        return Excel::download(new ImportErrorExport($report), 'import_error.xlsx');
    }
}
