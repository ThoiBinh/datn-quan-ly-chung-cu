<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\CanHo;
use App\Models\LoaiPhuongTien;
use App\Models\PhuongTien;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class PhuongTienController extends Controller
{
    private function getCanHoIds(): Collection
    {
        return auth('cudan')->user()?->canHoIdsHienTai() ?? collect();
    }

    public function index()
    {
        $canHoIds = $this->getCanHoIds();
        if ($canHoIds->isEmpty()) {
            return view('resident.phuong-tien.index', ['phuongTien' => collect()]);
        }

        $phuongTien = PhuongTien::with(['loaiPhuongTien', 'canHo.toaNha'])
            ->whereIn('can_ho', $canHoIds)
            ->get();

        return view('resident.phuong-tien.index', compact('phuongTien'));
    }

    public function create()
    {
        $canHoIds = $this->getCanHoIds();
        if ($canHoIds->isEmpty()) {
            return redirect()->route('resident.dashboard')->with('error', 'Bạn chưa được phân căn hộ.');
        }

        $dsCanHo = CanHo::with('toaNha')->whereIn('id', $canHoIds)->orderBy('so_can_ho')->get();
        $loaiPhuongTien = LoaiPhuongTien::all();
        return view('resident.phuong-tien.create', compact('loaiPhuongTien', 'dsCanHo'));
    }

    public function store(Request $request)
    {
        $canHoIds = $this->getCanHoIds();
        if ($canHoIds->isEmpty()) {
            return redirect()->route('resident.dashboard')->with('error', 'Bạn chưa được phân căn hộ.');
        }

        $request->validate([
            'ten_phuong_tien'  => 'nullable|string|max:255',
            'bien_so'          => [
                'required', 'string', 'max:50',
                Rule::unique('phuong_tien', 'bien_so')->whereNull('deletedAt'),
            ],
            'loai_phuong_tien' => 'required|exists:loai_phuong_tien,id',
            'can_ho'           => ['required', 'integer', Rule::in($canHoIds)],
        ], [
            'bien_so.required' => 'Vui lòng nhập biển số xe.',
            'bien_so.unique'   => 'Biển số xe đã được đăng ký.',
            'can_ho.required'  => 'Vui lòng chọn căn hộ.',
            'can_ho.in'        => 'Căn hộ không hợp lệ hoặc không thuộc quyền sử dụng của bạn.',
        ]);

        PhuongTien::create([
            'ten_phuong_tien'  => $request->ten_phuong_tien,
            'bien_so'          => $request->bien_so,
            'loai_phuong_tien' => $request->loai_phuong_tien,
            'can_ho'           => (int) $request->can_ho,
            'ngay_dang_ky'     => now(),
            'trang_thai'       => 1,
        ]);

        return redirect()->route('resident.phuong-tien.index')->with('success', 'Đăng ký xe thành công. Đang chờ duyệt.');
    }

    public function destroy(PhuongTien $phuongTien)
    {
        $canHoIds = $this->getCanHoIds();
        if (!$canHoIds->contains($phuongTien->can_ho)) {
            abort(403);
        }

        $phuongTien->update(['trang_thai' => 0, 'ngay_huy' => now()]);
        return redirect()->route('resident.phuong-tien.index')->with('success', 'Đã hủy đăng ký phương tiện.');
    }
}
