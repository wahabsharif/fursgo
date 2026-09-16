// modal js starts
(function() {
    let savedScrollY = 0;
    let scrollLocked = false;

    function isModalOpen(modal) {
        return window.getComputedStyle(modal).display !== 'none';
    }

    function hasOpenModal() {
        return Array.from(document.querySelectorAll('.modal')).some(isModalOpen);
    }

    function isScrollableModalTarget(target) {
        const modal = target && target.closest ? target.closest('.modal') : null;
        if (!modal) return false;

        let node = target;
        while (node && node !== modal.parentElement) {
            if (node === document.body || node === document.documentElement) break;
            if (node instanceof HTMLElement) {
                const style = window.getComputedStyle(node);
                const canScrollY = /(auto|scroll)/.test(style.overflowY);
                if (canScrollY && node.scrollHeight > node.clientHeight + 1) {
                    return true;
                }
            }
            if (node === modal) break;
            node = node.parentElement;
        }
        return false;
    }

    function onLockedScroll() {
        if (!scrollLocked) return;
        if (window.scrollY !== savedScrollY) {
            window.scrollTo(0, savedScrollY);
        }
    }

    function onLockedWheel(e) {
        if (!scrollLocked) return;
        if (isScrollableModalTarget(e.target)) return;
        e.preventDefault();
    }

    function onLockedTouchMove(e) {
        if (!scrollLocked) return;
        if (isScrollableModalTarget(e.target)) return;
        e.preventDefault();
    }

    function onLockedKeyDown(e) {
        if (!scrollLocked) return;

        const keys = ['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End', ' '];
        if (!keys.includes(e.key)) return;

        const tag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
        if (tag === 'input' || tag === 'textarea' || tag === 'select' || (e.target && e.target.isContentEditable)) {
            return;
        }
        if (isScrollableModalTarget(e.target)) return;

        e.preventDefault();
    }

    function addScrollLockListeners() {
        window.addEventListener('scroll', onLockedScroll, { passive: false });
        window.addEventListener('wheel', onLockedWheel, { passive: false });
        window.addEventListener('touchmove', onLockedTouchMove, { passive: false });
        window.addEventListener('keydown', onLockedKeyDown, { passive: false });
    }

    function removeScrollLockListeners() {
        window.removeEventListener('scroll', onLockedScroll, { passive: false });
        window.removeEventListener('wheel', onLockedWheel, { passive: false });
        window.removeEventListener('touchmove', onLockedTouchMove, { passive: false });
        window.removeEventListener('keydown', onLockedKeyDown, { passive: false });
    }

    function syncBodyScrollLock() {
        if (hasOpenModal()) {
            if (scrollLocked) return;

            savedScrollY = window.scrollY || window.pageYOffset;
            scrollLocked = true;

            document.documentElement.classList.add('modal-scroll-lock');
            document.body.classList.add('modal-scroll-lock');
            addScrollLockListeners();
            window.scrollTo(0, savedScrollY);
            return;
        }

        if (!scrollLocked) return;

        scrollLocked = false;
        document.documentElement.classList.remove('modal-scroll-lock');
        document.body.classList.remove('modal-scroll-lock');
        removeScrollLockListeners();
        window.scrollTo(0, savedScrollY);
    }

    window.syncBodyScrollLock = syncBodyScrollLock;

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.modal').forEach(function(modal) {
            new MutationObserver(syncBodyScrollLock).observe(modal, {
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        });
    });

    document.addEventListener('click', function(e) {
        const openTrigger = e.target.closest('[data-modal-open]');
        if (openTrigger) {
            e.preventDefault();
            const modal = document.getElementById(openTrigger.dataset.modalOpen);
            if (modal) {
                modal.style.display = 'flex';
                syncBodyScrollLock();
            }
        }

        if (e.target.closest('[data-modal-close]')) {
            e.preventDefault();
            const modal = e.target.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
                syncBodyScrollLock();
            }
        }

        if (e.target.closest('[data-modal-submit-close]')) {
            e.preventDefault();
            const modal = e.target.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
                syncBodyScrollLock();
            }
        }

        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
            syncBodyScrollLock();
        }
    });
})();
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

    const noScroll = wrapper.hasAttribute('data-tabs-no-scroll');
    const scrollY = noScroll ? window.scrollY : null;

    // reset
    tabs.forEach(t => t.classList.remove('active'));
    panels.forEach(p => p.classList.remove('active'));

    // activate
    tab.classList.add('active');

    const targetPanel = document.getElementById(targetId);
    if (targetPanel) {
        targetPanel.classList.add('active');
    }

    if (noScroll) {
        requestAnimationFrame(() => window.scrollTo({ top: scrollY, behavior: 'auto' }));
        return;
    }

    // Show the new tab content from the top (below sticky header)
    if (targetPanel) {
        requestAnimationFrame(() => {
            const header = document.querySelector('header');
            const headerHeight = header ? header.offsetHeight : 0;
            const top = targetPanel.getBoundingClientRect().top + window.scrollY - headerHeight - 20;
            window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
        });
    }
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

document.querySelectorAll('.custom-select:not([data-multiselect]):not([data-singleselect])').forEach(select => {
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

        document.querySelectorAll('.custom-select:not([data-multiselect]):not([data-singleselect])').forEach(s => {
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
    document.querySelectorAll('.custom-select:not([data-multiselect]):not([data-singleselect])').forEach(select => {
        if (!select.contains(e.target) && !select.querySelector('input[type="hidden"]').value) {
            select.classList.remove('has-value');
        }
    });
});


// tab content scroll js
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        // [data-tabs] is handled by the delegated tab switcher above.
        // Scrolling here makes sticky sidebars jump when cycling tabs.
        if (this.closest('[data-tabs]')) return;

        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

        this.classList.add('active');
        const target = document.getElementById(this.dataset.tab);
        if (!target) return;
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