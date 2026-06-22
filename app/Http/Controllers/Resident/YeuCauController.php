<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\LoaiYeuCau;
use App\Models\YeuCauCuDan;
use Illuminate\Http\Request;

class YeuCauController extends Controller
{
    public function index()
    {
        $cuDan = auth('cudan')->user();
        if (!$cuDan) {
            return view('resident.yeu-cau.index', ['yeuCau' => collect()]);
        }

        $yeuCau = YeuCauCuDan::where('cu_dan', $cuDan->id)
            ->orderByDesc('createdAt')->paginate(10);

        return view('resident.yeu-cau.index', compact('yeuCau'));
    }

    public function create()
    {
        $loaiYeuCau = LoaiYeuCau::orderBy('id')->get();
        return view('resident.yeu-cau.create', compact('loaiYeuCau'));
    }

    public function store(Request $request)
    {
        $cuDan = auth('cudan')->user();
        if (!$cuDan) {
            return back()->with('error', 'Không tìm thấy thông tin cư dân.');
        }

        $request->validate([
            'tieu_de'      => 'required|string|max:255',
            'noi_dung'     => 'required|string',
            'muc_do'       => 'required|integer|in:1,2,3,4',
            'loai_yeu_cau' => 'nullable|integer|exists:loai_yeu_cau,id',
        ], [
            'tieu_de.required'  => 'Vui lòng nhập tiêu đề.',
            'noi_dung.required' => 'Vui lòng nhập nội dung.',
            'muc_do.required'   => 'Vui lòng chọn mức độ ưu tiên.',
        ]);

        YeuCauCuDan::create([
            'cu_dan'         => $cuDan->id,
            'loai_yeu_cau'   => $request->loai_yeu_cau ?: null,
            'tieu_de'        => $request->tieu_de,
            'noi_dung'       => $request->noi_dung,
            'ngay_gui'       => now(),
            'muc_do_uu_tien' => $request->muc_do,
            'trang_thai'     => YeuCauCuDan::TRANG_THAI_MOI,
        ]);

        return redirect()->route('resident.yeu-cau.index')->with('success', 'Gửi phản ánh thành công.');
    }

    public function show(YeuCauCuDan $yeuCau)
    {
        $cuDan = auth('cudan')->user();
        if (!$cuDan || $yeuCau->cu_dan != $cuDan->id) {
            abort(403);
        }
        return view('resident.yeu-cau.show', compact('yeuCau'));
    }
}
