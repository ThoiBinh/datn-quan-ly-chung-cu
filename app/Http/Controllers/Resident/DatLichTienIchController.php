<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resident\StoreDatLichTienIchRequest;
use App\Models\CanHo;
use App\Models\CuDanCanHo;
use App\Models\DatLichTienIch;
use App\Models\TienIch;
use App\Services\BookingCapacityService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DatLichTienIchController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    /**
     * Các căn hộ mà cư dân đang đăng nhập hiện đang cư trú (trang_thai = 1
     * trên cu_dan_can_ho) — đây là danh sách căn hộ hợp lệ để chọn khi đặt lịch.
     */
    private function dsCanHoCuaCuDan(int $cuDanId)
    {
        $canHoIds = CuDanCanHo::where('cu_dan', $cuDanId)
            ->where('trang_thai', 1)
            ->pluck('can_ho');

        return CanHo::with('toaNha')->whereIn('id', $canHoIds)->orderBy('so_can_ho')->get();
    }

    public function index(Request $request)
    {
        $cuDanId = auth('cudan')->id();

        $query = DatLichTienIch::where('cu_dan', $cuDanId)
            ->with(['tienIch.loaiTienIch', 'canHo.toaNha']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ma_dat_lich', 'like', "%{$s}%")
                    ->orWhereHas('tienIch', fn ($qt) => $qt->where('ten_tien_ich', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('ngay_su_dung')) {
            $query->whereDate('thoi_gian_bat_dau', $request->ngay_su_dung);
        }

        $dsDatLich = $query->orderByDesc('thoi_gian_bat_dau')->paginate(10)->withQueryString();
        $dsTrangThai = DatLichTienIch::dsTrangThai();

        return view('resident.dat-lich-tien-ich.index', compact('dsDatLich', 'dsTrangThai'));
    }

    public function create(Request $request)
    {
        $cuDanId = auth('cudan')->id();
        $dsCanHo = $this->dsCanHoCuaCuDan($cuDanId);
        $dsTienIch = TienIch::active()->where('can_dat_truoc', true)->with('loaiTienIch')->orderBy('ten_tien_ich')->get();
        $tienIchDaChon = $request->integer('tien_ich') ?: null;

        return view('resident.dat-lich-tien-ich.create', compact('dsCanHo', 'dsTienIch', 'tienIchDaChon'));
    }

    /**
     * Sức chứa còn lại của tiện ích cho một khung giờ cụ thể — API đọc phục vụ
     * Card sức chứa real-time trên form đặt lịch (AJAX), không thay đổi dữ liệu.
     * Cùng field name với BookingSlotUpdated::broadcastWith() để JS dùng chung
     * một hàm chuẩn hoá cho cả kết quả AJAX lẫn broadcast Reverb.
     */
    public function sucChua(Request $request, TienIch $tienIch, BookingCapacityService $capacityService)
    {
        $data = $request->validate([
            'thoi_gian_bat_dau'  => ['required', 'date'],
            'thoi_gian_ket_thuc' => ['required', 'date', 'after:thoi_gian_bat_dau'],
        ]);

        $batDau  = Carbon::parse($data['thoi_gian_bat_dau']);
        $ketThuc = Carbon::parse($data['thoi_gian_ket_thuc']);

        $daDuyet = $capacityService->tongNguoiDaDuyetGiaoNhau($tienIch->id, $batDau, $ketThuc);
        $sucChua = (int) ($tienIch->suc_chua ?? 0);

        return response()->json([
            'tien_ich'           => $tienIch->id,
            'thoi_gian_bat_dau'  => $batDau->toIso8601String(),
            'thoi_gian_ket_thuc' => $ketThuc->toIso8601String(),
            'suc_chua'           => $sucChua,
            'da_duyet'           => $daDuyet,
            'con_lai'            => $sucChua ? max(0, $sucChua - $daDuyet) : null,
            'day'                => $sucChua ? ($daDuyet >= $sucChua) : false,
        ]);
    }

    public function store(StoreDatLichTienIchRequest $request)
    {
        $data = $request->validated();
        $data['cu_dan'] = auth('cudan')->id();

        $datLich = $this->bookingService->taoDatLich($data);

        // Đủ sức chứa và không ai đứng trước -> đã Đã duyệt ngay; ngược lại
        // -> Chờ duyệt, xếp vào hàng đợi FIFO (xem BookingApprovalService::taoDatLich()).
        $thongBao = (int) $datLich->trang_thai === DatLichTienIch::TRANG_THAI_DA_DUYET
            ? "Đặt lịch «{$datLich->ma_dat_lich}» thành công."
            : "Đặt lịch «{$datLich->ma_dat_lich}» đã được đưa vào hàng chờ.";

        return redirect()->route('resident.dat-lich-tien-ich.show', $datLich)
            ->with('success', $thongBao);
    }

    public function show(DatLichTienIch $datLichTienIch)
    {
        if ($datLichTienIch->cu_dan !== auth('cudan')->id()) {
            abort(403);
        }

        $datLichTienIch->load(['tienIch.loaiTienIch', 'canHo.toaNha']);

        return view('resident.dat-lich-tien-ich.show', compact('datLichTienIch'));
    }

    public function cancel(DatLichTienIch $datLichTienIch)
    {
        if ($datLichTienIch->cu_dan !== auth('cudan')->id()) {
            abort(403);
        }

        $datLich = $this->bookingService->huyBoiCuDan($datLichTienIch);

        return redirect()->route('resident.dat-lich-tien-ich.show', $datLich)
            ->with('success', "Đã hủy lịch đặt tiện ích «{$datLich->ma_dat_lich}».");
    }
}
