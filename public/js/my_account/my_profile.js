(function () {
    const tabs = document.querySelectorAll('.account-tab');
    const panels = document.querySelectorAll('.account-panel');

    const viewEl = document.getElementById('mp-view');
    const editEl = document.getElementById('mp-edit');
    const editBtn = document.getElementById('mp-edit-btn');
    const cancelBtn = document.getElementById('mp-cancel-btn');
    const form = document.getElementById('mp-form');
    const uploadBtn = document.getElementById('mp-upload-btn');
    const photoInput = document.getElementById('mp-photo-input');
    const viewAvatarImg = document.getElementById('mp-view-avatar-img');
    const editAvatarImg = document.getElementById('mp-edit-avatar-img');
    const cardAvatarImg = document.querySelector('.profile-card__avatar img');

    function switchTab(id) {
        tabs.forEach(function (t) {
            t.classList.toggle('is-active', t.getAttribute('data-tab') === id);
        });

        panels.forEach(function (panel) {
            const match = panel.getAttribute('data-panel') === id;
            panel.classList.toggle('is-active', match);
            panel.hidden = !match;
        });
    }

    function showView() {
        if (!viewEl || !editEl) return;
        viewEl.hidden = false;
        editEl.hidden = true;
        if (editBtn) editBtn.hidden = false;
    }

    function showEdit() {
        if (!viewEl || !editEl) return;
        viewEl.hidden = true;
        editEl.hidden = false;
        if (editBtn) editBtn.hidden = true;
    }

    function openProfileEdit() {
        switchTab('profile');
        showEdit();
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const id = tab.getAttribute('data-tab');
            switchTab(id);
            if (id === 'profile') {
                showView();
            }
        });
    });

    if (editBtn) {
        editBtn.addEventListener('click', showEdit);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', showView);
    }

    document.querySelectorAll('[data-open-profile-edit]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            openProfileEdit();
        });
    });

    if (uploadBtn && photoInput) {
        uploadBtn.addEventListener('click', function () {
            photoInput.click();
        });

        photoInput.addEventListener('change', function () {
            const file = photoInput.files && photoInput.files[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            if (editAvatarImg) editAvatarImg.src = url;
            if (viewAvatarImg) viewAvatarImg.src = url;
            if (cardAvatarImg) cardAvatarImg.src = url;
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const first = document.getElementById('mp-first-name').value.trim();
            const last = document.getElementById('mp-last-name').value.trim();
            const email = document.getElementById('mp-email').value.trim();
            const phone = document.getElementById('mp-phone').value.trim();
            const address = document.getElementById('mp-address').value.trim();
            const city = document.getElementById('mp-city').value.trim();
            const postcode = document.getElementById('mp-postcode').value.trim();
            const about = document.getElementById('mp-about').value.trim();

            const fullName = [first, last].filter(Boolean).join(' ');
            const fullAddress = [address, city, postcode].filter(Boolean).join(', ');

            const nameEl = document.querySelector('.profile-card__name');
            if (nameEl && fullName) nameEl.textContent = fullName;

            const emailEl = document.getElementById('mp-display-email');
            const phoneEl = document.getElementById('mp-display-phone');
            const addressEl = document.getElementById('mp-display-address');
            const aboutEl = document.getElementById('mp-display-about');

            if (emailEl) emailEl.textContent = email;
            if (phoneEl) phoneEl.textContent = phone;
            if (addressEl) addressEl.textContent = fullAddress;
            if (aboutEl) aboutEl.textContent = about;

            showView();
        });
    }

})();

