<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreHoaDonRequest;
use App\Http\Requests\Manager\UpdateHoaDonRequest;
use App\Models\CanHo;
use App\Models\CauHinhThanhToan;
use App\Models\ChiTietHoaDon;
use App\Models\HoaDon;
use App\Models\ToaNha;
use App\Services\AuditLogService;
use App\Services\HoaDonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    public function __construct(private HoaDonService $hoaDonService) {}

    private const SORTABLE = ['id', 'createdAt', 'tong_tien', 'han_thanh_toan', 'thang', 'nam'];

    public function index(Request $request)
    {
        $this->hoaDonService->capNhatTrangThaiTreHan();

        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = HoaDon::with(['canHo.toaNha', 'canHo.chuHo.cuDan']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ma_thanh_toan', 'like', "%$s%")
                  ->orWhereHas('canHo', fn ($q2) => $q2->where('so_can_ho', 'like', "%$s%"))
                  ->orWhereHas('canHo.toaNha', fn ($q2) => $q2->where('ten_toa_nha', 'like', "%$s%"))
                  ->orWhereHas('canHo.chuHo.cuDan', fn ($q2) => $q2->where('ho_ten', 'like', "%$s%"));
            });
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('thang')) {
            $query->where('thang', $request->thang);
        }
        if ($request->filled('nam')) {
            $query->where('nam', $request->nam);
        }
        if ($request->filled('toa_nha')) {
            $query->whereHas('canHo', fn ($q) => $q->where('toa_nha', $request->toa_nha));
        }

        $hoaDon   = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
        $dsToaNha = ToaNha::orderBy('ten_toa_nha')->get();
        $stats    = $this->layThongKe();

        return view('manager.hoa-don.index', compact('hoaDon', 'dsToaNha', 'sort', 'direction', 'stats'));
    }

    public function create()
    {
        $dsCanHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        return view('manager.hoa-don.create', compact('dsCanHo'));
    }

    public function canHoServices(Request $request)
    {
        $request->validate(['can_ho' => 'required|integer|exists:can_ho,id']);
        try {
            $services = $this->hoaDonService->layDichVuModal($request->can_ho);
            return response()->json(['services' => $services]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function syncCanHoServices(Request $request)
    {
        $request->validate([
            'can_ho'              => 'required|integer|exists:can_ho,id',
            'phi_dich_vu_ids'     => 'array',
            'phi_dich_vu_ids.*'   => 'integer|exists:phi_dich_vu,id',
        ]);

        try {
            $this->hoaDonService->syncDichVuCanHo($request->can_ho, $request->input('phi_dich_vu_ids', []));
            $services = $this->hoaDonService->layDichVuModal($request->can_ho);
            return response()->json(['services' => $services]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function previewPhi(Request $request)
    {
        $request->validate([
            'can_ho' => 'required|integer|exists:can_ho,id',
            'thang'  => 'required|integer|min:1|max:12',
            'nam'    => 'required|integer|min:2020',
        ]);

        try {
            $data = $this->hoaDonService->previewPhi($request->can_ho, $request->thang, $request->nam);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function store(StoreHoaDonRequest $request)
    {
        foreach ($request->input('chi_so', []) as $phiId => $values) {
            $cu  = (int) ($values['cu']  ?? 0);
            $moi = (int) ($values['moi'] ?? 0);
            if ($moi < $cu) {
                return back()->withInput()->with('error', 'Chỉ số mới không được nhỏ hơn chỉ số cũ.');
            }
        }

        $exists = HoaDon::where('can_ho', $request->can_ho)
            ->where('thang', $request->thang)
            ->where('nam', $request->nam)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Hóa đơn tháng này đã tồn tại cho căn hộ này.');
        }

        $excludedPhiIds = array_map('intval', $request->input('excluded_services', []));

        $hoaDon = $this->hoaDonService->taoHoaDon(
            $request->can_ho,
            $request->thang,
            $request->nam,
            $request->input('chi_so', []),
            $excludedPhiIds
        );

        return redirect()->route('manager.hoa-don.show', $hoaDon)->with('success', 'Tạo hóa đơn thành công.');
    }

    public function show(HoaDon $hoaDon)
    {
        $this->hoaDonService->capNhatTrangThaiTreHan();
        $hoaDon->refresh();
        $hoaDon->load([
            'canHo.toaNha', 'canHo.chuHo.cuDan', 'canHo.cuDanHienTai.cuDan',
            'chiTiet', 'nguoiCapNhat.chucVu',
            'lichSuThanhToan' => fn ($q) => $q->orderBy('ngay_thanh_toan'),
            'lichSuThanhToan.nguoiThanhToan',
            'lichSuThanhToan.nguonTao',
        ]);
        $phuongThuc = CauHinhThanhToan::where('trang_thai', 1)->orderBy('loai_phuong_thuc')->get();
        return view('manager.hoa-don.show', compact('hoaDon', 'phuongThuc'));
    }

    public function edit(HoaDon $hoaDon)
    {
        $this->hoaDonService->capNhatTrangThaiTreHan();
        $hoaDon->refresh();
        $hoaDon->load('chiTiet', 'canHo.toaNha');
        $isDaTT = $hoaDon->trang_thai === HoaDon::TRANG_THAI_DA_THANH_TOAN;
        return view('manager.hoa-don.edit', compact('hoaDon', 'isDaTT'));
    }

    public function update(UpdateHoaDonRequest $request, HoaDon $hoaDon)
    {
        $isCurrentlyChuaTT = $hoaDon->trang_thai == HoaDon::TRANG_THAI_CHUA_THANH_TOAN;

        if ($isCurrentlyChuaTT && $request->has('chi_tiet')) {
            foreach ($request->chi_tiet as $id => $values) {
                $cu  = (int) ($values['chi_so_cu']  ?? 0);
                $moi = (int) ($values['chi_so_moi'] ?? 0);
                if ($moi < $cu) {
                    return back()->withInput()->with('error', 'Chỉ số mới không được nhỏ hơn chỉ số cũ.');
                }
            }
        }

        $old = $hoaDon->toArray();

        DB::transaction(function () use ($request, $hoaDon, $isCurrentlyChuaTT) {
            $hoaDon->update(array_merge(
                $request->only('han_thanh_toan'),
                ['nguoi_cap_nhat' => auth('nhanvien')->id()]
            ));

            if ($isCurrentlyChuaTT && $request->has('chi_tiet')) {
                $this->hoaDonService->capNhatChiSo($hoaDon, $request->chi_tiet);
            }

            $this->hoaDonService->syncStatus($hoaDon->refresh());
        });

        AuditLogService::log('UPDATE', 'hoa_don', $hoaDon->id, $old, $hoaDon->fresh()->toArray());

        return redirect()->route('manager.hoa-don.show', $hoaDon)->with('success', 'Cập nhật hóa đơn thành công.');
    }

    public function destroy(HoaDon $hoaDon)
    {
        if ($hoaDon->trang_thai === HoaDon::TRANG_THAI_DA_THANH_TOAN) {
            return back()->with('error', 'Hóa đơn đã thanh toán nên không thể xóa.');
        }

        if ($hoaDon->lichSuThanhToan()->exists()) {
            return back()->with('error', 'Không thể xóa hóa đơn đã có lịch sử thanh toán. Vui lòng liên hệ quản trị viên nếu cần hỗ trợ.');
        }

        $old      = $hoaDon->toArray();
        $maHoaDon = $hoaDon->ma_thanh_toan;

        DB::transaction(function () use ($hoaDon, $old) {
            $hoaDon->chiTiet()->delete();
            $hoaDon->delete();
            AuditLogService::log('DELETE', 'hoa_don', $hoaDon->id, $old, null);
        });

        return redirect()->route('manager.hoa-don.index')->with('success', "Xóa hóa đơn «{$maHoaDon}» thành công.");
    }

    public function destroyChiTiet(HoaDon $hoaDon, ChiTietHoaDon $chiTiet)
    {
        if ($chiTiet->hoa_don !== $hoaDon->id) {
            return response()->json(['error' => 'Chi tiết không thuộc hóa đơn này.'], 403);
        }

        if ($hoaDon->lichSuThanhToan()->exists()) {
            return response()->json(['error' => 'Không thể xóa chi tiết hóa đơn vì hóa đơn này đã phát sinh lịch sử thanh toán.'], 422);
        }

        DB::transaction(function () use ($hoaDon, $chiTiet) {
            $chiTiet->delete();
            $this->hoaDonService->calculateInvoiceTotal($hoaDon);
            $this->hoaDonService->syncStatus($hoaDon->refresh());
        });

        $hoaDon->refresh();

        return response()->json([
            'success'       => true,
            'tong_tien'     => (float) $hoaDon->tong_tien,
            'tong_tien_fmt' => number_format((float) $hoaDon->tong_tien, 0, ',', '.') . 'đ',
        ]);
    }

    private function layThongKe(): array
    {
        $row = HoaDon::whereIn('trang_thai', [
            HoaDon::TRANG_THAI_CHUA_THANH_TOAN,
            HoaDon::TRANG_THAI_QUA_HAN,
        ])->selectRaw('COALESCE(SUM(tong_tien - so_tien_da_thanh_toan), 0) as cong_no')->first();

        return [
            'tong'      => HoaDon::count(),
            'chua_tt'   => HoaDon::where('trang_thai', HoaDon::TRANG_THAI_CHUA_THANH_TOAN)->count(),
            'da_tt'     => HoaDon::where('trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)->count(),
            'qua_han'   => HoaDon::where('trang_thai', HoaDon::TRANG_THAI_QUA_HAN)->count(),
            'doanh_thu' => (float) HoaDon::where('trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)->sum('so_tien_da_thanh_toan'),
            'cong_no'   => (float) ($row?->cong_no ?? 0),
        ];
    }
}
