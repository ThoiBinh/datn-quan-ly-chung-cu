<?php

namespace App\Services;

use App\Models\CauHinhWebsite;
use Illuminate\Http\Request;

class VnpayService
{
    private string $tmnCode;
    private string $hashSecret;
    private string $vnpayUrl;
    private string $returnUrl;
    private bool $enabled;

    public function __construct()
    {
        $cauHinh = CauHinhWebsite::layNhom('payment');
        $pick    = fn (string $key, $fallback) => (($cauHinh[$key] ?? '') !== '') ? $cauHinh[$key] : $fallback;

        $this->tmnCode    = trim((string) $pick('vnp_tmn_code', config('vnpay.tmn_code', '')));
        $this->hashSecret = trim((string) $pick('vnp_hash_secret', config('vnpay.hash_secret', '')));
        $this->vnpayUrl   = trim((string) $pick('vnp_url', config('vnpay.url', '')));
        $this->returnUrl  = trim((string) $pick('vnp_return_url', config('vnpay.return_url', '')));
        $this->enabled     = $pick('vnp_enable', '0') === '1';
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function taoUrlThanhToan(string $txnRef, int $amount, string $orderInfo): string
    {
        if (! $this->enabled) {
            throw new \RuntimeException('Cổng thanh toán VNPay hiện đang tắt. Vui lòng liên hệ quản trị viên.');
        }

        $vnp_Params = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => $amount * 100,
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $txnRef,
            'vnp_OrderInfo'  => $orderInfo,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_IpAddr'     => request()->ip(),
            'vnp_CreateDate' => date('YmdHis'),
        ];

        ksort($vnp_Params);
        $hashData      = http_build_query($vnp_Params);
        $secureHash    = hash_hmac('sha512', $hashData, $this->hashSecret);

        return $this->vnpayUrl . '?' . $hashData . '&vnp_SecureHash=' . $secureHash;
    }

    public function xacMinhChuKy(Request $request): bool
    {
        $vnpSecureHash = $request->vnp_SecureHash ?? '';
        $inputData     = [];

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'vnp_') && $key !== 'vnp_SecureHash' && $key !== 'vnp_SecureHashType') {
                $inputData[$key] = $value;
            }
        }

        ksort($inputData);
        $hashData   = http_build_query($inputData);
        $calculated = hash_hmac('sha512', $hashData, $this->hashSecret);

        return hash_equals($calculated, $vnpSecureHash);
    }
}
