<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreHoaDonRequest;
use App\Http\Requests\Manager\UpdateHoaDonRequest;
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

        $hoaDon = $this->hoaDonService->taoHoaDon(
            $request->can_ho,
            $request->thang,
            $request->nam,
            $request->input('chi_so', [])
        );

        return redirect()->route('manager.hoa-don.show', $hoaDon)->with('success', 'Tạo hóa đơn thành công.');
    }

    public function show(HoaDon $hoaDon)
    {
        $hoaDon->load([
            'canHo.toaNha', 'canHo.chuHo.cuDan',
            'chiTiet', 'nguoiCapNhat.chucVu',
            'lichSuThanhToan.nguoiThanhToan', 'lichSuThanhToan.nguonTao',
        ]);
        return view('manager.hoa-don.show', compact('hoaDon'));
    }

    public function edit(HoaDon $hoaDon)
    {
        $hoaDon->load('chiTiet', 'canHo.toaNha');
        return view('manager.hoa-don.edit', compact('hoaDon'));
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
                $request->only('han_thanh_toan', 'trang_thai'),
                ['nguoi_cap_nhat' => auth('nhanvien')->id()]
            ));

            if ($isCurrentlyChuaTT && $request->has('chi_tiet')) {
                $this->hoaDonService->capNhatChiSo($hoaDon, $request->chi_tiet);
            }
        });

        AuditLogService::log('UPDATE', 'hoa_don', $hoaDon->id, $old, $hoaDon->fresh()->toArray());

        return redirect()->route('manager.hoa-don.show', $hoaDon)->with('success', 'Cập nhật hóa đơn thành công.');
    }

    public function destroy(HoaDon $hoaDon)
    {
        if ($hoaDon->trang_thai !== HoaDon::TRANG_THAI_CHUA_THANH_TOAN) {
            return back()->with('error', 'Chỉ có thể xóa hóa đơn chưa thanh toán.');
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

    public function ghiNhanThanhToan(Request $request, HoaDon $hoaDon)
    {
        $request->validate([
            'so_tien'                => 'required|numeric|min:1000',
            'phuong_thuc_thanh_toan' => 'required|string',
            'ma_giao_dich'           => 'nullable|string',
        ]);

        $this->hoaDonService->ghiNhanThanhToan(
            $hoaDon,
            $request->so_tien,
            $request->phuong_thuc_thanh_toan,
            $request->ma_giao_dich
        );

        return back()->with('success', 'Ghi nhận thanh toán thành công.');
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
