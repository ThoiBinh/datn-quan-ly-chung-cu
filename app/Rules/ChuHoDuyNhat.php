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

        $chuHoId = self::chuHoVaiTroId();

        if ($chuHoId === null || (int) $value !== (int) $chuHoId) {
            return;
        }

        if (self::daCoChuHo($this->canHoId, $chuHoId, $this->ignoreRecordId, $this->ignoreCuDanId)) {
            $fail('Căn hộ này đã có Chủ hộ. Mỗi căn hộ chỉ được phép có một Chủ hộ.');
        }
    }

    /**
     * Id của vai trò Chủ hộ (Chủ sở hữu), tra từ dữ liệu hệ thống (không hard
     * code id). Giá trị lưu trong bảng vai_tro là "Chủ sở hữu" (xem VaiTroSeeder).
     */
    public static function chuHoVaiTroId(): ?int
    {
        return VaiTro::where('vai_tro', 'Chủ sở hữu')->value('id');
    }

    /**
     * Căn hộ đã có Chủ hộ đang cư trú (trang_thai=1) hay chưa.
     * Dùng chung cho validation rule và endpoint kiểm tra AJAX phía frontend.
     */
    public static function daCoChuHo(
        mixed $canHoId,
        ?int $chuHoId = null,
        ?int $ignoreRecordId = null,
        ?int $ignoreCuDanId = null
    ): bool {
        $chuHoId ??= self::chuHoVaiTroId();

        if (empty($canHoId) || $chuHoId === null) {
            return false;
        }

        return CuDanCanHo::where('can_ho', $canHoId)
            ->where('vai_tro', $chuHoId)
            ->where('trang_thai', 1)
            ->when($ignoreRecordId, fn ($q) => $q->where('id', '!=', $ignoreRecordId))
            ->when($ignoreCuDanId, fn ($q) => $q->where('cu_dan', '!=', $ignoreCuDanId))
            ->exists();
    }

    public static function conCuTru(?string $ngayChuyenDi): bool
    {
        if (empty($ngayChuyenDi)) {
            return true;
        }

        return Carbon::parse($ngayChuyenDi)->gt(Carbon::today());
    }
}
