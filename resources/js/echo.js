import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Reverb có thể chưa được cấu hình/chạy ở môi trường local (thiếu VITE_REVERB_*
// trong .env). Pusher throw đồng bộ nếu thiếu `key`, việc đó sẽ làm sập toàn bộ
// module app.js và kéo theo mọi thứ import sau echo.js (vd. window.DatLichTimeSlots)
// không bao giờ được gán. Bỏ qua khởi tạo Echo trong trường hợp đó thay vì throw.
if (import.meta.env.VITE_REVERB_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}
