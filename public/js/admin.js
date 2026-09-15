/**
 * Admin portal Alpine state.
 * Pass tabs from Blade so the list lives in one place.
 */
function adminPortal(tabs) {
    return {
        tab: (tabs[0] && tabs[0].id) ? tabs[0].id : 'overview',
        tabs: tabs || [],
        period: 'monthly',
        periods: [
            { id: 'today', label: 'Today' },
            { id: 'weekly', label: 'Weekly' },
            { id: 'monthly', label: 'Monthly' },
            { id: 'yearly', label: 'Yearly' },
        ],
        setTab(id) {
            this.tab = id;

            if (id === 'overview') {
                this.$nextTick(() => {
                    window.requestAnimationFrame(() => {
                        refreshAdminSignupGrowthChart();
                    });
                });
            }
        },
        setPeriod(id) {
            this.period = id;
        },
        init() {
            this.$nextTick(() => {
                window.requestAnimationFrame(() => {
                    refreshAdminSignupGrowthChart();
                });
            });
        },
    };
}

function resetAdminChartCanvas(canvas) {
    if (!canvas) {
        return;
    }

    if (canvas._adminChart) {
        canvas._adminChart.destroy();
        canvas._adminChart = null;
    }

    canvas.removeAttribute('width');
    canvas.removeAttribute('height');
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    canvas.style.maxWidth = '100%';
    canvas.style.display = 'block';
}

function refreshAdminSignupGrowthChart() {
    const canvas = document.getElementById('admin-signup-growth-chart');
    if (!canvas) {
        return null;
    }

    const panel = canvas.closest('.admin-tab-panel');
    if (panel && getComputedStyle(panel).display === 'none') {
        window.setTimeout(refreshAdminSignupGrowthChart, 30);
        return null;
    }

    return mountAdminSignupGrowthChart(canvas);
}

/**
 * Sign-up growth trends chart — matches design:
 * soft blue area fill, single halo point near W20, W17–W21.
 */
function mountAdminSignupGrowthChart(canvas) {
    if (!canvas || typeof Chart === 'undefined') {
        return null;
    }

    resetAdminChartCanvas(canvas);

    const labels = ['W17', 'W18', 'W19', 'W20', 'W21'];
    const values = [55, 75, 118, 175, 178];
    const highlightIndex = 3; // W20 peak marker in design
    const stroke = '#9EBFD4';

    canvas._adminChart = new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    data: values,
                    borderColor: stroke,
                    backgroundColor: 'rgba(203, 220, 232, 0.10)',
                    fill: true,
                    tension: 0.28,
                    borderWidth: 2.5,
                    pointRadius: function (ctx) {
                        return ctx.dataIndex === highlightIndex ? 5 : 0;
                    },
                    pointHoverRadius: 6,
                    pointBackgroundColor: stroke,
                    pointBorderColor: 'rgba(158, 191, 212, 0.35)',
                    pointBorderWidth: function (ctx) {
                        return ctx.dataIndex === highlightIndex ? 8 : 0;
                    },
                    pointHitRadius: 12,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            layout: {
                padding: { top: 4, right: 8, bottom: 0, left: 0 },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#3b3731',
                    titleFont: { family: 'Lato', size: 12 },
                    bodyFont: { family: 'Lato', size: 12 },
                    padding: 8,
                    cornerRadius: 8,
                },
            },
            scales: {
                x: {
                    offset: true,
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        color: '#3B3731',
                        font: { family: 'Lato', size: 8, weight: '400', style: 'normal' },
                        padding: 8,
                    },
                    border: {
                        display: true,
                        color: 'rgba(59, 55, 49, 0.12)',
                        width: 1,
                    },
                },
                y: {
                    min: 50,
                    max: 200,
                    ticks: {
                        stepSize: 50,
                        color: '#3B3731',
                        font: { family: 'Lato', size: 8, weight: '400', style: 'normal' },
                        padding: 8,
                    },
                    grid: {
                        display: false,
                        drawBorder: false,
                        drawTicks: false,
                    },
                    border: { display: false },
                },
            },
        },
    });

    window.requestAnimationFrame(() => {
        if (canvas._adminChart) {
            canvas._adminChart.resize();
        }
    });

    return canvas._adminChart;
}
