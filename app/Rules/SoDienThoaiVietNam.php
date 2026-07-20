<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SoDienThoaiVietNam implements ValidationRule
{
    public const PATTERN = '/^(03|05|07|08|09)[0-9]{8}$/';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !preg_match(self::PATTERN, $value)) {
            $fail('Số điện thoại không đúng định dạng Việt Nam.');
        }
    }
}
