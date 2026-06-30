<?php

namespace App\Services;

use App\Models\CanHo;
use App\Models\ChiTietHoaDon;
use App\Models\HoaDon;
use App\Models\LichSuThanhToan;
use App\Models\LoaiTinhPhiDichVu;
use App\Models\NguonTao;
use App\Models\PhiDichVu;
use App\Models\PhuongTien;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HoaDonService
{
    // ─────────────────────────────────────────────────────────────
    //  Core invoice creation
    // ─────────────────────────────────────────────────────────────

    public function taoHoaDon(int $canHoId, int $thang, int $nam, array $chiSoData = [], array $excludedPhiIds = []): HoaDon
    {
        return DB::transaction(function () use ($canHoId, $thang, $nam, $chiSoData, $excludedPhiIds) {
            $canHo = CanHo::with([
                'phiDichVu.loaiTinhPhi',
                'phuongTien.loaiPhuongTien',
                'thuocTinh',
            ])->findOrFail($canHoId);

            $hoaDon = HoaDon::create([
                'ma_thanh_toan'         => 'HD-' . $nam . str_pad($thang, 2, '0', STR_PAD_LEFT)
                                         . '-' . str_pad($canHoId, 4, '0', STR_PAD_LEFT),
                'can_ho'                => $canHoId,
                'thang'                 => $thang,
                'nam'                   => $nam,
                'tong_tien'             => 0,
                'so_tien_da_thanh_toan' => 0,
                'han_thanh_toan'        => Carbon::create($nam, $thang)->endOfMonth(),
                'trang_thai'            => HoaDon::TRANG_THAI_CHUA_THANH_TOAN,
                'nguoi_cap_nhat'        => auth('nhanvien')->id(),
            ]);

            $tongTien = $this->hinhThanhChiTiet($hoaDon, $canHo, $thang, $nam, $chiSoData, $excludedPhiIds);
            $hoaDon->update(['tong_tien' => $tongTien]);

            AuditLogService::log('INSERT', 'hoa_don', $hoaDon->id, null, $hoaDon->fresh()->toArray());

            return $hoaDon;
        });
    }

    // ─────────────────────────────────────────────────────────────
    //  Meter-reading update (for edit form)
    // ─────────────────────────────────────────────────────────────

    public function capNhatChiSo(HoaDon $hoaDon, array $chiTietData): void
    {
        foreach ($chiTietData as $id => $values) {
            $chiTiet = ChiTietHoaDon::where('id', $id)
                ->where('hoa_don', $hoaDon->id)
                ->whereNotNull('chi_so_cu')
                ->first();

            if (!$chiTiet) {
                continue;
            }

            $chiSoCu  = (int) ($values['chi_so_cu']  ?? 0);
            $chiSoMoi = (int) ($values['chi_so_moi'] ?? 0);
            $result   = $this->calculateMeterFee((float) $chiTiet->don_gia, $chiSoCu, $chiSoMoi);

            $chiTiet->update([
                'chi_so_cu'  => $chiSoCu,
                'chi_so_moi' => $chiSoMoi,
                'so_luong'   => $result['so_luong'],
                'thanh_tien' => $result['thanh_tien'],
            ]);
        }

        $this->calculateInvoiceTotal($hoaDon);
    }

    // ─────────────────────────────────────────────────────────────
    //  Total recalculation
    // ─────────────────────────────────────────────────────────────

    public function calculateInvoiceTotal(HoaDon $hoaDon): void
    {
        $tongTien = (float) ChiTietHoaDon::where('hoa_don', $hoaDon->id)->sum('thanh_tien');
        $hoaDon->update([
            'tong_tien'      => $tongTien,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  Auto-update overdue invoices (called on index/show/edit)
    // ─────────────────────────────────────────────────────────────

    public function capNhatTrangThaiTreHan(): void
    {
        $today = now()->startOfDay();

        HoaDon::whereRaw('so_tien_da_thanh_toan >= tong_tien')
            ->where('trang_thai', '!=', HoaDon::TRANG_THAI_DA_THANH_TOAN)
            ->update(['trang_thai' => HoaDon::TRANG_THAI_DA_THANH_TOAN]);

        HoaDon::whereRaw('so_tien_da_thanh_toan < tong_tien')
            ->whereNotNull('han_thanh_toan')
            ->where('han_thanh_toan', '<', $today)
            ->where('trang_thai', '!=', HoaDon::TRANG_THAI_QUA_HAN)
            ->update(['trang_thai' => HoaDon::TRANG_THAI_QUA_HAN]);

        HoaDon::whereRaw('so_tien_da_thanh_toan < tong_tien')
            ->where(function ($q) use ($today) {
                $q->whereNull('han_thanh_toan')
                  ->orWhere('han_thanh_toan', '>=', $today);
            })
            ->where('trang_thai', '!=', HoaDon::TRANG_THAI_CHUA_THANH_TOAN)
            ->update(['trang_thai' => HoaDon::TRANG_THAI_CHUA_THANH_TOAN]);
    }

    public function calculateStatus(HoaDon $hoaDon): int
    {
        $tongTien = (float) ($hoaDon->tong_tien ?? 0);
        $daTT     = (float) ($hoaDon->so_tien_da_thanh_toan ?? 0);

        if ($daTT >= $tongTien) {
            return HoaDon::TRANG_THAI_DA_THANH_TOAN;
        }

        if ($hoaDon->han_thanh_toan && now()->startOfDay()->gt($hoaDon->han_thanh_toan)) {
            return HoaDon::TRANG_THAI_QUA_HAN;
        }

        return HoaDon::TRANG_THAI_CHUA_THANH_TOAN;
    }

    public function syncStatus(HoaDon $hoaDon): void
    {
        $hoaDon->update(['trang_thai' => $this->calculateStatus($hoaDon)]);
    }

    // ─────────────────────────────────────────────────────────────
    //  Services for apartment (modal in create form)
    // ─────────────────────────────────────────────────────────────

    public function layDichVuCanHo(int $canHoId): array
    {
        $canHo = CanHo::with([
            'phiDichVu.loaiPhiDichVu',
            'phiDichVu.donViTinh',
            'phiDichVu.loaiTinhPhi',
        ])->findOrFail($canHoId);

        return $canHo->phiDichVu->map(function ($phi) {
            $donGia = (float) ($phi->pivot->don_gia ?? $phi->don_gia);
            return [
                'phi_dich_vu_id'   => $phi->id,
                'ten_phi_dich_vu'  => $phi->ten_phi_dich_vu,
                'loai_phi_dich_vu' => $phi->loaiPhiDichVu?->ten_loai_phi_dich_vu ?? '—',
                'don_vi_tinh'      => $phi->donViTinh?->don_vi ?? '—',
                'loai_tinh_phi'    => $phi->loaiTinhPhi?->ten_loai ?? '—',
                'don_gia'          => $donGia,
                'don_gia_fmt'      => number_format($donGia, 0, ',', '.') . 'đ',
            ];
        })->values()->toArray();
    }

    // ─────────────────────────────────────────────────────────────
    //  AJAX fee preview for create form
    // ─────────────────────────────────────────────────────────────

    public function previewPhi(int $canHoId, int $thang, int $nam): array
    {
        $canHo = CanHo::with([
            'phiDichVu.loaiTinhPhi',
            'phuongTien.loaiPhuongTien',
            'thuocTinh',
        ])->findOrFail($canHoId);

        $fees = [];

        foreach ($canHo->phiDichVu as $phi) {
            $billingType = $this->identifyBillingType($phi);
            $loaiTinhPhi = (int) ($phi->loaiTinhPhi?->id ?? $phi->loai_tinh_phi ?? 0);
            $donGia      = (float) ($phi->pivot->don_gia ?? $phi->don_gia);

            $feeData = [
                'phi_dich_vu_id'  => $phi->id,
                'ten_phi_dich_vu' => $phi->ten_phi_dich_vu,
                'loai_tinh_phi'   => $loaiTinhPhi,
                'billing_type'    => $billingType,
                'don_gia'         => $donGia,
                'don_gia_fmt'     => number_format($donGia, 0, ',', '.') . 'đ',
                'so_luong'        => null,
                'thanh_tien'      => null,
                'preview_rows'    => null,
            ];

            switch ($billingType) {
                case 'area':
                    $result = $this->calculateAreaFee($canHo, $donGia);
                    $feeData['so_luong']   = $result['so_luong'];
                    $feeData['thanh_tien'] = $result['thanh_tien'];
                    break;

                case 'fixed':
                    $result = $this->calculateFixedFee($donGia);
                    $feeData['so_luong']   = $result['so_luong'];
                    $feeData['thanh_tien'] = $result['thanh_tien'];
                    break;

                case 'vehicle':
                    $rows    = $this->calculateVehicleFee($canHo, $thang, $nam, $phi->ten_phi_dich_vu, $donGia);
                    $totalPT = (float) array_sum(array_column($rows, 'thanh_tien'));
                    $feeData['thanh_tien']   = $totalPT;
                    $feeData['preview_rows'] = array_map(fn ($r) => [
                        'ten'        => $r['ten_phi_dich_vu'],
                        'so_luong'   => $r['so_luong'],
                        'don_gia'    => number_format($r['don_gia'], 0, ',', '.') . 'đ',
                        'thanh_tien' => number_format($r['thanh_tien'], 0, ',', '.') . 'đ',
                    ], $rows);
                    break;

                // 'meter': leave thanh_tien null — user must supply chi_so
            }

            $fees[] = $feeData;
        }

        return [
            'fees'             => $fees,
            'so_can_ho'        => $canHo->so_can_ho,
            'dien_tich'        => $this->layDienTich($canHo),
            'phuong_tien_info' => $this->layThongTinPhuongTien($canHo, $thang, $nam),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    //  Named calculation methods (public per spec)
    // ─────────────────────────────────────────────────────────────

    /**
     * Diện tích: so_luong = m² từ thuoc_tinh_can_ho (fallback = 1)
     */
    public function calculateAreaFee(CanHo $canHo, float $donGia): array
    {
        $dienTich = $this->layDienTich($canHo);
        $soLuong  = $dienTich > 0 ? $dienTich : 1;

        return [
            'so_luong'   => $soLuong,
            'thanh_tien' => round($soLuong * $donGia, 2),
        ];
    }

    /**
     * Cố định: so_luong = 1 (flat rate, không tính theo đơn vị phụ)
     */
    public function calculateFixedFee(float $donGia, float $soLuong = 1): array
    {
        return [
            'so_luong'   => $soLuong,
            'thanh_tien' => round($soLuong * $donGia, 2),
        ];
    }

    /**
     * Chỉ số: (chi_so_moi - chi_so_cu) × don_gia
     */
    public function calculateMeterFee(float $donGia, int $chiSoCu, int $chiSoMoi): array
    {
        $soLuong = max(0, $chiSoMoi - $chiSoCu);

        return [
            'so_luong'   => $soLuong,
            'thanh_tien' => round($soLuong * $donGia, 2),
        ];
    }

    /**
     * Phương tiện: đọc từ bảng phuong_tien, nhóm theo loai_phuong_tien.
     * Tính tỉ lệ theo số ngày hoạt động thực tế trong tháng.
     *
     * @return array[]  Mỗi phần tử là một dòng chi_tiet_hoa_don cho từng loại xe.
     */
    public function calculateVehicleFee(CanHo $canHo, int $thang, int $nam, string $tenPhi, float $donGia): array
    {
        $startDate   = Carbon::create($nam, $thang, 1)->startOfDay();
        $endDate     = Carbon::create($nam, $thang, 1)->endOfMonth()->endOfDay();
        $daysInMonth = (int) Carbon::create($nam, $thang, 1)->endOfMonth()->format('d');

        // Include vehicles that overlap with billing month:
        //   - Active (trang_thai=1) vehicles registered before end of month that have not been
        //     cancelled before the month started
        //   - Vehicles cancelled DURING the billing month (to charge for days used)
        $vehicles = PhuongTien::where('can_ho', $canHo->id)
            ->where('ngay_dang_ky', '<=', $endDate)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('trang_thai', 1)
                  ->orWhereBetween('ngay_huy', [$startDate, $endDate]);
            })
            ->with('loaiPhuongTien')
            ->get()
            ->unique('id'); // safeguard against duplicates if both conditions match

        if ($vehicles->isEmpty()) {
            return [];
        }

        $rows = [];
        foreach ($vehicles->groupBy('loai_phuong_tien') as $group) {
            $tenLoai      = $group->first()->loaiPhuongTien?->ten_loai_phuong_tien ?? 'Không rõ loại';
            $totalSoLuong = 0.0;

            foreach ($group as $vehicle) {
                $totalSoLuong += $this->tinhTiLePhuongTien($vehicle, $startDate, $endDate, $daysInMonth);
            }

            $soLuong   = round($totalSoLuong, 2);
            $thanhTien = round($soLuong * $donGia, 2);

            $rows[] = [
                'ten_phi_dich_vu' => $tenPhi . ' - ' . $tenLoai,
                'don_gia'         => $donGia,
                'so_luong'        => $soLuong,
                'thanh_tien'      => $thanhTien,
            ];
        }

        return $rows;
    }

    // ─────────────────────────────────────────────────────────────
    //  Payment recording (immutable — no edit/delete after insert)
    // ─────────────────────────────────────────────────────────────

    public function ghiNhanThanhToan(
        HoaDon $hoaDon,
        float $soTien,
        string $phuongThuc,
        ?string $maGiaoDich = null,
        ?int $nguonTao = null,
        ?string $ngayThanhToan = null,
        ?string $ghiChu = null,
        ?int $nguoiThanhToan = null
    ): LichSuThanhToan {
        return DB::transaction(function () use ($hoaDon, $soTien, $phuongThuc, $maGiaoDich, $nguonTao, $ngayThanhToan, $ghiChu, $nguoiThanhToan) {
            $ls = LichSuThanhToan::create([
                'hoa_don'                => $hoaDon->id,
                'ngay_thanh_toan'        => $ngayThanhToan ?? now()->format('Y-m-d'),
                'so_tien'                => $soTien,
                'phuong_thuc_thanh_toan' => $phuongThuc,
                'ma_giao_dich'           => $maGiaoDich,
                'nguoi_thanh_toan'       => $nguoiThanhToan,
                'ghi_chu'                => $ghiChu,
                'nguon_tao'              => $nguonTao ?? NguonTao::ADMIN,
                'createdAt'              => now(),
            ]);

            $tongDaTT = (float) LichSuThanhToan::where('hoa_don', $hoaDon->id)->sum('so_tien');
            $hoaDon->so_tien_da_thanh_toan = $tongDaTT;

            $hoaDon->update([
                'so_tien_da_thanh_toan' => $tongDaTT,
                'trang_thai'            => $this->calculateStatus($hoaDon),
                'nguoi_cap_nhat'        => auth('nhanvien')->id(),
            ]);

            AuditLogService::log('INSERT', 'lich_su_thanh_toan', $ls->id, null, $ls->toArray());

            return $ls;
        });
    }

    // ─────────────────────────────────────────────────────────────
    //  Private helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Determine billing type by loaiTinhPhi.ten_loai (name-based, ID-fallback).
     * Returns: 'meter' | 'vehicle' | 'area' | 'fixed'
     */
    private function identifyBillingType(PhiDichVu $phi): string
    {
        $ten = mb_strtolower($phi->loaiTinhPhi?->ten_loai ?? '');

        if (mb_strpos($ten, 'chỉ số') !== false || mb_strpos($ten, 'chi so') !== false
            || mb_strpos($ten, 'đồng hồ') !== false || mb_strpos($ten, 'dong ho') !== false) {
            return 'meter';
        }
        if (mb_strpos($ten, 'phương tiện') !== false || mb_strpos($ten, 'phuong tien') !== false) {
            return 'vehicle';
        }
        if (mb_strpos($ten, 'diện tích') !== false || mb_strpos($ten, 'dien tich') !== false) {
            return 'area';
        }

        // Fallback to integer constants when ten_loai is not descriptive
        $loaiId = (int) ($phi->loaiTinhPhi?->id ?? $phi->loai_tinh_phi ?? 0);
        if ($loaiId === LoaiTinhPhiDichVu::THEO_CHI_SO)      return 'meter';
        if ($loaiId === LoaiTinhPhiDichVu::THEO_PHUONG_TIEN) return 'vehicle';

        // THEO_DAU_NGUOI and unknowns → flat fixed rate
        return 'fixed';
    }

    /**
     * Build chi_tiet rows for all fee services of the apartment.
     */
    private function hinhThanhChiTiet(HoaDon $hoaDon, CanHo $canHo, int $thang, int $nam, array $chiSoData, array $excludedPhiIds = []): float
    {
        $tongTien = 0.0;

        foreach ($canHo->phiDichVu as $phi) {
            if (in_array($phi->id, $excludedPhiIds)) {
                continue;
            }

            $billingType = $this->identifyBillingType($phi);
            $donGia      = (float) ($phi->pivot->don_gia ?? $phi->don_gia);

            switch ($billingType) {
                case 'meter':
                    $tongTien += $this->taoChiTietChiSo($hoaDon, $phi->ten_phi_dich_vu, $donGia, $phi->id, $chiSoData);
                    break;

                case 'vehicle':
                    $rows = $this->calculateVehicleFee($canHo, $thang, $nam, $phi->ten_phi_dich_vu, $donGia);
                    foreach ($rows as $row) {
                        ChiTietHoaDon::create([
                            'hoa_don'         => $hoaDon->id,
                            'ten_phi_dich_vu' => $row['ten_phi_dich_vu'],
                            'don_gia'         => $row['don_gia'],
                            'so_luong'        => $row['so_luong'],
                            'thanh_tien'      => $row['thanh_tien'],
                        ]);
                        $tongTien += $row['thanh_tien'];
                    }
                    break;

                case 'area':
                    $result = $this->calculateAreaFee($canHo, $donGia);
                    ChiTietHoaDon::create([
                        'hoa_don'         => $hoaDon->id,
                        'ten_phi_dich_vu' => $phi->ten_phi_dich_vu,
                        'don_gia'         => $donGia,
                        'so_luong'        => $result['so_luong'],
                        'thanh_tien'      => $result['thanh_tien'],
                    ]);
                    $tongTien += $result['thanh_tien'];
                    break;

                default: // 'fixed'
                    $soLuong = max(1, (int) ($chiSoData[$phi->id]['so_luong'] ?? 1));
                    $result  = $this->calculateFixedFee($donGia, $soLuong);
                    ChiTietHoaDon::create([
                        'hoa_don'         => $hoaDon->id,
                        'ten_phi_dich_vu' => $phi->ten_phi_dich_vu,
                        'don_gia'         => $donGia,
                        'so_luong'        => $result['so_luong'],
                        'thanh_tien'      => $result['thanh_tien'],
                    ]);
                    $tongTien += $result['thanh_tien'];
                    break;
            }
        }

        return $tongTien;
    }

    /**
     * Create a single THEO_CHI_SO chi_tiet row from submitted meter readings.
     */
    private function taoChiTietChiSo(HoaDon $hoaDon, string $tenPhi, float $donGia, int $phiId, array $chiSoData): float
    {
        $chiSoCu  = (int) ($chiSoData[$phiId]['cu']  ?? 0);
        $chiSoMoi = (int) ($chiSoData[$phiId]['moi'] ?? 0);
        $result   = $this->calculateMeterFee($donGia, $chiSoCu, $chiSoMoi);

        ChiTietHoaDon::create([
            'hoa_don'         => $hoaDon->id,
            'ten_phi_dich_vu' => $tenPhi,
            'don_gia'         => $donGia,
            'chi_so_cu'       => $chiSoCu,
            'chi_so_moi'      => $chiSoMoi,
            'so_luong'        => $result['so_luong'],
            'thanh_tien'      => $result['thanh_tien'],
        ]);

        return $result['thanh_tien'];
    }

    /**
     * Proportional billing: fraction of billing month a vehicle was active.
     */
    private function tinhTiLePhuongTien(PhuongTien $vehicle, Carbon $startDate, Carbon $endDate, int $daysInMonth): float
    {
        $ngayDangKy = $vehicle->ngay_dang_ky instanceof Carbon
            ? $vehicle->ngay_dang_ky
            : Carbon::parse($vehicle->ngay_dang_ky);

        $activeStart = $ngayDangKy->gt($startDate)
            ? $ngayDangKy->copy()->startOfDay()
            : $startDate->copy();

        $ngayHuy = $vehicle->ngay_huy
            ? ($vehicle->ngay_huy instanceof Carbon ? $vehicle->ngay_huy : Carbon::parse($vehicle->ngay_huy))
            : null;

        $activeEnd = ($ngayHuy && $ngayHuy->lte($endDate))
            ? $ngayHuy->copy()->startOfDay()
            : $endDate->copy()->startOfDay();

        if ($activeEnd->lt($activeStart)) {
            return 0.0;
        }

        $daysActive = $activeStart->diffInDays($activeEnd) + 1;

        return min(1.0, $daysActive / $daysInMonth);
    }

    /**
     * Trả về danh sách phương tiện hợp lệ trong tháng, nhóm theo loại, dùng collection đã eager-load.
     */
    private function layThongTinPhuongTien(CanHo $canHo, int $thang, int $nam): array
    {
        $startDate = Carbon::create($nam, $thang, 1)->startOfDay();
        $endDate   = Carbon::create($nam, $thang, 1)->endOfMonth()->endOfDay();

        $valid = $canHo->phuongTien->filter(function ($pt) use ($startDate, $endDate) {
            if (!$pt->ngay_dang_ky) {
                return false;
            }
            $dangKy = $pt->ngay_dang_ky instanceof Carbon ? $pt->ngay_dang_ky : Carbon::parse($pt->ngay_dang_ky);
            if ($dangKy->gt($endDate)) {
                return false;
            }
            if ($pt->ngay_huy) {
                $huy = $pt->ngay_huy instanceof Carbon ? $pt->ngay_huy : Carbon::parse($pt->ngay_huy);
                return $huy->gte($startDate);
            }
            return true;
        });

        return $valid->groupBy('loai_phuong_tien')
            ->map(fn ($group) => [
                'ten_loai' => $group->first()->loaiPhuongTien?->ten_loai_phuong_tien ?? 'Không rõ loại',
                'so_luong' => $group->count(),
            ])
            ->values()
            ->toArray();
    }

    /**
     * Find "diện tích" attribute value from thuoc_tinh_can_ho.
     */
    private function layDienTich(CanHo $canHo): float
    {
        $attr = $canHo->thuocTinh->first(
            fn ($tt) => mb_stripos($tt->ten_thuoc_tinh, 'diện tích') !== false
                     || mb_stripos($tt->ten_thuoc_tinh, 'dien tich') !== false
        );

        return $attr ? (float) ($attr->pivot->gia_tri_thuoc_tinh ?? 0) : 0;
    }
}
