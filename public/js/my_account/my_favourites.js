(function () {
    const root = document.getElementById('favourites-root');
    if (!root || root.dataset.favReady === '1') return;
    root.dataset.favReady = '1';

    const titleEl = document.getElementById('fav-title');
    const searchInput = document.getElementById('fav-search');
    const tabs = root.querySelectorAll('.fav-tab');
    const panels = root.querySelectorAll('[data-fav-panel]');
    const badge = document.querySelector('.account-tab[data-tab="favourites"] .account-tab__badge');
    const favPanel = document.getElementById('panel-favourites');

    const titles = {
        groomers: 'Favourite Groomers',
        spaces: 'Favourite Spaces'
    };

    function createSlider(grid, prevBtn, nextBtn) {
        if (!grid) return { update: function () {} };

        function getStep() {
            const card = grid.querySelector(':scope > .fav-card:not([hidden])');
            return card ? card.offsetWidth + 40 : 335;
        }

        function update() {
            const n = grid.querySelectorAll(':scope > .fav-card:not([hidden])').length;
            const max = grid.scrollWidth - grid.clientWidth;
            const can = n > 1 && grid.clientWidth > 0 && max > 2;
            prevBtn?.classList.toggle('is-hidden', !can || grid.scrollLeft <= 2);
            nextBtn?.classList.toggle('is-hidden', !can || grid.scrollLeft >= max - 2);
        }

        nextBtn?.addEventListener('click', function () {
            grid.scrollBy({ left: getStep(), behavior: 'smooth' });
        });
        prevBtn?.addEventListener('click', function () {
            grid.scrollBy({ left: -getStep(), behavior: 'smooth' });
        });
        grid.addEventListener('scroll', update, { passive: true });

        return { update: update };
    }

    const groomersSlider = createSlider(
        document.getElementById('fav-groomers-grid'),
        document.getElementById('fav-groomers-prev'),
        document.getElementById('fav-groomers-next')
    );
    const spacesSlider = createSlider(
        document.getElementById('fav-spaces-grid'),
        document.getElementById('fav-spaces-prev'),
        document.getElementById('fav-spaces-next')
    );

    function refreshSliders() {
        groomersSlider.update();
        spacesSlider.update();
    }

    function activePanel() {
        return root.querySelector('[data-fav-panel]:not([hidden])');
    }

    function updateBadge() {
        if (!badge) return;
        const count = root.querySelectorAll('.fav-card').length;
        badge.textContent = String(count);
        badge.hidden = count === 0;
    }

    function applySearch() {
        const panel = activePanel();
        if (!panel) return;

        const q = (searchInput && searchInput.value || '').trim().toLowerCase();
        const cards = panel.querySelectorAll('.fav-card');
        let visible = 0;

        cards.forEach(function (card) {
            const name = (card.getAttribute('data-fav-name') || '').toLowerCase();
            const match = !q || name.indexOf(q) !== -1;
            card.hidden = !match;
            if (match) visible += 1;
        });

        const empty = panel.querySelector('.fav-empty');
        if (empty) empty.hidden = visible > 0;

        const grid = panel.querySelector('.fav-grid');
        if (grid) grid.scrollLeft = 0;
        requestAnimationFrame(refreshSliders);
    }

    function switchFavTab(id) {
        tabs.forEach(function (tab) {
            const on = tab.getAttribute('data-fav-tab') === id;
            tab.classList.toggle('is-active', on);
            tab.setAttribute('aria-selected', on ? 'true' : 'false');
        });

        panels.forEach(function (panel) {
            const on = panel.getAttribute('data-fav-panel') === id;
            panel.hidden = !on;
        });

        if (titleEl) titleEl.textContent = titles[id] || titles.groomers;
        if (searchInput) searchInput.value = '';
        applySearch();
        requestAnimationFrame(refreshSliders);
    }

    function closeConfirm(card) {
        if (!card) return;
        card.classList.remove('is-confirming');
        const confirm = card.querySelector('.fav-card__confirm');
        if (confirm) confirm.hidden = true;
    }

    function openConfirm(card) {
        root.querySelectorAll('.fav-card.is-confirming').forEach(closeConfirm);
        card.classList.add('is-confirming');
        const confirm = card.querySelector('.fav-card__confirm');
        if (confirm) confirm.hidden = false;
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            switchFavTab(tab.getAttribute('data-fav-tab'));
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applySearch);
    }

    root.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('[data-fav-delete]');
        if (deleteBtn) {
            e.preventDefault();
            openConfirm(deleteBtn.closest('.fav-card'));
            return;
        }

        const cancelBtn = e.target.closest('[data-fav-cancel]');
        if (cancelBtn) {
            e.preventDefault();
            closeConfirm(cancelBtn.closest('.fav-card'));
            return;
        }

        const confirmBtn = e.target.closest('[data-fav-confirm-delete]');
        if (confirmBtn) {
            e.preventDefault();
            const card = confirmBtn.closest('.fav-card');
            if (card) card.remove();
            updateBadge();
            applySearch();
            requestAnimationFrame(refreshSliders);
        }
    });

    window.addEventListener('resize', refreshSliders);

    document.querySelectorAll('.account-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            if (tab.getAttribute('data-tab') === 'favourites') {
                requestAnimationFrame(refreshSliders);
            }
        });
    });

    if (favPanel && typeof MutationObserver !== 'undefined') {
        new MutationObserver(function () {
            if (!favPanel.hidden) requestAnimationFrame(refreshSliders);
        }).observe(favPanel, { attributes: true, attributeFilter: ['hidden'] });
    }

    updateBadge();
    requestAnimationFrame(refreshSliders);
})();

