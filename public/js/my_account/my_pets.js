/**
 * My Pets — archive / restore
 *
 * Hard rule: restore/archive moves ONE direct child card by data-pet-id.
 * Nested cards (if any) are pulled out as siblings first so they never travel along.
 */
(function () {
    const panel = document.getElementById('panel-pets');
    if (!panel || panel.dataset.petsReady === '1') return;
    panel.dataset.petsReady = '1';

    const activeView = document.getElementById('pets-active-view');
    const archivedView = document.getElementById('pets-archived-view');
    const activeGrid = document.getElementById('pets-grid');
    const archivedGrid = document.getElementById('archived-pets-grid');
    const emptyMsg = document.getElementById('archived-pets-empty');
    const archivedBtn = document.getElementById('pets-archived-btn');
    const closeArchivedBtn = document.getElementById('pets-archived-close');

    /* ---------- Slider ---------- */
    function createSlider(grid, prevBtn, nextBtn) {
        if (!grid) return { update: function () {} };

        function getStep() {
            const card = grid.querySelector(':scope > .pet-card');
            return card ? card.offsetWidth + 16 : 276;
        }

        function update() {
            const n = grid.querySelectorAll(':scope > .pet-card').length;
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

    const activeSlider = createSlider(
        activeGrid,
        document.getElementById('pets-slider-prev'),
        document.getElementById('pets-slider-next')
    );
    const archivedSlider = createSlider(
        archivedGrid,
        document.getElementById('archived-slider-prev'),
        document.getElementById('archived-slider-next')
    );

    function refreshSliders() {
        activeSlider.update();
        archivedSlider.update();
    }
    window.addEventListener('resize', refreshSliders);

    /* ---------- Views ---------- */
    function showActiveView() {
        if (activeView) activeView.hidden = false;
        if (archivedView) archivedView.hidden = true;
        requestAnimationFrame(refreshSliders);
    }

    function showArchivedView() {
        closeMenus();
        hideConfirms();
        if (activeView) activeView.hidden = true;
        if (archivedView) archivedView.hidden = false;
        updateEmpty();
        requestAnimationFrame(refreshSliders);
    }

    function updateEmpty() {
        const n = archivedGrid
            ? archivedGrid.querySelectorAll(':scope > .pet-card').length
            : 0;
        if (emptyMsg) emptyMsg.hidden = n > 0;
    }

    /* ---------- Card chrome ---------- */
    function closeMenus(except) {
        panel.querySelectorAll('.pet-card__dropdown').forEach(function (m) {
            if (m !== except) m.hidden = true;
        });
    }

    function hideConfirms() {
        panel.querySelectorAll('.pet-card').forEach(function (card) {
            card.classList.remove('is-confirming');
            card.querySelectorAll('.pet-card__confirm').forEach(function (box) {
                box.hidden = true;
            });
        });
    }

    function setMode(card, mode) {
        const activeMenu = card.querySelector('.pet-card__menu-active');
        const archivedMenu = card.querySelector('.pet-card__menu-archived');
        card.dataset.mode = mode;
        card.classList.toggle('pet-card--archived', mode === 'archived');
        if (activeMenu) activeMenu.hidden = mode === 'archived';
        if (archivedMenu) archivedMenu.hidden = mode !== 'archived';
    }

    function fillRestoreCopy(card) {
        const name = card.dataset.petName || 'this pet';
        const title = card.querySelector('[data-restore-title]');
        const copy = card.querySelector('[data-restore-copy]');
        if (title) title.textContent = 'Restore ' + name + '?';
        if (copy) {
            copy.textContent =
                name +
                "'s profile will be visible to groomers again and can be added to new bookings.";
        }
    }

    function showConfirm(card, kind) {
        closeMenus();
        hideConfirms();
        card.classList.add('is-confirming');
        const sel = {
            archive: '.pet-card__confirm--archive',
            delete: '.pet-card__confirm--delete',
            restore: '.pet-card__confirm--restore'
        }[kind];
        const box = card.querySelector(sel);
        if (box) box.hidden = false;
        if (kind === 'restore') fillRestoreCopy(card);
    }

    /**
     * If a card somehow contains other pet-cards, pull them out as siblings
     * so moving one card never drags the others along.
     */
    function ejectNestedCards(card, parkGrid, parkMode) {
        if (!card || !parkGrid) return;
        const nested = card.querySelectorAll('.pet-card');
        nested.forEach(function (child) {
            setMode(child, parkMode);
            parkGrid.appendChild(child);
        });
    }

    /** Move exactly one direct-child card by id between grids */
    function moveById(petId, fromGrid, toGrid, mode) {
        if (!petId || !fromGrid || !toGrid) return false;

        // Safety: flatten any accidental nesting in both grids
        fromGrid.querySelectorAll(':scope > .pet-card').forEach(function (c) {
            ejectNestedCards(c, fromGrid, fromGrid === archivedGrid ? 'archived' : 'active');
        });

        const card = fromGrid.querySelector(
            ':scope > .pet-card[data-pet-id="' + petId + '"]'
        );
        if (!card) return false;

        // Eject anything nested inside THIS card before moving it
        ejectNestedCards(
            card,
            fromGrid,
            fromGrid === archivedGrid ? 'archived' : 'active'
        );

        closeMenus();
        hideConfirms();
        setMode(card, mode);
        toGrid.appendChild(card);
        updateEmpty();
        requestAnimationFrame(refreshSliders);
        return true;
    }

    function archiveById(petId) {
        moveById(petId, activeGrid, archivedGrid, 'archived');
    }

    function restoreById(petId) {
        const ok = moveById(petId, archivedGrid, activeGrid, 'active');
        if (ok) {
            // Stay on Archived so you can see only that one left the list
            showArchivedView();
        }
    }

    function deleteById(petId) {
        const card =
            panel.querySelector('.pet-card[data-pet-id="' + petId + '"]');
        if (!card) return;
        ejectNestedCards(
            card,
            card.parentElement === archivedGrid ? archivedGrid : activeGrid,
            card.parentElement === archivedGrid ? 'archived' : 'active'
        );
        card.remove();
        hideConfirms();
        updateEmpty();
        requestAnimationFrame(refreshSliders);
    }

    /* ---------- Clicks: read pet id from the card that owns the button ---------- */
    let locked = false;

    panel.addEventListener('click', function (e) {
        const control = e.target.closest('[data-pets-action]');
        if (!control || !panel.contains(control)) return;

        const action = control.getAttribute('data-pets-action');
        const card = control.closest('.pet-card');
        if (!action || !card || !panel.contains(card)) return;

        const petId = card.getAttribute('data-pet-id');
        if (!petId) return;

        e.preventDefault();
        e.stopPropagation();

        if (action.indexOf('confirm-') === 0) {
            if (locked) return;
            locked = true;
            window.setTimeout(function () {
                locked = false;
            }, 500);
        }

        if (action === 'menu') {
            const dropdown = card.querySelector('.pet-card__dropdown');
            const open = dropdown && dropdown.hidden;
            closeMenus(dropdown);
            if (dropdown) dropdown.hidden = !open;
            return;
        }

        if (action === 'archive') {
            showConfirm(card, 'archive');
            return;
        }
        if (action === 'restore') {
            showConfirm(card, 'restore');
            return;
        }
        if (action === 'delete') {
            showConfirm(card, 'delete');
            return;
        }
        if (action === 'edit') {
            closeMenus();
            document.dispatchEvent(
                new CustomEvent('pets:edit', { detail: { card: card } })
            );
            return;
        }
        if (action === 'cancel') {
            hideConfirms();
            return;
        }

        // Confirms — always by id against the correct grid
        if (action === 'confirm-archive') {
            archiveById(petId);
            return;
        }
        if (action === 'confirm-restore') {
            restoreById(petId);
            return;
        }
        if (action === 'confirm-delete') {
            deleteById(petId);
        }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.pet-card__menu')) closeMenus();
    });

    archivedBtn?.addEventListener('click', showArchivedView);
    closeArchivedBtn?.addEventListener('click', showActiveView);

    document.querySelectorAll('.account-tab[data-tab="pets"]').forEach(function (tab) {
        tab.addEventListener('click', function () {
            requestAnimationFrame(refreshSliders);
        });
    });

    document.addEventListener('pets:added', function () {
        requestAnimationFrame(refreshSliders);
    });

    document.addEventListener('pets:form-closed', function () {
        requestAnimationFrame(refreshSliders);
    });

    refreshSliders();
    updateEmpty();
})();

