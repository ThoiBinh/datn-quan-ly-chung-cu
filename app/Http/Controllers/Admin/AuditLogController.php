<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhatKyHeThong;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = NhatKyHeThong::with('nguoiThucHien');

        if ($request->filled('hanh_dong')) {
            $query->where('hanh_dong', $request->hanh_dong);
        }

        if ($request->filled('bang_tac_dong')) {
            $query->where('bang_tac_dong', $request->bang_tac_dong);
        }

        if ($request->filled('tu_ngay')) {
            $query->whereDate('thoi_gian', '>=', $request->tu_ngay);
        }

        if ($request->filled('den_ngay')) {
            $query->whereDate('thoi_gian', '<=', $request->den_ngay);
        }

        $logs = $query->orderByDesc('createdAt')->paginate(20)->withQueryString();

        $bangList = NhatKyHeThong::distinct()->pluck('bang_tac_dong')->filter()->values();

        return view('admin.audit-logs.index', compact('logs', 'bangList'));
    }

    public function show(NhatKyHeThong $nhatKy)
    {
        return view('admin.audit-logs.show', compact('nhatKy'));
    }
}
