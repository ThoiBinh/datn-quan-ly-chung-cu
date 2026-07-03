<?php

namespace App\View\Composers;

use App\Models\CauHinhWebsite;
use Illuminate\View\View;

class CauHinhWebsiteComposer
{
    private const DEFAULT_TEN_CHUNG_CU = 'Smart Apartment';

    private const RESIDENT_KEYS = [
        'ten_chung_cu', 'mo_ta_seo', 'logo', 'favicon', 'website',
        'so_dien_thoai', 'hotline', 'email', 'dia_chi', 'gio_lam_viec',
        'facebook', 'zalo', 'youtube', 'copyright', 'ban_do_google',
        'seo_title', 'seo_description', 'seo_keywords',
        'mau_chinh', 'mau_phu', 'banner', 'ma_so_thue',
    ];

    public function compose(View $view): void
    {
        $isResident = $view->getName() === 'layouts.resident';

        $keys = $isResident ? self::RESIDENT_KEYS : ['ten_chung_cu', 'mo_ta_seo', 'logo'];

        $cauHinh = CauHinhWebsite::where('trang_thai', 1)
            ->whereIn('ma_thuoc_tinh', $keys)
            ->get()
            ->keyBy('ma_thuoc_tinh');

        $data = [
            'tenChungCu'  => $cauHinh->get('ten_chung_cu')?->gia_tri ?: self::DEFAULT_TEN_CHUNG_CU,
            'moTaWebsite' => $cauHinh->get('mo_ta_seo')?->gia_tri ?: null,
            'logoWebsite' => $cauHinh->get('logo')?->gia_tri_url,
        ];

        if ($isResident) {
            $data += $this->residentFooterData($cauHinh);
        }

        $view->with($data);
    }

    private function residentFooterData($cauHinh): array
    {
        $banDoRaw = $cauHinh->get('ban_do_google')?->gia_tri;
        $chwBanDoSrc = null;
        if (filled($banDoRaw)) {
            if (preg_match('/src=["\']([^"\']+)["\']/i', $banDoRaw, $m)) {
                $chwBanDoSrc = $m[1] !== '' ? $m[1] : null;
            } elseif (filter_var(trim($banDoRaw), FILTER_VALIDATE_URL)) {
                $chwBanDoSrc = trim($banDoRaw);
            }
        }

        return [
            'chwFaviconUrl'    => $cauHinh->get('favicon')?->gia_tri_url,
            'chwWebsiteUrl'    => $cauHinh->get('website')?->gia_tri,
            'chwSoDienThoai'   => $cauHinh->get('so_dien_thoai')?->gia_tri,
            'chwHotline'       => $cauHinh->get('hotline')?->gia_tri,
            'chwEmail'         => $cauHinh->get('email')?->gia_tri,
            'chwDiaChi'        => $cauHinh->get('dia_chi')?->gia_tri,
            'chwGioLamViec'    => $cauHinh->get('gio_lam_viec')?->gia_tri,
            'chwFacebook'      => $cauHinh->get('facebook')?->gia_tri,
            'chwZalo'          => $cauHinh->get('zalo')?->gia_tri,
            'chwYoutube'       => $cauHinh->get('youtube')?->gia_tri,
            'chwCopyright'     => $cauHinh->get('copyright')?->gia_tri,
            'chwBanDoSrc'      => $chwBanDoSrc,
            'chwSeoTitle'      => $cauHinh->get('seo_title')?->gia_tri,
            'chwSeoDescription' => $cauHinh->get('seo_description')?->gia_tri,
            'chwSeoKeywords'   => $cauHinh->get('seo_keywords')?->gia_tri,
            'chwMauChinh'      => $cauHinh->get('mau_chinh')?->gia_tri,
            'chwMauPhu'        => $cauHinh->get('mau_phu')?->gia_tri,
            'chwBannerUrl'     => $cauHinh->get('banner')?->gia_tri_url,
            'chwMaSoThue'      => $cauHinh->get('ma_so_thue')?->gia_tri,
        ];
    }
}
