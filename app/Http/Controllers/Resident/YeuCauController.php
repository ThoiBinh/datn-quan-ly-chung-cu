<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\LoaiPhuongTien;
use App\Models\LoaiYeuCau;
use App\Models\PhuongTien;
use App\Models\YeuCauCuDan;
use Illuminate\Http\Request;

class YeuCauController extends Controller
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

    public function index(Request $request)
    {
        $cuDan = auth('cudan')->user();
        if (!$cuDan) {
            return view('resident.yeu-cau.index', ['yeuCau' => collect(), 'dsTrangThai' => $this->dsTrangThai()]);
        }

        $query = YeuCauCuDan::where('cu_dan', $cuDan->id)
            ->with(['loaiYeuCau', 'nhanVienXuLy'])
            ->orderByDesc('createdAt');

        if ($request->filled('search')) {
            $query->where('tieu_de', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $yeuCau      = $query->paginate(10)->withQueryString();
        $dsTrangThai = $this->dsTrangThai();

        return view('resident.yeu-cau.index', compact('yeuCau', 'dsTrangThai'));
    }

    public function create()
    {
        $loaiYeuCau       = LoaiYeuCau::orderBy('name')->get();
        $dsMucDo          = $this->dsMucDo();
        $dsLoaiPhuongTien = LoaiPhuongTien::orderBy('ten_loai_phuong_tien')->get();
        $loaiDangKyPTId   = LoaiYeuCau::where('name', 'Đăng ký phương tiện')->value('id');
        return view('resident.yeu-cau.create', compact('loaiYeuCau', 'dsMucDo', 'dsLoaiPhuongTien', 'loaiDangKyPTId'));
    }

    public function store(Request $request)
    {
        $cuDan = auth('cudan')->user();
        if (!$cuDan) {
            return back()->with('error', 'Không tìm thấy thông tin cư dân.');
        }

        $validMucDo     = implode(',', array_keys($this->dsMucDo()));
        $loaiDangKyPTId = LoaiYeuCau::where('name', 'Đăng ký phương tiện')->value('id');
        $isDangKyPT     = $loaiDangKyPTId && (int)$request->loai_yeu_cau === (int)$loaiDangKyPTId;

        $rules = [
            'tieu_de'      => 'required|string|max:255',
            'noi_dung'     => $isDangKyPT ? 'nullable|string' : 'required|string',
            'muc_do'       => 'nullable|integer|in:' . $validMucDo,
            'loai_yeu_cau' => 'nullable|integer|exists:loai_yeu_cau,id',
        ];
        if ($isDangKyPT) {
            $rules['bien_so']          = 'required|string|max:50|unique:phuong_tien,bien_so';
            $rules['loai_phuong_tien'] = 'required|integer|exists:loai_phuong_tien,id';
            $rules['ten_phuong_tien']  = 'nullable|string|max:255';
        }

        $request->validate($rules, [
            'tieu_de.required'          => 'Vui lòng nhập tiêu đề.',
            'noi_dung.required'         => 'Vui lòng nhập nội dung.',
            'bien_so.required'          => 'Vui lòng nhập biển số xe.',
            'bien_so.unique'            => 'Biển số xe này đã được đăng ký trong hệ thống.',
            'loai_phuong_tien.required' => 'Vui lòng chọn loại phương tiện.',
        ]);

        if ($isDangKyPT) {
            $loaiPT  = LoaiPhuongTien::find($request->loai_phuong_tien);
            $bienSo  = strtoupper(trim($request->bien_so));
            $noiDung = "Biển số: {$bienSo}\n"
                . "Loại phương tiện: " . ($loaiPT?->ten_loai_phuong_tien ?? '') . "\n"
                . "Tên phương tiện: " . ($request->ten_phuong_tien ?: '—');
            if ($request->filled('noi_dung')) {
                $noiDung .= "\nGhi chú: " . $request->noi_dung;
            }
        } else {
            $noiDung = $request->noi_dung ?? '';
        }

        YeuCauCuDan::create([
            'cu_dan'         => $cuDan->id,
            'loai_yeu_cau'   => $request->loai_yeu_cau ?: null,
            'tieu_de'        => $request->tieu_de,
            'noi_dung'       => $noiDung,
            'ngay_gui'       => now(),
            'muc_do_uu_tien' => $request->muc_do ? (int)$request->muc_do : YeuCauCuDan::MUC_DO_THAP,
            'trang_thai'     => YeuCauCuDan::TRANG_THAI_MOI,
        ]);

        if ($isDangKyPT) {
            $canHoId = $cuDan->canHoHienTai?->can_ho;
            if ($canHoId) {
                PhuongTien::create([
                    'ten_phuong_tien'  => $request->ten_phuong_tien,
                    'bien_so'          => strtoupper(trim($request->bien_so)),
                    'loai_phuong_tien' => $request->loai_phuong_tien,
                    'can_ho'           => $canHoId,
                    'ngay_dang_ky'     => now()->toDateString(),
                    'trang_thai'       => 1,
                ]);
            }
        }

        return redirect()->route('resident.yeu-cau.index')->with('success', 'Gửi yêu cầu thành công.');
    }

    public function show(YeuCauCuDan $yeuCau)
    {
        $cuDan = auth('cudan')->user();
        if (!$cuDan || $yeuCau->cu_dan != $cuDan->id) {
            abort(403);
        }
        $yeuCau->load(['loaiYeuCau', 'nhanVienXuLy.chucVu', 'cuDan.canHoHienTai.canHo.toaNha']);
        return view('resident.yeu-cau.show', compact('yeuCau'));
    }

    public function edit(YeuCauCuDan $yeuCau)
    {
        $cuDan = auth('cudan')->user();
        if (!$cuDan || $yeuCau->cu_dan != $cuDan->id) {
            abort(403);
        }
        if ($yeuCau->trang_thai != YeuCauCuDan::TRANG_THAI_MOI) {
            return redirect()->route('resident.yeu-cau.show', $yeuCau)
                ->with('error', 'Chỉ được chỉnh sửa yêu cầu ở trạng thái Mới.');
        }
        $loaiYeuCau = LoaiYeuCau::orderBy('name')->get();
        $dsMucDo    = $this->dsMucDo();
        return view('resident.yeu-cau.edit', compact('yeuCau', 'loaiYeuCau', 'dsMucDo'));
    }

    public function update(Request $request, YeuCauCuDan $yeuCau)
    {
        $cuDan = auth('cudan')->user();
        if (!$cuDan || $yeuCau->cu_dan != $cuDan->id) {
            abort(403);
        }
        if ($yeuCau->trang_thai != YeuCauCuDan::TRANG_THAI_MOI) {
            return redirect()->route('resident.yeu-cau.show', $yeuCau)
                ->with('error', 'Chỉ được chỉnh sửa yêu cầu ở trạng thái Mới.');
        }

        $validMucDo = implode(',', array_keys($this->dsMucDo()));
        $request->validate([
            'tieu_de'      => 'required|string|max:255',
            'noi_dung'     => 'required|string',
            'muc_do'       => 'required|integer|in:' . $validMucDo,
            'loai_yeu_cau' => 'nullable|integer|exists:loai_yeu_cau,id',
        ]);

        $yeuCau->update([
            'tieu_de'        => $request->tieu_de,
            'noi_dung'       => $request->noi_dung,
            'loai_yeu_cau'   => $request->loai_yeu_cau ?: null,
            'muc_do_uu_tien' => $request->muc_do,
        ]);

        return redirect()->route('resident.yeu-cau.show', $yeuCau)
            ->with('success', 'Cập nhật yêu cầu thành công.');
    }

    public function cancel(Request $request, YeuCauCuDan $yeuCau)
    {
        $cuDan = auth('cudan')->user();
        if (!$cuDan || $yeuCau->cu_dan != $cuDan->id) {
            abort(403);
        }
        if ($yeuCau->trang_thai != YeuCauCuDan::TRANG_THAI_MOI) {
            return back()->with('error', 'Chỉ có thể hủy yêu cầu ở trạng thái Mới.');
        }

        $yeuCau->update(['trang_thai' => YeuCauCuDan::TRANG_THAI_TU_CHOI]);

        return redirect()->route('resident.yeu-cau.index')
            ->with('success', 'Đã hủy yêu cầu thành công.');
    }
}
