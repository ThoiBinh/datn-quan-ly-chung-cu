<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\YeuCauCuDan;
use Illuminate\Http\Request;

class YeuCauController extends Controller
{
    public function index()
    {
        $cuDan = auth()->user()->cuDan;
        if (!$cuDan) {
            return view('resident.yeu-cau.index', ['yeuCau' => collect()]);
        }

        $yeuCau = YeuCauCuDan::where('cu_dan', $cuDan->id)
            ->orderByDesc('created_at')->paginate(10);

        return view('resident.yeu-cau.index', compact('yeuCau'));
    }

    public function create()
    {
        return view('resident.yeu-cau.create');
    }

    public function store(Request $request)
    {
        $cuDan = auth()->user()->cuDan;
        if (!$cuDan) {
            return back()->with('error', 'Không tìm thấy thông tin cư dân.');
        }

        $request->validate([
            'tieu_de'    => 'required|string|max:255',
            'noi_dung'   => 'required|string',
            'loai_yeu_cau' => 'required|string',
            'muc_do'     => 'required|integer|in:1,2,3,4',
        ], [
            'tieu_de.required'    => 'Vui lòng nhập tiêu đề.',
            'noi_dung.required'   => 'Vui lòng nhập nội dung.',
            'loai_yeu_cau.required' => 'Vui lòng chọn loại yêu cầu.',
        ]);

        YeuCauCuDan::create([
            'cu_dan'       => $cuDan->id,
            'tieu_de'      => $request->tieu_de,
            'noi_dung'     => $request->noi_dung,
            'loai_yeu_cau' => $request->loai_yeu_cau,
            'ngay_gui'     => now(),
            'muc_do'       => $request->muc_do,
            'muc_do_uu_tien' => $request->muc_do,
            'trang_thai'   => YeuCauCuDan::TRANG_THAI_MOI,
        ]);

        return redirect()->route('resident.yeu-cau.index')->with('success', 'Gửi phản ánh thành công.');
    }

    public function show(YeuCauCuDan $yeuCau)
    {
        $cuDan = auth()->user()->cuDan;
        if (!$cuDan || $yeuCau->cu_dan != $cuDan->id) {
            abort(403);
        }
        return view('resident.yeu-cau.show', compact('yeuCau'));
    }
}
