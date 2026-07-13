<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNhanVienRequest;
use App\Http\Requests\Admin\UpdateNhanVienRequest;
use App\Models\ChucVu;
use App\Models\HoaDon;
use App\Models\LichSuThanhToan;
use App\Models\NhanVien;
use App\Models\YeuCauCuDan;
use App\Services\AuditLogService;
use App\Services\NhanVienTrangThaiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NhanVienController extends Controller
{
    private const SORTABLE = ['ho_ten', 'ma_nhan_vien', 'ngay_vao_lam', 'createdAt', 'trang_thai'];

    public function index(Request $request)
    {
        NhanVienTrangThaiService::syncExpired();

        $query = NhanVien::with('chucVu')
            ->withCount(['hoaDon', 'lichSuThanhToan', 'yeuCauXuLy', 'thongBao', 'bangTin']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ho_ten',        'like', "%{$s}%")
                  ->orWhere('ma_nhan_vien', 'like', "%{$s}%")
                  ->orWhere('cccd',         'like', "%{$s}%")
                  ->orWhere('email',        'like', "%{$s}%")
                  ->orWhere('sdt',          'like', "%{$s}%");
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

        $nhanVien = $query->paginate(15)->withQueryString();

        $stats = [
            'tong'            => NhanVien::count(),
            'dang_lam'        => NhanVien::where('trang_thai', 1)->count(),
            'da_nghi'         => NhanVien::where('trang_thai', 0)->count(),
            'tong_hoa_don'    => HoaDon::whereNotNull('nguoi_cap_nhat')->count(),
            'tong_thanh_toan' => LichSuThanhToan::count(),
            'tong_yeu_cau'    => YeuCauCuDan::whereNotNull('nhan_vien_xu_ly')->count(),
        ];

        $dsChucVu = ChucVu::orderBy('chuc_vu')->get();

        return view('admin.nhan-vien.index', compact('nhanVien', 'stats', 'dsChucVu', 'sort', 'direction'));
    }

    public function create()
    {
        $dsChucVu = ChucVu::orderBy('chuc_vu')->get();
        return view('admin.nhan-vien.create', compact('dsChucVu'));
    }

    public function store(StoreNhanVienRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, &$nhanVien) {
            $nextId = (NhanVien::max('id') ?? 0) + 1;
            $data['mat_khau']       = Hash::make($data['mat_khau']);
            $data['ma_nhan_vien']   = $data['ma_nhan_vien'] ?: 'NV' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $data['nguoi_cap_nhat'] = auth('nhanvien')->id();
            unset($data['mat_khau_confirmation']);
            $nhanVien = NhanVien::create($data);

            AuditLogService::log('INSERT', 'nhan_vien', $nhanVien->id, null, $nhanVien->toArray());
        });

        return redirect()->route('admin.nhan-vien.index')
            ->with('success', "Thêm nhân viên «{$nhanVien->ho_ten}» thành công.");
    }

    public function show(NhanVien $nhanVien)
    {
        NhanVienTrangThaiService::syncOne($nhanVien);

        $nhanVien->load([
            'chucVu',
            'hoaDon'          => fn ($q) => $q->with('canHo.toaNha', 'canHo.chuHo.cuDan')->latest('createdAt')->limit(50),
            'lichSuThanhToan' => fn ($q) => $q->with(['hoaDon.canHo', 'nguoiThanhToan'])->latest('ngay_thanh_toan')->limit(50),
            'yeuCauXuLy'      => fn ($q) => $q->with(['cuDan.canHoHienTai.canHo', 'loaiYeuCau'])->latest('createdAt')->limit(50),
            'thongBao'        => fn ($q) => $q->withoutTrashed()->latest('createdAt')->limit(20),
            'bangTin'         => fn ($q) => $q->withoutTrashed()->latest('createdAt')->limit(20),
        ])->loadCount(['hoaDon', 'lichSuThanhToan', 'yeuCauXuLy', 'thongBao', 'bangTin']);

        return view('admin.nhan-vien.show', compact('nhanVien'));
    }

    public function edit(NhanVien $nhanVien)
    {
        NhanVienTrangThaiService::syncOne($nhanVien);

        $nhanVien->load('chucVu');
        $dsChucVu = ChucVu::orderBy('chuc_vu')->get();
        return view('admin.nhan-vien.edit', compact('nhanVien', 'dsChucVu'));
    }

    public function update(UpdateNhanVienRequest $request, NhanVien $nhanVien)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $nhanVien) {
            $old = $nhanVien->toArray();

            if (!empty($data['mat_khau'])) {
                $data['mat_khau'] = Hash::make($data['mat_khau']);
            } else {
                unset($data['mat_khau']);
            }
            unset($data['mat_khau_confirmation']);

            $data['nguoi_cap_nhat'] = auth('nhanvien')->id();

            $nhanVien->update($data);
            AuditLogService::log('UPDATE', 'nhan_vien', $nhanVien->id, $old, $nhanVien->fresh()->toArray());
        });

        return redirect()->route('admin.nhan-vien.show', $nhanVien)
            ->with('success', 'Cập nhật nhân viên thành công.');
    }

    public function destroy(NhanVien $nhanVien)
    {
        if ($nhanVien->id === auth('nhanvien')->id()) {
            return back()->with('error', 'Không thể xóa tài khoản của chính mình.');
        }

        $hasData = $nhanVien->hoaDon()->exists()
            || $nhanVien->lichSuThanhToan()->exists()
            || $nhanVien->yeuCauXuLy()->exists()
            || $nhanVien->thongBao()->exists()
            || $nhanVien->bangTin()->exists();

        if ($hasData) {
            return back()->with('error', 'Nhân viên đã phát sinh dữ liệu nên không thể xóa.');
        }

        $old = $nhanVien->toArray();
        $nhanVien->delete();
        AuditLogService::log('DELETE', 'nhan_vien', $nhanVien->id, $old, null);

        return redirect()->route('admin.nhan-vien.index')
            ->with('success', "Đã xóa nhân viên «{$nhanVien->ho_ten}».");
    }

    public function toggleStatus(NhanVien $nhanVien)
    {
        if ($nhanVien->id === auth('nhanvien')->id()) {
            return back()->with('error', 'Không thể thay đổi trạng thái tài khoản của chính mình.');
        }

        $old       = ['trang_thai' => $nhanVien->trang_thai];
        $newStatus = $nhanVien->trang_thai == 1 ? 0 : 1;
        $nhanVien->update(['trang_thai' => $newStatus, 'nguoi_cap_nhat' => auth('nhanvien')->id()]);

        AuditLogService::log('UPDATE', 'nhan_vien', $nhanVien->id, $old, ['trang_thai' => $newStatus]);

        $msg = $newStatus === 1 ? "Đã mở khóa tài khoản «{$nhanVien->ho_ten}»." : "Đã khóa tài khoản «{$nhanVien->ho_ten}».";
        return back()->with('success', $msg);
    }
}
