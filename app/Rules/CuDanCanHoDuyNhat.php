<?php

namespace App\Rules;

use App\Models\CuDanCanHo;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Đảm bảo một cư dân không bị gán trùng lặp vào cùng một căn hộ.
 * Áp dụng cho field `cu_dan`, so khớp với `can_ho` được chọn trong cùng request.
 */
class CuDanCanHoDuyNhat implements ValidationRule
{
    public function __construct(
        private readonly mixed $canHoId,
        private readonly ?int $ignoreRecordId = null,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || empty($this->canHoId)) {
            return;
        }

        $exists = CuDanCanHo::where('cu_dan', $value)
            ->where('can_ho', $this->canHoId)
            ->when($this->ignoreRecordId, fn ($q) => $q->where('id', '!=', $this->ignoreRecordId))
            ->exists();

        if ($exists) {
            $fail('Cư dân đã thuộc căn hộ này.');
        }
    }
}
