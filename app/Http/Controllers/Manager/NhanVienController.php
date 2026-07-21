<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreNhanVienRequest;
use App\Http\Requests\Manager\UpdateNhanVienRequest;
use App\Models\ChucVu;
use App\Models\HoaDon;
use App\Models\LichSuThanhToan;
use App\Models\NhanVien;
use App\Models\YeuCauCuDan;
use App\Services\AuditLogService;
use App\Services\NhanVienTrangThaiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class NhanVienController extends Controller
{
    private const SORTABLE = ['ho_ten', 'ma_nhan_vien', 'ngay_vao_lam', 'createdAt', 'trang_thai'];

    public function index(Request $request)
    {
        NhanVienTrangThaiService::syncExpired();

        $query = NhanVien::visibleToManager()
            ->with('chucVu')
            ->withCount(['hoaDon', 'yeuCauXuLy', 'thongBao', 'bangTin']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ho_ten',         'like', "%{$s}%")
                  ->orWhere('ma_nhan_vien', 'like', "%{$s}%")
                  ->orWhere('cccd',          'like', "%{$s}%")
                  ->orWhere('email',         'like', "%{$s}%")
                  ->orWhere('sdt',           'like', "%{$s}%");
            });
        }

        if ($request->filled('chuc_vu')) {
            $query->where('chuc_vu', $request->chuc_vu);
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $sort      = in_array($request->sort, self::SORTABLE) ? $request->sort : 'createdAt';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $direction);

        $nhanVienList = $query->paginate(15)->withQueryString();

        $stats = [
            'tong'          => NhanVien::visibleToManager()->count(),
            'dang_lam'      => NhanVien::visibleToManager()->where('trang_thai', 1)->count(),
            'da_nghi'       => NhanVien::visibleToManager()->where('trang_thai', 0)->count(),
            'tong_hoa_don'  => HoaDon::whereNotNull('nguoi_cap_nhat')->count(),
            'tong_thanh_toan' => LichSuThanhToan::count(),
            'tong_yeu_cau'  => YeuCauCuDan::whereNotNull('nhan_vien_xu_ly')->count(),
        ];

        $dsChucVu = ChucVu::selectableByManager()->orderBy('chuc_vu')->get();

        return view('manager.nhan-vien.index', compact('nhanVienList', 'stats', 'dsChucVu', 'sort', 'direction'));
    }

    public function create()
    {
        $dsChucVu = ChucVu::selectableByManager()->orderBy('chuc_vu')->get();
        return view('manager.nhan-vien.create', compact('dsChucVu'));
    }

    public function store(StoreNhanVienRequest $request)
    {
        $maxCode = NhanVien::selectRaw("
    MAX(CAST(SUBSTRING(ma_nhan_vien, 3) AS UNSIGNED)) as max_code
")->value('max_code');

$nextNumber = ($maxCode ?? 0) + 1;
        $data   = $request->validated();

        $data['mat_khau']       = Hash::make($data['mat_khau']);
        $data['ma_nhan_vien']   = $data['ma_nhan_vien'] ?: 'NV' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        $data['nguoi_cap_nhat'] = auth('nhanvien')->id();
        $data['trang_thai']     = $this->resolveTrangThai($data);

        unset($data['mat_khau_confirmation']);

        $nhanVien = NhanVien::create($data);

        AuditLogService::log('INSERT', 'nhan_vien', $nhanVien->id, null, $nhanVien->toArray());

        return redirect()->route('manager.nhan-vien.index')
            ->with('success', "Thêm nhân viên «{$nhanVien->ho_ten}» thành công.");
    }

    public function show(NhanVien $nhanVien)
    {
        $this->ensureManagerCanAccess($nhanVien);

        NhanVienTrangThaiService::syncOne($nhanVien);

        $nhanVien->load([
            'chucVu',
            'hoaDon'        => fn($q) => $q->with('canHo.toaNha')->latest('createdAt')->limit(50),
            'lichSuThanhToan' => fn($q) => $q->with(['hoaDon.canHo', 'nguoiThanhToan'])->latest('ngay_thanh_toan')->limit(50),
            'yeuCauXuLy'    => fn($q) => $q->with(['cuDan', 'loaiYeuCau'])->latest('createdAt')->limit(50),
            'thongBao'      => fn($q) => $q->withoutTrashed()->latest('createdAt')->limit(20),
            'bangTin'       => fn($q) => $q->withoutTrashed()->latest('createdAt')->limit(20),
        ])->loadCount([
            'hoaDon',
            'yeuCauXuLy',
            'thongBao',
            'bangTin',
        ]);

        return view('manager.nhan-vien.show', compact('nhanVien'));
    }

    public function edit(NhanVien $nhanVien)
    {
        $this->ensureManagerCanAccess($nhanVien);

        NhanVienTrangThaiService::syncOne($nhanVien);

        $nhanVien->load('chucVu');
        $dsChucVu = ChucVu::selectableByManager()->orderBy('chuc_vu')->get();
        return view('manager.nhan-vien.edit', compact('nhanVien', 'dsChucVu'));
    }

    public function update(UpdateNhanVienRequest $request, NhanVien $nhanVien)
    {
        $old  = $nhanVien->toArray();
        $data = $request->validated();

        if (!empty($data['mat_khau'])) {
            $data['mat_khau'] = Hash::make($data['mat_khau']);
        } else {
            unset($data['mat_khau']);
        }
        unset($data['mat_khau_confirmation']);

        $data['nguoi_cap_nhat'] = auth('nhanvien')->id();
        $data['trang_thai']     = $this->resolveTrangThai($data);

        $nhanVien->update($data);
        AuditLogService::log('UPDATE', 'nhan_vien', $nhanVien->id, $old, $nhanVien->fresh()->toArray());

        return redirect()->route('manager.nhan-vien.show', $nhanVien)
            ->with('success', 'Cập nhật nhân viên thành công.');
    }

    public function destroy(NhanVien $nhanVien)
    {
        $this->ensureManagerCanAccess($nhanVien);

        if ($nhanVien->id === auth('nhanvien')->id()) {
            return back()->with('error', 'Không thể xóa tài khoản của chính mình.');
        }

        $hasData = $nhanVien->hoaDon()->exists()
            || $nhanVien->yeuCauXuLy()->exists()
            || $nhanVien->thongBao()->exists()
            || $nhanVien->bangTin()->exists();

        if ($hasData) {
            return back()->with('error', 'Nhân viên đã phát sinh dữ liệu nên không thể xóa.');
        }

        $old = $nhanVien->toArray();
        $nhanVien->delete();
        AuditLogService::log('DELETE', 'nhan_vien', $nhanVien->id, $old, null);

        return redirect()->route('manager.nhan-vien.index')
            ->with('success', "Đã xóa nhân viên «{$nhanVien->ho_ten}».");
    }

    /**
     * Nếu ngay_nghi_lam được điền và nhỏ hơn hoặc bằng hôm nay → buộc trang_thai = 0.
     * Ngược lại dùng giá trị trang_thai từ form.
     */
    private function resolveTrangThai(array $data): int
    {
        if (!empty($data['ngay_nghi_lam'])) {
            $ngayNghi = \Carbon\Carbon::parse($data['ngay_nghi_lam'])->startOfDay();
            if ($ngayNghi->lte(now()->startOfDay())) {
                return 0;
            }
        }

        return (int) ($data['trang_thai'] ?? 1);
    }

    /**
     * Chặn Manager truy cập nhân viên có chức vụ Admin/Quản lý (xem/sửa/xóa),
     * kể cả khi gọi thẳng route bằng Postman/URL. Có ghi audit log lần cố truy cập.
     */
    private function ensureManagerCanAccess(NhanVien $nhanVien): void
    {
        if ($nhanVien->isRestrictedForManager()) {
            AuditLogService::log(
                'ACCESS_DENIED',
                'nhan_vien',
                $nhanVien->id,
                null,
                ['ly_do' => 'Manager cố truy cập nhân viên có chức vụ Admin/Quản lý']
            );

            abort(403);
        }
    }
}
