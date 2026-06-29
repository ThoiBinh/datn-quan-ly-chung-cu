<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHoaDonRequest;
use App\Http\Requests\Admin\UpdateHoaDonRequest;
use App\Models\CanHo;
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

        return view('admin.hoa-don.index', compact('hoaDon', 'dsToaNha', 'sort', 'direction', 'stats'));
    }

    public function create()
    {
        $dsCanHo = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        return view('admin.hoa-don.create', compact('dsCanHo'));
    }

    public function canHoServices(Request $request)
    {
        $request->validate(['can_ho' => 'required|integer|exists:can_ho,id']);
        try {
            $services = $this->hoaDonService->layDichVuCanHo($request->can_ho);
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
        // Validate chi_so: moi >= cu
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

        return redirect()->route('admin.hoa-don.show', $hoaDon)->with('success', 'Tạo hóa đơn thành công.');
    }

    public function show(HoaDon $hoaDon)
    {
        $this->hoaDonService->capNhatTrangThaiTreHan();
        $hoaDon->refresh();
        $hoaDon->load([
            'canHo.toaNha', 'canHo.chuHo.cuDan',
            'chiTiet', 'nguoiCapNhat.chucVu',
            'lichSuThanhToan.nguoiThanhToan', 'lichSuThanhToan.nguonTao',
        ]);
        $isCanHuy = $hoaDon->trang_thai != HoaDon::TRANG_THAI_DA_HUY;
        return view('admin.hoa-don.show', compact('hoaDon', 'isCanHuy'));
    }

    public function edit(HoaDon $hoaDon)
    {
        $this->hoaDonService->capNhatTrangThaiTreHan();
        $hoaDon->refresh();
        $hoaDon->load('chiTiet', 'canHo.toaNha');
        $isDaTT = $hoaDon->trang_thai === HoaDon::TRANG_THAI_DA_THANH_TOAN;
        return view('admin.hoa-don.edit', compact('hoaDon', 'isDaTT'));
    }

    public function update(UpdateHoaDonRequest $request, HoaDon $hoaDon)
    {
        // chi_so edit only when CHUA_THANH_TOAN
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
                $request->only('han_thanh_toan', 'trang_thai'),
                ['nguoi_cap_nhat' => auth('nhanvien')->id()]
            ));

            if ($isCurrentlyChuaTT && $request->has('chi_tiet')) {
                $this->hoaDonService->capNhatChiSo($hoaDon, $request->chi_tiet);
            }
        });

        AuditLogService::log('UPDATE', 'hoa_don', $hoaDon->id, $old, $hoaDon->fresh()->toArray());

        return redirect()->route('admin.hoa-don.show', $hoaDon)->with('success', 'Cập nhật hóa đơn thành công.');
    }

    public function destroy(HoaDon $hoaDon)
    {
        if ($hoaDon->trang_thai === HoaDon::TRANG_THAI_DA_THANH_TOAN) {
            return back()->with('error', 'Hóa đơn đã thanh toán nên không thể xóa.');
        }

        $old      = $hoaDon->toArray();
        $maHoaDon = $hoaDon->ma_thanh_toan;

        DB::transaction(function () use ($hoaDon, $old) {
            $hoaDon->chiTiet()->delete();
            $hoaDon->delete();
            AuditLogService::log('DELETE', 'hoa_don', $hoaDon->id, $old, null);
        });

        return redirect()->route('admin.hoa-don.index')->with('success', "Xóa hóa đơn «{$maHoaDon}» thành công.");
    }

    public function destroyChiTiet(HoaDon $hoaDon, ChiTietHoaDon $chiTiet)
    {
        if ($chiTiet->hoa_don !== $hoaDon->id) {
            return response()->json(['error' => 'Chi tiết không thuộc hóa đơn này.'], 403);
        }
        if ($hoaDon->trang_thai === HoaDon::TRANG_THAI_DA_THANH_TOAN) {
            return response()->json(['error' => 'Không thể xóa chi tiết hóa đơn đã thanh toán.'], 403);
        }

        DB::transaction(function () use ($hoaDon, $chiTiet) {
            $chiTiet->delete();
            $this->hoaDonService->calculateInvoiceTotal($hoaDon);
        });

        $hoaDon->refresh();

        return response()->json([
            'success'       => true,
            'tong_tien'     => (float) $hoaDon->tong_tien,
            'tong_tien_fmt' => number_format((float) $hoaDon->tong_tien, 0, ',', '.') . 'đ',
        ]);
    }

    public function toggleStatus(HoaDon $hoaDon)
    {
        $old    = $hoaDon->toArray();
        $newVal = $hoaDon->trang_thai == HoaDon::TRANG_THAI_DA_HUY
            ? HoaDon::TRANG_THAI_CHUA_THANH_TOAN
            : HoaDon::TRANG_THAI_DA_HUY;

        $hoaDon->update(['trang_thai' => $newVal, 'nguoi_cap_nhat' => auth('nhanvien')->id()]);
        AuditLogService::log('UPDATE', 'hoa_don', $hoaDon->id, $old, $hoaDon->fresh()->toArray());

        $msg = $newVal == HoaDon::TRANG_THAI_DA_HUY ? 'Hủy' : 'Khôi phục';
        return back()->with('success', "$msg hóa đơn «{$hoaDon->ma_thanh_toan}» thành công.");
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
            'da_huy'    => HoaDon::where('trang_thai', HoaDon::TRANG_THAI_DA_HUY)->count(),
            'doanh_thu' => (float) HoaDon::where('trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)->sum('so_tien_da_thanh_toan'),
            'cong_no'   => (float) ($row?->cong_no ?? 0),
        ];
    }
}
