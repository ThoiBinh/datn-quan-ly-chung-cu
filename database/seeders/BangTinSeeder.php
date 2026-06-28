<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BangTinSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('nhan_vien')->where('email', 'admin@chungcu.vn')->value('id') ?? 1;

        $data = [
            ['tieu_de' => 'Thông báo lịch bảo trì thang máy tháng 7/2025', 'noi_dung' => 'Ban quản lý trân trọng thông báo lịch bảo trì thang máy định kỳ sẽ diễn ra vào ngày 05/07/2025 từ 8:00 đến 12:00. Trong thời gian này, thang máy tòa A sẽ tạm ngừng hoạt động. Kính mong cư dân thông cảm và sắp xếp thời gian hợp lý.'],
            ['tieu_de' => 'Khai trương khu vui chơi trẻ em tầng trệt', 'noi_dung' => 'Khu vui chơi trẻ em tầng trệt tòa A chính thức mở cửa từ 01/07/2025. Khu vui chơi được trang bị đầy đủ thiết bị vui chơi an toàn cho trẻ em từ 3-12 tuổi. Giờ mở cửa: 7:00 - 20:00 hàng ngày.'],
            ['tieu_de' => 'Quy định mới về gửi xe từ tháng 7/2025', 'noi_dung' => 'Áp dụng thẻ từ cho khu vực hầm xe từ tháng 7/2025. Cư dân cần đăng ký thẻ từ tại văn phòng ban quản lý trước ngày 30/06/2025. Phí làm thẻ: 50.000đ/thẻ. Liên hệ lễ tân để biết thêm thông tin.'],
            ['tieu_de' => 'Thông báo cúp nước ngày 10/07/2025', 'noi_dung' => 'Do công tác sửa chữa hệ thống cấp nước, khu vực tòa B sẽ bị cúp nước từ 8:00 đến 17:00 ngày 10/07/2025. Ban quản lý đã chuẩn bị nước dự phòng tại tầng trệt. Kính mong cư dân thông cảm.'],
            ['tieu_de' => 'Lịch vệ sinh hồ bơi tháng 7/2025', 'noi_dung' => 'Hồ bơi sẽ được vệ sinh và thay nước vào các ngày thứ Hai hàng tuần từ 7:00 đến 10:00. Trong thời gian vệ sinh, hồ bơi tạm thời đóng cửa. Kính mong cư dân lưu ý.'],
            ['tieu_de' => 'Thông báo thu phí tháng 7/2025', 'noi_dung' => 'Ban quản lý thông báo thời hạn đóng phí dịch vụ tháng 7/2025 là ngày 15/07/2025. Cư dân có thể thanh toán trực tiếp tại văn phòng hoặc chuyển khoản theo thông tin đã cung cấp. Quá hạn sẽ bị tính phí trễ hạn.'],
            ['tieu_de' => 'Hướng dẫn phân loại rác tại chung cư', 'noi_dung' => 'Nhằm bảo vệ môi trường, ban quản lý yêu cầu cư dân phân loại rác thải theo 3 nhóm: Rác hữu cơ (túi xanh), rác tái chế (túi vàng), rác còn lại (túi đen). Các thùng rác phân loại đặt tại hành lang mỗi tầng.'],
            ['tieu_de' => 'Thông báo về quy định nuôi thú cưng', 'noi_dung' => 'Ban quản lý nhắc nhở cư dân tuân thủ quy định nuôi thú cưng: Phải đăng ký với ban quản lý, không để thú cưng gây ồn ào sau 22:00, phải dọn vệ sinh ngay khi thú cưng thải ra nơi công cộng.'],
            ['tieu_de' => 'Kết quả hội nghị cư dân năm 2025', 'noi_dung' => 'Hội nghị cư dân năm 2025 đã diễn ra thành công ngày 15/03/2025 với sự tham gia của hơn 200 hộ dân. Các vấn đề được thông qua bao gồm: điều chỉnh phí dịch vụ, kế hoạch nâng cấp hạ tầng và bầu ban đại diện cư dân nhiệm kỳ 2025-2027.'],
            ['tieu_de' => 'Thông báo lịch phun diệt muỗi', 'noi_dung' => 'Ban quản lý sẽ tổ chức phun diệt muỗi toàn bộ chung cư vào ngày 20/07/2025 từ 8:00 đến 17:00. Kính mong cư dân đóng cửa sổ và tạm thời rời khỏi căn hộ trong thời gian phun thuốc để đảm bảo an toàn sức khỏe.'],
            ['tieu_de' => 'Tuyển dụng bảo vệ ca đêm', 'noi_dung' => 'Ban quản lý cần tuyển 2 bảo vệ ca đêm (22:00-6:00). Yêu cầu: Nam, 25-45 tuổi, sức khỏe tốt, có kinh nghiệm bảo vệ. Liên hệ nộp hồ sơ tại văn phòng ban quản lý trong giờ hành chính.'],
            ['tieu_de' => 'Thông báo nâng cấp hệ thống camera an ninh', 'noi_dung' => 'Hệ thống camera an ninh toàn chung cư sẽ được nâng cấp từ ngày 25-27/07/2025. Trong thời gian thi công có thể ảnh hưởng đến hành lang một số tầng. Đội ngũ kỹ thuật sẽ hỗ trợ đảm bảo an ninh trong suốt quá trình nâng cấp.'],
        ];

        foreach ($data as $item) {
            if (!DB::table('bang_tin')->where('tieu_de', $item['tieu_de'])->exists()) {
                DB::table('bang_tin')->insert(array_merge($item, [
                    'nguoi_tao'      => $adminId,
                    'nguoi_cap_nhat' => $adminId,
                    'createdAt'      => now(),
                    'updatedAt'      => now(),
                ]));
            }
        }
    }
}
