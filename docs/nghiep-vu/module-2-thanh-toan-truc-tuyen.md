# TÀI LIỆU NGHIỆP VỤ VÀ ĐẶC TẢ CHỨC NĂNG

## HỆ THỐNG QUẢN LÝ CĂN HỘ CHUNG CƯ

# MODULE 2 — THANH TOÁN HÓA ĐƠN TRỰC TUYẾN

---

## Quy ước và phạm vi tài liệu

Module này mô tả nghiệp vụ thanh toán hóa đơn trực tuyến thông qua hai cổng thanh toán được tích hợp trong hệ thống: VNPay và MoMo. Nội dung được xây dựng dựa trên mã nguồn thực tế (`App\Services\VnpayService`, `App\Services\MomoService`, `App\Http\Controllers\Payment\VnpayController`, `App\Http\Controllers\Payment\MomoController`) cùng schema các bảng `lich_su_thanh_toan`, `nguon_tao`, `cau_hinh_website`. Module này phụ thuộc trực tiếp vào hàm ghi nhận thanh toán trung tâm (`HoaDonService::ghiNhanThanhToan`) đã trình bày ở Module 1 — mọi giao dịch trực tuyến thành công cuối cùng đều đi qua đúng hàm này để đảm bảo tính nhất quán tuyệt đối giữa thanh toán thủ công và thanh toán trực tuyến. Tài liệu ghi chú rõ ràng một hạn chế đã phát hiện trong mã nguồn hiện tại — hai cổng thanh toán hiện dùng chung một giá trị nguồn tạo giao dịch — để người đọc không hiểu nhầm giữa đặc tả kỳ vọng và hiện trạng.

---

## 1. GIỚI THIỆU

### 1.1. Mục đích chức năng

Module Thanh toán trực tuyến cho phép cư dân tất toán công nợ hóa đơn của căn hộ mình đang cư trú ngay trên hệ thống, không cần đến trực tiếp văn phòng Ban quản lý hay thực hiện chuyển khoản thủ công rồi chờ nhân viên đối chiếu. Chức năng này đóng vai trò cầu nối giữa hệ thống quản lý chung cư và hai hạ tầng thanh toán điện tử phổ biến tại Việt Nam — cổng VNPay (đại diện cho phương thức thanh toán qua thẻ ATM nội địa, thẻ quốc tế, ví điện tử liên kết ngân hàng) và cổng MoMo (đại diện cho phương thức thanh toán qua ví điện tử di động) — đồng thời đảm bảo mọi giao dịch, dù thành công hay thất bại, dù được xác nhận qua kênh nào, đều được xử lý theo đúng một bộ quy tắc nghiệp vụ duy nhất về công nợ đã xác lập ở Module 1.

### 1.2. Ý nghĩa trong hệ thống

Về bản chất kỹ thuật, module này không tự mình quyết định một hóa đơn đã được thanh toán hay chưa — nó chỉ đóng vai trò trung gian xác thực giao dịch với cổng thanh toán bên thứ ba (xác minh chữ ký số, xác minh số tiền, xác minh trạng thái giao dịch), sau đó ủy quyền toàn bộ việc ghi nhận công nợ cho hàm nghiệp vụ trung tâm của Module 1. Thiết kế tách bạch này có ý nghĩa kiến trúc quan trọng: mọi ràng buộc nghiệp vụ về công nợ (không vượt quá tổng tiền, khóa sửa khi đã có thanh toán, tính lại trạng thái hóa đơn) chỉ cần định nghĩa và bảo trì tại một nơi duy nhất, thay vì phải lặp lại logic tương tự trong từng service tích hợp cổng thanh toán, giảm thiểu rủi ro hai kênh thanh toán ứng xử khác nhau trước cùng một tình huống nghiệp vụ.

### 1.3. Vai trò đối với Ban quản lý

Ban quản lý sử dụng module này theo hai cách: thứ nhất, cấu hình và quản lý thông tin kết nối với cổng thanh toán (mã đối tác, khóa bí mật, việc bật/tắt từng cổng) mà không cần can thiệp vào mã nguồn hay triển khai lại hệ thống mỗi khi cần thay đổi; thứ hai, khởi tạo giao dịch thanh toán trực tuyến hộ cư dân trong trường hợp cư dân yêu cầu hỗ trợ tại quầy nhưng muốn thanh toán qua ví điện tử thay vì tiền mặt. Đồng thời, việc mọi giao dịch trực tuyến đều để lại một bản ghi lịch sử thanh toán bất biến, có mã giao dịch tham chiếu về phía cổng thanh toán, tạo điều kiện thuận lợi cho công tác đối soát tài chính định kỳ giữa số liệu nội bộ và sao kê từ nhà cung cấp dịch vụ thanh toán.

### 1.4. Vai trò đối với Cư dân

Cư dân là người trực tiếp hưởng lợi lớn nhất từ module này: thay vì phải đến văn phòng Ban quản lý trong giờ hành chính hoặc thực hiện chuyển khoản ngân hàng rồi chờ xác nhận thủ công (có độ trễ và có thể xảy ra sai sót nhập liệu do nhân viên), cư dân chỉ cần vài thao tác trên ứng dụng để hoàn tất nghĩa vụ tài chính bất kỳ lúc nào, và nhận được xác nhận gần như tức thời khi giao dịch thành công. Cư dân cũng có thể thanh toán một phần công nợ thay vì bắt buộc phải thanh toán toàn bộ trong một lần, phù hợp với các hóa đơn có giá trị lớn mà cư dân muốn chia nhỏ theo khả năng tài chính của mình.

---

## 2. CÁC TÁC NHÂN THAM GIA

### 2.1. Admin

Admin có quyền cấu hình toàn bộ thông tin kết nối cổng thanh toán (mã đối tác, khóa bí mật, địa chỉ endpoint, bật/tắt từng cổng) áp dụng cho toàn hệ thống, không giới hạn theo tòa nhà. Admin cũng có quyền khởi tạo giao dịch thanh toán trực tuyến hộ bất kỳ hóa đơn nào trong hệ thống và xem toàn bộ lịch sử giao dịch trực tuyến đã phát sinh.

### 2.2. Manager

Manager có quyền khởi tạo giao dịch thanh toán trực tuyến hộ cư dân đối với các hóa đơn thuộc phạm vi quản lý được phân công, và xem lịch sử giao dịch trong phạm vi đó. Việc cấu hình thông tin kết nối cổng thanh toán (mã đối tác, khóa bí mật) là thao tác có ảnh hưởng ở cấp toàn hệ thống nên thuộc thẩm quyền cấu hình chung, Manager sử dụng cấu hình đã được thiết lập sẵn, không tự ý thay đổi.

### 2.3. Cư dân

Cư dân có quyền tự khởi tạo và hoàn tất giao dịch thanh toán trực tuyến cho hóa đơn của căn hộ mình đang cư trú, xem lịch sử các giao dịch mình đã thực hiện. Cư dân không có quyền truy cập màn hình cấu hình cổng thanh toán và không thể khởi tạo giao dịch cho hóa đơn ngoài phạm vi cư trú của mình.

### 2.4. Cổng thanh toán bên thứ ba (VNPay, MoMo)

Mặc dù không phải là người dùng của hệ thống theo nghĩa thông thường, VNPay và MoMo đóng vai trò tác nhân hệ thống quan trọng: chúng nhận yêu cầu thanh toán được hệ thống chuyển hướng đến, xử lý giao dịch theo hạ tầng riêng của mình (xác thực người dùng, trừ tiền), sau đó chủ động gọi ngược lại hệ thống qua hai kênh độc lập — chuyển hướng trình duyệt người dùng trở về (return URL) và gọi trực tiếp máy chủ đến máy chủ để xác nhận kết quả giao dịch (IPN — Instant Payment Notification, hoặc gọi tắt là callback/notify). Toàn bộ thiết kế bảo mật của module này xoay quanh việc xác thực đúng đắn hai kênh gọi ngược này.

---

## 3. QUY TRÌNH NGHIỆP VỤ TỔNG THỂ

### 3.1. Nguyên tắc kiến trúc cốt lõi: tách bạch Return và IPN

Trước khi mô tả quy trình, cần làm rõ nguyên tắc kiến trúc quan trọng nhất chi phối toàn bộ module: hệ thống nhận về hai loại lời gọi ngược khác nhau từ cổng thanh toán sau khi người dùng hoàn tất (hoặc hủy) thanh toán trên trang của cổng.

Loại thứ nhất là **trang trả về (Return URL)** — trình duyệt của cư dân được cổng thanh toán chuyển hướng trở lại địa chỉ của hệ thống, kèm theo các tham số kết quả giao dịch trên chuỗi truy vấn URL. Vì đây là một yêu cầu xuất phát từ trình duyệt người dùng, đường truyền này hoàn toàn có thể bị người dùng chủ động chỉnh sửa tham số URL trước khi gửi (ví dụ đổi mã kết quả giao dịch từ thất bại thành thành công), hoặc bị phát lại (replay) nhiều lần chỉ bằng cách tải lại trang. Do đó, nguyên tắc bắt buộc là **trang trả về chỉ có nhiệm vụ hiển thị kết quả cho người dùng xem, tuyệt đối không được phép ghi nhận thanh toán vào cơ sở dữ liệu** dựa trên tham số của yêu cầu này.

Loại thứ hai là **thông báo tức thời (IPN/Notify)** — một lời gọi độc lập, xuất phát trực tiếp từ máy chủ của cổng thanh toán gọi đến máy chủ của hệ thống (không đi qua trình duyệt người dùng), có chữ ký số xác thực bằng khóa bí mật chỉ hai bên (hệ thống và cổng thanh toán) biết. Đây là **kênh duy nhất được phép ghi nhận thanh toán chính thức vào hệ thống**. Việc tách bạch này là biện pháp phòng thủ theo chiều sâu bắt buộc đối với mọi hệ thống tích hợp cổng thanh toán tại Việt Nam, và được cả VNPay lẫn MoMo khuyến nghị chính thức trong tài liệu tích hợp của họ.

### 3.2. Mô tả tổng thể quy trình

Quy trình thanh toán trực tuyến trải qua bốn giai đoạn: khởi tạo giao dịch, chuyển hướng và xử lý tại cổng thanh toán, tiếp nhận và xác thực lời gọi ngược, và ghi nhận kết quả vào công nợ hóa đơn.

**Giai đoạn khởi tạo.** Cư dân (hoặc nhân viên thay mặt cư dân) chọn hóa đơn cần thanh toán, nhập số tiền muốn thanh toán (có thể là một phần hoặc toàn bộ công nợ) và chọn cổng thanh toán mong muốn. Hệ thống kiểm tra số tiền hợp lệ (dương, không vượt quá công nợ hiện tại), sinh ra một mã tham chiếu giao dịch duy nhất gắn liền với mã hóa đơn và thời điểm khởi tạo, sau đó xây dựng một địa chỉ URL thanh toán có chữ ký số theo đúng đặc tả kỹ thuật của cổng được chọn.

**Giai đoạn chuyển hướng và xử lý tại cổng.** Cư dân được chuyển hướng sang giao diện của cổng thanh toán (nằm ngoài hệ thống), thực hiện xác thực và hoàn tất giao dịch theo quy trình riêng của VNPay hoặc MoMo (nhập thông tin thẻ/tài khoản, xác thực OTP, xác nhận trong ứng dụng ví điện tử...). Toàn bộ giai đoạn này nằm ngoài phạm vi kiểm soát của hệ thống.

**Giai đoạn tiếp nhận lời gọi ngược.** Sau khi cổng thanh toán xử lý xong (thành công, thất bại, hoặc người dùng hủy giữa chừng), hai sự kiện xảy ra gần như đồng thời nhưng độc lập: cổng thanh toán chuyển hướng trình duyệt cư dân trở về trang trả về của hệ thống để hiển thị kết quả, đồng thời (thường trong vòng vài giây, có thể sớm hơn hoặc muộn hơn thời điểm return tùy hạ tầng của từng cổng) gọi thông báo tức thời (IPN) trực tiếp đến máy chủ hệ thống. Hệ thống xử lý độc lập hai lời gọi này.

**Giai đoạn ghi nhận kết quả.** Chỉ tại kênh IPN, sau khi xác thực chữ ký số hợp lệ và xác nhận giao dịch thành công theo mã phản hồi của cổng, hệ thống mới gọi đến hàm ghi nhận thanh toán trung tâm của Module 1 để chính thức cập nhật công nợ hóa đơn, tạo bản ghi lịch sử thanh toán. Kênh IPN sau đó phải phản hồi lại đúng định dạng xác nhận đã nhận theo quy ước riêng của từng cổng, để cổng thanh toán không tiếp tục gửi lại thông báo.

### 3.3. Sơ đồ luồng nghiệp vụ tổng thể (dạng văn bản)

```
[Cư dân chọn hóa đơn + nhập số tiền + chọn cổng]
        --> {Kiểm tra: 0 < số tiền <= công nợ hiện tại}
              -- Không hợp lệ --> [Từ chối, báo lỗi]
              -- Hợp lệ --> [Sinh mã tham chiếu giao dịch duy nhất: HD{id}_{time} / VNP{id}_{time}]
        --> [Ký số tham số giao dịch bằng khóa bí mật của cổng]
        --> [Chuyển hướng cư dân sang giao diện cổng thanh toán]
        --> [Cư dân xác thực và hoàn tất giao dịch tại cổng — NGOÀI hệ thống]
        --> (Cổng thanh toán xử lý xong, phát sinh 2 lời gọi độc lập gần như đồng thời)
             |
             +--> [Return URL] --> Trình duyệt cư dân được đưa về hệ thống
             |         --> {Xác minh chữ ký số của tham số return}
             |               -- Sai --> [Hiển thị "Giao dịch không hợp lệ"]
             |               -- Đúng --> [CHỈ hiển thị kết quả cho cư dân xem — KHÔNG ghi DB]
             |
             +--> [IPN/Notify] --> Máy chủ cổng thanh toán gọi trực tiếp máy chủ hệ thống
                       --> {Xác minh chữ ký số}
                             -- Sai --> [Trả mã lỗi chữ ký, KHÔNG xử lý]
                       --> {Mã phản hồi giao dịch = thành công?}
                             -- Không --> [Xác nhận đã nhận, KHÔNG ghi nhận thanh toán]
                       --> {Mã giao dịch đã tồn tại trong lịch sử thanh toán?}
                             -- Đã tồn tại, số tiền khớp --> [Xác nhận đã xử lý — idempotent]
                             -- Đã tồn tại, số tiền LỆCH --> [Từ chối — nghi giả mạo]
                       --> {0 < số tiền <= công nợ hiện tại (dung sai 0,01)}
                       --> [Gọi HoaDonService::ghiNhanThanhToan() — Module 1]
                       --> [Trả xác nhận thành công đúng định dạng cổng yêu cầu]
```

---

## 4. QUY TẮC NGHIỆP VỤ (BUSINESS RULES)

### 4.1. Quy tắc chung cho cả hai cổng thanh toán

**BR-01 — Chỉ IPN được ghi nhận thanh toán.** Trang trả về (return) không bao giờ được phép gọi hàm ghi nhận thanh toán; hàm này chỉ được gọi từ luồng xử lý IPN/Notify. Đây là quy tắc kiến trúc bắt buộc, không có ngoại lệ cho bất kỳ cổng nào.

**BR-02 — Xác minh chữ ký số là điều kiện tiên quyết tuyệt đối.** Mọi tham số nhận được từ cổng thanh toán, dù ở kênh return hay kênh IPN, đều phải được xác minh chữ ký số hợp lệ trước khi được tin tưởng sử dụng cho bất kỳ mục đích nào (kể cả chỉ để hiển thị). Nếu chữ ký không hợp lệ, toàn bộ tham số đi kèm được coi là không đáng tin cậy và bị từ chối xử lý.

**BR-03 — Số tiền giao dịch phải khớp chính xác với công nợ đang xử lý.** Số tiền xác nhận từ cổng thanh toán phải nằm trong khoảng dương và không vượt quá công nợ hiện tại của hóa đơn tại thời điểm xử lý IPN (có dung sai làm tròn 0,01 đơn vị tiền tệ), áp dụng đúng quy tắc BR-13 của Module 1. Đây là lớp kiểm tra độc lập bổ sung, không thay thế cho kiểm tra đã có ở tầng Module 1, mà cộng thêm vào — vì giữa thời điểm khởi tạo giao dịch và thời điểm IPN được xử lý, công nợ của hóa đơn có thể đã thay đổi do một giao dịch khác được ghi nhận trước đó (ví dụ nhân viên ghi nhận thanh toán thủ công song song).

**BR-04 — Idempotency theo mã giao dịch của cổng thanh toán.** Với mỗi giao dịch, cổng thanh toán cung cấp một mã giao dịch định danh duy nhất (mã giao dịch VNPay hoặc mã `transId` của MoMo). Trước khi ghi nhận, hệ thống kiểm tra mã này đã tồn tại trong lịch sử thanh toán hay chưa: nếu đã tồn tại và số tiền khớp, hệ thống coi như đã xử lý thành công trước đó và chỉ xác nhận lại cho cổng thanh toán mà không ghi nhận thêm lần nữa; nếu đã tồn tại nhưng số tiền không khớp, hệ thống từ chối xử lý và coi đây là dấu hiệu bất thường cần cảnh giác (khả năng giả mạo tham số).

**BR-05 — Mỗi hóa đơn có thể được thanh toán bằng nhiều giao dịch trực tuyến độc lập.** Không có giới hạn về số lần một hóa đơn được thanh toán qua cổng trực tuyến, miễn là tổng cộng dồn không vượt quá tổng tiền hóa đơn (kế thừa BR-13 của Module 1); một cư dân có thể thanh toán một phần công nợ qua VNPay rồi thanh toán phần còn lại qua MoMo trong một lần khác.

**BR-06 — Cổng thanh toán phải được bật (enable) mới cho phép khởi tạo giao dịch.** Mỗi cổng thanh toán có một cờ bật/tắt cấu hình riêng; nếu cổng đang bị tắt, mọi yêu cầu khởi tạo giao dịch qua cổng đó bị từ chối ngay tại bước khởi tạo, không tiến hành xây dựng URL hay gọi bất kỳ API nào của cổng.

### 4.2. Quy tắc riêng cho VNPay

**BR-07 — Thuật toán ký số VNPay.** Chữ ký được tính bằng thuật toán HMAC-SHA512 trên chuỗi tham số đã được sắp xếp theo thứ tự bảng chữ cái của tên tham số, sử dụng khóa bí mật (hash secret) được cấp riêng cho từng đối tác tích hợp. Việc xác minh chữ ký thực hiện lại đúng quy trình này với toàn bộ tham số nhận về (loại trừ chính tham số chữ ký), so sánh bằng phương pháp so sánh chuỗi an toàn chống tấn công đo thời gian xử lý (`hash_equals`), không so sánh bằng phép so sánh chuỗi thông thường.

**BR-08 — Quy đổi đơn vị số tiền của VNPay.** Theo đặc tả kỹ thuật của VNPay, số tiền giao dịch được truyền đi và nhận về ở đơn vị nhân 100 lần so với đơn vị tiền tệ thực tế (ví dụ 100.000 đồng được biểu diễn là 10.000.000 trong tham số giao dịch). Hệ thống phải quy đổi lại đúng bằng cách chia cho 100 trước khi so sánh với công nợ hoặc ghi nhận vào lịch sử thanh toán; sai sót ở bước quy đổi này sẽ dẫn đến sai lệch số tiền ghi nhận gấp 100 lần, là lỗi nghiêm trọng cần kiểm thử kỹ.

**BR-09 — Điều kiện xác định giao dịch VNPay thành công.** Một giao dịch chỉ được coi là thành công khi đồng thời mã phản hồi giao dịch (response code) và mã trạng thái giao dịch (transaction status) đều bằng giá trị quy ước thành công theo tài liệu VNPay; chỉ kiểm tra một trong hai mã là không đủ để kết luận giao dịch thành công.

### 4.3. Quy tắc riêng cho MoMo

**BR-10 — Thuật toán ký số MoMo.** Chữ ký được tính bằng thuật toán HMAC-SHA256 trên một chuỗi dữ liệu thô được ghép theo đúng thứ tự trường cố định quy định bởi MoMo (không sắp xếp theo bảng chữ cái như VNPay), sử dụng khóa bí mật riêng (secret key) cấp cho đối tác. Việc xây dựng yêu cầu thanh toán ban đầu và việc xác minh chữ ký ở bước IPN phải dùng đúng cùng một công thức ghép chuỗi, khác nhau tùy theo đang xử lý chiều yêu cầu (request) hay chiều phản hồi (response/IPN).

**BR-11 — Điều kiện xác định giao dịch MoMo thành công.** Một giao dịch chỉ được coi là thành công khi mã kết quả (`resultCode`) trả về bằng 0 theo đúng quy ước của MoMo; mọi giá trị khác 0 được coi là thất bại hoặc bị hủy, không ghi nhận thanh toán.

**BR-12 — Định dạng phản hồi IPN của MoMo.** Sau khi xử lý xong lời gọi IPN (dù ghi nhận thành công hay từ chối do không hợp lệ), hệ thống phải phản hồi đúng theo quy ước HTTP mà MoMo yêu cầu (mã trạng thái không nội dung) để MoMo xác nhận hệ thống đã nhận được thông báo và không gửi lại nhiều lần.

---

## 5. LUỒNG XỬ LÝ CHÍNH (MAIN FLOW)

### 5.1. Luồng thanh toán qua VNPay — góc nhìn cư dân

**Bước 1.** Cư dân mở trang chi tiết hóa đơn còn công nợ, chọn "Thanh toán qua VNPay", xác nhận số tiền muốn thanh toán (mặc định gợi ý bằng đúng công nợ còn lại, cho phép sửa để thanh toán một phần).

**Bước 2.** Hệ thống xác thực hóa đơn thuộc phạm vi căn hộ cư dân đang cư trú, kiểm tra số tiền hợp lệ (dương, không vượt công nợ hiện tại), kiểm tra cổng VNPay đang ở trạng thái bật (BR-06).

**Bước 3.** Hệ thống sinh mã tham chiếu giao dịch theo định dạng `VNP{mã hóa đơn}_{thời điểm khởi tạo dạng unix timestamp}`, lưu tạm thông tin điều hướng sau khi xem kết quả (địa chỉ trang hiển thị kết quả, địa chỉ trang danh sách hóa đơn) vào phiên làm việc (session) để phục vụ hiển thị ở bước trả về.

**Bước 4.** Hệ thống xây dựng đầy đủ tham số giao dịch theo đặc tả VNPay (mã đối tác, số tiền đã quy đổi nhân 100, mã tham chiếu, thông tin đơn hàng, địa chỉ trả về), sắp xếp tham số theo thứ tự bảng chữ cái, tính chữ ký HMAC-SHA512, ghép thành địa chỉ URL hoàn chỉnh trỏ đến cổng thanh toán VNPay.

**Bước 5.** Cư dân được chuyển hướng sang giao diện VNPay, thực hiện chọn ngân hàng, xác thực và hoàn tất giao dịch theo quy trình của VNPay (nằm ngoài phạm vi kiểm soát của hệ thống).

**Bước 6a (Return — chỉ hiển thị).** VNPay chuyển hướng trình duyệt cư dân trở về địa chỉ trả về của hệ thống kèm tham số kết quả. Hệ thống xác minh chữ ký số của tham số return (BR-02); nếu hợp lệ và mã phản hồi/mã trạng thái đều là thành công (BR-09), hiển thị thông báo "Giao dịch thành công" cùng thông tin tóm tắt; nếu không, hiển thị thông báo tương ứng (thất bại/hủy). Bước này không ghi bất kỳ thay đổi nào vào cơ sở dữ liệu hóa đơn.

**Bước 6b (IPN — ghi nhận, chạy độc lập song song với 6a).** VNPay gọi trực tiếp đến địa chỉ IPN của hệ thống. Hệ thống thực hiện tuần tự: xác minh chữ ký số (BR-02), nếu sai trả về mã lỗi chữ ký cho VNPay và dừng xử lý; kiểm tra mã phản hồi/mã trạng thái giao dịch, nếu không phải thành công thì xác nhận đã nhận thông báo cho VNPay nhưng không ghi nhận thanh toán (tránh VNPay lặp lại gửi vô ích một giao dịch vốn đã thất bại); trích xuất mã hóa đơn từ mã tham chiếu giao dịch; xác nhận hóa đơn tồn tại; quy đổi số tiền về đúng đơn vị thực tế (BR-08); kiểm tra idempotency theo mã giao dịch VNPay (BR-04); kiểm tra số tiền nằm trong khoảng hợp lệ so với công nợ hiện tại (BR-03); gọi hàm ghi nhận thanh toán trung tâm của Module 1 với nguồn tạo giao dịch là cổng thanh toán; phản hồi xác nhận thành công theo đúng định dạng VNPay yêu cầu.

**Bước 7.** Cư dân quay lại trang hóa đơn (từ bước 6a), thấy công nợ đã được cập nhật theo kết quả xử lý của bước 6b — do IPN thường được xử lý gần như tức thời, tại thời điểm cư dân xem lại trang hóa đơn thì trạng thái công nợ trong đa số trường hợp đã phản ánh đúng kết quả giao dịch; tuy nhiên đây là hai luồng độc lập nên về lý thuyết có độ trễ nhỏ giữa hai bước, xem thêm Mục 6 về luồng thay thế liên quan.

### 5.2. Luồng thanh toán qua MoMo — góc nhìn cư dân

**Bước 1 đến Bước 3** tương tự luồng VNPay, khác biệt ở định dạng mã tham chiếu giao dịch (`HD{mã hóa đơn}_{thời điểm khởi tạo}`) và ở việc hệ thống phải chủ động gọi một API của MoMo (`requestType=captureWallet`) để lấy về địa chỉ thanh toán (`payUrl`) thay vì tự xây dựng URL hoàn toàn ở phía hệ thống như VNPay.

**Bước 4.** Hệ thống ghép chuỗi dữ liệu thô theo đúng thứ tự trường quy định của MoMo, ký HMAC-SHA256 bằng khóa bí mật, gửi yêu cầu HTTP POST đến endpoint tạo giao dịch của MoMo, nhận về địa chỉ `payUrl`.

**Bước 5.** Cư dân được chuyển hướng đến `payUrl`, xác thực và hoàn tất giao dịch trong ứng dụng/giao diện ví điện tử MoMo.

**Bước 6a (Return — chỉ hiển thị).** Tương tự Bước 6a của VNPay, chỉ hiển thị kết quả, không ghi nhận thanh toán.

**Bước 6b (IPN — ghi nhận).** MoMo gọi trực tiếp đến địa chỉ IPN của hệ thống. Hệ thống xác minh chữ ký số bằng cách ghép lại chuỗi dữ liệu thô theo đúng thứ tự trường của chiều phản hồi và so sánh với chữ ký nhận được (BR-02, BR-10); nếu sai, trả về mã lỗi HTTP tương ứng và dừng xử lý; kiểm tra mã kết quả (`resultCode`) bằng 0 (BR-11), nếu khác thì xác nhận đã nhận nhưng không ghi nhận thanh toán; trích xuất mã hóa đơn từ mã tham chiếu (`orderId`); kiểm tra idempotency theo `transId` (BR-04); kiểm tra số tiền hợp lệ so với công nợ (BR-03); gọi hàm ghi nhận thanh toán trung tâm với nguồn tạo giao dịch là cổng thanh toán; phản hồi đúng định dạng MoMo yêu cầu (BR-12).

**Bước 7.** Tương tự Bước 7 của luồng VNPay.

### 5.3. Luồng nhân viên khởi tạo thanh toán trực tuyến hộ cư dân

**Bước 1.** Nhân viên (Admin/Manager) mở trang chi tiết hóa đơn của một căn hộ trong phạm vi quản lý, chọn chức năng khởi tạo thanh toán qua VNPay hoặc MoMo hộ cư dân (áp dụng cho trường hợp cư dân đến quầy nhờ hỗ trợ nhưng muốn quét mã/chuyển hướng thanh toán bằng ví điện tử của chính mình).

**Bước 2.** Nhân viên nhập số tiền cần thanh toán (kiểm tra nằm trong khoảng hợp lệ tối thiểu và không vượt công nợ).

**Bước 3 trở đi** giống hệt quy trình ở Mục 5.1/5.2 kể từ bước sinh mã tham chiếu, khác biệt duy nhất là màn hình chuyển hướng ban đầu được hiển thị cho nhân viên (ví dụ dưới dạng mã QR để cư dân tự quét bằng điện thoại) thay vì tự động chuyển hướng trình duyệt của chính cư dân.

---

## 6. LUỒNG THAY THẾ (ALTERNATE FLOWS)

**AF-01 — Thanh toán thất bại tại cổng.** Nếu cư dân nhập sai thông tin xác thực hoặc ngân hàng/ví điện tử từ chối giao dịch (không đủ số dư, thẻ bị khóa...), cổng thanh toán trả về mã phản hồi thất bại. Trang trả về hiển thị thông báo thất bại tương ứng cho cư dân; kênh IPN (nếu cổng thanh toán vẫn gọi IPN cho giao dịch thất bại, tùy hạ tầng từng cổng) nhận diện mã phản hồi khác thành công và xác nhận đã nhận thông báo nhưng không ghi nhận bất kỳ thanh toán nào vào hóa đơn. Cư dân có thể quay lại thử thanh toán lại từ đầu (sinh mã tham chiếu giao dịch mới, không tái sử dụng mã cũ).

**AF-02 — Cư dân chủ động hủy giữa chừng tại giao diện cổng thanh toán.** Cư dân thoát khỏi giao diện VNPay/MoMo mà không hoàn tất xác thực. Tùy hạ tầng của cổng, trình duyệt có thể được chuyển hướng về trang trả về với mã phản hồi thể hiện việc hủy, hoặc không có bất kỳ lời gọi ngược nào được gửi (cư dân đóng thẳng trình duyệt/tab). Trong cả hai trường hợp, không có giao dịch nào được ghi nhận; hóa đơn giữ nguyên trạng thái công nợ trước đó. Nếu không có lời gọi ngược nào xảy ra, cư dân chỉ đơn giản không thấy có gì thay đổi khi quay lại trang hóa đơn của hệ thống.

**AF-03 — Hóa đơn quá hạn nhưng vẫn cho phép thanh toán.** Một hóa đơn đang ở trạng thái Quá hạn (theo định nghĩa Module 1) vẫn được phép khởi tạo và hoàn tất thanh toán trực tuyến bình thường; trạng thái Quá hạn chỉ phản ánh việc đã trễ hạn thanh toán theo cấu hình, không phải trạng thái khóa thanh toán. Sau khi ghi nhận đủ công nợ, hóa đơn chuyển sang Đã thanh toán bất kể trước đó đã từng ở trạng thái Quá hạn hay chưa.

**AF-04 — Timeout hoặc không nhận được phản hồi từ cổng thanh toán khi khởi tạo (áp dụng MoMo).** Vì việc khởi tạo giao dịch MoMo yêu cầu hệ thống chủ động gọi một API bên ngoài để lấy `payUrl` (khác với VNPay chỉ cần tự xây dựng URL), nếu API này không phản hồi trong thời gian chờ cho phép hoặc trả về lỗi kết nối, hệ thống không thể chuyển hướng cư dân đi được. Hệ thống cần hiển thị thông báo lỗi kết nối cổng thanh toán tạm thời, đề nghị cư dân thử lại sau hoặc chuyển sang thanh toán bằng phương thức khác (VNPay hoặc thủ công), không để cư dân chờ đợi vô thời hạn ở trạng thái xử lý.

**AF-05 — IPN bị trùng (Duplicate callback).** Cổng thanh toán, theo cơ chế đảm bảo tin cậy của riêng mình, có thể gọi IPN nhiều lần cho cùng một giao dịch nếu không nhận được xác nhận phản hồi đúng định dạng từ hệ thống ở lần gọi trước (ví dụ do lỗi mạng tạm thời phía hệ thống tại lần gọi đầu). Nhờ cơ chế idempotency theo mã giao dịch (BR-04), lần gọi thứ hai trở đi được nhận diện là đã xử lý trước đó, hệ thống chỉ xác nhận lại thành công cho cổng thanh toán mà không tạo thêm bản ghi lịch sử thanh toán hay cộng dồn thêm tiền vào công nợ đã thanh toán.

**AF-06 — VNPay timeout khi cư dân giữ trang thanh toán quá lâu.** Nếu cư dân mở trang thanh toán VNPay nhưng không hoàn tất xác thực trong thời gian hiệu lực quy định bởi VNPay, phiên giao dịch tại VNPay hết hạn; VNPay có thể chuyển hướng cư dân về trang trả về với mã phản hồi thể hiện hết hạn/hủy, tương tự AF-01. Mã tham chiếu giao dịch đã sinh ra cho phiên hết hạn này không được tái sử dụng cho lần thử thanh toán tiếp theo; hệ thống sinh mã tham chiếu hoàn toàn mới (do thành phần thời điểm khởi tạo trong mã tham chiếu luôn khác nhau giữa các lần khởi tạo).

**AF-07 — MoMo callback lỗi định dạng hoặc thiếu trường bắt buộc.** Nếu dữ liệu IPN gửi đến từ MoMo thiếu trường bắt buộc để xác minh chữ ký hoặc trích xuất mã hóa đơn, hệ thống từ chối xử lý ngay từ bước xác minh chữ ký (chữ ký tính lại từ dữ liệu thiếu trường chắc chắn không khớp với chữ ký nhận được), trả về mã lỗi tương ứng, không có bất kỳ thay đổi nào được ghi nhận.

**AF-08 — Người dùng thanh toán một phần rồi hủy nốt phần còn lại.** Cư dân thanh toán một phần công nợ qua VNPay thành công, sau đó quyết định không thanh toán phần còn lại ngay mà để lại cho kỳ sau hoặc thanh toán tại quầy. Đây là luồng hợp lệ theo BR-05 của module này và BR-14 của Module 1: hóa đơn giữ trạng thái Chưa thanh toán hoặc Quá hạn (tùy hạn thanh toán) với công nợ còn lại bằng phần chưa thanh toán, không có ràng buộc bắt buộc phải tất toán toàn bộ trong một phiên giao dịch.

---

## 7. LUỒNG NGOẠI LỆ (EXCEPTION FLOWS)

**EF-01 — Không tìm thấy hóa đơn khi xử lý IPN.** Nếu mã hóa đơn trích xuất được từ mã tham chiếu giao dịch không tồn tại trong hệ thống (trường hợp bất thường, ví dụ dữ liệu bị hỏng hoặc mã tham chiếu bị giả mạo dù chữ ký hợp lệ về mặt toán học nhưng dữ liệu nghiệp vụ không khớp), hệ thống từ chối xử lý, ghi nhận vào nhật ký kỹ thuật để điều tra, và trả về mã phản hồi lỗi phù hợp cho cổng thanh toán (không xác nhận thành công), vì đây là tình huống bất thường cần được rà soát thủ công thay vì âm thầm bỏ qua.

**EF-02 — Sai trạng thái nghiệp vụ: số tiền IPN lệch với mã giao dịch đã ghi nhận trước đó.** Khi mã giao dịch đã tồn tại trong lịch sử thanh toán nhưng số tiền của lời gọi IPN hiện tại không khớp với số tiền đã ghi nhận trước đó (chênh lệch vượt quá dung sai 0,01), đây là dấu hiệu nghi ngờ giả mạo hoặc dữ liệu bất thường nghiêm trọng từ phía gọi đến. Hệ thống từ chối xử lý, không ghi đè lên bản ghi cũ, không tạo thêm bản ghi mới, ghi nhận chi tiết sự việc vào nhật ký kỹ thuật kèm cảnh báo mức độ ưu tiên cao để đội vận hành rà soát thủ công.

**EF-03 — Không đủ quyền truy cập khi khởi tạo thanh toán hộ.** Nhân viên (Manager) cố khởi tạo giao dịch thanh toán cho hóa đơn ngoài phạm vi quản lý được phân công bị từ chối ở tầng kiểm tra quyền, tương tự nguyên tắc đã áp dụng cho Module 1. Cư dân cố khởi tạo thanh toán cho hóa đơn không thuộc căn hộ mình đang cư trú bị từ chối truy cập.

**EF-04 — Lỗi hệ thống trong quá trình gọi hàm ghi nhận thanh toán trung tâm.** Nếu trong lúc xử lý IPN, hàm ghi nhận thanh toán trung tâm của Module 1 phát sinh lỗi không mong muốn (ví dụ vi phạm ràng buộc vượt quá công nợ do một giao dịch khác vừa được ghi nhận song song ngay trước đó, dẫn đến ngoại lệ được ném ra), toàn bộ giao dịch cơ sở dữ liệu của lần xử lý IPN này bị hủy bỏ (rollback), không có bản ghi nào được tạo. Hệ thống cần trả về mã phản hồi lỗi cho cổng thanh toán (không xác nhận thành công) để cổng thanh toán có cơ hội gọi lại IPN sau đó; tại lần gọi lại, nếu tình huống gây lỗi đã không còn (ví dụ công nợ đã được giải phóng), giao dịch sẽ được xử lý thành công bình thường.

**EF-05 — Cấu hình cổng thanh toán bị thiếu hoặc không hợp lệ.** Nếu thông tin cấu hình bắt buộc (mã đối tác, khóa bí mật, địa chỉ endpoint) của một cổng thanh toán chưa được thiết lập hoặc bị để trống, mọi yêu cầu khởi tạo giao dịch qua cổng đó bị từ chối với thông báo cấu hình chưa sẵn sàng, không được phép tiếp tục với giá trị rỗng hoặc giá trị mặc định không an toàn.

**EF-06 — Chữ ký số không hợp lệ.** Áp dụng cho cả trang trả về lẫn IPN của cả hai cổng: nếu chữ ký số tính lại từ tham số nhận được không khớp với chữ ký gửi kèm, toàn bộ tham số bị coi là không đáng tin cậy. Đối với trang trả về, hệ thống hiển thị thông báo lỗi chung cho cư dân, không tiết lộ chi tiết kỹ thuật của việc xác minh chữ ký thất bại. Đối với IPN, hệ thống trả về mã lỗi tương ứng theo quy ước riêng của từng cổng và tuyệt đối không xử lý ghi nhận thanh toán.

---

## 8. VALIDATION

| Trường/Đối tượng | Điều kiện kiểm tra | Áp dụng tại |
|---|---|---|
| Số tiền thanh toán (khi khởi tạo) | Số dương, không vượt quá công nợ hiện tại của hóa đơn | Khởi tạo giao dịch (cư dân, nhân viên) |
| Hóa đơn (khi khởi tạo) | Phải tồn tại, thuộc phạm vi cư trú/quản lý của người khởi tạo | Khởi tạo giao dịch |
| Trạng thái bật/tắt cổng thanh toán | Cổng được chọn phải đang ở trạng thái bật | Khởi tạo giao dịch |
| Chữ ký số (return) | Phải khớp với chữ ký tính lại từ toàn bộ tham số nhận được | Trang trả về (cả hai cổng) |
| Chữ ký số (IPN) | Phải khớp với chữ ký tính lại từ toàn bộ tham số nhận được | Xử lý IPN (cả hai cổng) |
| Mã phản hồi/mã trạng thái giao dịch | Phải đúng giá trị quy ước "thành công" của từng cổng mới được ghi nhận | Xử lý IPN |
| Số tiền giao dịch (IPN, sau quy đổi đơn vị nếu là VNPay) | Phải nằm trong khoảng (0, công nợ hiện tại + 0,01] | Xử lý IPN |
| Mã giao dịch của cổng thanh toán | Không được xử lý ghi nhận lần thứ hai nếu đã tồn tại và số tiền khớp; từ chối nếu đã tồn tại nhưng số tiền lệch | Xử lý IPN |
| Mã hóa đơn trích xuất từ mã tham chiếu giao dịch | Phải trích xuất được đúng định dạng quy định và hóa đơn phải tồn tại | Xử lý IPN |

---

## 9. TRẠNG THÁI DỮ LIỆU

Bản thân module Thanh toán trực tuyến không định nghĩa một bảng trạng thái giao dịch trung gian riêng biệt ở tầng cơ sở dữ liệu nội bộ (không có bảng "giao dịch thanh toán đang chờ xử lý"); trạng thái của một phiên thanh toán trực tuyến chỉ tồn tại tạm thời trong phiên làm việc (session) của trình duyệt giữa thời điểm khởi tạo và thời điểm cư dân được chuyển hướng trở về, và trong hệ thống lưu vết riêng của cổng thanh toán bên thứ ba (nằm ngoài phạm vi kiểm soát của hệ thống). Kết quả cuối cùng của một phiên thanh toán trực tuyến chỉ thể hiện ở hệ thống thông qua một trong hai khả năng: hoặc xuất hiện một bản ghi mới trong lịch sử thanh toán (giao dịch thành công, đã được IPN ghi nhận) hoặc hoàn toàn không có bản ghi nào xuất hiện (giao dịch thất bại, bị hủy, hoặc IPN chưa/không được gọi).

**Sơ đồ trạng thái phiên thanh toán trực tuyến (dạng văn bản, ở mức khái niệm — không phải trạng thái lưu trong một bảng dữ liệu cụ thể):**

```
[Khởi tạo giao dịch] --> [Đã chuyển hướng sang cổng thanh toán]
        |
        +--> [Cư dân hoàn tất xác thực thành công tại cổng]
        |         --> [Cổng gọi IPN] --> {Xác minh chữ ký + mã kết quả + idempotency + số tiền}
        |               -- Tất cả hợp lệ --> [BẢN GHI LỊCH SỬ THANH TOÁN ĐƯỢC TẠO] (trạng thái cuối, bất biến)
        |               -- Một điều kiện không thỏa --> [KHÔNG có bản ghi nào được tạo] (trạng thái cuối)
        |
        +--> [Cư dân hủy/thất bại/timeout tại cổng]
                  --> [KHÔNG có bản ghi nào được tạo] (trạng thái cuối)
```

Sau khi một bản ghi lịch sử thanh toán được tạo từ giao dịch trực tuyến, vòng đời dữ liệu của nó tuân theo đúng nguyên tắc bất biến đã trình bày tại Mục 9.3 của Module 1 — không có trạng thái trung gian, không thể sửa hoặc xóa.

---

## 10. NHẬT KÝ HỆ THỐNG (AUDIT LOG)

Mọi khoản thanh toán trực tuyến được ghi nhận thành công đều đi qua đúng hàm ghi nhận thanh toán trung tâm của Module 1, do đó tự động được ghi vào nhật ký hệ thống với đầy đủ thông tin đã mô tả tại Mục 10 của Module 1 (số tiền, phương thức, mã giao dịch, nguồn tạo). Vì giao dịch trực tuyến khởi phát từ hành động của cổng thanh toán gọi đến hệ thống (không phải một nhân viên đang đăng nhập trực tiếp thao tác), trường người thực hiện trong nhật ký hệ thống được để trống đối với các bản ghi phát sinh từ luồng cư dân tự thanh toán, và mang giá trị của nhân viên đã khởi tạo đối với luồng nhân viên khởi tạo hộ.

Ngoài nhật ký nghiệp vụ dùng chung với Module 1, các sự kiện đặc thù của module này — xác minh chữ ký thất bại, số tiền lệch khi phát hiện trùng mã giao dịch (EF-02), lỗi kết nối đến API cổng thanh toán khi khởi tạo (AF-04) — cần được ghi vào nhật ký kỹ thuật (application log) của hệ thống ở mức cảnh báo phù hợp (mức cao đối với các dấu hiệu nghi giả mạo như EF-02), phục vụ giám sát bảo mật và điều tra sự cố, độc lập với nhật ký nghiệp vụ dành cho audit tài chính thông thường.

---

## 11. THÔNG BÁO

Tương tự hạn chế đã ghi nhận ở Module 1, hệ thống hiện chưa tích hợp cơ chế gửi email hoặc tin nhắn xác nhận tự động cho cư dân ngay sau khi một giao dịch thanh toán trực tuyến được ghi nhận thành công qua IPN. Cư dân nhận biết kết quả giao dịch chủ yếu qua trang trả về hiển thị ngay sau khi hoàn tất thanh toán tại cổng (Bước 6a của Mục 5), và có thể xác nhận lại bằng cách xem lại trạng thái công nợ/lịch sử thanh toán của hóa đơn sau đó.

Cần lưu ý một điểm quan trọng về độ tin cậy thông báo: vì trang trả về và IPN là hai luồng xử lý độc lập, có khả năng (dù hiếm, do độ trễ mạng hoặc do cổng thanh toán chậm gửi IPN) rằng cư dân nhìn thấy thông báo "giao dịch thành công" ở trang trả về trước khi IPN thực sự được xử lý và ghi nhận vào công nợ. Trong trường hợp này, nếu cư dân lập tức kiểm tra lại trang hóa đơn, có thể tạm thời vẫn thấy công nợ chưa được cập nhật cho đến khi IPN hoàn tất xử lý (thường trong vòng vài giây). Đây không phải là lỗi hệ thống mà là đặc tính vốn có của kiến trúc tách bạch return/IPN; giao diện cư dân nên có khuyến cáo phù hợp (ví dụ "công nợ sẽ được cập nhật trong giây lát") thay vì gây hiểu nhầm rằng giao dịch chưa thành công.

Định hướng bổ sung cần cân nhắc: gửi thông báo trong ứng dụng (tương tự cơ chế thông báo thời gian thực đã áp dụng cho Module 3) ngay khi IPN xử lý thành công, giúp cư dân nhận được xác nhận độc lập với việc họ có đang chờ ở trang trả về hay không (ví dụ trường hợp cư dân đóng tab trình duyệt ngay sau khi xác thực xong tại cổng thanh toán, trước khi kịp được chuyển hướng về).

---

## 12. PHÂN QUYỀN

Ai được xem: Admin xem toàn bộ lịch sử giao dịch trực tuyến trên hệ thống; Manager xem trong phạm vi quản lý được phân công; cư dân chỉ xem giao dịch thuộc các hóa đơn của căn hộ mình đang cư trú.

Ai được tạo (khởi tạo giao dịch): cư dân được tự khởi tạo cho hóa đơn của chính căn hộ mình đang cư trú; nhân viên (Admin/Manager) được khởi tạo hộ cho hóa đơn trong phạm vi quyền hạn tương ứng.

Ai được cấu hình cổng thanh toán: chỉ Admin có quyền thiết lập, thay đổi thông tin kết nối (mã đối tác, khóa bí mật, endpoint) và bật/tắt từng cổng — đây là cấu hình ảnh hưởng ở cấp toàn hệ thống, không phân quyền theo phạm vi quản lý như dữ liệu nghiệp vụ thông thường.

Ai được thanh toán: chỉ chính cư dân sở hữu quan hệ cư trú hợp lệ với căn hộ của hóa đơn (khi tự thanh toán), hoặc nhân viên trong phạm vi quản lý (khi khởi tạo hộ, nhưng bước xác thực và hoàn tất giao dịch thực tế tại cổng thanh toán vẫn do cư dân/người cầm thiết bị thanh toán thực hiện, hệ thống chỉ hỗ trợ sinh giao diện chuyển hướng).

Ai được duyệt: không có bước phê duyệt trung gian nào ở tầng hệ thống đối với giao dịch trực tuyến — việc "duyệt" giao dịch hoàn toàn thuộc thẩm quyền và quy trình nội bộ của cổng thanh toán (xác thực OTP, xác thực sinh trắc học trong ứng dụng ví điện tử...), hệ thống chỉ tiếp nhận kết quả cuối cùng qua IPN.

---

## 13. RÀNG BUỘC DỮ LIỆU

Mỗi bản ghi lịch sử thanh toán phát sinh từ giao dịch trực tuyến vẫn tuân theo đầy đủ ràng buộc đã mô tả tại Mục 13 của Module 1 (khóa ngoại bắt buộc đến hóa đơn, không xóa được dưới mọi hình thức, không sửa được). Trường nguồn tạo giao dịch (`nguon_tao`) của bản ghi phân biệt giao dịch có nguồn gốc từ cổng thanh toán trực tuyến với giao dịch ghi nhận thủ công, phục vụ thống kê và đối soát theo kênh thanh toán.

**Hạn chế cần lưu ý về dữ liệu hiện tại:** danh mục nguồn tạo giao dịch trong hệ thống hiện định nghĩa giá trị cho "Hệ thống", "Thủ công" và một giá trị chung được cả VNPay lẫn MoMo cùng sử dụng — nói cách khác, ở tầng dữ liệu `nguon_tao`, hệ thống hiện **không phân biệt được** một giao dịch thanh toán trực tuyến đến từ cổng VNPay hay cổng MoMo chỉ bằng cách nhìn vào trường nguồn tạo; việc phân biệt hai cổng chỉ có thể thực hiện được gián tiếp thông qua nội dung văn bản tự do của trường phương thức thanh toán (ví dụ giá trị "VNPay (NCB)" so với "MoMo") được ghi kèm ở mỗi bản ghi. Đây là điểm hạn chế của thiết kế dữ liệu hiện tại cần được lưu ý khi xây dựng báo cáo thống kê tách biệt theo từng cổng thanh toán, và nên được xem xét bổ sung giá trị nguồn tạo riêng biệt cho từng cổng trong các đợt nâng cấp tiếp theo để đảm bảo tính chính xác của số liệu đối soát phân loại theo kênh.

Không cho xóa: bản ghi lịch sử thanh toán phát sinh từ giao dịch trực tuyến, giống mọi bản ghi lịch sử thanh toán khác, không có cơ chế xóa dưới bất kỳ hình thức nào trong toàn hệ thống.

Ràng buộc unique theo nghiệp vụ: mã giao dịch của cổng thanh toán, dù không có ràng buộc duy nhất (unique constraint) tường minh ở tầng cơ sở dữ liệu trên cột lưu mã giao dịch, được đảm bảo không trùng lặp về mặt xử lý nghiệp vụ nhờ cơ chế kiểm tra tồn tại trước khi ghi kết hợp với việc toàn bộ thao tác ghi nhận nằm trong phạm vi bảo vệ của khóa độc quyền trên bản ghi hóa đơn (`lockForUpdate`, kế thừa từ Module 1), giảm thiểu đáng kể (dù về mặt lý thuyết chưa tuyệt đối bằng một ràng buộc duy nhất ở tầng cơ sở dữ liệu) khả năng ghi trùng khi có nhiều lời gọi IPN đến đồng thời cho cùng một mã giao dịch.

---

## 14. HIỆU NĂNG

Thao tác xử lý IPN cần được thiết kế để phản hồi nhanh cho cổng thanh toán (tránh cổng thanh toán tự động coi là timeout và gửi lại thông báo dẫn đến áp lực xử lý trùng lặp không cần thiết dù đã có cơ chế idempotency); toàn bộ xử lý IPN — từ xác minh chữ ký đến ghi nhận thanh toán — nên hoàn tất trong một khoảng thời gian ngắn, không thực hiện các thao tác nặng, không liên quan trực tiếp (như gửi email đồng bộ) ngay trong luồng xử lý IPN mà nên tách ra xử lý bất đồng bộ nếu cần bổ sung trong tương lai.

Việc ghi nhận thanh toán trực tuyến sử dụng lại đúng cơ chế khóa độc quyền (`lockForUpdate`) trên bản ghi hóa đơn của Module 1; điểm cần lưu ý về hiệu năng là thời gian giữ khóa trong giao dịch xử lý IPN cần được giới hạn tối thiểu (chỉ bao gồm các thao tác đọc/ghi dữ liệu công nợ thực sự cần thiết), không nên thực hiện lời gọi mạng ra bên ngoài (ví dụ gọi thêm API xác minh khác) trong khi vẫn đang giữ khóa, để tránh kéo dài thời gian khóa gây nghẽn cho các giao dịch khác đang chờ xử lý trên cùng hóa đơn.

Đối với việc khởi tạo giao dịch MoMo — vốn yêu cầu gọi đồng bộ đến API bên ngoài để lấy `payUrl` trước khi có thể chuyển hướng cư dân (khác với VNPay chỉ cần tự xây dựng URL nội bộ, không cần gọi mạng ra ngoài) — cần thiết lập thời gian chờ (timeout) hợp lý cho lời gọi này để tránh yêu cầu của cư dân bị treo vô thời hạn khi hạ tầng MoMo phản hồi chậm hoặc gặp sự cố tạm thời.

Việc tra cứu lịch sử giao dịch trực tuyến phục vụ đối soát định kỳ cần hỗ trợ lọc theo khoảng thời gian và theo nguồn tạo, đi kèm phân trang, tương tự nguyên tắc hiệu năng chung đã nêu tại Module 1, đặc biệt quan trọng khi khối lượng giao dịch trực tuyến tích lũy theo thời gian có xu hướng tăng nhanh hơn giao dịch thủ công do sự tiện lợi của kênh này.

---

## 15. BẢO MẬT

An toàn của toàn bộ module này phụ thuộc gần như tuyệt đối vào việc bảo vệ đúng đắn khóa bí mật (hash secret/secret key) dùng để ký và xác minh chữ ký số với từng cổng thanh toán; khóa bí mật phải được lưu trữ trong cấu hình bảo mật của hệ thống (không hiển thị dưới dạng văn bản thuần khi tra cứu cấu hình qua giao diện quản trị), không được nhúng cứng trong mã nguồn công khai, và chỉ Admin có quyền xem/chỉnh sửa.

Chống thanh toán trùng (double payment) và chống double submit được đảm bảo bằng sự kết hợp của ba lớp: kiểm tra idempotency theo mã giao dịch của cổng thanh toán (BR-04) là lớp chính; khóa độc quyền bản ghi hóa đơn trong giao dịch cơ sở dữ liệu là lớp đảm bảo tính đúng đắn khi có nhiều lời gọi đồng thời (chống race condition, kế thừa từ Module 1); và việc mã tham chiếu giao dịch luôn được sinh mới cho mỗi lần khởi tạo (không tái sử dụng giữa các lần thử) đảm bảo mỗi phiên thanh toán là độc lập, không có khái niệm "thử lại" trên cùng một mã tham chiếu cũ.

Chống race condition khi hai lời gọi IPN từ hai giao dịch khác nhau đến gần như đồng thời cho cùng một hóa đơn (ví dụ thanh toán một phần qua VNPay và một phần qua MoMo được gửi gần như cùng lúc): cơ chế khóa độc quyền bản ghi hóa đơn đảm bảo hai lời gọi được xử lý tuần tự chứ không chạy song song, mỗi lời gọi đọc đúng giá trị công nợ đã được cập nhật bởi lời gọi trước đó (nếu có) trước khi tự kiểm tra và ghi nhận, loại bỏ hoàn toàn khả năng cả hai lời gọi cùng đọc một giá trị công nợ ban đầu rồi cùng ghi đè gây sai lệch.

CSRF không áp dụng cho các route xử lý return/IPN của cổng thanh toán, vì đây là các route công khai (không yêu cầu đăng nhập) được thiết kế để nhận lời gọi từ trình duyệt người dùng (return, không mang phiên đăng nhập của hệ thống theo nghĩa CSRF bảo vệ) và từ máy chủ cổng thanh toán (IPN, hoàn toàn không phải lời gọi từ trình duyệt nên khái niệm CSRF không áp dụng); thay vào đó, an toàn của các route này hoàn toàn dựa vào cơ chế xác minh chữ ký số đã trình bày, đây là cơ chế xác thực mạnh hơn và phù hợp hơn CSRF token cho bối cảnh giao tiếp máy chủ-máy chủ.

Idempotency (BR-04) không chỉ là biện pháp đảm bảo tính đúng đắn nghiệp vụ mà còn là lớp phòng thủ bảo mật quan trọng chống lại kịch bản một bên cố tình phát lại (replay) một lời gọi IPN đã ghi nhận thành công trước đó với hy vọng hệ thống ghi nhận thêm một lần tiền nữa vào công nợ đã thanh toán — miễn là chữ ký số của lời gọi phát lại này vẫn còn hợp lệ (thực tế chữ ký chỉ có thể được tạo bởi bên nắm khóa bí mật, nên kịch bản này chủ yếu phòng ngừa việc chính cổng thanh toán vô tình gửi lại nhiều lần chứ không phải một kẻ tấn công bên ngoài giả mạo được chữ ký hợp lệ mà không có khóa bí mật).

Việc trích xuất mã hóa đơn trực tiếp từ mã tham chiếu giao dịch (vốn có cấu trúc có thể đoán được, ví dụ chỉ là mã hóa đơn nối với một mốc thời gian) không tự nó gây ra rủi ro bảo mật nghiêm trọng, vì mọi ghi nhận thanh toán vẫn bắt buộc phải đi kèm chữ ký số hợp lệ từ phía cổng thanh toán và số tiền phải khớp với công nợ thực tế của đúng hóa đơn đó — một kẻ tấn công dù đoán đúng hoặc tự tạo ra mã tham chiếu trỏ đến một hóa đơn bất kỳ cũng không thể tự tạo ra một chữ ký số hợp lệ nếu không nắm giữ khóa bí mật của cổng thanh toán, do đó không thể tự mình kích hoạt việc ghi nhận thanh toán giả mạo cho hóa đơn của người khác.

---

*(Hết Module 2 — Thanh toán trực tuyến. Tài liệu tiếp tục với Module 3 — Đặt lịch sử dụng tiện ích.)*
