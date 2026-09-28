/* Extracted from legacy login-signup/owner_account_setup_form.php
 * Re-inits on full load and Livewire wire:navigate (DOMContentLoaded does not re-fire).
 */
(function () {
    'use strict';

    var state = {
        currentStep: 0,
        editMode: false,
        bound: false,
    };

    function qs(sel, root) {
        return (root || document).querySelector(sel);
    }

    function qsa(sel, root) {
        return Array.from((root || document).querySelectorAll(sel));
    }

    function isOwnerSetupPage() {
        return !!qs('#multiForm') || !!qs('.form-outer-div');
    }

    function toggleActive(items, activeClass, checkOther) {
        items.forEach(function (item) {
            if (item.dataset.oasBound === '1') return;
            item.dataset.oasBound = '1';
            item.addEventListener('click', function () {
                items.forEach(function (i) {
                    i.classList.remove(activeClass);
                });
                item.classList.add(activeClass);
                if (checkOther) {
                    handlePetOption(item);
                }
            });
        });
    }

    function handlePetOption(item) {
        var span = item.querySelector('span');
        if (!span) return;
        var spanValue = span.textContent;
        var pet_type = document.getElementById('pet_type');
        var pet_type_label = document.getElementById('pet_type_label');
        var breed = document.getElementById('breed');
        if (!pet_type || !pet_type_label || !breed) return;

        if (spanValue === 'Other') {
            pet_type.style.display = 'block';
            pet_type_label.style.display = 'block';
            breed.style.display = 'none';
            breed.disabled = true;
            pet_type.disabled = false;
        } else {
            pet_type.style.display = 'none';
            pet_type_label.style.display = 'none';
            breed.style.display = 'block';
            breed.disabled = false;
            pet_type.disabled = true;
        }
    }

    function handleInitialPetSelection() {
        var selectedPet = qs('.pet-option.highlight');
        if (selectedPet) {
            handlePetOption(selectedPet);
        }
    }

    function showStep(n) {
        var steps = qsa('.step');
        var stepItems = qsa('.step-item');
        var progressFill = qs('.progress-fill');
        if (!steps.length || !steps[n]) return;

        steps.forEach(function (step) {
            step.classList.remove('active');
        });
        steps[n].classList.add('active');

        stepItems.forEach(function (item, index) {
            item.classList.remove('active', 'inactive');
            item.classList.add(index <= n ? 'active' : 'inactive');
        });

        if (progressFill) {
            progressFill.style.width = ((n + 1) / Math.max(stepItems.length, 1)) * 100 + '%';
        }

        var goBack = document.getElementById('goBack');
        var prevBtn = document.getElementById('prevBtn');
        var nextBtn = document.getElementById('nextBtn');
        var buttonsContainer = document.getElementById('buttonsContainer');
        var sub_heading = qs('.sub-heading');
        var activeLabels = qsa('.step-item.active .step-label');
        var activeLabelElement = activeLabels[activeLabels.length - 1];

        if (activeLabelElement && sub_heading) {
            sub_heading.innerHTML = activeLabelElement.innerHTML;
        }

        if (prevBtn) prevBtn.style.display = n === 0 ? 'none' : 'inline';
        if (goBack) goBack.style.display = n === 0 ? 'none' : 'inline';
        if (nextBtn) nextBtn.innerHTML = n === steps.length - 1 ? 'Save & Continue' : 'Next';

        if (buttonsContainer) {
            buttonsContainer.style.justifyContent = n === 0 ? 'flex-end' : 'space-between';
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function nextPrev(n) {
        var steps = qsa('.step');
        var petListSection = qs('.pet-list-section');
        var formOuterDiv = qs('.form-outer-div');
        if (!steps[state.currentStep]) return;

        var input = steps[state.currentStep].querySelector('input, textarea');
        if (n === 1 && input && !input.checkValidity()) {
            input.reportValidity();
            return;
        }

        if (n === -1 && state.editMode) {
            state.editMode = false;
            if (formOuterDiv) formOuterDiv.classList.add('hidden');
            setTimeout(function () {
                if (formOuterDiv) formOuterDiv.style.display = 'none';
                if (petListSection) {
                    petListSection.style.display = 'block';
                    requestAnimationFrame(function () {
                        petListSection.classList.add('active');
                    });
                }
            }, 180);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        state.currentStep += n;

        if (state.currentStep >= steps.length) {
            state.editMode = false;
            if (formOuterDiv) formOuterDiv.classList.add('hidden');
            setTimeout(function () {
                if (formOuterDiv) formOuterDiv.style.display = 'none';
                if (petListSection) {
                    petListSection.style.display = 'block';
                    requestAnimationFrame(function () {
                        petListSection.classList.add('active');
                    });
                }
            }, 180);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        showStep(state.currentStep);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function changeValue(step) {
        var input = document.getElementById('weightInput');
        if (!input) return;
        var current = parseInt(input.value, 10) || 0;
        var min = input.min ? parseInt(input.min, 10) : -Infinity;
        var newValue = current + step;
        if (newValue >= min) {
            input.value = newValue;
        }
    }

    function handleBackNavigation() {
        var steps = qsa('.step');
        var petListSection = qs('.pet-list-section');
        var formOuterDiv = qs('.form-outer-div');

        if (petListSection && petListSection.classList.contains('active')) {
            petListSection.classList.remove('active');
            setTimeout(function () {
                petListSection.style.display = 'none';
                if (formOuterDiv) {
                    formOuterDiv.style.display = 'block';
                    requestAnimationFrame(function () {
                        formOuterDiv.classList.remove('hidden');
                    });
                }
                state.currentStep = Math.max(steps.length - 1, 0);
                showStep(state.currentStep);
            }, 180);
            return;
        }

        if (state.currentStep > 0) {
            state.currentStep--;
            showStep(state.currentStep);
        }
    }

    function initUpload(inputId, previewId, placeholderId, uploadLabelId, uploadBtnId, editLabelId) {
        var input = document.getElementById(inputId);
        var preview = document.getElementById(previewId);
        var placeholder = document.getElementById(placeholderId);
        var uploadLabel = document.getElementById(uploadLabelId);
        var uploadBtn = document.getElementById(uploadBtnId);
        var editLabel = document.getElementById(editLabelId);
        if (!input || !preview || !placeholder || !uploadLabel || !uploadBtn || !editLabel) return;
        if (input.dataset.oasUpload === '1') return;
        input.dataset.oasUpload = '1';

        editLabel.addEventListener('click', function () {
            input.click();
        });

        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                uploadLabel.style.display = 'none';
                uploadBtn.style.display = 'none';
                editLabel.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    function bindDeleteButtons() {
        qsa('.btn-delete').forEach(function (btn) {
            if (btn.dataset.oasDelete === '1') return;
            btn.dataset.oasDelete = '1';

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                btn.style.display = 'none';

                var card = btn.closest('.pet-card');
                if (!card) return;
                var nameEl = card.querySelector('.pet-name');
                var petName = nameEl ? nameEl.textContent.trim() : 'this pet';

                qsa('.delete-confirm-bar').forEach(function (existingBar) {
                    var prevCard = existingBar.previousElementSibling;
                    if (prevCard && prevCard.classList.contains('pet-card')) {
                        var prevBtn = prevCard.querySelector('.btn-delete');
                        if (prevBtn) prevBtn.style.display = 'block';
                    }
                    existingBar.remove();
                });

                var bar = document.createElement('div');
                bar.className = 'delete-confirm-bar';
                bar.innerHTML =
                    '<div class="delete-confirm-inner d-flex align-items-center justify-content-between">' +
                    '<div class="delete-confirm-msg d-flex align-items-center gap-15">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">' +
                    '<path d="M20.5981 21H2.6817C2.3907 20.9999 2.1047 20.9244 1.85164 20.7807C1.59858 20.6371 1.38712 20.4302 1.23792 20.1804C1.08872 19.9306 1.00688 19.6463 1.00042 19.3554C0.993947 19.0645 1.06306 18.7768 1.20101 18.5206L10.1587 1.88463C10.7942 0.705125 12.4856 0.705125 13.1211 1.88463L22.0788 18.5206C22.2168 18.7768 22.2859 19.0645 22.2794 19.3554C22.2729 19.6463 22.1911 19.9306 22.0419 20.1804C21.8927 20.4302 21.6812 20.6371 21.4282 20.7807C21.1751 20.9244 20.8891 20.9999 20.5981 21Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' +
                    '<path d="M11.6393 14.2268L11.3376 7.81423C11.3361 7.77395 11.3427 7.73379 11.357 7.69611C11.3713 7.65843 11.393 7.62399 11.4209 7.59484C11.4487 7.56569 11.4821 7.54241 11.5191 7.52637C11.556 7.51034 11.5959 7.50187 11.6362 7.50148C11.6772 7.50108 11.7178 7.50905 11.7557 7.5249C11.7935 7.54075 11.8277 7.56415 11.8562 7.59367C11.8847 7.62318 11.9069 7.6582 11.9213 7.69657C11.9358 7.73495 11.9424 7.77588 11.9405 7.81685L11.6393 14.2268Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' +
                    '<path d="M11.6396 18.4248C11.4317 18.4248 11.2285 18.3631 11.0556 18.2476C10.8827 18.1321 10.748 17.9679 10.6684 17.7758C10.5888 17.5837 10.568 17.3723 10.6086 17.1684C10.6491 16.9645 10.7493 16.7772 10.8963 16.6302C11.0433 16.4831 11.2306 16.383 11.4345 16.3425C11.6385 16.3019 11.8498 16.3227 12.0419 16.4023C12.234 16.4819 12.3982 16.6166 12.5137 16.7895C12.6292 16.9623 12.6909 17.1656 12.6909 17.3735C12.6909 17.6523 12.5801 17.9197 12.383 18.1169C12.1858 18.314 11.9184 18.4248 11.6396 18.4248Z" fill="#FF6E6E"/>' +
                    '</svg>' +
                    '<p class="fs-16-600" style="color: #FF6E6E;">Are you sure you want to delete <strong>&nbsp;' +
                    petName +
                    "'s&nbsp;</strong> profile? This action is permanent.</p>" +
                    '</div>' +
                    '<div class="delete-confirm-actions d-flex align-items-center gap-15">' +
                    '<button type="button" class="btn-cancel-delete cursor fs-16-500">Cancel</button>' +
                    '<button type="button" class="btn-confirm-delete fs-16-500">Yes, delete</button>' +
                    '</div>' +
                    '</div>';

                card.insertAdjacentElement('afterend', bar);
                requestAnimationFrame(function () {
                    bar.classList.add('show');
                });

                bar.querySelector('.btn-cancel-delete').addEventListener('click', function () {
                    bar.classList.remove('show');
                    btn.style.display = 'block';
                    setTimeout(function () {
                        bar.remove();
                    }, 300);
                });

                bar.querySelector('.btn-confirm-delete').addEventListener('click', function () {
                    bar.classList.remove('show');
                    card.classList.add('removing');
                    setTimeout(function () {
                        card.remove();
                        bar.remove();
                    }, 300);
                });
            });
        });
    }

    function initOwnerAccountSetup() {
        if (!isOwnerSetupPage()) return;

        state.currentStep = 0;
        state.editMode = false;

        toggleActive(qsa('.pet-option'), 'highlight', true);
        toggleActive(qsa('.weight-option'), 'active', false);
        handleInitialPetSelection();

        var weightInput = document.getElementById('weightInput');
        if (weightInput && weightInput.dataset.oasBound !== '1') {
            weightInput.dataset.oasBound = '1';
            weightInput.addEventListener('input', function () {
                if (weightInput.value.length > 2) {
                    weightInput.value = weightInput.value.slice(0, 2);
                }
            });
        }

        qsa('.groomer-service-button').forEach(function (button) {
            if (button.dataset.oasBound === '1') return;
            button.dataset.oasBound = '1';
            button.addEventListener('click', function () {
                button.classList.toggle('active');
            });
        });

        showStep(state.currentStep);

        var goBackBtn = document.getElementById('goBack');
        var addPetBtn = document.getElementById('add-pet');
        if (goBackBtn && goBackBtn.dataset.oasBound !== '1') {
            goBackBtn.dataset.oasBound = '1';
            goBackBtn.addEventListener('click', handleBackNavigation);
        }
        if (addPetBtn && addPetBtn.dataset.oasBound !== '1') {
            addPetBtn.dataset.oasBound = '1';
            addPetBtn.addEventListener('click', handleBackNavigation);
        }

        qsa('.edit-pet').forEach(function (btn) {
            if (btn.dataset.oasBound === '1') return;
            btn.dataset.oasBound = '1';
            btn.addEventListener('click', function () {
                state.editMode = true;
                var petListSection = qs('.pet-list-section');
                var formOuterDiv = qs('.form-outer-div');
                if (petListSection) petListSection.classList.remove('active');
                setTimeout(function () {
                    if (petListSection) petListSection.style.display = 'none';
                    if (formOuterDiv) {
                        formOuterDiv.style.display = 'block';
                        requestAnimationFrame(function () {
                            formOuterDiv.classList.remove('hidden');
                        });
                    }
                    state.currentStep = 1;
                    showStep(state.currentStep);
                }, 180);
            });
        });

        qsa('.radio-item').forEach(function (item) {
            if (item.dataset.oasBound === '1') return;
            item.dataset.oasBound = '1';
            item.addEventListener('click', function () {
                var radio = item.querySelector('input');
                if (radio) radio.checked = true;
            });
        });

        qsa('.pet-card').forEach(function (card) {
            if (card.dataset.oasBound === '1') return;
            card.dataset.oasBound = '1';
            card.addEventListener('click', function (e) {
                if (e.target.closest('.actions') || e.target.closest('.delete-confirm-bar')) return;
                qsa('.pet-card').forEach(function (c) {
                    c.classList.remove('selected');
                    c.classList.add('alt');
                });
                card.classList.remove('alt');
                card.classList.add('selected');
            });
        });

        initUpload(
            'ownerProfileImage',
            'avatarPreview1',
            'avatarPlaceholder1',
            'uploadLabel1',
            'uploadBtn1',
            'editPhotoLabel1'
        );
        initUpload(
            'petProfileImage',
            'avatarPreview2',
            'avatarPlaceholder2',
            'uploadLabel2',
            'uploadBtn2',
            'editPhotoLabel2'
        );
        bindDeleteButtons();
    }

    // onclick="" handlers in the Blade markup
    window.nextPrev = nextPrev;
    window.showStep = showStep;
    window.changeValue = changeValue;
    window.initOwnerAccountSetup = initOwnerAccountSetup;

    function boot() {
        initOwnerAccountSetup();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    document.addEventListener('livewire:navigated', boot);
})();
