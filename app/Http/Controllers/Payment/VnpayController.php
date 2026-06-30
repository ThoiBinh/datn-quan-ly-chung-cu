<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\HoaDon;
use App\Models\LichSuThanhToan;
use App\Models\NguonTao;
use App\Services\HoaDonService;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VnpayController extends Controller
{
    public function __construct(
        private VnpayService $vnpayService,
        private HoaDonService $hoaDonService
    ) {}

    /**
     * GET /payment/vnpay/return
     *
     * VNPay redirect người dùng về đây sau khi thanh toán.
     * Chỉ hiển thị kết quả — KHÔNG ghi nhận vào DB.
     * Việc cập nhật dữ liệu do IPN endpoint đảm nhận.
     */
    public function return(Request $request)
    {
        $responseCode = $request->vnp_ResponseCode ?? '';
        $txStatus     = $request->vnp_TransactionStatus ?? '';
        $success      = false;

        try {
            $success = ($responseCode === '00' && $txStatus === '00')
                && $this->vnpayService->xacMinhChuKy($request);
        } catch (\Throwable) {
            $success = false;
        }

        if (!$success) {
            Log::warning('[VNPay Return] Invalid or failed', [
                'responseCode' => $responseCode,
                'txStatus'     => $txStatus,
                'txnRef'       => $request->vnp_TxnRef ?? null,
            ]);
        }

        // Parse hoaDon ID từ txnRef (format: VNP{id}_{timestamp})
        preg_match('/^VNP(\d+)_/', $request->vnp_TxnRef ?? '', $matches);
        $hoaDon = isset($matches[1]) ? HoaDon::find($matches[1]) : null;

        // Lấy URL điều hướng từ session (set bởi admin/manager trước khi redirect sang VNPay)
        $showUrl  = session('vnpay_show_url');
        $indexUrl = session('vnpay_index_url');
        session()->forget(['vnpay_show_url', 'vnpay_index_url']);

        // Fallback
        if (!$showUrl && $hoaDon && auth('cudan')->check()) {
            $showUrl = route('resident.hoa-don.show', $hoaDon);
        }
        if (!$indexUrl) {
            $indexUrl = auth('cudan')->check()
                ? route('resident.hoa-don.index')
                : route('home');
        }

        return view('payment.vnpay.return', [
            'success'      => $success,
            'responseCode' => $responseCode,
            'amount'       => (int) ($request->vnp_Amount ?? 0) / 100,
            'txnRef'       => $request->vnp_TxnRef ?? '',
            'transactionNo' => $request->vnp_TransactionNo ?? '',
            'bankCode'     => $request->vnp_BankCode ?? '',
            'payDate'      => $request->vnp_PayDate ?? '',
            'orderInfo'    => $request->vnp_OrderInfo ?? '',
            'hoaDon'       => $hoaDon,
            'showUrl'      => $showUrl,
            'indexUrl'     => $indexUrl,
        ]);
    }

    /**
     * POST /payment/vnpay/ipn
     *
     * VNPay gọi server-to-server để thông báo kết quả.
     * Đây là nơi DUY NHẤT ghi nhận thanh toán vào DB.
     */
    public function ipn(Request $request)
    {
        $data = $request->all();
        Log::info('[VNPay IPN] Received', [
            'txnRef'       => $data['vnp_TxnRef'] ?? null,
            'responseCode' => $data['vnp_ResponseCode'] ?? null,
            'txStatus'     => $data['vnp_TransactionStatus'] ?? null,
        ]);

        // 1. Xác thực chữ ký
        try {
            $signatureValid = $this->vnpayService->xacMinhChuKy($request);
        } catch (\Throwable $e) {
            Log::warning('[VNPay IPN] Signature error', ['error' => $e->getMessage()]);
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature'], 200);
        }

        if (!$signatureValid) {
            Log::warning('[VNPay IPN] Signature mismatch', ['txnRef' => $data['vnp_TxnRef'] ?? null]);
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature'], 200);
        }

        // 2. Chỉ xử lý giao dịch thành công
        if (($data['vnp_ResponseCode'] ?? '') !== '00' || ($data['vnp_TransactionStatus'] ?? '') !== '00') {
            Log::info('[VNPay IPN] Non-success transaction, skip', [
                'responseCode' => $data['vnp_ResponseCode'] ?? null,
                'txStatus'     => $data['vnp_TransactionStatus'] ?? null,
            ]);
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success'], 200);
        }

        // 3. Parse hoaDon ID từ txnRef (format: VNP{id}_{timestamp})
        preg_match('/^VNP(\d+)_/', $data['vnp_TxnRef'] ?? '', $matches);
        $hoaDonId = $matches[1] ?? null;

        if (!$hoaDonId) {
            Log::error('[VNPay IPN] Cannot parse hoaDon from txnRef', ['txnRef' => $data['vnp_TxnRef'] ?? null]);
            return response()->json(['RspCode' => '01', 'Message' => 'Order Not Found'], 200);
        }

        // 4. Kiểm tra hóa đơn tồn tại
        $hoaDon = HoaDon::find($hoaDonId);
        if (!$hoaDon) {
            Log::error('[VNPay IPN] Invoice not found', ['hoaDonId' => $hoaDonId]);
            return response()->json(['RspCode' => '01', 'Message' => 'Order Not Found'], 200);
        }

        // 5. Kiểm tra amount hợp lệ (VNPay gửi amount * 100)
        $amount = (float) ($data['vnp_Amount'] ?? 0) / 100;
        if ($amount <= 0) {
            Log::error('[VNPay IPN] Invalid amount', ['amount' => $amount]);
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid Amount'], 200);
        }

        $transactionNo = (string) ($data['vnp_TransactionNo'] ?? '');

        // 6. Idempotency — tránh ghi trùng nếu VNPay gửi lại IPN
        if (LichSuThanhToan::where('ma_giao_dich', $transactionNo)->exists()) {
            Log::info('[VNPay IPN] Already processed, skip', ['transactionNo' => $transactionNo]);
            return response()->json(['RspCode' => '02', 'Message' => 'Order Already Confirmed'], 200);
        }

        // 7. Ghi nhận thanh toán
        $bankCode = $data['vnp_BankCode'] ?? 'VNPay';
        $this->hoaDonService->ghiNhanThanhToan(
            $hoaDon,
            $amount,
            'VNPay' . ($bankCode ? " ({$bankCode})" : ''),
            $transactionNo,
            NguonTao::VNPAY
        );

        Log::info('[VNPay IPN] Payment recorded', [
            'hoaDonId'     => $hoaDonId,
            'amount'       => $amount,
            'transactionNo' => $transactionNo,
        ]);

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success'], 200);
    }
}
