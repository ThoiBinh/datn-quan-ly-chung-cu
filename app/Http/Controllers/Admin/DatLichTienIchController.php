<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelDatLichTienIchRequest;
use App\Http\Requests\Admin\RejectDatLichTienIchRequest;
use App\Http\Requests\Admin\StoreDatLichTienIchRequest;
use App\Http\Requests\Admin\UpdateDatLichTienIchRequest;
use App\Models\CanHo;
use App\Models\CuDan;
use App\Models\DatLichTienIch;
use App\Models\NhanVien;
use App\Models\TienIch;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatLichTienIchController extends Controller
{
    private const SORTABLE = ['thoi_gian_bat_dau', 'createdAt', 'trang_thai', 'ho_ten'];

    public function __construct(private readonly BookingService $bookingService)
    {
    }

    private function danhSachLua(): array
    {
        return [
            'dsCuDan'     => CuDan::orderBy('ho_ten_dem')->orderBy('ten')->get(),
            'dsCanHo'     => CanHo::with('toaNha')->orderBy('so_can_ho')->get(),
            'dsTienIch'   => TienIch::with('loaiTienIch')->orderBy('ten_tien_ich')->get(),
            'dsNhanVien'  => NhanVien::where('trang_thai', 1)->orderBy('ho_ten')->get(),
            'dsTrangThai' => DatLichTienIch::dsTrangThai(),
        ];
    }

    public function index(Request $request)
    {
        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'thoi_gian_bat_dau';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = DatLichTienIch::with(['cuDan', 'canHo.toaNha', 'tienIch.loaiTienIch', 'nhanVienDuyet', 'nguoiCapNhat']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ma_dat_lich', 'like', "%{$s}%")
                    ->orWhereHas('cuDan', fn ($qc) => $qc->where('ho_ten_dem', 'like', "%{$s}%")
                        ->orWhere('ten', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%"))
                    ->orWhereHas('canHo', fn ($qc) => $qc->where('so_can_ho', 'like', "%{$s}%"))
                    ->orWhereHas('tienIch', fn ($qt) => $qt->where('ten_tien_ich', 'like', "%{$s}%"));

                foreach (DatLichTienIch::dsTrangThai() as $id => $label) {
                    if (mb_stripos($label, $s) !== false) {
                        $q->orWhere('trang_thai', $id);
                    }
                }
            });
        }
        if ($request->filled('tien_ich')) {
            $query->where('tien_ich', $request->tien_ich);
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('can_ho')) {
            $query->where('can_ho', $request->can_ho);
        }
        if ($request->filled('ngay_su_dung')) {
            $query->whereDate('thoi_gian_bat_dau', $request->ngay_su_dung);
        }
        if ($request->boolean('show_trashed')) {
            $query->onlyTrashed();
        }

        if ($sort === 'ho_ten') {
            $chuHoTen = CuDan::query()
                ->selectRaw("CONCAT(cu_dan.ho_ten_dem, ' ', cu_dan.ten)")
                ->whereColumn('cu_dan.id', 'dat_lich_tien_ich.cu_dan')
                ->limit(1);
            $query->orderBy($chuHoTen, $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $dsDatLich   = $query->paginate(15)->withQueryString();
        $dsTienIch   = TienIch::orderBy('ten_tien_ich')->get();
        $dsCanHo     = CanHo::with('toaNha')->orderBy('so_can_ho')->get();
        $dsTrangThai = DatLichTienIch::dsTrangThai();

        // 1 query duy nhất (conditional aggregation) thay vì 4 query COUNT/SUM riêng lẻ.
        $tk = DatLichTienIch::query()->selectRaw(
            'COUNT(*) as tong,
             SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as cho_duyet,
             SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as da_duyet,
             SUM(CASE WHEN trang_thai IN (?, ?) AND thoi_gian_bat_dau >= ? AND thoi_gian_bat_dau <= ? THEN phi_su_dung ELSE 0 END) as doanh_thu',
            [
                DatLichTienIch::TRANG_THAI_CHO_DUYET,
                DatLichTienIch::TRANG_THAI_DA_DUYET,
                DatLichTienIch::TRANG_THAI_DA_DUYET, DatLichTienIch::TRANG_THAI_HOAN_THANH,
                now()->startOfMonth(), now()->endOfMonth(),
            ]
        )->first();

        $thongKe = [
            'tong'      => (int) $tk->tong,
            'cho_duyet' => (int) $tk->cho_duyet,
            'da_duyet'  => (int) $tk->da_duyet,
            'doanh_thu' => (float) $tk->doanh_thu,
        ];

        return view('admin.dat-lich-tien-ich.index', compact(
            'dsDatLich', 'dsTienIch', 'dsCanHo', 'dsTrangThai', 'thongKe', 'sort', 'direction'
        ));
    }

    /**
     * Dashboard thống kê: 10 card + 6 biểu đồ, tổng cộng chỉ 5 câu query
     * (1 aggregate cho card + phân bố trạng thái, 1 group-by-tháng dùng chung
     * cho 2 biểu đồ, 3 top-N join+group trực tiếp ở DB — không N+1, không
     * load toàn bộ bản ghi về PHP để đếm/cộng tay).
     */
    public function dashboard()
    {
        $now          = now();
        $today        = $now->copy()->startOfDay();
        $tomorrow     = $today->copy()->addDay();
        $startOfWeek  = $now->copy()->startOfWeek();
        $endOfWeek    = $now->copy()->endOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();

        $CHO_DUYET  = DatLichTienIch::TRANG_THAI_CHO_DUYET;
        $DA_DUYET   = DatLichTienIch::TRANG_THAI_DA_DUYET;
        $TU_CHOI    = DatLichTienIch::TRANG_THAI_TU_CHOI;
        $DA_HUY     = DatLichTienIch::TRANG_THAI_DA_HUY;
        $HOAN_THANH = DatLichTienIch::TRANG_THAI_HOAN_THANH;

        // 1) Toàn bộ 10 card + phân bố trạng thái trong đúng 1 query (conditional aggregation)
        $tk = DatLichTienIch::query()->selectRaw(
            'COUNT(*) as tong,
             SUM(CASE WHEN thoi_gian_bat_dau >= ? AND thoi_gian_bat_dau < ? THEN 1 ELSE 0 END) as hom_nay,
             SUM(CASE WHEN thoi_gian_bat_dau >= ? AND thoi_gian_bat_dau <= ? THEN 1 ELSE 0 END) as tuan,
             SUM(CASE WHEN thoi_gian_bat_dau >= ? AND thoi_gian_bat_dau <= ? THEN 1 ELSE 0 END) as thang,
             SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as cho_duyet,
             SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as da_duyet,
             SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as tu_choi,
             SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as da_huy,
             SUM(CASE WHEN trang_thai = ? THEN 1 ELSE 0 END) as hoan_thanh,
             SUM(CASE WHEN trang_thai IN (?, ?) THEN phi_su_dung ELSE 0 END) as doanh_thu,
             SUM(CASE WHEN trang_thai IN (?, ?) AND thoi_gian_bat_dau >= ? AND thoi_gian_bat_dau <= ? THEN phi_su_dung ELSE 0 END) as doanh_thu_thang',
            [
                $today, $tomorrow,
                $startOfWeek, $endOfWeek,
                $startOfMonth, $endOfMonth,
                $CHO_DUYET, $DA_DUYET, $TU_CHOI, $DA_HUY, $HOAN_THANH,
                $DA_DUYET, $HOAN_THANH,
                $DA_DUYET, $HOAN_THANH, $startOfMonth, $endOfMonth,
            ]
        )->first();

        $thongKe = [
            'tong'            => (int) $tk->tong,
            'hom_nay'         => (int) $tk->hom_nay,
            'tuan'            => (int) $tk->tuan,
            'thang'           => (int) $tk->thang,
            'cho_duyet'       => (int) $tk->cho_duyet,
            'da_duyet'        => (int) $tk->da_duyet,
            'tu_choi'         => (int) $tk->tu_choi,
            'da_huy'          => (int) $tk->da_huy,
            'hoan_thanh'      => (int) $tk->hoan_thanh,
            'doanh_thu'       => (float) $tk->doanh_thu,
            'doanh_thu_thang' => (float) $tk->doanh_thu_thang,
        ];

        // 2) Booking + doanh thu theo tháng (12 tháng gần nhất) — 1 query group-by dùng chung cho 2 biểu đồ
        $tuNgay     = $now->copy()->subMonths(11)->startOfMonth();
        $driver     = DB::connection()->getDriverName();
        $thangExpr  = $driver === 'sqlite'
            ? "strftime('%Y-%m', thoi_gian_bat_dau)"
            : "DATE_FORMAT(thoi_gian_bat_dau, '%Y-%m')";

        $theoThang = DatLichTienIch::query()
            ->selectRaw(
                "{$thangExpr} as thang, COUNT(*) as so_booking,
                 SUM(CASE WHEN trang_thai IN (?, ?) THEN phi_su_dung ELSE 0 END) as doanh_thu",
                [$DA_DUYET, $HOAN_THANH]
            )
            ->where('thoi_gian_bat_dau', '>=', $tuNgay)
            ->groupBy('thang')
            ->orderBy('thang')
            ->get()
            ->keyBy('thang');

        $nhanThang = $bookingTheoThang = $doanhThuTheoThang = [];
        for ($i = 11; $i >= 0; $i--) {
            $moc  = $now->copy()->subMonths($i);
            $key  = $moc->format('Y-m');
            $hang = $theoThang->get($key);

            $nhanThang[]         = $moc->format('m/Y');
            $bookingTheoThang[]  = (int) ($hang->so_booking ?? 0);
            $doanhThuTheoThang[] = (float) ($hang->doanh_thu ?? 0);
        }

        // 3) Top 5 tiện ích / loại tiện ích / tòa nhà — join + group trực tiếp ở DB (không N+1)
        $topTienIch = DatLichTienIch::query()
            ->join('tien_ich', 'tien_ich.id', '=', 'dat_lich_tien_ich.tien_ich')
            ->selectRaw('tien_ich.ten_tien_ich as ten, COUNT(*) as so_luong')
            ->groupBy('tien_ich.id', 'tien_ich.ten_tien_ich')
            ->orderByDesc('so_luong')
            ->limit(5)
            ->get();

        $topLoaiTienIch = DatLichTienIch::query()
            ->join('tien_ich', 'tien_ich.id', '=', 'dat_lich_tien_ich.tien_ich')
            ->join('loai_tien_ich', 'loai_tien_ich.id', '=', 'tien_ich.loai_tien_ich')
            ->selectRaw('loai_tien_ich.ten_loai_tien_ich as ten, COUNT(*) as so_luong')
            ->groupBy('loai_tien_ich.id', 'loai_tien_ich.ten_loai_tien_ich')
            ->orderByDesc('so_luong')
            ->limit(5)
            ->get();

        $topToaNha = DatLichTienIch::query()
            ->join('can_ho', 'can_ho.id', '=', 'dat_lich_tien_ich.can_ho')
            ->join('toa_nha', 'toa_nha.id', '=', 'can_ho.toa_nha')
            ->selectRaw('toa_nha.ten_toa_nha as ten, COUNT(*) as so_luong')
            ->groupBy('toa_nha.id', 'toa_nha.ten_toa_nha')
            ->orderByDesc('so_luong')
            ->limit(5)
            ->get();

        return view('admin.dat-lich-tien-ich.dashboard', compact(
            'thongKe', 'nhanThang', 'bookingTheoThang', 'doanhThuTheoThang',
            'topTienIch', 'topLoaiTienIch', 'topToaNha'
        ));
    }

    public function create()
    {
        return view('admin.dat-lich-tien-ich.create', $this->danhSachLua());
    }

    public function store(StoreDatLichTienIchRequest $request)
    {
        $datLich = $this->bookingService->taoDatLich($request->validated());

        return redirect()->route('admin.dat-lich-tien-ich.show', $datLich)
            ->with('success', "Thêm lịch đặt tiện ích «{$datLich->ma_dat_lich}» thành công.");
    }

    public function show(DatLichTienIch $datLichTienIch)
    {
        $datLichTienIch->load([
            'cuDan', 'canHo.toaNha', 'canHo.loaiCanHo', 'tienIch.loaiTienIch',
            'nhanVienDuyet', 'nguoiCapNhat',
        ]);

        return view('admin.dat-lich-tien-ich.show', compact('datLichTienIch'));
    }

    public function edit(DatLichTienIch $datLichTienIch)
    {
        return view('admin.dat-lich-tien-ich.edit', array_merge(
            ['datLichTienIch' => $datLichTienIch],
            $this->danhSachLua()
        ));
    }

    public function update(UpdateDatLichTienIchRequest $request, DatLichTienIch $datLichTienIch)
    {
        $datLich = $this->bookingService->capNhatDatLich($datLichTienIch, $request->validated());

        return redirect()->route('admin.dat-lich-tien-ich.show', $datLich)
            ->with('success', "Cập nhật lịch đặt tiện ích «{$datLich->ma_dat_lich}» thành công.");
    }

    public function destroy(DatLichTienIch $datLichTienIch)
    {
        $this->bookingService->xoa($datLichTienIch);

        return redirect()->route('admin.dat-lich-tien-ich.index')
            ->with('success', "Đã xóa lịch đặt tiện ích «{$datLichTienIch->ma_dat_lich}».");
    }

    public function restore(int $id)
    {
        $datLich = $this->bookingService->khoiPhuc($id);

        return back()->with('success', "Khôi phục lịch đặt tiện ích «{$datLich->ma_dat_lich}» thành công.");
    }

    public function approve(DatLichTienIch $datLichTienIch)
    {
        $datLich = $this->bookingService->duyet($datLichTienIch);

        return back()->with('success', "Đã duyệt lịch đặt tiện ích «{$datLich->ma_dat_lich}».");
    }

    public function reject(RejectDatLichTienIchRequest $request, DatLichTienIch $datLichTienIch)
    {
        $datLich = $this->bookingService->tuChoi($datLichTienIch, $request->validated('ly_do'));

        return back()->with('success', "Đã từ chối lịch đặt tiện ích «{$datLich->ma_dat_lich}».");
    }

    public function cancel(CancelDatLichTienIchRequest $request, DatLichTienIch $datLichTienIch)
    {
        $datLich = $this->bookingService->huy($datLichTienIch, $request->validated('ly_do'));

        return back()->with('success', "Đã hủy lịch đặt tiện ích «{$datLich->ma_dat_lich}».");
    }
}
