/**
 * FursGo custom UI (home hero, dropdowns, datetime, testimonials)
 * Uses document delegation where possible so Livewire wire:navigate
 * does not break Find Space / pet toggles / custom-selects.
 */
(function () {
  "use strict";

<<<<<<< HEAD
  let documentUiBound = false;
  let datetimeDocBound = false;

  function initMobileMenu() {
    const toggleBtn = document.querySelector(".menu-toggle");
    const menu = document.querySelector(".menu-items");
    const header = document.querySelector(".logo-toggle-button");
=======
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
>>>>>>> 3686cf2a86831824005858d1e89dafed90b1bdcd

    if (!toggleBtn || !menu || !header || toggleBtn.dataset.menuBound === "1") {
      return;
    }

    toggleBtn.dataset.menuBound = "1";
    toggleBtn.addEventListener("click", () => {
      menu.classList.toggle("active");
      header.classList.toggle("fixed");
      document.body.classList.toggle("menu-open");
      toggleBtn.innerHTML = menu.classList.contains("active") ? "✖" : "&#9776;";
    });
  }

  function setFindTab(isGroomer) {
    const groomerContent = document.querySelector(".find-groomer-content-area");
    const spaceContent = document.querySelector(".find-space-content-area");
    const groomerText = document.querySelector(
      ".find-groomer .find-groomer-space-text",
    );
    const spaceText = document.querySelector(
      ".find-space .find-groomer-space-text",
    );

    if (!groomerContent || !spaceContent || !groomerText || !spaceText) return;

<<<<<<< HEAD
    groomerContent.style.display = isGroomer ? "block" : "none";
    spaceContent.style.display = isGroomer ? "none" : "block";
    groomerText.classList.toggle("active", isGroomer);
    spaceText.classList.toggle("active", !isGroomer);
  }

  function bindDocumentUiOnce() {
    if (documentUiBound) return;
    documentUiBound = true;

    // Find Groomer / Find Space
    document.addEventListener("click", (e) => {
      if (e.target.closest(".find-groomer")) {
        setFindTab(true);
        return;
      }
      if (e.target.closest(".find-space")) {
        setFindTab(false);
      }
    });

    // Pet type / size toggles
    document.addEventListener("click", (e) => {
      const petOption = e.target.closest(
        ".find-grommer-content .pet-option, .find-space-content .pet-option",
      );
      if (petOption) {
        const container = petOption.closest(
          ".find-grommer-content, .find-space-content",
        );
        container
          ?.querySelectorAll(".pet-option")
          .forEach((btn) => btn.classList.remove("highlight"));
        petOption.classList.add("highlight");
        return;
      }

      const weightOption = e.target.closest(
        ".find-grommer-content .weight-option, .find-space-content .weight-option",
      );
      if (weightOption) {
        const container = weightOption.closest(
          ".find-grommer-content, .find-space-content",
        );
        container
          ?.querySelectorAll(".weight-option")
          .forEach((btn) => btn.classList.remove("active"));
        weightOption.classList.add("active");
      }
    });

    // Custom select open/close/select (delegation)
    document.addEventListener("click", (e) => {
      const option = e.target.closest(
        ".custom-select:not([data-multiselect]) .select-options li",
      );
      if (option) {
        const select = option.closest(".custom-select");
        const trigger = select?.querySelector(".select-trigger");
        const text = select?.querySelector(".selected-text");
        const hiddenInput = select?.querySelector('input[type="hidden"]');

        if (text) text.textContent = option.textContent;
        if (hiddenInput) hiddenInput.value = option.dataset.value;

        select?.classList.remove("open");
        select?.classList.add("has-value");
        if (trigger) {
          trigger.style.borderBottomLeftRadius = "12px";
          trigger.style.borderBottomRightRadius = "12px";
        }
        e.stopPropagation();
        return;
      }

      const trigger = e.target.closest(
        ".custom-select:not([data-multiselect]) .select-trigger",
      );
      if (trigger) {
        e.stopPropagation();
        const select = trigger.closest(".custom-select");

        document
          .querySelectorAll(".popover")
          .forEach((popover) => (popover.style.display = "none"));

        document
          .querySelectorAll(".custom-select:not([data-multiselect])")
          .forEach((s) => {
            if (s !== select) {
              s.classList.remove("open");
              const t = s.querySelector(".select-trigger");
              if (t) {
                t.style.borderBottomLeftRadius = "12px";
                t.style.borderBottomRightRadius = "12px";
              }
            }
          });

        const isOpen = select.classList.toggle("open");
        trigger.style.borderBottomLeftRadius = isOpen ? "0" : "12px";
        trigger.style.borderBottomRightRadius = isOpen ? "0" : "12px";
        return;
      }

      // Click outside: close selects
      document
        .querySelectorAll(".custom-select:not([data-multiselect])")
        .forEach((select) => {
          if (!select.contains(e.target)) {
            select.classList.remove("open");
            const t = select.querySelector(".select-trigger");
            if (t) {
              t.style.borderBottomLeftRadius = "12px";
              t.style.borderBottomRightRadius = "12px";
            }
            const hidden = select.querySelector('input[type="hidden"]');
            if (hidden && !hidden.value) {
              select.classList.remove("has-value");
            }
          }
        });
    });
  }

  function initDateTimePickers() {
    const datetimeWrappers = document.querySelectorAll(".datetime-wrapper");

    datetimeWrappers.forEach((wrapper) => {
      if (wrapper.dataset.datetimeBound === "1") return;
      wrapper.dataset.datetimeBound = "1";

      const dateField = wrapper.querySelector(".field.date");
      const timeField = wrapper.querySelector(".field.time");
      if (!dateField || !timeField) return;

      const dateInput = dateField.querySelector(".fake-input");
      const datePopover = dateField.querySelector(".popover");
      const daysGrid = datePopover?.querySelector(".days-grid");
      const monthLabel = datePopover?.querySelector("#monthLabel");
      const prevMonthBtn = datePopover?.querySelector("#prevMonth");
      const nextMonthBtn = datePopover?.querySelector("#nextMonth");
      const weekdayRow = datePopover?.querySelector(".weekday-row");
      const timeInput = timeField.querySelector(".fake-input");
      const timeList = datePopover?.querySelector(".time-list");

      if (
        !dateInput ||
        !datePopover ||
        !daysGrid ||
        !monthLabel ||
        !prevMonthBtn ||
        !nextMonthBtn ||
        !weekdayRow ||
        !timeInput ||
        !timeList
      ) {
        return;
      }

      const weekdays = ["M", "T", "W", "T", "F", "S", "S"];
      let selectedDate = new Date();
      let viewYear = selectedDate.getFullYear();
      let viewMonth = selectedDate.getMonth();
      let selectedTime = "13:00";

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
        return isSameDate(d, new Date());
      }
      function withMeridiem(hour) {
        return hour < 12 ? "AM" : "PM";
      }

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

      function generateTimes() {
        timeList.innerHTML = "";

        for (let hour = 0; hour < 24; hour++) {
          const timeValue = pad(hour) + ":00";
          const item = document.createElement("div");

          item.className = "time-item";
          item.dataset.time = timeValue;
          item.tabIndex = 0;
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

      function openPopover() {
        datePopover.style.display = "block";
        dateField.classList.add("focused");
        timeField.classList.add("focused");
        dateField.style.setProperty(
          "border-bottom-left-radius",
          "0px",
          "important",
        );
        timeField.style.setProperty(
          "border-bottom-right-radius",
          "0px",
          "important",
        );
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

      dateField.querySelector(".input-row")?.addEventListener("click", () => {
        datePopover.style.display === "block"
          ? closeAllPopovers()
          : openPopover();
      });

      timeField.querySelector(".input-row")?.addEventListener("click", () => {
        if (datePopover.style.display !== "block") openPopover();
        else {
          const el = timeList.querySelector(
            `.time-item[data-time="${selectedTime}"]`,
          );
          if (el) el.scrollIntoView({ block: "center", behavior: "smooth" });
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

      renderWeekdays();
      renderCalendar(viewYear, viewMonth);
      generateTimes();

      dateInput.value = formatDateForInput(selectedDate);
      timeInput.value = `${selectedTime} ${withMeridiem(parseInt(selectedTime, 10))}`;

      const selectedEl = timeList.querySelector(
        `.time-item[data-time="${selectedTime}"]`,
      );
      if (selectedEl) {
        selectedEl.classList.add("selected");
        selectedEl.scrollIntoView({ block: "center" });
      }
    });

    if (!datetimeDocBound) {
      datetimeDocBound = true;

      document.addEventListener("click", (e) => {
        if (!e.target.closest(".datetime-wrapper")) {
          document.querySelectorAll(".datetime-wrapper").forEach((wrapper) => {
            const dateField = wrapper.querySelector(".field.date");
            const timeField = wrapper.querySelector(".field.time");
            if (!dateField || !timeField) return;

            dateField.classList.remove("focused");
            timeField.classList.remove("focused");
            dateField.style.borderBottomLeftRadius = "10px";
            timeField.style.borderBottomRightRadius = "10px";
          });

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
    }
  }

  function initTestimonials() {
    const track = document.getElementById("track");
    const viewport = document.getElementById("viewport");
    const dotsWrap = document.getElementById("dots");
    const cards = [...document.querySelectorAll(".card")];

    if (!track || !viewport || cards.length === 0) return;
    if (track.dataset.testimonialBound === "1") return;
    track.dataset.testimonialBound = "1";

    let index = 0;
    const gap =
      parseInt(
        getComputedStyle(document.documentElement).getPropertyValue("--gap"),
      ) || 16;

    const getWidth = (el) => el.getBoundingClientRect().width;
    const isMobile = () => window.matchMedia("(max-width:768px)").matches;
    const normalize = (i) => ((i % cards.length) + cards.length) % cards.length;

    let currentOffset = 0;

    if (dotsWrap) {
      dotsWrap.innerHTML = "";
      cards.forEach((_, i) => {
        const dot = document.createElement("div");
        dot.className = "dot";
        dot.addEventListener("click", () => go(i));
        dotsWrap.appendChild(dot);
      });
    }

    function go(i, smooth = true) {
      index = normalize(i);
      const mobile = isMobile();
      const viewportW =
        mobile && window.visualViewport
          ? window.visualViewport.width
          : viewport.clientWidth;
=======
if (
    groomerBtn &&
    spaceBtn &&
    groomerContent &&
    spaceContent &&
    groomerText &&
    spaceText
) {
    const setActiveTab = (isGroomer) => {
        groomerContent.style.display = isGroomer ? "block" : "none";
        spaceContent.style.display = isGroomer ? "none" : "block";
        groomerText.classList.toggle("active", isGroomer);
        spaceText.classList.toggle("active", !isGroomer);
    };

    groomerBtn.addEventListener("click", () => setActiveTab(true));
    spaceBtn.addEventListener("click", () => setActiveTab(false));
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

            dateField.style.setProperty(
                "border-bottom-left-radius",
                "0px",
                "important",
            );
            timeField.style.setProperty(
                "border-bottom-right-radius",
                "0px",
                "important",
            );

            // Auto scroll to selected time
            const el = timeList.querySelector(
                `.time-item[data-time="${selectedTime}"]`,
            );
            // if (el) el.scrollIntoView({ block: "center", behavior: "smooth" });
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
                .querySelectorAll(".datetime-wrapper")
                .forEach((wrapper) => {
                    const dateField = wrapper.querySelector(".field.date");
                    const timeField = wrapper.querySelector(".field.time");

                    dateField.classList.remove("focused");
                    timeField.classList.remove("focused");

                    dateField.style.borderBottomLeftRadius = "10px";
                    timeField.style.borderBottomRightRadius = "10px";
                });

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
    if (select.dataset.dropdownBound === "1") return;
    if (select.classList.contains("help-category-select")) return;
    select.dataset.dropdownBound = "1";

    const trigger = select.querySelector(".select-trigger");
    if (!trigger) return;

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
                if (!t) return;
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
            if (text) text.textContent = option.textContent;
            if (hiddenInput) hiddenInput.value = option.dataset.value;

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
        const hidden = select.querySelector('input[type="hidden"]');
        if (!select.contains(e.target) && hidden && !hidden.value) {
            select.classList.remove("has-value");
        }
    });
});

// click outside
document.addEventListener("click", () => {
    document.querySelectorAll(".custom-select").forEach((select) => {
        if (select.classList.contains("help-category-select")) return;
        select.classList.remove("open");
        const trigger = select.querySelector(".select-trigger");
        if (!trigger) return;
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
>>>>>>> 3686cf2a86831824005858d1e89dafed90b1bdcd

      let offset = 0;
      for (let n = 0; n < index; n++) {
        const piece = mobile ? cards[n] : cards[n].querySelector("svg");
        if (!piece) continue;
        offset += getWidth(piece) + gap;
      }

      offset = offset + 12;

<<<<<<< HEAD
      const activeEl = mobile ? cards[index] : cards[index].querySelector("svg");
      if (!activeEl) return;
      const activeW = getWidth(activeEl);

      offset = index === 0 ? 0 : viewportW / 2 - (offset + activeW / 2);
      if (mobile) offset = Math.round(offset);

      track.style.transition = smooth ? "transform .45s ease" : "none";
      track.style.transform = `translateX(${offset}px)`;
      currentOffset = offset;

      cards.forEach((c, ci) => c.classList.toggle("active", ci === index));
      document
        .querySelectorAll(".dot")
        .forEach((d, di) => d.classList.toggle("active", di === index));
    }

    const prevBtn = document.getElementById("prev");
    const nextBtn = document.getElementById("next");
    if (prevBtn) prevBtn.addEventListener("click", () => go(index - 1));
    if (nextBtn) nextBtn.addEventListener("click", () => go(index + 1));
=======
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
>>>>>>> 3686cf2a86831824005858d1e89dafed90b1bdcd

    window.addEventListener("resize", () => go(index, false));
    go(index, false);

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
      track.style.transform = `translateX(${currentOffset + delta}px)`;
    };

    const onDragEnd = () => {
      if (!isDragging) return;
      isDragging = false;
      const delta = currentX - startX;

      if (delta < -50) go(index + 1);
      else if (delta > 50) go(index - 1);
      else go(index);
    };

    track.addEventListener("mousedown", onDragStart);
    track.addEventListener("touchstart", onDragStart);
    window.addEventListener("mousemove", onDragMove);
    window.addEventListener("touchmove", onDragMove);
    window.addEventListener("mouseup", onDragEnd);
    window.addEventListener("touchend", onDragEnd);
  }

  function cleanupPageStylesheets() {
    const path = window.location.pathname.replace(/\/+$/, "") || "/";
    const needsCompanyCss =
      path.includes("company_information") ||
      path.includes("about_us") ||
      path.includes("contact_us") ||
      path.includes("help-and-support") ||
      path.includes("support-and-assistance") ||
      path.includes("account_and_setting") ||
      path.includes("account-settings");

    if (needsCompanyCss) return;

    document
      .querySelectorAll('link[href*="company_information.css"]')
      .forEach((link) => link.remove());
  }

  function initFursgoCustomUi() {
    cleanupPageStylesheets();
    bindDocumentUiOnce();
    initMobileMenu();
    initDateTimePickers();
    initTestimonials();
  }

  window.initFursgoCustomUi = initFursgoCustomUi;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initFursgoCustomUi);
  } else {
    initFursgoCustomUi();
  }

  document.addEventListener("livewire:navigated", initFursgoCustomUi);
})();
