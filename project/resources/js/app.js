import './bootstrap';

import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';
import flatpickr from 'flatpickr';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import { createIcons, icons } from 'lucide';
import 'flatpickr/dist/flatpickr.css';

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;

Alpine.start();

const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

function initIcons() {
    createIcons({ icons });
}

function initDashboardCharts() {
    const revenueEl = document.querySelector('[data-chart="revenue"]');
    if (revenueEl) {
        const labels = JSON.parse(revenueEl.dataset.labels || '[]');
        const values = JSON.parse(revenueEl.dataset.values || '[]');
        const height = Number(revenueEl.dataset.height || 235);
        new ApexCharts(revenueEl, {
            chart: { type: 'area', height, toolbar: { show: false }, fontFamily: 'Inter, ui-sans-serif, system-ui' },
            colors: ['#2563EB'],
            series: [{ name: 'Pendapatan', data: values }],
            xaxis: { categories: labels, labels: { style: { colors: '#64748B' } } },
            yaxis: {
                labels: {
                    style: { colors: '#64748B' },
                    formatter: (value) => value >= 1000000 ? `${Math.round(value / 1000000)} jt` : `${Math.round(value / 1000)} rb`,
                },
            },
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.28, opacityTo: 0.02 } },
            grid: { borderColor: '#E2E8F0', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: (value) => rupiah.format(value) } },
        }).render();
    }

    const statusEl = document.querySelector('[data-chart="booking-status"]');
    if (statusEl) {
        const height = Number(statusEl.dataset.height || 215);
        new ApexCharts(statusEl, {
            chart: { type: 'donut', height, fontFamily: 'Inter, ui-sans-serif, system-ui' },
            labels: JSON.parse(statusEl.dataset.labels || '[]'),
            series: JSON.parse(statusEl.dataset.values || '[]'),
            colors: ['#2563EB', '#22C55E', '#F59E0B', '#8B5CF6', '#EF4444', '#64748B'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: 'Total' } } } } },
        }).render();
    }

    const utilizationEl = document.querySelector('[data-chart="utilization"]');
    if (utilizationEl) {
        const height = Number(utilizationEl.dataset.height || 175);
        new ApexCharts(utilizationEl, {
            chart: { type: 'radialBar', height, sparkline: { enabled: true } },
            series: [Number(utilizationEl.dataset.value || 0)],
            colors: ['#2563EB'],
            plotOptions: {
                radialBar: {
                    hollow: { size: '62%' },
                    track: { background: '#E2E8F0' },
                    dataLabels: { value: { fontSize: '30px', fontWeight: 800, color: '#0F172A' }, name: { show: false } },
                },
            },
        }).render();
    }
}

function initCalendar() {
    const calendarEl = document.getElementById('operational-calendar');
    if (!calendarEl) return;

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        locale: 'id',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            day: 'Hari',
        },
        events: calendarEl.dataset.eventsUrl,
    });

    calendar.render();
}

function initDatePickers() {
    flatpickr('[data-datepicker]', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        time_24hr: true,
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            },
        },
    });
}

function initBookingCalculator() {
    const form = document.querySelector('[data-booking-form]');
    if (!form) return;

    const checkboxes = [...form.querySelectorAll('[data-asset-checkbox]')];
    const subtotalEl = form.querySelector('[data-summary-subtotal]');
    const totalEl = form.querySelector('[data-summary-total]');
    const countEl = form.querySelector('[data-summary-count]');

    const update = () => {
        const selected = checkboxes.filter((input) => input.checked);
        const subtotal = selected.reduce((sum, input) => sum + Number(input.dataset.rate || 0), 0);
        const insurance = subtotal * 0.04;
        const tax = (subtotal + insurance) * 0.11;
        const total = subtotal + insurance + tax;

        if (subtotalEl) subtotalEl.textContent = rupiah.format(subtotal);
        if (totalEl) totalEl.textContent = rupiah.format(total);
        if (countEl) countEl.textContent = `${selected.length} aset`;
    };

    checkboxes.forEach((input) => input.addEventListener('change', update));
    update();
}

document.addEventListener('DOMContentLoaded', () => {
    initIcons();
    initDashboardCharts();
    initCalendar();
    initDatePickers();
    initBookingCalculator();
});
