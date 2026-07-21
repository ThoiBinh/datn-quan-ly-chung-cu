<?php

namespace App\Exports;

use App\Exports\Sheets\ImportTemplateSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * File template nhiều sheet cho Universal Import — sinh HOÀN TOÀN từ
 * config('import.sheets'), không hard-code tên sheet nào ở đây. Thêm 1 entity mới
 * vào config là template tự có thêm 1 sheet tương ứng, không cần sửa gì ở đây.
 */
class ImportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = ['HuongDan' => $this->huongDanSheet()];

        $configured = collect(config('import.sheets', []))
            ->map(fn ($cfg, $key) => array_merge($cfg, ['key' => $key]))
            ->sortBy('order');

        foreach ($configured as $cfg) {
            /** @var class-string<\App\Imports\AbstractSheetImport> $class */
            $class = $cfg['class'];

            $sheets[$cfg['key']] = new ImportTemplateSheet(
                $cfg['key'],
                $class::expectedHeaders(),
                [$class::sampleRow()]
            );
        }

        return $sheets;
    }

    private function huongDanSheet(): ImportTemplateSheet
    {
        $lines = [
            ['Sheet "HuongDan" chỉ chứa hướng dẫn, sẽ KHÔNG được import.'],
            ['Mỗi sheet phải giữ đúng tên cột ở dòng tiêu đề (dòng 1) — không đổi tên, không xoá cột.'],
            ['Xoá dòng dữ liệu mẫu (dòng 2) trước khi nhập dữ liệu thật.'],
            ['Sheet không có dữ liệu hoặc không có trong danh sách hỗ trợ sẽ tự động bị bỏ qua, không báo lỗi.'],
            ['Các cột tham chiếu (ví dụ tien_to, ten_loai_can_ho, so_can_ho, ma_cu_dan...) phải khớp với dữ liệu đã có trong hệ thống hoặc dữ liệu ở sheet khác trong CÙNG file này.'],
            ['Ngày tháng nhập theo định dạng yyyy-mm-dd (ví dụ 2024-01-01).'],
            ['Cột hinh_url (sheet BangTin) có thể dán URL/text sẵn có, HOẶC chèn ảnh trực tiếp vào ô đó (Insert > Picture) — hệ thống tự trích ảnh theo từng dòng.'],
            ['Nên dùng "Kiểm tra dữ liệu" (Dry Run) trước khi Import thật để phát hiện lỗi mà không ảnh hưởng dữ liệu hệ thống.'],
        ];

        return new ImportTemplateSheet('HuongDan', ['Hướng dẫn sử dụng file import'], $lines);
    }
}
