<?php

namespace App\Services;

use App\Models\CanHo;
use App\Models\ChiTietHoaDon;
use App\Models\HoaDon;
use App\Models\LichSuThanhToan;
use App\Models\NguonTao;
use Carbon\Carbon;
use Illuminate\Support\Str;

class HoaDonService
{
    public function taoHoaDon(int $canHoId, int $thang, int $nam): HoaDon
    {
        $canHo = CanHo::with('phiDichVu')->findOrFail($canHoId);

        $hoaDon = HoaDon::create([
            'ma_thanh_toan' => 'HD-' . $nam . str_pad($thang, 2, '0', STR_PAD_LEFT) . '-' . str_pad($canHoId, 4, '0', STR_PAD_LEFT),
            'can_ho'        => $canHoId,
            'thang'         => $thang,
            'nam'           => $nam,
            'tong_tien'     => 0,
            'so_tien_da_thanh_toan' => 0,
            'han_thanh_toan' => Carbon::create($nam, $thang)->endOfMonth(),
            'trang_thai'    => HoaDon::TRANG_THAI_CHUA_THANH_TOAN,
        ]);

        $tongTien = 0;
        foreach ($canHo->phiDichVu as $phi) {
            $donGia = $phi->pivot->don_gia ?? $phi->don_gia;
            $chiTiet = ChiTietHoaDon::create([
                'hoa_don'    => $hoaDon->id,
                'phi_dich_vu' => $phi->id,
                'don_gia'    => $donGia,
                'so_luong'   => 1,
                'thanh_tien' => $donGia,
            ]);
            $tongTien += $chiTiet->thanh_tien;
        }

        $hoaDon->update(['tong_tien' => $tongTien]);

        AuditLogService::log('INSERT', 'hoa_don', $hoaDon->id, null, $hoaDon->toArray());

        return $hoaDon;
    }

    public function ghiNhanThanhToan(HoaDon $hoaDon, float $soTien, string $phuongThuc, ?string $maGiaoDich = null, ?int $nguonTao = null): LichSuThanhToan
    {
        $ls = LichSuThanhToan::create([
            'hoa_don'               => $hoaDon->id,
            'ngay_thanh_toan'       => now(),
            'so_tien'               => $soTien,
            'phuong_thuc_thanh_toan' => $phuongThuc,
            'ma_giao_dich'          => $maGiaoDich ?? Str::uuid(),
            'nguoi_thanh_toan'      => auth()->user()?->cuDan?->id,
            'nguon_tao'             => $nguonTao ?? NguonTao::ADMIN,
            'createdAt'             => now(),
        ]);

        $tongDaThanhToan = $hoaDon->so_tien_da_thanh_toan + $soTien;
        $trangThai = $tongDaThanhToan >= $hoaDon->tong_tien
            ? HoaDon::TRANG_THAI_DA_THANH_TOAN
            : HoaDon::TRANG_THAI_CHUA_THANH_TOAN;

        $hoaDon->update([
            'so_tien_da_thanh_toan' => $tongDaThanhToan,
            'trang_thai'            => $trangThai,
        ]);

        AuditLogService::log('UPDATE', 'hoa_don', $hoaDon->id, ['trang_thai' => $hoaDon->getOriginal('trang_thai')], ['trang_thai' => $trangThai]);

        return $ls;
    }
}
