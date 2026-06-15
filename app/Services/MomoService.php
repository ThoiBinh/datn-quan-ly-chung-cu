<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MomoService
{
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private string $endpoint;
    private string $redirectUrl;
    private string $ipnUrl;

    public function __construct()
    {
        $this->partnerCode = config('services.momo.partner_code', '');
        $this->accessKey   = config('services.momo.access_key', '');
        $this->secretKey   = config('services.momo.secret_key', '');
        $this->endpoint    = config('services.momo.endpoint', '');
        $this->redirectUrl = config('services.momo.redirect_url', '');
        $this->ipnUrl      = config('services.momo.ipn_url', '');
    }

    public function taoYeuCauThanhToan(string $orderId, int $amount, string $orderInfo): array
    {
        $requestId = $orderId . '_' . time();
        $extraData = '';
        $requestType = 'payWithATM';

        $rawHash = "accessKey={$this->accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$this->ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$this->partnerCode}&redirectUrl={$this->redirectUrl}&requestId={$requestId}&requestType={$requestType}";
        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $body = [
            'partnerCode' => $this->partnerCode,
            'accessKey'   => $this->accessKey,
            'requestId'   => $requestId,
            'amount'      => (string)$amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl'      => $this->ipnUrl,
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
            'lang'        => 'vi',
        ];

        $response = Http::post($this->endpoint, $body);

        return $response->json();
    }

    public function xacMinhChuKy(array $data): bool
    {
        $rawHash = "accessKey={$this->accessKey}&amount={$data['amount']}&extraData={$data['extraData']}&message={$data['message']}&orderId={$data['orderId']}&orderInfo={$data['orderInfo']}&orderType={$data['orderType']}&partnerCode={$data['partnerCode']}&payType={$data['payType']}&requestId={$data['requestId']}&responseTime={$data['responseTime']}&resultCode={$data['resultCode']}&transId={$data['transId']}";
        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        return $signature === $data['signature'];
    }
}
