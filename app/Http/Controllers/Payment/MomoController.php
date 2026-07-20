<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\HoaDon;
use App\Models\LichSuThanhToan;
use App\Models\NguonTao;
use App\Services\HoaDonService;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MomoController extends Controller
{
    public function __construct(
        private MomoService $momoService,
        private HoaDonService $hoaDonService
    ) {}

    /**
     * GET /payment/momo/return
     *
     * MoMo redirect người dùng về đây sau khi thanh toán.
     * Chỉ hiển thị kết quả — KHÔNG ghi nhận thanh toán vào DB.
     * Việc cập nhật dữ liệu do IPN endpoint đảm nhận.
     */
    public function return(Request $request)
    {
        $resultCode = (int) ($request->resultCode ?? -1);
        $success    = false;

        try {
            $success = ($resultCode === 0) && $this->momoService->xacMinhChuKy($request->all());
        } catch (\Throwable) {
            $success = false;
        }

        preg_match('/^HD(\d+)_/', $request->orderId ?? '', $matches);
        $hoaDon = isset($matches[1]) ? HoaDon::find($matches[1]) : null;

        // Lấy URL điều hướng từ session (được set bởi admin/manager trước khi redirect sang MoMo)
        $showUrl  = session('momo_show_url');
        $indexUrl = session('momo_index_url');
        session()->forget(['momo_show_url', 'momo_index_url']);

        // Fallback dựa theo guard đang đăng nhập
        if (!$showUrl && $hoaDon) {
            if (auth('cudan')->check()) {
                $showUrl = route('resident.hoa-don.show', $hoaDon);
            }
        }
        if (!$indexUrl) {
            $indexUrl = auth('cudan')->check()
                ? route('resident.hoa-don.index')
                : route('home');
        }

        return view('payment.momo.return', [
            'success'    => $success,
            'resultCode' => $resultCode,
            'message'    => $request->message ?? '',
            'amount'     => (int) ($request->amount ?? 0),
            'transId'    => $request->transId ?? '',
            'orderId'    => $request->orderId ?? '',
            'hoaDon'     => $hoaDon,
            'showUrl'    => $showUrl,
            'indexUrl'   => $indexUrl,
        ]);
    }

    /**
     * POST /payment/momo/ipn
     *
     * MoMo gọi server-to-server để thông báo kết quả thanh toán.
     * Đây là nơi DUY NHẤT ghi nhận thanh toán vào DB.
     *
     * @see https://developers.momo.vn/#/docs/en/aiov2/?id=payment-notification
     */
    public function ipn(Request $request)
    {
        $data = $request->all();
        Log::info('[MoMo IPN] Received', ['orderId' => $data['orderId'] ?? null, 'resultCode' => $data['resultCode'] ?? null]);

        // 1. Xác thực chữ ký
        try {
            $signatureValid = $this->momoService->xacMinhChuKy($data);
        } catch (\Throwable $e) {
            Log::warning('[MoMo IPN] Signature error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        if (!$signatureValid) {
            Log::warning('[MoMo IPN] Signature mismatch', ['orderId' => $data['orderId'] ?? null]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // 2. Chỉ xử lý khi MoMo xác nhận giao dịch thành công
        if ((int) ($data['resultCode'] ?? -1) !== 0) {
            Log::info('[MoMo IPN] Non-zero resultCode, skip', ['resultCode' => $data['resultCode'] ?? null]);
            return response()->noContent(); // HTTP 204
        }

        // 3. Parse hoaDon ID từ orderId (format: HD{id}_{timestamp})
        preg_match('/^HD(\d+)_/', $data['orderId'] ?? '', $matches);
        $hoaDonId = $matches[1] ?? null;

        if (!$hoaDonId) {
            Log::error('[MoMo IPN] Cannot parse hoaDon from orderId', ['orderId' => $data['orderId'] ?? null]);
            return response()->json(['message' => 'Invalid orderId format'], 400);
        }

        // 4. Kiểm tra hóa đơn tồn tại
        $hoaDon = HoaDon::find($hoaDonId);
        if (!$hoaDon) {
            Log::error('[MoMo IPN] Invoice not found', ['hoaDonId' => $hoaDonId]);
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        // 5. Kiểm tra amount hợp lệ
        $amount = (float) ($data['amount'] ?? 0);
        if ($amount <= 0) {
            Log::error('[MoMo IPN] Invalid amount', ['amount' => $amount]);
            return response()->json(['message' => 'Invalid amount'], 400);
        }

        $transId = (string) ($data['transId'] ?? '');

        // 6. Idempotency — tránh ghi trùng nếu MoMo gửi lại IPN
        if (LichSuThanhToan::where('ma_giao_dich', $transId)->exists()) {
            Log::info('[MoMo IPN] Already processed, skip', ['transId' => $transId]);
            return response()->noContent(); // HTTP 204 — thành công để MoMo không retry
        }

        // 7. Ghi nhận thanh toán (ghiNhanThanhToan đã bao gồm DB::transaction)
        $this->hoaDonService->ghiNhanThanhToan(
            $hoaDon,
            $amount,
            'MoMo',
            $transId,
            NguonTao::MOMO
        );

        Log::info('[MoMo IPN] Payment recorded', ['hoaDonId' => $hoaDonId, 'amount' => $amount, 'transId' => $transId]);

        return response()->noContent(); // HTTP 204
    }
}
