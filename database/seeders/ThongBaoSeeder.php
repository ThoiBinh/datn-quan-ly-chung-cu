<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Sinh 50 bản ghi mẫu cho bảng thong_bao (bảng tin nội bộ, phát cho toàn thể
 * cư dân — không có cột người nhận/mức độ/trạng thái, trạng thái "đã đọc"
 * theo từng cư dân được theo dõi riêng ở bảng thong_bao_da_doc).
 *
 * Idempotent: mỗi tiêu đề được đánh số cố định "Thông báo số {i}: ..." theo
 * index sinh dữ liệu (không đổi giữa các lần chạy vì Faker được seed cố
 * định), dùng làm khóa cho updateOrInsert() nên chạy lại nhiều lần không
 * tạo dữ liệu trùng.
 */
class ThongBaoSeeder extends Seeder
{
    private const SO_BAN_GHI = 50;

    public function run(): void
    {
        $faker = FakerFactory::create('vi_VN');
        $faker->seed(20260714);

        $nguoiTaoIds = DB::table('nhan_vien')->pluck('id')->all();

        if (empty($nguoiTaoIds)) {
            $this->command?->warn('ThongBaoSeeder: thiếu dữ liệu nhan_vien, bỏ qua.');
            return;
        }

        $toaNha = ['A', 'B', 'C'];
        $tienIch = ['hồ bơi', 'phòng gym', 'sân tennis', 'phòng sinh hoạt cộng đồng'];
        $mauThongBao = [
            fn () => [
                'tieu_de' => sprintf('Nhắc thanh toán phí quản lý tháng %d/%d', $faker->numberBetween(1, 12), $faker->numberBetween(2025, 2026)),
                'noi_dung' => sprintf(
                    'Kính gửi quý cư dân, phí quản lý tháng %d/%d sẽ đến hạn thanh toán vào ngày %d. Vui lòng thanh toán đúng hạn để tránh phát sinh phí trễ hạn.',
                    $faker->numberBetween(1, 12), $faker->numberBetween(2025, 2026), $faker->numberBetween(1, 28)
                ),
            ],
            fn () => [
                'tieu_de' => sprintf('Đã ghi nhận thanh toán hóa đơn tháng %d/%d', $faker->numberBetween(1, 12), $faker->numberBetween(2025, 2026)),
                'noi_dung' => 'Ban quản lý xác nhận đã nhận được thanh toán hóa đơn của quý cư dân. Cảm ơn quý cư dân đã hợp tác.',
            ],
            fn () => [
                'tieu_de' => sprintf('Hóa đơn tháng %d/%d đã được phát hành', $faker->numberBetween(1, 12), $faker->numberBetween(2025, 2026)),
                'noi_dung' => sprintf('Hóa đơn phí dịch vụ đã có trên hệ thống, quý cư dân vui lòng kiểm tra và thanh toán trước ngày %d.', $faker->numberBetween(1, 28)),
            ],
            fn () => [
                'tieu_de' => sprintf('Đặt lịch tiện ích %s đã được duyệt', $faker->randomElement($tienIch)),
                'noi_dung' => sprintf('Lượt đặt lịch sử dụng %s ngày %d/%d của quý cư dân đã được Ban quản lý duyệt.', $faker->randomElement($tienIch), $faker->numberBetween(1, 28), $faker->numberBetween(1, 12)),
            ],
            fn () => [
                'tieu_de' => sprintf('Đặt lịch tiện ích %s bị từ chối', $faker->randomElement($tienIch)),
                'noi_dung' => sprintf('Rất tiếc, lượt đặt lịch sử dụng %s ngày %d/%d của quý cư dân không được duyệt do trùng lịch bảo trì.', $faker->randomElement($tienIch), $faker->numberBetween(1, 28), $faker->numberBetween(1, 12)),
            ],
            fn () => [
                'tieu_de' => sprintf('Đặt lịch tiện ích %s đã hủy', $faker->randomElement($tienIch)),
                'noi_dung' => sprintf('Lượt đặt lịch sử dụng %s ngày %d/%d đã được hủy theo yêu cầu của quý cư dân.', $faker->randomElement($tienIch), $faker->numberBetween(1, 28), $faker->numberBetween(1, 12)),
            ],
            fn () => [
                'tieu_de' => sprintf('Đặt lịch tiện ích %s đã hoàn thành', $faker->randomElement($tienIch)),
                'noi_dung' => sprintf('Lượt sử dụng %s ngày %d/%d của quý cư dân đã hoàn thành. Cảm ơn quý cư dân.', $faker->randomElement($tienIch), $faker->numberBetween(1, 28), $faker->numberBetween(1, 12)),
            ],
            fn () => [
                'tieu_de' => 'Bạn được chuyển từ hàng chờ sang Đã duyệt',
                'noi_dung' => sprintf('Do có cư dân khác hủy lịch, lượt đặt %s ngày %d/%d của quý cư dân đã được tự động chuyển từ hàng chờ sang Đã duyệt.', $faker->randomElement($tienIch), $faker->numberBetween(1, 28), $faker->numberBetween(1, 12)),
            ],
            fn () => [
                'tieu_de' => sprintf('Thông báo bảo trì thang máy tòa %s', $faker->randomElement($toaNha)),
                'noi_dung' => sprintf('Thang máy tòa %s sẽ được bảo trì định kỳ vào ngày %d/%d, từ %02d:00 đến %02d:00. Mong quý cư dân thông cảm.', $faker->randomElement($toaNha), $faker->numberBetween(1, 28), $faker->numberBetween(1, 12), $faker->numberBetween(8, 12), $faker->numberBetween(13, 17)),
            ],
            fn () => [
                'tieu_de' => sprintf('Thông báo cúp nước tòa %s', $faker->randomElement($toaNha)),
                'noi_dung' => sprintf('Do sự cố đường ống, tòa %s sẽ tạm ngưng cấp nước ngày %d/%d từ %02d:00 đến %02d:00.', $faker->randomElement($toaNha), $faker->numberBetween(1, 28), $faker->numberBetween(1, 12), $faker->numberBetween(8, 12), $faker->numberBetween(13, 17)),
            ],
            fn () => [
                'tieu_de' => sprintf('Thông báo cúp điện tòa %s', $faker->randomElement($toaNha)),
                'noi_dung' => sprintf('Điện lực khu vực sẽ cắt điện bảo trì tòa %s ngày %d/%d từ %02d:00 đến %02d:00.', $faker->randomElement($toaNha), $faker->numberBetween(1, 28), $faker->numberBetween(1, 12), $faker->numberBetween(8, 12), $faker->numberBetween(13, 17)),
            ],
            fn () => [
                'tieu_de' => sprintf('Diễn tập PCCC tòa %s', $faker->randomElement($toaNha)),
                'noi_dung' => sprintf('Ban quản lý tổ chức diễn tập phòng cháy chữa cháy tại tòa %s vào ngày %d/%d, kính mời cư dân tham gia.', $faker->randomElement($toaNha), $faker->numberBetween(1, 28), $faker->numberBetween(1, 12)),
            ],
            fn () => [
                'tieu_de' => 'Vệ sinh hồ bơi định kỳ',
                'noi_dung' => sprintf('Hồ bơi sẽ tạm ngưng phục vụ để vệ sinh định kỳ vào ngày %d/%d.', $faker->numberBetween(1, 28), $faker->numberBetween(1, 12)),
            ],
            fn () => [
                'tieu_de' => 'Bảo trì phòng gym',
                'noi_dung' => sprintf('Phòng gym sẽ tạm ngưng hoạt động để bảo trì thiết bị vào ngày %d/%d.', $faker->numberBetween(1, 28), $faker->numberBetween(1, 12)),
            ],
            fn () => [
                'tieu_de' => sprintf('Cảnh báo cháy tòa %s', $faker->randomElement($toaNha)),
                'noi_dung' => sprintf('Phát hiện sự cố cháy nhỏ tại tòa %s, đã được xử lý kịp thời. Đề nghị cư dân bình tĩnh, không hoảng loạn.', $faker->randomElement($toaNha)),
            ],
            fn () => [
                'tieu_de' => sprintf('Cảnh báo an ninh khu vực tòa %s', $faker->randomElement($toaNha)),
                'noi_dung' => sprintf('Ghi nhận có người lạ ra vào khu vực tòa %s, đề nghị cư dân nâng cao cảnh giác và báo bảo vệ khi phát hiện bất thường.', $faker->randomElement($toaNha)),
            ],
            fn () => [
                'tieu_de' => 'Thông báo khẩn',
                'noi_dung' => $faker->randomElement([
                    'Đề nghị toàn bộ cư dân di chuyển xe khỏi khu vực hầm B2 trước 22:00 hôm nay để phục vụ công tác bảo trì khẩn cấp.',
                    'Phát hiện rò rỉ gas tại khu bếp tầng trệt, đã được xử lý an toàn. Đề nghị cư dân không hoảng loạn.',
                    'Ban quản lý đang phối hợp xử lý sự cố mất nước khẩn cấp, sẽ thông báo lại khi khắc phục xong.',
                ]),
            ],
            fn () => [
                'tieu_de' => sprintf('Cập nhật ứng dụng phiên bản %d.%d.%d', $faker->numberBetween(1, 3), $faker->numberBetween(0, 9), $faker->numberBetween(0, 9)),
                'noi_dung' => 'Ứng dụng quản lý chung cư đã được cập nhật với nhiều cải tiến và sửa lỗi mới, quý cư dân vui lòng cập nhật lên phiên bản mới nhất.',
            ],
            fn () => [
                'tieu_de' => 'Thông báo hệ thống: đặt lại mật khẩu',
                'noi_dung' => 'Mật khẩu đăng nhập của một số tài khoản đã được đặt lại theo yêu cầu. Quý cư dân vui lòng đổi mật khẩu sau khi đăng nhập lại.',
            ],
            fn () => [
                'tieu_de' => 'Tài khoản cư dân được kích hoạt',
                'noi_dung' => 'Ban quản lý xác nhận tài khoản cư dân mới đã được kích hoạt thành công trên hệ thống.',
            ],
            fn () => [
                'tieu_de' => 'Hồ sơ căn hộ đã được cập nhật',
                'noi_dung' => 'Thông tin hồ sơ một số căn hộ đã được Ban quản lý cập nhật trên hệ thống, quý cư dân vui lòng kiểm tra lại thông tin của mình.',
            ],
        ];

        for ($i = 1; $i <= self::SO_BAN_GHI; $i++) {
            $mau = $mauThongBao[($i - 1) % count($mauThongBao)]();

            $ngayLech = $faker->numberBetween(0, 30);
            $createdAt = Carbon::today()->subDays($ngayLech)->setTime($faker->numberBetween(7, 20), $faker->randomElement([0, 15, 30, 45]));
            if ($createdAt->greaterThan(Carbon::now())) {
                $createdAt = Carbon::now()->copy();
            }
            $updatedAt = $createdAt->copy()->addMinutes($faker->numberBetween(0, 120));
            if ($updatedAt->greaterThan(Carbon::now())) {
                $updatedAt = Carbon::now()->copy();
            }

            $tieuDe = sprintf('Thông báo số %02d: %s', $i, $mau['tieu_de']);

            $data = [
                'tieu_de'   => $tieuDe,
                'noi_dung'  => $mau['noi_dung'],
                'nguoi_tao' => $faker->randomElement($nguoiTaoIds),
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ];

            DB::table('thong_bao')->updateOrInsert(
                ['tieu_de' => $tieuDe],
                $data
            );
        }
    }
}
