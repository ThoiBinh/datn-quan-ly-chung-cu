/**
 * Toast Notification realtime cho module Đặt lịch tiện ích — dùng chung
 * window.Echo (Reverb) đã khởi tạo ở echo.js và window.Toast (toast.js).
 *
 * Nguyên tắc lọc actor: mỗi event mang sẵn actor_loai ('nhanvien' | 'cudan' |
 * 'system' — xem BookingRealtimeService::nguoiThucHien()). Một toast chỉ
 * hiển thị khi actor_loai KHÁC loại tài khoản đang xem trang (không hiển thị
 * cho chính người vừa thao tác; 'system' — FIFO/Scheduler tự động — luôn
 * hiển thị cho cả hai phía vì không ai đang "thực hiện" nó).
 */

import { show as showToast } from './toast.js';

const EVENTS_BY_VIEWER = {
    nhanvien: ['booking.created', 'booking.cancelled', 'booking.rescheduled'],
    cudan: ['booking.approved', 'booking.rejected', 'booking.cancelled', 'booking.rescheduled'],
};

function xayDungNoiDung(tenSuKien, payload, viewerLoai) {
    const tenTienIch = payload.ten_tien_ich || 'tiện ích';
    const soCanHo = payload.so_can_ho || '—';

    switch (tenSuKien) {
        case 'booking.created':
            return { title: 'Có đặt lịch tiện ích mới', message: `Căn hộ ${soCanHo} vừa đặt ${tenTienIch}.`, status: 'info' };
        case 'booking.approved':
            return { title: 'Đặt lịch đã được duyệt', message: `Đặt lịch ${tenTienIch} của bạn đã được duyệt.`, status: 'success' };
        case 'booking.rejected':
            return { title: 'Đặt lịch bị từ chối', message: `Đặt lịch ${tenTienIch} của bạn đã bị từ chối.`, status: 'error' };
        case 'booking.cancelled':
            return viewerLoai === 'nhanvien'
                ? { title: 'Cư dân đã hủy đặt lịch', message: `Cư dân căn hộ ${soCanHo} đã hủy đặt ${tenTienIch}.`, status: 'warning' }
                : { title: 'Đặt lịch đã bị hủy', message: `Đặt lịch ${tenTienIch} của bạn đã bị hủy.`, status: 'warning' };
        case 'booking.rescheduled':
            return { title: 'Thay đổi giờ đặt', status: 'info', message: viewerLoai === 'nhanvien'
                ? `Cư dân đã thay đổi thời gian đặt ${tenTienIch}.`
                : `Ban quản lý đã thay đổi thời gian đặt ${tenTienIch}.` };
        default:
            return null;
    }
}

// Chống gửi trùng khi Echo/Reverb re-deliver cùng một message (VD reconnect).
const daHienThi = new Set();
function laTrung(tenSuKien, payload) {
    const key = `${tenSuKien}:${payload.id}:${payload.trang_thai}:${payload.thoi_gian_bat_dau}`;
    if (daHienThi.has(key)) return true;
    daHienThi.add(key);
    setTimeout(() => daHienThi.delete(key), 5000);
    return false;
}

function moDuongDan(viewerLoai, payload) {
    const routes = window.DatLichTienIchRoutes || {};
    if (viewerLoai === 'nhanvien' && routes.show) {
        window.location.href = routes.show.replace('__ID__', payload.id);
    } else if (viewerLoai === 'cudan' && routes.index) {
        window.location.href = routes.index;
    }
}

export function initBookingToast() {
    const user = window.AppUser;
    if (!user || !window.Echo || !EVENTS_BY_VIEWER[user.loai]) return;

    const channel = user.loai === 'nhanvien'
        ? window.Echo.private('dat-lich-tien-ich.nhanvien')
        : window.Echo.private('dat-lich-tien-ich.cudan.' + user.id);

    EVENTS_BY_VIEWER[user.loai].forEach((tenSuKien) => {
        channel.listen('.' + tenSuKien, (payload) => {
            // Không hiển thị cho chính người vừa thao tác.
            if (payload.actor_loai === user.loai) return;
            if (laTrung(tenSuKien, payload)) return;

            const noiDung = xayDungNoiDung(tenSuKien, payload, user.loai);
            if (!noiDung) return;

            showToast({ ...noiDung, onClick: () => moDuongDan(user.loai, payload) });
        });
    });
}
