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
        $this->tmnCode    = config('services.vnpay.tmn_code', '');
        $this->hashSecret = config('services.vnpay.hash_secret', '');
        $this->vnpayUrl   = config('services.vnpay.url', '');
        $this->returnUrl  = config('services.vnpay.return_url', '');
    }

    public function taoUrlThanhToan(string $orderId, int $amount, string $orderInfo): string
    {
        $vnp_Params = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => $amount * 100,
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $orderId,
            'vnp_OrderInfo'  => $orderInfo,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_IpAddr'     => request()->ip(),
            'vnp_CreateDate' => date('YmdHis'),
        ];

        ksort($vnp_Params);
        $query = http_build_query($vnp_Params);
        $hashData = $query;
        $vnpSecureHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        return $this->vnpayUrl . '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;
    }

    public function xacMinhChuKy(Request $request): bool
    {
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = [];

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'vnp_') && $key !== 'vnp_SecureHash') {
                $inputData[$key] = $value;
            }
        }

        ksort($inputData);
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        return $secureHash === $vnp_SecureHash;
    }
}
