import './charts/revenue-widget.js';
import './charts/dat-lich-dashboard.js';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

import { subscribeNhanVienBooking, subscribeCuDanBooking } from './dat-lich-realtime';
import { validateKhungGioTienIch } from './dat-lich-time-validation';
import { taoDanhSachGio, coTheLaGioBatDau, locDanhSachGioKetThuc } from './dat-lich-time-slots';
import { initChuHoCheck } from './cu-dan-can-ho-chu-ho-check';
// Blade dùng <script> thường (không phải ES module) nên phải lộ ra qua window
// để các view dat-lich-tien-ich gọi được.
window.DatLichRealtime = { subscribeNhanVienBooking, subscribeCuDanBooking };
window.DatLichTimeValidation = { validate: validateKhungGioTienIch };
window.DatLichTimeSlots = { taoDanhSachGio, coTheLaGioBatDau, locDanhSachGioKetThuc };
window.ChuHoCheck = { init: initChuHoCheck };
