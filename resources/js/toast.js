/**
 * Toast Notification — public API dùng chung cho toàn hệ thống.
 *
 *   Toast.show({ title, message, status, duration, showTimestamp, onClick })
 *   Toast.update(id, { title, message, status, ... })   // sửa tại chỗ, không tạo mới
 *   Toast.dismiss(id)
 *   Toast.dismissAll()
 *
 * Kiến trúc: file này CHỈ lo lắp ráp DOM + tương tác (drag/ESC/click) + gọi
 * WAAPI animation; toàn bộ màu sắc/kích thước nằm ở toast-theme.js, animation
 * thuần ở toast-animation.js, hàng đợi thuần ở toast-manager.js — sửa 1 khía
 * cạnh không phải đọc lại cả file.
 */

import { getIcon } from './toast-icons';
import {
    SHELL_CLASS, TITLE_CLASS, MESSAGE_CLASS, TIMESTAMP_CLASS, CLOSE_BUTTON_CLASS,
    PROGRESS_TRACK_CLASS, PROGRESS_BAR_BASE_CLASS, ICON_WRAP_BASE_CLASS, STATUS_THEME,
} from './toast-theme';
import { playEnter, playExit, playProgress, DEFAULT_DURATION } from './toast-animation';
import { ToastManager } from './toast-manager';

const CONTAINER_ID = 'app-toast-container';
const SWIPE_THRESHOLD = 80;
const DRAG_DETECT_THRESHOLD = 4;

// ── Container responsive: Desktop top-right / Tablet top-center / Mobile bottom-center ──
function layContainer() {
    let el = document.getElementById(CONTAINER_ID);
    if (el) return el;

    el = document.createElement('div');
    el.id = CONTAINER_ID;
    el.setAttribute('aria-live', 'polite');
    el.setAttribute('aria-atomic', 'false');
    el.className = [
        'pointer-events-none fixed z-[100] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-3',
        'bottom-4 left-1/2 -translate-x-1/2 items-center',
        'md:bottom-auto md:top-4 md:left-1/2 md:-translate-x-1/2 md:items-center',
        'lg:left-auto lg:right-4 lg:top-4 lg:translate-x-0 lg:items-end',
    ].join(' ');
    document.body.appendChild(el);
    return el;
}

// ── ESC đóng toast được hiển thị gần nhất còn đang mở (ngăn xếp LIFO) ──
const escStack = [];
document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape' || escStack.length === 0) return;
    escStack[escStack.length - 1]();
});

function formatTimestamp() {
    return new Intl.DateTimeFormat('vi-VN', { hour: '2-digit', minute: '2-digit' }).format(new Date());
}

/** Sinh HTML phần bên trong khung kính — dùng chung cho lần dựng đầu và update(). */
function renderContent(descriptor) {
    const theme = STATUS_THEME[descriptor.status] || STATUS_THEME.info;
    const dismissible = descriptor.status !== 'loading';

    return `
        <span class="${ICON_WRAP_BASE_CLASS} ${theme.iconWrap}" data-role="icon">${getIcon(descriptor.status)}</span>
        <div class="min-w-0 flex-1 pt-0.5">
            <div class="flex items-baseline justify-between gap-2">
                <p class="${TITLE_CLASS}">${descriptor.title ?? ''}</p>
                ${descriptor.showTimestamp ? `<span class="${TIMESTAMP_CLASS}">${formatTimestamp()}</span>` : ''}
            </div>
            ${descriptor.message ? `<p class="${MESSAGE_CLASS}">${descriptor.message}</p>` : ''}
        </div>
        ${dismissible ? `
        <button type="button" data-role="close" aria-label="Đóng thông báo" class="${CLOSE_BUTTON_CLASS}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>` : ''}
        ${dismissible ? `
        <div class="${PROGRESS_TRACK_CLASS}">
            <div data-role="progress" class="${PROGRESS_BAR_BASE_CLASS} ${theme.progress}"></div>
        </div>` : ''}
    `;
}

/** Kéo bằng chuột (desktop) hoặc vuốt (mobile) để tắt — Pointer Events dùng chung 1 code path cho cả hai. */
function attachSwipe(el, { onDismiss, onPause, onResume }) {
    let startX = 0;
    let dx = 0;
    let dragging = false;
    let didDrag = false;

    el.addEventListener('pointerdown', (e) => {
        if (e.target.closest('[data-role="close"]')) return;
        dragging = true;
        didDrag = false;
        startX = e.clientX;
        el.setPointerCapture(e.pointerId);
        onPause();
    });

    el.addEventListener('pointermove', (e) => {
        if (!dragging) return;
        dx = e.clientX - startX;
        if (Math.abs(dx) > DRAG_DETECT_THRESHOLD) didDrag = true;
        el.style.transform = `translateX(${dx}px)`;
        el.style.opacity = String(Math.max(1 - Math.abs(dx) / 240, 0.3));
    });

    const endDrag = () => {
        if (!dragging) return;
        dragging = false;
        if (Math.abs(dx) > SWIPE_THRESHOLD) {
            onDismiss(dx > 0 ? 200 : -200);
        } else {
            el.style.transform = '';
            el.style.opacity = '';
            onResume();
        }
        dx = 0;
    };
    el.addEventListener('pointerup', endDrag);
    el.addEventListener('pointercancel', endDrag);

    // Hover để đọc: tạm dừng đếm ngược, giống mọi hệ thống toast trưởng thành.
    el.addEventListener('pointerenter', onPause);
    el.addEventListener('pointerleave', () => { if (!dragging) onResume(); });

    return () => didDrag;
}

/** Dựng 1 toast trên DOM + toàn bộ vòng đời (progress, drag, đóng, update). Được ToastManager gọi. */
function render(descriptor, onDone) {
    const container = layContainer();
    const el = document.createElement('div');
    el.className = SHELL_CLASS;
    el.style.pointerEvents = 'auto';
    el.tabIndex = 0;
    el.innerHTML = renderContent(descriptor);
    container.appendChild(el);

    let progressAnim = null;
    let dismissed = false;
    let getDidDrag = () => false;

    const bindCloseButton = () => {
        el.querySelector('[data-role="close"]')?.addEventListener('click', (e) => {
            e.stopPropagation();
            dismiss();
        });
    };

    const startProgress = () => {
        progressAnim?.cancel();
        if (descriptor.status === 'loading') return;
        const bar = el.querySelector('[data-role="progress"]');
        if (!bar) return;
        progressAnim = playProgress(bar, descriptor.duration ?? DEFAULT_DURATION);
        progressAnim.finished.then(() => { if (!dismissed) dismiss(); }).catch(() => {});
    };

    const pauseProgress = () => progressAnim?.pause();
    const resumeProgress = () => progressAnim?.play();

    function dismiss(dx = 0) {
        if (dismissed) return;
        dismissed = true;
        progressAnim?.cancel();
        const i = escStack.indexOf(dismiss);
        if (i !== -1) escStack.splice(i, 1);
        playExit(el, dx).finished.then(() => {
            el.remove();
            onDone();
        });
    }

    const applyAriaRole = () => {
        el.setAttribute('role', (STATUS_THEME[descriptor.status] || STATUS_THEME.info).role);
    };

    function update(patch) {
        Object.assign(descriptor, patch);
        el.innerHTML = renderContent(descriptor);
        applyAriaRole();
        bindCloseButton();
        startProgress();
    }

    applyAriaRole();
    el.setAttribute('aria-atomic', 'true');

    if (descriptor.onClick) {
        el.classList.add('cursor-pointer');
        el.addEventListener('click', (e) => {
            if (e.target.closest('[data-role="close"]') || getDidDrag()) return;
            descriptor.onClick();
            dismiss();
        });
    }

    bindCloseButton();
    getDidDrag = attachSwipe(el, { onDismiss: dismiss, onPause: pauseProgress, onResume: resumeProgress });
    escStack.push(dismiss);

    playEnter(el);
    startProgress();

    return { update, dismiss };
}

const manager = new ToastManager(render);

function normalize(descriptor) {
    return {
        status: 'info',
        duration: DEFAULT_DURATION,
        showTimestamp: false,
        ...descriptor,
    };
}

/** @returns {string} id — truyền lại cho update()/dismiss() để thao tác đúng toast này. */
export function show(descriptor) {
    return manager.show(normalize(descriptor));
}

export function update(id, patch) {
    manager.update(id, patch);
}

export function dismiss(id) {
    manager.dismiss(id);
}

export function dismissAll() {
    manager.dismissAll();
}
