{{-- Scripts from my_bookings.php (body + after body) --}}
<script>
            const tabs = document.querySelectorAll('.booking-filters .tab');

            const upcomingBookings = document.querySelector('.upcoming-bookings');
            const pastBookings = document.querySelectorAll('.past-bookings');
            const cancelledBookings = document.querySelectorAll('.cancelled-bookings');

            function hideAll() {
                if (upcomingBookings) upcomingBookings.style.display = 'none';
                pastBookings.forEach(el => el.style.display = 'none');
                cancelledBookings.forEach(el => el.style.display = 'none');
            }

            function show(type) {
                hideAll();

                if (type === 'all') {
                    if (upcomingBookings) upcomingBookings.style.display = 'block';
                    pastBookings.forEach(el => el.style.display = 'block');
                    cancelledBookings.forEach(el => el.style.display = 'block');
                    return;
                }

                if (type === 'upcoming' && upcomingBookings) {
                    upcomingBookings.style.display = 'block';
                }

                if (type === 'past') {
                    pastBookings.forEach(el => el.style.display = 'block');
                }

                if (type === 'cancelled') {
                    cancelledBookings.forEach(el => el.style.display = 'block');
                }
            }

            // default: All — show every section with its own label
            show('all');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    show(this.dataset.filter || 'all');
                });
            });
        </script>
<script>
            const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            let vy = new Date().getFullYear();
            const d = new Date();
            let month = d.getMonth() + 1;

            vm = month; // view year/month
            let start = null; // Date – lower bound
            let end = null; // Date – upper bound
            // phase: 'none' | 'picking' | 'done'
            let phase = 'none';
            let hover = null; // Date – only used in 'picking' phase

            const key = d => d ? `${d.getFullYear()}-${d.getMonth()}-${d.getDate()}` : '';
            const same = (a, b) => key(a) === key(b);
            const fmt = d => d.toLocaleDateString('en-GB', {
                day: 'numeric',
                month: 'long'
            });
            const fmtShort = d => d.getDate() + ' ' + MONTHS[d.getMonth()];
            const CHIP_CLOSE_SVG = `<span><svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M0.5 7.57L7.572 0.5M0.5 0.5L7.572 7.57" stroke="#FBAC83" stroke-linecap="round" /></svg></span>`;

            function getChipsContainer() {
                return document.querySelector('.chips-container');
            }

            function createFilterChip(filter, value, label) {
                const chip = document.createElement('div');
                chip.className = 'chip light-color-font d-flex align-items-center gap-10 close cursor';
                chip.dataset.filter = filter;
                chip.dataset.value = value;
                chip.innerHTML = `${label} ${CHIP_CLOSE_SVG}`;
                return chip;
            }

            function upsertFilterChip(filter, value, label) {
                const container = getChipsContainer();
                if (!container) return;

                let chip = container.querySelector(`.chip[data-filter="${filter}"]`);
                if (!chip) {
                    chip = createFilterChip(filter, value, label);
                    container.appendChild(chip);
                    return;
                }

                chip.dataset.value = value;
                chip.innerHTML = `${label} ${CHIP_CLOSE_SVG}`;
            }

            function removeFilterChip(filter) {
                const container = getChipsContainer();
                container?.querySelector(`.chip[data-filter="${filter}"]`)?.remove();
            }

            function updateDateChip() {
                if (phase === 'done' && start && end) {
                    upsertFilterChip('date', `${key(start)}_${key(end)}`, `${fmtShort(start)} - ${fmtShort(end)}`);
                } else {
                    removeFilterChip('date');
                }
            }

            function clearDateRange() {
                start = null;
                end = null;
                phase = 'none';
                hover = null;
                render();
                setHint();
                updateDateChip();
            }

            function setHint() {
                const el = document.getElementById('hint');
                if (phase === 'none') el.textContent = 'Click a start date';
                else if (phase === 'picking') el.textContent = 'Now click an end date';
                else el.textContent = fmt(start) + '  -  ' + fmt(end);
            }

            function header() {
                let pm = vm - 1,
                    py = vy;
                if (pm < 0) {
                    pm = 11;
                    py--;
                }
                document.getElementById('pLabel').textContent = MONTHS[pm] + ' ' + py;
                document.getElementById('cLabel').textContent = MONTHS[vm] + ' ' + vy;
            }

            function render() {
                const grid = document.getElementById('grid');
                while (grid.children.length > 7) grid.removeChild(grid.lastChild);

                const off = (new Date(vy, vm, 1).getDay() + 6) % 7;
                const days = new Date(vy, vm + 1, 0).getDate();

                // effective lo/hi
                let lo = start,
                    hi = end;
                if (phase === 'picking' && hover) {
                    lo = start <= hover ? start : hover;
                    hi = start <= hover ? hover : start;
                }

                const loK = key(lo),
                    hiK = key(hi);

                const empty = () => {
                    const c = document.createElement('div');
                    c.className = 'cell empty';
                    const n = document.createElement('div');
                    n.className = 'num';
                    c.appendChild(n);
                    return c;
                };

                for (let i = 0; i < off; i++) grid.appendChild(empty());

                for (let d = 1; d <= days; d++) {
                    const date = new Date(vy, vm, d);
                    const dk = key(date);
                    const c = document.createElement('div');
                    c.className = 'cell';
                    c.dataset.d = d;

                    const isLo = dk === loK;
                    const isHi = dk === hiK && !same(lo, hi);
                    const solo = same(lo, hi) && isLo;
                    const mid = lo && hi && date > lo && date < hi;

                    if (solo) {
                        c.classList.add('sel-s', 'sel-e');
                    } else if (isLo) {
                        c.classList.add('rng-s', 'sel-s');
                    } else if (isHi) {
                        c.classList.add('rng-e', 'sel-e');
                    } else if (mid) {
                        c.classList.add('in-range');
                    }

                    const n = document.createElement('div');
                    n.className = 'num';
                    n.textContent = d;
                    c.appendChild(n);
                    grid.appendChild(c);
                }

                const rem = (off + days) % 7 === 0 ? 0 : 7 - (off + days) % 7;
                for (let i = 0; i < rem; i++) grid.appendChild(empty());
            }

            // ── Click: delegate on grid ─────────────────────────────────────────────
            document.getElementById('grid').addEventListener('click', e => {
                const cell = e.target.closest('.cell:not(.empty)');
                if (!cell) return;
                const date = new Date(vy, vm, +cell.dataset.d);

                if (phase === 'none') {
                    // First click — set start, begin picking
                    start = date;
                    end = null;
                    hover = null;
                    phase = 'picking';

                } else if (phase === 'picking') {
                    // Second click — lock range, stop hover completely
                    if (same(date, start)) {
                        // Same day clicked — cancel
                        start = null;
                        phase = 'none';
                    } else {
                        if (date < start) {
                            end = start;
                            start = date;
                        } else {
                            end = date;
                        }
                        phase = 'done';
                        hover = null; // kill hover permanently
                    }

                } else {
                    // Range locked — expand by moving nearest boundary
                    if (date < start) start = date;
                    else if (date > end) end = date;
                    else {
                        const ds = Math.abs(date - start),
                            de = Math.abs(date - end);
                        if (ds <= de) start = date;
                        else end = date;
                    }
                    // stay in 'done', hover stays null
                }

                render();
                setHint();
                updateDateChip();
            });

            // ── Hover preview ONLY while picking ────────────────────────────────────
            document.getElementById('grid').addEventListener('mousemove', e => {
                if (phase !== 'picking') return; // hard gate
                const cell = e.target.closest('.cell:not(.empty)');
                const d = cell ? new Date(vy, vm, +cell.dataset.d) : null;
                if (key(d) !== key(hover)) {
                    hover = d;
                    render();
                }
            });

            document.getElementById('grid').addEventListener('mouseleave', () => {
                if (phase !== 'picking') return;
                hover = null;
                render();
            });

            // ── Nav ─────────────────────────────────────────────────────────────────
            document.getElementById('prev').addEventListener('click', () => {
                vm--;
                if (vm < 0) {
                    vm = 11;
                    vy--;
                }
                hover = null;
                header();
                render();
            });
            document.getElementById('next').addEventListener('click', () => {
                vm++;
                if (vm > 11) {
                    vm = 0;
                    vy++;
                }
                hover = null;
                header();
                render();
            });

            header();
            render();
            setHint();
        </script>
<script>
            document.querySelectorAll('#review-modal .upload-box').forEach(box => {
                const input = box.querySelector('.file-input');
                const btn = box.querySelector('.upload-btn');
                const placeholder = box.querySelector('.upload-icon');
                const previewContainer = box.querySelector('.preview-container');
                const deleteBtn = box.querySelector('.delete-btn');

                function openPicker() {
                    if (box.classList.contains('has-preview')) return;
                    input.click();
                }

                box.addEventListener('click', function(e) {
                    if (e.target.closest('.delete-btn')) return;
                    openPicker();
                });

                input.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (!file) return;

                    const url = URL.createObjectURL(file);

                    placeholder.style.display = 'none';
                    btn.style.display = 'none';

                    previewContainer.innerHTML = `<img src="${url}" alt="" />`;
                    previewContainer.style.display = 'block';
                    box.classList.add('has-preview');

                    deleteBtn.style.display = 'flex';
                });

                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    input.value = '';

                    previewContainer.innerHTML = '';
                    previewContainer.style.display = 'none';
                    box.classList.remove('has-preview');

                    placeholder.style.display = 'block';
                    btn.style.display = 'flex';

                    deleteBtn.style.display = 'none';
                });
            });
        </script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const calendarBtn = document.getElementById("toggleCalendar");
        const calendar = document.getElementById("calendarCard");
        const sortBtn = document.querySelector(".sort-by");
        const sortDropdown = document.querySelector(".sort-dropdown");
        const chipsContainer = document.querySelector(".chips-container");
        const sortRadios = document.querySelectorAll('input[name="sort"]');

        function getSortLabel(radio) {
            return radio.closest('label')?.querySelector('.option-text')?.textContent.trim() || radio.value;
        }

        function syncSortChip() {
            const selected = Array.from(sortRadios).find(radio => radio.checked);
            if (!selected) {
                removeFilterChip('sort');
                return;
            }
            upsertFilterChip('sort', selected.value, getSortLabel(selected));
        }

        calendarBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            sortDropdown.classList.remove("show");
            calendar.classList.toggle("show");
        });

        sortBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            if (sortDropdown.contains(e.target)) return;
            calendar.classList.remove("show");
            sortDropdown.classList.toggle("show");
        });

        calendar.addEventListener("click", e => e.stopPropagation());
        sortDropdown.addEventListener("click", e => e.stopPropagation());

        document.addEventListener("click", function() {
            calendar.classList.remove("show");
            sortDropdown.classList.remove("show");
        });

        sortRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                syncSortChip();
                sortDropdown.classList.remove('show');
            });
        });

        chipsContainer?.addEventListener('click', function(e) {
            const chip = e.target.closest('.chip');
            if (!chip) return;

            const filter = chip.dataset.filter;

            if (filter === 'sort') {
                sortRadios.forEach(radio => {
                    radio.checked = false;
                });
                chip.remove();
                return;
            }

            if (filter === 'date') {
                clearDateRange();
                return;
            }

            chip.remove();
        });

        syncSortChip();
    });
</script>
<script>
    // Change booking modal
    (function() {
        const ORIGINAL = {
            dateKey: '2025-12-18',
            time: '14:30 - 15:30',
            extras: [1, 2, 12],
            totalPaid: 48,
            originalExtrasTotal: 44
        };

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const monthShort = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        const availableDates = ['2025-12-14', '2025-12-15', '2025-12-18', '2025-12-20', '2025-12-26', '2025-12-29', '2025-12-30'];

        let viewDate = new Date(2025, 11);
        let selectedDateKey = ORIGINAL.dateKey;
        let selectedTime = ORIGINAL.time;
        let currentExtrasTotal = ORIGINAL.originalExtrasTotal;
        let currentExtrasIds = ORIGINAL.extras.slice();

        function sameExtras(a, b) {
            if (a.length !== b.length) return false;
            const aa = a.slice().sort(function(x, y) {
                return x - y;
            });
            const bb = b.slice().sort(function(x, y) {
                return x - y;
            });
            return aa.every(function(id, i) {
                return id === bb[i];
            });
        }

        function hasChanges() {
            return selectedDateKey !== ORIGINAL.dateKey ||
                selectedTime !== ORIGINAL.time ||
                !sameExtras(currentExtrasIds, ORIGINAL.extras);
        }

        function updateConfirm() {
            const btn = document.getElementById('cbm-confirm');
            if (!btn) return;
            if (hasChanges()) {
                btn.classList.remove('is-disabled');
                btn.setAttribute('aria-disabled', 'false');
            } else {
                btn.classList.add('is-disabled');
                btn.setAttribute('aria-disabled', 'true');
            }
        }

        function updatePrices() {
            const extrasTotal = currentExtrasTotal || 0;
            const serviceOnly = ORIGINAL.totalPaid - ORIGINAL.originalExtrasTotal;
            const updated = serviceOnly + extrasTotal;
            const delta = updated - ORIGINAL.totalPaid;

            document.getElementById('cbm-addons-delta').textContent = '£' + extrasTotal.toFixed(2);
            document.getElementById('cbm-updated-total').textContent = '£' + updated.toFixed(2);

            const chargeEl = document.getElementById('cbm-alert-charge');
            const chargeText = document.getElementById('cbm-alert-charge-text');
            const refundEl = document.getElementById('cbm-alert-refund');
            const refundText = document.getElementById('cbm-alert-refund-text');

            chargeEl.style.display = 'none';
            refundEl.style.display = 'none';

            if (delta > 0) {
                chargeEl.style.display = 'flex';
                chargeText.textContent = "You'll be charged an additional £" + delta.toFixed(2) + ' when you confirm.';
            } else if (delta < 0) {
                refundEl.style.display = 'flex';
                refundText.textContent = "You'll receive a £" + Math.abs(delta).toFixed(2) + ' refund. Refunds processed in 3-5 days.';
            }

            updateConfirm();
        }

        function updateTimesLabel() {
            const parts = selectedDateKey.split('-');
            const day = parseInt(parts[2], 10);
            const month = parseInt(parts[1], 10) - 1;
            document.getElementById('cbm-times-label').textContent = 'AVAILABLE TIMES · ' + day + ' ' + monthShort[month];
        }

        function renderCalendar() {
            const datesContainer = document.getElementById('cbm-cal-dates');
            const headerTitle = document.getElementById('cbm-cal-title');
            if (!datesContainer || !headerTitle) return;

            datesContainer.innerHTML = '';
            const year = viewDate.getFullYear();
            const month = viewDate.getMonth();
            headerTitle.textContent = monthNames[month].toUpperCase() + ' ' + year;

            const firstDay = new Date(year, month, 1).getDay() || 7;
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            for (let i = 1; i < firstDay; i++) {
                datesContainer.appendChild(document.createElement('div'));
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateDiv = document.createElement('div');
                dateDiv.className = 'cbm-date';
                dateDiv.textContent = day;

                const dateKey = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');

                if (availableDates.includes(dateKey)) {
                    dateDiv.classList.add('available');
                    if (dateKey === selectedDateKey) dateDiv.classList.add('selected');
                    dateDiv.addEventListener('click', function() {
                        selectedDateKey = dateKey;
                        updateTimesLabel();
                        renderCalendar();
                        updateConfirm();
                    });
                }

                datesContainer.appendChild(dateDiv);
            }
        }

        window.handleChangeBookingExtras = function(ids, total) {
            currentExtrasIds = ids.slice();
            currentExtrasTotal = total;
            updatePrices();
        };

        document.getElementById('cbm-cal-prev')?.addEventListener('click', function() {
            viewDate.setMonth(viewDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('cbm-cal-next')?.addEventListener('click', function() {
            viewDate.setMonth(viewDate.getMonth() + 1);
            renderCalendar();
        });

        document.querySelectorAll('#cbm-time-list .cbm-time').forEach(function(slot) {
            slot.addEventListener('click', function() {
                document.querySelectorAll('#cbm-time-list .cbm-time').forEach(function(t) {
                    t.classList.remove('selected');
                });
                slot.classList.add('selected');
                selectedTime = slot.dataset.range || slot.textContent.trim();
                updateConfirm();
            });
        });

        document.getElementById('cbm-confirm')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.classList.contains('is-disabled') || this.getAttribute('aria-disabled') === 'true') {
                return;
            }

            // Fill confirmation modal with selected values
            const dateEl = document.getElementById('bum-groomer-date');
            const timeEl = document.getElementById('bum-groomer-time');
            const priceEl = document.getElementById('bum-groomer-price');
            if (dateEl) dateEl.textContent = formatFriendlyDate(selectedDateKey);
            if (timeEl) timeEl.textContent = selectedTime.replace(' - ', ' – ');
            if (priceEl) priceEl.textContent = document.getElementById('cbm-updated-total')?.textContent || '£48.00';

            // Close change modal, open updated modal
            const changeModal = document.getElementById('change_groomer_booking_modal');
            const updatedModal = document.getElementById('groomer_booking_updated_modal');
            if (changeModal) changeModal.style.display = 'none';
            if (updatedModal) updatedModal.style.display = 'flex';
        });

        function formatFriendlyDate(key) {
            const parts = key.split('-');
            const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

        // Close parent modal when opening change booking from booking details
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('[data-close-parent-modal]');
            if (!trigger) return;
            const parentModal = trigger.closest('.modal');
            if (parentModal) parentModal.style.display = 'none';
        });

        renderCalendar();
        updateTimesLabel();
        updatePrices();
    })();
</script>
<script>
    // Change space booking modal
    (function() {
        // Current booking state — Confirm stays disabled until date, time, or extras change.
        const ORIGINAL = {
            dateKey: '2025-12-18',
            time: '14:30 - 18:30',
            extras: [2], // Deep Clean (matches default_selected)
            totalPaid: 48,
            originalExtrasTotal: 10
        };

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const monthShort = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        const availableDates = ['2025-12-14', '2025-12-15', '2025-12-18', '2025-12-20', '2025-12-26', '2025-12-29', '2025-12-30'];

        let viewDate = new Date(2025, 11);
        let selectedDateKey = ORIGINAL.dateKey;
        let selectedTime = ORIGINAL.time;
        let currentExtrasTotal = ORIGINAL.originalExtrasTotal;
        let currentExtrasIds = ORIGINAL.extras.slice();

        function sameExtras(a, b) {
            if (a.length !== b.length) return false;
            const aa = a.slice().sort(function(x, y) {
                return x - y;
            });
            const bb = b.slice().sort(function(x, y) {
                return x - y;
            });
            return aa.every(function(id, i) {
                return id === bb[i];
            });
        }

        function hasChanges() {
            return selectedDateKey !== ORIGINAL.dateKey ||
                selectedTime !== ORIGINAL.time ||
                !sameExtras(currentExtrasIds, ORIGINAL.extras);
        }

        function updateConfirm() {
            const btn = document.getElementById('cbs-confirm');
            if (!btn) return;
            if (hasChanges()) {
                btn.classList.remove('is-disabled');
                btn.setAttribute('aria-disabled', 'false');
            } else {
                btn.classList.add('is-disabled');
                btn.setAttribute('aria-disabled', 'true');
            }
        }

        function updatePrices() {
            const extrasTotal = currentExtrasTotal || 0;
            const serviceOnly = ORIGINAL.totalPaid - ORIGINAL.originalExtrasTotal;
            const updated = serviceOnly + extrasTotal;
            const delta = updated - ORIGINAL.totalPaid;

            document.getElementById('cbs-addons-delta').textContent = '£' + extrasTotal.toFixed(2);
            document.getElementById('cbs-updated-total').textContent = '£' + updated.toFixed(2);

            const chargeEl = document.getElementById('cbs-alert-charge');
            const chargeText = document.getElementById('cbs-alert-charge-text');
            const refundEl = document.getElementById('cbs-alert-refund');
            const refundText = document.getElementById('cbs-alert-refund-text');

            chargeEl.style.display = 'none';
            refundEl.style.display = 'none';

            if (delta > 0) {
                chargeEl.style.display = 'flex';
                chargeText.textContent = "You'll be charged an additional £" + delta.toFixed(2) + ' when you confirm.';
            } else if (delta < 0) {
                refundEl.style.display = 'flex';
                refundText.textContent = "You'll receive a £" + Math.abs(delta).toFixed(2) + ' refund. Refunds processed in 3-5 days.';
            }

            updateConfirm();
        }

        function updateTimesLabel() {
            const parts = selectedDateKey.split('-');
            const day = parseInt(parts[2], 10);
            const month = parseInt(parts[1], 10) - 1;
            document.getElementById('cbs-times-label').textContent = 'AVAILABLE TIMES · ' + day + ' ' + monthShort[month];
        }

        function renderCalendar() {
            const datesContainer = document.getElementById('cbs-cal-dates');
            const headerTitle = document.getElementById('cbs-cal-title');
            if (!datesContainer || !headerTitle) return;

            datesContainer.innerHTML = '';
            const year = viewDate.getFullYear();
            const month = viewDate.getMonth();
            headerTitle.textContent = monthNames[month].toUpperCase() + ' ' + year;

            const firstDay = new Date(year, month, 1).getDay() || 7;
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            for (let i = 1; i < firstDay; i++) {
                datesContainer.appendChild(document.createElement('div'));
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateDiv = document.createElement('div');
                dateDiv.className = 'cbm-date';
                dateDiv.textContent = day;

                const dateKey = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');

                if (availableDates.includes(dateKey)) {
                    dateDiv.classList.add('available');
                    if (dateKey === selectedDateKey) dateDiv.classList.add('selected');
                    dateDiv.addEventListener('click', function() {
                        selectedDateKey = dateKey;
                        updateTimesLabel();
                        renderCalendar();
                        updateConfirm();
                    });
                }

                datesContainer.appendChild(dateDiv);
            }
        }

        window.handleChangeSpaceExtras = function(ids, total) {
            currentExtrasIds = ids.slice();
            currentExtrasTotal = total;
            updatePrices();
        };

        document.getElementById('cbs-cal-prev')?.addEventListener('click', function() {
            viewDate.setMonth(viewDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('cbs-cal-next')?.addEventListener('click', function() {
            viewDate.setMonth(viewDate.getMonth() + 1);
            renderCalendar();
        });

        document.querySelectorAll('#cbs-time-list .cbm-time').forEach(function(slot) {
            slot.addEventListener('click', function() {
                document.querySelectorAll('#cbs-time-list .cbm-time').forEach(function(t) {
                    t.classList.remove('selected');
                });
                slot.classList.add('selected');
                selectedTime = slot.dataset.range || slot.textContent.trim();
                updateConfirm();
            });
        });

        document.getElementById('cbs-confirm')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.classList.contains('is-disabled') || this.getAttribute('aria-disabled') === 'true') {
                return;
            }

            // Fill confirmation modal with selected values
            const dateEl = document.getElementById('bum-space-date');
            const timeEl = document.getElementById('bum-space-time');
            const priceEl = document.getElementById('bum-space-price');
            if (dateEl) dateEl.textContent = formatFriendlyDate(selectedDateKey);
            if (timeEl) timeEl.textContent = selectedTime.replace(' - ', ' – ');
            if (priceEl) priceEl.textContent = document.getElementById('cbs-updated-total')?.textContent || '£48.00';

            // Close change modal, open updated modal
            const changeModal = document.getElementById('change_space_booking_modal');
            const updatedModal = document.getElementById('space_booking_updated_modal');
            if (changeModal) changeModal.style.display = 'none';
            if (updatedModal) updatedModal.style.display = 'flex';
        });

        function formatFriendlyDate(key) {
            const parts = key.split('-');
            const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

        renderCalendar();
        updateTimesLabel();
        updatePrices();
    })();
</script>
<script>
    // Cancel booking confirmation flow
    (function() {
        document.getElementById('cnl-confirm-groomer')?.addEventListener('click', function() {
            const changeModal = document.getElementById('cancel_groomer_booking_modal');
            const cancelledModal = document.getElementById('groomer_booking_cancelled_modal');
            if (changeModal) changeModal.style.display = 'none';
            if (cancelledModal) cancelledModal.style.display = 'flex';
        });

        document.getElementById('cnl-confirm-space')?.addEventListener('click', function() {
            const changeModal = document.getElementById('cancel_space_booking_modal');
            const cancelledModal = document.getElementById('space_booking_cancelled_modal');
            if (changeModal) changeModal.style.display = 'none';
            if (cancelledModal) cancelledModal.style.display = 'flex';
        });
    })();
</script>
<script>
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const rating = parseInt(this.querySelector('input[name="overall_rating"]:checked')?.value || '4', 10);
        const ratingValue = document.getElementById('rsm-rating-value');
        const starsContainer = document.getElementById('rsm-stars');

        if (ratingValue) {
            ratingValue.textContent = ' ' + rating + '/5 STARS';
        }

        if (starsContainer) {
            starsContainer.querySelectorAll('.rsm-star').forEach(function(star) {
                const starNum = parseInt(star.dataset.star, 10);
                const path = star.querySelector('path');
                if (path) {
                    path.setAttribute('fill', starNum <= rating ? '#FFC97A' : '#EFEFEF');
                }
            });
        }

        // open success modal
        document.querySelector('[data-modal-open="review-submitted-modal"]').click();

        // hide review modal
        const reviewModal = document.getElementById('review-modal');
        if (reviewModal) {
            reviewModal.style.display = 'none';
        }

        // reset everything (ONE CALL)
        resetReviewUI(this);
    });

    function resetReviewUI(form) {

        // reset form fields
        form.querySelectorAll('input, textarea, select').forEach(el => {
            if (el.type === 'checkbox' || el.type === 'radio') {
                el.checked = false;
            } else {
                el.value = '';
            }
        });

        // remove chips inside the review form only
        form.querySelectorAll('.chip').forEach(c => c.remove());

        // reset upload boxes
        document.querySelectorAll('.upload-box').forEach(box => {
            const input = box.querySelector('.file-input');
            const btn = box.querySelector('.upload-btn');
            const placeholder = box.querySelector('.upload-icon');
            const previewContainer = box.querySelector('.preview-container');
            const deleteBtn = box.querySelector('.delete-btn');

            if (input) input.value = '';

            if (previewContainer) {
                previewContainer.innerHTML = '';
                previewContainer.style.display = 'none';
            }

            box.classList.remove('has-preview');
            if (placeholder) placeholder.style.display = 'block';
            if (btn) btn.style.display = 'flex';
            if (deleteBtn) deleteBtn.style.display = 'none';
        });
    }
</script>
<script>
    // Rebook modals
    (function() {
        function initSlots(containerId) {
            document.querySelectorAll('#' + containerId + ' .rbm-slot').forEach(function(slot) {
                slot.addEventListener('click', function() {
                    document.querySelectorAll('#' + containerId + ' .rbm-slot').forEach(function(s) {
                        s.classList.remove('selected');
                    });
                    slot.classList.add('selected');
                });
            });
        }

        initSlots('rbm-slots');
        initSlots('rbm-space-slots');

        const groomerService = 48;
        window.handleRebookExtras = function(ids, total) {
            const countEl = document.getElementById('rbm-addons-count');
            const selectedEl = document.getElementById('rbm-extras-selected-count');
            const addonsEl = document.getElementById('rbm-addons-total');
            const grandEl = document.getElementById('rbm-grand-total');
            if (countEl) countEl.textContent = ids.length;
            if (selectedEl) selectedEl.textContent = '(' + ids.length + ' Selected)';
            if (addonsEl) addonsEl.textContent = '+ £' + total.toFixed(2);
            if (grandEl) grandEl.textContent = '£' + (groomerService + total).toFixed(2);
        };

        const spaceService = 48;
        window.handleRebookSpaceExtras = function(ids, total) {
            const selectedEl = document.getElementById('rbm-space-extras-selected-count');
            const grandEl = document.getElementById('rbm-space-grand-total');
            if (selectedEl) selectedEl.textContent = '(' + ids.length + ' Selected)';
            if (grandEl) grandEl.textContent = '£' + (spaceService + total).toFixed(2);
        };

        document.querySelectorAll('[data-extras-toggle]').forEach(function(section) {
            const btn = section.querySelector('.rbm-extras-toggle-btn');
            const panel = section.querySelector('.rbm-extras-toggle-panel');
            if (!btn || !panel) return;

            btn.addEventListener('click', function() {
                const isOpen = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                panel.hidden = isOpen;
            });
        });

        const groomerAddons = document.getElementById('furs-addons-rebook-modal');
        if (groomerAddons && groomerAddons.fursAddons) {
            window.handleRebookExtras(groomerAddons.fursAddons.getSelected(), groomerAddons.fursAddons.getTotal());
        }

        const spaceAddons = document.getElementById('furs-addons-rebook-space-modal');
        if (spaceAddons && spaceAddons.fursAddons) {
            window.handleRebookSpaceExtras(spaceAddons.fursAddons.getSelected(), spaceAddons.fursAddons.getTotal());
        }

        function getSelectedSlotInfo(slotsContainerId) {
            const selected = document.querySelector('#' + slotsContainerId + ' .rbm-slot.selected');
            if (!selected) return {
                date: '',
                time: ''
            };

            const date = selected.querySelector('.rbm-slot-date')?.textContent.trim() || '';
            const timeEl = selected.querySelector('.rbm-slot-time');
            let time = '';

            if (timeEl) {
                const clone = timeEl.cloneNode(true);
                clone.querySelectorAll('svg').forEach(function(svg) {
                    svg.remove();
                });
                time = clone.textContent.replace(/\s+/g, ' ').trim();
            }

            return {
                date: date,
                time: time
            };
        }

        function openRebookConfirmed(rebookModalId, confirmedModalId, slotsContainerId, dateElId, timeElId, totalElId, totalSourceId, fallbackTotal) {
            const slot = getSelectedSlotInfo(slotsContainerId);
            const dateEl = document.getElementById(dateElId);
            const timeEl = document.getElementById(timeElId);
            const totalEl = document.getElementById(totalElId);

            if (dateEl) dateEl.textContent = slot.date;
            if (timeEl) timeEl.textContent = slot.time;
            if (totalEl) {
                totalEl.textContent = document.getElementById(totalSourceId)?.textContent || fallbackTotal;
            }

            const rebookModal = document.getElementById(rebookModalId);
            const confirmedModal = document.getElementById(confirmedModalId);
            if (rebookModal) rebookModal.style.display = 'none';
            if (confirmedModal) confirmedModal.style.display = 'flex';
            if (window.syncBodyScrollLock) window.syncBodyScrollLock();
        }

        document.getElementById('rbm-groomer-confirm')?.addEventListener('click', function() {
            openRebookConfirmed(
                'rebook_groomer_modal',
                'groomer_rebook_confirmed_modal',
                'rbm-slots',
                'rbk-groomer-date',
                'rbk-groomer-time',
                'rbk-groomer-total',
                'rbm-grand-total',
                '£92.00'
            );
        });

        document.getElementById('rbm-space-confirm')?.addEventListener('click', function() {
            openRebookConfirmed(
                'rebook_space_modal',
                'space_rebook_confirmed_modal',
                'rbm-space-slots',
                'rbk-space-date',
                'rbk-space-time',
                'rbk-space-total',
                'rbm-space-grand-total',
                '£58.00'
            );
        });
    })();
</script>
