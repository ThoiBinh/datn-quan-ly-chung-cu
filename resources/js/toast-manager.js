/**
 * Quản lý HÀNG ĐỢI toast — thuần logic, KHÔNG đụng DOM (SRP: toast.js lo
 * DOM/animation, file này chỉ quyết định "toast nào đang hiển thị, toast nào
 * phải chờ, khi nào gọi lại render()"). Nhờ vậy có thể unit-test độc lập.
 *
 * Quy tắc: tối đa MAX_VISIBLE toast hiển thị cùng lúc; dư ra vào hàng đợi
 * FIFO; toast đầu biến mất mới "kéo" toast kế tiếp trong hàng đợi vào.
 * show() gọi lại với cùng id (đang hiển thị HOẶC đang chờ) sẽ UPDATE tại chỗ
 * thay vì tạo bản mới — dùng cho luồng "Đang xử lý... -> Thành công".
 */

const MAX_VISIBLE = 3;

export class ToastManager {
    #active = new Map();
    #queue = [];

    /** @param {(descriptor: object, onDone: () => void) => { update: Function, dismiss: Function }} renderFn */
    constructor(renderFn) {
        this.renderFn = renderFn;
    }

    show(descriptor) {
        const id = descriptor.id ?? ToastManager.generateId();
        const full = { ...descriptor, id };

        const active = this.#active.get(id);
        if (active) {
            active.update(full);
            return id;
        }

        const queued = this.#queue.find((item) => item.id === id);
        if (queued) {
            Object.assign(queued, full);
            return id;
        }

        if (this.#active.size >= MAX_VISIBLE) {
            this.#queue.push(full);
            return id;
        }

        this.#mount(full);
        return id;
    }

    update(id, patch) {
        const active = this.#active.get(id);
        if (active) {
            active.update(patch);
            return;
        }
        const queued = this.#queue.find((item) => item.id === id);
        if (queued) Object.assign(queued, patch);
    }

    dismiss(id) {
        this.#active.get(id)?.dismiss();
    }

    dismissAll() {
        [...this.#active.values()].forEach((controller) => controller.dismiss());
        this.#queue = [];
    }

    #mount(descriptor) {
        const controller = this.renderFn(descriptor, () => this.#onReleased(descriptor.id));
        this.#active.set(descriptor.id, controller);
    }

    #onReleased(id) {
        this.#active.delete(id);
        const next = this.#queue.shift();
        if (next) this.#mount(next);
    }

    static generateId() {
        return `toast-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
    }
}
