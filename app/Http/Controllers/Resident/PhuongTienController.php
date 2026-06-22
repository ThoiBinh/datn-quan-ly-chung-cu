<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\LoaiPhuongTien;
use App\Models\PhuongTien;
use Illuminate\Http\Request;

class PhuongTienController extends Controller
{
    private function getCanHoId(): ?int
    {
        return auth('cudan')->user()?->canHoHienTai?->can_ho;
    }

    public function index()
    {
        $canHoId = $this->getCanHoId();
        if (!$canHoId) {
            return view('resident.phuong-tien.index', ['phuongTien' => collect()]);
        }

        $phuongTien = PhuongTien::with('loaiPhuongTien')
            ->where('can_ho', $canHoId)
            ->get();

        return view('resident.phuong-tien.index', compact('phuongTien'));
    }

    public function create()
    {
        $canHoId = $this->getCanHoId();
        if (!$canHoId) {
            return redirect()->route('resident.dashboard')->with('error', 'Bạn chưa được phân căn hộ.');
        }

        $loaiPhuongTien = LoaiPhuongTien::all();
        return view('resident.phuong-tien.create', compact('loaiPhuongTien'));
    }

    public function store(Request $request)
    {
        $canHoId = $this->getCanHoId();
        if (!$canHoId) {
            return redirect()->route('resident.dashboard')->with('error', 'Bạn chưa được phân căn hộ.');
        }

        $request->validate([
            'ten_phuong_tien'  => 'nullable|string|max:255',
            'bien_so'          => 'required|string|max:50|unique:phuong_tien,bien_so',
            'loai_phuong_tien' => 'required|exists:loai_phuong_tien,id',
        ], [
            'bien_so.required' => 'Vui lòng nhập biển số xe.',
            'bien_so.unique'   => 'Biển số xe đã được đăng ký.',
        ]);

        PhuongTien::create([
            'ten_phuong_tien'  => $request->ten_phuong_tien,
            'bien_so'          => $request->bien_so,
            'loai_phuong_tien' => $request->loai_phuong_tien,
            'can_ho'           => $canHoId,
            'ngay_dang_ky'     => now(),
            'trang_thai'       => 1,
        ]);

        return redirect()->route('resident.phuong-tien.index')->with('success', 'Đăng ký xe thành công. Đang chờ duyệt.');
    }

    public function destroy(PhuongTien $phuongTien)
    {
        $canHoId = $this->getCanHoId();
        if ($phuongTien->can_ho !== $canHoId) {
            abort(403);
        }

        $phuongTien->update(['trang_thai' => 0]);
        return redirect()->route('resident.phuong-tien.index')->with('success', 'Đã hủy đăng ký phương tiện.');
    }
}
