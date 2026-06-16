(function () {
    const CHART_JS_SRC =
        "https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js";

    const DEFAULT_COLORS = {
        primary: "#FBAC83",
        light: "#FBAC83",
        donut: ["#FBAC83", "#FDD0B3", "#FFF4E4"],
    };

    function parseJsonDataset(canvas, key, fallback) {
        try {
            const raw = canvas.dataset[key];
            if (!raw) return fallback;
            return JSON.parse(raw);
        } catch (e) {
            return fallback;
        }
    }

    function destroyChart(instanceKey) {
        if (window[instanceKey]) {
            window[instanceKey].destroy();
            window[instanceKey] = null;
        }
    }

    function formatPoundLabel(n) {
        const value = Number(n) || 0;
        if (value === 0) return "£0";
        if (value >= 1000) {
            const inThousands = value / 1000;
            const rounded =
                inThousands % 1 === 0
                    ? inThousands.toFixed(0)
                    : inThousands.toFixed(1);
            return "£" + rounded + "K";
        }
        return "£" + Math.round(value);
    }

    function buildYAxisTicks(values) {
        const maxValue = Math.max(...values.map((v) => Number(v) || 0), 0);
        const yMax = Math.max(
            1000,
            maxValue <= 0 ? 1000 : Math.ceil((maxValue * 1.15) / 100) * 100,
        );
        const tickCount = 5;
        const step = yMax / (tickCount - 1);

        return {
            yMax,
            ticks: Array.from({ length: tickCount }, (_, i) =>
                i === tickCount - 1 ? yMax : Math.round(i * step),
            ),
        };
    }

    function parseBookingDate(dateStr) {
        const [year, month, day] = String(dateStr).split("-").map(Number);
        return new Date(year, month - 1, day);
    }

    function endOfDay(date) {
        return new Date(
            date.getFullYear(),
            date.getMonth(),
            date.getDate(),
            23,
            59,
            59,
            999,
        );
    }

    function daysInMonth(year, month) {
        return new Date(year, month, 0).getDate();
    }

    function sumBookingsInMonth(bookings, year, month) {
        return bookings.reduce((total, booking) => {
            const date = parseBookingDate(booking.date);
            if (date.getFullYear() === year && date.getMonth() + 1 === month) {
                return total + (Number(booking.amount) || 0);
            }
            return total;
        }, 0);
    }

    function sumBookingsBetween(bookings, from, to) {
        return bookings.reduce((total, booking) => {
            const date = parseBookingDate(booking.date);
            if (date >= from && date <= to) {
                return total + (Number(booking.amount) || 0);
            }
            return total;
        }, 0);
    }

    function sumBookingsOnDay(bookings, date) {
        const targetYear = date.getFullYear();
        const targetMonth = date.getMonth() + 1;
        const targetDay = date.getDate();

        return bookings.reduce((total, booking) => {
            const bookingDate = parseBookingDate(booking.date);
            if (
                bookingDate.getFullYear() === targetYear &&
                bookingDate.getMonth() + 1 === targetMonth &&
                bookingDate.getDate() === targetDay
            ) {
                return total + (Number(booking.amount) || 0);
            }
            return total;
        }, 0);
    }

    function startOfWeekMonday(date) {
        const start = startOfDay(new Date(date));
        const weekday = start.getDay();
        const diff = weekday === 0 ? -6 : 1 - weekday;
        start.setDate(start.getDate() + diff);
        return start;
    }

    function isSameDay(left, right) {
        return (
            left.getFullYear() === right.getFullYear() &&
            left.getMonth() === right.getMonth() &&
            left.getDate() === right.getDate()
        );
    }

    function referenceDayForMonth(year, month) {
        const now = new Date();
        if (now.getFullYear() === year && now.getMonth() + 1 === month) {
            return now.getDate();
        }

        return 1;
    }

    function startOfDay(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    function buildEarningsChartData(bookings, period, month, year) {
        const referenceDay = referenceDayForMonth(year, month);
        let periodTotal = 0;
        let labels = [];
        let values = [];
        let barMeta = [];
        let highlightIndex = -1;

        if (period === "month") {
            const start = new Date(year, month - 1 - 5, 1);

            for (let i = 0; i < 6; i++) {
                const current = new Date(
                    start.getFullYear(),
                    start.getMonth() + i,
                    1,
                );
                const currentMonth = current.getMonth() + 1;
                const currentYear = current.getFullYear();
                const amount = sumBookingsInMonth(
                    bookings,
                    currentYear,
                    currentMonth,
                );

                labels.push(
                    current.toLocaleDateString("en-GB", { month: "short" }),
                );
                values.push(amount);
                barMeta.push({ month: currentMonth, year: currentYear });

                if (currentYear === year && currentMonth === month) {
                    highlightIndex = i;
                }
            }
        } else if (period === "week") {
            const totalDays = daysInMonth(year, month);
            const weeks = [
                { label: "Wk 1", from: 1, to: 7 },
                { label: "Wk 2", from: 8, to: 14 },
                { label: "Wk 3", from: 15, to: 21 },
                { label: "Wk 4", from: 22, to: totalDays },
            ];

            weeks.forEach((week, index) => {
                const weekStart = startOfDay(
                    new Date(year, month - 1, week.from),
                );
                const weekEnd = endOfDay(
                    new Date(year, month - 1, Math.min(week.to, totalDays)),
                );
                const amount = sumBookingsBetween(bookings, weekStart, weekEnd);

                labels.push(week.label);
                values.push(amount);
                barMeta.push({
                    weekIndex: index,
                    from: week.from,
                    to: week.to,
                });
            });

            highlightIndex = Math.min(
                Math.max(Math.ceil(referenceDay / 7) - 1, 0),
                weeks.length - 1,
            );
        } else {
            const anchor = new Date(year, month - 1, referenceDay);
            const weekStart = startOfWeekMonday(anchor);
            const today = new Date();

            for (let index = 0; index < 7; index += 1) {
                const dayDate = new Date(weekStart);
                dayDate.setDate(weekStart.getDate() + index);

                const amount = sumBookingsOnDay(bookings, dayDate);
                const label = dayDate.toLocaleDateString("en-GB", {
                    weekday: "short",
                });

                labels.push(label);
                values.push(amount);
                barMeta.push({
                    day: dayDate.getDate(),
                    month: dayDate.getMonth() + 1,
                    year: dayDate.getFullYear(),
                });

                if (isSameDay(dayDate, anchor)) {
                    highlightIndex = index;
                } else if (
                    highlightIndex < 0 &&
                    isSameDay(dayDate, today) &&
                    today.getFullYear() === year &&
                    today.getMonth() + 1 === month
                ) {
                    highlightIndex = index;
                }
            }
        }

        if (highlightIndex < 0 && values.length > 0) {
            highlightIndex = 0;
        }

        periodTotal =
            highlightIndex >= 0 ? Number(values[highlightIndex]) || 0 : 0;

        const chartTitle =
            period === "day"
                ? "Daily Earnings"
                : period === "week"
                  ? "Weekly Earnings"
                  : "Monthly Earnings";

        return {
            title: chartTitle,
            periodTotal,
            labels,
            values,
            barMeta,
            highlightIndex,
        };
    }

    function registerEarningsBarChartAlpine() {
        if (window.__earningsBarChartAlpineRegistered) return;

        const register = () => {
            if (typeof Alpine === "undefined") return;

            Alpine.data("earningsChartPanel", (config = {}) => ({
                bookings: config.bookings || [],
                period: config.period || "month",
                month: Number(config.month || new Date().getMonth() + 1),
                year: Number(config.year || new Date().getFullYear()),
                primary: config.primary || DEFAULT_COLORS.primary,
                light: config.light || DEFAULT_COLORS.light,
                chartAnimationKey: 0,
                selectedBucketIndex: null,

                get chart() {
                    return buildEarningsChartData(
                        this.bookings,
                        this.period,
                        this.month,
                        this.year,
                    );
                },

                get labels() {
                    return this.chart.labels;
                },

                get values() {
                    return this.chart.values;
                },

                get highlightIndex() {
                    if (this.selectedBucketIndex !== null) {
                        return this.selectedBucketIndex;
                    }

                    return this.chart.highlightIndex;
                },

                get chartTitle() {
                    return this.chart.title;
                },

                get periodTotal() {
                    const index = this.highlightIndex;
                    return Number(this.values[index]) || 0;
                },

                get formattedTotal() {
                    return Number(this.periodTotal || 0).toLocaleString(
                        "en-GB",
                        {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        },
                    );
                },

                get periodLabel() {
                    return new Date(
                        this.year,
                        this.month - 1,
                        1,
                    ).toLocaleDateString("en-GB", {
                        month: "long",
                        year: "numeric",
                    });
                },

                get periodShort() {
                    const monthName = new Date(
                        this.year,
                        this.month - 1,
                        1,
                    ).toLocaleDateString("en-GB", { month: "long" });
                    const activeBar = this.bars[this.highlightIndex];

                    if (this.period === "week" && activeBar?.label) {
                        return monthName + " · " + activeBar.label;
                    }

                    if (this.period === "day" && activeBar?.label) {
                        return monthName + " · " + activeBar.label;
                    }

                    return monthName;
                },

                get axis() {
                    return buildYAxisTicks(this.values);
                },

                get yTicks() {
                    return this.axis.ticks;
                },

                get yMax() {
                    return this.axis.yMax;
                },

                get bars() {
                    return this.labels.map((label, index) => ({
                        label,
                        value: Number(this.values[index]) || 0,
                        month: this.chart.barMeta[index]?.month ?? null,
                        year: this.chart.barMeta[index]?.year ?? null,
                    }));
                },

                init() {
                    this.syncActiveBucketToCurrent();
                },

                syncActiveBucketToCurrent() {
                    if (this.period === "month") {
                        this.selectedBucketIndex = null;
                        return;
                    }

                    this.$nextTick(() => {
                        this.selectedBucketIndex = this.chart.highlightIndex;
                    });
                },

                setPeriod(period) {
                    if (!["day", "week", "month"].includes(period)) return;
                    if (this.period === period) return;
                    this.period = period;
                    this.selectedBucketIndex = null;
                    this.bumpChartAnimation();
                    this.syncActiveBucketToCurrent();
                },

                previousPeriod() {
                    const date = new Date(this.year, this.month - 1, 1);
                    date.setMonth(date.getMonth() - 1);
                    this.month = date.getMonth() + 1;
                    this.year = date.getFullYear();
                    this.selectedBucketIndex = null;
                    this.bumpChartAnimation();
                    this.syncActiveBucketToCurrent();
                },

                nextPeriod() {
                    const date = new Date(this.year, this.month - 1, 1);
                    date.setMonth(date.getMonth() + 1);
                    this.month = date.getMonth() + 1;
                    this.year = date.getFullYear();
                    this.selectedBucketIndex = null;
                    this.bumpChartAnimation();
                    this.syncActiveBucketToCurrent();
                },

                selectBar(index) {
                    if (index < 0 || index >= this.values.length) return;

                    if (this.period === "month") {
                        const meta = this.chart.barMeta[index];
                        if (!meta) return;

                        if (
                            meta.month === this.month &&
                            meta.year === this.year
                        ) {
                            return;
                        }

                        this.month = meta.month;
                        this.year = meta.year;
                        this.selectedBucketIndex = null;
                        this.bumpChartAnimation();
                        return;
                    }

                    if (this.selectedBucketIndex === index) return;
                    this.selectedBucketIndex = index;
                    this.bumpChartAnimation();
                },

                bumpChartAnimation() {
                    this.chartAnimationKey += 1;
                },

                barStaggerDelay(index) {
                    return index * 70 + "ms";
                },

                barHeight(value) {
                    if (this.yMax <= 0) return 0;
                    return Math.max(0, (Number(value) / this.yMax) * 100);
                },

                barColor(index) {
                    return index === this.highlightIndex
                        ? this.primary
                        : this.light;
                },

                isActive(index) {
                    return index === this.highlightIndex;
                },

                formatPound: formatPoundLabel,
            }));

            window.__earningsBarChartAlpineRegistered = true;
        };

        if (typeof Alpine !== "undefined") {
            register();
        } else {
            document.addEventListener("alpine:init", register);
        }
    }

    function initEarningsDonutChart(canvas) {
        canvas = canvas || document.getElementById("earningsDonutChart");
        if (!canvas || typeof Chart === "undefined") return false;

        const labels = parseJsonDataset(canvas, "labels", []);
        const values = parseJsonDataset(canvas, "values", []);
        const colors = parseJsonDataset(canvas, "colors", DEFAULT_COLORS.donut);

        if (!labels.length || !values.length) return false;

        destroyChart("__earningsDonutChartInstance");

        const chart = new Chart(canvas.getContext("2d"), {
            type: "doughnut",
            data: {
                labels,
                datasets: [
                    {
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "72%",
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                const total = context.dataset.data.reduce(
                                    (sum, v) => sum + Number(v),
                                    0,
                                );
                                const value = Number(context.parsed) || 0;
                                const pct =
                                    total > 0
                                        ? Math.round((value / total) * 100)
                                        : 0;
                                return (
                                    context.label +
                                    ": £" +
                                    value.toFixed(2) +
                                    " (" +
                                    pct +
                                    "%)"
                                );
                            },
                        },
                    },
                },
            },
        });

        window.__earningsDonutChartInstance = chart;
        return true;
    }

    function initEarningsCharts() {
        return initEarningsDonutChart();
    }

    function ensureChartJsReady(callback) {
        if (typeof Chart !== "undefined") {
            callback();
            return;
        }

        const existing = document.querySelector(
            "script[data-earnings-chart-js]",
        );
        if (existing) {
            existing.addEventListener("load", callback, { once: true });
            return;
        }

        const script = document.createElement("script");
        script.src = CHART_JS_SRC;
        script.dataset.earningsChartJs = "1";
        script.onload = callback;
        document.head.appendChild(script);
    }

    function scheduleEarningsChartsInit() {
        ensureChartJsReady(() => {
            let attempt = 0;
            const maxAttempts = 40;

            const tryInit = () => {
                const didInit = initEarningsCharts();
                if (didInit || attempt >= maxAttempts) return;
                attempt += 1;
                setTimeout(tryInit, 100);
            };

            requestAnimationFrame(() => setTimeout(tryInit, 0));
        });
    }

    function mountEarningsCharts(root) {
        ensureChartJsReady(() => {
            const scope = root || document;
            const donut = scope.querySelector("#earningsDonutChart");
            if (donut) initEarningsDonutChart(donut);
        });
    }

    registerEarningsBarChartAlpine();

    window.scheduleEarningsChartsInit = scheduleEarningsChartsInit;
    window.mountEarningsCharts = mountEarningsCharts;
    window.initEarningsCharts = initEarningsCharts;

    if (!window.__earningsChartsBindingsRegistered) {
        document.addEventListener(
            "DOMContentLoaded",
            scheduleEarningsChartsInit,
        );
        document.addEventListener(
            "livewire:navigated",
            scheduleEarningsChartsInit,
        );
        document.addEventListener(
            "earnings-mounted",
            scheduleEarningsChartsInit,
        );

        document.addEventListener("livewire:init", () => {
            Livewire.hook("morph.updated", ({ el }) => {
                if (
                    el?.querySelector?.("#earningsDonutChart") ||
                    el?.id === "earningsDonutChart"
                ) {
                    scheduleEarningsChartsInit();
                }
            });
        });

        window.__earningsChartsBindingsRegistered = true;
    }

    if (document.readyState === "loading") {
        document.addEventListener(
            "DOMContentLoaded",
            scheduleEarningsChartsInit,
            { once: true },
        );
    } else {
        scheduleEarningsChartsInit();
    }
})();
