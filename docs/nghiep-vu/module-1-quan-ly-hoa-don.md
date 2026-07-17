# TÀI LIỆU NGHIỆP VỤ VÀ ĐẶC TẢ CHỨC NĂNG

## HỆ THỐNG QUẢN LÝ CĂN HỘ CHUNG CƯ

# MODULE 1 — QUẢN LÝ HÓA ĐƠN

---

## Quy ước và phạm vi tài liệu

Tài liệu này mô tả nghiệp vụ quản lý hóa đơn giữa hai nhóm tác nhân chính của hệ thống là Ban quản lý (bao gồm Quản trị viên cấp hệ thống — sau đây gọi là Admin, và Nhân viên quản lý tòa nhà — sau đây gọi là Manager) và Cư dân. Nội dung được xây dựng dựa trên schema cơ sở dữ liệu và mã nguồn thực tế của hệ thống tại thời điểm biên soạn (các bảng `hoa_don`, `chi_tiet_hoa_don`, `lich_su_thanh_toan`, `can_ho`, `cu_dan`, `cu_dan_can_ho`, `phi_dich_vu`, `can_ho_phi_dich_vu`, `thuoc_tinh_can_ho`, `phuong_tien`), nhằm đảm bảo tài liệu phản ánh đúng hành vi hệ thống, không mô tả suy diễn. Ở những điểm mà nghiệp vụ được yêu cầu nhưng chưa được triển khai đầy đủ trong mã nguồn hiện tại (ví dụ: tạo hóa đơn hàng loạt), tài liệu sẽ nêu rõ đặc tả yêu cầu đồng thời chú thích rõ ràng tình trạng triển khai để tránh gây hiểu nhầm giữa "yêu cầu nghiệp vụ" và "hiện trạng hệ thống".

---

## 1. GIỚI THIỆU

### 1.1. Mục đích chức năng

Module Quản lý Hóa đơn là module trung tâm của toàn bộ hệ thống quản lý căn hộ chung cư, đảm nhiệm việc ghi nhận, tính toán và theo dõi toàn bộ nghĩa vụ tài chính hàng tháng phát sinh giữa Ban quản lý và cư dân đang sinh sống tại các căn hộ. Chức năng này chuyển hóa các khoản phí dịch vụ trừu tượng (phí quản lý, phí gửi xe, tiền điện, tiền nước, phí dịch vụ chung cư…) thành một chứng từ tài chính cụ thể — hóa đơn — gắn với một căn hộ, một kỳ tính phí (tháng/năm) xác định, có cấu trúc chi tiết theo từng khoản mục, có hạn thanh toán, có trạng thái công nợ được theo dõi liên tục, và có lịch sử thanh toán không thể chỉnh sửa nhằm đảm bảo tính minh bạch tài chính.

Khác với các hệ thống quản lý chung cư đơn giản chỉ lưu một con số "số tiền phải đóng" mỗi tháng, hệ thống này tách biệt rõ ràng giữa hóa đơn (đại diện cho nghĩa vụ tài chính) và các dòng chi tiết hóa đơn (đại diện cho cách nghĩa vụ đó được cấu thành từ những khoản phí nào, theo cách tính nào). Thiết kế này cho phép hệ thống hỗ trợ đồng thời bốn phương pháp tính phí khác biệt nhau về bản chất nghiệp vụ: tính theo đầu người/cố định, tính theo chỉ số tiêu thụ (điện, nước), tính theo phương tiện đăng ký, và tính theo diện tích sử dụng căn hộ — mà không phải xây dựng bốn module tách rời.

### 1.2. Ý nghĩa trong hệ thống

Module Hóa đơn là điểm giao thoa dữ liệu quan trọng nhất giữa các module khác của hệ thống: nó tiêu thụ dữ liệu từ module Quản lý Căn hộ (diện tích, danh sách phí dịch vụ áp dụng cho từng căn hộ), module Quản lý Phương tiện (số lượng và thời gian đăng ký xe để tính phí gửi xe theo tỷ lệ ngày sử dụng), và module Quản lý Cư dân (xác định ai có quyền xem hóa đơn của căn hộ nào thông qua quan hệ cư trú). Đồng thời, module này là nguồn dữ liệu đầu vào bắt buộc cho Module 2 — Thanh toán trực tuyến, vì mọi giao dịch thanh toán, dù qua VNPay, MoMo hay ghi nhận thủ công, đều phải quy chiếu về một hóa đơn cụ thể còn công nợ.

Về mặt kiến trúc, toàn bộ nghiệp vụ tính toán, tạo lập, chỉnh sửa và ghi nhận thanh toán hóa đơn được tập trung trong một lớp dịch vụ nghiệp vụ duy nhất (`HoaDonService`), các Controller của Admin, Manager và Cư dân chỉ đóng vai trò tiếp nhận yêu cầu, kiểm tra quyền truy cập và ủy quyền xử lý cho lớp dịch vụ này. Cách tổ chức này đảm bảo rằng logic tính phí và quy tắc khóa sửa được áp dụng nhất quán cho mọi kênh tạo hóa đơn, không phân tán và không có nguy cơ lệch pha giữa các luồng nghiệp vụ khác nhau.

### 1.3. Vai trò đối với Ban quản lý

Đối với Ban quản lý, module Hóa đơn là công cụ chủ đạo để hiện thực hóa chính sách thu phí của tòa nhà. Ban quản lý cấu hình danh mục phí dịch vụ áp dụng chung hoặc áp dụng riêng cho từng căn hộ (thông qua bảng giá override `can_ho_phi_dich_vu`), sau đó phát hành hóa đơn hàng tháng dựa trên danh mục này. Module cung cấp cho Ban quản lý khả năng giám sát tổng công nợ toàn tòa nhà theo thời gian thực, xác định các căn hộ chậm thanh toán để có biện pháp nhắc nhở phù hợp, và duy trì một lịch sử thanh toán bất biến làm căn cứ đối soát tài chính khi cần thiết. Việc khóa chỉnh sửa hóa đơn ngay khi phát sinh thanh toán đầu tiên là cơ chế bảo vệ tính toàn vẹn của số liệu kế toán, tránh tình trạng hóa đơn bị thay đổi sau khi cư dân đã thực hiện nghĩa vụ tài chính dựa trên số liệu ban đầu.

### 1.4. Vai trò đối với Cư dân

Đối với cư dân, module Hóa đơn là kênh minh bạch hóa nghĩa vụ tài chính hàng tháng. Cư dân có thể tra cứu chi tiết cách tính từng khoản phí (ví dụ: số điện tiêu thụ trong tháng được tính bằng chênh lệch giữa chỉ số mới và chỉ số cũ, đơn giá áp dụng tại thời điểm lập hóa đơn), theo dõi số tiền đã thanh toán và công nợ còn lại theo từng hóa đơn cũng như tổng công nợ trên toàn bộ các căn hộ mà mình đang cư trú, và thực hiện thanh toán trực tiếp trên hệ thống. Vì một cư dân có thể đồng thời là thành viên cư trú hợp lệ của nhiều căn hộ khác nhau (ví dụ sở hữu hai căn hộ, hoặc là thành viên gia đình được ghi nhận cư trú ở căn hộ của người thân), module đảm bảo cư dân nhìn thấy đầy đủ hóa đơn của toàn bộ các căn hộ đó trong một dashboard hợp nhất, đồng thời đảm bảo dữ liệu hóa đơn của một căn hộ được chia sẻ minh bạch cho tất cả các cư dân hợp lệ đang cư trú tại căn hộ đó, không phân biệt ai là người đứng tên chủ hộ.

---

## 2. CÁC TÁC NHÂN THAM GIA

### 2.1. Admin (Quản trị viên hệ thống)

Admin là tài khoản thuộc nhóm nhân viên (`nhan_vien`) có cờ quản trị cao nhất, có toàn quyền trên toàn bộ dữ liệu hóa đơn của mọi tòa nhà trong hệ thống, không giới hạn theo phạm vi quản lý. Admin có quyền:

- Xem danh sách, tìm kiếm, lọc hóa đơn theo tòa nhà, căn hộ, kỳ hạn, trạng thái công nợ trên toàn hệ thống.
- Tạo hóa đơn mới cho bất kỳ căn hộ nào, thuộc bất kỳ tòa nhà nào.
- Cấu hình danh mục phí dịch vụ áp dụng cho từng căn hộ (đồng bộ qua chức năng gán/hủy gán dịch vụ).
- Sửa chi tiết hóa đơn khi hóa đơn còn ở trạng thái Chưa thanh toán và chưa phát sinh lịch sử thanh toán.
- Xóa hóa đơn khi hóa đơn chưa phát sinh bất kỳ khoản thanh toán nào.
- Ghi nhận thanh toán thủ công (tiền mặt, chuyển khoản) và khởi tạo giao dịch thanh toán trực tuyến hộ cư dân.
- Xem toàn bộ lịch sử thanh toán và nhật ký hệ thống (audit log) liên quan đến hóa đơn.
- Cấu hình các kênh thanh toán thủ công hiển thị cho nhân viên và cư dân.

### 2.2. Manager (Nhân viên Ban quản lý tòa nhà)

Manager là tài khoản thuộc nhóm nhân viên nhưng không có cờ quản trị, được gán phạm vi quản lý giới hạn (thường theo tòa nhà hoặc cụm tòa nhà được phân công). Bộ quyền nghiệp vụ của Manager đối với module Hóa đơn về cơ bản tương đương Admin — tạo, sửa (trong điều kiện cho phép), ghi nhận thanh toán, xem lịch sử — nhưng phạm vi dữ liệu bị giới hạn theo cơ chế Scope ở tầng truy vấn, đảm bảo Manager không thể thao tác trên hóa đơn của các căn hộ nằm ngoài phạm vi được phân công quản lý. Điểm khác biệt quan trọng nhất giữa Manager và Admin là Manager không có quyền truy cập, chỉnh sửa dữ liệu hồ sơ của các tài khoản nhân viên khác (Admin/Manager khác) — tuy nhiên ràng buộc này thuộc phạm vi module Quản lý Nhân sự, không ảnh hưởng trực tiếp đến module Hóa đơn.

### 2.3. Cư dân

Cư dân là tài khoản thuộc bảng `cu_dan`, xác thực độc lập với nhân viên (guard riêng biệt). Đối với module Hóa đơn, cư dân chỉ có quyền:

- Xem danh sách và chi tiết hóa đơn của các căn hộ mà mình đang có quan hệ cư trú hợp lệ (`trang_thai = 1` trong bảng `cu_dan_can_ho`, tức đang ở, không bao gồm các căn hộ đã từng ở nhưng đã chuyển đi).
- Xem tổng công nợ hợp nhất trên tất cả các căn hộ đang cư trú.
- Thực hiện thanh toán trực tuyến (VNPay/MoMo) đối với hóa đơn của các căn hộ mình đang cư trú.

Cư dân không có quyền tạo, sửa, xóa hóa đơn, không có quyền ghi nhận thanh toán thủ công, và không thể truy cập hóa đơn của căn hộ mà quan hệ cư trú đã kết thúc (đã chuyển đi) hoặc chưa từng tồn tại.

### 2.4. Bảng tổng hợp quyền theo tác nhân

| Hành động | Admin | Manager | Cư dân |
|---|---|---|---|
| Xem danh sách hóa đơn toàn hệ thống | Có | Có (giới hạn phạm vi quản lý) | Không |
| Xem hóa đơn của căn hộ mình cư trú | Có (gián tiếp, không giới hạn) | Có (trong phạm vi) | Có |
| Tạo hóa đơn | Có | Có | Không |
| Sửa chi tiết hóa đơn (khi đủ điều kiện) | Có | Có | Không |
| Xóa hóa đơn (khi đủ điều kiện) | Có | Có | Không |
| Ghi nhận thanh toán thủ công | Có | Có | Không |
| Thanh toán trực tuyến | Có (hộ cư dân) | Có (hộ cư dân) | Có (cho chính mình) |
| Xem lịch sử thanh toán | Có (toàn hệ thống) | Có (trong phạm vi) | Có (của căn hộ mình) |
| Xem nhật ký hệ thống (audit log) | Có | Không (mặc định chỉ Admin) | Không |
| Cấu hình danh mục phí dịch vụ căn hộ | Có | Có | Không |

---

## 3. QUY TRÌNH NGHIỆP VỤ TỔNG THỂ

Vòng đời nghiệp vụ của một hóa đơn trải qua năm giai đoạn kế tiếp nhau: chuẩn bị dữ liệu đầu vào, phát hành hóa đơn, theo dõi công nợ trong suốt kỳ hạn, tiếp nhận thanh toán, và kết thúc kỳ hạn thanh toán. Phần này mô tả toàn bộ quy trình bằng lời, đi kèm giải thích nghiệp vụ cho từng bước, trước khi đặc tả chi tiết ở các mục sau.

### 3.1. Giai đoạn chuẩn bị dữ liệu đầu vào

Trước khi một hóa đơn có thể được phát hành cho một căn hộ, Ban quản lý phải đảm bảo ba nhóm dữ liệu nền tảng đã sẵn sàng. Thứ nhất là danh mục phí dịch vụ áp dụng cho căn hộ đó, được thiết lập thông qua quan hệ nhiều-nhiều giữa căn hộ và phí dịch vụ (bảng trung gian `can_ho_phi_dich_vu`); một căn hộ có thể áp dụng đơn giá mặc định của phí dịch vụ hoặc một đơn giá riêng (override) do Ban quản lý thiết lập cho chính căn hộ đó, ví dụ do căn hộ có diện tích đặc thù hoặc theo thỏa thuận riêng. Thứ hai là các thuộc tính vật lý của căn hộ cần thiết cho việc tính phí, cụ thể là diện tích sử dụng, được lưu trong bảng thuộc tính động của căn hộ (`thuoc_tinh_can_ho`) — đây là dữ liệu bắt buộc phải có nếu căn hộ áp dụng bất kỳ khoản phí nào tính theo diện tích. Thứ ba là danh sách phương tiện đã đăng ký của căn hộ (bảng `phuong_tien`), bao gồm thời điểm đăng ký và thời điểm hủy đăng ký (nếu có), là dữ liệu bắt buộc nếu căn hộ áp dụng khoản phí tính theo phương tiện.

### 3.2. Giai đoạn phát hành hóa đơn

Đến kỳ tính phí (thông thường vào đầu tháng hoặc cuối tháng cho kỳ kế tiếp), nhân viên Ban quản lý thực hiện thao tác tạo hóa đơn cho một căn hộ xác định, chỉ định rõ tháng và năm áp dụng. Hệ thống trước tiên kiểm tra căn hộ đó chưa có hóa đơn nào được lập cho đúng kỳ tháng/năm này — đây là ràng buộc nghiệp vụ cốt lõi nhằm tránh phát sinh hai hóa đơn trùng lặp cho cùng một nghĩa vụ tài chính. Sau khi xác nhận không trùng lặp, hệ thống nạp toàn bộ danh mục phí dịch vụ đang áp dụng cho căn hộ, xác định phương pháp tính phí phù hợp cho từng khoản phí (dựa trên loại tính phí được cấu hình ở danh mục dịch vụ), và với những khoản phí cần dữ liệu đầu vào bổ sung theo từng kỳ (điển hình là chỉ số điện, nước), nhân viên nhập trực tiếp chỉ số cũ và chỉ số mới ngay tại màn hình tạo hóa đơn. Nhân viên cũng có quyền loại trừ một số khoản phí dịch vụ ra khỏi hóa đơn của kỳ này nếu căn hộ được miễn giảm tạm thời khoản phí đó (ví dụ căn hộ đang trống, không phát sinh phí dịch vụ chung).

Toàn bộ quá trình tính toán chi tiết hóa đơn — sinh ra từng dòng chi tiết tương ứng với từng khoản phí, tính đơn giá, số lượng và thành tiền theo đúng phương pháp tính phí của từng khoản, rồi tổng hợp thành tổng tiền hóa đơn — được thực hiện trong một giao dịch cơ sở dữ liệu duy nhất (transaction), đảm bảo hóa đơn và toàn bộ chi tiết của nó được ghi nhận đồng thời hoặc không ghi nhận gì cả nếu có lỗi phát sinh giữa chừng. Hạn thanh toán của hóa đơn được hệ thống tự động ấn định là ngày cuối cùng của tháng phát hành, không yêu cầu nhân viên nhập tay.

### 3.3. Giai đoạn theo dõi công nợ

Sau khi phát hành, hóa đơn bước vào trạng thái Chưa thanh toán với công nợ ban đầu bằng đúng tổng tiền hóa đơn. Trong suốt giai đoạn này, hệ thống liên tục — mỗi khi có người truy cập vào danh sách, chi tiết hoặc màn hình chỉnh sửa hóa đơn — kiểm tra và cập nhật lại trạng thái công nợ dựa trên hai tiêu chí: số tiền đã thanh toán so với tổng tiền, và hạn thanh toán so với thời điểm hiện tại. Nếu số tiền đã thanh toán đạt hoặc vượt tổng tiền, hóa đơn chuyển sang trạng thái Đã thanh toán; nếu chưa đạt và đã qua hạn thanh toán, hóa đơn chuyển sang trạng thái Quá hạn; các trường hợp còn lại giữ nguyên trạng thái Chưa thanh toán.

Trong giai đoạn này, chỉ khi hóa đơn chưa phát sinh bất kỳ bản ghi lịch sử thanh toán nào, Ban quản lý mới được phép điều chỉnh chi tiết hóa đơn (thêm khoản phí, xóa khoản phí, thay đổi chỉ số) hoặc xóa toàn bộ hóa đơn. Ngay khi khoản thanh toán đầu tiên — dù là một phần hay toàn bộ — được ghi nhận, hóa đơn chuyển sang trạng thái bị khóa chỉnh sửa hoàn toàn về mặt chi tiết và không thể xóa, chỉ còn có thể tiếp tục nhận thêm các khoản thanh toán bổ sung cho đến khi đủ công nợ.

### 3.4. Giai đoạn tiếp nhận thanh toán

Thanh toán có thể được thực hiện theo hai kênh: ghi nhận thủ công bởi nhân viên Ban quản lý (khi cư dân nộp tiền mặt hoặc chuyển khoản ngân hàng truyền thống), hoặc thanh toán trực tuyến qua cổng VNPay/MoMo do chính cư dân thực hiện hoặc do nhân viên khởi tạo hộ. Dù qua kênh nào, mọi khoản thanh toán đều được xử lý thông qua một hàm nghiệp vụ trung tâm duy nhất, đảm bảo tính nhất quán tuyệt đối của quy tắc nghiệp vụ: khóa độc quyền bản ghi hóa đơn trong suốt quá trình xử lý để tránh hai giao dịch ghi nhận đồng thời gây sai lệch số liệu, kiểm tra chống trùng lặp dựa trên mã giao dịch để đảm bảo một giao dịch thanh toán không bị ghi nhận hai lần, và kiểm tra chặn cứng để đảm bảo tổng số tiền đã thanh toán không bao giờ vượt quá tổng tiền hóa đơn. Thanh toán một phần được hỗ trợ đầy đủ: cư dân hoặc nhân viên có thể ghi nhận nhiều khoản thanh toán nhỏ liên tiếp cho cùng một hóa đơn cho đến khi công nợ về không, mỗi khoản thanh toán tạo ra một bản ghi lịch sử thanh toán độc lập, bất biến, không thể sửa hoặc xóa sau khi đã tạo.

### 3.5. Giai đoạn kết thúc

Một hóa đơn kết thúc vòng đời nghiệp vụ khi đạt trạng thái Đã thanh toán (công nợ về không) hoặc — theo đặc tả yêu cầu, dù hiện tại chưa có luồng thao tác nào trong hệ thống thực sự đưa hóa đơn vào trạng thái này — khi bị hủy bởi Ban quản lý trong trường hợp đặc biệt (ví dụ hóa đơn được lập sai hoàn toàn nhưng đã trót phát sinh một khoản thanh toán nhỏ không thể xóa cứng). Mục 4 và Mục 9 sẽ phân tích chi tiết ràng buộc và tình trạng triển khai của trạng thái Đã hủy này.

### 3.6. Sơ đồ luồng nghiệp vụ tổng thể (dạng văn bản)

```
[Cấu hình phí dịch vụ căn hộ] --> [Nhập/cập nhật thuộc tính căn hộ: diện tích]
        --> [Nhập/cập nhật phương tiện đăng ký]
        --> [Nhân viên chọn căn hộ + kỳ tháng/năm để tạo hóa đơn]
        --> {Kiểm tra trùng lặp (căn hộ, tháng, năm)}
              -- Trùng --> [Từ chối, báo lỗi "Hóa đơn tháng này đã tồn tại"]
              -- Không trùng --> [Nhập chỉ số điện/nước nếu có khoản phí theo chỉ số]
        --> [Hệ thống tính chi tiết từng khoản phí theo đúng phương pháp tính]
        --> [Tổng hợp tổng tiền, ấn định hạn thanh toán = cuối tháng phát hành]
        --> [Hóa đơn ở trạng thái CHƯA THANH TOÁN]
        --> {Có bản ghi thanh toán nào chưa?}
              -- Chưa --> [Cho phép sửa chi tiết / xóa hóa đơn]
              -- Đã có --> [KHÓA sửa chi tiết, khóa xóa]
        --> [Cư dân/nhân viên ghi nhận thanh toán (một phần hoặc toàn bộ, một hoặc nhiều lần)]
        --> {so_tien_da_thanh_toan >= tong_tien ?}
              -- Đúng --> [Trạng thái = ĐÃ THANH TOÁN] --> [Kết thúc vòng đời]
              -- Sai, đã quá han_thanh_toan --> [Trạng thái = QUÁ HẠN] --> (tiếp tục chờ thanh toán)
              -- Sai, chưa quá hạn --> [Trạng thái = CHƯA THANH TOÁN] --> (tiếp tục chờ thanh toán)
```

---

## 4. QUY TẮC NGHIỆP VỤ (BUSINESS RULES)

### 4.1. Quy tắc về tạo hóa đơn

**BR-01 — Không trùng lặp kỳ hạn.** Một căn hộ chỉ được phép có duy nhất một hóa đơn cho mỗi cặp (tháng, năm). Hệ thống kiểm tra điều kiện này ở tầng ứng dụng bằng truy vấn tồn tại trước khi tạo mới; nếu đã tồn tại, yêu cầu tạo mới bị từ chối kèm thông báo rõ ràng. Cần lưu ý rằng ràng buộc này hiện chỉ được thực thi ở tầng ứng dụng, không có ràng buộc duy nhất (unique constraint) tương ứng ở tầng cơ sở dữ liệu cho tổ hợp (căn hộ, tháng, năm); do đó về mặt lý thuyết, hai yêu cầu tạo hóa đơn được gửi đồng thời cho cùng một căn hộ, cùng một kỳ hạn có thể dẫn đến tình huống hai hóa đơn trùng lặp được tạo ra nếu cả hai đều vượt qua bước kiểm tra trước khi bất kỳ hóa đơn nào được ghi vào cơ sở dữ liệu. Đây là rủi ro cần lưu ý khi vận hành với tần suất thao tác đồng thời cao và nên được xem xét bổ sung ràng buộc duy nhất ở tầng cơ sở dữ liệu trong các phiên bản nâng cấp tiếp theo.

**BR-02 — Chỉ số mới không được nhỏ hơn chỉ số cũ.** Đối với mọi khoản phí tính theo chỉ số tiêu thụ (điện, nước), giá trị chỉ số mới nhập vào bắt buộc phải lớn hơn hoặc bằng chỉ số cũ. Ràng buộc này được kiểm tra ở tầng Controller trước khi ủy quyền xử lý cho tầng dịch vụ, áp dụng cho cả luồng tạo mới lẫn luồng chỉnh sửa hóa đơn.

**BR-03 — Đơn giá luôn là snapshot tại thời điểm lập hóa đơn.** Đơn giá của từng dòng chi tiết hóa đơn được lấy từ đơn giá hiện hành của danh mục phí dịch vụ (hoặc đơn giá override riêng cho căn hộ, nếu có) tại đúng thời điểm hóa đơn được tạo, và được lưu cứng vào dòng chi tiết. Việc thay đổi đơn giá của danh mục phí dịch vụ sau thời điểm lập hóa đơn không ảnh hưởng đến các hóa đơn đã phát hành trước đó — đây là nguyên tắc kế toán cơ bản nhằm đảm bảo mỗi hóa đơn phản ánh đúng chính sách giá tại thời điểm phát sinh nghĩa vụ tài chính.

**BR-04 — Tên phí dịch vụ được lưu dạng snapshot chuỗi văn bản.** Dòng chi tiết hóa đơn lưu tên khoản phí dưới dạng chuỗi ký tự tại thời điểm lập hóa đơn, không tham chiếu khóa ngoại trực tiếp đến danh mục phí dịch vụ. Điều này đảm bảo rằng nếu danh mục phí dịch vụ bị đổi tên hoặc bị xóa sau này, các hóa đơn lịch sử vẫn hiển thị đúng tên khoản phí như tại thời điểm phát hành.

### 4.2. Quy tắc về chỉnh sửa hóa đơn

**BR-05 — Khóa sửa tuyệt đối khi đã phát sinh thanh toán.** Ngay khi hóa đơn có ít nhất một bản ghi trong lịch sử thanh toán (bất kể số tiền thanh toán là một phần hay toàn bộ), toàn bộ thao tác chỉnh sửa chi tiết hóa đơn (sửa chỉ số, sửa số lượng, thêm/xóa khoản phí) bị khóa hoàn toàn, không có ngoại lệ cho Admin hay Manager. Đây là quy tắc bảo vệ tính toàn vẹn kế toán quan trọng nhất của module: một khi cư dân đã thực hiện nghĩa vụ tài chính dựa trên số liệu được công bố, số liệu đó không được phép thay đổi ngược lại.

**BR-06 — Chỉ được sửa chi tiết khi hóa đơn ở trạng thái Chưa thanh toán.** Ngay cả khi chưa có lịch sử thanh toán, việc sửa chi tiết (chỉ số, số lượng) chỉ được phép khi hóa đơn đang ở đúng trạng thái Chưa thanh toán; các trường thông tin khác như hạn thanh toán vẫn có thể chỉnh sửa trong các trạng thái khác miễn là chưa có lịch sử thanh toán.

**BR-07 — Số lượng dịch vụ phải lớn hơn 0.** Với mọi dòng chi tiết không thuộc loại tính theo chỉ số, số lượng nhập vào khi chỉnh sửa phải là số dương, không chấp nhận giá trị 0 hoặc âm.

**BR-08 — Không được đổi loại tính phí của một dòng chi tiết đã tồn tại khi chỉnh sửa.** Khi cập nhật một dòng chi tiết đã có, hệ thống nhận diện lại loại tính phí dựa trên dữ liệu chỉ số đã lưu của chính dòng đó, không cho phép chuyển đổi bản chất tính phí của dòng (ví dụ từ tính theo chỉ số sang tính cố định) thông qua thao tác chỉnh sửa thông thường.

**BR-09 — Đơn giá và số lượng của dòng theo diện tích không nhận trực tiếp từ người dùng.** Với các khoản phí tính theo diện tích, số lượng luôn được tính lại từ thuộc tính diện tích hiện hành của căn hộ tại thời điểm chỉnh sửa, không chấp nhận giá trị số lượng nhập tay từ phía người dùng, nhằm tránh sai lệch giữa số liệu hóa đơn và thực tế diện tích căn hộ.

### 4.3. Quy tắc về xóa hóa đơn

**BR-10 — Điều kiện xóa hóa đơn.** Một hóa đơn chỉ được phép xóa (xóa mềm) khi đồng thời thỏa hai điều kiện: trạng thái hiện tại không phải Đã thanh toán, và chưa tồn tại bất kỳ bản ghi lịch sử thanh toán nào. Khi xóa hóa đơn, toàn bộ các dòng chi tiết liên quan cũng bị xóa cứng (xóa vĩnh viễn) trong cùng một giao dịch, vì bản thân dòng chi tiết không có ý nghĩa tồn tại độc lập ngoài hóa đơn cha.

**BR-11 — Xóa từng dòng chi tiết đơn lẻ.** Ngoài xóa toàn bộ hóa đơn, hệ thống hỗ trợ xóa từng dòng chi tiết riêng lẻ (thao tác này thường dùng khi Ban quản lý phát hiện một khoản phí đã đưa vào hóa đơn không áp dụng cho căn hộ). Thao tác này áp dụng cùng điều kiện chặn như BR-05: chỉ thực hiện được khi hóa đơn chưa phát sinh lịch sử thanh toán. Sau khi xóa một dòng chi tiết, hệ thống tự động tính lại tổng tiền hóa đơn và đồng bộ lại trạng thái công nợ tương ứng.

### 4.4. Quy tắc về công nợ và thanh toán

**BR-12 — Công thức tính công nợ.** Công nợ còn lại của một hóa đơn tại bất kỳ thời điểm nào được tính bằng công thức `max(0, tong_tien - so_tien_da_thanh_toan)`, tuyệt đối không được tính bằng cách chỉ lấy `tong_tien` mà bỏ qua phần đã thanh toán. Việc lấy giá trị lớn nhất với 0 nhằm phòng tránh hiển thị công nợ âm trong trường hợp có sai lệch làm số tiền đã thanh toán vượt quá tổng tiền (dù về nguyên tắc BR-13 đã ngăn chặn tình huống này xảy ra ở nguồn).

**BR-13 — Không cho phép thanh toán vượt quá công nợ.** Tổng số tiền đã thanh toán lũy kế của một hóa đơn không bao giờ được vượt quá tổng tiền hóa đơn (có dung sai làm tròn 0,01 đơn vị tiền tệ để xử lý sai số dấu phẩy động). Mọi yêu cầu ghi nhận thanh toán, dù thủ công hay tự động qua cổng thanh toán, đều được kiểm tra chặn trước khi xử lý (kiểm tra ở tầng Controller đối với ghi nhận thủ công) và kiểm tra chặn lại một lần nữa ngay trong giao dịch cơ sở dữ liệu ở tầng dịch vụ trung tâm (không tin tưởng tuyệt đối vào kiểm tra ở tầng trên, đề phòng trường hợp có nhiều luồng gọi đồng thời).

**BR-14 — Hỗ trợ thanh toán một phần và nhiều lần.** Một hóa đơn có thể được thanh toán bằng nhiều khoản nhỏ liên tiếp, tại các thời điểm khác nhau, qua các phương thức khác nhau (ví dụ lần đầu thanh toán tiền mặt một phần, sau đó thanh toán nốt phần còn lại qua VNPay). Mỗi lần ghi nhận thanh toán tạo ra chính xác một bản ghi lịch sử thanh toán mới, độc lập.

**BR-15 — Lịch sử thanh toán là bất biến.** Một khi đã được tạo, bản ghi lịch sử thanh toán không được phép sửa hoặc xóa bởi bất kỳ tác nhân nào (không có route hoặc chức năng nào trong hệ thống cho phép thao tác này). Đây là nguyên tắc kế toán cơ bản: lịch sử giao dịch tài chính phải là một sổ cái chỉ được thêm vào (append-only), không được viết lại.

**BR-16 — Chống trùng lặp thanh toán theo mã giao dịch.** Với mỗi khoản thanh toán có mã giao dịch xác định (đặc biệt là các giao dịch qua cổng thanh toán trực tuyến), hệ thống kiểm tra mã giao dịch đã tồn tại trong lịch sử thanh toán trước khi ghi nhận; nếu đã tồn tại, hệ thống trả về bản ghi cũ mà không xử lý lại, nhằm đảm bảo tính chất idempotent (bất biến khi gọi lại nhiều lần) của thao tác ghi nhận thanh toán — điều tối quan trọng khi cổng thanh toán có thể gửi lại thông báo xác nhận nhiều lần (xem chi tiết ở Module 2).

### 4.5. Quy tắc về chuyển trạng thái

**BR-17 — Trạng thái công nợ được tính toán lại tự động, không lưu tùy ý.** Trạng thái công nợ của hóa đơn (Chưa thanh toán / Đã thanh toán / Quá hạn) không phải là một trường dữ liệu do người dùng thiết lập trực tiếp, mà luôn được hệ thống tự động suy ra và đồng bộ lại dựa trên số tiền đã thanh toán so với tổng tiền, và hạn thanh toán so với thời điểm hiện tại, mỗi khi có thao tác truy cập hoặc ghi nhận thanh toán liên quan đến hóa đơn. Việc đồng bộ này hiện được thực hiện theo cơ chế "lười" (on-demand) — nghĩa là trạng thái chỉ được tính toán lại tại thời điểm có người truy cập vào trang danh sách, chi tiết hoặc chỉnh sửa hóa đơn — chứ không có tiến trình nền (scheduler) chạy định kỳ để cập nhật trạng thái độc lập với hành vi truy cập của người dùng. Hệ quả nghiệp vụ cần lưu ý: một hóa đơn quá hạn thanh toán nhưng không có ai truy cập vào các trang liên quan sẽ tiếp tục hiển thị trạng thái Chưa thanh toán cho đến khi có một lượt truy cập kích hoạt việc đồng bộ lại.

**BR-18 — Trạng thái Đã hủy được định nghĩa nhưng chưa có luồng nghiệp vụ kích hoạt.** Hệ thống có định nghĩa hằng số cho trạng thái thứ tư — Đã hủy — dành cho hóa đơn bị vô hiệu hóa nhưng không thể xóa cứng (ví dụ do đã phát sinh một khoản thanh toán nhỏ, không thỏa điều kiện xóa ở BR-10). Tuy nhiên tại thời điểm biên soạn tài liệu, chưa có bất kỳ thao tác nghiệp vụ, route hay giao diện nào trong hệ thống thực sự đưa một hóa đơn vào trạng thái này. Đây được xem là một điểm cần bổ sung trong lộ trình phát triển tiếp theo: Ban quản lý hiện không có công cụ để vô hiệu hóa một hóa đơn đã phát sinh thanh toán nhưng bị lập sai, ngoài việc phải xử lý thủ công ngoài hệ thống hoặc điều chỉnh bằng một hóa đơn bù trừ trong kỳ kế tiếp.

---

## 5. LUỒNG XỬ LÝ CHÍNH (MAIN FLOW)

### 5.1. Luồng tạo hóa đơn đơn lẻ

**Bước 1.** Nhân viên (Admin/Manager) truy cập màn hình tạo hóa đơn, chọn tòa nhà rồi chọn căn hộ cần lập hóa đơn.

**Bước 2.** Hệ thống hiển thị danh mục phí dịch vụ hiện đang áp dụng cho căn hộ đã chọn (bao gồm cả đơn giá gốc và đơn giá override riêng nếu có), cho phép nhân viên xem trước cách tính (`previewPhi`) trước khi xác nhận tạo chính thức.

**Bước 3.** Nhân viên chọn kỳ tính phí bằng cách nhập tháng và năm áp dụng.

**Bước 4.** Đối với các khoản phí thuộc loại tính theo chỉ số tiêu thụ (điện, nước), nhân viên nhập chỉ số cũ và chỉ số mới cho từng khoản. Hệ thống kiểm tra ngay tại bước này: chỉ số mới phải lớn hơn hoặc bằng chỉ số cũ (BR-02); nếu vi phạm, yêu cầu bị từ chối và nhân viên phải nhập lại.

**Bước 5.** Nhân viên có thể tùy chọn loại trừ một số khoản phí dịch vụ ra khỏi hóa đơn của kỳ này nếu căn hộ được miễn giảm tạm thời.

**Bước 6.** Nhân viên xác nhận tạo hóa đơn. Hệ thống kiểm tra điều kiện không trùng lặp kỳ hạn (BR-01); nếu đã tồn tại hóa đơn cho đúng cặp (căn hộ, tháng, năm), yêu cầu bị từ chối.

**Bước 7.** Trong một giao dịch cơ sở dữ liệu duy nhất, hệ thống: tạo bản ghi hóa đơn với tổng tiền khởi tạo bằng 0 và hạn thanh toán được ấn định tự động là ngày cuối cùng của tháng phát hành; với từng khoản phí dịch vụ áp dụng (trừ các khoản đã bị loại trừ ở Bước 5), xác định phương pháp tính phí phù hợp và sinh ra dòng chi tiết hóa đơn tương ứng (xem Mục 5.2 về bốn phương pháp tính phí); sau khi sinh xong toàn bộ chi tiết, tính tổng và cập nhật lại tổng tiền hóa đơn; ghi nhật ký hệ thống ghi nhận hành động tạo mới.

**Bước 8.** Hệ thống chuyển hướng nhân viên đến trang chi tiết hóa đơn vừa tạo, hiển thị thông báo tạo thành công. Hóa đơn ở trạng thái Chưa thanh toán, công nợ bằng đúng tổng tiền.

### 5.2. Bốn phương pháp tính phí chi tiết cho từng dòng chi tiết hóa đơn

Với mỗi khoản phí dịch vụ áp dụng cho căn hộ, hệ thống xác định phương pháp tính phù hợp dựa trên loại tính phí được cấu hình sẵn ở danh mục dịch vụ (nhận diện theo tên loại tính phí, ví dụ tên chứa "chỉ số"/"đồng hồ" ứng với tính theo chỉ số, tên chứa "phương tiện" ứng với tính theo phương tiện, tên chứa "diện tích" ứng với tính theo diện tích; các trường hợp còn lại mặc định áp dụng phương pháp cố định/theo đầu người).

**a) Phương pháp cố định (theo đầu người/phí cố định hàng tháng).** Đây là phương pháp mặc định, áp dụng cho các khoản phí không phụ thuộc vào chỉ số tiêu thụ hay đặc điểm vật lý của căn hộ, ví dụ phí quản lý chung, phí vệ sinh chung. Số lượng mặc định bằng 1, thành tiền của dòng bằng đúng đơn giá áp dụng.

**b) Phương pháp theo chỉ số tiêu thụ (điện, nước).** Số lượng tiêu thụ trong kỳ được tính bằng hiệu số giữa chỉ số mới và chỉ số cũ do nhân viên nhập tại Bước 4, có chặn dưới bằng 0 để tránh giá trị âm trong trường hợp dữ liệu bất thường. Thành tiền của dòng bằng số lượng tiêu thụ nhân với đơn giá trên mỗi đơn vị tiêu thụ.

**c) Phương pháp theo diện tích sử dụng.** Số lượng của dòng được lấy trực tiếp từ giá trị thuộc tính diện tích (đơn vị mét vuông) đang được lưu trong hồ sơ thuộc tính động của căn hộ; nếu căn hộ chưa được khai báo thuộc tính diện tích, hệ thống sử dụng giá trị mặc định bằng 1 để tránh dòng chi tiết có thành tiền bằng 0 một cách không chủ ý (đây là giá trị dự phòng, Ban quản lý cần đảm bảo khai báo đầy đủ diện tích cho mọi căn hộ áp dụng loại phí này để tránh sai lệch số liệu). Thành tiền bằng diện tích nhân với đơn giá trên mỗi mét vuông.

**d) Phương pháp theo phương tiện đăng ký.** Đây là phương pháp phức tạp nhất trong bốn phương pháp. Hệ thống gom nhóm toàn bộ phương tiện của căn hộ theo từng loại phương tiện (ô tô, xe máy, xe đạp…), sau đó với mỗi phương tiện tính tỷ lệ số ngày phương tiện thực sự có hiệu lực trong tháng tính phí trên tổng số ngày của tháng đó, có xét đến cả trường hợp phương tiện được đăng ký mới hoặc bị hủy đăng ký giữa tháng (tỷ lệ được chặn trên ở giá trị 1,0 để một phương tiện đăng ký trọn tháng không bị tính vượt quá 100%). Thành tiền của dòng phí theo phương tiện phản ánh đúng thời gian thực tế phương tiện lưu hành trong kỳ, đảm bảo công bằng cho cư dân đăng ký hoặc hủy xe giữa chừng thay vì tính trọn tháng bất kể thời điểm phát sinh.

### 5.3. Luồng tạo hóa đơn hàng loạt (đặc tả yêu cầu mở rộng)

**Lưu ý về tình trạng triển khai:** tại thời điểm biên soạn tài liệu, chức năng tạo hóa đơn được triển khai trong hệ thống chỉ hỗ trợ thao tác trên một căn hộ mỗi lần gọi. Phần đặc tả dưới đây mô tả yêu cầu nghiệp vụ cho chức năng tạo hàng loạt, được thiết kế để tái sử dụng nguyên vẹn engine tính phí đơn lẻ đã có (Mục 5.1, 5.2), thay vì xây dựng một luồng tính phí song song riêng biệt — nhằm đảm bảo tính nhất quán tuyệt đối giữa hóa đơn tạo đơn lẻ và hóa đơn tạo hàng loạt.

**Bước 1.** Nhân viên chọn phạm vi tạo hàng loạt: theo toàn bộ căn hộ của một tòa nhà, theo một tầng cụ thể, hoặc theo danh sách căn hộ được chọn thủ công; đồng thời chọn kỳ tính phí (tháng, năm) áp dụng chung cho toàn bộ đợt tạo.

**Bước 2.** Hệ thống loại trừ trước khỏi danh sách xử lý các căn hộ đã có hóa đơn cho đúng kỳ hạn đã chọn (áp dụng lại BR-01 cho từng căn hộ), hiển thị cho nhân viên xem trước danh sách căn hộ sẽ được tạo hóa đơn và danh sách căn hộ bị bỏ qua kèm lý do.

**Bước 3.** Đối với các căn hộ có khoản phí tính theo chỉ số tiêu thụ, vì việc nhập chỉ số cho hàng chục hoặc hàng trăm căn hộ trong một màn hình duy nhất là không khả thi về mặt thao tác, hệ thống cung cấp hai lựa chọn: (a) nhân viên tải lên một bảng chỉ số theo mẫu định sẵn (import) cho các căn hộ có đồng hồ đo riêng, hoặc (b) tạm thời loại trừ khoản phí theo chỉ số ra khỏi đợt tạo hàng loạt và bổ sung sau bằng thao tác chỉnh sửa hóa đơn đơn lẻ (áp dụng BR-06, chỉ thực hiện được khi hóa đơn còn ở trạng thái Chưa thanh toán và chưa có lịch sử thanh toán).

**Bước 4.** Với mỗi căn hộ trong danh sách đã xác nhận, hệ thống gọi lại đúng quy trình tạo hóa đơn đơn lẻ (Bước 1 đến Bước 7 của Mục 5.1) trong một giao dịch cơ sở dữ liệu riêng cho từng căn hộ — không gộp toàn bộ đợt tạo hàng loạt vào một giao dịch duy nhất, để một căn hộ gặp lỗi (ví dụ thiếu dữ liệu diện tích) không làm thất bại toàn bộ các căn hộ còn lại.

**Bước 5.** Sau khi xử lý xong toàn bộ danh sách, hệ thống hiển thị báo cáo kết quả tổng hợp: số hóa đơn tạo thành công, số căn hộ bị bỏ qua do trùng lặp kỳ hạn, số căn hộ xử lý thất bại kèm lý do cụ thể (ví dụ thiếu thuộc tính diện tích bắt buộc), cho phép nhân viên xử lý riêng các trường hợp thất bại bằng thao tác tạo đơn lẻ thông thường.

### 5.4. Luồng ghi nhận thanh toán (thủ công)

**Bước 1.** Nhân viên mở trang chi tiết hóa đơn đang có công nợ, chọn chức năng ghi nhận thanh toán.

**Bước 2.** Nhân viên chọn phương thức thanh toán thủ công (tiền mặt, chuyển khoản theo một trong các kênh đã cấu hình sẵn trong danh mục kênh thanh toán), nhập số tiền thanh toán và ghi chú (nếu có).

**Bước 3.** Hệ thống kiểm tra số tiền nhập vào không vượt quá công nợ hiện tại của hóa đơn (BR-13) ở tầng Controller.

**Bước 4.** Hệ thống ủy quyền xử lý cho hàm ghi nhận thanh toán trung tâm: khóa độc quyền bản ghi hóa đơn (`lockForUpdate`) trong một giao dịch cơ sở dữ liệu để tuần tự hóa các thao tác ghi nhận thanh toán đồng thời trên cùng một hóa đơn; tạo bản ghi lịch sử thanh toán mới gắn với hóa đơn, người thanh toán và nguồn tạo giao dịch (trong trường hợp này là "Thủ công"); tính lại tổng số tiền đã thanh toán lũy kế bằng tổng toàn bộ các bản ghi lịch sử thanh toán của hóa đơn; kiểm tra chặn lại một lần nữa tổng đã thanh toán không vượt quá tổng tiền (dung sai 0,01); cập nhật số tiền đã thanh toán và tính toán lại trạng thái công nợ mới của hóa đơn; ghi nhật ký hệ thống.

**Bước 5.** Hệ thống hiển thị thông báo ghi nhận thành công, cập nhật lại giao diện hiển thị công nợ và trạng thái mới nhất của hóa đơn.

### 5.5. Luồng xem hóa đơn và công nợ của cư dân

**Bước 1.** Cư dân đăng nhập, truy cập mục hóa đơn của tôi.

**Bước 2.** Hệ thống xác định toàn bộ căn hộ mà cư dân đang có quan hệ cư trú hợp lệ hiện tại (trạng thái đang ở trong bảng quan hệ cư dân-căn hộ), sau đó truy vấn toàn bộ hóa đơn thuộc các căn hộ này.

**Bước 3.** Hệ thống hiển thị danh sách hóa đơn kèm trạng thái công nợ của từng hóa đơn, đồng thời hiển thị tổng công nợ hợp nhất trên toàn bộ các căn hộ (tổng của công nợ tính theo công thức BR-12 trên từng hóa đơn còn nợ, không phải tổng `tong_tien` của mọi hóa đơn).

**Bước 4.** Cư dân chọn xem chi tiết một hóa đơn cụ thể, hệ thống kiểm tra hóa đơn đó thuộc một trong các căn hộ cư dân đang cư trú trước khi hiển thị (kiểm soát truy cập theo phạm vi căn hộ); nếu không thuộc phạm vi, từ chối truy cập.

**Bước 5.** Cư dân có thể chọn thực hiện thanh toán trực tuyến ngay tại trang chi tiết hóa đơn (chuyển sang luồng của Module 2).

---

## 6. LUỒNG THAY THẾ (ALTERNATE FLOWS)

**AF-01 — Chỉ số nhập vào không hợp lệ khi tạo hóa đơn.** Nếu nhân viên nhập chỉ số mới nhỏ hơn chỉ số cũ cho bất kỳ khoản phí theo chỉ số nào, hệ thống từ chối toàn bộ yêu cầu tạo hóa đơn (không tạo một phần), trả về màn hình nhập liệu kèm dữ liệu đã nhập trước đó (`withInput`) để nhân viên chỉ cần sửa lại giá trị sai mà không phải nhập lại từ đầu, kèm thông báo lỗi cụ thể.

**AF-02 — Căn hộ đã có hóa đơn cho kỳ hạn được chọn.** Khi phát hiện trùng lặp, hệ thống từ chối tạo mới và hiển thị thông báo "Hóa đơn tháng này đã tồn tại cho căn hộ này". Nhân viên có hai lựa chọn tiếp theo: chuyển sang xem/chỉnh sửa hóa đơn đã tồn tại (nếu còn đủ điều kiện chỉnh sửa theo BR-05/BR-06), hoặc chọn kỳ hạn khác nếu việc chọn nhầm tháng/năm là nguyên nhân trùng lặp.

**AF-03 — Căn hộ chưa có dữ liệu diện tích khi áp dụng phí theo diện tích.** Hệ thống áp dụng giá trị mặc định bằng 1 mét vuông để không chặn toàn bộ quy trình tạo hóa đơn, nhưng đây là số liệu không phản ánh đúng thực tế; Ban quản lý cần được cảnh báo (qua giao diện xem trước `previewPhi`) và khuyến nghị bổ sung thuộc tính diện tích trước khi phát hành chính thức.

**AF-04 — Thử sửa hóa đơn đã có lịch sử thanh toán.** Khi nhân viên cố truy cập màn hình chỉnh sửa của một hóa đơn đã phát sinh thanh toán, hệ thống chặn ngay tại bước điều hướng vào trang chỉnh sửa, tự động chuyển hướng về trang chi tiết hóa đơn kèm thông báo lý do; nếu yêu cầu cập nhật vẫn được gửi trực tiếp (ví dụ bằng cách gọi thẳng địa chỉ xử lý, bỏ qua giao diện), hệ thống chặn lại một lần nữa ở tầng xử lý cập nhật, không tin tưởng vào việc điều hướng giao diện đã ngăn được người dùng.

**AF-05 — Thử ghi nhận thanh toán vượt quá công nợ.** Nếu số tiền thanh toán nhập vào (thủ công) vượt quá công nợ hiện tại, hệ thống từ chối ngay tại tầng Controller trước khi gọi đến tầng xử lý trung tâm. Trong trường hợp hiếm gặp mà nhiều khoản thanh toán được gửi gần như đồng thời khiến tổng cộng dồn vượt công nợ dù từng khoản riêng lẻ hợp lệ tại thời điểm kiểm tra ban đầu, tầng xử lý trung tâm phát hiện lại vi phạm sau khi khóa bản ghi hóa đơn và hủy toàn bộ giao dịch (rollback), không có khoản thanh toán nào được ghi nhận trong tình huống này.

**AF-06 — Thanh toán một phần nhiều lần cho đến khi đủ.** Đây là luồng hợp lệ được thiết kế chủ đích (không phải luồng lỗi): cư dân hoặc nhân viên thực hiện nhiều lượt ghi nhận thanh toán nhỏ liên tiếp; ở mỗi lượt, hệ thống tính lại công nợ còn lại và cho phép tiếp tục cho đến khi công nợ về không, tại lượt cuối cùng hóa đơn tự động chuyển trạng thái Đã thanh toán.

**AF-07 — Xóa dòng chi tiết làm hóa đơn không còn dòng chi tiết nào.** Nếu nhân viên xóa hết toàn bộ dòng chi tiết của một hóa đơn (thông qua thao tác xóa từng dòng), hệ thống vẫn giữ lại bản ghi hóa đơn với tổng tiền được tính lại bằng 0; hóa đơn không tự động bị xóa. Nhân viên có thể tiếp tục bổ sung khoản phí mới vào hóa đơn trống này, hoặc chủ động xóa toàn bộ hóa đơn nếu không còn cần thiết (nếu vẫn thỏa BR-10).

**AF-08 — Nhân viên loại trừ khoản phí khi tạo hóa đơn.** Khi một khoản phí dịch vụ áp dụng cho căn hộ theo cấu hình chung nhưng không phát sinh trong kỳ này (ví dụ căn hộ để trống không có người ở, không phát sinh phí gửi xe), nhân viên đánh dấu loại trừ khoản phí đó tại Bước 5 của Mục 5.1; dòng chi tiết tương ứng sẽ không được sinh ra trong hóa đơn của kỳ này, không ảnh hưởng đến cấu hình danh mục phí dịch vụ dài hạn của căn hộ ở các kỳ sau.

---

## 7. LUỒNG NGOẠI LỆ (EXCEPTION FLOWS)

**EF-01 — Không tìm thấy hóa đơn.** Khi một yêu cầu truy cập, chỉnh sửa hoặc xóa hóa đơn với mã định danh không tồn tại (hoặc đã bị xóa mềm) trong hệ thống, framework trả về lỗi không tìm thấy tài nguyên (HTTP 404) trước khi bất kỳ logic nghiệp vụ nào được thực thi, nhờ cơ chế ràng buộc mô hình theo route (route model binding) tự động áp dụng của Laravel.

**EF-02 — Không tìm thấy căn hộ khi tạo hóa đơn.** Nếu mã căn hộ được gửi lên không tồn tại hoặc đã bị xóa mềm, yêu cầu tạo hóa đơn bị từ chối ngay tại tầng validate của form request với thông báo lỗi tương ứng, không đi vào giao dịch tạo hóa đơn.

**EF-03 — Sai trạng thái nghiệp vụ.** Bao gồm các tình huống cố ý bỏ qua điều kiện tiên quyết đã nêu tại Mục 4, ví dụ cố chỉnh sửa hóa đơn Đã thanh toán, cố xóa hóa đơn đã có lịch sử thanh toán, cố ghi nhận thanh toán cho hóa đơn không thuộc phạm vi quản lý được phân công (đối với Manager). Trong mọi trường hợp, hệ thống trả về thông báo lỗi nghiệp vụ rõ ràng bằng tiếng Việt, không để lộ chi tiết kỹ thuật nội bộ, và không thực hiện bất kỳ thay đổi dữ liệu nào.

**EF-04 — Không đủ quyền truy cập.** Cư dân cố truy cập hóa đơn của căn hộ không thuộc phạm vi cư trú hiện tại của mình (bao gồm cả trường hợp căn hộ đã từng cư trú nhưng đã chuyển đi) bị từ chối truy cập với mã lỗi không có quyền (HTTP 403), không tiết lộ hóa đơn đó có tồn tại hay không nhằm tránh rò rỉ thông tin về sự tồn tại của dữ liệu thuộc người khác. Manager cố thao tác trên hóa đơn của căn hộ ngoài phạm vi quản lý được phân công cũng bị chặn tương tự ở tầng Scope truy vấn kết hợp kiểm tra quyền ở tầng FormRequest/Policy.

**EF-05 — Lỗi hệ thống trong quá trình tính chi tiết hóa đơn.** Nếu phát sinh lỗi không mong muốn trong quá trình tính toán chi tiết (ví dụ lỗi truy vấn cơ sở dữ liệu, dữ liệu cấu hình phí dịch vụ bị thiếu trường bắt buộc), toàn bộ giao dịch tạo hóa đơn được hủy bỏ hoàn toàn (rollback), không để lại bản ghi hóa đơn mồ côi không có chi tiết hoặc chi tiết không đầy đủ. Hệ thống ghi nhận lỗi vào nhật ký kỹ thuật (application log) để phục vụ điều tra sự cố, đồng thời hiển thị cho nhân viên thông báo lỗi chung, đề nghị thử lại hoặc liên hệ quản trị viên kỹ thuật nếu lỗi lặp lại.

**EF-06 — Xung đột đồng thời khi ghi nhận thanh toán (race condition).** Khi hai yêu cầu ghi nhận thanh toán cho cùng một hóa đơn được gửi gần như đồng thời (ví dụ nhân viên ghi nhận thủ công đúng lúc cư dân hoàn tất thanh toán trực tuyến), cơ chế khóa độc quyền bản ghi hóa đơn (`lockForUpdate`) đảm bảo yêu cầu thứ hai phải chờ giao dịch của yêu cầu thứ nhất hoàn tất (commit hoặc rollback) trước khi được xử lý, tránh tình trạng cả hai yêu cầu đọc cùng một giá trị công nợ ban đầu và cùng ghi đè lên nhau gây sai lệch số liệu (lost update).

---

## 8. VALIDATION

Bảng dưới đây tổng hợp toàn bộ điều kiện kiểm tra dữ liệu áp dụng cho module Hóa đơn.

| Trường/Đối tượng | Điều kiện kiểm tra | Áp dụng tại |
|---|---|---|
| Căn hộ (khi tạo hóa đơn) | Bắt buộc, phải tồn tại và chưa bị xóa mềm | Tạo hóa đơn |
| Tháng | Bắt buộc, số nguyên từ 1 đến 12 | Tạo hóa đơn, xem trước phí |
| Năm | Bắt buộc, số nguyên, tối thiểu 2020 | Tạo hóa đơn, xem trước phí |
| Cặp (căn hộ, tháng, năm) | Không được trùng với hóa đơn đã tồn tại | Tạo hóa đơn |
| Chỉ số mới (từng khoản phí theo chỉ số) | Số nguyên, phải lớn hơn hoặc bằng chỉ số cũ tương ứng | Tạo hóa đơn, chỉnh sửa hóa đơn |
| Hạn thanh toán (khi chỉnh sửa) | Định dạng ngày hợp lệ, cho phép để trống | Chỉnh sửa hóa đơn |
| Danh sách phí dịch vụ mới thêm | Từng phần tử phải tồn tại trong danh mục phí dịch vụ | Chỉnh sửa hóa đơn |
| Số lượng dịch vụ (khi chỉnh sửa, loại không theo chỉ số) | Phải là số và lớn hơn 0 | Chỉnh sửa hóa đơn |
| Chỉ số mới của dịch vụ vừa thêm khi chỉnh sửa | Lớn hơn hoặc bằng chỉ số cũ nhập kèm | Chỉnh sửa hóa đơn |
| Số tiền thanh toán | Số dương, không vượt quá công nợ hiện tại của hóa đơn (dung sai 0,01) | Ghi nhận thanh toán thủ công và tự động |
| Mã giao dịch thanh toán | Nếu đã tồn tại trong lịch sử, không xử lý lại (trả kết quả cũ) | Ghi nhận thanh toán |
| Điều kiện xóa hóa đơn | Trạng thái khác Đã thanh toán và chưa có lịch sử thanh toán | Xóa hóa đơn |
| Điều kiện sửa chi tiết hóa đơn | Chưa có lịch sử thanh toán (khóa cứng); và đang ở trạng thái Chưa thanh toán (đối với sửa chỉ số/số lượng) | Chỉnh sửa hóa đơn |
| Phạm vi truy cập hóa đơn (cư dân) | Hóa đơn phải thuộc căn hộ mà cư dân đang có quan hệ cư trú với trạng thái đang ở | Xem hóa đơn (cư dân) |
| Phạm vi truy cập hóa đơn (Manager) | Căn hộ của hóa đơn phải thuộc phạm vi quản lý được phân công | Mọi thao tác (Manager) |

---

## 9. TRẠNG THÁI DỮ LIỆU

### 9.1. Trạng thái của hóa đơn

Hóa đơn có bốn giá trị trạng thái được định nghĩa: (1) Chưa thanh toán — trạng thái khởi tạo mặc định khi hóa đơn được phát hành; (2) Đã thanh toán — khi số tiền đã thanh toán lũy kế đạt hoặc vượt tổng tiền hóa đơn; (3) Quá hạn — khi chưa thanh toán đủ và thời điểm hiện tại đã vượt qua hạn thanh toán; (4) Đã hủy — trạng thái được định nghĩa dành cho hóa đơn bị vô hiệu hóa nhưng, như đã nêu tại BR-18, hiện chưa có luồng nghiệp vụ nào trong hệ thống thực sự thiết lập giá trị này.

Việc chuyển đổi giữa ba trạng thái đầu (Chưa thanh toán, Đã thanh toán, Quá hạn) là hoàn toàn tự động, được tính toán lại (không phải chuyển đổi một chiều tuyến tính) mỗi khi có sự kiện kích hoạt: truy cập trang danh sách/chi tiết/chỉnh sửa hóa đơn, hoặc có một khoản thanh toán mới được ghi nhận. Một hóa đơn đang Quá hạn hoàn toàn có thể quay trở lại trạng thái Chưa thanh toán về mặt logic tính toán nếu hạn thanh toán được nhân viên gia hạn thêm (thông qua thao tác chỉnh sửa hạn thanh toán, chỉ khả dụng khi chưa có lịch sử thanh toán).

**Sơ đồ trạng thái (State Diagram) dạng văn bản:**

```
                    [Tạo hóa đơn mới]
                            |
                            v
                 +---------------------+
                 |  CHƯA THANH TOÁN    | <---------------------------+
                 +---------------------+                             |
                    |               |                                |
     (đã qua han_thanh_toan,        | (gia hạn han_thanh_toan,       |
      chưa đủ tiền)                 |  vẫn chưa đủ tiền)             |
                    v               |                                |
                 +--------+          |                                |
                 | QUÁ HẠN | --------+                                |
                 +--------+                                          |
                    |                                                 |
      (thanh toán đủ, so_tien_da_thanh_toan >= tong_tien)             |
                    v                                                 |
              +---------------+                                       |
              | ĐÃ THANH TOÁN |                                        |
              +---------------+                                       |
                                                                       |
      [ĐÃ HỦY] <-- (định nghĩa sẵn, CHƯA có luồng nghiệp vụ kích hoạt tại thời điểm hiện tại)
```

### 9.2. Trạng thái của dòng chi tiết hóa đơn

Dòng chi tiết hóa đơn không có trường trạng thái riêng; vòng đời của nó gắn chặt với hóa đơn cha: tồn tại từ khi được sinh ra trong giao dịch tạo hóa đơn (hoặc thêm vào khi chỉnh sửa), có thể bị xóa cứng khi nhân viên xóa riêng dòng đó hoặc khi toàn bộ hóa đơn cha bị xóa, và không thể tồn tại độc lập nếu hóa đơn cha không còn.

### 9.3. Trạng thái của bản ghi lịch sử thanh toán

Bản ghi lịch sử thanh toán chỉ có một trạng thái duy nhất kể từ khi được tạo: đã ghi nhận (tồn tại). Không có trạng thái trung gian (ví dụ "đang xử lý", "chờ xác nhận") ở tầng dữ liệu của module này — một bản ghi chỉ được tạo ra sau khi toàn bộ điều kiện nghiệp vụ (BR-13, BR-16) đã được xác nhận thỏa mãn trong cùng giao dịch; nếu điều kiện không thỏa, giao dịch bị hủy và không có bản ghi nào được tạo, tức là không tồn tại trạng thái "thanh toán thất bại" được lưu vết ở tầng dữ liệu của module Hóa đơn (trạng thái thất bại của riêng giao dịch cổng thanh toán được xử lý và có thể được lưu vết ở Module 2).

---

## 10. NHẬT KÝ HỆ THỐNG (AUDIT LOG)

Mọi thao tác ghi, sửa, xóa dữ liệu có ảnh hưởng đến tính toàn vẹn tài chính trong module Hóa đơn đều được ghi vào nhật ký hệ thống tập trung, độc lập với bảng nghiệp vụ chính, nhằm phục vụ truy vết và đối soát khi cần thiết. Các hành động bắt buộc phải ghi log bao gồm:

- Tạo mới hóa đơn (hành động INSERT trên bảng hóa đơn), lưu kèm toàn bộ giá trị mới của bản ghi.
- Cập nhật hóa đơn (hành động UPDATE), lưu kèm cả giá trị cũ và giá trị mới trước/sau khi cập nhật, phục vụ so sánh chênh lệch khi cần điều tra.
- Xóa hóa đơn (hành động DELETE), lưu kèm giá trị cũ tại thời điểm xóa.
- Ghi nhận một khoản thanh toán mới (hành động INSERT trên bảng lịch sử thanh toán), lưu kèm toàn bộ thông tin giao dịch: số tiền, phương thức, mã giao dịch, người thanh toán, nguồn tạo.

Mỗi bản ghi nhật ký lưu tối thiểu các thông tin: người thực hiện (nếu hành động do nhân viên thực hiện; để trống nếu hành động do chính cư dân tự thực hiện, ví dụ tự thanh toán trực tuyến, vì trường người thực hiện tham chiếu đến hồ sơ nhân viên, không tham chiếu đến hồ sơ cư dân), thời điểm thực hiện, loại hành động, tên bảng dữ liệu bị tác động, mã định danh bản ghi bị tác động, và toàn bộ giá trị dữ liệu cũ/mới ở định dạng có cấu trúc để có thể tra cứu và so sánh sau này. Nhật ký hệ thống, một khi đã ghi, không có cơ chế chỉnh sửa hay xóa trong toàn bộ hệ thống, đảm bảo tính chất bất biến cần thiết của một sổ nhật ký kiểm toán. Quyền xem nhật ký hệ thống hiện chỉ dành cho Admin.

---

## 11. THÔNG BÁO

Tại thời điểm biên soạn tài liệu, module Hóa đơn chưa tích hợp cơ chế gửi thông báo tự động qua email hoặc tin nhắn khi phát hành hóa đơn mới, khi hóa đơn chuyển sang trạng thái quá hạn, hoặc khi một khoản thanh toán được ghi nhận thành công. Đây là hạn chế cần được ghi nhận rõ ràng trong tài liệu để tránh kỳ vọng sai về hành vi hệ thống hiện tại. Kênh thông báo duy nhất cư dân có thể tiếp cận là chủ động đăng nhập vào hệ thống để tự tra cứu tình trạng hóa đơn và công nợ của mình; hệ thống cũng có một bảng tin thông báo nội bộ dùng chung cho toàn hệ thống, nhưng bảng tin này được nhân viên soạn và đăng thủ công, không tự động sinh nội dung theo sự kiện phát sinh hóa đơn hay thay đổi công nợ.

Trong phạm vi định hướng phát triển tiếp theo, các sự kiện nên được xem xét bổ sung cơ chế thông báo tự động bao gồm: thông báo cho cư dân ngay khi hóa đơn kỳ mới được phát hành cho căn hộ mình cư trú; nhắc nhở trước một khoảng thời gian nhất định so với hạn thanh toán đối với các hóa đơn còn công nợ; thông báo ngay khi hóa đơn chuyển sang trạng thái quá hạn; và xác nhận cho cư dân ngay khi một khoản thanh toán của họ được ghi nhận thành công, kênh gợi ý bao gồm cả thông báo thời gian thực trong ứng dụng (tương tự cơ chế đã áp dụng cho Module 3 — Đặt lịch tiện ích) lẫn email.

---

## 12. PHÂN QUYỀN

Việc phân quyền chi tiết theo từng hành động đã được trình bày tổng quan tại Mục 2.4; phần này bổ sung nguyên tắc kỹ thuật áp dụng phân quyền.

Đối với Admin và Manager, phân quyền được thực thi ở hai lớp: lớp thứ nhất là middleware xác thực theo guard riêng cho nhân viên, phân biệt Admin (có cờ quản trị) với Manager (không có cờ quản trị); lớp thứ hai là cơ chế giới hạn phạm vi dữ liệu (Scope) áp dụng cho Manager tại tầng truy vấn, đảm bảo mọi câu truy vấn hóa đơn của Manager tự động được lọc theo phạm vi tòa nhà/căn hộ được phân công quản lý mà không cần lặp lại điều kiện lọc thủ công ở từng Controller — nguyên tắc này nhất quán với cách hệ thống chặn Manager truy cập dữ liệu nhân sự ngoài phạm vi ở các module khác.

Đối với cư dân, phân quyền dựa hoàn toàn vào quan hệ cư trú hiện hành (bảng trung gian cư dân-căn hộ với trạng thái đang ở); mọi truy vấn hóa đơn khởi nguồn từ cư dân đều bắt buộc đi qua bước xác định tập hợp căn hộ cư dân đang cư trú hợp lệ trước khi truy vấn hóa đơn, không có đường truy cập hóa đơn nào của cư dân bỏ qua bước xác định phạm vi này. Đây là điểm mấu chốt đảm bảo nguyên tắc nghiệp vụ "tất cả cư dân hợp lệ của một căn hộ đều được xem hóa đơn của căn hộ đó" đồng thời không rò rỉ hóa đơn của căn hộ khác.

Ai được thanh toán: cư dân chỉ được thanh toán (trực tuyến) cho hóa đơn thuộc phạm vi căn hộ mình đang cư trú; nhân viên (Admin/Manager) được ghi nhận thanh toán (thủ công hoặc khởi tạo trực tuyến hộ) cho bất kỳ hóa đơn nào trong phạm vi quản lý của mình, không giới hạn theo việc bản thân nhân viên có phải là cư dân của căn hộ đó hay không (nhân viên không phải là cư dân).

Ai được duyệt: module Hóa đơn không có khái niệm "duyệt" hóa đơn theo quy trình phê duyệt nhiều cấp — hóa đơn được coi là chính thức có hiệu lực ngay khi tạo thành công, không qua bước phê duyệt trung gian nào trước khi hiển thị cho cư dân.

---

## 13. RÀNG BUỘC DỮ LIỆU

Hóa đơn có khóa ngoại bắt buộc trỏ đến căn hộ và khóa ngoại tùy chọn trỏ đến nhân viên thực hiện cập nhật gần nhất; dòng chi tiết hóa đơn có khóa ngoại bắt buộc trỏ đến hóa đơn cha; bản ghi lịch sử thanh toán có khóa ngoại bắt buộc trỏ đến hóa đơn, khóa ngoại tùy chọn trỏ đến cư dân thực hiện thanh toán và đến danh mục nguồn tạo giao dịch. Bảng hóa đơn và bảng dòng chi tiết đều tham chiếu đến các bảng danh mục liên quan (căn hộ, nhân viên) bằng cách cho phép truy vấn cả những bản ghi cha đã bị xóa mềm (`withTrashed`), đảm bảo hóa đơn lịch sử vẫn hiển thị đầy đủ thông tin căn hộ/nhân viên dù các bản ghi đó về sau có bị xóa khỏi danh mục đang hoạt động.

Hóa đơn và danh mục phí dịch vụ áp dụng cơ chế xóa mềm (soft delete): một hóa đơn bị xóa (thỏa BR-10) không bị loại bỏ vật lý khỏi cơ sở dữ liệu mà chỉ được đánh dấu thời điểm xóa, cho phép khôi phục hoặc tra cứu lại khi cần thiết cho mục đích kiểm toán, đồng thời loại trừ khỏi các truy vấn nghiệp vụ thông thường. Ngược lại, dòng chi tiết hóa đơn và bản ghi lịch sử thanh toán không áp dụng xóa mềm: dòng chi tiết bị xóa cứng hoàn toàn khi bị xóa riêng lẻ hoặc khi hóa đơn cha bị xóa (vì không có ý nghĩa lưu vết độc lập ngoài hóa đơn cha); bản ghi lịch sử thanh toán về nguyên tắc không có route xóa nào cả — không xóa mềm cũng không xóa cứng — thể hiện đúng bản chất sổ cái chỉ được thêm vào của dữ liệu tài chính.

Không cho xóa: hóa đơn ở trạng thái Đã thanh toán, hoặc hóa đơn đã có bất kỳ bản ghi lịch sử thanh toán nào dù trạng thái hiện tại là gì (BR-10); dòng chi tiết của một hóa đơn đã có lịch sử thanh toán (BR-05); bản ghi lịch sử thanh toán dưới mọi hình thức, mọi vai trò.

Điều kiện khóa: chỉnh sửa chi tiết hóa đơn bị khóa hoàn toàn ngay khi phát sinh khoản thanh toán đầu tiên (BR-05); chỉnh sửa chỉ số/số lượng dòng chi tiết chỉ mở khi hóa đơn ở đúng trạng thái Chưa thanh toán (BR-06).

Ràng buộc tổ hợp duy nhất theo nghiệp vụ (căn hộ, tháng, năm) hiện được thực thi ở tầng ứng dụng chứ chưa có ràng buộc duy nhất tương ứng ở tầng cơ sở dữ liệu — đã phân tích rủi ro liên quan tại BR-01, khuyến nghị bổ sung ràng buộc duy nhất composite ở tầng cơ sở dữ liệu trong các đợt nâng cấp schema tiếp theo để loại bỏ hoàn toàn rủi ro race condition tại nguồn thay vì chỉ dựa vào kiểm tra ứng dụng.

---

## 14. HIỆU NĂNG

Việc cập nhật trạng thái quá hạn hàng loạt (BR-17) được thực hiện bằng các câu lệnh cập nhật hàng loạt theo điều kiện (không lặp qua từng bản ghi ở tầng ứng dụng rồi cập nhật từng cái một), giảm thiểu số lượt round-trip đến cơ sở dữ liệu khi danh sách hóa đơn lớn. Việc tính tổng công nợ phục vụ thống kê, dashboard được thực hiện bằng truy vấn tổng hợp có điều kiện (`SUM` kèm `CASE WHEN`) ngay ở tầng cơ sở dữ liệu, tránh việc phải tải toàn bộ bản ghi hóa đơn về tầng ứng dụng rồi mới tính tổng bằng vòng lặp — cách tiếp cận này đặc biệt quan trọng đối với dashboard Ban quản lý cần tổng hợp công nợ trên quy mô toàn tòa nhà hoặc toàn hệ thống.

Danh sách hóa đơn, danh sách lịch sử thanh toán khi hiển thị cho người dùng đều cần áp dụng phân trang (pagination), không tải toàn bộ dữ liệu một lần, đặc biệt quan trọng đối với các căn hộ đã tích lũy lịch sử thanh toán nhiều kỳ hoặc đối với Admin xem toàn hệ thống nhiều tòa nhà.

Việc ghi nhận thanh toán bắt buộc thực hiện trong một giao dịch cơ sở dữ liệu duy nhất kết hợp khóa độc quyền (`lockForUpdate`) trên đúng bản ghi hóa đơn liên quan, không khóa toàn bảng, nhằm vừa đảm bảo tính đúng đắn của việc tính lại công nợ vừa không gây nghẽn hiệu năng khi nhiều hóa đơn khác nhau được thanh toán đồng thời — khóa chỉ tạo điểm nghẽn tuần tự hóa cho các thao tác cùng nhắm vào một hóa đơn cụ thể.

Đối với các trường phục vụ tra cứu và lọc thường xuyên (căn hộ, kỳ tháng/năm, trạng thái công nợ trên bảng hóa đơn; hóa đơn trên bảng chi tiết và bảng lịch sử thanh toán), cần đảm bảo có chỉ mục (index) phù hợp ở tầng cơ sở dữ liệu tương ứng với các điều kiện lọc/join thường dùng ở Mục 3 và Mục 5, để duy trì thời gian phản hồi ổn định khi khối lượng hóa đơn tích lũy theo thời gian tăng lên đáng kể qua nhiều kỳ vận hành.

Module Hóa đơn hiện không sử dụng hàng đợi xử lý nền (queue) hay tiến trình lập lịch (scheduler) — toàn bộ xử lý là đồng bộ trong vòng đời một request HTTP. Điều này phù hợp với khối lượng nghiệp vụ hiện tại (tạo/sửa/xóa hóa đơn, ghi nhận thanh toán là các thao tác đơn lẻ, không phải xử lý hàng loạt tức thời trên diện rộng), nhưng đối với chức năng tạo hóa đơn hàng loạt được đặc tả tại Mục 5.3, khi số lượng căn hộ trong một đợt tạo lớn (ví dụ toàn bộ một tòa nhà hàng trăm căn hộ), nên cân nhắc đưa việc xử lý từng căn hộ vào hàng đợi nền để tránh yêu cầu HTTP bị timeout, đồng thời báo cáo tiến độ xử lý theo thời gian thực cho nhân viên khởi tạo đợt tạo hàng loạt.

---

## 15. BẢO MẬT

Mọi hành động ghi/sửa/xóa hóa đơn đều bắt buộc kiểm tra quyền tương ứng với vai trò của tác nhân gọi đến (Mục 12) trước khi thực thi bất kỳ thay đổi dữ liệu nào; việc kiểm tra quyền không chỉ dừng ở việc ẩn nút bấm trên giao diện mà được lặp lại ở tầng xử lý phía máy chủ cho mọi route, đảm bảo không thể "sửa URL" hoặc gọi thẳng địa chỉ xử lý để bỏ qua kiểm tra quyền hiển thị trên giao diện (đã minh chứng cụ thể tại AF-04 đối với hành vi cố tình chỉnh sửa hóa đơn đã khóa).

Chống thanh toán trùng được đảm bảo ở hai lớp: lớp nghiệp vụ dựa trên kiểm tra mã giao dịch đã tồn tại trong lịch sử thanh toán trước khi xử lý (BR-16), và lớp đồng thời dựa trên khóa độc quyền bản ghi hóa đơn trong giao dịch cơ sở dữ liệu (EF-06), đảm bảo ngay cả khi hai yêu cầu ghi nhận cùng một giao dịch được gửi gần như đồng thời (ví dụ do người dùng bấm nút xác nhận nhiều lần liên tiếp — hiện tượng double submit), chỉ một trong hai được xử lý thực sự, yêu cầu còn lại nhận về kết quả của yêu cầu đã xử lý trước mà không tạo bản ghi trùng lặp.

Việc chuyển hướng và xử lý dữ liệu nhạy cảm về tài chính (số tiền thanh toán, mã giao dịch) luôn kiểm tra tính hợp lệ và phạm vi thuộc quyền của tác nhân gọi trước khi ghi nhận, không tin tưởng bất kỳ giá trị nào gửi từ phía trình duyệt liên quan đến đơn giá hoặc số lượng của các dòng tính theo diện tích (BR-09) hay tính theo phương tiện — toàn bộ các giá trị này luôn được tính lại hoàn toàn ở phía máy chủ dựa trên dữ liệu gốc lưu trong hệ thống, không chấp nhận giá trị client tự tính rồi gửi lên.

Mọi form ghi/sửa/xóa dữ liệu hóa đơn tuân theo cơ chế bảo vệ CSRF chuẩn của framework nền tảng, không có ngoại lệ nào được cấu hình bỏ qua kiểm tra này cho các route thuộc module Hóa đơn (khác với các route công khai không xác thực dành riêng cho callback cổng thanh toán ở Module 2, vốn có cơ chế xác thực khác dựa trên chữ ký số, không dùng CSRF token vì nguồn gọi đến là máy chủ bên thứ ba, không phải trình duyệt người dùng).

Idempotency của hành động ghi nhận thanh toán (BR-16) không chỉ có ý nghĩa chống trùng lặp về mặt hiển thị mà còn là biện pháp bảo mật quan trọng chống lại việc replay lại một yêu cầu thanh toán đã xử lý (dù vô tình do lỗi mạng khiến client gửi lại request, hay cố ý bởi một bên muốn khai thác việc gửi lại yêu cầu nhiều lần để cộng dồn tiền đã thanh toán vượt quá thực tế) — vì hàm xử lý trung tâm luôn trả về đúng bản ghi đã tồn tại thay vì tạo thêm bản ghi mới khi phát hiện mã giao dịch trùng lặp.

---

*(Hết Module 1 — Quản lý Hóa đơn. Tài liệu tiếp tục với Module 2 — Thanh toán trực tuyến.)*
