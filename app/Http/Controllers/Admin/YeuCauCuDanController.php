<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoaiPhuongTien;
use App\Models\LoaiYeuCau;
use App\Models\NhanVien;
use App\Models\PhuongTien;
use App\Models\YeuCauCuDan;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    private function parseDuLieuPhuongTien(YeuCauCuDan $yeuCau): ?array
    {
        $data = json_decode($yeuCau->noi_dung ?? '', true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['bien_so'])) {
            return $data;
        }
        return null;
    }

    private function findPhuongTienByYeuCau(YeuCauCuDan $yeuCau, int $loaiDangKyPTId): ?PhuongTien
    {
        if (!$yeuCau->loai_yeu_cau || (int)$yeuCau->loai_yeu_cau !== $loaiDangKyPTId) {
            return null;
        }
        $data = json_decode($yeuCau->noi_dung ?? '', true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['bien_so'])) {
            $bienSo = strtoupper(trim($data['bien_so']));
        } else {
            preg_match('/Biển số:\s*([^\n]+)/ui', $yeuCau->noi_dung ?? '', $m);
            $bienSo = strtoupper(trim($m[1] ?? ''));
        }
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
        $isDangKyPT     = $loaiDangKyPTId && (int)$yeuCau->loai_yeu_cau === (int)$loaiDangKyPTId;

        $duLieuPhuongTien = null;
        if ($isDangKyPT) {
            $duLieuPhuongTien = $this->parseDuLieuPhuongTien($yeuCau);
            if ($duLieuPhuongTien && isset($duLieuPhuongTien['loai_phuong_tien'])) {
                $loaiPT = LoaiPhuongTien::find($duLieuPhuongTien['loai_phuong_tien']);
                $duLieuPhuongTien['ten_loai_phuong_tien'] = $loaiPT?->ten_loai_phuong_tien ?? '—';
            }
        }

        $phuongTienLienQuan = ($loaiDangKyPTId && $isDangKyPT)
            ? $this->findPhuongTienByYeuCau($yeuCau, (int)$loaiDangKyPTId)
            : null;
        if ($phuongTienLienQuan) {
            $phuongTienLienQuan->load('loaiPhuongTien');
        }

        return view('admin.yeu-cau.show', compact(
            'yeuCau', 'dsNhanVien', 'dsTrangThai', 'phuongTienLienQuan',
            'isDangKyPT', 'duLieuPhuongTien', 'loaiDangKyPTId'
        ));
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
        AuditLogService::log('UPDATE', 'yeu_cau_cu_dan', $yeuCau->id, $old, $yeuCau->fresh()->toArray());

        return back()->with('success', 'Cập nhật yêu cầu thành công.');
    }

    public function approve(Request $request, YeuCauCuDan $yeuCau)
    {
        $loaiDangKyPTId = LoaiYeuCau::where('name', 'Đăng ký phương tiện')->value('id');
        if (!$loaiDangKyPTId || (int)$yeuCau->loai_yeu_cau !== (int)$loaiDangKyPTId) {
            return back()->with('error', 'Yêu cầu này không phải đăng ký phương tiện.');
        }
        if ((int)$yeuCau->trang_thai !== YeuCauCuDan::TRANG_THAI_MOI) {
            return back()->with('error', 'Chỉ duyệt được yêu cầu ở trạng thái Mới.');
        }

        $data = $this->parseDuLieuPhuongTien($yeuCau);
        if (!$data) {
            return back()->with('error', 'Không đọc được thông tin phương tiện từ yêu cầu.');
        }

        $bienSo = strtoupper(trim($data['bien_so']));
        if (PhuongTien::where('bien_so', $bienSo)->where('trang_thai', 1)->exists()) {
            return back()->with('error', 'Biển số ' . $bienSo . ' đã được đăng ký và đang hoạt động.');
        }

        $yeuCau->load('cuDan.canHoHienTai');
        $canHoId = $yeuCau->cuDan?->canHoHienTai?->can_ho;
        if (!$canHoId) {
            return back()->with('error', 'Không xác định được căn hộ của cư dân này.');
        }

        $hangXe = trim($data['hang_xe'] ?? '');
        $mauXe  = trim($data['mau_xe'] ?? '');
        $tenPT  = trim($hangXe . ($mauXe ? ' - ' . $mauXe : ''), ' -') ?: $bienSo;

        DB::transaction(function () use ($yeuCau, $data, $bienSo, $canHoId, $tenPT) {
            $old = $yeuCau->toArray();
            PhuongTien::create([
                'ten_phuong_tien'  => $tenPT,
                'bien_so'          => $bienSo,
                'loai_phuong_tien' => (int)$data['loai_phuong_tien'],
                'can_ho'           => $canHoId,
                'ngay_dang_ky'     => now(),
                'trang_thai'       => 1,
                'nguoi_cap_nhat'   => auth('nhanvien')->id(),
            ]);
            $yeuCau->update([
                'trang_thai'      => YeuCauCuDan::TRANG_THAI_HOAN_THANH,
                'nhan_vien_xu_ly' => auth('nhanvien')->id(),
                'ngay_hoan_thanh' => now(),
                'nguoi_cap_nhat'  => auth('nhanvien')->id(),
            ]);
            AuditLogService::log('UPDATE', 'yeu_cau_cu_dan', $yeuCau->id, $old, $yeuCau->fresh()->toArray());
        });

        return back()->with('success', 'Đã duyệt và đăng ký phương tiện ' . $bienSo . ' thành công.');
    }

    public function reject(Request $request, YeuCauCuDan $yeuCau)
    {
        $loaiDangKyPTId = LoaiYeuCau::where('name', 'Đăng ký phương tiện')->value('id');
        if (!$loaiDangKyPTId || (int)$yeuCau->loai_yeu_cau !== (int)$loaiDangKyPTId) {
            return back()->with('error', 'Yêu cầu này không phải đăng ký phương tiện.');
        }
        if ((int)$yeuCau->trang_thai !== YeuCauCuDan::TRANG_THAI_MOI) {
            return back()->with('error', 'Chỉ từ chối được yêu cầu ở trạng thái Mới.');
        }

        $request->validate([
            'ly_do_tu_choi' => 'required|string|max:500',
        ], [
            'ly_do_tu_choi.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        $old     = $yeuCau->toArray();
        $noiDung = json_decode($yeuCau->noi_dung ?? '{}', true) ?: [];
        $noiDung['ly_do_tu_choi'] = $request->ly_do_tu_choi;

        $yeuCau->update([
            'trang_thai'      => YeuCauCuDan::TRANG_THAI_TU_CHOI,
            'noi_dung'        => json_encode($noiDung, JSON_UNESCAPED_UNICODE),
            'nhan_vien_xu_ly' => auth('nhanvien')->id(),
            'nguoi_cap_nhat'  => auth('nhanvien')->id(),
        ]);
        AuditLogService::log('UPDATE', 'yeu_cau_cu_dan', $yeuCau->id, $old, $yeuCau->fresh()->toArray());

        return back()->with('success', 'Đã từ chối yêu cầu đăng ký phương tiện.');
    }

    public function destroy(YeuCauCuDan $yeuCau)
    {
        AuditLogService::log('DELETE', 'yeu_cau_cu_dan', $yeuCau->id, $yeuCau->toArray(), null);
        $yeuCau->delete();
        return redirect()->route('admin.yeu-cau.index')->with('success', 'Xóa yêu cầu thành công.');
    }
}
