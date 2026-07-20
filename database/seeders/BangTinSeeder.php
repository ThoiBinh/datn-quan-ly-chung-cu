<?php

namespace Database\Seeders;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BangTinSeeder extends Seeder
{
    /**
     * Sinh 50 bản ghi bảng tin mẫu (5 chủ đề x 10 bài: bảo trì, sự kiện, thông báo, an ninh, tiện ích).
     * Idempotent: dùng updateOrInsert theo tieu_de, không truncate/delete dữ liệu cũ.
     */
    public function run(): void
    {
        $faker = FakerFactory::create('vi_VN');

        // Danh sách nhân viên hợp lệ để gán người đăng/người cập nhật (khóa ngoại nhan_vien.id)
        $nhanVienIds = DB::table('nhan_vien')->pluck('id')->all();
        if (empty($nhanVienIds)) {
            $nhanVienIds = [1];
        }

        $baiViet = $this->danhSachBaiViet();

        foreach ($baiViet as $item) {
            $noiDung = $this->buildNoiDung($item['mo_dau'], $item['thoi_gian']);

            $nguoiTao = $faker->randomElement($nhanVienIds);
            $nguoiCapNhat = $faker->randomElement($nhanVienIds);

            $createdAt = $faker->dateTimeBetween('-12 months', 'now');
            $updatedAt = $faker->dateTimeBetween($createdAt, 'now');

            // Ảnh minh họa random storage/app/public/bang-tin/bangtinNN.jpg (đúng thư mục lưu ảnh của BangTinController)
            $soThuTuAnh = str_pad((string) $faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT);
            $hinhUrl = 'bang-tin/bangtin' . $soThuTuAnh . '.jpg';

            DB::table('bang_tin')->updateOrInsert(
                ['tieu_de' => $item['tieu_de']],
                [
                    'noi_dung'       => $noiDung,
                    'hinh_url'       => $hinhUrl,
                    'nguoi_tao'      => $nguoiTao,
                    'nguoi_cap_nhat' => $nguoiCapNhat,
                    'createdAt'      => $createdAt,
                    'updatedAt'      => $updatedAt,
                ]
            );
        }
    }

    /**
     * Ghép nội dung bài viết theo cấu trúc chuẩn: lời chào - nội dung chi tiết - thời gian - đề nghị phối hợp - lời cảm ơn.
     */
    private function buildNoiDung(string $moDau, string $thoiGian): string
    {
        return implode("\n\n", [
            'Kính gửi Quý cư dân,',
            $moDau,
            'Thời gian thực hiện: ' . $thoiGian,
            'Kính đề nghị Quý cư dân phối hợp, sắp xếp thời gian và tuân thủ hướng dẫn của Ban Quản lý để công việc trên diễn ra thuận lợi, đảm bảo an toàn và quyền lợi chung của toàn thể cư dân. Mọi thắc mắc xin liên hệ Ban Quản lý qua văn phòng tại sảnh tầng trệt hoặc tổng đài hỗ trợ cư dân.',
            "Xin chân thành cảm ơn sự hợp tác của Quý cư dân.\n\nTrân trọng,\nBan Quản lý chung cư",
        ]);
    }

    /**
     * 50 bài viết mẫu, chia đều 5 chủ đề (mỗi chủ đề 10 bài): bảo trì, sự kiện, thông báo, an ninh, tiện ích.
     * Tiêu đề không trùng, nội dung chi tiết khác nhau giữa các bài.
     *
     * @return array<int, array{tieu_de: string, mo_dau: string, thoi_gian: string}>
     */
    private function danhSachBaiViet(): array
    {
        return [
            // ===== BẢO TRÌ (10 bài) =====
            [
                'tieu_de' => 'Thông báo bảo trì thang máy Block A',
                'mo_dau' => 'Ban Quản lý xin thông báo sẽ tiến hành bảo trì định kỳ hệ thống thang máy tại Block A nhằm đảm bảo an toàn vận hành. Trong quá trình bảo trì, đơn vị kỹ thuật sẽ kiểm tra cáp treo, hệ thống phanh, bo mạch điều khiển và vệ sinh cabin thang máy. Một thang máy sẽ tạm ngừng hoạt động, thang còn lại vẫn phục vụ bình thường để hạn chế ảnh hưởng đến sinh hoạt của cư dân.',
                'thoi_gian' => 'từ 08:00 đến 12:00 ngày 08/07/2026.',
            ],
            [
                'tieu_de' => 'Bảo trì hệ thống PCCC định kỳ Quý III',
                'mo_dau' => 'Nhằm đảm bảo hệ thống phòng cháy chữa cháy luôn trong trạng thái sẵn sàng hoạt động, Ban Quản lý phối hợp cùng đơn vị chuyên trách tiến hành bảo trì, kiểm định các thiết bị báo cháy, bình chữa cháy, hệ thống bơm nước chữa cháy và van xả tại các tầng. Trong thời gian bảo trì có thể phát sinh tiếng chuông báo động thử, kính mong cư dân bình tĩnh, không hoang mang.',
                'thoi_gian' => 'từ 08:00 đến 17:00 ngày 14/07/2026, thực hiện lần lượt từng tầng.',
            ],
            [
                'tieu_de' => 'Bảo trì máy phát điện dự phòng',
                'mo_dau' => 'Ban Quản lý thông báo lịch chạy thử và bảo trì máy phát điện dự phòng nhằm đảm bảo nguồn điện thay thế hoạt động ổn định khi xảy ra sự cố mất điện lưới. Trong thời gian chạy thử, cư dân có thể nghe thấy tiếng máy nổ tại khu vực tầng hầm B2, đây là hoạt động bình thường trong quy trình bảo trì.',
                'thoi_gian' => 'từ 09:00 đến 10:30 ngày 10/07/2026.',
            ],
            [
                'tieu_de' => 'Bảo trì bãi đỗ xe hầm B1',
                'mo_dau' => 'Ban Quản lý sẽ tiến hành sơn kẻ lại vạch phân làn, kiểm tra hệ thống chiếu sáng và barrier tại bãi đỗ xe hầm B1. Trong thời gian thi công, khu vực này sẽ tạm ngừng nhận xe, cư dân vui lòng gửi xe tạm tại hầm B2 hoặc bãi xe ngoài trời theo hướng dẫn của bảo vệ.',
                'thoi_gian' => 'từ 22:00 ngày 12/07/2026 đến 05:00 ngày 13/07/2026.',
            ],
            [
                'tieu_de' => 'Sửa chữa đường nội khu phía sau Block C',
                'mo_dau' => 'Do mặt đường nội khu phía sau Block C xuất hiện một số điểm lún nứt, Ban Quản lý sẽ tiến hành sửa chữa, trải nhựa lại để đảm bảo an toàn giao thông trong khuôn viên. Trong thời gian thi công, xe cộ vui lòng di chuyển theo lối đi tạm được bố trí biển báo hướng dẫn.',
                'thoi_gian' => 'từ 07:00 đến 17:00 các ngày 15-17/07/2026.',
            ],
            [
                'tieu_de' => 'Vệ sinh và khử khuẩn hành lang các tầng',
                'mo_dau' => 'Đội vệ sinh sẽ thực hiện tổng vệ sinh, lau sàn và khử khuẩn toàn bộ hành lang các tầng thuộc ba tòa nhà nhằm đảm bảo môi trường sống sạch sẽ. Kính mong cư dân không để giày dép, vật dụng cá nhân ngoài hành lang trong thời gian vệ sinh để thuận tiện cho công tác thi công.',
                'thoi_gian' => 'từ 08:00 đến 11:00 vào thứ Ba hàng tuần.',
            ],
            [
                'tieu_de' => 'Vệ sinh tầng hầm định kỳ tháng 7',
                'mo_dau' => 'Ban Quản lý thông báo lịch vệ sinh, quét dọn và xử lý mùi tại khu vực tầng hầm B1, B2 nhằm đảm bảo không gian đỗ xe luôn sạch sẽ, thoáng mát. Trong thời gian vệ sinh, một số vị trí đỗ xe sẽ tạm thời được rào chắn, kính mong cư dân phối hợp gửi xe đúng vị trí được hướng dẫn.',
                'thoi_gian' => 'từ 06:00 đến 08:00 ngày 20/07/2026.',
            ],
            [
                'tieu_de' => 'Bảo trì hệ thống cấp thoát nước',
                'mo_dau' => 'Nhằm khắc phục tình trạng thoát nước chậm tại một số khu vực, đơn vị kỹ thuật sẽ kiểm tra, thông tắc và bảo trì hệ thống ống cấp thoát nước toàn chung cư. Nước sinh hoạt có thể yếu hơn bình thường trong thời gian thi công, cư dân nên chủ động trữ nước sử dụng.',
                'thoi_gian' => 'từ 08:00 đến 16:00 ngày 18/07/2026.',
            ],
            [
                'tieu_de' => 'Bảo trì hệ thống điện chung cư',
                'mo_dau' => 'Ban Quản lý phối hợp đơn vị điện lực kiểm tra, siết lại các mối nối tủ điện tổng và thay thế thiết bị bảo vệ đã xuống cấp nhằm đảm bảo an toàn cung cấp điện. Điện khu vực hành lang, thang máy và tiện ích chung có thể bị gián đoạn tạm thời trong quá trình thi công.',
                'thoi_gian' => 'từ 09:00 đến 15:00 ngày 22/07/2026.',
            ],
            [
                'tieu_de' => 'Cắt tỉa và chăm sóc cây xanh khuôn viên',
                'mo_dau' => 'Đội chăm sóc cảnh quan sẽ tiến hành cắt tỉa cành, làm cỏ và bón phân cho toàn bộ cây xanh, thảm cỏ trong khuôn viên chung cư nhằm giữ gìn mỹ quan xanh - sạch - đẹp. Trong thời gian thi công, một số lối đi bộ có thể tạm thời bị chắn, kính mong cư dân đi theo lối chỉ dẫn.',
                'thoi_gian' => 'từ 07:00 đến 10:00 thứ Bảy hàng tuần trong tháng 7/2026.',
            ],

            // ===== SỰ KIỆN (10 bài) =====
            [
                'tieu_de' => 'Chương trình Trung thu yêu thương 2026',
                'mo_dau' => 'Ban Quản lý phối hợp Ban đại diện cư dân tổ chức chương trình Trung thu yêu thương dành cho các em thiếu nhi trong chung cư với các hoạt động rước đèn, phá cỗ, múa lân và trao quà. Chương trình được tổ chức tại sân sinh hoạt cộng đồng tầng trệt, kính mời quý phụ huynh đưa các bé đến tham dự.',
                'thoi_gian' => '18:00 ngày 25/09/2026 tại sân sinh hoạt cộng đồng.',
            ],
            [
                'tieu_de' => 'Ngày hội Quốc tế Thiếu nhi 1/6 cho các bé',
                'mo_dau' => 'Nhân dịp Quốc tế Thiếu nhi 1/6, Ban Quản lý tổ chức ngày hội vui chơi với các trò chơi dân gian, vẽ tranh, tặng quà cho các bé thiếu nhi đang sinh sống tại chung cư. Chương trình miễn phí tham dự, kính mời quý cư dân đăng ký cho con em tại văn phòng Ban Quản lý.',
                'thoi_gian' => '08:00 đến 11:00 ngày 01/06/2026.',
            ],
            [
                'tieu_de' => 'Thông báo lịch nghỉ Tết Nguyên đán 2026',
                'mo_dau' => 'Ban Quản lý thông báo lịch nghỉ Tết Nguyên đán của bộ phận văn phòng, kế toán; riêng bộ phận bảo vệ, kỹ thuật và vệ sinh vẫn bố trí trực đầy đủ để đảm bảo an ninh, an toàn cho cư dân trong suốt kỳ nghỉ. Mọi yêu cầu khẩn cấp xin liên hệ số hotline trực Tết được dán tại bảng thông báo mỗi tòa.',
                'thoi_gian' => 'từ ngày 15/02/2026 đến hết ngày 21/02/2026.',
            ],
            [
                'tieu_de' => 'Thông báo lịch nghỉ Tết Dương lịch 2026',
                'mo_dau' => 'Ban Quản lý thông báo lịch nghỉ Tết Dương lịch của khối văn phòng hành chính. Trong thời gian nghỉ, bộ phận bảo vệ và kỹ thuật vẫn trực 24/24 để xử lý các tình huống phát sinh, đảm bảo an ninh trật tự và vận hành các hệ thống kỹ thuật của tòa nhà.',
                'thoi_gian' => 'ngày 01/01/2026, văn phòng làm việc trở lại từ 02/01/2026.',
            ],
            [
                'tieu_de' => 'Chương trình chào mừng Quốc khánh 2/9',
                'mo_dau' => 'Nhân dịp kỷ niệm Quốc khánh nước Cộng hòa Xã hội Chủ nghĩa Việt Nam, Ban Quản lý tổ chức treo cờ Tổ quốc tại các sảnh tòa nhà và khuôn viên chung cư. Kính mong Quý cư dân cùng hưởng ứng treo cờ tại ban công căn hộ để tạo không khí trang trọng, ý nghĩa.',
                'thoi_gian' => 'từ ngày 30/08/2026 đến hết ngày 03/09/2026.',
            ],
            [
                'tieu_de' => 'Kỷ niệm ngày Giải phóng miền Nam 30/4',
                'mo_dau' => 'Nhân dịp kỷ niệm ngày Giải phóng miền Nam, thống nhất đất nước 30/4, Ban Quản lý tổ chức treo cờ Tổ quốc và trang trí khuôn viên chung cư. Đồng thời, lịch nghỉ lễ của khối văn phòng cũng được thông báo để cư dân chủ động sắp xếp công việc liên quan đến thủ tục hành chính tại văn phòng Ban Quản lý.',
                'thoi_gian' => 'nghỉ từ ngày 30/04/2026 đến hết ngày 01/05/2026.',
            ],
            [
                'tieu_de' => 'Tọa đàm chào mừng ngày Quốc tế Phụ nữ 8/3',
                'mo_dau' => 'Nhân ngày Quốc tế Phụ nữ 8/3, Ban Quản lý phối hợp Ban đại diện cư dân tổ chức buổi gặp mặt, tặng hoa và quà tri ân đến các cư dân nữ, nhân viên nữ đang sinh sống và làm việc tại chung cư. Chương trình có tiết mục văn nghệ và tiệc trà nhẹ tại sảnh cộng đồng.',
                'thoi_gian' => '17:30 ngày 08/03/2026 tại sảnh sinh hoạt cộng đồng tầng trệt.',
            ],
            [
                'tieu_de' => 'Thông báo mở bán ki-ốt thương mại tầng trệt',
                'mo_dau' => 'Ban Quản lý thông báo chương trình mở bán, cho thuê các ki-ốt kinh doanh tại khu vực tầng trệt Block A phục vụ nhu cầu mua sắm, dịch vụ tiện ích của cư dân. Quý cư dân và các đơn vị có nhu cầu thuê ki-ốt vui lòng liên hệ văn phòng Ban Quản lý để được tư vấn diện tích, giá thuê và các điều khoản hợp đồng.',
                'thoi_gian' => 'nhận đăng ký từ ngày 20/07/2026 đến hết ngày 20/08/2026.',
            ],
            [
                'tieu_de' => 'Hội nghị cư dân thường niên năm 2026',
                'mo_dau' => 'Ban Quản lý trân trọng thông báo tổ chức Hội nghị cư dân thường niên năm 2026 nhằm báo cáo tình hình hoạt động, thu chi quỹ bảo trì và lấy ý kiến đóng góp của cư dân về công tác quản lý vận hành tòa nhà trong thời gian tới. Kính mời đại diện các hộ gia đình tham dự đông đủ.',
                'thoi_gian' => '08:30 ngày 26/07/2026 tại hội trường tầng 3.',
            ],
            [
                'tieu_de' => 'Ngày hội gia đình chung cư - Kết nối yêu thương',
                'mo_dau' => 'Nhằm tăng cường sự gắn kết giữa các cư dân trong cộng đồng, Ban Quản lý tổ chức Ngày hội gia đình với các hoạt động thể thao, trò chơi tập thể, ẩm thực và bốc thăm trúng thưởng. Chương trình dành cho mọi lứa tuổi, kính mời toàn thể cư dân cùng tham gia.',
                'thoi_gian' => '07:30 đến 11:00 ngày 09/08/2026 tại sân trung tâm.',
            ],

            // ===== THÔNG BÁO (10 bài) =====
            [
                'tieu_de' => 'Thông báo lịch cắt điện bảo trì lưới ngày 16/07',
                'mo_dau' => 'Theo thông báo từ Công ty Điện lực khu vực, lưới điện cấp cho chung cư sẽ tạm ngừng để phục vụ công tác bảo trì, sửa chữa đường dây. Ban Quản lý đã chuẩn bị phương án chạy máy phát điện dự phòng cho khu vực thang máy, chiếu sáng hành lang và bơm nước sinh hoạt trong thời gian mất điện.',
                'thoi_gian' => 'từ 08:00 đến 16:00 ngày 16/07/2026.',
            ],
            [
                'tieu_de' => 'Thông báo lịch cắt nước sửa chữa đường ống chính',
                'mo_dau' => 'Đơn vị cấp nước thông báo tạm ngừng cấp nước để sửa chữa đường ống chính dẫn vào khu vực chung cư. Ban Quản lý đã bố trí xe bồn nước dự phòng đặt tại sảnh tầng trệt mỗi tòa để phục vụ nhu cầu sinh hoạt thiết yếu của cư dân trong thời gian cắt nước.',
                'thoi_gian' => 'từ 08:00 đến 17:00 ngày 15/07/2026.',
            ],
            [
                'tieu_de' => 'Thông báo tuyển dụng nhân viên bảo vệ',
                'mo_dau' => 'Ban Quản lý cần tuyển bổ sung 02 nhân viên bảo vệ ca đêm, yêu cầu nam giới, độ tuổi từ 25 đến 45, sức khỏe tốt, ưu tiên có kinh nghiệm làm bảo vệ tại các tòa nhà chung cư, văn phòng. Ứng viên quan tâm vui lòng nộp hồ sơ trực tiếp tại văn phòng Ban Quản lý trong giờ hành chính.',
                'thoi_gian' => 'nhận hồ sơ đến hết ngày 31/07/2026.',
            ],
            [
                'tieu_de' => 'Thông báo tuyển dụng nhân viên vệ sinh',
                'mo_dau' => 'Ban Quản lý cần tuyển 03 nhân viên vệ sinh làm việc theo ca, phụ trách vệ sinh hành lang, sảnh và khu vực công cộng. Ưu tiên ứng viên có kinh nghiệm, chăm chỉ, trung thực. Mức lương thỏa thuận theo năng lực, có đóng bảo hiểm theo quy định. Liên hệ nộp hồ sơ tại văn phòng Ban Quản lý.',
                'thoi_gian' => 'phỏng vấn trực tiếp từ ngày 20/07/2026.',
            ],
            [
                'tieu_de' => 'Thông báo thay đổi giờ làm việc văn phòng Ban Quản lý',
                'mo_dau' => 'Kể từ tháng 8/2026, văn phòng Ban Quản lý điều chỉnh giờ làm việc để phục vụ cư dân tốt hơn vào buổi tối. Cụ thể, văn phòng mở cửa từ 08:00 đến 20:00 các ngày trong tuần, riêng Chủ nhật làm việc buổi sáng từ 08:00 đến 12:00. Kính mong cư dân sắp xếp thời gian đến liên hệ công việc phù hợp.',
                'thoi_gian' => 'áp dụng chính thức từ ngày 01/08/2026.',
            ],
            [
                'tieu_de' => 'Thông báo mở thêm tiện ích phòng đọc sách cộng đồng',
                'mo_dau' => 'Nhằm đa dạng hóa tiện ích phục vụ cư dân, Ban Quản lý đưa vào hoạt động phòng đọc sách cộng đồng tại tầng 3 Block B với không gian yên tĩnh, đầu sách phong phú dành cho mọi lứa tuổi. Cư dân có thể đến đọc sách tại chỗ hoặc mượn sách theo quy định của phòng đọc.',
                'thoi_gian' => 'mở cửa hàng ngày từ 08:00 đến 21:00, bắt đầu từ 25/07/2026.',
            ],
            [
                'tieu_de' => 'Hướng dẫn thủ tục và thời hạn đóng phí dịch vụ',
                'mo_dau' => 'Ban Quản lý thông báo thời hạn đóng phí dịch vụ, phí quản lý hàng tháng để cư dân chủ động thanh toán đúng hạn. Cư dân có thể thanh toán trực tiếp tại văn phòng, chuyển khoản theo thông tin tài khoản của Ban Quản lý hoặc thanh toán online qua ứng dụng quản lý chung cư. Quá hạn thanh toán sẽ áp dụng phí trễ hạn theo quy định.',
                'thoi_gian' => 'hạn chót đóng phí là ngày 10 hàng tháng.',
            ],
            [
                'tieu_de' => 'Thông báo lịch cắt tỉa và thay mới cây xanh lối vào',
                'mo_dau' => 'Một số cây xanh tại khu vực cổng chính đã già cỗi, ảnh hưởng đến mỹ quan và an toàn khi có mưa gió lớn. Ban Quản lý sẽ tiến hành cắt tỉa, thay thế bằng cây xanh mới phù hợp với cảnh quan chung cư. Trong thời gian thi công, lối vào chính có thể bị thu hẹp một phần.',
                'thoi_gian' => 'từ 07:00 đến 11:00 ngày 19/07/2026.',
            ],
            [
                'tieu_de' => 'Thông báo quy định nuôi và chăm sóc thú cưng',
                'mo_dau' => 'Nhằm đảm bảo vệ sinh và an toàn chung, Ban Quản lý nhắc lại quy định về việc nuôi thú cưng trong chung cư: cư dân phải đăng ký thông tin thú cưng với Ban Quản lý, rọ mõm hoặc dây xích khi di chuyển tại khu vực công cộng, không để thú cưng gây ồn sau 22 giờ và phải dọn vệ sinh nếu thú cưng phóng uế nơi công cộng.',
                'thoi_gian' => 'áp dụng ngay từ ngày ra thông báo.',
            ],
            [
                'tieu_de' => 'Thông báo lịch phun thuốc diệt côn trùng, muỗi định kỳ',
                'mo_dau' => 'Ban Quản lý phối hợp đơn vị chuyên môn tổ chức phun thuốc diệt muỗi, côn trùng gây hại tại khu vực hành lang, tầng hầm và khuôn viên cây xanh nhằm phòng chống dịch bệnh mùa mưa. Kính đề nghị cư dân đóng kín cửa sổ, không phơi thực phẩm ngoài ban công trong thời gian phun thuốc.',
                'thoi_gian' => 'từ 08:00 đến 17:00 ngày 23/07/2026.',
            ],

            // ===== AN NINH (10 bài) =====
            [
                'tieu_de' => 'Khuyến cáo phòng cháy chữa cháy mùa nắng nóng',
                'mo_dau' => 'Trước tình hình thời tiết nắng nóng kéo dài, nguy cơ chập cháy điện tăng cao, Ban Quản lý khuyến cáo cư dân kiểm tra hệ thống điện trong căn hộ, không cắm quá nhiều thiết bị vào cùng một ổ điện, tắt các thiết bị điện khi ra khỏi nhà và trang bị bình chữa cháy mini trong căn hộ để chủ động xử lý tình huống.',
                'thoi_gian' => 'khuyến cáo áp dụng thường xuyên, đặc biệt trong tháng 7-8/2026.',
            ],
            [
                'tieu_de' => 'Khuyến cáo phòng chống lừa đảo qua điện thoại, mạng xã hội',
                'mo_dau' => 'Thời gian gần đây xuất hiện nhiều đối tượng giả danh nhân viên Ban Quản lý, công ty điện lực, ngân hàng gọi điện, nhắn tin yêu cầu cư dân chuyển tiền hoặc cung cấp thông tin cá nhân. Ban Quản lý khẳng định không bao giờ yêu cầu cư dân chuyển khoản qua điện thoại. Kính đề nghị cư dân cảnh giác, không cung cấp mã OTP, thông tin tài khoản ngân hàng cho người lạ.',
                'thoi_gian' => 'khuyến cáo có hiệu lực thường xuyên.',
            ],
            [
                'tieu_de' => 'Cảnh báo tình trạng trộm cắp tài sản tại bãi xe',
                'mo_dau' => 'Ban Quản lý ghi nhận một số phản ánh về tình trạng mất cắp vặt đồ đạc để trong xe tại khu vực bãi đỗ. Kính đề nghị cư dân không để tài sản có giá trị trong xe, khóa cẩn thận và báo ngay cho bảo vệ trực khi phát hiện đối tượng khả nghi ra vào khu vực bãi xe, hành lang.',
                'thoi_gian' => 'tăng cường tuần tra từ 22:00 đến 05:00 hàng ngày.',
            ],
            [
                'tieu_de' => 'Thông báo lắp đặt bổ sung camera an ninh',
                'mo_dau' => 'Nhằm tăng cường công tác an ninh, Ban Quản lý sẽ lắp đặt bổ sung hệ thống camera giám sát tại các lối ra vào, thang máy và tầng hầm còn thiếu điểm quan sát. Toàn bộ dữ liệu camera được lưu trữ và chỉ sử dụng cho mục đích đảm bảo an ninh trật tự của chung cư.',
                'thoi_gian' => 'thi công từ ngày 21/07/2026 đến 25/07/2026.',
            ],
            [
                'tieu_de' => 'Thông báo diễn tập phòng cháy chữa cháy và cứu nạn cứu hộ',
                'mo_dau' => 'Thực hiện kế hoạch huấn luyện nghiệp vụ PCCC hàng năm, Ban Quản lý phối hợp lực lượng Cảnh sát PCCC tổ chức diễn tập tình huống giả định cháy và hướng dẫn kỹ năng thoát hiểm cho cư dân, nhân viên. Trong buổi diễn tập sẽ có còi báo động và khói giả, kính mong cư dân không hoang mang và tích cực tham gia thực hành.',
                'thoi_gian' => '08:00 đến 10:30 ngày 30/07/2026.',
            ],
            [
                'tieu_de' => 'Thông báo quy định đăng ký khách lưu trú',
                'mo_dau' => 'Để đảm bảo an ninh trật tự trong chung cư, Ban Quản lý đề nghị cư dân có khách đến lưu trú qua đêm hoặc từ 3 ngày trở lên thực hiện đăng ký thông tin khách tại văn phòng Ban Quản lý hoặc qua ứng dụng quản lý chung cư. Thủ tục đơn giản, chỉ cần cung cấp thông tin CCCD của khách lưu trú.',
                'thoi_gian' => 'áp dụng thường xuyên, đăng ký trước khi khách đến lưu trú.',
            ],
            [
                'tieu_de' => 'Khuyến cáo an toàn cho trẻ em tại khu vực ban công, lan can',
                'mo_dau' => 'Ban Quản lý khuyến cáo các gia đình có trẻ nhỏ không để trẻ em chơi một mình tại khu vực ban công, lan can, cửa sổ cao tầng. Không đặt bàn ghế, vật dụng gần lan can để tránh trẻ trèo leo gây nguy hiểm. Đây là khuyến cáo quan trọng nhằm phòng tránh các tai nạn đáng tiếc có thể xảy ra.',
                'thoi_gian' => 'khuyến cáo áp dụng thường xuyên.',
            ],
            [
                'tieu_de' => 'Thông báo kiểm tra hệ thống báo cháy tự động',
                'mo_dau' => 'Đội kỹ thuật sẽ tiến hành kiểm tra định kỳ đầu báo khói, báo nhiệt tại từng căn hộ và khu vực công cộng nhằm đảm bảo hệ thống báo cháy hoạt động chính xác. Kính mong cư dân tạo điều kiện cho nhân viên kỹ thuật vào căn hộ kiểm tra theo lịch đã thông báo trước.',
                'thoi_gian' => 'từ 08:00 đến 17:00, từ ngày 27/07/2026 đến 31/07/2026.',
            ],
            [
                'tieu_de' => 'Nhắc nhở cư dân khóa cửa cẩn thận khi ra khỏi căn hộ',
                'mo_dau' => 'Ban Quản lý nhắc nhở cư dân kiểm tra và khóa cửa chính, cửa sổ cẩn thận trước khi ra khỏi căn hộ, đặc biệt trong các dịp nghỉ lễ dài ngày. Cư dân nên thông báo cho bảo vệ hoặc người thân tin cậy trông coi căn hộ khi vắng nhà dài ngày để kịp thời phát hiện bất thường.',
                'thoi_gian' => 'áp dụng thường xuyên, đặc biệt các dịp lễ, Tết.',
            ],
            [
                'tieu_de' => 'Cảnh báo an toàn khu vực hồ bơi dành cho trẻ em',
                'mo_dau' => 'Ban Quản lý khuyến cáo phụ huynh giám sát chặt chẽ trẻ em khi vui chơi tại khu vực hồ bơi, không để trẻ tự ý xuống hồ khi không có người lớn đi cùng. Nhân viên cứu hộ chỉ trực trong khung giờ mở cửa hồ bơi, ngoài khung giờ này cư dân không tự ý sử dụng hồ bơi.',
                'thoi_gian' => 'áp dụng trong suốt thời gian hồ bơi hoạt động.',
            ],

            // ===== TIỆN ÍCH (10 bài) =====
            [
                'tieu_de' => 'Nội quy sử dụng hồ bơi chung cư',
                'mo_dau' => 'Ban Quản lý thông báo nội quy sử dụng hồ bơi: cư dân mang theo thẻ cư dân khi vào cổng, mặc trang phục bơi đúng quy định, tắm tráng trước khi xuống hồ, trẻ em dưới 12 tuổi phải có người lớn đi kèm và không mang thức ăn, đồ uống có cồn vào khu vực hồ bơi.',
                'thoi_gian' => 'hồ bơi mở cửa từ 05:30 đến 21:00 hàng ngày.',
            ],
            [
                'tieu_de' => 'Nội quy sử dụng phòng tập gym',
                'mo_dau' => 'Ban Quản lý thông báo nội quy phòng tập gym: cư dân mặc trang phục thể thao, mang giày phù hợp, vệ sinh và sắp xếp lại dụng cụ sau khi sử dụng, không sử dụng thiết bị khi chưa được hướng dẫn cách vận hành. Trẻ em dưới 16 tuổi cần có người lớn giám sát khi tập luyện.',
                'thoi_gian' => 'phòng gym mở cửa từ 05:00 đến 22:00 hàng ngày.',
            ],
            [
                'tieu_de' => 'Hướng dẫn đăng ký sử dụng phòng sinh hoạt cộng đồng',
                'mo_dau' => 'Cư dân có nhu cầu sử dụng phòng sinh hoạt cộng đồng để tổ chức sinh nhật, họp mặt gia đình vui lòng đăng ký trước tại văn phòng Ban Quản lý hoặc qua ứng dụng, kèm theo thông tin thời gian và mục đích sử dụng. Phòng được trang bị bàn ghế, âm thanh cơ bản, cư dân có trách nhiệm giữ gìn vệ sinh sau khi sử dụng.',
                'thoi_gian' => 'đăng ký trước ít nhất 3 ngày làm việc.',
            ],
            [
                'tieu_de' => 'Thông báo nâng cấp hệ thống internet, wifi tiện ích',
                'mo_dau' => 'Ban Quản lý hợp tác cùng nhà mạng nâng cấp đường truyền internet phục vụ khu vực sảnh, phòng sinh hoạt cộng đồng và hồ bơi nhằm mang lại trải nghiệm kết nối tốt hơn cho cư dân. Trong thời gian nâng cấp, wifi tại các khu vực này có thể gián đoạn tạm thời.',
                'thoi_gian' => 'thi công từ 09:00 đến 17:00 ngày 24/07/2026.',
            ],
            [
                'tieu_de' => 'Khuyến khích cư dân thanh toán phí dịch vụ trực tuyến',
                'mo_dau' => 'Nhằm tạo thuận tiện và hạn chế tiếp xúc trực tiếp, Ban Quản lý khuyến khích cư dân thanh toán phí dịch vụ, phí gửi xe qua ứng dụng quản lý chung cư hoặc chuyển khoản ngân hàng thay vì thanh toán tiền mặt tại quầy. Cư dân thanh toán online sẽ nhận biên lai điện tử ngay sau khi giao dịch thành công.',
                'thoi_gian' => 'áp dụng thường xuyên từ nay về sau.',
            ],
            [
                'tieu_de' => 'Hướng dẫn phân loại rác thải sinh hoạt',
                'mo_dau' => 'Ban Quản lý hướng dẫn cư dân phân loại rác thải thành 3 nhóm trước khi bỏ vào thùng rác chung: rác hữu cơ dễ phân hủy, rác tái chế như giấy, nhựa, kim loại và rác thải còn lại. Việc phân loại rác giúp giảm tải cho hệ thống xử lý, giữ gìn vệ sinh môi trường sống chung.',
                'thoi_gian' => 'áp dụng chính thức từ ngày 01/08/2026.',
            ],
            [
                'tieu_de' => 'Khai giảng lớp học bơi mùa hè cho cư dân',
                'mo_dau' => 'Ban Quản lý phối hợp huấn luyện viên chuyên nghiệp mở lớp học bơi dành cho cư dân và con em trong dịp hè, giúp trẻ rèn luyện sức khỏe và kỹ năng an toàn dưới nước. Học phí ưu đãi dành riêng cho cư dân chung cư, số lượng lớp có hạn.',
                'thoi_gian' => 'khai giảng ngày 27/07/2026, học 3 buổi mỗi tuần.',
            ],
            [
                'tieu_de' => 'Giới thiệu dịch vụ giặt ủi nhận - giao tận nhà',
                'mo_dau' => 'Ban Quản lý hợp tác với đơn vị giặt ủi uy tín cung cấp dịch vụ nhận và giao đồ giặt tận căn hộ, giúp cư dân tiết kiệm thời gian sinh hoạt. Cư dân có thể đặt lịch qua ứng dụng quản lý chung cư hoặc liên hệ trực tiếp quầy lễ tân để được tư vấn bảng giá.',
                'thoi_gian' => 'áp dụng từ ngày 01/08/2026, nhận đồ hàng ngày từ 08:00 đến 18:00.',
            ],
            [
                'tieu_de' => 'Ra mắt góc đọc sách và không gian làm việc chung',
                'mo_dau' => 'Nhằm đa dạng hóa tiện ích phục vụ nhu cầu học tập, làm việc của cư dân, Ban Quản lý đưa vào sử dụng góc đọc sách kết hợp không gian làm việc chung tại tầng 2 Block B, trang bị bàn làm việc, wifi tốc độ cao và không gian yên tĩnh.',
                'thoi_gian' => 'mở cửa từ 07:00 đến 22:00 hàng ngày, bắt đầu từ 22/07/2026.',
            ],
            [
                'tieu_de' => 'Thông báo nâng cấp khu vui chơi trẻ em',
                'mo_dau' => 'Ban Quản lý sẽ tiến hành nâng cấp, bổ sung thêm thiết bị vui chơi an toàn tại khu vui chơi trẻ em tầng trệt Block A nhằm mang đến không gian vui chơi phong phú hơn cho các bé. Trong thời gian thi công, khu vực này tạm thời đóng cửa, kính mong phụ huynh thông cảm.',
                'thoi_gian' => 'từ ngày 13/07/2026 đến 19/07/2026.',
            ],
        ];
    }
}
