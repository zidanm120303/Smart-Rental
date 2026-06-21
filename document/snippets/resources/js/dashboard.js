import Chart from 'chart.js/auto';

const revenueCanvas = document.getElementById('revenueChart');
if (revenueCanvas) {
    new Chart(revenueCanvas, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{ label: 'Pendapatan', data: [12,15,18,14,22,30,26,33,31,40,37,45], tension: 0.4, fill: true }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
}

const bookingStatusCanvas = document.getElementById('bookingStatusChart');
if (bookingStatusCanvas) {
    new Chart(bookingStatusCanvas, {
        type: 'doughnut',
        data: { labels: ['Disetujui', 'Menunggu', 'Aktif', 'Selesai', 'Dibatalkan'], datasets: [{ data: [46,18,12,7,3] }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
}
