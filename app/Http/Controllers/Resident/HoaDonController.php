<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\HoaDon;
use App\Models\LichSuThanhToan;
use App\Models\NguonTao;
use App\Services\HoaDonService;
use App\Services\MomoService;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HoaDonController extends Controller
{
    public function __construct(
        private HoaDonService $hoaDonService,
        private MomoService $momoService,
        private VnpayService $vnpayService
    ) {}

    public function index(Request $request)
    {
        $cuDan = auth('cudan')->user();
        $canHoIds = $cuDan?->canHoIdsHienTai() ?? collect();

        if ($canHoIds->isEmpty()) {
            return view('resident.hoa-don.index', ['hoaDon' => collect()]);
        }

        $query = HoaDon::whereIn('can_ho', $canHoIds)->with('canHo.toaNha');

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', (int) $request->trang_thai);
        }

        $hoaDon = $query->orderByDesc('nam')->orderByDesc('thang')->paginate(12)->withQueryString();

        return view('resident.hoa-don.index', compact('hoaDon'));
    }

    public function show(HoaDon $hoaDon)
    {
        $this->authorize_canho($hoaDon);
        $hoaDon->load(['chiTiet', 'lichSuThanhToan.nguonTao', 'canHo.toaNha']);
        $conNo = max(0, (float) $hoaDon->tong_tien - (float) $hoaDon->so_tien_da_thanh_toan);

        return view('resident.hoa-don.show', compact('hoaDon', 'conNo'));
    }

    public function thanhToanMomo(Request $request, HoaDon $hoaDon)
    {
        $this->authorize_canho($hoaDon);
        $hoaDon->refresh();

        $conNo = max(0, (float) $hoaDon->tong_tien - (float) $hoaDon->so_tien_da_thanh_toan);
        if ($conNo <= 0) {
            return back()->with('error', 'Hóa đơn đã được thanh toán đầy đủ.');
        }

        $request->validate([
            'so_tien' => ['required', 'numeric', 'min:1', 'max:' . (int) $conNo],
        ], [
            'so_tien.required' => 'Vui lòng nhập số tiền.',
            'so_tien.numeric'  => 'Số tiền phải là số.',
            'so_tien.min'      => 'Số tiền phải lớn hơn 0.',
            'so_tien.max'      => 'Số tiền không được lớn hơn ' . number_format($conNo, 0, ',', '.') . 'đ.',
        ]);

        $amount    = (int) $request->so_tien;
        $orderId   = 'HD' . $hoaDon->id . '_' . time();
        $orderInfo = 'Thanh toan HD ' . $hoaDon->ma_thanh_toan;

        session([
            'momo_show_url'  => route('resident.hoa-don.show', $hoaDon),
            'momo_index_url' => route('resident.hoa-don.index'),
        ]);

        try {
            Log::info('[MoMo Resident] Creating payment', ['orderId' => $orderId, 'amount' => $amount, 'hoaDon' => $hoaDon->id]);
            $result = $this->momoService->taoYeuCauThanhToan($orderId, $amount, $orderInfo);

            if (!empty($result['payUrl'])) {
                return redirect($result['payUrl']);
            }

            Log::warning('[MoMo Resident] No payUrl', ['result' => $result, 'hoaDon' => $hoaDon->id]);
            return back()->with('error', 'MoMo: ' . ($result['message'] ?? 'Không thể tạo giao dịch. Vui lòng thử lại.'));

        } catch (\Exception $e) {
            Log::error('[MoMo Resident] Exception', ['message' => $e->getMessage(), 'hoaDon' => $hoaDon->id]);
            return back()->with('error', 'Lỗi kết nối MoMo: ' . $e->getMessage());
        }
    }

    public function thanhToanVnpay(Request $request, HoaDon $hoaDon)
    {
        $this->authorize_canho($hoaDon);
        $hoaDon->refresh();

        $conNo = max(0, (float) $hoaDon->tong_tien - (float) $hoaDon->so_tien_da_thanh_toan);
        if ($conNo <= 0) {
            return back()->with('error', 'Hóa đơn đã được thanh toán đầy đủ.');
        }

        $request->validate([
            'so_tien' => ['required', 'numeric', 'min:1', 'max:' . (int) $conNo],
        ], [
            'so_tien.required' => 'Vui lòng nhập số tiền.',
            'so_tien.numeric'  => 'Số tiền phải là số.',
            'so_tien.min'      => 'Số tiền phải lớn hơn 0.',
            'so_tien.max'      => 'Số tiền không được lớn hơn ' . number_format($conNo, 0, ',', '.') . 'đ.',
        ]);

        $amount    = (int) $request->so_tien;
        $txnRef    = 'VNP' . $hoaDon->id . '_' . time();
        $orderInfo = 'Thanh toan HD ' . $hoaDon->ma_thanh_toan;

        session([
            'vnpay_show_url'  => route('resident.hoa-don.show', $hoaDon),
            'vnpay_index_url' => route('resident.hoa-don.index'),
        ]);

        try {
            Log::info('[VNPay Resident] Creating payment', ['txnRef' => $txnRef, 'amount' => $amount, 'hoaDon' => $hoaDon->id]);
            $payUrl = $this->vnpayService->taoUrlThanhToan($txnRef, $amount, $orderInfo);
            return redirect($payUrl);
        } catch (\Exception $e) {
            Log::error('[VNPay Resident] Exception', ['message' => $e->getMessage(), 'hoaDon' => $hoaDon->id]);
            return back()->with('error', 'Lỗi tạo thanh toán VNPay: ' . $e->getMessage());
        }
    }

    // Legacy — redirect URL đã chuyển sang global payment controllers
    public function callbackMomo(Request $request)
    {
        return redirect()->route('resident.hoa-don.index');
    }

    public function callbackVnpay(Request $request)
    {
        return redirect()->route('resident.hoa-don.index');
    }

    private function authorize_canho(HoaDon $hoaDon): void
    {
        $cuDan = auth('cudan')->user();
        $canHoIds = $cuDan?->canHoIdsHienTai() ?? collect();
        if (!$cuDan || !$canHoIds->contains($hoaDon->can_ho)) {
            abort(403, 'Bạn không có quyền xem hóa đơn này.');
        }
    }
}
