<?php

namespace Database\Seeders;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Sinh 50 bản ghi mẫu cho bảng thong_bao.
 *
 * Lưu ý về schema thực tế (xem migration 2024_01_01_000019_create_thong_bao_table.php):
 * bảng chỉ có id, tieu_de, noi_dung, nguoi_tao, createdAt, updatedAt, deletedAt.
 * Đây là thông báo chung phát cho toàn thể cư dân (không có cột cu_dan_id/
 * nguoi_gui_id, không có cột loại/muc_do/da_doc/duong_dan/icon), nên các yêu
 * cầu tương ứng với những cột không tồn tại được bỏ qua theo đúng nguyên tắc
 * "không tạo cột/khóa ngoại không có trong schema". Trạng thái đã đọc theo
 * từng cư dân được theo dõi riêng ở bảng thong_bao_da_doc (ngoài phạm vi seeder này).
 *
 * Idempotent: dùng updateOrInsert theo tieu_de (đã đảm bảo không trùng lặp ở
 * danh sách bên dưới), không truncate/delete dữ liệu cũ nên chạy lại nhiều
 * lần không lỗi và không làm mất dữ liệu.
 */
class ThongBaoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('vi_VN');

        // Người tạo thông báo lấy ngẫu nhiên từ nhan_vien đang có (khóa ngoại nguoi_tao)
        $nguoiTaoIds = DB::table('nhan_vien')->pluck('id')->all();
        if (empty($nguoiTaoIds)) {
            $nguoiTaoIds = [1];
        }

        foreach ($this->danhSachThongBao() as $item) {
            $createdAt = $faker->dateTimeBetween('-60 days', 'now');
            $updatedAt = $faker->dateTimeBetween($createdAt, 'now');

            DB::table('thong_bao')->updateOrInsert(
                ['tieu_de' => $item['tieu_de']],
                [
                    'noi_dung'  => $item['noi_dung'],
                    'nguoi_tao' => $faker->randomElement($nguoiTaoIds),
                    'createdAt' => $createdAt,
                    'updatedAt' => $updatedAt,
                ]
            );
        }
    }

    /**
     * 50 thông báo mẫu, tiêu đề không trùng, bao phủ đủ 20 nhóm chủ đề yêu cầu:
     * hóa đơn (mới/sắp hạn/quá hạn), thanh toán (thành công/thất bại), đặt tiện
     * ích (duyệt/từ chối/hủy), yêu cầu (phản hồi/tiếp nhận/đã xử lý), bảng tin,
     * tin tức, bảo trì, cắt điện, cắt nước, diễn tập PCCC, nghỉ lễ, nhắc cập
     * nhật thông tin cá nhân và cảnh báo an ninh.
     *
     * @return array<int, array{tieu_de: string, noi_dung: string}>
     */
    private function danhSachThongBao(): array
    {
        return [
            // ===== Có hóa đơn mới (3) =====
            ['tieu_de' => 'Bạn có hóa đơn mới tháng 06/2026', 'noi_dung' => 'Ban quản lý vừa phát hành hóa đơn phí dịch vụ tháng 06/2026. Vui lòng kiểm tra và thanh toán đúng hạn.'],
            ['tieu_de' => 'Bạn có hóa đơn mới tháng 07/2026', 'noi_dung' => 'Ban quản lý vừa phát hành hóa đơn phí dịch vụ tháng 07/2026. Vui lòng kiểm tra và thanh toán đúng hạn.'],
            ['tieu_de' => 'Bạn có hóa đơn mới tháng 08/2026', 'noi_dung' => 'Ban quản lý vừa phát hành hóa đơn phí dịch vụ tháng 08/2026. Vui lòng kiểm tra và thanh toán đúng hạn.'],

            // ===== Hóa đơn sắp đến hạn (3) =====
            ['tieu_de' => 'Hóa đơn tháng 06/2026 sắp đến hạn thanh toán', 'noi_dung' => 'Hóa đơn phí dịch vụ tháng 06/2026 của căn hộ sẽ đến hạn thanh toán trong 3 ngày tới. Vui lòng thanh toán sớm để tránh trễ hạn.'],
            ['tieu_de' => 'Hóa đơn tháng 07/2026 sắp đến hạn thanh toán', 'noi_dung' => 'Hóa đơn phí dịch vụ tháng 07/2026 của căn hộ sẽ đến hạn thanh toán trong 3 ngày tới. Vui lòng thanh toán sớm để tránh trễ hạn.'],
            ['tieu_de' => 'Hóa đơn tháng 08/2026 sắp đến hạn thanh toán', 'noi_dung' => 'Hóa đơn phí dịch vụ tháng 08/2026 của căn hộ sẽ đến hạn thanh toán trong 3 ngày tới. Vui lòng thanh toán sớm để tránh trễ hạn.'],

            // ===== Hóa đơn quá hạn (3) =====
            ['tieu_de' => 'Hóa đơn tháng 05/2026 đã quá hạn thanh toán', 'noi_dung' => 'Hóa đơn phí dịch vụ tháng 05/2026 của căn hộ đã quá hạn thanh toán. Vui lòng thanh toán sớm để tránh phát sinh phí trễ hạn.'],
            ['tieu_de' => 'Hóa đơn tháng 04/2026 đã quá hạn thanh toán', 'noi_dung' => 'Hóa đơn phí dịch vụ tháng 04/2026 của căn hộ đã quá hạn thanh toán. Vui lòng thanh toán sớm để tránh phát sinh phí trễ hạn.'],
            ['tieu_de' => 'Hóa đơn tháng 03/2026 đã quá hạn thanh toán', 'noi_dung' => 'Hóa đơn phí dịch vụ tháng 03/2026 của căn hộ đã quá hạn thanh toán. Vui lòng thanh toán sớm để tránh phát sinh phí trễ hạn.'],

            // ===== Thanh toán thành công (3) =====
            ['tieu_de' => 'Thanh toán hóa đơn tháng 06/2026 thành công', 'noi_dung' => 'Ban quản lý xác nhận đã nhận được thanh toán hóa đơn phí dịch vụ tháng 06/2026 của Quý cư dân. Cảm ơn Quý cư dân đã hợp tác.'],
            ['tieu_de' => 'Thanh toán hóa đơn tháng 07/2026 thành công', 'noi_dung' => 'Ban quản lý xác nhận đã nhận được thanh toán hóa đơn phí dịch vụ tháng 07/2026 của Quý cư dân. Cảm ơn Quý cư dân đã hợp tác.'],
            ['tieu_de' => 'Thanh toán hóa đơn tháng 08/2026 thành công', 'noi_dung' => 'Ban quản lý xác nhận đã nhận được thanh toán hóa đơn phí dịch vụ tháng 08/2026 của Quý cư dân. Cảm ơn Quý cư dân đã hợp tác.'],

            // ===== Thanh toán thất bại (3) =====
            ['tieu_de' => 'Thanh toán hóa đơn tháng 06/2026 thất bại', 'noi_dung' => 'Giao dịch thanh toán hóa đơn phí dịch vụ tháng 06/2026 không thành công do lỗi kết nối ngân hàng. Vui lòng thử lại hoặc liên hệ Ban quản lý để được hỗ trợ.'],
            ['tieu_de' => 'Thanh toán hóa đơn tháng 07/2026 thất bại', 'noi_dung' => 'Giao dịch thanh toán hóa đơn phí dịch vụ tháng 07/2026 không thành công do lỗi kết nối ngân hàng. Vui lòng thử lại hoặc liên hệ Ban quản lý để được hỗ trợ.'],
            ['tieu_de' => 'Thanh toán hóa đơn tháng 08/2026 thất bại', 'noi_dung' => 'Giao dịch thanh toán hóa đơn phí dịch vụ tháng 08/2026 không thành công do lỗi kết nối ngân hàng. Vui lòng thử lại hoặc liên hệ Ban quản lý để được hỗ trợ.'],

            // ===== Đơn đặt tiện ích đã được duyệt (3) =====
            ['tieu_de' => 'Đặt lịch hồ bơi đã được duyệt', 'noi_dung' => 'Đơn đặt lịch sử dụng hồ bơi của Quý cư dân đã được Ban quản lý phê duyệt. Vui lòng đến đúng thời gian đã đăng ký.'],
            ['tieu_de' => 'Đặt lịch sân tennis đã được duyệt', 'noi_dung' => 'Đơn đặt sân tennis của Quý cư dân đã được phê duyệt. Vui lòng đến đúng thời gian đã đăng ký.'],
            ['tieu_de' => 'Đặt lịch phòng sinh hoạt cộng đồng đã được duyệt', 'noi_dung' => 'Đơn đặt phòng sinh hoạt cộng đồng của Quý cư dân đã được phê duyệt. Vui lòng đến đúng thời gian đã đăng ký.'],

            // ===== Đơn đặt tiện ích bị từ chối (3) =====
            ['tieu_de' => 'Đặt lịch hồ bơi bị từ chối', 'noi_dung' => 'Rất tiếc, đơn đặt lịch sử dụng hồ bơi của Quý cư dân không được duyệt do trùng lịch bảo trì. Vui lòng chọn khung giờ khác.'],
            ['tieu_de' => 'Đặt lịch sân tennis bị từ chối', 'noi_dung' => 'Rất tiếc, đơn đặt sân tennis của Quý cư dân không được duyệt do trùng lịch với cư dân khác. Vui lòng chọn khung giờ khác.'],
            ['tieu_de' => 'Đặt lịch phòng gym bị từ chối', 'noi_dung' => 'Rất tiếc, đơn đặt phòng gym của Quý cư dân không được duyệt do vượt quá số lượt cho phép trong ngày. Vui lòng chọn khung giờ khác.'],

            // ===== Đơn đặt tiện ích đã hủy (3) =====
            ['tieu_de' => 'Đặt lịch hồ bơi đã hủy', 'noi_dung' => 'Lượt đặt lịch sử dụng hồ bơi của Quý cư dân đã được hủy theo yêu cầu.'],
            ['tieu_de' => 'Đặt lịch sân tennis đã hủy', 'noi_dung' => 'Lượt đặt sân tennis của Quý cư dân đã được hủy theo yêu cầu.'],
            ['tieu_de' => 'Đặt lịch phòng sinh hoạt cộng đồng đã hủy', 'noi_dung' => 'Lượt đặt phòng sinh hoạt cộng đồng của Quý cư dân đã được hủy theo yêu cầu.'],

            // ===== Có phản hồi yêu cầu (3) =====
            ['tieu_de' => 'Có phản hồi cho yêu cầu sửa chữa điện', 'noi_dung' => 'Ban quản lý đã phản hồi yêu cầu sửa chữa điện của Quý cư dân. Vui lòng vào mục Yêu cầu để xem chi tiết phản hồi.'],
            ['tieu_de' => 'Có phản hồi cho yêu cầu sửa chữa nước', 'noi_dung' => 'Ban quản lý đã phản hồi yêu cầu sửa chữa nước của Quý cư dân. Vui lòng vào mục Yêu cầu để xem chi tiết phản hồi.'],
            ['tieu_de' => 'Có phản hồi cho yêu cầu vệ sinh căn hộ', 'noi_dung' => 'Ban quản lý đã phản hồi yêu cầu vệ sinh căn hộ của Quý cư dân. Vui lòng vào mục Yêu cầu để xem chi tiết phản hồi.'],

            // ===== Yêu cầu đã tiếp nhận (3) =====
            ['tieu_de' => 'Yêu cầu sửa chữa căn hộ đã được tiếp nhận', 'noi_dung' => 'Yêu cầu sửa chữa căn hộ của Quý cư dân đã được tiếp nhận và đang được xử lý.'],
            ['tieu_de' => 'Yêu cầu hỗ trợ kỹ thuật đã được tiếp nhận', 'noi_dung' => 'Yêu cầu hỗ trợ kỹ thuật của Quý cư dân đã được tiếp nhận và đang được xử lý.'],
            ['tieu_de' => 'Yêu cầu vệ sinh căn hộ đã được tiếp nhận', 'noi_dung' => 'Yêu cầu vệ sinh căn hộ của Quý cư dân đã được tiếp nhận và đang được xử lý.'],

            // ===== Yêu cầu đã xử lý (2) =====
            ['tieu_de' => 'Yêu cầu sửa chữa căn hộ của bạn đã được xử lý', 'noi_dung' => 'Yêu cầu sửa chữa căn hộ của Quý cư dân đã được xử lý xong. Vui lòng phản hồi lại nếu vấn đề chưa được khắc phục hoàn toàn.'],
            ['tieu_de' => 'Yêu cầu hỗ trợ kỹ thuật của bạn đã được xử lý', 'noi_dung' => 'Yêu cầu hỗ trợ kỹ thuật của Quý cư dân đã được xử lý xong. Vui lòng phản hồi lại nếu vấn đề chưa được khắc phục hoàn toàn.'],

            // ===== Có bảng tin mới (2) =====
            ['tieu_de' => 'Có bảng tin mới từ Ban quản lý', 'noi_dung' => 'Ban quản lý vừa đăng một bảng tin mới. Mời Quý cư dân vào mục Bảng tin để xem chi tiết.'],
            ['tieu_de' => 'Bảng tin mới: Lịch bảo trì tháng 7/2026', 'noi_dung' => 'Ban quản lý vừa đăng bảng tin về lịch bảo trì tháng 7/2026. Mời Quý cư dân vào mục Bảng tin để xem chi tiết.'],

            // ===== Có tin tức mới (2) =====
            ['tieu_de' => 'Có tin tức mới đáng chú ý', 'noi_dung' => 'Chung cư vừa cập nhật một tin tức mới. Mời Quý cư dân theo dõi để nắm thông tin mới nhất.'],
            ['tieu_de' => 'Tin tức mới: Sự kiện cộng đồng tháng 8/2026', 'noi_dung' => 'Chung cư vừa cập nhật tin tức về sự kiện cộng đồng diễn ra trong tháng 8/2026. Mời Quý cư dân theo dõi để nắm thông tin chi tiết.'],

            // ===== Thông báo bảo trì (2) =====
            ['tieu_de' => 'Thông báo bảo trì thang máy tòa A', 'noi_dung' => 'Thang máy tòa A sẽ được bảo trì định kỳ trong hôm nay. Mong Quý cư dân thông cảm và sử dụng thang bộ trong thời gian bảo trì.'],
            ['tieu_de' => 'Thông báo bảo trì hệ thống PCCC tòa B', 'noi_dung' => 'Hệ thống phòng cháy chữa cháy tòa B sẽ được bảo trì định kỳ trong hôm nay. Mong Quý cư dân thông cảm nếu nghe tiếng chuông báo động thử.'],

            // ===== Thông báo cắt điện (2) =====
            ['tieu_de' => 'Thông báo cắt điện tòa A ngày 16/07/2026', 'noi_dung' => 'Điện lực khu vực sẽ cắt điện bảo trì lưới tại tòa A vào ngày 16/07/2026 từ 08:00 đến 16:00. Kính mong Quý cư dân chủ động sắp xếp sinh hoạt.'],
            ['tieu_de' => 'Thông báo cắt điện tòa B ngày 20/07/2026', 'noi_dung' => 'Điện lực khu vực sẽ cắt điện bảo trì lưới tại tòa B vào ngày 20/07/2026 từ 08:00 đến 16:00. Kính mong Quý cư dân chủ động sắp xếp sinh hoạt.'],

            // ===== Thông báo cắt nước (2) =====
            ['tieu_de' => 'Thông báo cắt nước tòa A ngày 15/07/2026', 'noi_dung' => 'Đơn vị cấp nước sẽ tạm ngừng cấp nước tại tòa A vào ngày 15/07/2026 từ 08:00 đến 17:00 để sửa chữa đường ống. Kính mong Quý cư dân chủ động trữ nước sử dụng.'],
            ['tieu_de' => 'Thông báo cắt nước tòa C ngày 22/07/2026', 'noi_dung' => 'Đơn vị cấp nước sẽ tạm ngừng cấp nước tại tòa C vào ngày 22/07/2026 từ 08:00 đến 17:00 để sửa chữa đường ống. Kính mong Quý cư dân chủ động trữ nước sử dụng.'],

            // ===== Thông báo diễn tập PCCC (2) =====
            ['tieu_de' => 'Thông báo diễn tập PCCC tòa A', 'noi_dung' => 'Ban quản lý tổ chức diễn tập phòng cháy chữa cháy tại tòa A trong tuần này. Kính mời Quý cư dân tham gia và làm quen với kỹ năng thoát hiểm.'],
            ['tieu_de' => 'Thông báo diễn tập PCCC tòa B', 'noi_dung' => 'Ban quản lý tổ chức diễn tập phòng cháy chữa cháy tại tòa B trong tuần này. Kính mời Quý cư dân tham gia và làm quen với kỹ năng thoát hiểm.'],

            // ===== Thông báo nghỉ lễ (2) =====
            ['tieu_de' => 'Thông báo nghỉ lễ Quốc khánh 2/9', 'noi_dung' => 'Văn phòng Ban quản lý tạm nghỉ nhân dịp lễ Quốc khánh 2/9, bộ phận bảo vệ và kỹ thuật vẫn trực đầy đủ để đảm bảo an ninh cho cư dân.'],
            ['tieu_de' => 'Thông báo nghỉ lễ Tết Dương lịch 2026', 'noi_dung' => 'Văn phòng Ban quản lý tạm nghỉ nhân dịp Tết Dương lịch 2026, bộ phận bảo vệ và kỹ thuật vẫn trực đầy đủ để đảm bảo an ninh cho cư dân.'],

            // ===== Nhắc cập nhật thông tin cá nhân (2) =====
            ['tieu_de' => 'Nhắc cập nhật thông tin cá nhân', 'noi_dung' => 'Vui lòng kiểm tra và cập nhật thông tin cá nhân, số điện thoại liên hệ trên hệ thống để Ban quản lý tiện liên lạc khi cần thiết.'],
            ['tieu_de' => 'Nhắc cập nhật thông tin CCCD', 'noi_dung' => 'Vui lòng kiểm tra và cập nhật số CCCD, ngày cấp trên hồ sơ cư dân để đảm bảo thông tin lưu trữ chính xác.'],

            // ===== Cảnh báo an ninh (2) =====
            ['tieu_de' => 'Cảnh báo an ninh khu vực bãi xe', 'noi_dung' => 'Ban quản lý ghi nhận có dấu hiệu bất thường tại khu vực bãi xe. Đề nghị Quý cư dân nâng cao cảnh giác, không để tài sản giá trị trong xe.'],
            ['tieu_de' => 'Cảnh báo phòng chống lừa đảo', 'noi_dung' => 'Gần đây xuất hiện các cuộc gọi giả mạo Ban quản lý để lừa đảo. Đề nghị Quý cư dân cảnh giác, không cung cấp thông tin cá nhân hoặc chuyển tiền cho người lạ.'],
        ];
    }
}
