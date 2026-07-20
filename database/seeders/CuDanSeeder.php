<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CuDanSeeder extends Seeder
{
    /**
     * Sinh 50 cư dân mẫu, dữ liệu thực tế (tên/địa chỉ Việt Nam theo đơn vị hành chính mới sau sáp nhập).
     * Idempotent: chỉ insert bản ghi có email chưa tồn tại, không truncate/xóa dữ liệu cũ.
     */
    public function run(): void
    {
        $password = Hash::make('12345678');

        // Danh sách nhân viên hợp lệ để gán nguoi_cap_nhat (khóa ngoại nhan_vien.id)
        $nhanVienIds = DB::table('nhan_vien')->pluck('id')->all();
        if (empty($nhanVienIds)) {
            $nhanVienIds = [1];
        }

        // Họ + chữ đệm theo giới tính để tên xen kẽ nam/nữ tự nhiên, không dùng "Nguyễn Văn A"/"Trần Thị B"
        $hoDemNam = ['Nguyễn Minh', 'Trần Ngọc', 'Lê Hoàng', 'Phạm Đức', 'Đỗ Đức', 'Huỳnh Thanh', 'Phan Quốc', 'Bùi Quốc', 'Vũ Anh', 'Đặng Xuân', 'Hoàng Gia', 'Ngô Bảo', 'Dương Công', 'Đinh Tiến', 'Lý Hữu'];
        $tenNam = ['Khôi', 'Nam', 'Bảo', 'Phúc', 'Anh', 'Tâm', 'Duy', 'Hiếu', 'Khang', 'Long', 'Phong', 'Quang', 'Thắng', 'Việt', 'Đạt'];

        $hoDemNu = ['Trần Ngọc', 'Phạm Gia', 'Phan Khánh', 'Lê Thu', 'Nguyễn Bảo', 'Đỗ Hồng', 'Huỳnh Diệu', 'Vũ Thùy', 'Đặng Minh', 'Bùi Thanh', 'Hoàng Yến', 'Ngô Quỳnh', 'Dương Ái', 'Đinh Thảo', 'Lý Mỹ'];
        $tenNu = ['Hân', 'Vy', 'Anh', 'Linh', 'Ngọc', 'Nhi', 'My', 'Trâm', 'Thư', 'Uyên', 'Chi', 'Hà', 'Quỳnh', 'Trang', 'Xuân'];

        // Địa chỉ theo đơn vị hành chính mới (Phường/Xã, Tỉnh/Thành phố) - không dùng Quận/Huyện/Thị xã
        $duong = ['Nguyễn Huệ', 'Lê Văn Sỹ', 'Võ Văn Kiệt', 'Phạm Văn Đồng', 'Điện Biên Phủ', 'Nguyễn Trãi', 'Trần Hưng Đạo', 'Hùng Vương', 'Lý Thường Kiệt', 'Nguyễn Văn Cừ', 'Cách Mạng Tháng Tám', 'Trường Chinh', 'Nguyễn Thị Minh Khai', 'Lê Lợi', 'Hai Bà Trưng'];
        $phuongXaTinh = [
            ['xa' => 'Phường Sài Gòn', 'tinh' => 'Thành phố Hồ Chí Minh'],
            ['xa' => 'Phường Tân Sơn', 'tinh' => 'Thành phố Hồ Chí Minh'],
            ['xa' => 'Phường Chợ Lớn', 'tinh' => 'Thành phố Hồ Chí Minh'],
            ['xa' => 'Phường Hiệp Bình', 'tinh' => 'Thành phố Hồ Chí Minh'],
            ['xa' => 'Phường Gia Định', 'tinh' => 'Thành phố Hồ Chí Minh'],
            ['xa' => 'Phường Bến Thành', 'tinh' => 'Thành phố Hồ Chí Minh'],
            ['xa' => 'Phường Chợ Quán', 'tinh' => 'Thành phố Hồ Chí Minh'],
            ['xa' => 'Phường Hải Châu', 'tinh' => 'Thành phố Đà Nẵng'],
            ['xa' => 'Phường Ninh Kiều', 'tinh' => 'Thành phố Cần Thơ'],
            ['xa' => 'Phường Hồng Gai', 'tinh' => 'Tỉnh Quảng Ninh'],
            ['xa' => 'Phường Hoàn Kiếm', 'tinh' => 'Thành phố Hà Nội'],
            ['xa' => 'Phường Ba Đình', 'tinh' => 'Thành phố Hà Nội'],
        ];

        // Đầu số điện thoại di động Việt Nam hợp lệ
        $dauSo = ['032', '033', '034', '035', '036', '037', '038', '039', '070', '076', '077', '078', '079', '081', '082', '083', '084', '085', '086', '088', '089', '090', '091', '093', '094', '096', '097', '098', '099'];

        $usedCccd = array_flip(DB::table('cu_dan')->pluck('cccd')->filter()->all());
        $usedSdt = array_flip(DB::table('cu_dan')->pluck('sdt')->filter()->all());

        $data = [];
        for ($i = 1; $i <= 50; $i++) {
            $isNam = $i % 2 === 1; // Nam/nữ xen kẽ

            if ($isNam) {
                $hoDem = $hoDemNam[($i - 1) % count($hoDemNam)];
                $ten = $tenNam[intdiv($i - 1, count($hoDemNam)) % count($tenNam)];
            } else {
                $hoDem = $hoDemNu[($i - 1) % count($hoDemNu)];
                $ten = $tenNu[intdiv($i - 1, count($hoDemNu)) % count($tenNu)];
            }

            $diaChiInfo = $phuongXaTinh[($i - 1) % count($phuongXaTinh)];
            $soNha = 1 + (($i * 37) % 200);
            $tenDuong = $duong[($i - 1) % count($duong)];

            $cccd = $this->sinhCccdDuyNhat($usedCccd);
            $sdt = $this->sinhSdtDuyNhat($usedSdt, $dauSo, $i);

            $createdAt = now()->subDays(rand(0, 730))->subSeconds(rand(0, 86399));
            $updatedAt = (clone $createdAt)->addSeconds(rand(0, 86400 * 30));

            $data[] = [
                'ho_ten_dem'     => $hoDem,
                'ten'            => $ten,
                'sdt'            => $sdt,
                'cccd'           => $cccd,
                'email'          => 'cudan' . $i . '@chungcu.vn',
                'mat_khau'       => $password,
                'ngay_sinh'      => date('Y-m-d', mktime(0, 0, 0, rand(1, 12), rand(1, 28), rand(1965, 2005))),
                'gioi_tinh'      => $isNam ? 1 : 0,
                'tinh'           => $diaChiInfo['tinh'],
                'xa'             => $diaChiInfo['xa'],
                'dia_chi'        => $soNha . ' ' . $tenDuong,
                'trang_thai'     => 1, // 1: đang cư trú (theo comment migration cu_dan.trang_thai)
                'nguoi_cap_nhat' => $nhanVienIds[array_rand($nhanVienIds)],
                'createdAt'      => $createdAt,
                'updatedAt'      => $updatedAt,
            ];
        }

        // updateOrInsert theo email: không truncate/xóa dữ liệu cũ, chạy lại nhiều lần vẫn an toàn
        // và tự làm mới các bản ghi mẫu (cudanN@chungcu.vn) về dữ liệu thực tế.
        foreach ($data as $item) {
            DB::table('cu_dan')->updateOrInsert(
                ['email' => $item['email']],
                $item
            );
        }
    }

    /**
     * Sinh số CCCD 12 chữ số chưa tồn tại (tránh trùng với dữ liệu cũ và trong lô sinh mới).
     */
    private function sinhCccdDuyNhat(array &$usedCccd): string
    {
        do {
            $cccd = '079' . str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
        } while (isset($usedCccd[$cccd]));

        $usedCccd[$cccd] = true;

        return $cccd;
    }

    /**
     * Sinh SĐT theo đầu số Việt Nam hợp lệ, chưa tồn tại.
     */
    private function sinhSdtDuyNhat(array &$usedSdt, array $dauSo, int $seed): string
    {
        do {
            $prefix = $dauSo[array_rand($dauSo)];
            $sdt = $prefix . str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
        } while (isset($usedSdt[$sdt]));

        $usedSdt[$sdt] = true;

        return $sdt;
    }
}
