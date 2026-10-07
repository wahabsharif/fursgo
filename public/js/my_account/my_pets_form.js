/**
 * Pet form — Edit details OR Add pet (same UI).
 * Events: pets:edit { card }, pets:add
 */
(function () {
    const listShell = document.getElementById('pets-list-shell');
    const editView = document.getElementById('pets-edit-view');
    const form = document.getElementById('ep-form');
    const activeGrid = document.getElementById('pets-grid');
    const addBtn = document.getElementById('pets-add-btn');
    if (!editView || !form) return;

    const titleEl = document.getElementById('ep-title');
    const saveBtn = document.getElementById('ep-save');
    const petIdInput = document.getElementById('ep-pet-id');
    const nameInput = document.getElementById('ep-name');
    const nameCheck = document.getElementById('ep-name-check');
    const birthdayInput = document.getElementById('ep-birthday');
    const otherTypeWrap = document.getElementById('ep-other-type-wrap');
    const otherTypeInput = document.getElementById('ep-other-type');
    const otherCheck = document.getElementById('ep-other-check');
    const breedSelect = document.getElementById('ep-breed');
    const weightInput = document.getElementById('ep-weight');
    const medicalInput = document.getElementById('ep-medical');
    const personalityInput = document.getElementById('ep-personality');
    const groomingInput = document.getElementById('ep-grooming');
    const notesInput = document.getElementById('ep-notes');
    const avatarImg = document.getElementById('ep-avatar-img');
    const avatarPh = document.getElementById('ep-avatar-ph');
    const avatarInput = document.getElementById('ep-avatar-input');
    const avatarBtn = document.getElementById('ep-avatar-btn');
    const cancelBtn = document.getElementById('ep-cancel');

    const BREEDS = {
        cat: ['British Shorthair', 'Persian', 'Siamese', 'Maine Coon', 'Ragdoll', 'Other'],
        dog: ['Cockapoo', 'French Bulldog', 'Labrador', 'Golden Retriever', 'Poodle', 'Other'],
        other: ['Mini Lop', 'Holland Lop', 'Guinea Pig', 'Hamster', 'Other']
    };

    /** 'edit' | 'add' */
    let mode = 'edit';
    let editingCard = null;

    function setType(value) {
        document.querySelectorAll('#ep-type-group .pet-option').forEach(function (btn) {
            btn.classList.toggle('highlight', btn.getAttribute('data-pet') === value);
        });
    }

    function getType() {
        const active = document.querySelector('#ep-type-group .pet-option.highlight');
        return active ? active.getAttribute('data-pet') : '';
    }

    function setSize(value) {
        document.querySelectorAll('#ep-size-group .weight-option').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-weight') === value);
        });
    }

    function getSize() {
        const active = document.querySelector('#ep-size-group .weight-option.active');
        return active ? active.getAttribute('data-weight') : '';
    }

    function fillBreeds(type, selected) {
        const list = BREEDS[type] || BREEDS.other;
        breedSelect.innerHTML = '<option value="">Select breed</option>';
        list.forEach(function (b) {
            const opt = document.createElement('option');
            opt.value = b;
            opt.textContent = b;
            breedSelect.appendChild(opt);
        });
        if (selected) {
            if (list.indexOf(selected) === -1) {
                const opt = document.createElement('option');
                opt.value = selected;
                opt.textContent = selected;
                breedSelect.appendChild(opt);
            }
            breedSelect.value = selected;
        }
    }

    function setAvatar(src) {
        if (src) {
            avatarImg.src = src;
            avatarImg.hidden = false;
            if (avatarPh) avatarPh.hidden = true;
        } else {
            avatarImg.removeAttribute('src');
            avatarImg.hidden = true;
            if (avatarPh) avatarPh.hidden = false;
        }
    }

    function updateChecks() {
        if (nameCheck) nameCheck.hidden = !(nameInput.value || '').trim();
        if (otherCheck) otherCheck.hidden = !(otherTypeInput.value || '').trim();
    }

    function syncOtherTypeVisibility() {
        const type = getType();
        if (otherTypeWrap) otherTypeWrap.hidden = type !== 'other';
    }

    function resetGallery() {
        editView.querySelectorAll('.ep-gallery-slot').forEach(function (slot) {
            slot.classList.remove('has-photo');
            const img = slot.querySelector('.ep-gallery-preview');
            const input = slot.querySelector('.ep-gallery-input');
            if (img) {
                img.hidden = true;
                img.removeAttribute('src');
            }
            if (input) input.value = '';
        });
    }

    function resetForm() {
        form.reset();
        petIdInput.value = '';
        setAvatar('');
        resetGallery();
        setType('');
        setSize('');
        fillBreeds('dog', '');
        updateChecks();
        syncOtherTypeVisibility();
        editingCard = null;
    }

    function showForm() {
        if (listShell) listShell.hidden = true;
        editView.hidden = false;
        editView.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function closeForm() {
        editView.hidden = true;
        if (listShell) listShell.hidden = false;
        resetForm();
        mode = 'edit';
        document.dispatchEvent(new CustomEvent('pets:form-closed'));
    }

    function readCard(card) {
        const avatar = card.querySelector('.pet-card__avatar');
        return {
            id: card.getAttribute('data-pet-id') || '',
            name: card.getAttribute('data-pet-name') || '',
            type: card.getAttribute('data-pet-type') || 'other',
            otherType: card.getAttribute('data-pet-other-type') || '',
            breed: card.getAttribute('data-pet-breed') || '',
            sex: card.getAttribute('data-pet-sex') || '',
            birthday: card.getAttribute('data-pet-birthday') || '',
            weight: card.getAttribute('data-pet-weight') || '',
            size: card.getAttribute('data-pet-size') || '',
            notes: card.getAttribute('data-pet-notes') || '',
            image: avatar ? avatar.getAttribute('src') : ''
        };
    }

    function readFormValues() {
        return {
            name: (nameInput.value || '').trim(),
            type: getType() || 'other',
            otherType: (otherTypeInput.value || '').trim(),
            breed: breedSelect.value || '',
            sex: (form.querySelector('input[name="ep_sex"]:checked') || {}).value || '',
            birthday: (birthdayInput.value || '').trim(),
            weight: (weightInput.value || '').trim(),
            size: getSize() || '',
            notes: (personalityInput.value || '').trim(),
            image: avatarImg && !avatarImg.hidden ? avatarImg.src : ''
        };
    }

    function populate(data) {
        petIdInput.value = data.id || '';
        nameInput.value = data.name || '';
        birthdayInput.value = data.birthday || '';
        weightInput.value = data.weight || '';
        otherTypeInput.value = data.otherType || '';
        personalityInput.value = data.notes || '';
        medicalInput.value = '';
        groomingInput.value = '';
        notesInput.value = '';

        setType(data.type || '');
        setSize(data.size || '');
        fillBreeds(data.type || 'dog', data.breed || '');
        syncOtherTypeVisibility();

        form.querySelectorAll('input[name="ep_sex"]').forEach(function (r) {
            r.checked = r.value === data.sex;
        });

        setAvatar(data.image || '');
        updateChecks();
    }

    function openEditForm(card) {
        if (!card) return;
        mode = 'edit';
        resetForm();
        editingCard = card;
        populate(readCard(card));

        if (titleEl) {
            const n = card.getAttribute('data-pet-name') || 'pet';
            titleEl.textContent = "Edit " + n + "'s details";
        }
        if (saveBtn) saveBtn.textContent = 'Save changes';
        showForm();
    }

    function openAddForm() {
        mode = 'add';
        resetForm();
        // Sensible empty defaults (match Figma demo accents)
        setType('other');
        setSize('medium');
        fillBreeds('other', '');
        syncOtherTypeVisibility();

        if (titleEl) titleEl.textContent = 'Add Pet';
        if (saveBtn) saveBtn.textContent = 'Add Pet';
        showForm();
    }

    function applyValuesToCard(card, v) {
        const id = card.getAttribute('data-pet-id') || 'pet-' + Date.now();
        card.setAttribute('data-pet-id', id);
        card.setAttribute('data-pet-name', v.name);
        card.setAttribute('data-pet-type', v.type);
        card.setAttribute('data-pet-other-type', v.otherType);
        card.setAttribute('data-pet-breed', v.breed);
        card.setAttribute('data-pet-sex', v.sex);
        card.setAttribute('data-pet-birthday', v.birthday);
        card.setAttribute('data-pet-weight', v.weight);
        card.setAttribute('data-pet-size', v.size);
        card.setAttribute('data-pet-notes', v.notes);
        card.setAttribute('data-pet-pronoun', v.sex === 'male' ? 'he' : 'she');
        card.dataset.mode = 'active';
        card.classList.remove('pet-card--archived', 'is-confirming');

        const nameEl = card.querySelector('.pet-card__name');
        if (nameEl) nameEl.textContent = v.name || 'Pet';

        const species =
            v.type === 'cat' ? 'Cat' : v.type === 'dog' ? 'Dog' : v.otherType || 'Other';
        const breedEl = card.querySelector('.pet-card__breed');
        if (breedEl) breedEl.textContent = species + (v.breed ? ' • ' + v.breed : '');

        const meta = card.querySelectorAll('.pet-card__meta li');
        if (meta[0]) {
            meta[0].lastChild.textContent =
                ' ' + (v.sex === 'male' ? 'Male' : v.sex === 'female' ? 'Female' : '—');
        }
        if (meta[1]) meta[1].lastChild.textContent = ' ' + (v.birthday || '—');
        if (meta[2]) meta[2].lastChild.textContent = ' ' + (v.weight ? v.weight + ' kg' : '—');
        if (meta[3]) meta[3].lastChild.textContent = ' ' + (v.notes || '—');

        const cardAvatar = card.querySelector('.pet-card__avatar');
        if (cardAvatar) {
            if (v.image) cardAvatar.src = v.image;
            cardAvatar.alt = v.name || 'Pet';
        }

        // Reset menus to active mode
        const activeMenu = card.querySelector('.pet-card__menu-active');
        const archivedMenu = card.querySelector('.pet-card__menu-archived');
        if (activeMenu) activeMenu.hidden = false;
        if (archivedMenu) archivedMenu.hidden = true;
        card.querySelectorAll('.pet-card__confirm').forEach(function (box) {
            box.hidden = true;
        });
        const dropdown = card.querySelector('.pet-card__dropdown');
        if (dropdown) dropdown.hidden = true;
    }

    function createPetCard(v) {
        const template =
            (activeGrid && activeGrid.querySelector(':scope > .pet-card')) ||
            document.querySelector('#archived-pets-grid > .pet-card') ||
            document.querySelector('.pet-card');
        if (!template) return null;

        const card = template.cloneNode(true);
        applyValuesToCard(card, v);
        card.setAttribute('data-pet-id', 'pet-' + Date.now());

        const archiveTitle = card.querySelector('.pet-card__confirm--archive h4');
        if (archiveTitle) archiveTitle.textContent = 'Archive ' + (v.name || 'pet') + '?';
        const deleteTitle = card.querySelector('.pet-card__confirm--delete h4');
        if (deleteTitle) {
            deleteTitle.textContent = "Delete " + (v.name || 'pet') + "'s profile?";
        }

        return card;
    }

    function saveForm() {
        const v = readFormValues();
        if (!v.name) {
            nameInput.focus();
            return;
        }

        if (mode === 'edit' && editingCard) {
            applyValuesToCard(editingCard, v);
        } else {
            const card = createPetCard(v);
            if (card && activeGrid) activeGrid.appendChild(card);
            // Make sure My Pets (active) list is visible after adding
            const activeView = document.getElementById('pets-active-view');
            const archivedView = document.getElementById('pets-archived-view');
            if (activeView) activeView.hidden = false;
            if (archivedView) archivedView.hidden = true;
            document.dispatchEvent(new CustomEvent('pets:added'));
        }

        closeForm();
    }

    /* Toggles — same classes as index: .highlight / .active */
    document.getElementById('ep-type-group')?.addEventListener('click', function (e) {
        const btn = e.target.closest('.pet-option');
        if (!btn) return;
        setType(btn.getAttribute('data-pet'));
        fillBreeds(btn.getAttribute('data-pet'), '');
        syncOtherTypeVisibility();
    });

    document.getElementById('ep-size-group')?.addEventListener('click', function (e) {
        const btn = e.target.closest('.weight-option');
        if (!btn) return;
        setSize(btn.getAttribute('data-weight'));
    });

    nameInput?.addEventListener('input', updateChecks);
    otherTypeInput?.addEventListener('input', updateChecks);

    avatarBtn?.addEventListener('click', function () {
        avatarInput?.click();
    });

    avatarInput?.addEventListener('change', function () {
        const file = avatarInput.files && avatarInput.files[0];
        if (!file) return;
        setAvatar(URL.createObjectURL(file));
    });

    editView.querySelectorAll('.ep-gallery-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const file = input.files && input.files[0];
            if (!file) return;
            const slot = input.closest('.ep-gallery-slot');
            const preview = slot && slot.querySelector('.ep-gallery-preview');
            if (!preview) return;
            preview.src = URL.createObjectURL(file);
            preview.hidden = false;
            slot.classList.add('has-photo');
        });
    });

    cancelBtn?.addEventListener('click', closeForm);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        saveForm();
    });

    addBtn?.addEventListener('click', openAddForm);

    document.addEventListener('pets:edit', function (e) {
        openEditForm(e.detail && e.detail.card);
    });

    document.addEventListener('pets:add', openAddForm);
})();

