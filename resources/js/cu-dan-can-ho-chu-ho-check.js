/**
 * Kiểm tra tức thời (không cần Submit) căn hộ đã có Chủ hộ đang cư trú hay
 * chưa, gương với ChuHoDuyNhat::daCoChuHo() ở backend. Backend vẫn là nguồn
 * kiểm tra cuối cùng (không thể vượt qua bằng DevTools/Postman/CURL).
 */
export function initChuHoCheck({ canHoSelect, vaiTroSelect, submitBtn, errorBox, checkUrl, ignoreId }) {
    if (!canHoSelect || !vaiTroSelect || !submitBtn || !errorBox || !checkUrl) {
        return;
    }

    let requestToken = 0;

    function laVaiTroChuHo() {
        const option = vaiTroSelect.options[vaiTroSelect.selectedIndex];
        return !!(option && option.dataset.chuHo === '1');
    }

    function hienLoi(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
    }

    function xoaLoi() {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
    }

    function kiemTra() {
        xoaLoi();

        if (!laVaiTroChuHo() || !canHoSelect.value) {
            return;
        }

        const token = ++requestToken;
        const params = new URLSearchParams({ can_ho: canHoSelect.value });
        if (ignoreId) {
            params.set('ignore_id', ignoreId);
        }

        fetch(`${checkUrl}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then((res) => res.json())
            .then((data) => {
                if (token !== requestToken) return; // bỏ qua phản hồi cũ (race condition)
                if (data.has_owner) {
                    hienLoi('Căn hộ này đã có Chủ hộ. Mỗi căn hộ chỉ được phép có một Chủ hộ.');
                }
            });
    }

    canHoSelect.addEventListener('change', kiemTra);
    vaiTroSelect.addEventListener('change', kiemTra);

    // Kiểm tra ngay khi tải trang (trường hợp form được khôi phục lựa chọn cũ
    // sau khi submit lỗi hoặc khi trình duyệt tự điền lại giá trị).
    kiemTra();
}
