const toggleBtn = document.querySelector(".menu-toggle");
const menu = document.querySelector(".menu-items");
const header = document.querySelector(".logo-toggle-button");

if (toggleBtn && menu && header) {
    toggleBtn.addEventListener("click", () => {
        menu.classList.toggle("active");
        header.classList.toggle("fixed");
        document.body.classList.toggle("menu-open");

        toggleBtn.innerHTML = menu.classList.contains("active")
            ? "✖"
            : "&#9776;";
    });
}

const groomerBtn = document.querySelector(".find-groomer");
const spaceBtn = document.querySelector(".find-space");
const groomerContent = document.querySelector(".find-groomer-content-area");
const spaceContent = document.querySelector(".find-space-content-area");

if (groomerBtn && spaceBtn && groomerContent && spaceContent) {
    groomerBtn.addEventListener("click", () => {
        groomerContent.style.display = "block";
        spaceContent.style.display = "none";
        groomerBtn.classList.add("active");
        spaceBtn.classList.remove("active");
    });

    spaceBtn.addEventListener("click", () => {
        spaceContent.style.display = "block";
        groomerContent.style.display = "none";
        spaceBtn.classList.add("active");
        groomerBtn.classList.remove("active");
    });
}

function toggleActive(containerSelector, itemSelector, activeClass) {
    const items = document.querySelectorAll(
        `${containerSelector} ${itemSelector}`,
    );

    items.forEach((item) => {
        item.addEventListener("click", () => {
            items.forEach((button) => button.classList.remove(activeClass));
            item.classList.add(activeClass);
        });
    });
}

// Find Groomer
toggleActive(".find-grommer-content", ".pet-option", "highlight");
toggleActive(".find-grommer-content", ".weight-option", "active");

// Find Space
toggleActive(".find-space-content", ".pet-option", "highlight");
toggleActive(".find-space-content", ".weight-option", "active");

// date time picker js

(function () {
    const datetimeWrappers = document.querySelectorAll(".datetime-wrapper");

    datetimeWrappers.forEach((wrapper) => {
        const dateField = wrapper.querySelector(".field.date");
        const dateInput = dateField.querySelector(".fake-input");
        const datePopover = dateField.querySelector(".popover");
        const daysGrid = datePopover.querySelector(".days-grid");
        const monthLabel = datePopover.querySelector("#monthLabel");
        const prevMonthBtn = datePopover.querySelector("#prevMonth");
        const nextMonthBtn = datePopover.querySelector("#nextMonth");
        const weekdayRow = datePopover.querySelector(".weekday-row");

        const timeField = wrapper.querySelector(".field.time");
        const timeInput = timeField.querySelector(".fake-input");
        const timeList = datePopover.querySelector(".time-list");

        const weekdays = ["M", "T", "W", "T", "F", "S", "S"];

        let selectedDate = new Date();
        let viewYear = selectedDate.getFullYear();
        let viewMonth = selectedDate.getMonth();
        let selectedTime = "13:00"; // internal 24h value

        /* ---------------------- Utility ---------------------- */
        function pad(n) {
            return n < 10 ? "0" + n : n;
        }
        function monthName(m) {
            return new Date(2000, m, 1).toLocaleString("en", { month: "long" });
        }
        function formatDateForInput(d) {
            return `${pad(d.getDate())} ${monthName(d.getMonth())} ${d.getFullYear()}`;
        }
        function isSameDate(a, b) {
            return (
                a &&
                b &&
                a.getFullYear() === b.getFullYear() &&
                a.getMonth() === b.getMonth() &&
                a.getDate() === b.getDate()
            );
        }
        function isToday(d) {
            const t = new Date();
            return isSameDate(d, t);
        }

        // AM / PM helper (KEEP 24h numbers)
        function withMeridiem(hour) {
            return hour < 12 ? "AM" : "PM";
        }

        /* ---------------------- Calendar ---------------------- */
        function renderWeekdays() {
            weekdayRow.innerHTML = "";
            weekdays.forEach((d) => {
                const el = document.createElement("div");
                el.textContent = d;
                weekdayRow.appendChild(el);
            });
        }

        function renderCalendar(year, month) {
            monthLabel.textContent = `${monthName(month)} ${year}`;
            daysGrid.innerHTML = "";

            const firstDay = new Date(year, month, 1);
            const startOffset = (firstDay.getDay() + 6) % 7;
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const prevMonthLastDay = new Date(year, month, 0).getDate();
            const totalCells = Math.ceil((startOffset + daysInMonth) / 7) * 7;

            for (let i = 0; i < totalCells; i++) {
                const cell = document.createElement("div");
                cell.className = "day";
                const dayIndex = i - startOffset + 1;

                if (i < startOffset) {
                    cell.textContent = prevMonthLastDay - (startOffset - 1 - i);
                    cell.classList.add("outside");
                } else if (dayIndex > daysInMonth) {
                    cell.textContent = dayIndex - daysInMonth;
                    cell.classList.add("outside");
                } else {
                    const cellDate = new Date(year, month, dayIndex);
                    cell.textContent = dayIndex;

                    if (isSameDate(cellDate, selectedDate))
                        cell.classList.add("selected");
                    else if (isToday(cellDate)) cell.classList.add("today");

                    cell.tabIndex = 0;
                    cell.addEventListener("click", () => {
                        selectedDate = cellDate;
                        dateInput.value = formatDateForInput(selectedDate);
                        dateField.classList.add("has-value");
                        closeAllPopovers();
                        renderCalendar(viewYear, viewMonth);
                    });
                }

                daysGrid.appendChild(cell);
            }
        }

        /* ---------------------- Time List ---------------------- */
        function generateTimes() {
            timeList.innerHTML = "";

            for (let hour = 0; hour < 24; hour++) {
                const timeValue = pad(hour) + ":00";
                const item = document.createElement("div");

                item.className = "time-item";
                item.dataset.time = timeValue;
                item.tabIndex = 0;

                // DISPLAY: 24h + AM/PM
                item.textContent = `${timeValue} ${withMeridiem(hour)}`;

                item.addEventListener("click", () => {
                    selectedTime = timeValue;
                    timeInput.value = `${timeValue} ${withMeridiem(hour)}`;
                    timeField.classList.add("has-value");

                    timeList
                        .querySelectorAll(".time-item")
                        .forEach((i) => i.classList.remove("selected"));

                    item.classList.add("selected");
                    closeAllPopovers();
                });

                timeList.appendChild(item);
            }
        }

        /* ---------------------- Popover ---------------------- */
        function openPopover() {
            datePopover.style.display = "block";
            dateField.classList.add("focused");
            timeField.classList.add("focused");

            dateField.style.borderBottomLeftRadius = "0px";
            timeField.style.borderBottomRightRadius = "0px";

            // Auto scroll to selected time
            const el = timeList.querySelector(
                `.time-item[data-time="${selectedTime}"]`,
            );
            if (el) el.scrollIntoView({ block: "center", behavior: "smooth" });
        }

        function closeAllPopovers() {
            document
                .querySelectorAll(".popover")
                .forEach((p) => (p.style.display = "none"));

            dateField.classList.remove("focused");
            timeField.classList.remove("focused");

            dateField.style.borderBottomLeftRadius = "10px";
            timeField.style.borderBottomRightRadius = "10px";
        }

        /* ---------------------- Events ---------------------- */
        dateField.querySelector(".input-row").addEventListener("click", () => {
            datePopover.style.display === "block"
                ? closeAllPopovers()
                : openPopover();
        });

        timeField.querySelector(".input-row").addEventListener("click", () => {
            if (datePopover.style.display !== "block") openPopover();
            else {
                const el = timeList.querySelector(
                    `.time-item[data-time="${selectedTime}"]`,
                );
                if (el)
                    el.scrollIntoView({ block: "center", behavior: "smooth" });
            }
        });

        prevMonthBtn.addEventListener("click", () => {
            viewMonth--;
            if (viewMonth < 0) {
                viewMonth = 11;
                viewYear--;
            }
            renderCalendar(viewYear, viewMonth);
        });

        nextMonthBtn.addEventListener("click", () => {
            viewMonth++;
            if (viewMonth > 11) {
                viewMonth = 0;
                viewYear++;
            }
            renderCalendar(viewYear, viewMonth);
        });

        /* ---------------------- Init ---------------------- */
        function init() {
            renderWeekdays();
            renderCalendar(viewYear, viewMonth);
            generateTimes();

            dateInput.value = formatDateForInput(selectedDate);
            timeInput.value = `${selectedTime} ${withMeridiem(parseInt(selectedTime))}`;

            const selectedEl = timeList.querySelector(
                `.time-item[data-time="${selectedTime}"]`,
            );
            if (selectedEl) {
                selectedEl.classList.add("selected");
                selectedEl.scrollIntoView({ block: "center" });
            }
        }

        init();
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".datetime-wrapper")) {
            document
                .querySelectorAll(".popover")
                .forEach((p) => (p.style.display = "none"));
        }
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            document
                .querySelectorAll(".popover")
                .forEach((p) => (p.style.display = "none"));
        }
    });
})();

// custom select dropdown js

document.querySelectorAll(".custom-select").forEach((select) => {
    const trigger = select.querySelector(".select-trigger");
    const options = select.querySelectorAll(".select-options li");
    const datePopovers = document.querySelectorAll(".popover");
    const text = select.querySelector(".selected-text");
    const hiddenInput = select.querySelector('input[type="hidden"]');

    trigger.addEventListener("click", (e) => {
        e.stopPropagation();

        datePopovers.forEach((popover) => {
            popover.style.display = "none";
        });

        document.querySelectorAll(".custom-select").forEach((s) => {
            if (s !== select) {
                s.classList.remove("open");
                const t = s.querySelector(".select-trigger");
                t.style.cssText = `
                    border-bottom-left-radius: 12px;
                    border-bottom-right-radius: 12px;
                `;
            }
        });

        const isOpen = select.classList.toggle("open");

        trigger.style.cssText = isOpen
            ? `border-bottom-left-radius: 0; border-bottom-right-radius: 0;`
            : `border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;`;
    });

    options.forEach((option) => {
        option.addEventListener("click", () => {
            text.textContent = option.textContent;
            hiddenInput.value = option.dataset.value;

            select.classList.remove("open");
            select.classList.add("has-value"); // add class to highlight border

            trigger.style.cssText = `
                border-bottom-left-radius: 12px;
                border-bottom-right-radius: 12px;
            `;
        });
    });
});

// Remove 'has-value' if clicked outside and no value
document.addEventListener("click", (e) => {
    document.querySelectorAll(".custom-select").forEach((select) => {
        if (
            !select.contains(e.target) &&
            !select.querySelector('input[type="hidden"]').value
        ) {
            select.classList.remove("has-value");
        }
    });
});

// click outside
document.addEventListener("click", () => {
    document.querySelectorAll(".custom-select").forEach((select) => {
        select.classList.remove("open");
        const trigger = select.querySelector(".select-trigger");
        trigger.style.cssText = `
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        `;
    });
});

// testimonial section js starts

// testimonial section js starts

const track = document.getElementById("track");
const viewport = document.getElementById("viewport");
const cards = [...document.querySelectorAll(".card")];
const dotsWrap = document.getElementById("dots");
let index = 0;
const gap =
    parseInt(
        getComputedStyle(document.documentElement).getPropertyValue("--gap"),
    ) || 16;

const getWidth = (el) => el.getBoundingClientRect().width;
const isMobile = () => window.matchMedia("(max-width:768px)").matches;

// Create dots (only if dotsWrap exists)
if (dotsWrap && cards.length > 0) {
    dotsWrap.innerHTML = "";
    cards.forEach((_, i) => {
        const dot = document.createElement("div");
        dot.className = "dot";
        dot.addEventListener("click", () => go(i));
        dotsWrap.appendChild(dot);
    });
}

// Normalize index for looping
const normalize = (i) => ((i % cards.length) + cards.length) % cards.length;

// Track current offset
let currentOffset = 0;

// Move to card
function go(i, smooth = true) {
    index = normalize(i);
    const mobile = isMobile();
    const viewportW =
        mobile && window.visualViewport
            ? window.visualViewport.width
            : viewport.clientWidth;

    let offset = 0;
    for (let n = 0; n < index; n++) {
        offset +=
            getWidth(mobile ? cards[n] : cards[n].querySelector("svg")) + gap;
        // offset += getWidth(cards[n]) + (gap);
    }

    offset = offset + 12;

    //console.log(offset);

    const activeEl = mobile ? cards[index] : cards[index].querySelector("svg");
    const activeW = getWidth(activeEl);

    // console.log(activeW);

    offset = index === 0 ? 0 : viewportW / 2 - (offset + activeW / 2);
    if (mobile) offset = Math.round(offset);

    track.style.transition = smooth ? "transform .45s ease" : "none";
    track.style.transform = `translateX(${offset}px)`;
    currentOffset = offset; // update current offset

    // Update active classes
    cards.forEach((c, ci) => c.classList.toggle("active", ci === index));
    document
        .querySelectorAll(".dot")
        .forEach((d, di) => d.classList.toggle("active", di === index));
}

// Arrow navigation
var prevBtn = document.getElementById("prev");
var nextBtn = document.getElementById("next");
if (prevBtn) prevBtn.addEventListener("click", () => go(index - 1));
if (nextBtn) nextBtn.addEventListener("click", () => go(index + 1));

// Init (only if viewport exists - carousel code is only for certain pages)
if (viewport && track && cards.length > 0) {
    window.addEventListener("resize", () => go(index, false));
    go(index, false);
}

// Dragging
let startX = 0,
    currentX = 0,
    isDragging = false;

const onDragStart = (e) => {
    isDragging = true;
    startX = e.touches ? e.touches[0].clientX : e.clientX;
    track.style.transition = "none";
};

const onDragMove = (e) => {
    if (!isDragging) return;
    currentX = e.touches ? e.touches[0].clientX : e.clientX;
    const delta = currentX - startX;
    track.style.transform = `translateX(${currentOffset + delta}px)`; // add delta to current offset
};

const onDragEnd = (e) => {
    if (!isDragging) return;
    isDragging = false;
    const delta = currentX - startX;

    if (delta < -50)
        go(index + 1); // swipe left
    else if (delta > 50)
        go(index - 1); // swipe right
    else go(index); // snap back
};

// Drag events
track.addEventListener("mousedown", onDragStart);
track.addEventListener("touchstart", onDragStart);
window.addEventListener("mousemove", onDragMove);
window.addEventListener("touchmove", onDragMove);
window.addEventListener("mouseup", onDragEnd);
window.addEventListener("touchend", onDragEnd);

// ======== pet-breed-autocomplete =========
window.petBreedsData = {};
window.selectedPetType = "";

// Load JSON from /data/pet-breeds.json
async function loadPetBreedsData() {
    try {
        const response = await fetch("/data/pet-breeds.json");
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        window.petBreedsData = await response.json();
    } catch (error) {
        console.error("Failed to load pet breeds:", error);
    }
}

// Populate a breed dropdown based on selected pet type
function populateBreeds(breedSelectId, petType) {
    const select = document.getElementById(breedSelectId);
    if (!select) return;

    select.innerHTML = '<option value="">Select a Breed</option>';
    if (petType && petType.breeds) {
        petType.breeds.forEach((breed) => {
            const option = document.createElement("option");
            option.value = breed;
            option.textContent = breed;
            select.appendChild(option);
        });
    }
    // If the select is a furs‑dropdown, refresh it
    if (select._fursDD) select._fursDD.refresh();
}

// Set up auto‑detection for a pet type input
function setupPetTypeAutoDetection(petTypeInputId, breedSelectId) {
    const petTypeInput = document.getElementById(petTypeInputId);
    const suggestionBox = document.getElementById(
        `${petTypeInputId}-suggestions`,
    );
    if (!petTypeInput || !suggestionBox) return;

    // Hide suggestions when clicking outside
    document.addEventListener("click", (e) => {
        if (e.target !== petTypeInput && !suggestionBox.contains(e.target)) {
            suggestionBox.style.display = "none";
        }
    });

    petTypeInput.addEventListener("focus", () => {
        if (petTypeInput.value.trim().length > 0) {
            suggestionBox.style.display = "block";
        }
    });

    petTypeInput.addEventListener("input", function () {
        const inputValue = this.value.trim().toLowerCase();
        suggestionBox.innerHTML = "";

        if (inputValue.length === 0) {
            suggestionBox.style.display = "none";
            // Clear breed dropdown
            populateBreeds(breedSelectId, null);
            window.selectedPetType = "";
            return;
        }

        const matches =
            window.petBreedsData.petTypes?.filter((petType) =>
                petType.name.toLowerCase().includes(inputValue),
            ) || [];

        if (matches.length > 0) {
            suggestionBox.style.display = "block";
            matches.forEach((match) => {
                const item = document.createElement("div");
                item.textContent = match.name;
                item.style.cssText =
                    "padding: 10px; cursor: pointer; border-bottom: 1px solid #EEE;font-family: Lato;color: rgb(59, 55, 49);";
                item.addEventListener("click", () => {
                    petTypeInput.value = match.name;
                    window.selectedPetType = match.name;
                    suggestionBox.style.display = "none";
                    populateBreeds(breedSelectId, match);
                });
                suggestionBox.appendChild(item);
            });
        } else {
            suggestionBox.style.display = "none";
            populateBreeds(breedSelectId, null);
        }

        // Exact match → populate breeds
        const exactMatch = window.petBreedsData.petTypes?.find(
            (p) => p.name.toLowerCase() === inputValue,
        );
        if (exactMatch) {
            window.selectedPetType = exactMatch.name;
            populateBreeds(breedSelectId, exactMatch);
            suggestionBox.style.display = "none";
        }
    });
}

// Load data on page load (only once)
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", loadPetBreedsData);
} else {
    loadPetBreedsData();
}
