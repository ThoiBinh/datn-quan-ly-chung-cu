<?php

namespace App\Rules;

use App\Models\CuDanCanHo;
use App\Models\VaiTro;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Đảm bảo mỗi căn hộ chỉ có duy nhất một cư dân đang cư trú với vai trò "Chủ hộ".
 * Áp dụng cho field `vai_tro`: chỉ kiểm tra khi giá trị được chọn là vai trò "Chủ hộ".
 */
class ChuHoDuyNhat implements ValidationRule
{
    public function __construct(
        private readonly mixed $canHoId,
        private readonly bool $dangHoatDong = true,
        private readonly ?int $ignoreRecordId = null,
        private readonly ?int $ignoreCuDanId = null,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || empty($this->canHoId) || !$this->dangHoatDong) {
            return;
        }

        $chuHoId = VaiTro::where('vai_tro', 'Chủ hộ')->value('id');

        if ($chuHoId === null || (int) $value !== (int) $chuHoId) {
            return;
        }

        $daCoChuHo = CuDanCanHo::where('can_ho', $this->canHoId)
            ->where('vai_tro', $chuHoId)
            ->where('trang_thai', 1)
            ->when($this->ignoreRecordId, fn ($q) => $q->where('id', '!=', $this->ignoreRecordId))
            ->when($this->ignoreCuDanId, fn ($q) => $q->where('cu_dan', '!=', $this->ignoreCuDanId))
            ->exists();

        if ($daCoChuHo) {
            $fail('Căn hộ này đã có Chủ hộ. Mỗi căn hộ chỉ được phép có một Chủ hộ.');
        }
    }

    public static function conCuTru(?string $ngayChuyenDi): bool
    {
        if (empty($ngayChuyenDi)) {
            return true;
        }

        return Carbon::parse($ngayChuyenDi)->gt(Carbon::today());
    }
}
