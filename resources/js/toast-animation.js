/**
 * Animation system — chỉ animate transform/opacity (Web Animations API, chạy
 * trên compositor thread, không phải main thread) để giữ 60fps kể cả khi
 * nhiều toast xuất hiện/biến mất cùng lúc. KHÔNG dùng transition CSS + toggle
 * class (dễ vô tình animate luôn width/height khi nội dung đổi ở update()).
 */

export const EASING = 'cubic-bezier(.22,1,.36,1)';
export const DURATION_ENTER = 320;
export const DURATION_EXIT = 220;
export const DEFAULT_DURATION = 4000;

/** Toast trượt vào nhẹ từ trên xuống + scale — cảm giác Framer Motion, không "nảy". */
export function playEnter(el) {
    return el.animate(
        [
            { opacity: 0, transform: 'translateY(-8px) scale(0.96)' },
            { opacity: 1, transform: 'translateY(0) scale(1)' },
        ],
        { duration: DURATION_ENTER, easing: EASING, fill: 'both' }
    );
}

/**
 * Toast biến mất — dx khác 0 khi bị vuốt/kéo để thoát cùng hướng thao tác của
 * người dùng (dx = 0 khi tự hết giờ hoặc bấm nút đóng).
 */
export function playExit(el, dx = 0) {
    return el.animate(
        [
            { opacity: 1, transform: 'translateX(0) scale(1)' },
            { opacity: 0, transform: `translateX(${dx}px) scale(0.96)` },
        ],
        { duration: DURATION_EXIT, easing: EASING, fill: 'forwards' }
    );
}

/**
 * Progress bar co lại bằng scaleX (KHÔNG dùng width) — scaleX là transform,
 * không kích hoạt layout/reflow như đổi width, đồng thời .pause()/.play()
 * của Animation object cho phép tạm dừng khi người dùng hover/kéo toast.
 */
export function playProgress(barEl, duration) {
    return barEl.animate(
        [{ transform: 'scaleX(1)' }, { transform: 'scaleX(0)' }],
        { duration, easing: 'linear', fill: 'forwards' }
    );
}
