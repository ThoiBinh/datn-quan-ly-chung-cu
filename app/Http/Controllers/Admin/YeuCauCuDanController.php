<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoaiYeuCau;
use App\Models\NhanVien;
use App\Models\PhuongTien;
use App\Models\YeuCauCuDan;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class YeuCauCuDanController extends Controller
{
    private function dsTrangThai(): array
    {
        return collect([
            YeuCauCuDan::TRANG_THAI_MOI,
            YeuCauCuDan::TRANG_THAI_DANG_XU_LY,
            YeuCauCuDan::TRANG_THAI_HOAN_THANH,
            YeuCauCuDan::TRANG_THAI_TU_CHOI,
        ])->mapWithKeys(fn($v) => [$v => (new YeuCauCuDan(['trang_thai' => $v]))->trang_thai_label])->all();
    }

    private function dsMucDo(): array
    {
        return collect([
            YeuCauCuDan::MUC_DO_THAP,
            YeuCauCuDan::MUC_DO_TRUNG_BINH,
            YeuCauCuDan::MUC_DO_KHAN_CAP,
            4,
        ])->mapWithKeys(fn($v) => [$v => (new YeuCauCuDan(['muc_do_uu_tien' => $v]))->muc_do_label])->all();
    }

    private function findPhuongTienByYeuCau(YeuCauCuDan $yeuCau, int $loaiDangKyPTId): ?PhuongTien
    {
        if (!$yeuCau->loai_yeu_cau || (int)$yeuCau->loai_yeu_cau !== $loaiDangKyPTId) {
            return null;
        }
        preg_match('/Biển số:\s*([^\n]+)/ui', $yeuCau->noi_dung ?? '', $m);
        $bienSo = strtoupper(trim($m[1] ?? ''));
        return $bienSo ? PhuongTien::where('bien_so', $bienSo)->first() : null;
    }

    public function index(Request $request)
    {
        $query = YeuCauCuDan::with(['cuDan.canHoHienTai.canHo.toaNha', 'nhanVienXuLy', 'loaiYeuCau'])
            ->orderByDesc('createdAt');

        if ($request->filled('search')) {
            $query->where('tieu_de', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('muc_do')) {
            $query->where('muc_do_uu_tien', $request->muc_do);
        }
        if ($request->filled('loai_yeu_cau')) {
            $query->where('loai_yeu_cau', $request->loai_yeu_cau);
        }

        $dsYeuCau       = $query->paginate(15)->withQueryString();
        $dsTrangThai    = $this->dsTrangThai();
        $dsMucDo        = $this->dsMucDo();
        $dsLoaiYeuCau   = LoaiYeuCau::orderBy('name')->get();
        $loaiDangKyPTId = LoaiYeuCau::where('name', 'Đăng ký phương tiện')->value('id');

        return view('admin.yeu-cau.index', compact('dsYeuCau', 'dsTrangThai', 'dsMucDo', 'dsLoaiYeuCau', 'loaiDangKyPTId'));
    }

    public function show(YeuCauCuDan $yeuCau)
    {
        $yeuCau->load(['cuDan.canHoHienTai.canHo.toaNha', 'nhanVienXuLy.chucVu', 'loaiYeuCau']);
        $dsNhanVien     = NhanVien::where('trang_thai', 1)->orderBy('ho_ten')->get();
        $dsTrangThai    = $this->dsTrangThai();
        $loaiDangKyPTId = LoaiYeuCau::where('name', 'Đăng ký phương tiện')->value('id');
        $phuongTienLienQuan = $loaiDangKyPTId
            ? $this->findPhuongTienByYeuCau($yeuCau, (int)$loaiDangKyPTId)
            : null;
        if ($phuongTienLienQuan) {
            $phuongTienLienQuan->load('loaiPhuongTien');
        }
        return view('admin.yeu-cau.show', compact('yeuCau', 'dsNhanVien', 'dsTrangThai', 'phuongTienLienQuan', 'loaiDangKyPTId'));
    }

    public function update(Request $request, YeuCauCuDan $yeuCau)
    {
        $validTrangThai = implode(',', array_keys($this->dsTrangThai()));
        $request->validate([
            'trang_thai'      => 'required|integer|in:' . $validTrangThai,
            'nhan_vien_xu_ly' => 'nullable|integer|exists:nhan_vien,id',
        ]);

        $old  = $yeuCau->toArray();
        $data = [
            'trang_thai'     => $request->trang_thai,
            'nguoi_cap_nhat' => auth('nhanvien')->id(),
        ];

        if ($request->filled('nhan_vien_xu_ly')) {
            $data['nhan_vien_xu_ly'] = $request->nhan_vien_xu_ly;
        }
        if ((int)$request->trang_thai === YeuCauCuDan::TRANG_THAI_HOAN_THANH && !$yeuCau->ngay_hoan_thanh) {
            $data['ngay_hoan_thanh'] = now();
        }

        $yeuCau->update($data);

        $loaiDangKyPTId = LoaiYeuCau::where('name', 'Đăng ký phương tiện')->value('id');
        if ($loaiDangKyPTId) {
            $pt = $this->findPhuongTienByYeuCau($yeuCau, (int)$loaiDangKyPTId);
            if ($pt) {
                if ((int)$request->trang_thai === YeuCauCuDan::TRANG_THAI_HOAN_THANH) {
                    $pt->update(['trang_thai' => 1]);
                } elseif ((int)$request->trang_thai === YeuCauCuDan::TRANG_THAI_TU_CHOI) {
                    $pt->update(['trang_thai' => 0]);
                }
            }
        }

        AuditLogService::log('UPDATE', 'yeu_cau_cu_dan', $yeuCau->id, $old, $yeuCau->fresh()->toArray());

        return back()->with('success', 'Cập nhật yêu cầu thành công.');
    }
}
