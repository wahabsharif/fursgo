// custom select dropdown js  

document.querySelectorAll('.service-type-select .custom-select').forEach(select => {
    const trigger = select.querySelector('.service-type-select .custom-select .select-trigger');
    const options = select.querySelectorAll('.service-type-select .custom-select .select-options li');
    const datePopovers = document.querySelectorAll('.popover');
    const text = select.querySelector('.selected-text');
    const hiddenInput = select.querySelector('input[type="hidden"]');

    trigger.addEventListener('click', e => {
        e.stopPropagation();

        datePopovers.forEach(popover => {
            popover.style.display = 'none';
        });

        document.querySelectorAll('.custom-select').forEach(s => {
            if (s !== select) {
                s.classList.remove('open');
                const t = s.querySelector('.select-trigger');
                t.style.cssText = `
                    border-bottom-left-radius: 12px;
                    border-bottom-right-radius: 12px;
                `;
            }
        });

        const isOpen = select.classList.toggle('open');

        trigger.style.cssText = isOpen ?
            `border-bottom-left-radius: 0; border-bottom-right-radius: 0;` :
            `border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;`;
    });

    options.forEach(option => {
        option.addEventListener('click', () => {
            text.textContent = option.textContent;
            hiddenInput.value = option.dataset.value;

            select.classList.remove('open');
            select.classList.add('has-value'); // add class to highlight border

            trigger.style.cssText = `
                border-bottom-left-radius: 12px;
                border-bottom-right-radius: 12px;
            `;
        });
    });
});


// calendar
const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

let currentDate = new Date(2025, 9); // October 2025

const headerTitle = document.querySelector('.calendar-header span');
const datesContainer = document.querySelector('.dates');
const prevBtn = document.querySelector('.nav-btn:first-child');
const nextBtn = document.querySelector('.nav-btn:last-child');

// Example available dates (can come from backend later)
const availableDates = [
    "2025-10-07",
    "2025-10-09",
    "2025-10-14",
    "2025-10-15",
    "2025-10-20",
    "2025-10-26",
    "2025-10-29",
    "2025-10-30"
];

function renderCalendar() {
    datesContainer.innerHTML = '';

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    headerTitle.textContent = `${monthNames[month]} ${year}`;

    const firstDay = new Date(year, month, 1).getDay() || 7;
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    for (let i = 1; i < firstDay; i++) {
        datesContainer.appendChild(document.createElement('div'));
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dateDiv = document.createElement('div');
        dateDiv.classList.add('date');
        dateDiv.textContent = day;

        const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        if (availableDates.includes(dateKey)) {
            dateDiv.classList.add('available');

            dateDiv.addEventListener('click', () => {
                document.querySelectorAll('.date').forEach(d => d.classList.remove('selected'));
                dateDiv.classList.add('selected');
            });
        }

        datesContainer.appendChild(dateDiv);
    }
}

prevBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});

nextBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

// Time selection (unchanged)
document.querySelectorAll('.time').forEach(time => {
    time.addEventListener('click', () => {
        document.querySelectorAll('.time').forEach(t => t.classList.remove('selected'));
        time.classList.add('selected');
    });
});

renderCalendar();


const tabs_go_to = document.querySelectorAll('.tab-go-to-section a');

tabs_go_to.forEach((tab) => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-go-to-section a').forEach((tab) => {
            tab.classList.remove('active');
        });
        tab.classList.add('active');
    });
});



//multi select 

document.querySelectorAll('[data-multiselect]').forEach(select => {
    const trigger = select.querySelector('.select-trigger');
    const options = select.querySelector('.select-options');
    const items = options.querySelectorAll('li');
    const hiddenInput = select.querySelector('input[type="hidden"]');
    const selectedBox = select.closest('.service-type-select')
        .querySelector('.service-selected-options');

    // ✅ Use data-color or default
    const tagColor = select.dataset.color || '#FBAC83';

    // Initialize selectedValues from pre-existing tags
    let selectedValues = [];
    selectedBox.querySelectorAll('.selected-item').forEach(tag => {
        const value = tag.dataset.value;
        if (value) selectedValues.push(value);

        const removeBtn = tag.querySelector('svg');
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                selectedValues = selectedValues.filter(v => v !== value);
                tag.remove();
                updateHiddenInput();
            });
        }
    });

    updateHiddenInput(); // sync hidden input with pre-selected values

    // Toggle dropdown
    trigger.addEventListener('click', () => {
        options.classList.toggle('open');
    });

    // Select option
    items.forEach(item => {
        item.addEventListener('click', () => {
            const value = item.dataset.value;
            const text = item.textContent.trim();

            if (selectedValues.includes(value)) return; // already selected

            selectedValues.push(value);
            updateHiddenInput();
            renderTag(value, text);
        });
    });

    function renderTag(value, text) {
        const tag = document.createElement('div');
        tag.className = 'selected-item d-flex align-items-center gap-10';
        tag.dataset.value = value;
        tag.style.background = 'none';
        tag.style.color = tagColor;
        tag.style.border = `1px solid ${tagColor}`;

        tag.innerHTML = `
            <p>${text}</p>
            <svg style="cursor: pointer" data-remove xmlns="http://www.w3.org/2000/svg" width="9" height="9"
                viewBox="0 0 9 9" fill="none">
                <path d="M0.5 7.57L7.572 0.5M0.5 0.5L7.572 7.57"
                    stroke="${tagColor}" stroke-linecap="round" />
            </svg>
        `;

        tag.querySelector('[data-remove]').addEventListener('click', () => {
            selectedValues = selectedValues.filter(v => v !== value);
            tag.remove();
            updateHiddenInput();
        });

        selectedBox.appendChild(tag);
    }

    function updateHiddenInput() {
        hiddenInput.value = selectedValues.join(',');
    }

    document.addEventListener('click', e => {
        if (!select.contains(e.target)) {
            options.classList.remove('open');
        }
    });
});

// fav button
const favButton = document.querySelector('.fav');

favButton.addEventListener('click', () => {
    favButton.classList.toggle('active');

    const pressed = favButton.getAttribute('aria-pressed') === 'true';
    favButton.setAttribute('aria-pressed', !pressed);
});
// fav button