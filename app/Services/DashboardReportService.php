<?php

namespace App\Services;

use App\Models\BangTin;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\HoaDon;
use App\Models\LichSuThanhToan;
use App\Models\LoaiPhuongTien;
use App\Models\NhanVien;
use App\Models\PhiDichVu;
use App\Models\PhuongTien;
use App\Models\ThongBao;
use App\Models\ToaNha;
use App\Models\YeuCauCuDan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardReportService
{
    public function getData(array $filters): array
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($filters);

        return [
            'thong_tin'            => $this->getThongTin($filters, $dateFrom, $dateTo),
            'thong_ke_he_thong'    => $this->getThongKeHeThong(),
            'thong_ke_hoa_don'     => $this->getThongKeHoaDon($filters, $dateFrom, $dateTo),
            'thong_ke_thanh_toan'  => $this->getThongKeThanhToan($filters, $dateFrom, $dateTo),
            'thong_ke_phi_dich_vu' => $this->getThongKePhiDichVu($filters, $dateFrom, $dateTo),
            'thong_ke_can_ho'      => $this->getThongKeCanHo($filters),
            'thong_ke_phuong_tien' => $this->getThongKePhuongTien(),
            'thong_ke_yeu_cau'     => $this->getThongKeYeuCau($filters, $dateFrom, $dateTo),
            'top_data'             => $this->getTopData($filters, $dateFrom, $dateTo),
        ];
    }

    public function getFilterOptions(): array
    {
        return [
            'toa_nha' => ToaNha::select('id', 'ten_toa_nha')->orderBy('ten_toa_nha')->get(),
            'phuong_thuc_tt' => LichSuThanhToan::select('phuong_thuc_thanh_toan')
                ->whereNotNull('phuong_thuc_thanh_toan')
                ->distinct()
                ->orderBy('phuong_thuc_thanh_toan')
                ->pluck('phuong_thuc_thanh_toan'),
            'phi_dich_vu' => PhiDichVu::select('id', 'ten_phi_dich_vu')->orderBy('ten_phi_dich_vu')->get(),
            'trang_thai_hoa_don' => [
                HoaDon::TRANG_THAI_CHUA_THANH_TOAN => 'Chưa thanh toán',
                HoaDon::TRANG_THAI_DA_THANH_TOAN   => 'Đã thanh toán',
                HoaDon::TRANG_THAI_QUA_HAN          => 'Quá hạn',
                HoaDon::TRANG_THAI_DA_HUY           => 'Đã hủy',
            ],
        ];
    }

    public function resolveDateRange(array $filters): array
    {
        $period = $filters['period'] ?? 'this_month';

        return match ($period) {
            'today'        => [today()->startOfDay(), today()->endOfDay()],
            'this_week'    => [now()->startOfWeek(), now()->endOfWeek()],
            'this_month'   => [now()->startOfMonth(), now()->endOfMonth()],
            'this_quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'this_year'    => [now()->startOfYear(), now()->endOfYear()],
            'custom'       => [
                !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : now()->startOfMonth(),
                !empty($filters['date_to'])   ? Carbon::parse($filters['date_to'])->endOfDay()   : now()->endOfMonth(),
            ],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    public function getKhoangThoiGianLabel(array $filters): string
    {
        return match ($filters['period'] ?? 'this_month') {
            'today'        => 'Hôm nay (' . today()->format('d/m/Y') . ')',
            'this_week'    => 'Tuần này (' . now()->startOfWeek()->format('d/m') . ' – ' . now()->endOfWeek()->format('d/m/Y') . ')',
            'this_month'   => 'Tháng ' . now()->month . '/' . now()->year,
            'this_quarter' => 'Quý ' . now()->quarter . '/' . now()->year,
            'this_year'    => 'Năm ' . now()->year,
            'custom'       => ($filters['date_from'] ?? '?') . ' → ' . ($filters['date_to'] ?? '?'),
            default        => 'Tháng ' . now()->month . '/' . now()->year,
        };
    }

    // ─── PRIVATE SECTIONS ─────────────────────────────────────────────────────

    private function getThongTin(array $filters, Carbon $dateFrom, Carbon $dateTo): array
    {
        $user = auth('nhanvien')->user();
        return [
            'ten_he_thong'     => config('app.name', 'Quản Lý Chung Cư'),
            'thoi_gian_xuat'   => now()->format('d/m/Y H:i:s'),
            'nguoi_xuat'       => $user?->ho_ten ?? 'N/A',
            'khoang_thoi_gian' => $this->getKhoangThoiGianLabel($filters),
            'date_from'        => $dateFrom->format('d/m/Y'),
            'date_to'          => $dateTo->format('d/m/Y'),
        ];
    }

    private function getThongKeHeThong(): array
    {
        return [
            'tong_cu_dan'      => CuDan::count(),
            'tong_nhan_vien'   => NhanVien::count(),
            'tong_toa_nha'     => ToaNha::count(),
            'tong_can_ho'      => CanHo::count(),
            'tong_phuong_tien' => PhuongTien::count(),
            'tong_hoa_don'     => HoaDon::count(),
            'tong_yeu_cau'     => YeuCauCuDan::count(),
            'tong_thong_bao'   => ThongBao::count(),
            'tong_ban_tin'     => BangTin::count(),
        ];
    }

    private function getThongKeHoaDon(array $filters, Carbon $dateFrom, Carbon $dateTo): array
    {
        $query = HoaDon::query()->whereBetween('createdAt', [$dateFrom, $dateTo]);

        if (!empty($filters['toa_nha'])) {
            $tnId = (int) $filters['toa_nha'];
            $query->whereHas('canHo', fn($q) => $q->where('toa_nha', $tnId));
        }
        if (!empty($filters['trang_thai_hoa_don'])) {
            $query->where('trang_thai', (int) $filters['trang_thai_hoa_don']);
        }

        $rows = $query->get(['trang_thai', 'tong_tien', 'so_tien_da_thanh_toan']);

        $tongTien   = $rows->sum('tong_tien');
        $tongDaTT   = $rows->sum('so_tien_da_thanh_toan');

        return [
            'tong'            => $rows->count(),
            'da_thanh_toan'   => $rows->where('trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)->count(),
            'chua_thanh_toan' => $rows->where('trang_thai', HoaDon::TRANG_THAI_CHUA_THANH_TOAN)->count(),
            'qua_han'         => $rows->where('trang_thai', HoaDon::TRANG_THAI_QUA_HAN)->count(),
            'da_huy'          => $rows->where('trang_thai', HoaDon::TRANG_THAI_DA_HUY)->count(),
            'tong_tien'       => (float) $tongTien,
            'tong_tien_da_tt' => (float) $tongDaTT,
            'tong_tien_con_no'=> (float) max(0, $tongTien - $tongDaTT),
        ];
    }

    private function getThongKeThanhToan(array $filters, Carbon $dateFrom, Carbon $dateTo): array
    {
        $base = LichSuThanhToan::query();

        if (!empty($filters['toa_nha'])) {
            $tnId = (int) $filters['toa_nha'];
            $base->whereHas('hoaDon.canHo', fn($q) => $q->where('toa_nha', $tnId));
        }
        if (!empty($filters['phuong_thuc_tt'])) {
            $base->where('phuong_thuc_thanh_toan', $filters['phuong_thuc_tt']);
        }

        $filtered = (clone $base)->whereBetween('ngay_thanh_toan', [$dateFrom, $dateTo]);

        return [
            'tong_giao_dich'    => $filtered->count(),
            'tong_doanh_thu'    => (float) $filtered->sum('so_tien'),
            'doanh_thu_hom_nay' => (float) (clone $base)->whereDate('ngay_thanh_toan', today())->sum('so_tien'),
            'doanh_thu_thang'   => (float) (clone $base)->whereYear('ngay_thanh_toan', now()->year)->whereMonth('ngay_thanh_toan', now()->month)->sum('so_tien'),
            'doanh_thu_nam'     => (float) (clone $base)->whereYear('ngay_thanh_toan', now()->year)->sum('so_tien'),
        ];
    }

    private function getThongKePhiDichVu(array $filters, Carbon $dateFrom, Carbon $dateTo): array
    {
        $phiList = PhiDichVu::with(['loaiTinhPhi', 'donViTinh'])->get();

        if (!empty($filters['phi_dich_vu'])) {
            $phiList = $phiList->where('id', (int) $filters['phi_dich_vu']);
        }

        $result = [];
        foreach ($phiList as $phi) {
            $soCanHo = DB::table('can_ho_phi_dich_vu')
                ->join('can_ho', 'can_ho_phi_dich_vu.can_ho', '=', 'can_ho.id')
                ->where('can_ho_phi_dich_vu.phi_dich_vu', $phi->id)
                ->whereNull('can_ho.deletedAt')
                ->count();

            $dtQuery = DB::table('chi_tiet_hoa_don as ct')
                ->join('hoa_don as hd', 'ct.hoa_don', '=', 'hd.id')
                ->where('ct.ten_phi_dich_vu', $phi->ten_phi_dich_vu)
                ->where('hd.trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)
                ->whereBetween('hd.createdAt', [$dateFrom, $dateTo])
                ->whereNull('hd.deletedAt');

            if (!empty($filters['toa_nha'])) {
                $tnId = (int) $filters['toa_nha'];
                $dtQuery->join('can_ho as ch', 'hd.can_ho', '=', 'ch.id')
                         ->where('ch.toa_nha', $tnId)
                         ->whereNull('ch.deletedAt');
            }

            $result[] = [
                'ten_phi'       => $phi->ten_phi_dich_vu,
                'don_gia'       => (float) $phi->don_gia,
                'loai_tinh_phi' => $phi->loaiTinhPhi?->ten_loai ?? 'N/A',
                'don_vi_tinh'   => $phi->donViTinh?->don_vi ?? 'N/A',
                'so_can_ho'     => $soCanHo,
                'doanh_thu'     => (float) $dtQuery->sum('ct.thanh_tien'),
            ];
        }

        usort($result, fn($a, $b) => $b['doanh_thu'] <=> $a['doanh_thu']);

        return $result;
    }

    private function getThongKeCanHo(array $filters): array
    {
        $toaNhaQuery = ToaNha::with('canHo');

        if (!empty($filters['toa_nha'])) {
            $toaNhaQuery->where('id', (int) $filters['toa_nha']);
        }

        $toaNhaList = $toaNhaQuery->get();
        $byBuilding = [];

        foreach ($toaNhaList as $toaNha) {
            $canHo = $toaNha->canHo;
            $byBuilding[] = [
                'ten_toa_nha'  => $toaNha->ten_toa_nha,
                'tong_can_ho'  => $canHo->count(),
                'dang_su_dung' => $canHo->where('trang_thai', 1)->count(),
                'trong'        => $canHo->where('trang_thai', 2)->count(),
                'bao_tri'      => $canHo->whereNotIn('trang_thai', [1, 2])->count(),
            ];
        }

        return [
            'tong'     => CanHo::count(),
            'theo_toa' => $byBuilding,
        ];
    }

    private function getThongKePhuongTien(): array
    {
        $loaiList = LoaiPhuongTien::withCount([
            'phuongTien as tong',
            'phuongTien as hoat_dong' => fn($q) => $q->where('trang_thai', 1),
        ])->get();

        return [
            'tong'      => PhuongTien::count(),
            'hoat_dong' => PhuongTien::where('trang_thai', 1)->count(),
            'bi_khoa'   => PhuongTien::where('trang_thai', '!=', 1)->count(),
            'theo_loai' => $loaiList->map(fn($l) => [
                'ten_loai'  => $l->ten_loai_phuong_tien,
                'tong'      => (int) $l->tong,
                'hoat_dong' => (int) $l->hoat_dong,
            ])->values()->toArray(),
        ];
    }

    private function getThongKeYeuCau(array $filters, Carbon $dateFrom, Carbon $dateTo): array
    {
        $query = YeuCauCuDan::query()->whereBetween('createdAt', [$dateFrom, $dateTo]);

        $rows = $query->get(['trang_thai']);

        return [
            'tong'       => $rows->count(),
            'cho_xu_ly'  => $rows->where('trang_thai', YeuCauCuDan::TRANG_THAI_MOI)->count(),
            'dang_xu_ly' => $rows->where('trang_thai', YeuCauCuDan::TRANG_THAI_DANG_XU_LY)->count(),
            'hoan_thanh' => $rows->where('trang_thai', YeuCauCuDan::TRANG_THAI_HOAN_THANH)->count(),
            'tu_choi'    => $rows->where('trang_thai', YeuCauCuDan::TRANG_THAI_TU_CHOI)->count(),
        ];
    }

    private function getTopData(array $filters, Carbon $dateFrom, Carbon $dateTo): array
    {
        // Top 10 căn hộ nợ nhiều nhất
        $noQuery = DB::table('hoa_don as hd')
            ->select('hd.can_ho', DB::raw('SUM(hd.tong_tien - hd.so_tien_da_thanh_toan) as tong_no'))
            ->whereIn('hd.trang_thai', [HoaDon::TRANG_THAI_CHUA_THANH_TOAN, HoaDon::TRANG_THAI_QUA_HAN])
            ->whereBetween('hd.createdAt', [$dateFrom, $dateTo])
            ->whereNull('hd.deletedAt');

        if (!empty($filters['toa_nha'])) {
            $tnId = (int) $filters['toa_nha'];
            $noQuery->join('can_ho as ch', 'hd.can_ho', '=', 'ch.id')
                    ->where('ch.toa_nha', $tnId)
                    ->whereNull('ch.deletedAt');
        }

        $canHoNo = $noQuery->groupBy('hd.can_ho')->orderByDesc('tong_no')->limit(10)->get();
        $canHoIds = $canHoNo->pluck('can_ho');
        $canHoMap = CanHo::with('toaNha')->whereIn('id', $canHoIds)->get()->keyBy('id');

        $canHoNoNhieu = $canHoNo->map(fn($r) => [
            'so_can_ho'  => $canHoMap[$r->can_ho]?->so_can_ho ?? 'N/A',
            'toa_nha'    => $canHoMap[$r->can_ho]?->toaNha?->ten_toa_nha ?? 'N/A',
            'tong_no'    => (float) $r->tong_no,
        ])->toArray();

        // Top 10 phí dịch vụ doanh thu cao nhất
        $phiDtQuery = DB::table('chi_tiet_hoa_don as ct')
            ->join('hoa_don as hd', 'ct.hoa_don', '=', 'hd.id')
            ->select('ct.ten_phi_dich_vu', DB::raw('SUM(ct.thanh_tien) as tong_doanh_thu'))
            ->where('hd.trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)
            ->whereBetween('hd.createdAt', [$dateFrom, $dateTo])
            ->whereNull('hd.deletedAt');

        if (!empty($filters['toa_nha'])) {
            $tnId = (int) $filters['toa_nha'];
            $phiDtQuery->join('can_ho as ch', 'hd.can_ho', '=', 'ch.id')
                       ->where('ch.toa_nha', $tnId)
                       ->whereNull('ch.deletedAt');
        }

        $phiDvDoanhThu = $phiDtQuery->groupBy('ct.ten_phi_dich_vu')
            ->orderByDesc('tong_doanh_thu')->limit(10)->get();

        // Top 10 cư dân thanh toán nhiều nhất
        $cuDanTtQuery = DB::table('lich_su_thanh_toan as lst')
            ->join('cu_dan as cd', 'lst.nguoi_thanh_toan', '=', 'cd.id')
            ->select('cd.id', DB::raw('CONCAT(cd.ho_ten_dem, " ", cd.ten) as ho_ten'), DB::raw('SUM(lst.so_tien) as tong_tt, COUNT(*) as so_gd'))
            ->whereNotNull('lst.nguoi_thanh_toan')
            ->whereBetween('lst.ngay_thanh_toan', [$dateFrom, $dateTo]);

        if (!empty($filters['toa_nha'])) {
            $tnId = (int) $filters['toa_nha'];
            $cuDanTtQuery->join('hoa_don as hd', 'lst.hoa_don', '=', 'hd.id')
                          ->join('can_ho as ch', 'hd.can_ho', '=', 'ch.id')
                          ->where('ch.toa_nha', $tnId)
                          ->whereNull('hd.deletedAt')
                          ->whereNull('ch.deletedAt');
        }

        $cuDanTtNhieu = $cuDanTtQuery->groupBy('cd.id', 'cd.ho_ten_dem', 'cd.ten')
            ->orderByDesc('tong_tt')->limit(10)->get();

        // Top 10 nhân viên xử lý nhiều giao dịch nhất (theo hóa đơn được xử lý)
        $nvQuery = DB::table('lich_su_thanh_toan as lst')
            ->join('hoa_don as hd', 'lst.hoa_don', '=', 'hd.id')
            ->join('nhan_vien as nv', 'hd.nguoi_cap_nhat', '=', 'nv.id')
            ->select('nv.id', 'nv.ho_ten', DB::raw('COUNT(*) as so_gd, SUM(lst.so_tien) as tong_tien'))
            ->whereBetween('lst.ngay_thanh_toan', [$dateFrom, $dateTo])
            ->whereNull('hd.deletedAt')
            ->whereNull('nv.deletedAt');

        $nvXuLyNhieu = $nvQuery->groupBy('nv.id', 'nv.ho_ten')
            ->orderByDesc('so_gd')->limit(10)->get();

        return [
            'can_ho_no_nhieu'       => $canHoNoNhieu,
            'phi_dv_doanh_thu_cao'  => $phiDvDoanhThu->toArray(),
            'cu_dan_tt_nhieu'       => $cuDanTtNhieu->toArray(),
            'nhan_vien_xu_ly_nhieu' => $nvXuLyNhieu->toArray(),
        ];
    }
}
