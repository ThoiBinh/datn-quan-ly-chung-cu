<?php

namespace App\Http\Controllers;

use App\Models\BangTin;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicBangTinController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $query = BangTin::with('nguoiTao');

        if ($search !== '') {
            $query->where(fn ($q) => $q
                ->where('tieu_de', 'like', "%{$search}%")
                ->orWhere('noi_dung', 'like', "%{$search}%")
            );
        }

        $dsBangTin = $query->orderByDesc('createdAt')->paginate(9)->withQueryString();

        return view('public.bang-tin.index', compact('dsBangTin', 'search'));
    }

    public function show(BangTin $bangTin): View
    {
        $bangTin->load('nguoiTao');

        $baiLienQuan = BangTin::where('id', '!=', $bangTin->id)
            ->orderByDesc('createdAt')
            ->take(6)
            ->get();

        return view('public.bang-tin.show', compact('bangTin', 'baiLienQuan'));
    }
}
