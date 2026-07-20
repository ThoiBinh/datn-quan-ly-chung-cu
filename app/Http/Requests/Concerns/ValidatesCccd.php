<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Rule;

trait ValidatesCccd
{
    protected function prepareCccdForValidation(): void
    {
        if (is_string($this->input('cccd'))) {
            $this->merge(['cccd' => trim($this->input('cccd'))]);
        }
    }

    protected function cccdRules(bool $required, string $table, int|string|null $ignoreId = null): array
    {
        $unique = Rule::unique($table, 'cccd');

        if ($ignoreId !== null) {
            $unique = $unique->ignore($ignoreId);
        }

        return [
            $required ? 'required' : 'nullable',
            'string',
            'digits:12',
            'regex:/^[0-9]{12}$/',
            $unique,
        ];
    }

    protected function cccdMessages(): array
    {
        return [
            'cccd.required' => 'Vui lòng nhập số CCCD.',
            'cccd.digits'   => 'Số CCCD phải gồm đúng 12 chữ số.',
            'cccd.regex'    => 'Số CCCD chỉ được chứa các chữ số.',
            'cccd.unique'   => 'Số CCCD đã tồn tại trong hệ thống.',
        ];
    }
}
