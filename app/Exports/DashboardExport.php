<?php

namespace App\Exports;

use App\Exports\Sheets\GenericSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DashboardExport implements WithMultipleSheets
{
    public function __construct(
        private array $data,
        private array $cauHinhWebsite = [],
    ) {}

    public function sheets(): array
    {
        return [
            $this->buildDashboardSheet(),
            $this->buildHoaDonSheet(),
            $this->buildThanhToanSheet(),
            $this->buildCuDanSheet(),
            $this->buildCanHoSheet(),
            $this->buildPhuongTienSheet(),
            $this->buildYeuCauSheet(),
            $this->buildPhiDichVuSheet(),
        ];
    }

    private function meta(): array
    {
        $tt = $this->data['thong_tin'] ?? [];

        return [
            'thoi_gian_xuat'   => $tt['thoi_gian_xuat'] ?? '',
            'nguoi_xuat'       => $tt['nguoi_xuat'] ?? '',
            'khoang_thoi_gian' => $tt['khoang_thoi_gian'] ?? '',
        ];
    }

    // ─── SHEET 1: DASHBOARD TỔNG QUAN ────────────────────────────────────────

    private function buildDashboardSheet(): GenericSheet
    {
        $tt  = $this->data['thong_tin']         ?? [];
        $hs  = $this->data['thong_ke_he_thong'] ?? [];
        $hd  = $this->data['thong_ke_hoa_don']  ?? [];
        $pay = $this->data['thong_ke_thanh_toan'] ?? [];

        $rows = [
            ['THÔNG TIN BÁO CÁO', ''],
            ['Hệ thống',           $tt['ten_he_thong']     ?? ''],
            ['Thời gian xuất',     $tt['thoi_gian_xuat']   ?? ''],
            ['Người xuất',         $tt['nguoi_xuat']       ?? ''],
            ['Khoảng thời gian',   $tt['khoang_thoi_gian'] ?? ''],
            ['Từ ngày',            $tt['date_from']        ?? ''],
            ['Đến ngày',           $tt['date_to']          ?? ''],
            ['', ''],

            ['THỐNG KÊ HỆ THỐNG', ''],
            ['Tổng cư dân',        $hs['tong_cu_dan']      ?? 0],
            ['Tổng nhân viên',     $hs['tong_nhan_vien']   ?? 0],
            ['Tổng tòa nhà',       $hs['tong_toa_nha']     ?? 0],
            ['Tổng căn hộ',        $hs['tong_can_ho']      ?? 0],
            ['Tổng phương tiện',   $hs['tong_phuong_tien'] ?? 0],
            ['Tổng hóa đơn',       $hs['tong_hoa_don']     ?? 0],
            ['Tổng yêu cầu',       $hs['tong_yeu_cau']     ?? 0],
            ['Tổng thông báo',     $hs['tong_thong_bao']   ?? 0],
            ['Tổng bản tin',       $hs['tong_ban_tin']     ?? 0],
            ['', ''],

            ['THỐNG KÊ HÓA ĐƠN', ''],
            ['Tổng hóa đơn',       $hd['tong']            ?? 0],
            ['Đã thanh toán',      $hd['da_thanh_toan']   ?? 0],
            ['Chưa thanh toán',    $hd['chua_thanh_toan'] ?? 0],
            ['Quá hạn',            $hd['qua_han']         ?? 0],
            ['Đã hủy',             $hd['da_huy']          ?? 0],
            ['Tổng tiền HĐ',       $hd['tong_tien']       ?? 0],
            ['Tổng đã thanh toán', $hd['tong_tien_da_tt'] ?? 0],
            ['Tổng còn nợ',        $hd['tong_tien_con_no'] ?? 0],
            ['', ''],

            ['THỐNG KÊ DOANH THU (từ lịch sử thanh toán)', ''],
            ['Tổng giao dịch',     $pay['tong_giao_dich']    ?? 0],
            ['Tổng doanh thu',     $pay['tong_doanh_thu']    ?? 0],
            ['Doanh thu hôm nay',  $pay['doanh_thu_hom_nay'] ?? 0],
            ['Doanh thu tháng',    $pay['doanh_thu_thang']   ?? 0],
            ['Doanh thu năm',      $pay['doanh_thu_nam']     ?? 0],
        ];

        return new GenericSheet('Dashboard', ['Chỉ số', 'Giá trị'], $rows, '1E3A5F', $this->cauHinhWebsite, 'BÁO CÁO TỔNG QUAN DASHBOARD', $this->meta());
    }

    // ─── SHEET 2: HÓA ĐƠN ───────────────────────────────────────────────────

    private function buildHoaDonSheet(): GenericSheet
    {
        $hd = $this->data['thong_ke_hoa_don'] ?? [];

        $rows = [
            ['Đã thanh toán',   $hd['da_thanh_toan']    ?? 0, $this->fmtMoney($hd['tong_tien_da_tt'] ?? 0)],
            ['Chưa thanh toán', $hd['chua_thanh_toan']  ?? 0, ''],
            ['Quá hạn',         $hd['qua_han']          ?? 0, ''],
            ['Đã hủy',          $hd['da_huy']           ?? 0, ''],
            ['TỔNG',            $hd['tong']             ?? 0, $this->fmtMoney($hd['tong_tien'] ?? 0)],
            ['', '', ''],
            ['Tổng tiền còn nợ', '', $this->fmtMoney($hd['tong_tien_con_no'] ?? 0)],
        ];

        return new GenericSheet('Hóa đơn', ['Trạng thái', 'Số lượng', 'Tổng tiền (VNĐ)'], $rows, '064E3B', $this->cauHinhWebsite, 'BÁO CÁO HÓA ĐƠN', $this->meta());
    }

    // ─── SHEET 3: THANH TOÁN ─────────────────────────────────────────────────

    private function buildThanhToanSheet(): GenericSheet
    {
        $pay = $this->data['thong_ke_thanh_toan'] ?? [];

        $rows = [
            ['Tổng giao dịch (kỳ)',   $pay['tong_giao_dich']    ?? 0, $this->fmtMoney($pay['tong_doanh_thu'] ?? 0)],
            ['Doanh thu hôm nay',     '',                              $this->fmtMoney($pay['doanh_thu_hom_nay'] ?? 0)],
            ['Doanh thu tháng này',   '',                              $this->fmtMoney($pay['doanh_thu_thang'] ?? 0)],
            ['Doanh thu năm nay',     '',                              $this->fmtMoney($pay['doanh_thu_nam'] ?? 0)],
        ];

        // Top cư dân thanh toán nhiều nhất
        $topCuDan = $this->data['top_data']['cu_dan_tt_nhieu'] ?? [];
        if (!empty($topCuDan)) {
            $rows[] = ['', '', ''];
            $rows[] = ['TOP CƯ DÂN THANH TOÁN NHIỀU NHẤT', '', ''];
            foreach ($topCuDan as $r) {
                $rows[] = [$r->ho_ten ?? 'N/A', (int) ($r->so_gd ?? 0), $this->fmtMoney((float)($r->tong_tt ?? 0))];
            }
        }

        return new GenericSheet('Thanh toán', ['Chỉ số / Cư dân', 'Số giao dịch', 'Tổng tiền (VNĐ)'], $rows, '7C3AED', $this->cauHinhWebsite, 'BÁO CÁO THANH TOÁN', $this->meta());
    }

    // ─── SHEET 4: CƯ DÂN ─────────────────────────────────────────────────────

    private function buildCuDanSheet(): GenericSheet
    {
        $hs = $this->data['thong_ke_he_thong'] ?? [];
        $topNo = $this->data['top_data']['can_ho_no_nhieu'] ?? [];

        $rows = [
            ['Tổng cư dân', $hs['tong_cu_dan'] ?? 0],
            ['', ''],
            ['TOP CĂN HỘ NỢ NHIỀU NHẤT (cư dân liên quan)', ''],
        ];

        foreach ($topNo as $r) {
            $rows[] = [
                'Căn hộ ' . ($r['so_can_ho'] ?? 'N/A') . ' – ' . ($r['toa_nha'] ?? ''),
                $this->fmtMoney($r['tong_no'] ?? 0),
            ];
        }

        return new GenericSheet('Cư dân', ['Thông tin', 'Giá trị'], $rows, '0E7490', $this->cauHinhWebsite, 'BÁO CÁO CƯ DÂN', $this->meta());
    }

    // ─── SHEET 5: CĂN HỘ ─────────────────────────────────────────────────────

    private function buildCanHoSheet(): GenericSheet
    {
        $ck = $this->data['thong_ke_can_ho'] ?? [];

        $rows = [];
        foreach ($ck['theo_toa'] ?? [] as $toa) {
            $rows[] = [
                $toa['ten_toa_nha'],
                $toa['tong_can_ho'],
                $toa['dang_su_dung'],
                $toa['trong'],
                $toa['bao_tri'],
            ];
        }

        if (!empty($rows)) {
            $rows[] = [
                'TỔNG',
                array_sum(array_column($rows, 1)),
                array_sum(array_column($rows, 2)),
                array_sum(array_column($rows, 3)),
                array_sum(array_column($rows, 4)),
            ];
        }

        return new GenericSheet(
            'Căn hộ',
            ['Tòa nhà', 'Tổng căn hộ', 'Đang sử dụng', 'Trống', 'Bảo trì/Khác'],
            $rows,
            '92400E',
            $this->cauHinhWebsite,
            'BÁO CÁO CĂN HỘ',
            $this->meta()
        );
    }

    // ─── SHEET 6: PHƯƠNG TIỆN ────────────────────────────────────────────────

    private function buildPhuongTienSheet(): GenericSheet
    {
        $pt = $this->data['thong_ke_phuong_tien'] ?? [];

        $rows = [
            ['TỔNG', $pt['tong'] ?? 0, $pt['hoat_dong'] ?? 0, $pt['bi_khoa'] ?? 0],
        ];

        foreach ($pt['theo_loai'] ?? [] as $loai) {
            $rows[] = [
                $loai['ten_loai'],
                $loai['tong'],
                $loai['hoat_dong'],
                ($loai['tong'] - $loai['hoat_dong']),
            ];
        }

        return new GenericSheet(
            'Phương tiện',
            ['Loại xe', 'Tổng', 'Hoạt động', 'Bị khóa/Hủy'],
            $rows,
            '065F46',
            $this->cauHinhWebsite,
            'BÁO CÁO PHƯƠNG TIỆN',
            $this->meta()
        );
    }

    // ─── SHEET 7: YÊU CẦU CƯ DÂN ────────────────────────────────────────────

    private function buildYeuCauSheet(): GenericSheet
    {
        $yc = $this->data['thong_ke_yeu_cau'] ?? [];

        $rows = [
            ['Chờ xử lý',   $yc['cho_xu_ly']  ?? 0],
            ['Đang xử lý',  $yc['dang_xu_ly'] ?? 0],
            ['Hoàn thành',  $yc['hoan_thanh'] ?? 0],
            ['Từ chối',     $yc['tu_choi']    ?? 0],
            ['TỔNG',        $yc['tong']       ?? 0],
        ];

        // Nhân viên xử lý nhiều nhất
        $topNv = $this->data['top_data']['nhan_vien_xu_ly_nhieu'] ?? [];
        if (!empty($topNv)) {
            $rows[] = ['', ''];
            $rows[] = ['TOP NHÂN VIÊN XỬ LÝ NHIỀU NHẤT', ''];
            foreach ($topNv as $nv) {
                $rows[] = [
                    is_object($nv) ? ($nv->ho_ten ?? 'N/A') : ($nv['ho_ten'] ?? 'N/A'),
                    is_object($nv) ? ($nv->so_gd  ?? 0)     : ($nv['so_giao_dich'] ?? 0),
                ];
            }
        }

        return new GenericSheet('Yêu cầu cư dân', ['Trạng thái / Nhân viên', 'Số lượng / Giao dịch'], $rows, 'BE185D', $this->cauHinhWebsite, 'BÁO CÁO YÊU CẦU CƯ DÂN', $this->meta());
    }

    // ─── SHEET 8: PHÍ DỊCH VỤ ───────────────────────────────────────────────

    private function buildPhiDichVuSheet(): GenericSheet
    {
        $rows = [];
        foreach ($this->data['thong_ke_phi_dich_vu'] ?? [] as $phi) {
            $rows[] = [
                $phi['ten_phi'],
                $this->fmtMoney($phi['don_gia']),
                $phi['loai_tinh_phi'],
                $phi['don_vi_tinh'],
                $phi['so_can_ho'],
                $this->fmtMoney($phi['doanh_thu']),
            ];
        }

        return new GenericSheet(
            'Phí dịch vụ',
            ['Tên phí', 'Đơn giá (VNĐ)', 'Loại tính phí', 'Đơn vị tính', 'Số căn hộ', 'Doanh thu (VNĐ)'],
            $rows,
            '3730A3',
            $this->cauHinhWebsite,
            'BÁO CÁO PHÍ DỊCH VỤ',
            $this->meta()
        );
    }

    // ─── HELPERS ─────────────────────────────────────────────────────────────

    private function fmtMoney(float $amount): string
    {
        return number_format($amount, 0, ',', '.') . 'đ';
    }
}
