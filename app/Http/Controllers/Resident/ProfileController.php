<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\HoaDon;
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

        // Căn hộ hiện tại (trang_thai = 1)
        $cuDanCanHoHienTai = $cuDan->cuDanCanHo->where('trang_thai', 1)->first();
        $canHo = $cuDanCanHoHienTai?->canHo;

        // PhuongTien & HoaDon qua CanHo
        $phuongTiens = collect();
        $hoaDonStats = ['tong' => 0, 'chua_thanh_toan' => 0, 'da_thanh_toan' => 0, 'qua_han' => 0];

        if ($canHo) {
            $canHo->load(['phuongTien.loaiPhuongTien', 'hoaDon']);
            $phuongTiens = $canHo->phuongTien;

            $hoaDons = $canHo->hoaDon;
            $hoaDonStats = [
                'tong'            => $hoaDons->count(),
                'chua_thanh_toan' => $hoaDons->where('trang_thai', HoaDon::TRANG_THAI_CHUA_THANH_TOAN)->count(),
                'da_thanh_toan'   => $hoaDons->where('trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)->count(),
                'qua_han'         => $hoaDons->where('trang_thai', HoaDon::TRANG_THAI_QUA_HAN)->count(),
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

    public function update(Request $request)
    {
        $cuDan = auth('cudan')->user();

        $validated = $request->validate([
            'ho_ten_dem' => 'nullable|string|max:150',
            'ten'        => 'required|string|max:50',
            'email'      => 'nullable|email|max:150|unique:cu_dan,email,' . $cuDan->id,
            'sdt'        => 'nullable|string|max:15',
            'ngay_sinh'  => 'nullable|date',
            'gioi_tinh'  => 'nullable|integer|in:0,1,2',
            'tinh'       => 'nullable|string|max:100',
            'xa'         => 'nullable|string|max:100',
            'dia_chi'    => 'nullable|string|max:255',
        ], [
            'ten.required'        => 'Vui lòng nhập tên.',
            'ten.max'             => 'Tên không được vượt quá 50 ký tự.',
            'email.email'         => 'Địa chỉ email không hợp lệ.',
            'email.unique'        => 'Email này đã được sử dụng.',
            'sdt.max'             => 'Số điện thoại không được vượt quá 15 ký tự.',
            'ngay_sinh.date'      => 'Ngày sinh không hợp lệ.',
            'gioi_tinh.in'        => 'Giới tính không hợp lệ.',
        ]);

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
