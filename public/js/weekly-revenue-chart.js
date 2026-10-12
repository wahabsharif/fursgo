(function () {
    const GROOMER_FILL = "#FFFAF2";
    const SPACE_FILL = "#FFF5F3";
    const CHART_JS_SRC =
        "https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js";

    function resolveAreaFill(lineColor) {
        const color = (lineColor || "").trim().toLowerCase();
        if (color === "#ffa899" || color.includes("255, 168, 153")) {
            return SPACE_FILL;
        }

        return GROOMER_FILL;
    }

    function initWeeklyRevenueChart(canvas) {
        canvas = canvas || document.getElementById("weeklyRevenueChart");
        if (!canvas || typeof Chart === "undefined") {
            return false;
        }

        let labels;
        let values;
        try {
            labels = JSON.parse(canvas.dataset.labels || "[]");
            values = JSON.parse(canvas.dataset.values || "[]");
        } catch (e) {
            return false;
        }

        if (!labels.length || !values.length) {
            return false;
        }

        const lineColor = (canvas.dataset.fill || "").trim() || "#FFC97A";
        const areaFill = resolveAreaFill(lineColor);
        const ctx = canvas.getContext("2d");

        if (window.__weeklyRevenueChartInstance) {
            window.__weeklyRevenueChartInstance.destroy();
            window.__weeklyRevenueChartInstance = null;
        }

        const chart = new Chart(ctx, {
            type: "line",
            data: {
                labels,
                datasets: [
                    {
                        data: values,
                        fill: "origin",
                        backgroundColor: areaFill,
                        borderColor: lineColor,
                        borderWidth: 2,
                        pointRadius: 4.5,
                        pointHoverRadius: 5.5,
                        pointBackgroundColor: lineColor,
                        pointBorderColor: "#fff",
                        pointBorderWidth: 3,
                        pointHitRadius: 10,
                        tension: 0.35,
                        cubicInterpolationMode: "default",
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 900,
                    easing: "easeOutCubic",
                },
                layout: {
                    padding: {
                        left: 2,
                        right: 8,
                        top: 10,
                        bottom: 0,
                    },
                },
                interaction: {
                    intersect: false,
                    mode: "index",
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                const y = context.parsed.y;
                                return y == null
                                    ? ""
                                    : "£" + Number(y).toFixed(2);
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        offset: false,
                        grid: {
                            display: false,
                        },
                        border: {
                            display: false,
                        },
                        ticks: {
                            color: "#9D9B98",
                            autoSkip: false,
                            maxRotation: 0,
                            padding: 10,
                            font: {
                                family: "Lato",
                                size: 14,
                                style: "normal",
                                weight: 600,
                                lineHeight: "normal",
                            },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        display: false,
                        grid: {
                            display: false,
                        },
                        border: {
                            display: false,
                        },
                        grace: "8%",
                    },
                },
            },
        });

        window.__weeklyRevenueChartInstance = chart;

        const wrap = canvas.closest(".weekly-chart-js-wrap");
        if (wrap && typeof ResizeObserver !== "undefined") {
            if (wrap.__wrcObs) wrap.__wrcObs.disconnect();
            const ro = new ResizeObserver(() => chart.resize());
            ro.observe(wrap);
            wrap.__wrcObs = ro;
        }

        if (wrap && typeof IntersectionObserver !== "undefined") {
            if (wrap.__wrcIo) wrap.__wrcIo.disconnect();
            const io = new IntersectionObserver((entries) => {
                if (entries.some((entry) => entry.isIntersecting)) {
                    requestAnimationFrame(() => chart.resize());
                }
            });
            io.observe(wrap);
            wrap.__wrcIo = io;
        }

        requestAnimationFrame(() => chart.resize());

        return true;
    }

    function whenLatoReady(callback) {
        if (!document.fonts || !document.fonts.load) {
            callback();
            return;
        }

        Promise.all([
            document.fonts.load("600 14px Lato"),
            document.fonts.load("normal 14px Lato"),
        ])
            .catch(() => {})
            .finally(callback);
    }

    function mountWeeklyRevenueChart(canvas) {
        ensureChartJsReady(() => {
            whenLatoReady(() => {
                initWeeklyRevenueChart(canvas);
            });
        });
    }

    function ensureChartJsReady(callback) {
        if (typeof Chart !== "undefined") {
            callback();
            return;
        }

        const existing = document.querySelector(
            "script[data-weekly-revenue-chart-js]",
        );
        if (existing) {
            existing.addEventListener("load", callback, { once: true });
            return;
        }

        const script = document.createElement("script");
        script.src = CHART_JS_SRC;
        script.dataset.weeklyRevenueChartJs = "1";
        script.onload = callback;
        document.head.appendChild(script);
    }

    function scheduleWeeklyRevenueChartInit() {
        ensureChartJsReady(() => {
            whenLatoReady(() => {
                let attempt = 0;
                const maxAttempts = 40;

                const tryInit = () => {
                    const didInit = initWeeklyRevenueChart();
                    if (didInit || attempt >= maxAttempts) {
                        return;
                    }

                    attempt += 1;
                    setTimeout(tryInit, 100);
                };

                requestAnimationFrame(() => {
                    setTimeout(tryInit, 0);
                });
            });
        });
    }

    window.scheduleWeeklyRevenueChartInit = scheduleWeeklyRevenueChartInit;
    window.mountWeeklyRevenueChart = mountWeeklyRevenueChart;

    if (!window.__weeklyRevenueChartBindingsRegistered) {
        document.addEventListener(
            "DOMContentLoaded",
            scheduleWeeklyRevenueChartInit,
        );
        document.addEventListener(
            "livewire:navigated",
            scheduleWeeklyRevenueChartInit,
        );
        document.addEventListener(
            "business-hub-mounted",
            scheduleWeeklyRevenueChartInit,
        );

        if (
            !window.__weeklyRevenueChartObserverRegistered &&
            typeof MutationObserver !== "undefined"
        ) {
            const observer = new MutationObserver(() => {
                if (
                    document.getElementById("weeklyRevenueChart") &&
                    !window.__weeklyRevenueChartInstance
                ) {
                    scheduleWeeklyRevenueChartInit();
                }
            });

            observer.observe(document.body, { childList: true, subtree: true });
            window.__weeklyRevenueChartObserverRegistered = true;
        }

        window.__weeklyRevenueChartBindingsRegistered = true;
    }

    if (document.readyState === "loading") {
        document.addEventListener(
            "DOMContentLoaded",
            scheduleWeeklyRevenueChartInit,
            { once: true },
        );
    } else {
        scheduleWeeklyRevenueChartInit();
    }
})();
