<?php

namespace App\Http\Controllers\Manager;

use App\Exports\DashboardExport;
use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\HoaDon;
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
            'can_ho_co_nguoi' => CanHo::where('trang_thai', 1)->count(),
            'can_ho_trong'    => CanHo::where('trang_thai', 2)->count(),
            'tong_cu_dan'     => CuDan::count(),
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

        $yeuCauGanDay = YeuCauCuDan::with('cuDan')
            ->whereIn('trang_thai', [1, 2])
            ->orderByDesc('createdAt')
            ->limit(8)
            ->get();

        $hoaDonQuaHan = HoaDon::with(['canHo.toaNha'])
            ->where('trang_thai', 3)
            ->orderByDesc('han_thanh_toan')
            ->limit(5)
            ->get();

        $tongDuNo = HoaDon::query()->chuaHuy()->selectSumDuNo()->value('du_no') ?? 0;

        $labels = [];
        $doanhThuNam = [];
        $duNoTheoThang = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = 'T' . $i;
            $doanhThuNam[] = HoaDon::where('trang_thai', 2)
                ->whereYear('updatedAt', now()->year)
                ->whereMonth('updatedAt', $i)
                ->sum('tong_tien');
            $duNoTheoThang[] = HoaDon::query()->chuaHuy()
                ->whereYear('updatedAt', now()->year)
                ->whereMonth('updatedAt', $i)
                ->selectSumDuNo()
                ->value('du_no') ?? 0;
        }
        $tongDoanhThuNam = array_sum($doanhThuNam);

        $filterOptions = $this->reportService->getFilterOptions();

        return view('manager.dashboard', compact(
            'stats', 'doanhThuThang', 'doanhThuThangTruoc', 'tyLeTangTruong',
            'yeuCauGanDay', 'hoaDonQuaHan', 'labels', 'doanhThuNam',
            'tongDoanhThuNam', 'tongDuNo', 'duNoTheoThang', 'filterOptions'
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
