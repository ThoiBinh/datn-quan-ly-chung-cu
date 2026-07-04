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
            ['tieu_de' => 'Thông báo lịch bảo trì thang máy tháng 7/2025', 'noi_dung' => 'Ban quản lý thông báo lịch bảo trì thang máy định kỳ vào ngày 05/07/2025 từ 8:00 đến 12:00. Thang máy tòa A tạm ngừng hoạt động. Kính mong cư dân thông cảm.'],
            ['tieu_de' => 'Khai trương khu vui chơi trẻ em tầng trệt', 'noi_dung' => 'Khu vui chơi trẻ em tầng trệt tòa A chính thức mở cửa từ 01/07/2025. Trang bị đầy đủ thiết bị vui chơi an toàn cho trẻ 3-12 tuổi. Giờ mở cửa: 7:00 - 20:00 hàng ngày.'],
            ['tieu_de' => 'Quy định mới về gửi xe từ tháng 7/2025', 'noi_dung' => 'Áp dụng thẻ từ cho khu vực hầm xe từ tháng 7/2025. Cư dân cần đăng ký thẻ từ tại văn phòng ban quản lý trước ngày 30/06/2025. Phí làm thẻ: 50.000đ/thẻ.'],
            ['tieu_de' => 'Thông báo cúp nước ngày 10/07/2025', 'noi_dung' => 'Do công tác sửa chữa hệ thống cấp nước, tòa B sẽ bị cúp nước từ 8:00 đến 17:00 ngày 10/07/2025. Ban quản lý đã chuẩn bị nước dự phòng tại tầng trệt.'],
            ['tieu_de' => 'Lịch vệ sinh hồ bơi tháng 7/2025', 'noi_dung' => 'Hồ bơi sẽ được vệ sinh và thay nước vào các ngày thứ Hai hàng tuần từ 7:00 đến 10:00. Trong thời gian vệ sinh, hồ bơi tạm thời đóng cửa.'],
            ['tieu_de' => 'Thông báo thu phí tháng 7/2025', 'noi_dung' => 'Thời hạn đóng phí dịch vụ tháng 7/2025 là ngày 15/07/2025. Cư dân có thể thanh toán trực tiếp tại văn phòng hoặc chuyển khoản. Quá hạn sẽ bị tính phí trễ hạn.'],
            ['tieu_de' => 'Hướng dẫn phân loại rác tại chung cư', 'noi_dung' => 'Phân loại rác theo 3 nhóm: Rác hữu cơ (túi xanh), rác tái chế (túi vàng), rác còn lại (túi đen). Các thùng rác phân loại đặt tại hành lang mỗi tầng.'],
            ['tieu_de' => 'Thông báo về quy định nuôi thú cưng', 'noi_dung' => 'Cư dân tuân thủ quy định nuôi thú cưng: Phải đăng ký với ban quản lý, không để thú cưng gây ồn sau 22:00, phải dọn vệ sinh khi thú cưng thải ra nơi công cộng.'],
            ['tieu_de' => 'Kết quả hội nghị cư dân năm 2025', 'noi_dung' => 'Hội nghị cư dân năm 2025 diễn ra thành công ngày 15/03/2025 với hơn 200 hộ dân tham gia. Các vấn đề được thông qua: điều chỉnh phí dịch vụ, kế hoạch nâng cấp hạ tầng.'],
            ['tieu_de' => 'Thông báo lịch phun diệt muỗi', 'noi_dung' => 'Phun diệt muỗi toàn bộ chung cư vào ngày 20/07/2025 từ 8:00 đến 17:00. Kính mong cư dân đóng cửa sổ và tạm thời rời khỏi căn hộ trong thời gian phun thuốc.'],
            ['tieu_de' => 'Tuyển dụng bảo vệ ca đêm', 'noi_dung' => 'Cần tuyển 2 bảo vệ ca đêm (22:00-6:00). Yêu cầu: Nam, 25-45 tuổi, sức khỏe tốt, có kinh nghiệm bảo vệ. Liên hệ nộp hồ sơ tại văn phòng ban quản lý.'],
            ['tieu_de' => 'Thông báo nâng cấp hệ thống camera an ninh', 'noi_dung' => 'Hệ thống camera an ninh sẽ được nâng cấp từ ngày 25-27/07/2025. Đội ngũ kỹ thuật sẽ đảm bảo an ninh trong suốt quá trình nâng cấp.'],
            ['tieu_de' => 'Kế hoạch sơn lại hành lang tháng 8/2025', 'noi_dung' => 'Ban quản lý sẽ tiến hành sơn lại toàn bộ hành lang các tầng từ ngày 01-15/08/2025. Cư dân lưu ý tránh để đồ vật ở hành lang trong thời gian thi công.'],
            ['tieu_de' => 'Thông báo kiểm tra phòng cháy chữa cháy', 'noi_dung' => 'Đội PCCC sẽ kiểm tra hệ thống phòng cháy chữa cháy vào ngày 12/07/2025. Kính mong cư dân tạo điều kiện cho đội kiểm tra vào căn hộ từ 8:00 đến 17:00.'],
            ['tieu_de' => 'Lễ tri ân cư dân nhân dịp kỷ niệm 5 năm thành lập', 'noi_dung' => 'Nhân dịp kỷ niệm 5 năm thành lập, ban quản lý tổ chức lễ tri ân cư dân vào ngày 20/08/2025 tại sảnh tầng trệt tòa A. Kính mời toàn thể cư dân tham dự.'],
            ['tieu_de' => 'Thông báo nâng cấp hệ thống điện tháng 7', 'noi_dung' => 'Hệ thống điện tòa C sẽ được nâng cấp vào ngày 08/07/2025 từ 9:00-15:00. Điện sẽ bị cắt trong thời gian này. Ban quản lý xin lỗi vì sự bất tiện.'],
            ['tieu_de' => 'Mở đăng ký lớp học bơi cho cư dân', 'noi_dung' => 'Ban quản lý phối hợp với HLV chuyên nghiệp mở lớp học bơi dành cho cư dân và con em từ tháng 8/2025. Học phí ưu đãi cho cư dân chung cư. Đăng ký tại lễ tân.'],
            ['tieu_de' => 'Thông báo đổi thẻ cư dân mới', 'noi_dung' => 'Từ ngày 01/08/2025, ban quản lý sẽ đổi toàn bộ thẻ cư dân sang thẻ chip mới. Cư dân mang thẻ cũ đến văn phòng trong giờ hành chính để đổi thẻ mới miễn phí.'],
            ['tieu_de' => 'Cảnh báo lừa đảo qua điện thoại', 'noi_dung' => 'Ban quản lý cảnh báo cư dân về các cuộc gọi giả mạo ban quản lý để lừa đảo chiếm đoạt tài sản. Ban quản lý không bao giờ yêu cầu chuyển tiền qua điện thoại.'],
            ['tieu_de' => 'Thông báo sửa chữa bể chứa nước tầng mái', 'noi_dung' => 'Bể chứa nước tầng mái tòa B sẽ được vệ sinh và sửa chữa vào ngày 15/07/2025 từ 6:00-12:00. Nước sinh hoạt có thể bị gián đoạn trong thời gian này.'],
            ['tieu_de' => 'Ra mắt ứng dụng quản lý chung cư', 'noi_dung' => 'Ban quản lý vui mừng thông báo ra mắt ứng dụng di động quản lý chung cư Urbano. Cư dân có thể thanh toán phí, gửi yêu cầu và nhận thông báo qua app.'],
            ['tieu_de' => 'Thông báo tắt thang máy để bảo dưỡng tháng 8', 'noi_dung' => 'Thang máy số 2 tòa A sẽ tắt để bảo dưỡng định kỳ vào ngày 03/08/2025 từ 8:00-16:00. Thang máy số 1 vẫn hoạt động bình thường.'],
            ['tieu_de' => 'Hướng dẫn đăng ký khách lưu trú', 'noi_dung' => 'Theo quy định, cư dân có khách lưu trú trên 3 ngày phải đăng ký tại văn phòng ban quản lý hoặc qua ứng dụng. Thủ tục đơn giản, chỉ cần CCCD của khách.'],
            ['tieu_de' => 'Thông báo thu gom đồ cũ từ thiện', 'noi_dung' => 'Ban quản lý phối hợp với tổ chức từ thiện tổ chức thu gom quần áo, sách vở, đồ dùng cũ để tặng cho trẻ em vùng khó khăn. Điểm thu nhận tại sảnh tầng trệt từ 15-20/07.'],
            ['tieu_de' => 'Lịch kiểm tra đồng hồ điện nước tháng 7', 'noi_dung' => 'Đội kỹ thuật sẽ kiểm tra và ghi chỉ số đồng hồ điện nước vào ngày 25/07/2025. Kính mong cư dân tạo điều kiện cho nhân viên vào ghi chỉ số.'],
            ['tieu_de' => 'Thông báo về giờ yên tĩnh trong chung cư', 'noi_dung' => 'Nhắc nhở cư dân tuân thủ giờ yên tĩnh từ 22:00-7:00. Không tổ chức tiệc, âm nhạc to, sửa chữa ồn ào trong khung giờ này. Vi phạm sẽ bị xử phạt theo quy định.'],
            ['tieu_de' => 'Khai giảng lớp học yoga cho cư dân', 'noi_dung' => 'Lớp học yoga dành cho cư dân khai giảng ngày 01/08/2025 tại phòng sinh hoạt cộng đồng tầng 2. Học 3 buổi/tuần, học phí ưu đãi. Đăng ký tại lễ tân.'],
            ['tieu_de' => 'Thông báo về việc thay thế bóng đèn hành lang', 'noi_dung' => 'Đội kỹ thuật sẽ tiến hành thay thế toàn bộ bóng đèn hành lang sang đèn LED tiết kiệm điện từ ngày 10-20/07/2025. Công việc tiến hành từng tầng.'],
            ['tieu_de' => 'Thông báo bàn giao căn hộ đợt mới', 'noi_dung' => 'Ban quản lý thông báo lịch bàn giao căn hộ đợt 3 từ ngày 01-05/08/2025. Cư dân nhận bàn giao vui lòng mang đầy đủ giấy tờ và liên hệ lễ tân để sắp xếp lịch.'],
            ['tieu_de' => 'Hội thảo phòng chống dịch bệnh mùa hè', 'noi_dung' => 'Ban quản lý phối hợp với trung tâm y tế tổ chức hội thảo phòng chống dịch bệnh mùa hè vào ngày 18/07/2025 lúc 9:00 tại hội trường tầng 3. Miễn phí tham dự.'],
            ['tieu_de' => 'Thông báo kiểm tra PCCC hàng năm', 'noi_dung' => 'Theo quy định, hệ thống PCCC toàn chung cư sẽ được kiểm tra định kỳ vào ngày 22/07/2025. Cư dân vui lòng không khóa cửa trong khung giờ kiểm tra 8:00-17:00.'],
            ['tieu_de' => 'Kết quả bình chọn cư dân tiêu biểu quý 2/2025', 'noi_dung' => 'Ban quản lý công bố kết quả bình chọn cư dân tiêu biểu quý 2/2025. Danh sách 5 cư dân được ghi nhận vì đóng góp tích cực cho cộng đồng chung cư.'],
            ['tieu_de' => 'Mở đăng ký xe mùa mưa', 'noi_dung' => 'Do mùa mưa sắp đến, ban quản lý mở thêm 50 chỗ để xe có mái che ưu tiên cho xe máy. Phí tăng thêm 50.000đ/tháng. Đăng ký tại văn phòng.'],
            ['tieu_de' => 'Thông báo sơn lại bãi đỗ xe hầm B1', 'noi_dung' => 'Hầm đỗ xe B1 sẽ được sơn lại vạch kẻ từ ngày 28-30/07/2025. Trong thời gian này, xe gửi tạm tại bãi trên mặt đất. Kính mong cư dân thông cảm.'],
            ['tieu_de' => 'Ưu đãi phí quản lý cho cư dân đóng 6 tháng', 'noi_dung' => 'Cư dân đóng phí quản lý 6 tháng một lần sẽ được giảm 5% tổng phí. Ưu đãi áp dụng từ tháng 8/2025. Liên hệ kế toán để biết thêm chi tiết.'],
            ['tieu_de' => 'Thông báo cải tạo sân chơi ngoài trời', 'noi_dung' => 'Sân chơi ngoài trời sẽ được cải tạo và nâng cấp từ ngày 01-15/08/2025. Trong thời gian thi công, cư dân vui lòng không đưa trẻ em vào khu vực công trường.'],
            ['tieu_de' => 'Hướng dẫn sử dụng phòng sinh hoạt cộng đồng', 'noi_dung' => 'Cư dân có nhu cầu sử dụng phòng sinh hoạt cộng đồng cho sự kiện cá nhân vui lòng đăng ký trước 3 ngày tại lễ tân. Phí thuê phòng: 500.000đ/buổi 4 tiếng.'],
            ['tieu_de' => 'Thông báo về việc lắp đặt máy ATM trong chung cư', 'noi_dung' => 'Ban quản lý đã ký kết hợp đồng với Vietcombank lắp đặt máy ATM tại sảnh tầng trệt tòa B. Máy ATM dự kiến hoạt động từ ngày 15/08/2025.'],
            ['tieu_de' => 'Kế hoạch trồng cây xanh mùa hè 2025', 'noi_dung' => 'Ban quản lý sẽ trồng thêm 50 cây xanh xung quanh khuôn viên chung cư vào ngày 05/08/2025. Cư dân có thể đăng ký tham gia chương trình trồng cây cùng ban quản lý.'],
            ['tieu_de' => 'Thông báo giờ mở cửa hồ bơi mùa hè', 'noi_dung' => 'Từ tháng 7-9/2025, hồ bơi mở cửa từ 5:30-21:00 (tăng thêm 1 tiếng so với bình thường). Cư dân mang thẻ cư dân để vào cổng miễn phí.'],
            ['tieu_de' => 'Cảnh báo an ninh: Không để đồ có giá trị trong xe', 'noi_dung' => 'Ban an ninh cảnh báo cư dân không để đồ có giá trị trong xe ở bãi đỗ xe công cộng. Chung cư không chịu trách nhiệm với tài sản để lại trong xe.'],
            ['tieu_de' => 'Thông báo lắp thêm camera tại các cổng', 'noi_dung' => 'Hệ thống camera tại các cổng ra vào sẽ được nâng cấp và lắp thêm camera góc rộng. Công việc thực hiện từ ngày 18-20/07/2025 mà không ảnh hưởng lưu thông.'],
            ['tieu_de' => 'Mời cư dân tham gia câu lạc bộ đọc sách', 'noi_dung' => 'Ban quản lý thành lập câu lạc bộ đọc sách dành cho cư dân, sinh hoạt mỗi tháng 2 buổi tại thư viện mini tầng 3. Cư dân yêu sách đăng ký tham gia.'],
            ['tieu_de' => 'Thông báo về quy định trang trí ban công', 'noi_dung' => 'Nhắc nhở cư dân không tự ý lắp mái che, lồng sắt hoặc thay đổi kết cấu ban công. Các vi phạm sẽ yêu cầu tháo dỡ và chịu phạt theo quy chế chung cư.'],
            ['tieu_de' => 'Thông báo kiểm tra thang thoát hiểm', 'noi_dung' => 'Thang thoát hiểm toàn bộ 3 tòa nhà sẽ được kiểm tra và vệ sinh vào ngày 07/08/2025. Kính mong cư dân không xếp đồ đạc, xe cộ ở cầu thang thoát hiểm.'],
            ['tieu_de' => 'Giới thiệu dịch vụ giặt ủi tiện ích tại chung cư', 'noi_dung' => 'Từ tháng 8/2025, chung cư hợp tác với chuỗi giặt ủi Cleanly cung cấp dịch vụ nhận-giao đồ tại nhà. Cư dân đặt lịch qua ứng dụng và nhận ưu đãi 20% tháng đầu.'],
            ['tieu_de' => 'Thông báo họp ban đại diện cư dân tháng 8', 'noi_dung' => 'Cuộc họp ban đại diện cư dân tháng 8/2025 sẽ diễn ra vào ngày 10/08/2025 lúc 14:00 tại phòng họp tầng 3. Các cư dân quan tâm có thể tham dự với tư cách quan sát viên.'],
            ['tieu_de' => 'Thông báo về việc cấm đốt nhang tại hành lang', 'noi_dung' => 'Ban quản lý nhắc nhở cư dân không đốt nhang, vàng mã tại hành lang, cầu thang. Chỉ được thực hiện bên trong căn hộ và đảm bảo thông gió tốt để tránh kích hoạt báo khói.'],
            ['tieu_de' => 'Chương trình khuyến mãi phí internet tháng 8', 'noi_dung' => 'Cư dân đăng ký gói internet trong tháng 8/2025 sẽ được miễn phí tháng đầu. Liên hệ lễ tân để đăng ký gói cước phù hợp.'],
            ['tieu_de' => 'Thông báo lịch cắt cỏ và chăm sóc cây xanh', 'noi_dung' => 'Đội chăm sóc cây xanh sẽ cắt cỏ và tỉa cây toàn khuôn viên vào các ngày thứ Bảy hàng tuần từ 7:00-11:00. Kính mong cư dân không đậu xe trong khu vực thi công.'],
            ['tieu_de' => 'Kết quả khảo sát sự hài lòng của cư dân 6 tháng đầu năm 2025', 'noi_dung' => 'Ban quản lý công bố kết quả khảo sát: 87% cư dân hài lòng với dịch vụ quản lý. Các vấn đề cần cải thiện: tốc độ xử lý yêu cầu và vệ sinh hầm xe.'],
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
