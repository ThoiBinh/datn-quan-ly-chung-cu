function formatVND(value) {
    return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
}

function formatAxisTick(value) {
    if (value >= 1000000) return (value / 1000000).toFixed(0) + 'tr';
    if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
    return value;
}

function initRevenueWidgetChart() {
    const canvas = document.getElementById('revenueWidgetChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');

    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, canvas.parentElement.clientHeight || 280);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.28)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Doanh thu',
                data: values,
                borderColor: '#2563eb',
                backgroundColor: gradient,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            animation: { duration: 800, easing: 'easeOutQuart' },
            hover: { animationDuration: 300 },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: (context) => formatVND(context.parsed.y),
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.15)' },
                    ticks: { callback: formatAxisTick },
                },
            },
        },
    });
}

document.addEventListener('DOMContentLoaded', initRevenueWidgetChart);

export { initRevenueWidgetChart };
