# TÀI LIỆU NGHIỆP VỤ VÀ ĐẶC TẢ CHỨC NĂNG

## HỆ THỐNG QUẢN LÝ CĂN HỘ CHUNG CƯ

# MODULE 3 — ĐẶT LỊCH SỬ DỤNG TIỆN ÍCH

---

## Quy ước và phạm vi tài liệu

Module này mô tả nghiệp vụ đặt lịch sử dụng các tiện ích chung của tòa nhà (phòng sinh hoạt cộng đồng, phòng gym, hồ bơi, sân thể thao...) giữa Ban quản lý và cư dân. Nội dung được xây dựng dựa trên schema bảng `dat_lich_tien_ich`, `tien_ich`, `loai_tien_ich`, cùng kiến trúc dịch vụ thực tế của hệ thống, tổ chức theo mô hình Facade: `BookingService` chỉ ủy quyền, toàn bộ logic nghiệp vụ nằm trong bốn dịch vụ con chuyên biệt — `BookingCapacityService` (tính phí và sức chứa), `BookingApprovalService` (máy trạng thái duyệt/từ chối/hủy/hoàn thành), `BookingFifoService` (xử lý hàng đợi theo thứ tự đặt trước), và `BookingSchedulerService` (ba tác vụ tự động định kỳ), phối hợp với `BookingRealtimeService` cho việc phát thông báo thời gian thực. Đây là module có kiến trúc nghiệp vụ phức tạp và tinh vi nhất trong ba module của tài liệu, do phải xử lý đồng thời bài toán giới hạn tài nguyên chung (sức chứa), tính công bằng giữa nhiều người dùng (FIFO), và tự động hóa theo thời gian thực (scheduler).

---

## 1. GIỚI THIỆU

### 1.1. Mục đích chức năng

Module Đặt lịch sử dụng tiện ích quản lý toàn bộ vòng đời của một yêu cầu sử dụng tiện ích chung trong tòa nhà: từ khi cư dân đăng ký một khung giờ sử dụng cụ thể, qua quá trình xét duyệt dựa trên sức chứa còn lại và thứ tự đăng ký, cho đến khi tiện ích được sử dụng xong hoặc yêu cầu bị hủy/từ chối. Vì tiện ích chung là tài nguyên hữu hạn được chia sẻ giữa nhiều cư dân (một phòng sinh hoạt cộng đồng chỉ chứa được một số người nhất định tại một thời điểm), module này về bản chất là một bài toán quản lý tài nguyên có giới hạn theo thời gian, đòi hỏi cơ chế xét duyệt công bằng, minh bạch và có khả năng tự động xử lý các tình huống phát sinh mà không cần con người can thiệp liên tục.

### 1.2. Ý nghĩa trong hệ thống

Khác với module Hóa đơn (nơi công nợ chỉ tăng/giảm theo một chiều tài chính) và module Thanh toán (nơi giao dịch hoặc thành công hoặc thất bại một cách dứt khoát), module Đặt lịch tiện ích có bài toán đồng thời (concurrency) phức tạp hơn hẳn: nhiều cư dân có thể cùng lúc muốn đặt cùng một tiện ích, cùng một khung giờ, trong khi sức chứa có giới hạn; đồng thời hệ thống phải tự vận hành theo thời gian thực để duyệt các yêu cầu đang chờ ngay khi có chỗ trống phát sinh (do một booking khác bị hủy) hoặc tự động hủy các yêu cầu sắp đến giờ mà không còn khả năng được duyệt, hoàn toàn không đợi một nhân viên phải trực theo dõi thủ công. Đây là module duy nhất trong ba module của tài liệu có tích hợp xử lý nền tự động (scheduler) chạy độc lập với hành vi truy cập của người dùng, và là module duy nhất phát thông báo qua kênh thời gian thực (WebSocket/Reverb) thay vì chỉ cập nhật khi tải lại trang.

### 1.3. Vai trò đối với Ban quản lý

Ban quản lý sử dụng module này để thiết lập danh mục tiện ích của tòa nhà (tên, mô tả, vị trí, sức chứa, khung giờ hoạt động, phí sử dụng, tiện ích nào cho phép đặt trước), theo dõi toàn bộ lịch sử đặt chỗ trên toàn tòa nhà qua dashboard tổng hợp (bao gồm doanh thu từ phí sử dụng, tiện ích được ưa chuộng nhất, tỷ lệ sử dụng theo khung giờ), và can thiệp thủ công vào các trường hợp cần xét duyệt đặc biệt (duyệt sớm, từ chối, hủy) ngoài cơ chế tự động thông thường. Việc tự động hóa phần lớn quy trình duyệt/hủy giúp giảm đáng kể khối lượng công việc vận hành hàng ngày của nhân viên, chỉ cần can thiệp khi có ngoại lệ hoặc khiếu nại.

### 1.4. Vai trò đối với Cư dân

Cư dân sử dụng module này để chủ động đăng ký sử dụng các tiện ích chung của tòa nhà theo nhu cầu cá nhân (tổ chức sinh nhật tại phòng sinh hoạt cộng đồng, đặt sân cầu lông vào cuối tuần...), theo dõi trạng thái yêu cầu của mình theo thời gian thực (được duyệt ngay, phải chờ do hết chỗ, bị từ chối, tự động hủy do quá gần giờ mà chưa đủ chỗ), và chủ động hủy yêu cầu khi không còn nhu cầu sử dụng nữa (trong giới hạn thời gian cho phép), giải phóng chỗ kịp thời cho cư dân khác đang chờ.

---

## 2. CÁC TÁC NHÂN THAM GIA

### 2.1. Admin

Admin có toàn quyền trên module này ở phạm vi toàn hệ thống: quản lý danh mục loại tiện ích và tiện ích cụ thể của mọi tòa nhà, xem/duyệt/từ chối/hủy/khôi phục mọi yêu cầu đặt lịch, xem dashboard thống kê tổng hợp toàn hệ thống.

### 2.2. Manager

Manager có bộ quyền nghiệp vụ tương đương Admin đối với module này (tạo/sửa tiện ích, duyệt/từ chối/hủy đặt lịch, xem dashboard), giới hạn trong phạm vi tòa nhà/khu vực được phân công quản lý — kiến trúc Controller của Manager gần như đồng nhất với Admin, chỉ khác không gian tên và giao diện, phần lọc phạm vi dữ liệu được áp dụng nhất quán theo đúng nguyên tắc Scope đã sử dụng xuyên suốt các module khác của hệ thống.

### 2.3. Cư dân

Cư dân có quyền xem danh sách tiện ích đang hoạt động và cho phép đặt trước, xem tình trạng sức chứa còn lại theo thời gian thực trước khi đặt, tạo yêu cầu đặt lịch mới cho căn hộ mình đang cư trú, sửa yêu cầu đang ở trạng thái chờ duyệt, và hủy yêu cầu của chính mình trong giới hạn quy tắc thời gian cho phép. Cư dân không có quyền duyệt hoặc từ chối yêu cầu (kể cả yêu cầu của chính mình), không có quyền quản lý danh mục tiện ích.

### 2.4. Hệ thống tự động (Scheduler)

Mặc dù không phải người dùng theo nghĩa thông thường, ba tiến trình tự động định kỳ của hệ thống đóng vai trò tác nhân chủ động quan trọng trong module này: tự động duyệt các yêu cầu đang chờ theo đúng thứ tự công bằng khi có đủ điều kiện, tự động đánh dấu hoàn thành các lịch đã đến hoặc qua giờ kết thúc, và tự động hủy các yêu cầu đang chờ mà không còn khả năng được duyệt khi đã cận kề giờ sử dụng. Các tiến trình này thao tác trên dữ liệu với đầy đủ ràng buộc nghiệp vụ như một nhân viên thực hiện thủ công, chỉ khác ở việc không gắn với một tài khoản người dùng cụ thể đang đăng nhập.

---

## 3. QUY TRÌNH NGHIỆP VỤ TỔNG THỂ

### 3.1. Nguyên tắc thiết kế cốt lõi: không tự động duyệt tại thời điểm tạo

Điểm khác biệt quan trọng nhất của module này so với trực giác thông thường là: một yêu cầu đặt lịch mới **không bao giờ tự động được duyệt ngay tại thời điểm tạo**, kể cả khi kiểm tra đơn thuần cho thấy sức chứa của tiện ích tại khung giờ đó vẫn còn đủ chỗ cho riêng yêu cầu này. Mọi yêu cầu mới luôn được khởi tạo ở trạng thái Chờ duyệt, sau đó ngay lập tức được đưa vào cơ chế xử lý hàng đợi theo thứ tự công bằng tuyệt đối (FIFO) để xác định nó có đủ điều kiện được duyệt ngay hay không.

Lý do nghiệp vụ của nguyên tắc này: nếu hệ thống chỉ đơn thuần kiểm tra "còn đủ chỗ cho riêng yêu cầu mới hay không" mà bỏ qua các yêu cầu khác đang chờ duyệt cho cùng khung giờ giao nhau, một yêu cầu nhỏ (ví dụ chỉ 2 người) được tạo sau có thể "vượt mặt" một yêu cầu lớn hơn (ví dụ 8 người) đã được tạo trước đó nhưng chưa được duyệt do sức chứa chưa đủ cho yêu cầu lớn — điều này vi phạm nguyên tắc công bằng "ai đặt trước được ưu tiên xét duyệt trước". Do đó, mọi yêu cầu — dù kích thước nhỏ đến đâu — đều phải đi qua đúng một cơ chế xét duyệt hàng đợi duy nhất, xét theo đúng thứ tự thời điểm tạo.

### 3.2. Mô tả tổng thể quy trình

Vòng đời một yêu cầu đặt lịch trải qua các giai đoạn: tạo yêu cầu, xét duyệt theo hàng đợi công bằng (tự động hoặc thủ công), theo dõi và có thể sửa đổi trong lúc còn chờ duyệt, tiếp nhận thay đổi trạng thái (duyệt, từ chối, hủy), và kết thúc (hoàn thành hoặc hủy).

**Giai đoạn tạo yêu cầu.** Cư dân chọn tiện ích (chỉ những tiện ích đang hoạt động và được đánh dấu cho phép đặt trước mới hiển thị trong danh sách lựa chọn), chọn khung giờ bắt đầu/kết thúc và số người tham gia dự kiến. Hệ thống kiểm tra hàng loạt điều kiện hợp lệ về mặt thời gian (không đặt quá khứ, đúng khung giờ hoạt động của tiện ích, thời lượng hợp lý, phút chẵn 00/30), tính toán trước phí sử dụng dự kiến, rồi tạo bản ghi ở trạng thái Chờ duyệt.

**Giai đoạn xét duyệt theo hàng đợi.** Ngay sau khi tạo (trong cùng giao dịch cơ sở dữ liệu), hệ thống gom nhóm toàn bộ các yêu cầu đang chờ duyệt của cùng tiện ích có khung giờ giao nhau với yêu cầu vừa tạo (kể cả giao nhau gián tiếp qua một yêu cầu trung gian khác) thành một cụm duy nhất, sắp xếp các yêu cầu trong cụm theo đúng thứ tự thời điểm tạo (cũ nhất trước), rồi lần lượt xét duyệt từng yêu cầu theo đúng thứ tự đó cho đến khi gặp yêu cầu đầu tiên không còn đủ chỗ — tại đó dừng lại toàn bộ việc xét duyệt của cụm, các yêu cầu còn lại (kể cả yêu cầu có số người ít hơn, có thể tự nó đủ chỗ) tiếp tục ở trạng thái Chờ duyệt cho đến lượt xử lý kế tiếp.

Cơ chế xét duyệt hàng đợi này còn được kích hoạt lại bất cứ khi nào có chỗ trống mới phát sinh — cụ thể là khi một yêu cầu đã duyệt trước đó bị hủy, bị chuyển về chờ duyệt, hoặc được đánh dấu hoàn thành — nhằm đảm bảo các yêu cầu đang chờ được xét duyệt lại ngay lập tức thay vì phải chờ đến chu kỳ quét định kỳ tiếp theo.

**Giai đoạn theo dõi và sửa đổi.** Trong lúc yêu cầu còn ở trạng thái Chờ duyệt, cư dân có thể chủ động sửa lại khung giờ hoặc số người (hệ thống tính lại phí sử dụng tương ứng, không giữ nguyên vị trí trong hàng đợi theo nghĩa "ưu tiên", mà tính lại toàn bộ điều kiện từ đầu ở lần xét duyệt kế tiếp), hoặc chủ động hủy hoàn toàn nếu không còn nhu cầu.

**Giai đoạn thay đổi trạng thái thủ công.** Song song với cơ chế tự động, nhân viên Ban quản lý có quyền can thiệp thủ công bất cứ lúc nào: duyệt sớm một yêu cầu đang chờ (sau khi kiểm tra lại sức chứa tại đúng thời điểm duyệt), từ chối một yêu cầu (với lý do ghi nhận), chuyển một yêu cầu đã duyệt quay lại trạng thái chờ duyệt (trong trường hợp cần nhường chỗ cho một sự kiện ưu tiên khác), hoặc hủy một yêu cầu đã duyệt (tuân theo quy tắc thời gian tối thiểu trước giờ sử dụng).

**Giai đoạn kết thúc.** Một yêu cầu đã được duyệt tự động chuyển sang trạng thái Hoàn thành khi thời điểm kết thúc đã qua (do tiến trình tự động định kỳ xử lý), hoặc bị hủy trước đó bởi cư dân/nhân viên/hệ thống tự động (do không đủ chỗ khi cận giờ), hoặc bị từ chối trong lúc còn chờ duyệt.

### 3.3. Sơ đồ luồng nghiệp vụ tổng thể (dạng văn bản)

```
[Cư dân chọn tiện ích + khung giờ + số người]
        --> {Validate: khung giờ hợp lệ, tiện ích hoạt động và cho phép đặt trước,
             căn hộ thuộc cư dân, không đặt quá khứ, phút chẵn 00/30, cùng ngày,
             thời lượng 1-8 giờ, trong giờ mở/đóng cửa tiện ích}
              -- Không hợp lệ --> [Từ chối, báo lỗi cụ thể]
        --> [Tính phí sử dụng = so_nguoi x don_gia_gio x so_gio]
        --> [Tạo bản ghi, trạng thái = CHỜ DUYỆT, ghi Audit log, broadcast realtime]
        --> [Gom cụm các yêu cầu Chờ duyệt giao nhau khung giờ cho cùng tiện ích]
        --> [Sắp xếp cụm theo (thời điểm tạo ASC)]
        --> [Xét từng yêu cầu theo thứ tự: đủ chỗ (tính cả các yêu cầu ĐÃ DUYỆT giao nhau)?]
              -- Đủ chỗ --> [Duyệt yêu cầu này] --> [Xét tiếp yêu cầu kế trong cụm]
              -- Không đủ chỗ --> [DỪNG xét toàn bộ cụm tại đây]
        --> (Yêu cầu ở trạng thái CHỜ DUYỆT hoặc ĐÃ DUYỆT tùy kết quả xét)
        --> {Trong lúc chờ duyệt, cư dân/nhân viên có thể}
              --> [Sửa khung giờ/số người] --> (tính lại phí, quay lại bước xét hàng đợi)
              --> [Hủy] --> (kết thúc vòng đời, giải phóng chỗ nếu đang có ảnh hưởng)
              --> [Nhân viên: Duyệt thủ công] --> (kiểm tra lại sức chứa ngay lúc duyệt)
              --> [Nhân viên: Từ chối] --> (kết thúc vòng đời)
        --> {Đã duyệt, cư dân/nhân viên muốn hủy}
              --> {now() + 2 giờ > thời điểm bắt đầu ?}
                    -- Đúng (quá cận giờ) --> [Từ chối hủy]
                    -- Sai --> [Hủy thành công] --> [Giải phóng chỗ] --> [Xét lại hàng đợi ngay]
        --> {Đã duyệt, đến/qua giờ kết thúc}
              --> [Tiến trình tự động: chuyển HOÀN THÀNH] --> [Giải phóng chỗ] --> [Xét lại hàng đợi ngay]
        --> {Chờ duyệt, còn <= 2 giờ đến giờ bắt đầu, chưa đủ chỗ}
              --> [Tiến trình tự động: HỦY do không đủ sức chứa trước giờ sử dụng 2 tiếng]
```

---

## 4. QUY TẮC NGHIỆP VỤ (BUSINESS RULES)

### 4.1. Quy tắc về tạo và tính phí

**BR-01 — Công thức tính phí sử dụng.** Phí sử dụng của một yêu cầu đặt lịch được tính bằng công thức: số người tham gia nhân với đơn giá phí sử dụng trên giờ của tiện ích, nhân với số giờ sử dụng (được tính từ chênh lệch thời điểm kết thúc trừ thời điểm bắt đầu, quy đổi ra giờ, làm tròn xuống theo phút để khớp ngữ nghĩa tính theo phút trọn). Phí sử dụng luôn do hệ thống tự tính lại ở phía máy chủ tại mọi thời điểm tạo hoặc sửa yêu cầu, tuyệt đối không nhận giá trị phí tính sẵn được gửi từ phía người dùng.

**BR-02 — Chỉ tiện ích đang hoạt động và cho phép đặt trước mới nhận yêu cầu mới từ cư dân.** Một tiện ích có hai cờ độc lập kiểm soát khả năng được đặt: trạng thái hoạt động (một tiện ích ngừng hoạt động, ví dụ đang bảo trì, không nhận bất kỳ yêu cầu mới nào) và cờ cho phép đặt trước (một số tiện ích có thể được thiết lập chỉ sử dụng tự do không cần đặt lịch, không hiển thị trong luồng đặt lịch của cư dân). Cả hai điều kiện phải đồng thời thỏa mãn thì cư dân mới tạo được yêu cầu mới.

**BR-03 — Căn hộ trong yêu cầu đặt lịch phải thuộc chính cư dân đang thực hiện thao tác và đang cư trú hợp lệ.** Áp dụng riêng cho luồng cư dân tự đặt lịch, nhằm ngăn cư dân đặt lịch gán cho một căn hộ không phải của mình (ví dụ nhằm mục đích gian lận phí sử dụng hoặc gây nhầm lẫn trách nhiệm thanh toán).

### 4.2. Quy tắc về sức chứa và hàng đợi

**BR-04 — Tiện ích không giới hạn sức chứa luôn được duyệt ngay.** Nếu một tiện ích không khai báo giá trị sức chứa (hoặc khai báo bằng 0, được hiểu là không giới hạn), mọi yêu cầu hợp lệ cho tiện ích đó luôn được duyệt ngay lập tức tại thời điểm tạo, không cần qua bước xét hàng đợi.

**BR-05 — Điều kiện đủ chỗ.** Với tiện ích có giới hạn sức chứa cụ thể, một yêu cầu được coi là đủ chỗ để duyệt khi tổng số người của tất cả các yêu cầu đã ở trạng thái Đã duyệt có khung giờ giao nhau với yêu cầu đang xét, cộng thêm số người của chính yêu cầu đang xét, không vượt quá sức chứa của tiện ích. Hai khung giờ được coi là giao nhau khi thời điểm bắt đầu của khung này nhỏ hơn thời điểm kết thúc của khung kia và ngược lại (điều kiện giao nhau chuẩn của hai khoảng nửa mở).

**BR-06 — Nguyên tắc công bằng tuyệt đối theo thứ tự tạo (FIFO), áp dụng theo từng cụm khung giờ giao nhau độc lập.** Trong phạm vi một cụm các yêu cầu Chờ duyệt có khung giờ giao nhau (kể cả giao nhau gián tiếp, bắc cầu qua yêu cầu trung gian), thứ tự xét duyệt tuân thủ nghiêm ngặt thời điểm tạo yêu cầu (yêu cầu tạo trước được xét trước); việc xét duyệt của cả cụm dừng lại ngay khi gặp yêu cầu đầu tiên không đủ chỗ, các yêu cầu đứng sau trong cụm — dù có thể tự thân đủ chỗ nếu được xét riêng lẻ — vẫn phải tiếp tục chờ, không được phép "vượt mặt" yêu cầu đến trước. Các cụm khung giờ khác nhau (không giao nhau với nhau) được xét độc lập, không ảnh hưởng lẫn nhau.

**BR-07 — Kiểm tra lại sức chứa tại đúng thời điểm nhân viên duyệt thủ công.** Khi nhân viên chủ động duyệt một yêu cầu (không thông qua tiến trình tự động), hệ thống vẫn bắt buộc kiểm tra lại điều kiện đủ chỗ tại đúng thời điểm duyệt (không dựa vào kết quả kiểm tra sức chứa đã tính từ lúc tạo yêu cầu, vì tình trạng sức chứa có thể đã thay đổi do các yêu cầu khác được duyệt/hủy trong khoảng thời gian chờ), nếu không đủ chỗ thì từ chối thao tác duyệt và thông báo lý do cho nhân viên.

### 4.3. Quy tắc về sửa đổi

**BR-08 — Chỉ được sửa yêu cầu khi đang ở trạng thái Chờ duyệt.** Một yêu cầu đã được duyệt, đã bị từ chối, đã hủy, hoặc đã hoàn thành không thể được sửa đổi khung giờ hay số người dưới bất kỳ hình thức nào; muốn thay đổi, cư dân phải hủy yêu cầu cũ (nếu còn hủy được) và tạo yêu cầu mới.

**BR-09 — Sửa yêu cầu không làm thay đổi trạng thái ngay lập tức.** Khi một yêu cầu đang Chờ duyệt được sửa (khung giờ hoặc số người), hệ thống chỉ tính lại phí sử dụng tương ứng, không tự động chuyển trạng thái sang Đã duyệt dù sau khi sửa yêu cầu có vẻ như đủ chỗ; việc xét duyệt lại vẫn phải đi qua đúng chu trình hàng đợi công bằng ở lượt xử lý kế tiếp, đảm bảo việc sửa đổi không trở thành một cách gián tiếp "vượt hàng".

### 4.4. Quy tắc về hủy, từ chối, chuyển trạng thái

**BR-10 — Tính idempotent của các thao tác chuyển trạng thái.** Nếu một hành động chuyển trạng thái (duyệt, từ chối, hủy, hoàn thành) được gọi lại trong khi yêu cầu đã ở đúng trạng thái đích của hành động đó, hệ thống coi đây là thao tác không cần thực hiện gì thêm (no-op) và trả về kết quả thành công, không phát sinh lỗi, không ghi nhật ký trùng lặp, không phát thông báo thời gian thực trùng lặp. Ngược lại, nếu yêu cầu đang ở một trạng thái nguồn không hợp lệ để chuyển sang trạng thái đích của hành động đang gọi (ví dụ cố duyệt một yêu cầu đã bị từ chối), hệ thống từ chối với thông báo lỗi nghiệp vụ rõ ràng.

**BR-11 — Quy tắc hai giờ khi hủy yêu cầu đã được duyệt.** Một yêu cầu đã ở trạng thái Đã duyệt chỉ được phép hủy (bởi cư dân, nhân viên, hoặc Admin — áp dụng như nhau cho mọi tác nhân, không có ngoại lệ theo vai trò) nếu thời điểm hiện tại còn cách thời điểm bắt đầu sử dụng tối thiểu hai giờ đồng hồ; nếu thời điểm hiện tại đã nằm trong phạm vi hai giờ trước giờ sử dụng, yêu cầu hủy bị từ chối. Quy tắc này không áp dụng cho yêu cầu đang ở trạng thái Chờ duyệt — yêu cầu Chờ duyệt có thể được hủy bất kỳ lúc nào, kể cả khi đã rất cận giờ sử dụng, vì tiến trình tự động hủy quá hạn (Mục 5.4) đã đảm nhiệm việc xử lý các yêu cầu Chờ duyệt cận giờ không còn khả năng được duyệt.

**BR-12 — Lý do hủy khi cư dân tự hủy được cố định, không tự do nhập.** Khi cư dân chủ động hủy yêu cầu của chính mình, hệ thống ghi nhận lý do hủy bằng một giá trị cố định định sẵn (thể hiện đây là hành động tự nguyện của cư dân), không cho phép cư dân nhập tự do nội dung lý do hủy, nhằm tránh việc ghi vào nhật ký hệ thống những nội dung tùy tiện không kiểm soát được.

**BR-13 — Giải phóng chỗ kích hoạt xét duyệt lại hàng đợi ngay lập tức.** Bất cứ khi nào một yêu cầu rời khỏi trạng thái Đã duyệt (do bị hủy, bị chuyển về chờ duyệt, hoặc được đánh dấu hoàn thành), hệ thống ngay lập tức kích hoạt lại việc xét duyệt hàng đợi cho tiện ích và khung giờ liên quan trong cùng giao dịch, không chờ đến chu kỳ quét định kỳ tiếp theo của tiến trình tự động, đảm bảo chỗ trống được tận dụng sớm nhất có thể cho các yêu cầu đang chờ.

### 4.5. Quy tắc về tự động hóa (Scheduler)

**BR-14 — Tự động duyệt theo hàng đợi công bằng.** Định kỳ, hệ thống tự động quét toàn bộ các tiện ích đang có yêu cầu Chờ duyệt và áp dụng lại đúng cơ chế xét duyệt hàng đợi công bằng (BR-06) — đây là biện pháp đảm bảo dự phòng, xử lý các trường hợp mà việc xét duyệt tại thời điểm tạo/giải phóng chỗ (BR-13) có thể không bao quát hết (ví dụ chỗ trống phát sinh do nguyên nhân khác ngoài các hành động chuyển trạng thái thông thường).

**BR-15 — Tự động đánh dấu hoàn thành.** Định kỳ, hệ thống tự động chuyển mọi yêu cầu đang ở trạng thái Đã duyệt mà thời điểm kết thúc đã trôi qua sang trạng thái Hoàn thành, không cần chờ nhân viên xác nhận thủ công.

**BR-16 — Tự động hủy yêu cầu quá hạn không đủ chỗ, có xét đến từng bước trong cụm.** Định kỳ, với các tiện ích có yêu cầu Chờ duyệt mà thời điểm bắt đầu sử dụng còn cách hiện tại không quá hai giờ, hệ thống gom cụm và xét từng yêu cầu theo đúng thứ tự tạo (giống BR-06), nhưng với cách xử lý khác biệt quan trọng khi gặp yêu cầu không đủ chỗ: nếu yêu cầu đó đã cận giờ (nằm trong phạm vi hai giờ), yêu cầu bị hủy tự động và **việc xét duyệt tiếp tục với yêu cầu kế tiếp trong cụm** (không dừng hẳn cụm như cách xét duyệt thông thường ở BR-06), vì yêu cầu kế tiếp có thể có số người ít hơn và vừa đủ chỗ sau khi yêu cầu không đủ chỗ đã bị loại khỏi cụm; ngược lại, nếu yêu cầu không đủ chỗ nhưng còn hơn hai giờ nữa mới đến giờ sử dụng, việc xét duyệt dừng hẳn tại đó, giữ đúng nguyên tắc công bằng FIFO cho các yêu cầu chưa đến hạn xử lý khẩn cấp.

**BR-17 — Ba tiến trình tự động chạy độc lập, không chồng lấn.** Ba tiến trình (tự động duyệt, tự động hoàn thành, tự động hủy quá hạn) chạy định kỳ độc lập với nhau, mỗi tiến trình được thiết lập cơ chế chống chồng lấn (không cho phép một phiên chạy mới bắt đầu khi phiên trước của cùng tiến trình đó chưa kết thúc), tránh tình trạng cùng một tập dữ liệu bị nhiều phiên xử lý đồng thời gây xung đột hoặc xử lý trùng lặp.

---

## 5. LUỒNG XỬ LÝ CHÍNH (MAIN FLOW)

### 5.1. Luồng cư dân tạo yêu cầu đặt lịch

**Bước 1.** Cư dân mở màn hình đặt lịch, chọn tiện ích từ danh sách các tiện ích đang hoạt động và cho phép đặt trước.

**Bước 2.** Cư dân chọn khung giờ bắt đầu và kết thúc mong muốn, hệ thống kiểm tra ngay tại giao diện các điều kiện: phút bắt đầu/kết thúc chỉ được 00 hoặc 30; không đặt trong quá khứ; bắt đầu và kết thúc phải cùng ngày; thời lượng từ 1 đến 8 giờ; nằm trong khung giờ mở cửa/đóng cửa của tiện ích (nếu tiện ích có khai báo).

**Bước 3.** Cư dân xem tình trạng sức chứa còn lại của tiện ích tại khung giờ đã chọn (hệ thống cung cấp API tra cứu sức chứa thời gian thực để cư dân tham khảo trước khi xác nhận, không ghi dữ liệu), nhập số người tham gia dự kiến, hệ thống hiển thị phí sử dụng dự tính.

**Bước 4.** Cư dân xác nhận đặt lịch. Hệ thống khóa độc quyền bản ghi tiện ích, kiểm tra tiện ích đang hoạt động (BR-02), kiểm tra căn hộ thuộc đúng cư dân đang cư trú (BR-03), tính lại phí sử dụng ở phía máy chủ (BR-01), sinh mã đặt lịch duy nhất theo định dạng ngày tháng kèm số thứ tự (tự động thử lại tối đa ba lần nếu phát sinh trùng lặp mã hiếm gặp do đồng thời), tạo bản ghi ở trạng thái Chờ duyệt.

**Bước 5.** Trong cùng giao dịch, hệ thống ghi nhật ký hệ thống, phát thông báo thời gian thực cho cư dân và cho nhân viên trực (yêu cầu mới đã được tạo, số lượng đang chờ duyệt của tiện ích đã thay đổi), sau đó gọi ngay cơ chế xử lý hàng đợi công bằng cho đúng tiện ích và khung giờ liên quan để xác định yêu cầu vừa tạo có được duyệt ngay hay không.

**Bước 6.** Cư dân nhận được kết quả gần như tức thời qua kênh thời gian thực: hoặc yêu cầu đã chuyển sang Đã duyệt ngay (nếu đủ chỗ và đứng đầu hàng đợi hoặc tiện ích không giới hạn sức chứa), hoặc vẫn ở trạng thái Chờ duyệt (nếu chưa đủ chỗ hoặc còn yêu cầu khác đứng trước chưa được xử lý).

### 5.2. Luồng nhân viên duyệt thủ công

**Bước 1.** Nhân viên mở danh sách yêu cầu đang Chờ duyệt (có thể lọc theo tiện ích, căn hộ, ngày sử dụng), xem chi tiết một yêu cầu cụ thể.

**Bước 2.** Nhân viên chọn duyệt. Hệ thống khóa độc quyền lần lượt bản ghi tiện ích rồi đến bản ghi yêu cầu đặt lịch (theo đúng một thứ tự cố định để tránh deadlock khi có nhiều thao tác đồng thời), kiểm tra yêu cầu đang ở trạng thái Chờ duyệt (áp dụng BR-10, nếu đã Đã duyệt thì coi như thành công không cần làm gì thêm), kiểm tra lại điều kiện đủ chỗ tại đúng thời điểm này (BR-07).

**Bước 3.** Nếu đủ chỗ, chuyển trạng thái sang Đã duyệt, ghi nhận nhân viên duyệt và thời điểm duyệt, ghi nhật ký hệ thống, phát thông báo thời gian thực cho cư dân sở hữu yêu cầu.

**Bước 4.** Nếu không đủ chỗ, hệ thống từ chối thao tác duyệt, hiển thị thông báo lỗi cho nhân viên giải thích lý do (không đủ sức chứa), không thay đổi trạng thái yêu cầu.

### 5.3. Luồng tự động duyệt theo hàng đợi (Scheduler)

**Bước 1.** Tiến trình tự động chạy định kỳ mỗi phút, quét toàn bộ các tiện ích hiện đang có ít nhất một yêu cầu ở trạng thái Chờ duyệt.

**Bước 2.** Với mỗi tiện ích, gom nhóm các yêu cầu Chờ duyệt thành các cụm theo khung giờ giao nhau (thuật toán gộp khoảng giao nhau, xử lý cả trường hợp giao nhau bắc cầu qua yêu cầu trung gian).

**Bước 3.** Với mỗi cụm, sắp xếp theo thời điểm tạo tăng dần, xét tuần tự từng yêu cầu: nếu đủ chỗ (tính cả các yêu cầu Đã duyệt khác đang giao nhau khung giờ), duyệt yêu cầu này và tiếp tục xét yêu cầu kế tiếp trong cụm; nếu không đủ chỗ, dừng lại toàn bộ việc xét cụm này, chuyển sang xử lý cụm/tiện ích tiếp theo.

**Bước 4.** Mọi thay đổi trạng thái trong tiến trình này đều được thực hiện trong giao dịch có cơ chế tự động thử lại tối đa ba lần nếu phát sinh xung đột khóa (deadlock) với các giao dịch khác đang chạy đồng thời (ví dụ một nhân viên đang thao tác thủ công trên cùng dữ liệu), ghi nhật ký hệ thống và phát thông báo thời gian thực cho từng yêu cầu được duyệt.

### 5.4. Luồng tự động hủy quá hạn (Scheduler)

**Bước 1.** Tiến trình tự động chạy định kỳ mỗi phút, xác định các tiện ích có yêu cầu Chờ duyệt mà thời điểm bắt đầu sử dụng còn cách hiện tại không quá hai giờ.

**Bước 2.** Với mỗi tiện ích liên quan, gom cụm và sắp xếp theo thời điểm tạo giống Mục 5.3, nhưng tại mỗi bước xét lại toàn bộ sức chứa hiện tại (không dùng số liệu đã tính từ bước trước, vì trạng thái có thể vừa thay đổi ngay trong chính lượt xử lý này).

**Bước 3.** Với yêu cầu đủ chỗ: duyệt ngay và tiếp tục xét yêu cầu kế tiếp trong cụm (áp dụng BR-16).

**Bước 4.** Với yêu cầu không đủ chỗ: nếu đã nằm trong phạm vi hai giờ trước giờ sử dụng, hủy yêu cầu với lý do cố định thể hiện việc hệ thống tự động hủy do không đủ sức chứa trước giờ sử dụng hai tiếng, ghi nhật ký, phát thông báo cho cư dân, rồi **vẫn tiếp tục xét yêu cầu kế tiếp trong cụm** (không dừng cụm, vì yêu cầu tiếp theo có thể ít người hơn và vừa đủ chỗ sau khi yêu cầu vừa hủy đã được loại ra); nếu còn hơn hai giờ nữa (trường hợp hiếm trong tiến trình này do điều kiện lọc ở Bước 1 đã giới hạn phạm vi hai giờ, nhưng có thể xảy ra với các yêu cầu ở cuối cụm gắn với một yêu cầu khác trong cụm có giờ bắt đầu xa hơn), dừng hẳn việc xét cụm tại đó.

### 5.5. Luồng tự động hoàn thành (Scheduler)

**Bước 1.** Tiến trình tự động chạy định kỳ mỗi phút, tìm mọi yêu cầu đang ở trạng thái Đã duyệt mà thời điểm kết thúc đã trôi qua so với hiện tại.

**Bước 2.** Với mỗi yêu cầu, chuyển trạng thái sang Hoàn thành (áp dụng tính idempotent BR-10 nếu vì lý do nào đó yêu cầu đã ở trạng thái này), ghi nhật ký hệ thống.

**Bước 3.** Ngay sau khi hoàn thành một yêu cầu (giải phóng chỗ mà yêu cầu đó đang chiếm dụng), hệ thống kích hoạt lại việc xét duyệt hàng đợi cho đúng tiện ích và khung giờ liên quan (BR-13), giúp các yêu cầu Chờ duyệt kế cận có cơ hội được duyệt ngay khi chỗ vừa được giải phóng, không cần chờ đến lượt chạy định kỳ tiếp theo của tiến trình tự động duyệt.

---

## 6. LUỒNG THAY THẾ (ALTERNATE FLOWS)

**AF-01 — Đặt lịch trùng thời gian, hết chỗ ngay tại thời điểm tạo.** Cư dân tạo yêu cầu cho một khung giờ mà tổng số người của các yêu cầu Đã duyệt hiện tại đã gần chạm sức chứa; yêu cầu vẫn được tạo thành công ở trạng thái Chờ duyệt (không bị từ chối tạo), nhưng bước xét duyệt hàng đợi ngay sau đó xác định chưa đủ chỗ (hoặc có yêu cầu khác đứng trước trong cụm chưa được xử lý), yêu cầu tiếp tục ở trạng thái Chờ duyệt cho đến khi có chỗ trống phát sinh hoặc đến lượt xử lý của tiến trình tự động.

**AF-02 — Tiện ích hết chỗ hoàn toàn tại khung giờ đã chọn.** Nếu sức chứa đã bị lấp đầy bởi các yêu cầu Đã duyệt khác cho cùng khung giờ, cư dân vẫn có thể tạo yêu cầu (hệ thống không chặn cứng việc tạo chỉ vì hết chỗ, vì có khả năng một yêu cầu Đã duyệt khác sẽ bị hủy trước giờ sử dụng, giải phóng chỗ cho yêu cầu Chờ duyệt này), nhưng cư dân cần được cảnh báo rõ ràng tại giao diện tạo yêu cầu (thông qua API tra cứu sức chứa thời gian thực ở Bước 3, Mục 5.1) rằng khả năng được duyệt hiện tại là thấp, để cư dân cân nhắc chọn khung giờ khác nếu cần chắc chắn được sử dụng.

**AF-03 — Cư dân sửa yêu cầu đang chờ duyệt sang khung giờ khác.** Cư dân đổi ý về khung giờ trong lúc yêu cầu còn Chờ duyệt; hệ thống áp dụng lại toàn bộ validate về thời gian (Bước 2, Mục 5.1) cho khung giờ mới, tính lại phí sử dụng, và yêu cầu quay lại đúng chu trình xét duyệt hàng đợi thông thường ở khung giờ mới tại lượt xử lý kế tiếp — không có bất kỳ ưu tiên đặc biệt nào được giữ lại từ vị trí trong hàng đợi cũ.

**AF-04 — Nhân viên chuyển một yêu cầu đã duyệt về lại chờ duyệt.** Áp dụng khi Ban quản lý cần nhường chỗ đã duyệt cho một nhu cầu ưu tiên khác (ví dụ sự kiện nội bộ đột xuất của tòa nhà). Sau khi chuyển về Chờ duyệt, chỗ mà yêu cầu này đang chiếm dụng được giải phóng, kích hoạt ngay việc xét lại hàng đợi (BR-13) — có thể dẫn đến việc chính yêu cầu vừa bị chuyển về chờ duyệt lại được duyệt trở lại ngay lập tức nếu không có yêu cầu nào khác đứng trước nó trong cụm, hoặc một yêu cầu khác đang chờ được duyệt thay vào chỗ đó.

**AF-05 — Người dùng hủy yêu cầu đang chờ duyệt (không áp dụng quy tắc hai giờ).** Cư dân hoặc nhân viên hủy một yêu cầu còn đang Chờ duyệt vào bất kỳ thời điểm nào, kể cả khi chỉ còn vài phút đến giờ sử dụng dự kiến — quy tắc hai giờ (BR-11) chỉ áp dụng cho yêu cầu đã Đã duyệt. Việc hủy này không giải phóng chỗ đã duyệt của người khác (vì yêu cầu đang hủy chưa từng chiếm chỗ Đã duyệt), nhưng vẫn kích hoạt xét lại hàng đợi cho các yêu cầu Chờ duyệt còn lại trong cùng cụm, vì việc loại một yêu cầu ra khỏi cụm có thể thay đổi kết quả xét duyệt của các yêu cầu còn lại.

**AF-06 — Hai cư dân cùng thao tác gần như đồng thời trên cùng tiện ích, cùng khung giờ.** Hai yêu cầu được gửi gần như cùng lúc cho cùng tiện ích, khung giờ giao nhau. Nhờ cơ chế khóa độc quyền bản ghi tiện ích trước khi xử lý tạo mới (Bước 4, Mục 5.1), hai yêu cầu được xử lý tuần tự chứ không song song thực sự; yêu cầu được xử lý trước có thời điểm tạo sớm hơn (dù chỉ chênh nhau vài phần nghìn giây) và do đó được xếp trước trong cụm hàng đợi, yêu cầu xử lý sau xếp sau — kết quả cuối cùng vẫn tuân thủ đúng nguyên tắc công bằng theo thời điểm tạo, không có tình huống cả hai cùng được duyệt vượt quá sức chứa cho phép.

**AF-07 — Tiện ích bị chuyển sang ngừng hoạt động trong khi vẫn còn yêu cầu Chờ duyệt hoặc Đã duyệt trong tương lai.** Việc ngừng hoạt động một tiện ích chỉ chặn việc tạo yêu cầu mới (BR-02), không tự động hủy các yêu cầu đã tồn tại từ trước; Ban quản lý cần chủ động rà soát và xử lý (từ chối hoặc hủy kèm thông báo lý do phù hợp) các yêu cầu hiện có liên quan đến tiện ích sắp ngừng hoạt động, đây là bước thao tác thủ công cần thiết đi kèm khi quyết định ngừng hoạt động một tiện ích có ảnh hưởng đến lịch đã đặt trong tương lai.

---

## 7. LUỒNG NGOẠI LỆ (EXCEPTION FLOWS)

**EF-01 — Không tìm thấy tiện ích hoặc yêu cầu đặt lịch.** Truy cập vào một tiện ích hoặc một yêu cầu đặt lịch không tồn tại (hoặc đã bị xóa mềm) trả về lỗi không tìm thấy tài nguyên trước khi bất kỳ logic nghiệp vụ nào được thực thi.

**EF-02 — Sai trạng thái nghiệp vụ khi chuyển đổi trạng thái.** Cố duyệt một yêu cầu đã bị từ chối, cố từ chối một yêu cầu đã hoàn thành, cố sửa một yêu cầu đã duyệt... — mọi tổ hợp chuyển trạng thái không hợp lệ theo máy trạng thái đã định nghĩa (Mục 9) đều bị từ chối với thông báo lỗi nghiệp vụ cụ thể, phân biệt rõ với trường hợp gọi lại hành động đã ở đúng trạng thái đích (được xử lý idempotent theo BR-10, không phải lỗi).

**EF-03 — Không đủ quyền.** Cư dân cố duyệt/từ chối yêu cầu (kể cả yêu cầu của chính mình) bị từ chối vì đây không thuộc quyền hạn của cư dân theo Mục 2.3; cư dân cố thao tác trên yêu cầu không thuộc căn hộ mình cư trú bị từ chối truy cập; Manager cố thao tác trên yêu cầu ngoài phạm vi quản lý được phân công bị chặn tương tự nguyên tắc Scope đã áp dụng xuyên suốt hệ thống.

**EF-04 — Xung đột khóa (deadlock) khi nhiều thao tác đồng thời cùng chạm vào một cụm dữ liệu.** Khi tiến trình tự động và một thao tác thủ công của nhân viên cùng cố gắng khóa các bản ghi liên quan theo thứ tự khác nhau, cơ sở dữ liệu có thể phát hiện và chủ động hủy một trong hai giao dịch để phá vỡ deadlock; hệ thống có cơ chế tự động thử lại giao dịch bị hủy tối đa ba lần trước khi thực sự báo lỗi cho người dùng, giảm thiểu đáng kể khả năng người dùng nhìn thấy lỗi kỹ thuật này trong thực tế vận hành.

**EF-05 — Lỗi hệ thống trong quá trình xử lý hàng đợi tự động.** Nếu tiến trình tự động gặp lỗi không mong muốn khi xử lý một tiện ích cụ thể (ví dụ dữ liệu bất thường), lỗi cần được cô lập ở phạm vi tiện ích đang xử lý (không để một tiện ích lỗi làm dừng toàn bộ tiến trình quét các tiện ích khác), ghi nhận vào nhật ký kỹ thuật để điều tra, tiếp tục xử lý các tiện ích còn lại trong cùng lượt chạy.

**EF-06 — Sinh mã đặt lịch bị trùng do đồng thời.** Trong trường hợp cực hiếm hai yêu cầu được tạo cùng một thời điểm sinh ra cùng một mã đặt lịch dự kiến (do cùng ngày và cùng số thứ tự ngẫu nhiên trùng lặp), hệ thống bắt lỗi vi phạm ràng buộc duy nhất ở tầng cơ sở dữ liệu và tự động thử sinh lại mã mới, lặp lại tối đa ba lần; nếu vẫn thất bại sau ba lần thử (xác suất cực thấp trong thực tế vận hành), hệ thống báo lỗi kỹ thuật và đề nghị người dùng thử lại thao tác tạo yêu cầu.

---

## 8. VALIDATION

| Trường/Đối tượng | Điều kiện kiểm tra | Áp dụng tại |
|---|---|---|
| Tiện ích | Bắt buộc chọn, phải tồn tại, đang hoạt động, cho phép đặt trước (đối với luồng cư dân) | Tạo yêu cầu |
| Căn hộ | Bắt buộc, phải thuộc chính cư dân đang thao tác và đang cư trú hợp lệ (luồng cư dân) | Tạo yêu cầu |
| Phút bắt đầu/kết thúc | Chỉ được 00 hoặc 30 | Tạo, sửa yêu cầu |
| Thời điểm bắt đầu | Không được nằm trong quá khứ so với hiện tại | Tạo, sửa yêu cầu |
| Ngày bắt đầu và kết thúc | Phải cùng một ngày | Tạo, sửa yêu cầu |
| Thời lượng sử dụng | Tối thiểu 1 giờ, tối đa 8 giờ | Tạo, sửa yêu cầu |
| Khung giờ đã chọn | Phải nằm trong khung giờ mở cửa - đóng cửa của tiện ích (nếu tiện ích có khai báo) | Tạo, sửa yêu cầu |
| Số người tham gia | Số nguyên dương, lớn hơn 0 | Tạo, sửa yêu cầu |
| Phí sử dụng | Luôn tính lại ở máy chủ, không nhận giá trị từ client | Tạo, sửa yêu cầu |
| Mã đặt lịch | Duy nhất trên toàn hệ thống, tự sinh, không nhận từ người dùng | Tạo yêu cầu |
| Ghi chú | Tối đa 500 ký tự, không bắt buộc | Tạo, sửa yêu cầu |
| Lý do từ chối/hủy (nhân viên) | Tối đa 500 ký tự | Từ chối, hủy (nhân viên) |
| Trạng thái nguồn khi chuyển đổi | Phải khớp đúng trạng thái nguồn hợp lệ của hành động đang thực hiện (xem Mục 9) | Duyệt, từ chối, hủy, hoàn thành |
| Thời điểm hủy so với giờ sử dụng (yêu cầu Đã duyệt) | Còn tối thiểu 2 giờ trước thời điểm bắt đầu | Hủy yêu cầu đã duyệt |
| Sức chứa tại thời điểm duyệt | Tổng số người Đã duyệt giao nhau khung giờ cộng yêu cầu đang xét không vượt sức chứa tiện ích | Duyệt (thủ công và tự động) |

---

## 9. TRẠNG THÁI DỮ LIỆU

### 9.1. Năm trạng thái của một yêu cầu đặt lịch

Một yêu cầu đặt lịch tiện ích có đúng năm giá trị trạng thái: (1) Chờ duyệt — trạng thái khởi tạo mặc định của mọi yêu cầu mới; (2) Đã duyệt — yêu cầu đã được xác nhận đủ chỗ và có hiệu lực sử dụng; (3) Từ chối — yêu cầu bị nhân viên từ chối trong lúc còn Chờ duyệt; (4) Đã hủy — yêu cầu bị hủy bởi cư dân, nhân viên hoặc hệ thống tự động; (5) Hoàn thành — yêu cầu đã Đã duyệt và đã qua thời điểm kết thúc sử dụng.

### 9.2. Điều kiện chuyển trạng thái hợp lệ

| Từ trạng thái | Sang trạng thái | Điều kiện | Tác nhân |
|---|---|---|---|
| (Mới tạo) | Chờ duyệt | Luôn luôn — không có yêu cầu nào được tạo trực tiếp ở trạng thái khác | Cư dân, Nhân viên |
| Chờ duyệt | Đã duyệt | Đủ chỗ theo BR-05, đến lượt trong hàng đợi theo BR-06 | Hệ thống tự động, Nhân viên |
| Chờ duyệt | Từ chối | Nhân viên chủ động từ chối, kèm lý do | Nhân viên |
| Chờ duyệt | Đã hủy | Không điều kiện thời gian (có thể hủy bất kỳ lúc nào) | Cư dân, Nhân viên, Hệ thống tự động (khi cận giờ, không đủ chỗ) |
| Đã duyệt | Chờ duyệt | Nhân viên chủ động chuyển về (nhường chỗ) | Nhân viên |
| Đã duyệt | Đã hủy | Còn tối thiểu 2 giờ trước giờ bắt đầu (BR-11) | Cư dân, Nhân viên |
| Đã duyệt | Hoàn thành | Thời điểm kết thúc đã qua so với hiện tại | Hệ thống tự động |
| Từ chối | (không chuyển tiếp) | Trạng thái kết thúc, không có luồng nghiệp vụ nào chuyển tiếp từ đây | — |
| Đã hủy | (không chuyển tiếp) | Trạng thái kết thúc | — |
| Hoàn thành | (không chuyển tiếp) | Trạng thái kết thúc | — |

### 9.3. Sơ đồ trạng thái (State Diagram) dạng văn bản

```
                       [Tạo yêu cầu mới]
                              |
                              v
                     +----------------+
        +----------->|   CHỜ DUYỆT    |<-----------+
        |            +----------------+            |
        |              |     |      |               |
        |     (đủ chỗ, |     |      | (nhân viên     | (nhân viên chuyển
        |     đến lượt |     |      |  từ chối)      |  về, nhường chỗ)
        |     FIFO)    |     |      v                |
        |              |     |  +----------+          |
        |              |     |  | TỪ CHỐI  | (kết thúc)
        |              |     |  +----------+
        |              v     |
        |        +-----------+                        
        |        | ĐÃ DUYỆT  |----------------------->+
        |        +-----------+   (hủy, còn >=2 giờ)
        |              |
        |   (đã qua giờ kết thúc, tự động)
        |              v
        |        +-------------+
        |        | HOÀN THÀNH  | (kết thúc)
        |        +-------------+
        |
        | (hủy bất kỳ lúc nào từ Chờ duyệt: cư dân/NV chủ động,
        |  HOẶC hệ thống tự động khi <=2 giờ mà không đủ chỗ)
        v
   +-----------+
   | ĐÃ HỦY    | (kết thúc)
   +-----------+
```

### 9.4. Trạng thái của tiện ích (đối tượng tài nguyên)

Độc lập với trạng thái của từng yêu cầu đặt lịch, bản thân tiện ích có hai trạng thái: Hoạt động và Ngừng hoạt động, do Ban quản lý chủ động thiết lập, không tự động chuyển đổi theo bất kỳ sự kiện nghiệp vụ nào khác (không có cơ chế tự động ngừng hoạt động một tiện ích dựa trên số lượng yêu cầu hay bất kỳ tiêu chí vận hành nào).

---

## 10. NHẬT KÝ HỆ THỐNG (AUDIT LOG)

Mọi thao tác làm thay đổi trạng thái của một yêu cầu đặt lịch — tạo mới, sửa đổi khung giờ/số người, duyệt, từ chối, hủy, chuyển về chờ duyệt, hoàn thành, xóa, khôi phục — đều được ghi vào nhật ký hệ thống dùng chung với Module 1, bao gồm người thực hiện (để trống nếu do chính cư dân thực hiện, hoặc để trống/đánh dấu đặc biệt nếu do tiến trình tự động thực hiện, vì các tiến trình tự động không gắn với một tài khoản nhân viên cụ thể đang đăng nhập), thời điểm, loại hành động, tên bảng bị tác động và toàn bộ giá trị trước/sau của bản ghi liên quan.

Việc ghi nhật ký được thực hiện nhất quán bất kể hành động xuất phát từ thao tác thủ công của nhân viên hay từ tiến trình tự động định kỳ — đây là điểm quan trọng đảm bảo khả năng truy vết đầy đủ vòng đời của một yêu cầu đặt lịch, kể cả khi phần lớn các thay đổi trạng thái trong thực tế vận hành có thể xuất phát từ tiến trình tự động chứ không phải từ một nhân viên đang trực tiếp thao tác. Đối với các trường hợp tự động hủy do không đủ sức chứa trước giờ sử dụng, nội dung lý do hủy được ghi nhận đầy đủ trong nhật ký, tạo cơ sở minh bạch để giải quyết khiếu nại của cư dân nếu có, cho phép tra cứu lại chính xác lý do và thời điểm hệ thống đưa ra quyết định tự động.

---

## 11. THÔNG BÁO

Khác biệt căn bản với Module 1 và Module 2, module Đặt lịch tiện ích là module duy nhất trong ba module của tài liệu có tích hợp cơ chế thông báo thời gian thực (qua kênh WebSocket) thay vì chỉ dựa vào việc người dùng chủ động tải lại trang để xem trạng thái mới. Toàn bộ việc phát thông báo được tập trung qua một điểm truy cập duy nhất, đảm bảo không có sự kiện phát thông báo nào được thực hiện trước khi giao dịch cơ sở dữ liệu liên quan thực sự được xác nhận hoàn tất (tránh tình huống thông báo báo đã duyệt/đã hủy trong khi giao dịch sau đó lại bị hủy bỏ do lỗi phát sinh).

Các sự kiện được phát thông báo thời gian thực bao gồm: yêu cầu đặt lịch mới được tạo, yêu cầu được duyệt, yêu cầu bị từ chối, yêu cầu bị hủy, yêu cầu được đánh dấu hoàn thành, cùng với các sự kiện cập nhật số liệu tổng hợp phục vụ hiển thị giao diện theo thời gian thực — số lượng yêu cầu đang chờ duyệt của một tiện ích thay đổi, và tình trạng chỗ trống của tiện ích tại một khung giờ thay đổi (giúp giao diện đặt lịch của cư dân khác đang mở cùng lúc tự động cập nhật lại số chỗ còn trống mà không cần tải lại trang).

Kênh phát thông báo được phân tách theo đối tượng nhận: một kênh dành riêng cho từng cư dân (chỉ nhận được thông báo liên quan đến chính yêu cầu của mình, có xác thực đúng cư dân sở hữu yêu cầu mới được phép lắng nghe kênh này), và một kênh dành cho toàn bộ nhân viên trực (nhận toàn bộ thông báo về mọi yêu cầu mới/thay đổi trạng thái, phục vụ nhân viên theo dõi và can thiệp kịp thời khi cần).

Về kênh email: tại thời điểm biên soạn tài liệu, module chưa tích hợp gửi email xác nhận cho các sự kiện nêu trên; cư dân nhận biết đầy đủ thông tin qua kênh thời gian thực khi đang mở ứng dụng, hoặc qua việc chủ động vào lại mục đặt lịch của mình để xem trạng thái mới nhất nếu bỏ lỡ thông báo thời gian thực (ví dụ do đóng ứng dụng vào đúng lúc thông báo được phát). Đây là điểm cần cân nhắc bổ sung trong định hướng phát triển tiếp theo, đặc biệt đối với sự kiện tự động hủy do hết hạn hai giờ — cư dân có thể không biết yêu cầu của mình đã bị hủy tự động nếu không đang mở ứng dụng vào đúng thời điểm đó.

---

## 12. PHÂN QUYỀN

Ai được xem: Admin xem toàn bộ trên phạm vi hệ thống; Manager xem trong phạm vi quản lý được phân công; cư dân chỉ xem yêu cầu đặt lịch của chính mình.

Ai được tạo: cư dân tạo yêu cầu cho căn hộ mình đang cư trú; nhân viên (Admin/Manager) có thể tạo yêu cầu thay mặt (hỗ trợ tại quầy).

Ai được sửa: chỉ chủ sở hữu yêu cầu (cư dân) hoặc nhân viên trong phạm vi quản lý được sửa, và chỉ khi yêu cầu đang ở trạng thái Chờ duyệt (BR-08).

Ai được xóa (xóa mềm): thuộc thẩm quyền nhân viên (Admin/Manager) trong phạm vi quản lý, áp dụng cho các trường hợp cần loại bỏ hoàn toàn một bản ghi khỏi danh sách hiển thị thông thường (khác với hủy — vốn vẫn giữ nguyên bản ghi ở trạng thái Đã hủy để tra cứu lịch sử); nhân viên cũng có quyền khôi phục bản ghi đã xóa mềm khi cần.

Ai được duyệt/từ chối: chỉ nhân viên (Admin/Manager) trong phạm vi quản lý được phân công có quyền duyệt hoặc từ chối một yêu cầu; cư dân không có quyền này dưới bất kỳ hình thức nào, kể cả với yêu cầu của chính mình.

Ai được hủy: chủ sở hữu yêu cầu (cư dân), nhân viên trong phạm vi quản lý, và hệ thống tự động (đối với trường hợp quá hạn không đủ chỗ) — áp dụng đồng nhất quy tắc hai giờ (BR-11) cho yêu cầu Đã duyệt bất kể tác nhân nào thực hiện việc hủy.

Ai được quản lý danh mục tiện ích (tạo/sửa/xóa tiện ích, loại tiện ích): chỉ Admin và Manager trong phạm vi quản lý được phân công; cư dân không có quyền này.

---

## 13. RÀNG BUỘC DỮ LIỆU

Mã đặt lịch (`ma_dat_lich`) có ràng buộc duy nhất (unique) ở tầng cơ sở dữ liệu trên toàn hệ thống, không phân biệt theo tiện ích hay tòa nhà — đây là ràng buộc cứng ở tầng dữ liệu, khác với ràng buộc trùng lặp kỳ hạn hóa đơn ở Module 1 vốn chỉ kiểm tra ở tầng ứng dụng; nhờ vậy, cơ chế bắt lỗi vi phạm ràng buộc duy nhất và tự động sinh lại mã (EF-06) hoạt động dựa trên sự đảm bảo tuyệt đối của cơ sở dữ liệu, không có khoảng hở race condition.

Bảng yêu cầu đặt lịch có ràng buộc kiểm tra (check constraint) ở tầng cơ sở dữ liệu (áp dụng khi hệ điều hành cơ sở dữ liệu hỗ trợ) đảm bảo thời điểm kết thúc luôn lớn hơn thời điểm bắt đầu, và số người tham gia luôn lớn hơn 0 — đây là lớp bảo vệ dữ liệu bổ sung ở tầng thấp nhất, độc lập với các validate đã thực hiện ở tầng ứng dụng, phòng ngừa trường hợp có đường ghi dữ liệu nào đó bỏ qua được tầng validate ứng dụng.

Bảng yêu cầu đặt lịch có chỉ mục kết hợp trên (tiện ích, thời điểm bắt đầu, thời điểm kết thúc) phục vụ trực tiếp cho truy vấn tìm kiếm các yêu cầu giao nhau khung giờ — vốn là truy vấn lõi được gọi liên tục trong toàn bộ cơ chế xét sức chứa và hàng đợi (BR-05, BR-06) — và chỉ mục trên (cư dân, thời điểm bắt đầu) phục vụ tra cứu lịch sử đặt chỗ theo từng cư dân.

Yêu cầu đặt lịch áp dụng cơ chế xóa mềm; một yêu cầu bị xóa (khác với bị hủy) vẫn có thể được khôi phục lại bởi nhân viên có thẩm quyền. Khi xóa một yêu cầu, hệ thống trước tiên chuyển trạng thái sang Đã hủy rồi mới thực hiện xóa mềm, đảm bảo ngữ nghĩa trạng thái nhất quán ngay cả khi bản ghi đã bị loại khỏi hiển thị thông thường — tránh tình huống một bản ghi bị xóa mềm nhưng vẫn mang trạng thái Chờ duyệt hoặc Đã duyệt gây hiểu nhầm nếu được khôi phục lại sau này mà không rà soát lại tính hợp lệ.

Ràng buộc khóa ngoại: yêu cầu đặt lịch tham chiếu bắt buộc đến cư dân và tiện ích, tham chiếu tùy chọn đến căn hộ, nhân viên duyệt và nhân viên cập nhật gần nhất — các tham chiếu đến nhân viên đều cho phép truy vấn cả bản ghi đã xóa mềm (tương tự nguyên tắc đã áp dụng ở Module 1), đảm bảo lịch sử đặt lịch vẫn hiển thị đúng tên nhân viên đã duyệt dù nhân viên đó về sau không còn thuộc biên chế đang hoạt động.

---

## 14. HIỆU NĂNG

Truy vấn tính tổng số người của các yêu cầu Đã duyệt giao nhau khung giờ (lõi của điều kiện đủ chỗ, BR-05) được thực hiện hoàn toàn bằng một câu truy vấn tổng hợp (`SUM`) ở tầng cơ sở dữ liệu, tận dụng trực tiếp chỉ mục kết hợp trên (tiện ích, thời điểm bắt đầu, thời điểm kết thúc) đã nêu ở Mục 13, tránh việc phải tải toàn bộ các bản ghi liên quan về tầng ứng dụng rồi tính tổng bằng vòng lặp — điều đặc biệt quan trọng vì truy vấn này được gọi lặp lại rất nhiều lần trong một lượt xử lý hàng đợi (mỗi bước xét một yêu cầu trong cụm đều gọi lại truy vấn này).

Ba tiến trình tự động định kỳ chạy mỗi phút cần được thiết kế để hoàn tất xử lý trong khoảng thời gian ngắn hơn đáng kể so với chu kỳ một phút, tránh tình trạng một phiên chạy chưa kết thúc thì phiên kế tiếp đã đến hạn khởi động (dù đã có cơ chế chống chồng lấn ở BR-17 ngăn việc chạy song song, việc trễ tiến độ liên tục vẫn làm giảm tính "gần thời gian thực" của việc tự động duyệt/hủy/hoàn thành mà nghiệp vụ kỳ vọng).

Việc gom cụm khung giờ giao nhau (thuật toán gộp khoảng giao nhau) có độ phức tạp tuyến tính theo số lượng yêu cầu Chờ duyệt của một tiện ích tại thời điểm xử lý sau khi đã sắp xếp; với khối lượng đặt lịch thông thường của một tiện ích trong tòa nhà (không phải hàng nghìn yêu cầu treo cùng lúc), thuật toán này không phải điểm nghẽn hiệu năng đáng lo ngại, nhưng cần lưu ý nếu một tiện ích cụ thể bất thường tích lũy số lượng yêu cầu Chờ duyệt rất lớn (ví dụ do lỗi vận hành khiến hàng đợi không được xử lý trong thời gian dài), thời gian xử lý mỗi lượt quét của tiến trình tự động cho riêng tiện ích đó sẽ tăng tương ứng.

Mọi thao tác chuyển trạng thái đều được bọc trong giao dịch cơ sở dữ liệu có cơ chế tự động thử lại khi gặp deadlock (tối đa ba lần, BR-17/EF-04), đây là lựa chọn đánh đổi hợp lý giữa việc chấp nhận độ trễ nhỏ khi xảy ra xung đột hiếm gặp để đổi lấy việc không phải thiết kế một cơ chế khóa toàn cục phức tạp hơn nhưng tiềm ẩn nguy cơ nghẽn hiệu năng cao hơn khi hệ thống có nhiều tiện ích được thao tác đồng thời bởi nhiều cư dân khác nhau.

Việc phát thông báo thời gian thực được thực hiện sau khi giao dịch cơ sở dữ liệu đã xác nhận hoàn tất (`afterCommit`), không nằm trong đường găng (critical path) của giao dịch chính — điều này đảm bảo rằng ngay cả khi hạ tầng phát thông báo thời gian thực gặp sự cố tạm thời (mất kết nối WebSocket, quá tải), giao dịch nghiệp vụ cốt lõi (thay đổi trạng thái yêu cầu đặt lịch) vẫn hoàn tất bình thường và không bị ảnh hưởng, chỉ có việc thông báo tức thời cho người dùng bị chậm hoặc bỏ lỡ.

---

## 15. BẢO MẬT

Chống race condition là mối quan tâm bảo mật/toàn vẹn dữ liệu trung tâm của module này, được xử lý ở nhiều lớp: khóa độc quyền theo thứ tự cố định (luôn khóa bản ghi tiện ích trước, bản ghi yêu cầu đặt lịch sau) nhằm loại trừ khả năng deadlock giữa các giao dịch cùng thao tác trên một cặp bản ghi tiện ích/yêu cầu theo hai thứ tự khác nhau; toàn bộ thao tác kiểm tra điều kiện và ghi thay đổi trạng thái được thực hiện trong cùng một giao dịch cơ sở dữ liệu duy nhất, không tách rời bước "kiểm tra" và bước "ghi" thành hai thao tác độc lập có thể bị chen ngang bởi một giao dịch khác ở giữa.

Chống double submit khi cư dân bấm nhiều lần liên tiếp nút xác nhận đặt lịch hoặc nút hủy: nhờ tính idempotent của các hành động chuyển trạng thái (BR-10), việc gọi lại cùng một hành động khi yêu cầu đã ở đúng trạng thái đích không gây ra tác dụng phụ ngoài ý muốn (không tạo thêm bản ghi trùng lặp, không phát thông báo trùng lặp); riêng với hành động tạo mới yêu cầu đặt lịch (không có khái niệm "trạng thái đích sẵn có" để idempotent theo cùng cách), việc bấm nhiều lần liên tiếp nút xác nhận tạo có thể dẫn đến nhiều bản ghi yêu cầu độc lập được tạo ra nếu không có biện pháp chặn trùng lặp ở tầng giao diện (vô hiệu hóa nút ngay sau lần bấm đầu tiên cho đến khi có phản hồi từ máy chủ) — đây là biện pháp cần đảm bảo triển khai đầy đủ ở tầng giao diện người dùng, vì tầng dịch vụ không tự nhận diện được hai yêu cầu tạo mới độc lập nhưng giống hệt nội dung là cùng một ý định của người dùng.

Kiểm tra quyền được thực thi nhất quán ở tầng xử lý phía máy chủ cho mọi hành động (duyệt, từ chối, hủy, sửa, xóa), không tin tưởng vào việc ẩn nút bấm ở giao diện là đủ để ngăn cản hành vi cố tình gọi thẳng địa chỉ xử lý — áp dụng cùng nguyên tắc phòng thủ đã nêu ở Module 1 và Module 2.

CSRF được bảo vệ theo cơ chế chuẩn của framework nền tảng cho mọi thao tác ghi dữ liệu qua giao diện web (tạo, sửa, hủy, duyệt, từ chối); kênh thông báo thời gian thực yêu cầu xác thực riêng theo từng kênh (kênh cư dân chỉ cho phép chính cư dân sở hữu yêu cầu lắng nghe, kênh nhân viên chỉ cho phép tài khoản nhân viên đang hoạt động lắng nghe), đảm bảo không rò rỉ thông tin về yêu cầu đặt lịch của người khác qua kênh thời gian thực dù kênh này về bản chất kỹ thuật là một luồng dữ liệu đẩy (push) khác biệt với luồng yêu cầu-phản hồi (request-response) thông thường của các route web khác.

Idempotency của các thao tác chuyển trạng thái, ngoài lợi ích chống double submit đã nêu, còn là biện pháp bảo mật giảm thiểu rủi ro khi tiến trình tự động và một thao tác thủ công cùng vô tình cố gắng thực hiện cùng một hành động chuyển trạng thái cho cùng một yêu cầu (ví dụ tiến trình tự động vừa duyệt một yêu cầu ngay trước khi nhân viên cũng bấm nút duyệt thủ công cho chính yêu cầu đó) — kết quả cuối cùng luôn nhất quán, không có hành động nào trong hai hành động đó gây ra tác dụng phụ chồng chéo hay lỗi hiển thị cho người dùng.

---

*(Hết Module 3 — Đặt lịch sử dụng tiện ích. Đây là module cuối cùng trong ba module trọng tâm của tài liệu nghiệp vụ.)*
