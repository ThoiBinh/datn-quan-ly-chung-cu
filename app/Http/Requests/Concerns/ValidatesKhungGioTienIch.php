<?php

namespace App\Http\Requests\Concerns;

use App\Models\TienIch;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

/**
 * Validation cross-field dùng chung cho mọi FormRequest đặt lịch tiện ích:
 * phút bắt đầu/kết thúc chỉ được 00 hoặc 30, không được ở quá khứ, phải
 * cùng ngày, phải nằm trong giờ mở cửa, và phải có thời lượng hợp lệ (tối
 * thiểu 01 giờ, tối đa 8 giờ). Trait để tránh lặp lại giữa Resident/Admin/Manager.
 */
trait ValidatesKhungGioTienIch
{
    /**
     * Phút của thời gian bắt đầu/kết thúc chỉ được phép là 00 hoặc 30.
     */
    private function validatePhutHopLe(
        ValidatorContract $validator,
        string $fieldBatDau = 'thoi_gian_bat_dau',
        string $fieldKetThuc = 'thoi_gian_ket_thuc'
    ): void {
        if ($validator->errors()->hasAny([$fieldBatDau, $fieldKetThuc])) {
            return;
        }

        foreach ([$fieldBatDau, $fieldKetThuc] as $field) {
            $gioTri = $this->input($field);

            if (!$gioTri) {
                continue;
            }

            try {
                $phut = Carbon::parse($gioTri)->minute;
            } catch (\Throwable) {
                continue;
            }

            if (!in_array($phut, [0, 30], true)) {
                $validator->errors()->add($field, 'Chỉ được chọn giờ tròn hoặc giờ rưỡi (00 hoặc 30 phút).');
            }
        }
    }

    /**
     * Không được đặt lịch trong quá khứ: thời gian bắt đầu phải lớn hơn
     * thời điểm hiện tại.
     */
    private function validateKhongQuaKhu(
        ValidatorContract $validator,
        string $fieldBatDau = 'thoi_gian_bat_dau',
        string $fieldKetThuc = 'thoi_gian_ket_thuc'
    ): void {
        if ($validator->errors()->hasAny([$fieldBatDau, $fieldKetThuc])) {
            return;
        }

        $batDau = $this->input($fieldBatDau);

        if (!$batDau) {
            return;
        }

        try {
            $batDau = Carbon::parse($batDau);
        } catch (\Throwable) {
            return;
        }

        if ($batDau->lte(now())) {
            $validator->errors()->add($fieldBatDau, 'Không được đặt lịch trong quá khứ.');
        }
    }

    private function validateCungNgay(
        ValidatorContract $validator,
        string $fieldBatDau = 'thoi_gian_bat_dau',
        string $fieldKetThuc = 'thoi_gian_ket_thuc'
    ): void {
        if ($validator->errors()->hasAny([$fieldBatDau, $fieldKetThuc])) {
            return;
        }

        $batDau  = $this->input($fieldBatDau);
        $ketThuc = $this->input($fieldKetThuc);

        if (!$batDau || !$ketThuc) {
            return;
        }

        try {
            $ngayBatDau  = Carbon::parse($batDau)->toDateString();
            $ngayKetThuc = Carbon::parse($ketThuc)->toDateString();
        } catch (\Throwable) {
            return;
        }

        if ($ngayBatDau !== $ngayKetThuc) {
            $validator->errors()->add($fieldKetThuc, 'Không được đặt lịch qua ngày.');
        }
    }

    /**
     * Thời lượng sử dụng phải từ 01 giờ đến 8 giờ.
     */
    private function validateThoiLuong(
        ValidatorContract $validator,
        string $fieldBatDau = 'thoi_gian_bat_dau',
        string $fieldKetThuc = 'thoi_gian_ket_thuc'
    ): void {
        if ($validator->errors()->hasAny([$fieldBatDau, $fieldKetThuc])) {
            return;
        }

        $batDau  = $this->input($fieldBatDau);
        $ketThuc = $this->input($fieldKetThuc);

        if (!$batDau || !$ketThuc) {
            return;
        }

        try {
            $soPhut = Carbon::parse($batDau)->diffInMinutes(Carbon::parse($ketThuc));
        } catch (\Throwable) {
            return;
        }

        if ($soPhut < 60) {
            $validator->errors()->add($fieldKetThuc, 'Thời gian sử dụng phải tối thiểu 01 giờ.');

            return;
        }

        if ($soPhut > 8 * 60) {
            $validator->errors()->add($fieldKetThuc, 'Một lượt đặt không được vượt quá 8 giờ.');
        }
    }

    private function validateTrongGioMoCua(
        ValidatorContract $validator,
        string $fieldTienIch = 'tien_ich',
        string $fieldBatDau = 'thoi_gian_bat_dau',
        string $fieldKetThuc = 'thoi_gian_ket_thuc'
    ): void {
        if ($validator->errors()->hasAny([$fieldTienIch, $fieldBatDau, $fieldKetThuc])) {
            return;
        }

        $tienIch = TienIch::find($this->input($fieldTienIch));

        if (!$tienIch || !$tienIch->gio_mo_cua || !$tienIch->gio_dong_cua) {
            return;
        }

        try {
            $gioBatDau  = Carbon::parse($this->input($fieldBatDau))->format('H:i:s');
            $gioKetThuc = Carbon::parse($this->input($fieldKetThuc))->format('H:i:s');
        } catch (\Throwable) {
            return;
        }

        $gioMoCua   = Carbon::parse($tienIch->gio_mo_cua)->format('H:i:s');
        $gioDongCua = Carbon::parse($tienIch->gio_dong_cua)->format('H:i:s');

        if ($gioBatDau < $gioMoCua || $gioKetThuc > $gioDongCua) {
            $validator->errors()->add($fieldBatDau, 'Thời gian đặt phải nằm trong giờ hoạt động của tiện ích.');
        }
    }
}
