<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DashboardExport;
use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\HoaDon;
use App\Models\NhanVien;
use App\Models\NhatKyHeThong;
use App\Models\PhuongTien;
use App\Models\ToaNha;
use App\Models\YeuCauCuDan;
use App\Services\DashboardReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function __construct(private DashboardReportService $reportService) {}

    public function index()
    {
        $stats = [
            'tong_toa_nha'    => ToaNha::count(),
            'tong_can_ho'     => CanHo::count(),
            'tong_cu_dan'     => CuDan::count(),
            'tong_nhan_vien'  => NhanVien::count(),
            'hoa_don_chua_tt' => HoaDon::where('trang_thai', 1)->count(),
            'hoa_don_qua_han' => HoaDon::where('trang_thai', 3)->count(),
            'yeu_cau_moi'     => YeuCauCuDan::where('trang_thai', 1)->count(),
            'phuong_tien'     => PhuongTien::where('trang_thai', 1)->count(),
        ];

        $doanhThuThang = HoaDon::where('trang_thai', 2)
            ->whereYear('updatedAt', now()->year)
            ->whereMonth('updatedAt', now()->month)
            ->sum('tong_tien');

        $thangTruoc = now()->copy()->subMonth();
        $doanhThuThangTruoc = HoaDon::where('trang_thai', 2)
            ->whereYear('updatedAt', $thangTruoc->year)
            ->whereMonth('updatedAt', $thangTruoc->month)
            ->sum('tong_tien');

        $tyLeTangTruong = $doanhThuThangTruoc > 0
            ? round((($doanhThuThang - $doanhThuThangTruoc) / $doanhThuThangTruoc) * 100, 1)
            : ($doanhThuThang > 0 ? 100 : 0);

        $nhatKy = NhatKyHeThong::with('nguoiThucHien')
            ->orderByDesc('createdAt')
            ->limit(10)
            ->get();

        $yeuCauMoi = YeuCauCuDan::with('cuDan')
            ->where('trang_thai', 1)
            ->orderByDesc('createdAt')
            ->limit(5)
            ->get();

        $labels = [];
        $doanhThuNam = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = 'T' . $i;
            $doanhThuNam[] = HoaDon::where('trang_thai', 2)
                ->whereYear('updatedAt', now()->year)
                ->whereMonth('updatedAt', $i)
                ->sum('tong_tien');
        }
        $tongDoanhThuNam = array_sum($doanhThuNam);

        $filterOptions = $this->reportService->getFilterOptions();

        return view('admin.dashboard', compact(
            'stats', 'doanhThuThang', 'doanhThuThangTruoc', 'tyLeTangTruong',
            'nhatKy', 'yeuCauMoi', 'labels', 'doanhThuNam',
            'tongDoanhThuNam', 'filterOptions'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->parseFilters($request);
        $data    = $this->reportService->getData($filters);

        $pdf = Pdf::loadView('reports.dashboard-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isRemoteEnabled', false)
            ->setOption('margin-top', '10')
            ->setOption('margin-bottom', '15');

        $filename = 'dashboard-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $filters  = $this->parseFilters($request);
        $data     = $this->reportService->getData($filters);
        $filename = 'dashboard-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new DashboardExport($data), $filename);
    }

    private function parseFilters(Request $request): array
    {
        return [
            'period'             => $request->input('period', 'this_month'),
            'date_from'          => $request->input('date_from'),
            'date_to'            => $request->input('date_to'),
            'toa_nha'            => $request->input('toa_nha'),
            'trang_thai_hoa_don' => $request->input('trang_thai_hoa_don'),
            'phuong_thuc_tt'     => $request->input('phuong_thuc_tt'),
            'phi_dich_vu'        => $request->input('phi_dich_vu'),
        ];
    }
}
