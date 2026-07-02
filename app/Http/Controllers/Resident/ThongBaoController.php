<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\ThongBaoDaDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongBaoController extends Controller
{
    public function index(Request $request)
    {
        $cuDanId = auth('cudan')->id();

        $query = ThongBao::whereNull('deletedAt')
            ->with([
                'nguoiTao',
                'daDoc' => fn($q) => $q->where('cu_dan_id', $cuDanId),
            ])
            ->orderByDesc('createdAt');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('tieu_de', 'like', "%$s%")
                ->orWhere('noi_dung', 'like', "%$s%")
            );
        }

        if ($request->filled('trang_thai')) {
            if ($request->trang_thai === 'da_doc') {
                $query->whereHas('daDoc', fn($q) => $q->where('cu_dan_id', $cuDanId));
            } elseif ($request->trang_thai === 'chua_doc') {
                $query->whereDoesntHave('daDoc', fn($q) => $q->where('cu_dan_id', $cuDanId));
            }
        }

        $dsThongBao  = $query->paginate(15)->withQueryString();
        $tongChuaDoc = ThongBao::whereNull('deletedAt')
            ->whereDoesntHave('daDoc', fn($q) => $q->where('cu_dan_id', $cuDanId))
            ->count();

        return view('resident.thong-bao.index', compact('dsThongBao', 'tongChuaDoc'));
    }

    public function show(Request $request, ThongBao $thongBao)
    {
        abort_if($thongBao->trashed(), 404);

        $cuDanId = auth('cudan')->id();

        // Ghi nhận đọc — chỉ INSERT lần đầu tiên, các lần mở lại sau đó
        // không được UPDATE / touch bản ghi đã tồn tại (giữ nguyên read_at).
        DB::transaction(function () use ($thongBao, $cuDanId) {
            $daDoc = ThongBaoDaDoc::where('thong_bao_id', $thongBao->id)
                ->where('cu_dan_id', $cuDanId)
                ->lockForUpdate()
                ->first();

            if (!$daDoc) {
                ThongBaoDaDoc::create([
                    'thong_bao_id' => $thongBao->id,
                    'cu_dan_id'    => $cuDanId,
                    'read_at'      => now(),
                ]);
            }
        });

        $thongBao->load('nguoiTao');

        $baiDoc = ThongBaoDaDoc::where('thong_bao_id', $thongBao->id)
            ->where('cu_dan_id', $cuDanId)
            ->first();

        return view('resident.thong-bao.show', compact('thongBao', 'baiDoc'));
    }
}
