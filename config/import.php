<?php

return [
    // Sheet (tên trong file Excel) => class Import + thứ tự xử lý.
    // Thêm entity mới chỉ cần thêm 1 dòng ở đây + 1 class trong app/Imports — không
    // cần sửa Controller/Service/Route/View. Sheet "HuongDan" và mọi sheet không có
    // trong danh sách này sẽ tự động bị bỏ qua, không báo lỗi.
    'sheets' => [
        'ToaNha' => [
            'class' => \App\Imports\ToaNhaImport::class,
            'order' => 10,
        ],
        'LoaiCanHo' => [
            'class' => \App\Imports\LoaiCanHoImport::class,
            'order' => 20,
        ],
        'TrangThaiCanHo' => [
            'class' => \App\Imports\TrangThaiCanHoImport::class,
            'order' => 25,
        ],
        'CanHo' => [
            'class' => \App\Imports\CanHoImport::class,
            'order' => 30,
        ],
        'ChucVu' => [
            'class' => \App\Imports\ChucVuImport::class,
            'order' => 35,
        ],
        'NhanVien' => [
            'class' => \App\Imports\NhanVienImport::class,
            'order' => 40,
        ],
        'CuDan' => [
            'class' => \App\Imports\CuDanImport::class,
            'order' => 50,
        ],
        'CuDanCanHo' => [
            'class' => \App\Imports\CuDanCanHoImport::class,
            'order' => 60,
        ],
        'LoaiPhuongTien' => [
            'class' => \App\Imports\LoaiPhuongTienImport::class,
            'order' => 70,
        ],
        'PhuongTien' => [
            'class' => \App\Imports\PhuongTienImport::class,
            'order' => 80,
        ],
        'LoaiTienIch' => [
            'class' => \App\Imports\LoaiTienIchImport::class,
            'order' => 90,
        ],
        'TienIch' => [
            'class' => \App\Imports\TienIchImport::class,
            'order' => 100,
        ],
        'LoaiPhiDichVu' => [
            'class' => \App\Imports\LoaiPhiDichVuImport::class,
            'order' => 110,
        ],
        'PhiDichVu' => [
            'class' => \App\Imports\PhiDichVuImport::class,
            'order' => 120,
        ],
        'CanHoPhiDichVu' => [
            'class' => \App\Imports\CanHoPhiDichVuImport::class,
            'order' => 130,
        ],
        'BangTin' => [
            'class' => \App\Imports\BangTinImport::class,
            'order' => 140,
        ],
    ],

    // Sheet chỉ chứa hướng dẫn sử dụng, không bao giờ được import dù có mặt trong file.
    'instruction_sheet' => 'HuongDan',

    'max_file_size_kb' => env('IMPORT_MAX_FILE_SIZE_KB', 51200), // 50MB

    // Tổng số dòng dữ liệu (tất cả sheet cộng lại) vượt ngưỡng này sẽ tự động đẩy vào queue.
    'queue_threshold' => env('IMPORT_QUEUE_THRESHOLD', 5000),

    'chunk_size' => env('IMPORT_CHUNK_SIZE', 500),
    'batch_size' => env('IMPORT_BATCH_SIZE', 500),

    // Nơi lưu file Excel tải lên tạm thời + file lỗi sinh ra sau khi import.
    'disk' => env('IMPORT_DISK', 'local'),
    'upload_path' => 'imports/uploads',
    'error_path' => 'imports/errors',

    // TTL (giây) của tiến trình import lưu trong Cache, dùng cho polling AJAX.
    'progress_ttl' => 3600,
];
