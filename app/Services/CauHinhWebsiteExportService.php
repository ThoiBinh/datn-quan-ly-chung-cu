<?php

namespace App\Services;

use App\Models\CauHinhWebsite;
use Illuminate\Support\Facades\Storage;

class CauHinhWebsiteExportService
{
    private const KEYS = [
        'ten_chung_cu', 'logo', 'website', 'so_dien_thoai', 'hotline',
        'email', 'dia_chi', 'gio_lam_viec', 'facebook', 'zalo', 'youtube',
        'copyright', 'ma_so_thue',
    ];

    /**
     * Lấy thông tin website/chung cư dùng cho header/footer của file export (PDF/Excel).
     * Một query duy nhất, không cache (dự án hiện chưa dùng cache cho cấu hình này ở đâu khác).
     */
    public function getInfo(): array
    {
        $cauHinh = CauHinhWebsite::where('trang_thai', 1)
            ->whereIn('ma_thuoc_tinh', self::KEYS)
            ->get()
            ->keyBy('ma_thuoc_tinh');

        $logoValue = $cauHinh->get('logo')?->gia_tri;
        $logoPath  = null;
        if ($logoValue && Storage::disk('public')->exists($logoValue)) {
            $logoPath = Storage::disk('public')->path($logoValue);
        }

        return [
            'ten_chung_cu'  => $cauHinh->get('ten_chung_cu')?->gia_tri ?: config('app.name'),
            'dia_chi'       => $cauHinh->get('dia_chi')?->gia_tri,
            'hotline'       => $cauHinh->get('hotline')?->gia_tri,
            'so_dien_thoai' => $cauHinh->get('so_dien_thoai')?->gia_tri,
            'email'         => $cauHinh->get('email')?->gia_tri,
            'website'       => $cauHinh->get('website')?->gia_tri,
            'facebook'      => $cauHinh->get('facebook')?->gia_tri,
            'zalo'          => $cauHinh->get('zalo')?->gia_tri,
            'youtube'       => $cauHinh->get('youtube')?->gia_tri,
            'ma_so_thue'    => $cauHinh->get('ma_so_thue')?->gia_tri,
            'gio_lam_viec'  => $cauHinh->get('gio_lam_viec')?->gia_tri,
            'copyright'     => $cauHinh->get('copyright')?->gia_tri,
            'logo_path'     => $logoPath,
        ];
    }
}
