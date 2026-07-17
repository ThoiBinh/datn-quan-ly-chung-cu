/**
 * Design tokens của Toast Notification — 1 nguồn duy nhất cho màu/shadow/bo
 * góc, để đổi theme không phải sửa rải rác trong toast.js.
 *
 * Nguyên tắc thiết kế (Linear/Vercel/Notion, không phải Bootstrap alert):
 * khung toast (SHELL_CLASS) LUÔN trung tính (kính mờ + viền mềm), KHÔNG đổi
 * màu nền theo status — chỉ icon và progress bar mang màu. Đây là điểm khác
 * biệt cốt lõi so với "alert-success/alert-danger" kiểu AdminLTE.
 *
 * Toàn bộ class là literal đầy đủ (không nối chuỗi động) để Tailwind v4 quét
 * được qua @source 'resources/**\/*.js' trong app.css.
 */

/**
 * Khung kính mờ dùng chung cho mọi status — 3 lớp shadow tạo chiều sâu kiểu
 * macOS/iOS (định nghĩa ở app.css qua @utility toast-shadow*, xem lý do ở đó).
 * Chỉ transition-shadow (không kèm transform): transform do WAAPI đảm nhiệm
 * (enter/exit/progress) và do thao tác kéo/vuốt gán trực tiếp — để CSS
 * transition xen vào transform sẽ làm thao tác kéo bị trễ, không còn cảm
 * giác "1:1 theo ngón tay/con trỏ".
 */
export const SHELL_CLASS =
    'relative flex w-full items-start gap-3 overflow-hidden rounded-2xl border ' +
    'border-black/[0.06] bg-white/75 p-4 pr-3 backdrop-blur-xl backdrop-saturate-150 ' +
    'toast-shadow dark:toast-shadow-dark ' +
    'transition-shadow duration-200 ease-out will-change-transform ' +
    'hover:-translate-y-0.5 hover:toast-shadow-hover dark:hover:toast-shadow-hover-dark ' +
    'dark:border-white/[0.08] dark:bg-slate-900/70';

export const TITLE_CLASS = 'truncate text-[15px] font-semibold leading-5 text-gray-900 dark:text-white';
export const MESSAGE_CLASS = 'mt-1 text-sm font-normal leading-5 text-gray-500 dark:text-slate-400';
export const TIMESTAMP_CLASS = 'flex-shrink-0 text-xs font-medium text-gray-400 dark:text-slate-500';

export const CLOSE_BUTTON_CLASS =
    'flex-shrink-0 rounded-full p-1 text-gray-300 transition-colors duration-150 ' +
    'hover:bg-gray-900/[0.06] hover:text-gray-500 ' +
    'dark:text-slate-500 dark:hover:bg-white/10 dark:hover:text-slate-300';

export const PROGRESS_TRACK_CLASS = 'absolute inset-x-0 bottom-0 h-[2px] bg-gray-900/[0.06] dark:bg-white/10';
export const PROGRESS_BAR_BASE_CLASS = 'h-full w-full origin-left rounded-full bg-gradient-to-r';

/** WCAG AA: cặp 50/600 (light) và 500/15%-400 (dark) đã dùng khắp dự án, đảm bảo tương phản > 4.5:1. */
export const STATUS_THEME = {
    success: {
        iconWrap: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400',
        progress: 'from-emerald-400 to-emerald-600',
        role: 'status',
    },
    error: {
        iconWrap: 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400',
        progress: 'from-red-400 to-red-600',
        role: 'alert',
    },
    warning: {
        iconWrap: 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400',
        progress: 'from-amber-400 to-amber-600',
        role: 'status',
    },
    info: {
        iconWrap: 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400',
        progress: 'from-blue-400 to-blue-600',
        role: 'status',
    },
    loading: {
        iconWrap: 'bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-300',
        progress: 'from-gray-300 to-gray-400',
        role: 'status',
    },
};

export const ICON_WRAP_BASE_CLASS = 'flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full';
