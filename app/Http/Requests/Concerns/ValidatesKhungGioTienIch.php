<?php

namespace App\Http\Requests\Concerns;

use App\Models\TienIch;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

/**
 * Validation cross-field dùng chung cho mọi FormRequest đặt lịch tiện ích:
 * thời gian bắt đầu/kết thúc phải cùng ngày, và phải nằm trong giờ mở cửa
 * của tiện ích đã chọn. Trait để tránh lặp lại giữa Resident/Admin/Manager.
 */
trait ValidatesKhungGioTienIch
{
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
            $validator->errors()->add($fieldKetThuc, 'Thời gian bắt đầu và kết thúc phải cùng một ngày.');
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
            $validator->errors()->add(
                $fieldBatDau,
                "Tiện ích chỉ hoạt động trong khung giờ {$tienIch->gio_hoat_dong}."
            );
        }
    }
}
