(function () {
  "use strict";

  const STEPS = ["Pet", "Service", "Extras", "Review", "Pay"];
  const SERVICE_PRICE = 60;

  let currentStep = 0;
  let petSubView = "choice";
  let selectedPetId = null;
  let editingPetId = null;
  let newPetData = null;
  let selectedService = { name: "Half-day", price: SERVICE_PRICE };
  let petBreedsData = {};
  let addressMode = "home"; // 'home' | 'different'
  let addressEditing = false;
  let appliedPromos = [];
  let promoDiscount = 0;
  let selectedPayMethod = null;
  let selectedExtraIds = ["1"];

  const SPACE_EXTRAS = {
    "1": { id: "1", name: "Storage Locker", price: 5, unit: "/ day" },
    "2": { id: "2", name: "Storage Locker", price: 20, unit: "" },
    "3": { id: "3", name: "Storage Locker", price: 10, unit: "" },
  };

  const VALID_PROMOS = {
    PROMO25: 3,
    PROMO22: 5,
    PROMO23: 1,
    PROMO26: 1,
  };

  const els = {
    stepItems: document.querySelectorAll(".cbg-step-item"),
    stepPanels: document.querySelectorAll(".cbg-step-panel"),
    continueWraps: {
      addNew: document.getElementById("cbgContinueAddNew"),
      selectExisting: document.getElementById("cbgContinueSelectExisting"),
      service: document.getElementById("cbgContinueService"),
      extras: document.getElementById("cbgContinueExtras"),
    },
    subPanels: {
      choice: document.getElementById("cbgPetChoice"),
      addNew: document.getElementById("cbgPetAddNew"),
      selectExisting: document.getElementById("cbgPetSelectExisting"),
    },
    summaryService: document.getElementById("cbgSummaryService"),
    summaryServiceName: document.getElementById("cbgSummaryServiceName"),
    summaryServiceItemPrice: document.getElementById("cbgSummaryServiceItemPrice"),
    summaryExtras: document.getElementById("cbgSummaryExtras"),
    summaryExtrasBadge: document.getElementById("cbgSummaryExtrasBadge"),
    summaryExtrasList: document.getElementById("cbgSummaryExtrasList"),
    summaryExtrasAccordion: document.getElementById("cbgSummaryExtrasAccordion"),
    summaryPromos: document.getElementById("cbgSummaryPromos"),
    summaryPromoDivider: document.getElementById("cbgSummaryPromoDivider"),
    summaryTotal: document.getElementById("cbgSummaryTotal"),
  };

  function getSelectedAddons() {
    return selectedExtraIds
      .map((id) => SPACE_EXTRAS[id])
      .filter(Boolean);
  }

  function getAddonsTotal() {
    return getSelectedAddons().reduce((sum, extra) => sum + extra.price, 0);
  }

  function getAddonsCount() {
    return selectedExtraIds.length;
  }

  function updateSummary() {
    const extrasTotal = getAddonsTotal();
    const extrasCount = getAddonsCount();
    const servicePriceText = "£" + selectedService.price.toFixed(2);
    const total = Math.max(
      0,
      selectedService.price + extrasTotal - promoDiscount,
    );

    if (els.summaryService) {
      els.summaryService.textContent = servicePriceText;
    }
    if (els.summaryServiceName) {
      els.summaryServiceName.textContent = selectedService.name;
    }
    if (els.summaryServiceItemPrice) {
      els.summaryServiceItemPrice.textContent = servicePriceText;
    }
    if (els.summaryExtrasAccordion) {
      const showExtras = extrasCount > 0;
      const divider = els.summaryExtrasAccordion.previousElementSibling;
      els.summaryExtrasAccordion.style.display = showExtras ? "" : "none";
      if (divider?.classList.contains("cbg-summary-divider")) {
        divider.style.display = showExtras ? "" : "none";
      }
    }
    if (els.summaryExtrasBadge) {
      els.summaryExtrasBadge.textContent = String(extrasCount);
      els.summaryExtrasBadge.style.display = extrasCount > 0 ? "" : "none";
    }
    if (els.summaryExtras) {
      els.summaryExtras.textContent = "£" + extrasTotal.toFixed(2);
    }
    if (els.summaryExtrasList) {
      els.summaryExtrasList.innerHTML = getSelectedAddons()
        .map((addon) => {
          const label = addon.unit
            ? `${addon.name} ${addon.unit}`
            : addon.name;
          return `<li><span>${label}</span><span>£${addon.price.toFixed(2)}</span></li>`;
        })
        .join("");
    }

    const hasPromo = appliedPromos.length > 0;
    if (els.summaryPromoDivider) els.summaryPromoDivider.hidden = !hasPromo;
    if (els.summaryPromos) {
      els.summaryPromos.hidden = !hasPromo;
      els.summaryPromos.innerHTML = appliedPromos
        .map((code) => {
          const amount = VALID_PROMOS[code] || 0;
          return (
            `<div class="cbg-summary-line cbg-summary-promo">` +
            `<span>Promo (${escapeHtml(code)})</span>` +
            `<span class="cbg-promo-discount">-£${amount.toFixed(2)}</span>` +
            `</div>`
          );
        })
        .join("");
    }

    if (els.summaryTotal) {
      els.summaryTotal.textContent = "£" + total.toFixed(2);
    }

    updateExtrasFooter();
    updateReviewPricing();
    if (currentStep === 4) updatePayStep();
  }

  function updateExtrasFooter() {
    const count = getAddonsCount();
    const total = getAddonsTotal();
    const labelEl = document.getElementById("cbgExtrasSummaryLabel");
    const totalEl = document.getElementById("cbgExtrasSummaryTotal");

    if (labelEl) {
      labelEl.textContent = count + " Extra's & Add-ons selected";
    }
    if (totalEl) {
      totalEl.textContent = "£" + total.toFixed(2);
    }
  }

  function updateSpaceExtrasUI() {
    const tagsEl = document.getElementById("cbsExtrasTags");
    const addons = getSelectedAddons();

    document.querySelectorAll(".cbs-extra-option").forEach((btn) => {
      btn.classList.toggle(
        "selected",
        selectedExtraIds.includes(btn.dataset.extraId),
      );
    });

    if (tagsEl) {
      if (addons.length) {
        tagsEl.hidden = false;
        tagsEl.innerHTML = addons
          .map((extra) => {
            const label = extra.unit
              ? extra.name + " " + extra.unit
              : extra.name + " (£" + extra.price + ")";
            return (
              '<span class="cbs-extras-tag" data-extra-id="' +
              escapeHtml(extra.id) +
              '">' +
              escapeHtml(label) +
              '<button type="button" class="cbs-extras-tag-remove" data-remove-extra="' +
              escapeHtml(extra.id) +
              '" aria-label="Remove ' +
              escapeHtml(extra.name) +
              '">&times;</button></span>'
            );
          })
          .join("");
      } else {
        tagsEl.hidden = true;
        tagsEl.innerHTML = "";
      }
    }

    updateExtrasFooter();
    updateSummary();
  }

  function removeSelectedExtra(id) {
    selectedExtraIds = selectedExtraIds.filter((extraId) => extraId !== id);
    updateSpaceExtrasUI();
  }

  function toggleExtra(id) {
    if (!id || !SPACE_EXTRAS[id]) return;
    if (selectedExtraIds.includes(id)) {
      selectedExtraIds = selectedExtraIds.filter((extraId) => extraId !== id);
    } else {
      selectedExtraIds = selectedExtraIds.concat(id);
    }
    updateSpaceExtrasUI();
  }

  function setupSpaceExtras() {
    document.querySelectorAll(".cbs-extra-option").forEach((btn) => {
      btn.addEventListener("click", () => {
        toggleExtra(btn.dataset.extraId);
      });
    });
    document
      .getElementById("cbsExtrasTags")
      ?.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-remove-extra]");
        if (!btn) return;
        e.stopPropagation();
        removeSelectedExtra(btn.getAttribute("data-remove-extra"));
      });
    updateSpaceExtrasUI();
  }

  function showStep(stepIndex) {
    currentStep = stepIndex;

    els.stepPanels.forEach((panel, i) => {
      panel.classList.toggle("active", i === stepIndex);
    });

    els.stepItems.forEach((item, i) => {
      item.classList.remove("active", "completed");
      if (i < stepIndex) item.classList.add("completed");
      if (i === stepIndex) item.classList.add("active");

      const circle = item.querySelector(".cbg-step-circle");
      if (circle) circle.textContent = i < stepIndex ? "✓" : String(i + 1);
    });

    updateContinueState();

    if (stepIndex === 3) populateReview();
    if (stepIndex === 4) {
      updateSummary();
      updatePayStep();
      positionPayFooter();
    }

    window.scrollTo({ top: 0, behavior: "smooth" });
  }

  function showPetSubView(view) {
    petSubView = view;
    Object.keys(els.subPanels).forEach((key) => {
      if (els.subPanels[key]) {
        els.subPanels[key].classList.toggle("active", key === view);
      }
    });
    updateContinueState();
    window.scrollTo({ top: 0, behavior: "smooth" });
  }

  function getSelectedPetInfo() {
    if (newPetData) return newPetData;
    if (selectedPetId == null) return null;
    const card = document.querySelector(
      '.cbg-pet-card[data-pet-id="' + selectedPetId + '"]',
    );
    if (!card) return null;
    return {
      name: card.dataset.name,
      type: card.dataset.type,
      breed: card.dataset.breed,
      birthday: card.dataset.birthday,
      sex: card.dataset.sex,
      notes: card.dataset.notes,
    };
  }

  function getSelectedPetType() {
    const btn = document.querySelector("#cbgPetAddNew .pet-option.highlight");
    if (!btn) return "";
    const map = { cat: "Cat", dog: "Dog", other: "Other" };
    return map[btn.dataset.pet] || "";
  }

  function validateNewPetForm(showErrors) {
    const name = document.getElementById("cbgPetName");
    const birthday = document.querySelector(
      '#cbgPetAddNew input[name="birthday"]',
    );
    const breed = document.getElementById("cbgPetBreed");
    const sex = document.querySelector(
      '#cbgPetAddNew input[name="sex"]:checked',
    );
    const weight = document.getElementById("cbgPetWeight");
    const petType = getSelectedPetType();

    const valid = !!(
      name &&
      name.value.trim() &&
      birthday &&
      birthday.value &&
      petType &&
      breed &&
      breed.value &&
      sex &&
      weight &&
      weight.value
    );

    if (showErrors && !valid) {
      alert("Please fill in all required pet details before continuing.");
    }
    return valid;
  }

  function setContinueState(wrap, canContinue) {
    if (!wrap) return;
    const btn = wrap.querySelector("button");
    wrap.classList.toggle("active", canContinue);
    if (btn) btn.disabled = !canContinue;
  }

  function isServiceStepValid() {
    return true;
  }

  function updateContinueState() {
    setContinueState(
      els.continueWraps.addNew,
      petSubView === "addNew" && validateNewPetForm(false),
    );
    setContinueState(
      els.continueWraps.selectExisting,
      petSubView === "selectExisting" && selectedPetId != null,
    );
    setContinueState(
      els.continueWraps.service,
      currentStep === 1 && isServiceStepValid(),
    );
    setContinueState(els.continueWraps.extras, currentStep === 2);
    updateConfirmPayState();
  }

  function updateConfirmPayState() {
    const btn = document.getElementById("cbgConfirmPayBtn");
    const terms = document.getElementById("cbgReviewTerms");
    if (!btn) return;
    const enabled = currentStep === 3 && terms?.checked;
    btn.disabled = !enabled;
    btn.classList.toggle("active", enabled);
  }

  function saveNewPetData() {
    const name = document.getElementById("cbgPetName").value.trim();
    const birthday = document.querySelector(
      '#cbgPetAddNew input[name="birthday"]',
    ).value;
    let type = getSelectedPetType();
    const breed = document.getElementById("cbgPetBreed").value;
    const sex =
      document.querySelector('#cbgPetAddNew input[name="sex"]:checked')
        ?.value || "";
    const weight = document.getElementById("cbgPetWeight").value;
    const notes = document.getElementById("cbgPetNotes").value.trim();
    const image = document.getElementById("cbgPetPhotoPreview")?.src || "";

    if (editingPetId != null && type === "Other") {
      const card = document.querySelector(
        '.cbg-pet-card[data-pet-id="' + editingPetId + '"]',
      );
      const originalType = card?.dataset.type || "";
      if (originalType && !/^(cat|dog)$/i.test(originalType)) {
        type = originalType;
      }
    }

    newPetData = { name, birthday, type, breed, sex, weight, notes, image };

    if (editingPetId != null) {
      syncEditedPetCard(editingPetId, newPetData);
      selectedPetId = editingPetId;
    } else {
      selectedPetId = null;
    }
    editingPetId = null;
  }

  function formatBirthdayDisplay(value) {
    if (!value) return "—";
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(value)) return value;
    const iso = parseBirthdayToISO(value);
    if (!iso) return value;
    const parts = iso.split("-");
    return parts[2] + "/" + parts[1] + "/" + parts[0];
  }

  function syncEditedPetCard(petId, data) {
    const card = document.querySelector(
      '.cbg-pet-card[data-pet-id="' + petId + '"]',
    );
    if (!card || !data) return;

    const birthdayDisplay = formatBirthdayDisplay(data.birthday);
    card.dataset.name = data.name || "";
    card.dataset.type = data.type || "";
    card.dataset.breed = data.breed || "";
    card.dataset.birthday = birthdayDisplay;
    card.dataset.sex = data.sex || "";
    card.dataset.weight = data.weight || "4";
    card.dataset.notes = data.notes || "";
    if (data.image) card.dataset.image = data.image;

    const avatar = card.querySelector(".cbg-pet-avatar img");
    if (avatar && data.image) {
      avatar.src = data.image;
      avatar.alt = data.name || "";
    }

    const cols = card.querySelectorAll(".cbg-pet-col");
    if (cols[0]) {
      const nameLabel = cols[0].querySelector(".cbg-pet-col-label");
      const typeValue = cols[0].querySelector(".cbg-pet-col-value");
      if (nameLabel) {
        const icon = nameLabel.querySelector("svg");
        nameLabel.textContent = "";
        if (icon) nameLabel.appendChild(icon);
        nameLabel.append(" " + (data.name || ""));
      }
      if (typeValue) {
        typeValue.textContent =
          (data.type || "") + " • " + (data.breed || "");
      }
    }
    if (cols[1]) {
      const birthdayValue = cols[1].querySelector(".cbg-pet-col-value");
      if (birthdayValue) birthdayValue.textContent = birthdayDisplay;
    }
    if (cols[2]) {
      const sexValue = cols[2].querySelector(".cbg-pet-col-value");
      if (sexValue) sexValue.textContent = data.sex || "";
    }
    if (cols[3]) {
      const notesValue = cols[3].querySelector(".cbg-pet-col-value");
      if (notesValue) {
        notesValue.textContent = data.notes || "—";
        notesValue.title = data.notes || "";
      }
    }
  }

  function goForward(context) {
    if (currentStep === 0) {
      if (context === "addNew") {
        if (!validateNewPetForm(true)) return;
        saveNewPetData();
      } else if (context === "selectExisting") {
        if (selectedPetId == null) return;
        newPetData = null;
      } else {
        return;
      }
    }

    if (currentStep >= STEPS.length - 1) return;
    showStep(currentStep + 1);
  }

  function populateReview() {
    const pet = getSelectedPetInfo();
    const addons = getSelectedAddons();

    document.getElementById("cbgReviewPetName").textContent = pet?.name || "—";
    document.getElementById("cbgReviewPetType").textContent = pet?.type || "—";
    document.getElementById("cbgReviewPetBreed").textContent =
      pet?.breed || "—";
    document.getElementById("cbgReviewServiceName").textContent =
      "Garden / Shed";

    const extrasList = document.getElementById("cbgReviewExtrasList");
    const extrasCard = document.getElementById("cbgReviewExtrasCard");
    if (extrasList) {
      extrasList.innerHTML = "";
      if (addons.length) {
        addons.forEach((a) => {
          const row = document.createElement("div");
          row.className = "cbg-review-extra-row";
          row.innerHTML =
            "<span>" +
            escapeHtml(a.name) +
            "</span><span>£" +
            a.price.toFixed(2) +
            "</span>";
          extrasList.appendChild(row);
        });
        extrasCard?.classList.remove("is-hidden");
      } else {
        extrasCard?.classList.add("is-hidden");
      }
    }

    updateReviewPricing();
    updateConfirmPayState();
  }

  function updateReviewPricing() {
    const extrasTotal = getAddonsTotal();
    const total = Math.max(
      0,
      selectedService.price + extrasTotal - promoDiscount,
    );

    const serviceEl = document.getElementById("cbgReviewServicePrice");
    const extrasEl = document.getElementById("cbgReviewExtrasPrice");
    const extrasLine = document.getElementById("cbgReviewExtrasLine");
    const promoLine = document.getElementById("cbgReviewPromoLine");
    const promoLabel = document.getElementById("cbgReviewPromoLabel");
    const promoAmount = document.getElementById("cbgReviewPromoAmount");
    const totalEl = document.getElementById("cbgReviewTotal");

    if (serviceEl)
      serviceEl.textContent = "£" + selectedService.price.toFixed(2);
    if (extrasEl) extrasEl.textContent = "£" + extrasTotal.toFixed(2);
    if (extrasLine)
      extrasLine.style.display = extrasTotal > 0 ? "flex" : "none";
    if (promoLine) promoLine.hidden = appliedPromos.length === 0;
    if (promoLabel && appliedPromos.length)
      promoLabel.textContent = "Promo (" + appliedPromos.join(", ") + ")";
    if (promoAmount) promoAmount.textContent = "-£" + promoDiscount.toFixed(2);
    if (totalEl) totalEl.textContent = "£" + total.toFixed(2);
  }

  function setPromoError(show) {
    const input = document.getElementById("cbgPromoInput");
    const errorEl = document.getElementById("cbgPromoError");
    if (errorEl) errorEl.hidden = !show;
    input?.classList.toggle("is-error", show);
  }

  function getPromoDiscountTotal() {
    return appliedPromos.reduce(
      (sum, code) => sum + (VALID_PROMOS[code] || 0),
      0,
    );
  }

  function updatePromoUI() {
    const hasPromo = appliedPromos.length > 0;
    const reviewApplied = document.getElementById("cbgReviewPromoApplied");

    if (reviewApplied) {
      reviewApplied.hidden = !hasPromo;
      reviewApplied.innerHTML = appliedPromos
        .map(
          (code) =>
            `<div class="cbg-promo-pill" data-promo-code="${escapeHtml(code)}">` +
            `<span>${escapeHtml(code)}</span>` +
            `<button type="button" class="cbg-promo-pill-remove" data-remove-promo="${escapeHtml(code)}" aria-label="Remove promo code ${escapeHtml(code)}">` +
            `<svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 8 8" fill="none" aria-hidden="true">` +
            `<path d="M0.5 7.5L7.5 0.5M0.5 0.5L7.5 7.5" stroke="#9D9B98" stroke-linecap="round" />` +
            `</svg></button></div>`,
        )
        .join("");
    }

    const sidebarPromo = document.getElementById("cbgSidebarPromo");
    if (sidebarPromo) sidebarPromo.hidden = !hasPromo;

    const sidebarCode = document.getElementById("cbgSidebarPromoCode");
    if (sidebarCode) sidebarCode.textContent = appliedPromos.join(", ");
  }

  function applyPromoCode() {
    const input = document.getElementById("cbgPromoInput");
    const code = (input?.value || "").trim().toUpperCase();
    const discount = VALID_PROMOS[code];

    if (!code) {
      setPromoError(false);
      return;
    }

    if (!discount) {
      setPromoError(true);
      return;
    }

    if (appliedPromos.includes(code)) {
      setPromoError(false);
      if (input) input.value = "";
      return;
    }

    appliedPromos.push(code);
    promoDiscount = getPromoDiscountTotal();
    setPromoError(false);
    if (input) input.value = "";

    updatePromoUI();
    updateSummary();
  }

  function removePromoCode(code) {
    if (code) {
      appliedPromos = appliedPromos.filter((c) => c !== code);
    } else {
      appliedPromos = [];
    }
    promoDiscount = getPromoDiscountTotal();

    setPromoError(false);
    updatePromoUI();
    updateSummary();
  }

  function getPayableTotal() {
    return Math.max(
      0,
      selectedService.price + getAddonsTotal() - promoDiscount,
    );
  }

  function updatePayStep() {
    const total = getPayableTotal();
    const totalEl = document.getElementById("cbgPayTotalAmount");
    const payLabel = document.getElementById("cbgPayBtnLabel");

    if (totalEl) totalEl.textContent = "£" + total.toFixed(2);
    if (payLabel) payLabel.textContent = "Pay £" + total.toFixed(2);

    updatePayFieldValidation();
    updatePayButtonState();
  }

  function updatePayFieldValidation() {
    const fields = {
      firstName: document.getElementById("cbgPayFirstName"),
      lastName: document.getElementById("cbgPayLastName"),
      cardNumber: document.getElementById("cbgPayCardNumber"),
      expiry: document.getElementById("cbgPayExpiry"),
      cvv: document.getElementById("cbgPayCvv"),
      city: document.getElementById("cbgPayCity"),
      postcode: document.getElementById("cbgPayPostcode"),
    };

    Object.entries(fields).forEach(([key, input]) => {
      const wrap = document.querySelector('[data-pay-field="' + key + '"]');
      if (!wrap || !input) return;
      const valid = !!input.value.trim();
      wrap.classList.toggle("valid", valid);
    });
  }

  function isCardFormValid() {
    const ids = [
      "cbgPayFirstName",
      "cbgPayLastName",
      "cbgPayCardNumber",
      "cbgPayExpiry",
      "cbgPayCvv",
      "cbgPayCity",
      "cbgPayPostcode",
    ];
    return ids.every((id) => {
      const el = document.getElementById(id);
      return el && el.value.trim().length > 0;
    });
  }

  function updatePayHeading() {
    const heading = document.getElementById("cbgPayHeading");
    if (!heading) return;
    heading.textContent = selectedPayMethod
      ? "Confirm & Pay"
      : "Select Payment Method";
  }

  function updatePayButtonState() {
    const btn = document.getElementById("cbgPayBtn");
    if (!btn || currentStep !== 4) return;

    const canPay =
      !selectedPayMethod
        ? false
        : selectedPayMethod === "card"
          ? isCardFormValid()
          : true;
    btn.disabled = !canPay;
    btn.classList.toggle("active", canPay);
  }

  function scrollActivePayMethodIntoView() {
    if (currentStep !== 4) return;

    const activeMethod = document.querySelector(".cbg-pay-method.active");
    const head = activeMethod?.querySelector(".cbg-pay-method-head");
    if (!head) return;

    const stickyOffset = 100;
    const rect = head.getBoundingClientRect();
    const targetY = window.scrollY + rect.top - stickyOffset;

    window.scrollTo({
      top: Math.max(0, targetY),
      behavior: "smooth",
    });
  }

  function selectPayMethod(method) {
    const isSame = selectedPayMethod === method;
    selectedPayMethod = method;

    document.querySelectorAll(".cbg-pay-method").forEach((el) => {
      el.classList.toggle("active", el.dataset.payMethod === method);
    });

    updatePayHeading();
    positionPayFooter();
    updatePayButtonState();

    if (!isSame) {
      requestAnimationFrame(() => {
        requestAnimationFrame(scrollActivePayMethodIntoView);
      });
    }
  }

  function positionPayFooter() {
    const footer = document.getElementById("cbgPayFooter");
    const methods = document.getElementById("cbgPayMethods");
    const activeMethod = document.querySelector(".cbg-pay-method.active");
    if (!footer) return;

    if (activeMethod) {
      activeMethod.insertAdjacentElement("afterend", footer);
    } else if (methods) {
      methods.appendChild(footer);
    }
  }

  function setupPayStep() {
    document.querySelectorAll(".cbg-pay-method-head").forEach((head) => {
      head.addEventListener("click", (e) => {
        const method = head.closest(".cbg-pay-method")?.dataset.payMethod;
        if (!method) return;
        head.blur();
        selectPayMethod(method);
      });
    });

    document
      .getElementById("cbgPayBackBtn")
      ?.addEventListener("click", () => showStep(3));

    document.getElementById("cbgPayBtn")?.addEventListener("click", () => {
      const btn = document.getElementById("cbgPayBtn");
      const baseUrl = document.body.dataset.baseUrl || "";

      if (btn && !btn.disabled) {
        // alert('Payment processing demo — booking confirmed!');
        window.location.href =
          baseUrl + "booking-space/confirmation.php";
      }
    });

    [
      "cbgPayFirstName",
      "cbgPayLastName",
      "cbgPayCardNumber",
      "cbgPayExpiry",
      "cbgPayCvv",
      "cbgPayCity",
      "cbgPayPostcode",
    ].forEach((id) => {
      document.getElementById(id)?.addEventListener("input", () => {
        updatePayFieldValidation();
        updatePayButtonState();
      });
    });

    positionPayFooter();
  }

  function escapeHtml(str) {
    if (!str) return "";
    const div = document.createElement("div");
    div.textContent = str;
    return div.innerHTML;
  }

  function populateBreeds(petType, preferredBreed) {
    const select = document.getElementById("cbgPetBreed");
    if (!select || !petBreedsData.petTypes) return;

    const typeData = petBreedsData.petTypes.find((t) => t.name === petType);
    select.innerHTML = '<option value="">Select a Breed</option>';

    const breeds = typeData && typeData.breeds ? [...typeData.breeds] : [];
    if (preferredBreed && !breeds.includes(preferredBreed)) {
      breeds.unshift(preferredBreed);
    }

    breeds.forEach((breed) => {
      const opt = document.createElement("option");
      opt.value = breed;
      opt.textContent = breed;
      select.appendChild(opt);
    });

    if (preferredBreed) {
      select.value = preferredBreed;
    }

    if (select._fursDD) select._fursDD.refresh();
  }

  function getBreedSourceType(petType) {
    const normalized = (petType || "").trim();
    if (!normalized) return "Other";
    if (/^(cat|dog)$/i.test(normalized)) {
      return normalized.charAt(0).toUpperCase() + normalized.slice(1).toLowerCase();
    }
    if (
      petBreedsData.petTypes &&
      petBreedsData.petTypes.some((t) => t.name === normalized)
    ) {
      return normalized;
    }
    return "Other";
  }

  function getTogglePetKey(petType) {
    const lower = (petType || "").toLowerCase();
    if (lower === "cat") return "cat";
    if (lower === "dog") return "dog";
    return "other";
  }

  function parseBirthdayToISO(value) {
    if (!value) return "";
    if (/^\d{4}-\d{2}-\d{2}$/.test(value)) return value;
    const match = String(value).match(/(\d{2})\/(\d{2})\/(\d{4})/);
    if (!match) return "";
    return match[3] + "-" + match[2] + "-" + match[1];
  }

  function setBirthdayField(isoDate) {
    const hidden = document.querySelector('#cbgPetAddNew input[name="birthday"]');
    if (!hidden) return;

    const uid = hidden.id.replace("bc-input-", "");
    const display = document.getElementById("bc-display-" + uid);
    const trigger = document.getElementById("bc-trigger-" + uid);

    if (isoDate) {
      hidden.value = isoDate;
      const parts = isoDate.split("-");
      if (display && parts.length === 3) {
        display.textContent = parts[2] + " / " + parts[1] + " / " + parts[0];
      }
      trigger?.classList.remove("bc-empty");
    } else {
      hidden.value = "";
      if (display) {
        display.textContent = trigger?.dataset.placeholder || "dd / mm / yyyy";
      }
      trigger?.classList.add("bc-empty");
    }

    hidden.dispatchEvent(new Event("change", { bubbles: true }));
  }

  function setPetPhotoPreview(src) {
    const preview = document.getElementById("cbgPetPhotoPreview");
    const placeholder = document.getElementById("cbgPetPhotoPlaceholder");
    const input = document.getElementById("cbgPetPhotoInput");
    if (!preview || !placeholder) return;

    if (src) {
      preview.src = src;
      preview.style.display = "block";
      placeholder.style.display = "none";
    } else {
      preview.src = "";
      preview.style.display = "none";
      placeholder.style.display = "";
    }
    if (input) input.value = "";
  }

  function resetPetForm() {
    editingPetId = null;

    const name = document.getElementById("cbgPetName");
    if (name) name.value = "";

    document.getElementById("cbgPetNameWrap")?.classList.remove("valid");
    setBirthdayField("");
    setPetPhotoPreview("");

    document
      .querySelectorAll("#cbgPetAddNew .pet-option")
      .forEach((btn) => btn.classList.remove("highlight"));
    document
      .querySelector('#cbgPetAddNew .pet-option[data-pet="other"]')
      ?.classList.add("highlight");
    populateBreeds("Other");

    document
      .querySelectorAll('#cbgPetAddNew input[name="sex"]')
      .forEach((r) => {
        r.checked = false;
      });

    const weight = document.getElementById("cbgPetWeight");
    if (weight) weight.value = "4";

    const notes = document.getElementById("cbgPetNotes");
    if (notes) notes.value = "";

    updateContinueState();
  }

  function populatePetForm(data) {
    if (!data) return;

    const name = document.getElementById("cbgPetName");
    if (name) name.value = data.name || "";
    document
      .getElementById("cbgPetNameWrap")
      ?.classList.toggle("valid", !!(data.name || "").trim());

    setBirthdayField(parseBirthdayToISO(data.birthday || ""));
    setPetPhotoPreview(data.image || "");

    const toggleKey = getTogglePetKey(data.type);
    document
      .querySelectorAll("#cbgPetAddNew .pet-option")
      .forEach((btn) => {
        btn.classList.toggle("highlight", btn.dataset.pet === toggleKey);
      });

    const breedSource = getBreedSourceType(data.type);
    populateBreeds(breedSource, data.breed || "");

    document
      .querySelectorAll('#cbgPetAddNew input[name="sex"]')
      .forEach((r) => {
        r.checked =
          (r.value || "").toLowerCase() === (data.sex || "").toLowerCase();
      });

    const weight = document.getElementById("cbgPetWeight");
    if (weight) weight.value = data.weight || "4";

    const notes = document.getElementById("cbgPetNotes");
    if (notes) notes.value = data.notes || "";

    updateContinueState();
  }

  function openEditPet(card) {
    if (!card) return;

    editingPetId = card.dataset.petId || null;
    selectedPetId = card.dataset.petId || null;
    document
      .querySelectorAll(".cbg-pet-card")
      .forEach((c) => c.classList.remove("selected"));
    card.classList.add("selected");

    populatePetForm({
      name: card.dataset.name || "",
      type: card.dataset.type || "",
      breed: card.dataset.breed || "",
      birthday: card.dataset.birthday || "",
      sex: card.dataset.sex || "",
      weight: card.dataset.weight || "4",
      notes: card.dataset.notes || "",
      image: card.dataset.image || card.querySelector("img")?.src || "",
    });

    showPetSubView("addNew");
  }

  function openAddNewPet() {
    resetPetForm();
    showPetSubView("addNew");
  }

  function leavePetForm() {
    const returnView = editingPetId != null ? "selectExisting" : "choice";
    resetPetForm();
    showPetSubView(returnView);
  }

  async function loadPetBreeds() {
    try {
      const baseUrl = document.body.dataset.baseUrl || "";
      const res = await fetch(baseUrl + "assets/data/pet-breeds.json");
      petBreedsData = await res.json();
    } catch (e) {
      console.error("Failed to load pet breeds", e);
    }
  }

  function setupPetTypeToggle() {
    document.querySelectorAll("#cbgPetAddNew .pet-option").forEach((btn) => {
      btn.addEventListener("click", () => {
        document
          .querySelectorAll("#cbgPetAddNew .pet-option")
          .forEach((b) => b.classList.remove("highlight"));
        btn.classList.add("highlight");
        const map = { cat: "Cat", dog: "Dog", other: "Other" };
        populateBreeds(map[btn.dataset.pet] || "Other");
        updateContinueState();
      });
    });
  }

  function setupNewPetValidation() {
    const fields = ["cbgPetName", "cbgPetBreed", "cbgPetWeight", "cbgPetNotes"];
    fields.forEach((id) => {
      const el = document.getElementById(id);
      if (el)
        el.addEventListener("input", () => {
          if (id === "cbgPetName") {
            document
              .getElementById("cbgPetNameWrap")
              ?.classList.toggle("valid", el.value.trim().length > 0);
          }
          updateContinueState();
        });
    });

    document
      .querySelectorAll('#cbgPetAddNew input[name="sex"]')
      .forEach((r) => {
        r.addEventListener("change", updateContinueState);
      });

    document
      .querySelector('#cbgPetAddNew input[name="birthday"]')
      ?.addEventListener("change", updateContinueState);
    document
      .getElementById("cbgPetBreed")
      ?.addEventListener("change", updateContinueState);
  }

  function setupWeightStepper() {
    const input = document.getElementById("cbgPetWeight");
    document.getElementById("cbgWeightMinus")?.addEventListener("click", () => {
      const val = Math.max(1, parseInt(input.value || 4, 10) - 1);
      input.value = val;
      updateContinueState();
    });
    document.getElementById("cbgWeightPlus")?.addEventListener("click", () => {
      const val = Math.min(99, parseInt(input.value || 4, 10) + 1);
      input.value = val;
      updateContinueState();
    });
  }

  function setupPetPhotoUpload() {
    const input = document.getElementById("cbgPetPhotoInput");
    const btn = document.getElementById("cbgPetPhotoBtn");
    const preview = document.getElementById("cbgPetPhotoPreview");
    const placeholder = document.getElementById("cbgPetPhotoPlaceholder");

    btn?.addEventListener("click", () => input?.click());
    input?.addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        preview.src = ev.target.result;
        preview.style.display = "block";
        placeholder.style.display = "none";
      };
      reader.readAsDataURL(file);
    });
  }

  function setupPetCards() {
    document.querySelectorAll(".cbg-pet-card").forEach((card) => {
      card.addEventListener("click", (e) => {
        if (e.target.closest(".btn-edit")) return;
        document
          .querySelectorAll(".cbg-pet-card")
          .forEach((c) => c.classList.remove("selected"));
        card.classList.add("selected");
        selectedPetId = card.dataset.petId;
        updateContinueState();
      });

      card.querySelector(".btn-edit")?.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        openEditPet(card);
      });
    });
  }

  function showAddressPanel(panelId) {
    document
      .querySelectorAll(".cbg-address-panel")
      .forEach((p) => p.classList.remove("active"));
    document.getElementById(panelId)?.classList.add("active");
  }

  function toggleAddressEditUI() {
    const editing = addressEditing;
    document
      .getElementById("cbgServiceFooter")
      ?.classList.toggle("is-hidden", editing);
    document
      .getElementById("cbgAddressNormalView")
      ?.classList.toggle("is-hidden", editing);
  }

  function updateAddressFieldValidation() {
    const line1 = document.getElementById("cbgAddrLine1");
    const line2 = document.getElementById("cbgAddrLine2");
    const city = document.getElementById("cbgAddrCity");
    const postcode = document.getElementById("cbgAddrPostcode");

    document
      .getElementById("cbgAddrLine1Wrap")
      ?.classList.toggle("valid", !!line1?.value.trim());
    document
      .getElementById("cbgAddrLine2Wrap")
      ?.classList.toggle("valid", true);
    document
      .getElementById("cbgAddrCityWrap")
      ?.classList.toggle("valid", !!city?.value.trim());
    document
      .getElementById("cbgAddrPostcodeWrap")
      ?.classList.toggle("valid", !!postcode?.value.trim());
  }

  function isAddressEditValid() {
    const line1 = document.getElementById("cbgAddrLine1")?.value.trim();
    const city = document.getElementById("cbgAddrCity")?.value.trim();
    const postcode = document.getElementById("cbgAddrPostcode")?.value.trim();
    return !!(line1 && city && postcode);
  }

  function setAddressMode(mode) {
    addressMode = mode;
    addressEditing = false;

    document.querySelectorAll(".cbg-address-toggle-btn").forEach((btn) => {
      btn.classList.toggle("active", btn.dataset.addressMode === mode);
    });

    if (mode === "home") {
      showAddressPanel("cbgAddressHomePanel");
    } else {
      showAddressPanel("cbgAddressDifferentPanel");
    }
    toggleAddressEditUI();
    updateContinueState();
  }

  function openAddressEdit() {
    addressEditing = true;
    const homeText =
      document.getElementById("cbgHomeAddressText")?.textContent || "";
    const parts = homeText.split(",").map((s) => s.trim());

    document.getElementById("cbgAddrLine1").value = parts[0] || "";
    document.getElementById("cbgAddrLine2").value = "";
    document.getElementById("cbgAddrCity").value = parts[1] || "";
    document.getElementById("cbgAddrPostcode").value = parts[2] || "";

    showAddressPanel("cbgAddressEditPanel");
    updateAddressFieldValidation();
    toggleAddressEditUI();
    updateContinueState();
  }

  function saveAddressEdit() {
    if (!isAddressEditValid()) {
      updateAddressFieldValidation();
      return;
    }

    const line1 = document.getElementById("cbgAddrLine1").value.trim();
    const city = document.getElementById("cbgAddrCity").value.trim();
    const postcode = document.getElementById("cbgAddrPostcode").value.trim();
    const full = [line1, city, postcode].filter(Boolean).join(", ");

    document.getElementById("cbgHomeAddressText").textContent = full;
    addressEditing = false;
    showAddressPanel("cbgAddressHomePanel");
    toggleAddressEditUI();
    updateContinueState();
  }

  function cancelAddressEdit() {
    addressEditing = false;
    showAddressPanel(
      addressMode === "home"
        ? "cbgAddressHomePanel"
        : "cbgAddressDifferentPanel",
    );
    toggleAddressEditUI();
    updateContinueState();
  }

  function setupServiceStep() {
    document
      .getElementById("cbgServiceBackBtn")
      ?.addEventListener("click", () => showStep(0));
  }

  function setupExtrasStep() {
    document
      .getElementById("cbgExtrasBackBtn")
      ?.addEventListener("click", () => showStep(1));
  }

  function setupReviewStep() {
    document.querySelectorAll(".cbg-review-edit").forEach((btn) => {
      btn.addEventListener("click", () => {
        const step = parseInt(btn.dataset.reviewStep, 10);
        if (!isNaN(step)) showStep(step);
      });
    });

    document
      .getElementById("cbgReviewBackBtn")
      ?.addEventListener("click", () => showStep(2));

    document
      .getElementById("cbgConfirmPayBtn")
      ?.addEventListener("click", () => {
        const btn = document.getElementById("cbgConfirmPayBtn");
        if (btn && !btn.disabled) showStep(4);
      });

    document
      .getElementById("cbgPromoApplyBtn")
      ?.addEventListener("click", applyPromoCode);
    document
      .getElementById("cbgPromoInput")
      ?.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          e.preventDefault();
          applyPromoCode();
        }
      });
    document
      .getElementById("cbgPromoInput")
      ?.addEventListener("input", () => setPromoError(false));
    document
      .getElementById("cbgReviewPromoApplied")
      ?.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-remove-promo]");
        if (!btn) return;
        removePromoCode(btn.getAttribute("data-remove-promo"));
      });
    document
      .getElementById("cbgRemovePromoBtn")
      ?.addEventListener("click", () => removePromoCode());

    document
      .getElementById("cbgReviewTerms")
      ?.addEventListener("change", updateConfirmPayState);

    document.querySelectorAll(".cbg-review-accordion-head").forEach((head) => {
      head.addEventListener("click", () => {
        const accordion = head.closest(".cbg-review-accordion");
        accordion?.classList.toggle("open");
      });
    });

    document.querySelectorAll(".cbg-summary-accordion-head").forEach((head) => {
      head.addEventListener("click", () => {
        head.closest(".cbg-summary-accordion")?.classList.toggle("open");
      });
    });
  }

  function setupStepNavigation() {
    els.stepItems.forEach((item, i) => {
      item.addEventListener("click", () => {
        if (i < currentStep) showStep(i);
      });
    });
  }

  function init() {
    Object.values(els.continueWraps).forEach((wrap) => {
      wrap?.addEventListener("click", () => {
        if (wrap.classList.contains("active")) {
          goForward(wrap.dataset.continueContext);
        }
      });
    });

    document
      .getElementById("cbgAddNewPetBtn")
      ?.addEventListener("click", openAddNewPet);
    document
      .getElementById("cbgSelectExistingBtn")
      ?.addEventListener("click", () => showPetSubView("selectExisting"));
    document
      .getElementById("cbgPetAddNewBackBtn")
      ?.addEventListener("click", leavePetForm);
    document
      .getElementById("cbgPetSelectExistingBackBtn")
      ?.addEventListener("click", () => showPetSubView("choice"));

    setupPetTypeToggle();
    setupNewPetValidation();
    setupWeightStepper();
    setupPetPhotoUpload();
    setupPetCards();
    setupServiceStep();
    setupSpaceExtras();
    setupExtrasStep();
    setupReviewStep();
    setupPayStep();
    setupStepNavigation();

    loadPetBreeds().then(() => {
      populateBreeds("Other");
    });

    showStep(0);
    updateSummary();
    updateConfirmPayState();
    updatePromoUI();
    setTimeout(updateSummary, 400);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

const navheader = document.querySelector("header");

function updateHeader() {
  navheader.style.backgroundColor = window.scrollY > 10 ? "white" : "#FDFCF8";
}

window.addEventListener("scroll", updateHeader);
updateHeader();
