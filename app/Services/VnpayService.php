<?php

namespace App\Services;

use Illuminate\Http\Request;

class VnpayService
{
    private string $tmnCode;
    private string $hashSecret;
    private string $vnpayUrl;
    private string $returnUrl;

    public function __construct()
    {
        $this->tmnCode    = trim((string) config('vnpay.tmn_code', ''));
        $this->hashSecret = trim((string) config('vnpay.hash_secret', ''));
        $this->vnpayUrl   = trim((string) config('vnpay.url', ''));
        $this->returnUrl  = trim((string) config('vnpay.return_url', ''));
    }

    public function taoUrlThanhToan(string $txnRef, int $amount, string $orderInfo): string
    {
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
