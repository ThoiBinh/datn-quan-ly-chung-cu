function toPhut(hhmm) {
    const [h, m] = hhmm.split(':').map(Number);
    return h * 60 + m;
}

function toHHMM(soPhut) {
    const h = Math.floor(soPhut / 60).toString().padStart(2, '0');
    const m = (soPhut % 60).toString().padStart(2, '0');
    return `${h}:${m}`;
}

/**
 * Sinh danh sách mốc giờ 00/30 phút trong khoảng giờ mở cửa → giờ đóng cửa
 * của tiện ích (không hard-code). Ví dụ mở 08:00, đóng 22:00 → 08:00, 08:30,
 * ..., 22:00. Nếu giờ mở/đóng không nằm trên lưới 30 phút, làm tròn vào bên
 * trong khung giờ hoạt động (không sinh mốc trước giờ mở hoặc sau giờ đóng).
 */
export function taoDanhSachGio(gioMoCua, gioDongCua) {
    if (!gioMoCua || !gioDongCua) return [];

    const moPhut = toPhut(gioMoCua);
    const dongPhut = toPhut(gioDongCua);
    const batDau = Math.ceil(moPhut / 30) * 30;
    const ketThuc = Math.floor(dongPhut / 30) * 30;

    const slots = [];
    for (let t = batDau; t <= ketThuc; t += 30) {
        slots.push(toHHMM(t));
    }

    return slots;
}

/**
 * Một mốc có thể chọn làm giờ bắt đầu chỉ khi vẫn còn đủ thời lượng tối
 * thiểu trước giờ đóng cửa.
 */
export function coTheLaGioBatDau(slot, gioDongCua, thoiLuongToiThieuPhut = 60) {
    if (!gioDongCua) return false;

    return toPhut(gioDongCua) - toPhut(slot) >= thoiLuongToiThieuPhut;
}

/**
 * Lọc danh sách giờ kết thúc hợp lệ theo giờ bắt đầu đã chọn: sau giờ bắt
 * đầu và tối thiểu thoiLuongToiThieuPhut. Không giới hạn tối đa theo mặc
 * định (hiển thị hết các mốc tới giờ đóng cửa); backend vẫn là nguồn kiểm
 * tra cuối cùng cho trần thời lượng tối đa.
 */
export function locDanhSachGioKetThuc(danhSachGio, gioBatDau, thoiLuongToiThieuPhut = 60, thoiLuongToiDaPhut = Infinity) {
    if (!gioBatDau) return [];

    const batDauPhut = toPhut(gioBatDau);

    return danhSachGio.filter((slot) => {
        const chenhLech = toPhut(slot) - batDauPhut;
        return chenhLech >= thoiLuongToiThieuPhut && chenhLech <= thoiLuongToiDaPhut;
    });
}
