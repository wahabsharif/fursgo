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
        Monday: [0, 0, 0, 0, 0, 0, 0, 0],
        Tuesday: [0, 0, 0, 0, 0, 0, 0, 0],
        Wednesday: [0, 0, 0, 0, 0, 0, 0, 0],
        Thursday: [0, 0, 0, 0, 0, 0, 0, 0],
        Friday: [0, 0, 0, 0, 0, 0, 0, 0],
        Saturday: [0, 0, 0, 0, 0, 0, 0, 0],
        Sunday: [0, 0, 0, 0, 0, 0, 0, 0],
    };

    const FALLBACK_TIME_LABELS = [
        "08-09",
        "09-10",
        "11-12",
        "13-14",
        "15-16",
        "17-18",
        "18-19",
        "20-21",
    ];

    const SERVICE_COLORS = ["#9AC1DD", "#FFC97A", "#FBAC83"];
    const PET_COLORS = ["#9AC1DD", "#C1DB8A", "#FBAC83"];
    const BAR_COLOR = "rgba(229, 238, 244, 0.5)";
    const BAR_COLOR_ACTIVE = "#9AC1DD";

    function chartData() {
        const el = document.querySelector("[data-mh-chart]");
        if (el) {
            const raw = el.getAttribute("data-mh-chart");
            if (raw) {
                try {
                    return JSON.parse(raw);
                } catch (e) {
                    /* fall through */
                }
            }
        }
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

    function slotStartsFromLabels(labels) {
        return labels.map((label) => {
            const match = String(label).match(/^(\d{1,2})/);
            return match ? parseInt(match[1], 10) : 0;
        });
    }

    function getCurrentTimeSlotIndex(labels) {
        const hour = new Date().getHours();
        const starts = slotStartsFromLabels(labels);
        let best = 0;
        for (let i = 0; i < starts.length; i++) {
            if (hour >= starts[i]) best = i;
        }
        return best;
    }

    function formatSlotForCopy(label) {
        return String(label).replace(/\s+/g, "");
    }

    function updatePeakMeta(day, values, labels) {
        const total = values.reduce(
            (sum, value) => sum + (Number(value) || 0),
            0,
        );
        const totalEl = document.getElementById("mhPeakTotal");
        if (totalEl) {
            totalEl.textContent =
                total + (total === 1 ? " Booking" : " Bookings");
        }

        const insightEl = document.getElementById("mhPeakInsightText");
        if (!insightEl) return;

        if (total <= 0) {
            insightEl.textContent =
                "Not enough booking data yet to spot peak times.";
            return;
        }

        let busyIndex = 0;
        let quietIndex = 0;
        values.forEach((value, index) => {
            if (value > values[busyIndex]) busyIndex = index;
            if (value < values[quietIndex]) quietIndex = index;
        });

        const busySlot = formatSlotForCopy(labels[busyIndex] || "");
        const quietSlot = formatSlotForCopy(labels[quietIndex] || "");
        insightEl.textContent =
            "Busiest on " +
            day +
            " at " +
            busySlot +
            " — consider opening more evening slots. Quietest at " +
            quietSlot +
            ", a good window for a timed promo.";
    }

    function createPeakBookingsChart(canvas) {
        if (!canvas || typeof Chart === "undefined") return;

        destroyChart("__mhPeakBookingsChart");

        const day = DAYS[peakDayIndex];
        const byDay = peakBookingsByDay();
        const values = byDay[day] || FALLBACK_PEAK.Monday;
        const labels = timeLabels();
        const maxValue = Math.max(...values, 0);
        const dayLabel = document.getElementById("mhPeakDayLabel");
        if (dayLabel) dayLabel.textContent = day;

        updatePeakMeta(day, values, labels);

        const todayIndex = (new Date().getDay() + 6) % 7;
        const activeBarIndex =
            peakDayIndex === todayIndex
                ? getCurrentTimeSlotIndex(labels)
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
                        data: values,
                        backgroundColor: barColors,
                        hoverBackgroundColor: barColors,
                        borderRadius: {
                            topLeft: 10,
                            topRight: 10,
                            bottomLeft: 5,
                            bottomRight: 5,
                        },
                        borderSkipped: false,
                        barPercentage: 0.72,
                        categoryPercentage: 0.78,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 8,
                        right: 0,
                        bottom: 0,
                        left: 0,
                    },
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
                                size: 14,
                                style: "normal",
                                weight: "600",
                                lineHeight: "normal",
                            },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        display: false,
                        grid: { display: false },
                        border: { display: false },
                    },
                },
            },
        });

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
                        hoverOffset: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "62%",
                layout: {
                    padding: 0,
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

        const data = chartData();
        window.__mhChartData = data;

        createPeakBookingsChart(peakCanvas);
        bindDaySwitcher();
        createDonutChart(
            servicesCanvas,
            Array.isArray(data.serviceColors) && data.serviceColors.length
                ? data.serviceColors
                : SERVICE_COLORS,
            "__mhServicesChart",
            data.services,
        );
        createDonutChart(
            petsCanvas,
            Array.isArray(data.petColors) && data.petColors.length
                ? data.petColors
                : PET_COLORS,
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

                document.addEventListener("livewire:navigated", () => {
                    bindDaySwitcher();
                    setTimeout(refreshWhenVisible, 50);
                });

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
