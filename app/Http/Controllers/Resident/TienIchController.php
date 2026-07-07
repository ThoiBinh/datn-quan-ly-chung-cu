<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\LoaiTienIch;
use App\Models\TienIch;
use App\Models\ToaNha;
use Illuminate\Http\Request;

class TienIchController extends Controller
{
    public function index(Request $request)
    {
        $query = TienIch::active()->with(['loaiTienIch', 'toaNha']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ten_tien_ich', 'like', "%{$s}%")
                    ->orWhere('mo_ta', 'like', "%{$s}%");
            });
        }
        if ($request->filled('loai_tien_ich')) {
            $query->where('loai_tien_ich', $request->loai_tien_ich);
        }
        if ($request->filled('toa_nha')) {
            $query->where('toa_nha', $request->toa_nha);
        }

        $dsTienIch = $query->orderBy('ten_tien_ich')->paginate(9)->withQueryString();
        $dsLoaiTienIch = LoaiTienIch::orderBy('ten_loai_tien_ich')->get();
        $dsToaNha = ToaNha::orderBy('ten_toa_nha')->get();

        return view('resident.tien-ich.index', compact('dsTienIch', 'dsLoaiTienIch', 'dsToaNha'));
    }

    public function show(TienIch $tienIch)
    {
        if ((int) $tienIch->trang_thai !== TienIch::TRANG_THAI_HOAT_DONG) {
            abort(404);
        }

        $tienIch->load(['loaiTienIch', 'toaNha']);

        return view('resident.tien-ich.show', compact('tienIch'));
    }
}
