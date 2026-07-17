export const KHUNG_GIO_TIEN_ICH_LOI = {
    PHUT_KHONG_HOP_LE: 'Chỉ được chọn giờ tròn hoặc giờ rưỡi (00 hoặc 30 phút).',
    THOI_LUONG_TOI_THIEU: 'Thời gian sử dụng phải tối thiểu 01 giờ.',
    KET_THUC_TRUOC_BAT_DAU: 'Thời gian kết thúc phải sau thời gian bắt đầu.',
};

function phutHopLe(datetimeLocalValue) {
    const phut = new Date(datetimeLocalValue).getMinutes();
    return phut === 0 || phut === 30;
}

/**
 * Kiểm tra tức thời cặp thời gian bắt đầu/kết thúc của form đặt lịch tiện
 * ích, gương với ValidatesKhungGioTienIch ở backend. Backend vẫn là nguồn
 * kiểm tra cuối cùng (không thể vượt qua bằng DevTools/Postman).
 */
export function validateKhungGioTienIch(batDau, ketThuc) {
    const loi = { batDau: '', ketThuc: '' };

    if (batDau && !phutHopLe(batDau)) {
        loi.batDau = KHUNG_GIO_TIEN_ICH_LOI.PHUT_KHONG_HOP_LE;
    }
    if (ketThuc && !phutHopLe(ketThuc)) {
        loi.ketThuc = KHUNG_GIO_TIEN_ICH_LOI.PHUT_KHONG_HOP_LE;
    }

    if (batDau && ketThuc && !loi.batDau && !loi.ketThuc) {
        const start = new Date(batDau);
        const end = new Date(ketThuc);

        if (end <= start) {
            loi.ketThuc = KHUNG_GIO_TIEN_ICH_LOI.KET_THUC_TRUOC_BAT_DAU;
        } else if ((end - start) / 60000 < 60) {
            loi.ketThuc = KHUNG_GIO_TIEN_ICH_LOI.THOI_LUONG_TOI_THIEU;
        }
    }

    return loi;
}
