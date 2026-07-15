/**
 * Helper dùng chung cho các trang Đặt lịch tiện ích (Admin/Manager/Resident)
 * để lắng nghe broadcast qua Reverb/Echo mà không cần lặp lại danh sách 7
 * event ở từng view. Không polling — chỉ cập nhật khi server thật sự
 * broadcast (ShouldBroadcastNow, ngay trong request tạo/duyệt/hủy/từ chối/
 * hoàn thành, xem BookingRealtimeService).
 */

const CAC_EVENT_BOOKING = [
    '.booking.created',
    '.booking.approved',
    '.booking.rejected',
    '.booking.cancelled',
    '.booking.completed',
    '.booking.waiting-changed',
    '.booking.slot-updated',
];

const CAC_EVENT_BOOKING_CA_NHAN = [
    '.booking.approved',
    '.booking.rejected',
    '.booking.cancelled',
    '.booking.completed',
];

function tenSuKien(tenCoDau) {
    return tenCoDau.replace(/^\./, '');
}

/**
 * Admin/Manager: nhận mọi thay đổi của mọi lượt đặt lịch (kênh private
 * "dat-lich-tien-ich.nhanvien"). onEvent(tenSuKien, payload).
 */
export function subscribeNhanVienBooking(onEvent) {
    if (!window.Echo) return () => {};
    const channel = window.Echo.private('dat-lich-tien-ich.nhanvien');
    CAC_EVENT_BOOKING.forEach((e) => channel.listen(e, (payload) => onEvent(tenSuKien(e), payload)));
    return () => window.Echo.leaveChannel('private-dat-lich-tien-ich.nhanvien');
}

/**
 * Resident: chỉ nhận thay đổi trên đúng các lượt đặt lịch của chính mình
 * (kênh private "dat-lich-tien-ich.cudan.{id}").
 */
export function subscribeCuDanBooking(cuDanId, onEvent) {
    if (!window.Echo || !cuDanId) return () => {};
    const channel = window.Echo.private('dat-lich-tien-ich.cudan.' + cuDanId);
    CAC_EVENT_BOOKING_CA_NHAN.forEach((e) => channel.listen(e, (payload) => onEvent(tenSuKien(e), payload)));
    return () => window.Echo.leaveChannel('private-dat-lich-tien-ich.cudan.' + cuDanId);
}
