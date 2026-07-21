<?php

namespace App\Http\Requests\Admin;

use App\Services\Import\ExcelSignatureValidator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Kiểm tra file Excel tải lên cho Universal Import: đuôi file + MIME (rule
 * "mimes"), dung lượng tối đa, và magic bytes thật của file (ExcelSignatureValidator)
 * để chặn .php/.exe/.js/.bat/.sh/.zip/.rar đổi đuôi thành .xlsx/.xls.
 */
class ImportUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxKb = (int) config('import.max_file_size_kb', 51200);

        return [
            'file' => [
                'required', 'file', 'mimes:xlsx,xls', "max:{$maxKb}",
                function ($attribute, $value, $fail) {
                    if (!app(ExcelSignatureValidator::class)->isValid($value->getRealPath())) {
                        $fail('File không đúng định dạng Excel (.xlsx/.xls) hợp lệ.');
                    }
                },
            ],
            'dry_run' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Vui lòng chọn file Excel.',
            'file.mimes' => 'Chỉ chấp nhận file .xlsx hoặc .xls.',
            'file.max' => 'Dung lượng file vượt quá giới hạn cho phép (50MB).',
        ];
    }
}
