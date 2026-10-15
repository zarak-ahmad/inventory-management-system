import './bootstrap';
import 'bootstrap';
import Swal from 'sweetalert2';

window.Swal = Swal;

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const loadingOverlay = document.getElementById('appLoadingOverlay');

    const showLoading = () => loadingOverlay?.classList.add('show');
    const hideLoading = () => loadingOverlay?.classList.remove('show');

    const closeSidebar = () => {
        sidebar?.classList.remove('show');
        backdrop?.classList.remove('show');
    };

    toggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('show');
        backdrop?.classList.toggle('show');
    });

    backdrop?.addEventListener('click', closeSidebar);

    const markButtonLoading = (button) => {
        if (!button || button.classList.contains('btn-loading')) {
            return;
        }
        button.classList.add('btn-loading');
        button.disabled = true;
        const spinner = document.createElement('span');
        spinner.className = 'btn-spinner';
        button.prepend(spinner);
    };

    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            event.preventDefault();

            const form = element.closest('form');
            const message = element.getAttribute('data-confirm') || 'Are you sure?';

            Swal.fire({
                title: 'Confirm',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed && form) {
                    markButtonLoading(element);
                    showLoading();
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.noLoading === '1') {
                return;
            }

            // Confirmed destructive actions already show loading.
            if (event.submitter?.hasAttribute('data-confirm') || form.querySelector('[data-confirm]:focus')) {
                return;
            }

            const submitter = event.submitter;
            if (submitter) {
                markButtonLoading(submitter);
            }
            showLoading();
        });
    });

    document.querySelectorAll('[data-toast]').forEach((el) => {
        const type = el.getAttribute('data-toast');
        const message = el.getAttribute('data-toast-message') || '';
        if (!message) {
            return;
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type === 'error' ? 'error' : 'success',
            title: message,
            showConfirmButton: false,
            timer: type === 'error' ? 4500 : 2800,
            timerProgressBar: true,
        });
    });

    window.addEventListener('pageshow', hideLoading);

    initDashboardCharts();
});

window.initDashboardCharts = initDashboardCharts;

function initDashboardCharts() {
    const data = window.dashboardCharts;
    if (!data) {
        return;
    }

    if (data.inventory && (data.inventory.labels || []).length) {
        drawBarChart(
            document.getElementById('inventoryChart'),
            data.inventory.labels || [],
            data.inventory.values || [],
            '#198754'
        );
    }
}

function drawLineChart(canvas, labels, values, color) {
    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const width = canvas.clientWidth || 600;
    const height = canvas.clientHeight || 240;
    canvas.width = width * dpr;
    canvas.height = height * dpr;
    ctx.scale(dpr, dpr);

    const padding = { top: 20, right: 16, bottom: 36, left: 48 };
    const chartW = width - padding.left - padding.right;
    const chartH = height - padding.top - padding.bottom;
    const max = Math.max(...values, 1);

    ctx.clearRect(0, 0, width, height);
    ctx.strokeStyle = '#dee2e6';
    ctx.lineWidth = 1;

    for (let i = 0; i <= 4; i++) {
        const y = padding.top + (chartH / 4) * i;
        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(padding.left + chartW, y);
        ctx.stroke();
        ctx.fillStyle = '#6c757d';
        ctx.font = '11px system-ui';
        ctx.textAlign = 'right';
        ctx.fillText(formatCompact(max - (max / 4) * i), padding.left - 8, y + 3);
    }

    if (values.length === 0) {
        return;
    }

    ctx.beginPath();
    ctx.strokeStyle = color;
    ctx.lineWidth = 2;
    values.forEach((value, index) => {
        const x = padding.left + (values.length === 1 ? chartW / 2 : (chartW / (values.length - 1)) * index);
        const y = padding.top + chartH - (value / max) * chartH;
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    ctx.stroke();

    values.forEach((value, index) => {
        const x = padding.left + (values.length === 1 ? chartW / 2 : (chartW / (values.length - 1)) * index);
        const y = padding.top + chartH - (value / max) * chartH;
        ctx.beginPath();
        ctx.fillStyle = color;
        ctx.arc(x, y, 3, 0, Math.PI * 2);
        ctx.fill();
    });

    ctx.fillStyle = '#6c757d';
    ctx.font = '10px system-ui';
    ctx.textAlign = 'center';
    labels.forEach((label, index) => {
        if (labels.length > 10 && index % 2 !== 0) {
            return;
        }
        const x = padding.left + (labels.length === 1 ? chartW / 2 : (chartW / (labels.length - 1)) * index);
        ctx.fillText(label, x, height - 12);
    });
}

function drawBarChart(canvas, labels, values, color) {
    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const width = canvas.clientWidth || 400;
    const height = canvas.clientHeight || 260;
    canvas.width = width * dpr;
    canvas.height = height * dpr;
    ctx.scale(dpr, dpr);

    const padding = { top: 16, right: 12, bottom: 56, left: 48 };
    const chartW = width - padding.left - padding.right;
    const chartH = height - padding.top - padding.bottom;
    const max = Math.max(...values, 1);
    const barGap = 8;
    const barWidth = Math.max(8, (chartW / Math.max(values.length, 1)) - barGap);

    ctx.clearRect(0, 0, width, height);

    values.forEach((value, index) => {
        const x = padding.left + index * (barWidth + barGap) + barGap / 2;
        const barH = (value / max) * chartH;
        const y = padding.top + chartH - barH;
        ctx.fillStyle = color;
        ctx.fillRect(x, y, barWidth, barH);

        ctx.fillStyle = '#6c757d';
        ctx.font = '10px system-ui';
        ctx.textAlign = 'center';
        const label = String(labels[index] || '').slice(0, 10);
        ctx.save();
        ctx.translate(x + barWidth / 2, height - 8);
        ctx.rotate(-Math.PI / 4);
        ctx.fillText(label, 0, 0);
        ctx.restore();
    });

    ctx.fillStyle = '#6c757d';
    ctx.font = '11px system-ui';
    ctx.textAlign = 'right';
    for (let i = 0; i <= 4; i++) {
        const y = padding.top + (chartH / 4) * i;
        ctx.fillText(formatCompact(max - (max / 4) * i), padding.left - 8, y + 3);
    }
}

function formatCompact(value) {
    if (value >= 1000000) {
        return (value / 1000000).toFixed(1) + 'M';
    }
    if (value >= 1000) {
        return (value / 1000).toFixed(1) + 'k';
    }

    return Number(value).toFixed(0);
}
