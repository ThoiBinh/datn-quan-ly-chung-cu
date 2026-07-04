<?php

namespace App\Services;

use App\Models\CauHinhWebsite;
use Illuminate\Support\Facades\Http;

class MomoService
{
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private string $endpoint;
    private string $redirectUrl;
    private string $ipnUrl;
    private bool $enabled;

    public function __construct()
    {
        $cauHinh = CauHinhWebsite::layNhom('payment');
        $pick    = fn (string $key, $fallback) => (($cauHinh[$key] ?? '') !== '') ? $cauHinh[$key] : $fallback;

        $this->partnerCode = (string) $pick('momo_partner_code', config('momo.partner_code', ''));
        $this->accessKey   = (string) $pick('momo_access_key', config('momo.access_key', ''));
        $this->secretKey   = (string) $pick('momo_secret_key', config('momo.secret_key', ''));
        $this->endpoint    = (string) $pick('momo_endpoint', config('momo.endpoint', ''));
        $this->redirectUrl = (string) $pick('momo_return_url', config('momo.redirect_url', ''));
        $this->ipnUrl       = (string) $pick('momo_notify_url', config('momo.ipn_url', ''));
        $this->enabled       = $pick('momo_enable', '0') === '1';
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function taoYeuCauThanhToan(string $orderId, int $amount, string $orderInfo, string $extraData = ''): array
    {
        if (! $this->enabled) {
            throw new \RuntimeException('Cổng thanh toán MoMo hiện đang tắt. Vui lòng liên hệ quản trị viên.');
        }

        $requestId   = $orderId . '_' . time();
        $requestType = 'captureWallet';

        $rawHash = "accessKey={$this->accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$this->ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$this->partnerCode}&redirectUrl={$this->redirectUrl}&requestId={$requestId}&requestType={$requestType}";
        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $body = [
            'partnerCode' => $this->partnerCode,
            'accessKey'   => $this->accessKey,
            'requestId'   => $requestId,
            'amount'      => (string) $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl'      => $this->ipnUrl,
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
            'lang'        => 'vi',
        ];

        $response = Http::withOptions(['verify' => config('momo.verify_ssl', true)])
            ->post($this->endpoint, $body);

        return $response->json();
    }

    public function xacMinhChuKy(array $data): bool
    {
        $rawHash = "accessKey={$this->accessKey}&amount={$data['amount']}&extraData={$data['extraData']}&message={$data['message']}&orderId={$data['orderId']}&orderInfo={$data['orderInfo']}&orderType={$data['orderType']}&partnerCode={$data['partnerCode']}&payType={$data['payType']}&requestId={$data['requestId']}&responseTime={$data['responseTime']}&resultCode={$data['resultCode']}&transId={$data['transId']}";
        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        return $signature === $data['signature'];
    }
}
