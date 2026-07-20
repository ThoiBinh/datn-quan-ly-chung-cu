<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resident\UpdateProfileRequest;
use App\Models\HoaDon;
use App\Models\PhuongTien;
use App\Models\ThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $cuDan = auth('cudan')->user();

        // Eager load tất cả lịch sử căn hộ với nested relations
        $cuDan->load([
            'cuDanCanHo.canHo.toaNha',
            'cuDanCanHo.canHo.loaiCanHo',
            'cuDanCanHo.vaiTro',
        ]);

        // Căn hộ hiện tại (trang_thai = 1) — dùng cho hiển thị đại diện trên UI.
        $cuDanCanHoHienTai = $cuDan->cuDanCanHo->where('trang_thai', 1)->first();
        $canHo = $cuDanCanHoHienTai?->canHo;

        // PhuongTien & HoaDon của TẤT CẢ căn hộ mà cư dân đang cư trú, không chỉ căn hộ đại diện.
        $canHoIds = $cuDan->canHoIdsHienTai();
        $phuongTiens = collect();
        $hoaDonStats = ['tong' => 0, 'chua_thanh_toan' => 0, 'da_thanh_toan' => 0, 'qua_han' => 0];

        if ($canHoIds->isNotEmpty()) {
            $phuongTiens = PhuongTien::with('loaiPhuongTien')->whereIn('can_ho', $canHoIds)->get();

            $hoaDonQuery = HoaDon::whereIn('can_ho', $canHoIds);
            $hoaDonStats = [
                'tong'            => (clone $hoaDonQuery)->count(),
                'chua_thanh_toan' => (clone $hoaDonQuery)->where('trang_thai', HoaDon::TRANG_THAI_CHUA_THANH_TOAN)->count(),
                'da_thanh_toan'   => (clone $hoaDonQuery)->where('trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)->count(),
                'qua_han'         => (clone $hoaDonQuery)->where('trang_thai', HoaDon::TRANG_THAI_QUA_HAN)->count(),
            ];
        }

        // YeuCau
        $cuDan->load('yeuCau');
        $yeuCaus = $cuDan->yeuCau;
        $yeuCauStats = [
            'tong'        => $yeuCaus->count(),
            'dang_xu_ly'  => $yeuCaus->whereIn('trang_thai', [1, 2])->count(),
            'hoan_thanh'  => $yeuCaus->where('trang_thai', 3)->count(),
            'da_huy'      => $yeuCaus->where('trang_thai', 4)->count(),
        ];

        // ThongBao stats
        $cuDan->load('thongBaoDaDoc');
        $tongThongBao = ThongBao::whereNull('deletedAt')->count();
        $soThongBaoDaDoc = $cuDan->thongBaoDaDoc->count();
        $thongBaoStats = [
            'tong'     => $tongThongBao,
            'chua_doc' => max(0, $tongThongBao - $soThongBaoDaDoc),
            'da_doc'   => $soThongBaoDaDoc,
        ];

        return view('resident.profile.show', compact(
            'cuDan', 'cuDanCanHoHienTai', 'canHo',
            'phuongTiens', 'hoaDonStats', 'yeuCauStats', 'thongBaoStats'
        ));
    }

    public function edit()
    {
        $cuDan = auth('cudan')->user();
        return view('resident.profile.edit', compact('cuDan'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $cuDan = auth('cudan')->user();

        $validated = $request->validated();

        $cuDan->update(array_filter($validated, fn($v) => $v !== null));

        return redirect()->route('resident.profile.show')->with('success', 'Cập nhật hồ sơ thành công.');
    }

    public function updateAvatar(Request $request)
    {
        $cuDan = auth('cudan')->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh.',
            'avatar.image'    => 'File phải là ảnh.',
            'avatar.mimes'    => 'Chỉ chấp nhận định dạng jpg, jpeg, png, webp.',
            'avatar.max'      => 'Ảnh không được vượt quá 2MB.',
        ]);

        // Xóa avatar cũ (tất cả extension)
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            if (Storage::disk('public')->exists("avatars/{$cuDan->id}.{$ext}")) {
                Storage::disk('public')->delete("avatars/{$cuDan->id}.{$ext}");
            }
        }

        // Lưu avatar mới
        $ext = $request->file('avatar')->getClientOriginalExtension();
        $request->file('avatar')->storeAs('avatars', "{$cuDan->id}.{$ext}", 'public');

        return redirect()->route('resident.profile.show')->with('success', 'Cập nhật ảnh đại diện thành công.');
    }

    public function showChangePassword()
    {
        return view('resident.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $cuDan = auth('cudan')->user();

        $request->validate([
            'current_password'          => 'required',
            'password'                  => 'required|min:8|confirmed',
            'password_confirmation'     => 'required',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required'         => 'Vui lòng nhập mật khẩu mới.',
            'password.min'              => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed'        => 'Xác nhận mật khẩu không khớp.',
        ]);

        if (!Hash::check($request->current_password, $cuDan->mat_khau)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $cuDan->update(['mat_khau' => Hash::make($request->password)]);

        return redirect()->route('resident.profile.show')->with('success', 'Đổi mật khẩu thành công.');
    }
}
