<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThanhToanHoaDonRequest;
use App\Models\CauHinhThanhToan;
use App\Models\HoaDon;
use App\Models\LichSuThanhToan;
use App\Models\NguonTao;
use App\Services\HoaDonService;
use App\Services\MomoService;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ThanhToanController extends Controller
{
    public function __construct(
        private HoaDonService $hoaDonService,
        private MomoService $momoService,
        private VnpayService $vnpayService
    ) {}

    public function index(Request $request)
    {
        $query = LichSuThanhToan::with([
            'hoaDon.canHo.toaNha',
            'hoaDon.canHo.chuHo.cuDan',
            'nguonTao',
        ])->orderByDesc('createdAt');

        if ($request->filled('phuong_thuc')) {
            $query->where('phuong_thuc_thanh_toan', 'like', '%' . $request->phuong_thuc . '%');
        }
        if ($request->filled('tu_ngay')) {
            $query->whereDate('ngay_thanh_toan', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('ngay_thanh_toan', '<=', $request->den_ngay);
        }
        if ($request->filled('hoa_don_id')) {
            $query->where('hoa_don', $request->hoa_don_id);
        }

        $lichSuList  = $query->paginate(20)->withQueryString();
        $tongDoanhThu = LichSuThanhToan::sum('so_tien');

        return view('admin.thanh-toan.index', compact('lichSuList', 'tongDoanhThu'));
    }

    public function create(HoaDon $hoaDon)
    {
        $conNo = max(0, (float) $hoaDon->tong_tien - (float) $hoaDon->so_tien_da_thanh_toan);
        if ($conNo <= 0) {
            return redirect()->route('admin.hoa-don.show', $hoaDon)
                ->with('error', 'Hóa đơn đã được thanh toán đầy đủ.');
        }

        $hoaDon->load([
            'canHo.toaNha',
            'canHo.chuHo.cuDan',
            'canHo.cuDanHienTai.cuDan',
            'lichSuThanhToan' => fn ($q) => $q->orderBy('ngay_thanh_toan'),
            'lichSuThanhToan.nguonTao',
        ]);

        $phuongThuc = CauHinhThanhToan::where('trang_thai', 1)->orderBy('loai_phuong_thuc')->get();

        return view('admin.thanh-toan.create', compact('hoaDon', 'phuongThuc', 'conNo'));
    }

    public function store(ThanhToanHoaDonRequest $request, HoaDon $hoaDon)
    {
        $conNo = max(0, (float) $hoaDon->tong_tien - (float) $hoaDon->so_tien_da_thanh_toan);
        if ((float) $request->so_tien > $conNo) {
            return back()->withInput()->with(
                'error',
                'Số tiền thanh toán không được lớn hơn số tiền còn nợ (' . number_format($conNo, 0, ',', '.') . 'đ).'
            );
        }

        $this->hoaDonService->ghiNhanThanhToan(
            $hoaDon,
            (float) $request->so_tien,
            $request->phuong_thuc_thanh_toan,
            null,
            NguonTao::ADMIN,
            $request->ngay_thanh_toan,
            $request->ghi_chu,
            $request->nguoi_thanh_toan ? (int) $request->nguoi_thanh_toan : null
        );

        return redirect()->route('admin.hoa-don.show', $hoaDon)
            ->with('success', 'Ghi nhận thanh toán ' . number_format($request->so_tien, 0, ',', '.') . 'đ thành công.');
    }

    public function show(LichSuThanhToan $lichSu)
    {
        $lichSu->load([
            'hoaDon.canHo.toaNha',
            'hoaDon.canHo.chuHo.cuDan',
            'nguoiThanhToan',
            'nguonTao',
        ]);

        return view('admin.thanh-toan.show', compact('lichSu'));
    }

    public function initiateMomo(Request $request, HoaDon $hoaDon)
    {
        $hoaDon->refresh();
        $conNo = max(0, (float) $hoaDon->tong_tien - (float) $hoaDon->so_tien_da_thanh_toan);

        if ($conNo <= 0 || $hoaDon->trang_thai === HoaDon::TRANG_THAI_DA_THANH_TOAN) {
            return back()->with('error', 'Hóa đơn đã được thanh toán đầy đủ.');
        }

        $request->validate([
            'so_tien' => ['required', 'numeric', 'min:1', 'max:' . (int) $conNo],
        ], [
            'so_tien.required' => 'Vui lòng nhập số tiền.',
            'so_tien.numeric'  => 'Số tiền phải là số.',
            'so_tien.min'      => 'Số tiền phải lớn hơn 0.',
            'so_tien.max'      => 'Số tiền không được lớn hơn số tiền còn nợ (' . number_format($conNo, 0, ',', '.') . 'đ).',
        ]);

        $amount  = (int) $request->so_tien;
        $orderId = 'HD' . $hoaDon->id . '_' . time();

        // Lưu URL điều hướng vào session để trang return hiển thị đúng link
        session([
            'momo_show_url'  => route('admin.hoa-don.show', $hoaDon),
            'momo_index_url' => route('admin.hoa-don.index'),
        ]);

        $orderInfo = 'Thanh toan HD ' . $hoaDon->ma_thanh_toan;

        try {
            $result = $this->momoService->taoYeuCauThanhToan($orderId, $amount, $orderInfo);

            if (!empty($result['payUrl'])) {
                return redirect($result['payUrl']);
            }

            Log::warning('[MoMo Admin] No payUrl in response', ['result' => $result, 'hoaDon' => $hoaDon->id]);
            return back()->with('error', 'MoMo: ' . ($result['message'] ?? 'Không thể tạo giao dịch MoMo.'));

        } catch (\Exception $e) {
            Log::error('[MoMo Admin] Exception', ['message' => $e->getMessage(), 'hoaDon' => $hoaDon->id]);
            return back()->with('error', 'Lỗi kết nối MoMo: ' . $e->getMessage());
        }
    }

    public function initiateVnpay(Request $request, HoaDon $hoaDon)
    {
        $hoaDon->refresh();
        $conNo = max(0, (float) $hoaDon->tong_tien - (float) $hoaDon->so_tien_da_thanh_toan);

        if ($conNo <= 0 || $hoaDon->trang_thai === HoaDon::TRANG_THAI_DA_THANH_TOAN) {
            return back()->with('error', 'Hóa đơn đã được thanh toán đầy đủ.');
        }

        $request->validate([
            'so_tien' => ['required', 'numeric', 'min:1', 'max:' . (int) $conNo],
        ], [
            'so_tien.required' => 'Vui lòng nhập số tiền.',
            'so_tien.numeric'  => 'Số tiền phải là số.',
            'so_tien.min'      => 'Số tiền phải lớn hơn 0.',
            'so_tien.max'      => 'Số tiền không được lớn hơn số tiền còn nợ (' . number_format($conNo, 0, ',', '.') . 'đ).',
        ]);

        $amount    = (int) $request->so_tien;
        $txnRef    = 'VNP' . $hoaDon->id . '_' . time();
        $orderInfo = 'Thanh toan HD ' . $hoaDon->ma_thanh_toan;

        session([
            'vnpay_show_url'  => route('admin.hoa-don.show', $hoaDon),
            'vnpay_index_url' => route('admin.hoa-don.index'),
        ]);

        try {
            Log::info('[VNPay Admin] Creating payment URL', ['txnRef' => $txnRef, 'amount' => $amount, 'hoaDon' => $hoaDon->id]);
            $payUrl = $this->vnpayService->taoUrlThanhToan($txnRef, $amount, $orderInfo);
            return redirect($payUrl);
        } catch (\Exception $e) {
            Log::error('[VNPay Admin] Exception', ['message' => $e->getMessage(), 'hoaDon' => $hoaDon->id]);
            return back()->with('error', 'Lỗi tạo thanh toán VNPay: ' . $e->getMessage());
        }
    }
}
