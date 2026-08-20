// modal js starts 
document.addEventListener('click', (e) => {

    // Open modal
    const openTrigger = e.target.closest('[data-modal-open]');
    if (openTrigger) {
        const modal = document.getElementById(openTrigger.dataset.modalOpen);
        if (modal) modal.style.display = 'flex';
    }

    // Close modal (button)
    if (e.target.closest('[data-modal-close]')) {
        const modal = e.target.closest('.modal');
        if (modal) modal.style.display = 'none';
    }

    if (e.target.closest('[data-modal-submit-close]')) {
        const modal = e.target.closest('.modal');
        if (modal) modal.style.display = 'none';
    }
    
    // Close modal (overlay)
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});

// modal js ends


// tab js starts

document.addEventListener('click', function (e) {
    const tab = e.target.closest('.tab-btn');
    if (!tab) return;

    const wrapper = tab.closest('[data-tabs]');
    if (!wrapper) return;

    const tabs = wrapper.querySelectorAll('.tab-btn');
    const targetId = tab.dataset.tab;

    // 1️⃣ Try to find panels inside wrapper
    let panels = wrapper.querySelectorAll('.tab-panel');

    // 2️⃣ If none found, fallback to global panels
    if (!panels.length) {
        panels = document.querySelectorAll('.tab-panel');
    }

    const noScroll = wrapper.hasAttribute('data-tabs-no-scroll'); // 👈
    const scrollY = noScroll ? window.scrollY : null;             // 👈

    // reset
    tabs.forEach(t => t.classList.remove('active'));
    panels.forEach(p => p.classList.remove('active'));

    // activate
    tab.classList.add('active');

    const targetPanel = document.getElementById(targetId);
    if (targetPanel) {
        targetPanel.classList.add('active');
    }

    if (noScroll) requestAnimationFrame(() => window.scrollTo({ top: scrollY, behavior: 'instant' })); // 👈
});

// tab js ends


// accodian js starts 

var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function () {
        this.classList.toggle("acc-active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight) {
            panel.style.maxHeight = null;
        } else {
            panel.style.maxHeight = panel.scrollHeight + "px";
        }
    });
}

// accodian js ends


// custom select dropdown js  

document.querySelectorAll('.custom-select:not([data-multiselect])').forEach(select => {
    const trigger = select.querySelector('.select-trigger');
    const options = select.querySelectorAll('.select-options li');
    const datePopovers = document.querySelectorAll('.popover');
    const text = select.querySelector('.selected-text');
    const hiddenInput = select.querySelector('input[type="hidden"]');

    trigger.addEventListener('click', e => {
        e.stopPropagation();

        datePopovers.forEach(popover => {
            popover.style.display = 'none';
        });

        document.querySelectorAll('.custom-select:not([data-multiselect])').forEach(s => {
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

// Remove 'has-value' if clicked outside and no value
document.addEventListener('click', (e) => {
    document.querySelectorAll('.custom-select:not([data-multiselect])').forEach(select => {
        if (!select.contains(e.target) && !select.querySelector('input[type="hidden"]').value) {
            select.classList.remove('has-value');
        }
    });
});


// tab content scroll js
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

        this.classList.add('active');
        const target = document.getElementById(this.dataset.tab);
        target.classList.add('active');

        // Offset scroll by header height
        const headerHeight = document.querySelector('header').offsetHeight;
        const top = target.getBoundingClientRect().top + window.scrollY - headerHeight - 20;
        window.scrollTo({ top, behavior: 'smooth' });
    });
});


// horizontal tabs 

document.querySelectorAll('.tab-wrapper').forEach(wrapper => {
    const buttons = wrapper.querySelectorAll('.tablinks');
    const contents = document.querySelectorAll('.tabcontent');

    function activateTab(tabName) {
        contents.forEach(c => {
            if (c.dataset.tabContent === tabName) {
                c.style.display = c.dataset.display || 'flex';
            } else {
                c.style.display = 'none';
            }
        });

        buttons.forEach(b => {
            b.classList.toggle('active', b.dataset.tab === tabName);
        });
    }

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            activateTab(btn.dataset.tab);
        });
    });

    // activate the button that already has 'active', otherwise first button
    const activeBtn = wrapper.querySelector('.tablinks.active');
    if (activeBtn) {
        activateTab(activeBtn.dataset.tab);
    } else if (buttons.length) {
        activateTab(buttons[0].dataset.tab);
    }
});


// show hide divs 
// Reusable toggle function
function toggleDisplay(triggerSelector, targetSelector) {
    const trigger = document.querySelector(triggerSelector);
    const target = document.querySelector(targetSelector);

    if (!trigger || !target) return;

    trigger.addEventListener('click', (e) => {
        if (target.contains(e.target)) return; // don't toggle if clicking inside
        target.style.display = (target.style.display === 'block') ? 'none' : 'block';
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!trigger.contains(e.target)) {
            target.style.display = 'none';
        }
    });
}

// Usage
// toggleDisplay('.sort-by', '.sort-by-filter');
// toggleDisplay('.mark-notification-dots-svg', '.clear-read.dropdown');