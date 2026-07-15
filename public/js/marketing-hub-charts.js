(function () {
    const CHART_JS_SRC =
        "https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js";

    const DAYS = [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday",
    ];

    const FALLBACK_PEAK = {
        Monday: [0, 0, 0, 0, 0, 0, 0],
        Tuesday: [0, 0, 0, 0, 0, 0, 0],
        Wednesday: [0, 0, 0, 0, 0, 0, 0],
        Thursday: [0, 0, 0, 0, 0, 0, 0],
        Friday: [0, 0, 0, 0, 0, 0, 0],
        Saturday: [0, 0, 0, 0, 0, 0, 0],
        Sunday: [0, 0, 0, 0, 0, 0, 0],
    };

    const FALLBACK_TIME_LABELS = [
        "08 - 09",
        "10 - 11",
        "12 - 13",
        "14 - 15",
        "16 - 17",
        "18 - 19",
        "20 - 21",
    ];

    function chartData() {
        return window.__mhChartData || {};
    }

    function peakBookingsByDay() {
        const data = chartData().peakBookings;
        return data && typeof data === "object" ? data : FALLBACK_PEAK;
    }

    function timeLabels() {
        const labels = chartData().timeLabels;
        return Array.isArray(labels) && labels.length
            ? labels
            : FALLBACK_TIME_LABELS;
    }

    let peakDayIndex = (new Date().getDay() + 6) % 7; // Mon=0 … Sun=6

    const BAR_COLOR = "rgba(203, 220, 232, 0.5)";
    const BAR_COLOR_ACTIVE = "#CBDCE8";

    const POINT_RADIUS = 5;
    // Gap from dot center so segments stay clear of each point
    const LINE_GAP_FROM_CENTER = POINT_RADIUS + 14;

    const gappedLinePlugin = {
        id: "mhGappedLine",
        afterDatasetsDraw(chart) {
            if (chart.canvas?.id !== "mhPeakBookingsChart") return;

            const meta = chart.getDatasetMeta(1);
            if (!meta?.data?.length) return;

            const ctx = chart.ctx;
            const gap = LINE_GAP_FROM_CENTER;

            ctx.save();
            ctx.strokeStyle = "#D8E8B7";
            ctx.lineWidth = 2;
            ctx.lineCap = "round";

            for (let i = 0; i < meta.data.length - 1; i++) {
                const p0 = meta.data[i];
                const p1 = meta.data[i + 1];
                if (!p0 || !p1 || p0.skip || p1.skip) continue;

                const dx = p1.x - p0.x;
                const dy = p1.y - p0.y;
                const dist = Math.hypot(dx, dy);
                if (dist <= gap * 2) continue;

                const ux = dx / dist;
                const uy = dy / dist;

                ctx.beginPath();
                ctx.moveTo(p0.x + ux * gap, p0.y + uy * gap);
                ctx.lineTo(p1.x - ux * gap, p1.y - uy * gap);
                ctx.stroke();
            }

            ctx.restore();
        },
    };

    function registerGappedLinePlugin() {
        if (
            typeof Chart === "undefined" ||
            window.__mhGappedLinePluginRegistered
        ) {
            return;
        }
        Chart.register(gappedLinePlugin);
        window.__mhGappedLinePluginRegistered = true;
    }

    function registerPeakTooltipPositioner() {
        if (
            typeof Chart === "undefined" ||
            window.__mhPeakTooltipPositionerRegistered
        ) {
            return;
        }
        Chart.Tooltip.positioners.mhPeakDot = function (items) {
            // Anchor to the line point (dot), not the bar
            const dot =
                items.find((item) => item.datasetIndex === 1) || items[0];
            if (!dot?.element) return false;
            return {
                x: dot.element.x - 6,
                y: dot.element.y - 2,
            };
        };
        window.__mhPeakTooltipPositionerRegistered = true;
    }

    function loadChartJs() {
        if (typeof Chart !== "undefined") {
            return Promise.resolve();
        }

        return new Promise((resolve, reject) => {
            const existing = document.querySelector(
                'script[src*="chart.umd.min.js"]',
            );
            if (existing) {
                existing.addEventListener("load", () => resolve());
                if (typeof Chart !== "undefined") resolve();
                return;
            }

            const script = document.createElement("script");
            script.src = CHART_JS_SRC;
            script.onload = () => resolve();
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function destroyChart(key) {
        if (window[key]) {
            window[key].destroy();
            window[key] = null;
        }
    }

    function getCurrentTimeSlotIndex() {
        const hour = new Date().getHours();
        // Labels: 08-09, 10-11, 12-13, 14-15, 16-17, 18-19, 20-21
        const starts = [8, 10, 12, 14, 16, 18, 20];
        let best = 0;
        for (let i = 0; i < starts.length; i++) {
            if (hour >= starts[i]) best = i;
        }
        return best;
    }

    function createPeakBookingsChart(canvas) {
        if (!canvas || typeof Chart === "undefined") return;

        destroyChart("__mhPeakBookingsChart");
        registerGappedLinePlugin();
        registerPeakTooltipPositioner();

        const day = DAYS[peakDayIndex];
        const byDay = peakBookingsByDay();
        const values = byDay[day] || FALLBACK_PEAK.Monday;
        const labels = timeLabels();
        const maxValue = Math.max(...values, 0);
        // Small lift above bar tops (~10% of scale), not a fixed booking count
        const pointOffset =
            maxValue > 0 ? Math.max(0.25, maxValue * 0.1) : 0.35;
        const lineValues = values.map((v) => v + pointOffset);
        const dayLabel = document.getElementById("mhPeakDayLabel");
        if (dayLabel) dayLabel.textContent = day;

        const todayIndex = (new Date().getDay() + 6) % 7;
        const activeBarIndex =
            peakDayIndex === todayIndex
                ? getCurrentTimeSlotIndex()
                : values.indexOf(maxValue);

        const barColors = values.map((_, i) =>
            i === activeBarIndex ? BAR_COLOR_ACTIVE : BAR_COLOR,
        );

        window.__mhPeakBookingsChart = new Chart(canvas.getContext("2d"), {
            type: "bar",
            data: {
                labels: labels,
                datasets: [
                    {
                        type: "bar",
                        data: values,
                        backgroundColor: barColors,
                        hoverBackgroundColor: barColors,
                        borderRadius: 10,
                        borderSkipped: false,
                        barPercentage: 0.55,
                        categoryPercentage: 0.7,
                        order: 2,
                    },
                    {
                        type: "line",
                        data: lineValues,
                        borderColor: "#D8E8B7",
                        borderWidth: 0,
                        showLine: false,
                        pointBackgroundColor: "#509DD4",
                        pointBorderColor: "#509DD4",
                        pointBorderWidth: 0,
                        pointRadius: POINT_RADIUS,
                        pointHoverRadius: POINT_RADIUS + 2,
                        pointHitRadius: 14,
                        tension: 0,
                        fill: false,
                        order: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 12,
                        right: 0,
                        bottom: 0,
                        left: 0,
                    },
                },
                interaction: {
                    mode: "index",
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "#F6F6F6",
                        titleColor: "#3B3731",
                        bodyColor: "#3B3731",
                        borderWidth: 0,
                        padding: { x: 12, y: 12 },
                        displayColors: false,
                        cornerRadius: 8,
                        caretSize: 6,
                        caretPadding: 8,
                        position: "mhPeakDot",
                        xAlign: "right",
                        yAlign: "center",
                        filter: (item) => item.datasetIndex === 1,
                        bodyFont: {
                            family: "Lato",
                            size: 14,
                            style: "normal",
                            weight: "600",
                            lineHeight: "normal",
                        },
                        callbacks: {
                            title: () => "",
                            label: (ctx) => {
                                const count = values[ctx.dataIndex] ?? 0;
                                return (
                                    count +
                                    (count === 1 ? " Booking" : " Bookings")
                                );
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            color: (ctx) =>
                                ctx.index === activeBarIndex
                                    ? "#3B3731"
                                    : "#9D9B98",
                            font: {
                                family: "Lato",
                                size: 18,
                                style: "normal",
                                weight: "600",
                                lineHeight: "normal",
                            },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: maxValue + pointOffset * 2,
                        display: false,
                        grid: {
                            display: false,
                        },
                        border: { display: false },
                    },
                },
            },
        });

        // Re-measure after layout (x-show / grid can leave canvas narrow on first paint)
        requestAnimationFrame(() => {
            window.__mhPeakBookingsChart?.resize();
        });
    }

    function createDonutChart(canvas, colors, key, overrideValues) {
        if (!canvas || typeof Chart === "undefined") return;

        destroyChart(key);

        let values;
        if (Array.isArray(overrideValues) && overrideValues.length) {
            values = overrideValues;
        } else {
            try {
                values = JSON.parse(canvas.dataset.values || "[]");
            } catch (e) {
                values = [];
            }
        }

        if (!values.length) return;

        window[key] = new Chart(canvas.getContext("2d"), {
            type: "doughnut",
            data: {
                datasets: [
                    {
                        data: values,
                        backgroundColor: colors,
                        hoverBackgroundColor: colors,
                        borderWidth: 0,
                        hoverBorderWidth: 0,
                        hoverOffset: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "68%",
                layout: {
                    padding: 8,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "#fff",
                        titleColor: "#3B3731",
                        bodyColor: "#3B3731",
                        borderColor: "#E5E7EB",
                        borderWidth: 1,
                        padding: 10,
                        displayColors: true,
                        cornerRadius: 8,
                        callbacks: {
                            label: (ctx) => " " + ctx.parsed + "%",
                        },
                    },
                },
            },
        });
    }

    function initMarketingHubCharts() {
        const peakCanvas = document.getElementById("mhPeakBookingsChart");
        const servicesCanvas = document.getElementById("mhServicesChart");
        const petsCanvas = document.getElementById("mhPetsChart");

        if (!peakCanvas && !servicesCanvas && !petsCanvas) return;

        createPeakBookingsChart(peakCanvas);
        const data = chartData();
        createDonutChart(
            servicesCanvas,
            ["#FBAC83", "#FDD0B3", "#FFF4E4"],
            "__mhServicesChart",
            data.services,
        );
        createDonutChart(
            petsCanvas,
            [
                "#D8E8B7",
                "rgba(216, 232, 183, 0.60)",
                "rgba(216, 232, 183, 0.20)",
            ],
            "__mhPetsChart",
            data.pets,
        );
    }

    function bindDaySwitcher() {
        const prev = document.getElementById("mhPeakDayPrev");
        const next = document.getElementById("mhPeakDayNext");
        if (!prev || !next || prev.dataset.bound === "1") return;

        prev.dataset.bound = "1";
        next.dataset.bound = "1";

        prev.addEventListener("click", () => {
            peakDayIndex = (peakDayIndex + DAYS.length - 1) % DAYS.length;
            createPeakBookingsChart(
                document.getElementById("mhPeakBookingsChart"),
            );
        });

        next.addEventListener("click", () => {
            peakDayIndex = (peakDayIndex + 1) % DAYS.length;
            createPeakBookingsChart(
                document.getElementById("mhPeakBookingsChart"),
            );
        });
    }

    function refreshWhenVisible() {
        const panel = document.querySelector(
            '.marketing-hub-panels [x-show*="marketing-hub"]',
        );
        if (!panel) {
            initMarketingHubCharts();
            return;
        }

        // Alpine may hide the panel initially; retry once visible
        const tryInit = () => {
            if (panel.offsetParent === null && panel.style.display === "none") {
                return false;
            }
            initMarketingHubCharts();
            return true;
        };

        if (!tryInit()) {
            const observer = new MutationObserver(() => {
                if (tryInit()) observer.disconnect();
            });
            observer.observe(panel, {
                attributes: true,
                attributeFilter: ["style", "class"],
            });
            setTimeout(() => {
                tryInit();
                observer.disconnect();
            }, 800);
        }
    }

    function boot() {
        loadChartJs()
            .then(() => {
                bindDaySwitcher();
                refreshWhenVisible();

                // Re-init after Livewire navigations
                document.addEventListener("livewire:navigated", () => {
                    bindDaySwitcher();
                    setTimeout(refreshWhenVisible, 50);
                });

                // Re-draw when returning to marketing-hub section
                window.addEventListener("dashboard-nav-changed", (event) => {
                    const section = event.detail?.section;
                    if (section === "marketing-hub") {
                        setTimeout(() => {
                            initMarketingHubCharts();
                        }, 80);
                    }
                });
            })
            .catch(() => {});
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", boot);
    } else {
        boot();
    }

    window.__initMarketingHubCharts = initMarketingHubCharts;
})();
