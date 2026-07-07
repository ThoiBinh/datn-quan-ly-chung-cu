function formatVND(value) {
    return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
}

function formatAxisTick(value) {
    if (value >= 1000000) return (value / 1000000).toFixed(0) + 'tr';
    if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
    return value;
}

function readJson(el, key, fallback) {
    try {
        return JSON.parse(el.dataset[key] || JSON.stringify(fallback));
    } catch (e) {
        return fallback;
    }
}

function initBookingThangChart() {
    const canvas = document.getElementById('datLichBookingThangChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = readJson(canvas, 'labels', []);
    const values = readJson(canvas, 'values', []);

    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Số lượt đặt',
                data: values,
                backgroundColor: 'rgba(99, 102, 241, 0.7)',
                borderRadius: 6,
                maxBarThickness: 28,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 600 },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: (ctx) => `${ctx.parsed.y} lượt` },
                },
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(148, 163, 184, 0.15)' } },
            },
        },
    });
}

function initDoanhThuThangChart() {
    const canvas = document.getElementById('datLichDoanhThuThangChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = readJson(canvas, 'labels', []);
    const values = readJson(canvas, 'values', []);

    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, canvas.parentElement.clientHeight || 260);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Doanh thu',
                data: values,
                borderColor: '#10b981',
                backgroundColor: gradient,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 600 },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: (ctx) => formatVND(ctx.parsed.y) },
                },
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { callback: formatAxisTick }, grid: { color: 'rgba(148, 163, 184, 0.15)' } },
            },
        },
    });
}

function initHorizontalTopChart(canvasId, color) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = readJson(canvas, 'labels', []);
    const values = readJson(canvas, 'values', []);

    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Số lượt đặt',
                data: values,
                backgroundColor: color,
                borderRadius: 6,
                maxBarThickness: 22,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 600 },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: (ctx) => `${ctx.parsed.x} lượt` },
                },
            },
            scales: {
                x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(148, 163, 184, 0.15)' } },
                y: { grid: { display: false } },
            },
        },
    });
}

function initTrangThaiChart() {
    const canvas = document.getElementById('datLichTrangThaiChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = readJson(canvas, 'labels', []);
    const values = readJson(canvas, 'values', []);
    const colors = readJson(canvas, 'colors', []);

    new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 600 },
            cutout: '65%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle', padding: 14 },
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: (ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return `${ctx.label}: ${ctx.parsed} (${pct}%)`;
                        },
                    },
                },
            },
        },
    });
}

function initDatLichDashboardCharts() {
    initBookingThangChart();
    initDoanhThuThangChart();
    initHorizontalTopChart('datLichTopTienIchChart', 'rgba(99, 102, 241, 0.7)');
    initHorizontalTopChart('datLichTopLoaiTienIchChart', 'rgba(139, 92, 246, 0.7)');
    initHorizontalTopChart('datLichTopToaNhaChart', 'rgba(59, 130, 246, 0.7)');
    initTrangThaiChart();
}

document.addEventListener('DOMContentLoaded', initDatLichDashboardCharts);

export { initDatLichDashboardCharts };
