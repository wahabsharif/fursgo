(function () {
    const AXIS_COLOR = "#D9D5CF";
    const TICK_COLOR = "#9B958C";
    const Y_TICKS = [0, 100, 150, 200, 250];
    const Y_MIN = 0;
    const Y_MAX = 250;
    const CORNER_RAD = 4;
    const FILL_GAP = 12;
    const CHART_JS_SRC =
        "https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js";

    const roundedAreaClipPlugin = {
        id: "weeklyRevenueRoundedAreaClip",
        beforeDatasetsDraw(chart) {
            if (chart.canvas?.id !== "weeklyRevenueChart") return;

            const { ctx, chartArea } = chart;
            if (!chartArea) return;

            const left = chartArea.left + FILL_GAP;
            const top = chartArea.top;
            const right = chartArea.right;
            const bottom = chartArea.bottom - FILL_GAP;
            const r = Math.min(
                CORNER_RAD,
                (right - left) / 2,
                (bottom - top) / 2,
            );

            ctx.save();
            ctx.beginPath();
            ctx.moveTo(left + r, top);
            ctx.arcTo(right, top, right, bottom, r);
            ctx.arcTo(right, bottom, left, bottom, r);
            ctx.arcTo(left, bottom, left, top, r);
            ctx.arcTo(left, top, right, top, r);
            ctx.closePath();
            ctx.clip();
        },
        afterDatasetsDraw(chart) {
            if (chart.canvas?.id !== "weeklyRevenueChart") return;
            chart.ctx.restore();
        },
    };

    function registerChartPlugin() {
        if (
            typeof Chart === "undefined" ||
            window.__weeklyRevenueRoundedPluginRegistered
        ) {
            return;
        }

        Chart.register(roundedAreaClipPlugin);
        window.__weeklyRevenueRoundedPluginRegistered = true;
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

        registerChartPlugin();

        const fillColor = (canvas.dataset.fill || "").trim() || "#FFC97A";
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
                        backgroundColor: fillColor,
                        borderColor: "transparent",
                        borderWidth: 0,
                        pointRadius: 0,
                        pointHoverRadius: 0,
                        pointHitRadius: 8,
                        tension: 0.6,
                        cubicInterpolationMode: "default",
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1100,
                    easing: "easeOutCubic",
                },
                animations: {
                    tension: {
                        duration: 1200,
                        easing: "easeOutQuart",
                        from: 0.18,
                        to: 0.6,
                    },
                },
                layout: {
                    padding: {
                        left: 12,
                        right: 8,
                        top: 10,
                        bottom: 18,
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
                            display: true,
                            color: AXIS_COLOR,
                        },
                        ticks: {
                            color: TICK_COLOR,
                            autoSkip: false,
                            maxRotation: 0,
                            padding: 6,
                            align: "inner",
                            font: {
                                family: "Lato, sans-serif",
                                size: 12,
                                weight: 500,
                            },
                        },
                    },
                    y: {
                        min: Y_MIN,
                        max: Y_MAX,
                        grid: {
                            display: false,
                        },
                        border: {
                            display: true,
                            color: AXIS_COLOR,
                        },
                        afterBuildTicks: (scale) => {
                            scale.ticks = Y_TICKS.map((value) => ({ value }));
                        },
                        ticks: {
                            color: TICK_COLOR,
                            autoSkip: false,
                            padding: 4,
                            font: {
                                family: "Lato, sans-serif",
                                size: 11,
                                weight: 500,
                            },
                            callback(v) {
                                const n = Number(v);
                                if (!Y_TICKS.includes(n)) return "";
                                return n === 0 ? "0" : "£" + n;
                            },
                        },
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

    function mountWeeklyRevenueChart(canvas) {
        ensureChartJsReady(() => {
            initWeeklyRevenueChart(canvas);
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
