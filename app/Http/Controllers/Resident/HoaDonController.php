<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\HoaDon;
use App\Models\NguonTao;
use App\Services\HoaDonService;
use App\Services\MomoService;
use App\Services\VnpayService;
use Illuminate\Http\Request;

class HoaDonController extends Controller
{
    public function __construct(
        private HoaDonService $hoaDonService,
        private MomoService $momoService,
        private VnpayService $vnpayService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $cuDan = $user->cuDan;

        if (!$cuDan || !$cuDan->canHoHienTai) {
            return view('resident.hoa-don.index', ['hoaDon' => collect()]);
        }

        $canHoId = $cuDan->canHoHienTai->can_ho;
        $hoaDon = HoaDon::where('can_ho', $canHoId)
            ->orderByDesc('nam')->orderByDesc('thang')
            ->paginate(12);

        return view('resident.hoa-don.index', compact('hoaDon'));
    }

    public function show(HoaDon $hoaDon)
    {
        $this->authorize_canho($hoaDon);
        $hoaDon->load(['chiTiet.phiDichVu', 'lichSuThanhToan', 'canHo']);
        return view('resident.hoa-don.show', compact('hoaDon'));
    }

    public function thanhToanMomo(HoaDon $hoaDon)
    {
        $this->authorize_canho($hoaDon);

        if ($hoaDon->trang_thai == HoaDon::TRANG_THAI_DA_THANH_TOAN) {
            return back()->with('error', 'Hóa đơn đã được thanh toán.');
        }

        $soTienConLai = (int)$hoaDon->conNo();
        $result = $this->momoService->taoYeuCauThanhToan(
            'HD' . $hoaDon->id . '_' . time(),
            $soTienConLai,
            'Thanh toán hóa đơn ' . $hoaDon->ma_thanh_toan
        );

        if (isset($result['payUrl'])) {
            return redirect($result['payUrl']);
        }

        return back()->with('error', 'Không thể kết nối đến MoMo. Vui lòng thử lại sau.');
    }

    public function callbackMomo(Request $request)
    {
        if ($request->resultCode != 0) {
            return redirect()->route('resident.hoa-don.index')->with('error', 'Thanh toán MoMo thất bại.');
        }

        if (!$this->momoService->xacMinhChuKy($request->all())) {
            return redirect()->route('resident.hoa-don.index')->with('error', 'Chữ ký không hợp lệ.');
        }

        preg_match('/^HD(\d+)_/', $request->orderId, $matches);
        $hoaDonId = $matches[1] ?? null;

        if ($hoaDonId) {
            $hoaDon = HoaDon::find($hoaDonId);
            if ($hoaDon && $hoaDon->trang_thai != HoaDon::TRANG_THAI_DA_THANH_TOAN) {
                $this->hoaDonService->ghiNhanThanhToan(
                    $hoaDon,
                    (float)$request->amount,
                    'MoMo',
                    $request->transId,
                    NguonTao::MOMO
                );
            }
        }

        return redirect()->route('resident.hoa-don.index')->with('success', 'Thanh toán MoMo thành công!');
    }

    public function thanhToanVnpay(HoaDon $hoaDon)
    {
        $this->authorize_canho($hoaDon);

        if ($hoaDon->trang_thai == HoaDon::TRANG_THAI_DA_THANH_TOAN) {
            return back()->with('error', 'Hóa đơn đã được thanh toán.');
        }

        $url = $this->vnpayService->taoUrlThanhToan(
            'HD' . $hoaDon->id . '_' . time(),
            (int)$hoaDon->conNo(),
            'Thanh toán hóa đơn ' . $hoaDon->ma_thanh_toan
        );

        return redirect($url);
    }

    public function callbackVnpay(Request $request)
    {
        if ($request->vnp_ResponseCode != '00') {
            return redirect()->route('resident.hoa-don.index')->with('error', 'Thanh toán VNPay thất bại.');
        }

        if (!$this->vnpayService->xacMinhChuKy($request)) {
            return redirect()->route('resident.hoa-don.index')->with('error', 'Chữ ký không hợp lệ.');
        }

        preg_match('/^HD(\d+)_/', $request->vnp_TxnRef, $matches);
        $hoaDonId = $matches[1] ?? null;

        if ($hoaDonId) {
            $hoaDon = HoaDon::find($hoaDonId);
            if ($hoaDon && $hoaDon->trang_thai != HoaDon::TRANG_THAI_DA_THANH_TOAN) {
                $this->hoaDonService->ghiNhanThanhToan(
                    $hoaDon,
                    (float)$request->vnp_Amount / 100,
                    'VNPay',
                    $request->vnp_TransactionNo,
                    NguonTao::VNPAY
                );
            }
        }

        return redirect()->route('resident.hoa-don.index')->with('success', 'Thanh toán VNPay thành công!');
    }

    private function authorize_canho(HoaDon $hoaDon): void
    {
        $cuDan = auth()->user()->cuDan;
        if (!$cuDan || !$cuDan->canHoHienTai || $cuDan->canHoHienTai->can_ho != $hoaDon->can_ho) {
            abort(403, 'Bạn không có quyền xem hóa đơn này.');
        }
    }
}
