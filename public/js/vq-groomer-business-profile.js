(function () {
    const SERVICE_KEY_LEGACY = {
        "Full Groom (bath, dry, haircut)": "full_groom",
        "Face Trim Only": "face_trim",
        "Nail Trim": "nail_trim",
        "Ear Cleaning": "ear_cleaning",
        "Tail Trim Only": "tail_trim_only",
        "Bath & Brush": "bath_brush",
        "Luxury Spa": "luxury_spa",
    };

    const ADDON_KEY_LEGACY = {
        "Flea & Tick Treatment": "flea_tick",
        "Fast-Dry Service (express grooming)": "fast_dry",
    };

    function slugify(name) {
        const base = String(name)
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, "_")
            .replace(/^_+|_+$/g, "");

        if (base !== "") {
            return base;
        }

        let hash = 0;
        for (let i = 0; i < name.length; i++) {
            hash = (hash << 5) - hash + name.charCodeAt(i);
            hash |= 0;
        }

        return "item_" + Math.abs(hash).toString(16).slice(0, 8);
    }

    function serviceKey(name) {
        return SERVICE_KEY_LEGACY[name] ?? slugify(name);
    }

    function addonKey(name) {
        return ADDON_KEY_LEGACY[name] ?? slugify(name);
    }

    function toggleInArray(list, value) {
        const idx = list.indexOf(value);
        if (idx >= 0) {
            list.splice(idx, 1);
        } else {
            list.push(value);
        }
    }

    function parseSpecialtyOtherTags(raw) {
        if (Array.isArray(raw)) {
            return raw
                .map((item) => String(item ?? "").trim())
                .filter(
                    (item, index, list) =>
                        item !== "" && list.indexOf(item) === index,
                );
        }

        if (typeof raw !== "string" || raw.trim() === "") {
            return [];
        }

        return raw
            .split(",")
            .map((item) => item.trim())
            .filter(
                (item, index, list) =>
                    item !== "" && list.indexOf(item) === index,
            );
    }

    function syncPricingMap(selectedNames, existingMap, keyFn) {
        const next = {};
        selectedNames.forEach((name) => {
            if (typeof name !== "string" || name.trim() === "") {
                return;
            }
            const trimmed = name.trim();
            const key = keyFn(trimmed);
            const row = existingMap[key] ?? {};
            next[key] = {
                name: trimmed,
                price: String(row.price ?? "").trim(),
                description: String(row.description ?? "").trim(),
            };
        });

        return next;
    }

    document.addEventListener("alpine:init", registerGroomerBusinessProfile);
    if (window.Alpine && typeof window.Alpine.data === "function") {
        registerGroomerBusinessProfile();
    }

    function registerGroomerBusinessProfile() {
        Alpine.data("groomerBusinessProfile", (seed = {}) => ({
            experience: seed.experience ?? "",
            petSpecialties: Array.isArray(seed.petSpecialties)
                ? [...seed.petSpecialties]
                : [],
            specialtyOtherInput: "",
            specialtyOtherTags: parseSpecialtyOtherTags(seed.specialtyOther),
            petSizes: Array.isArray(seed.petSizes) ? [...seed.petSizes] : [],
            serviceInput: "",
            customServices: Array.isArray(seed.customServices)
                ? [...seed.customServices]
                : [],
            selectedServices: Array.isArray(seed.selectedServices)
                ? [...seed.selectedServices]
                : [],
            servicesPricing:
                seed.servicesPricing && typeof seed.servicesPricing === "object"
                    ? JSON.parse(JSON.stringify(seed.servicesPricing))
                    : {},
            addonInput: "",
            customAddons: Array.isArray(seed.customAddons)
                ? [...seed.customAddons]
                : [],
            selectedAddons: Array.isArray(seed.selectedAddons)
                ? [...seed.selectedAddons]
                : [],
            addonPricing:
                seed.addonPricing && typeof seed.addonPricing === "object"
                    ? JSON.parse(JSON.stringify(seed.addonPricing))
                    : {},
            serviceCatalog: Array.isArray(seed.serviceCatalog)
                ? seed.serviceCatalog
                : [],
            addonCatalog: Array.isArray(seed.addonCatalog)
                ? seed.addonCatalog
                : [],
            serviceDefaultDescriptions: seed.serviceDefaultDescriptions ?? {},
            serviceAddPending: false,
            addonAddPending: false,
            submitting: false,
            serviceDescriptionsCommitted: {},
            serviceDescriptionsEditing: {},
            addonDescriptionsCommitted: {},
            addonDescriptionsEditing: {},
            addonsAddedViaInput: {},

            init() {
                this.normalizeSelectedAddons();
                this.syncServicesPricing();
                this.syncAddonPricing();
                this.hydrateAddonsAddedViaInput();
                this.hydrateDescriptionCommittedState();
            },

            normalizeSelectedAddons() {
                const catalog = new Set(this.addonCatalog);
                const custom = new Set(this.customAddons);
                const keyToLabel = {};

                this.addonCatalog.forEach((label) => {
                    keyToLabel[addonKey(label)] = label;
                });
                this.customAddons.forEach((label) => {
                    keyToLabel[addonKey(label)] = label;
                });

                const normalized = [];
                const seen = new Set();

                this.selectedAddons.forEach((item) => {
                    if (typeof item !== "string" || item.trim() === "") {
                        return;
                    }

                    let name = item.trim();
                    if (catalog.has(name) || custom.has(name)) {
                        // already a display label
                    } else if (keyToLabel[name]) {
                        name = keyToLabel[name];
                    } else if (
                        this.addonPricing[name] &&
                        typeof this.addonPricing[name].name === "string" &&
                        this.addonPricing[name].name.trim() !== ""
                    ) {
                        name = this.addonPricing[name].name.trim();
                    } else if (/^[a-z0-9_]+$/.test(name)) {
                        return;
                    }

                    if (!seen.has(name)) {
                        seen.add(name);
                        normalized.push(name);
                    }
                });

                this.selectedAddons = normalized;
            },

            hydrateAddonsAddedViaInput() {
                this.addonsAddedViaInput = {};
                this.customAddons.forEach((name) => {
                    this.addonsAddedViaInput[addonKey(name)] = true;
                });
            },

            hydrateDescriptionCommittedState() {
                this.serviceDescriptionsCommitted = {};
                this.selectedServices.forEach((name) => {
                    const key = serviceKey(name);
                    const desc = String(
                        this.servicesPricing[key]?.description ?? "",
                    ).trim();
                    if (desc !== "") {
                        this.serviceDescriptionsCommitted[key] = true;
                    }
                });
                this.addonDescriptionsCommitted = {};
                this.selectedAddons.forEach((name) => {
                    const key = addonKey(name);
                    const desc = String(
                        this.addonPricing[key]?.description ?? "",
                    ).trim();
                    if (desc !== "") {
                        this.addonDescriptionsCommitted[key] = true;
                    }
                });
            },

            serviceKey,
            addonKey,

            togglePetSpecialty(value) {
                toggleInArray(this.petSpecialties, value);
            },

            addSpecialtyOtherTags() {
                const raw = String(this.specialtyOtherInput ?? "").trim();
                if (raw === "") {
                    return;
                }

                const next = [...this.specialtyOtherTags];
                raw.split(",").forEach((part) => {
                    const tag = part.trim();
                    if (tag === "" || next.includes(tag)) {
                        return;
                    }
                    next.push(tag);
                });

                this.specialtyOtherTags = next;
                this.specialtyOtherInput = "";

                if (!this.petSpecialties.includes("other")) {
                    this.petSpecialties.push("other");
                }
            },

            removeSpecialtyOtherTag(index) {
                if (index < 0 || index >= this.specialtyOtherTags.length) {
                    return;
                }
                this.specialtyOtherTags.splice(index, 1);
            },

            togglePetSize(value) {
                toggleInArray(this.petSizes, value);
            },

            isServiceSelected(name) {
                return this.selectedServices.includes(name);
            },

            isAddonSelected(name) {
                return this.selectedAddons.includes(name);
            },

            toggleService(name) {
                toggleInArray(this.selectedServices, name);
                this.syncServicesPricing();
            },

            toggleAddon(name) {
                toggleInArray(this.selectedAddons, name);
                this.syncAddonPricing();
            },

            syncServicesPricing() {
                this.servicesPricing = syncPricingMap(
                    this.selectedServices,
                    this.servicesPricing,
                    serviceKey,
                );
            },

            syncAddonPricing() {
                this.addonPricing = syncPricingMap(
                    this.selectedAddons,
                    this.addonPricing,
                    addonKey,
                );
            },

            addCustomService() {
                if (this.serviceAddPending) {
                    return;
                }
                const name = this.serviceInput.trim();
                if (name === "") {
                    return;
                }

                this.serviceAddPending = true;
                window.requestAnimationFrame(() => {
                    try {
                        if (this.serviceCatalog.includes(name)) {
                            if (!this.selectedServices.includes(name)) {
                                this.selectedServices.push(name);
                            }
                        } else {
                            if (!this.customServices.includes(name)) {
                                this.customServices.push(name);
                            }
                            if (!this.selectedServices.includes(name)) {
                                this.selectedServices.push(name);
                            }
                        }
                        this.serviceInput = "";
                        this.syncServicesPricing();
                    } finally {
                        this.serviceAddPending = false;
                    }
                });
            },

            addCustomAddon() {
                if (this.addonAddPending) {
                    return;
                }
                const name = this.addonInput.trim();
                if (name === "") {
                    return;
                }

                this.addonAddPending = true;
                window.requestAnimationFrame(() => {
                    try {
                        if (this.addonCatalog.includes(name)) {
                            if (!this.selectedAddons.includes(name)) {
                                this.selectedAddons.push(name);
                            }
                        } else {
                            if (!this.customAddons.includes(name)) {
                                this.customAddons.push(name);
                            }
                            if (!this.selectedAddons.includes(name)) {
                                this.selectedAddons.push(name);
                            }
                        }
                        this.addonsAddedViaInput[addonKey(name)] = true;
                        this.addonInput = "";
                        this.syncAddonPricing();
                    } finally {
                        this.addonAddPending = false;
                    }
                });
            },

            stepPrice(key, delta, type = "service") {
                const map =
                    type === "service"
                        ? this.servicesPricing
                        : this.addonPricing;
                const row = map[key];
                if (!row) {
                    return;
                }
                const current = parseInt(row.price, 10);
                const safe = Number.isFinite(current) ? current : 0;
                row.price = String(Math.max(0, safe + delta));
            },

            serviceDefaultDescription(name) {
                return this.serviceDefaultDescriptions[name] ?? "";
            },

            serviceDescriptionText(name) {
                const key = serviceKey(name);
                const custom = String(
                    this.servicesPricing[key]?.description ?? "",
                ).trim();

                if (Boolean(this.serviceDescriptionsCommitted[key])) {
                    return custom;
                }

                const defaultDesc = this.serviceDefaultDescription(name);
                if (defaultDesc !== "" && custom === "") {
                    return defaultDesc;
                }

                return custom;
            },

            showServiceDescriptionText(name) {
                const key = serviceKey(name);
                if (this.serviceDescriptionsEditing[key]) {
                    return false;
                }

                if (Boolean(this.serviceDescriptionsCommitted[key])) {
                    return true;
                }

                const custom = String(
                    this.servicesPricing[key]?.description ?? "",
                ).trim();
                const defaultDesc = this.serviceDefaultDescription(name);

                return defaultDesc !== "" && custom === "";
            },

            editServiceDescription(name) {
                const key = serviceKey(name);
                if (!this.servicesPricing[key]) {
                    return;
                }

                const custom = String(
                    this.servicesPricing[key].description ?? "",
                ).trim();
                if (
                    custom === "" &&
                    !Boolean(this.serviceDescriptionsCommitted[key])
                ) {
                    const defaultDesc = this.serviceDefaultDescription(name);
                    if (defaultDesc !== "") {
                        this.servicesPricing[key].description = defaultDesc;
                    }
                }

                this.serviceDescriptionsEditing[key] = true;
            },

            commitServiceDescription(name) {
                const key = serviceKey(name);
                if (this.servicesPricing[key]) {
                    this.servicesPricing[key].description = String(
                        this.servicesPricing[key].description ?? "",
                    ).trim();
                }
                this.serviceDescriptionsCommitted[key] = true;
                this.serviceDescriptionsEditing[key] = false;
            },

            addonDescriptionText(name) {
                const key = addonKey(name);

                return String(this.addonPricing[key]?.description ?? "").trim();
            },

            showAddonDescriptionText(name) {
                const key = addonKey(name);
                if (this.addonDescriptionsEditing[key]) {
                    return false;
                }

                return Boolean(this.addonDescriptionsCommitted[key]);
            },

            showAddonDescriptionEditor(name) {
                return !this.showAddonDescriptionText(name);
            },

            editAddonDescription(name) {
                const key = addonKey(name);
                this.addonDescriptionsEditing[key] = true;
            },

            commitAddonDescription(name) {
                const key = addonKey(name);
                if (this.addonPricing[key]) {
                    this.addonPricing[key].description = String(
                        this.addonPricing[key].description ?? "",
                    ).trim();
                }
                this.addonDescriptionsCommitted[key] = true;
                this.addonDescriptionsEditing[key] = false;
            },

            get canContinue() {
                const hasSpecialty = this.petSpecialties.length > 0;
                const otherOk =
                    !this.petSpecialties.includes("other") ||
                    this.specialtyOtherTags.length > 0;

                return (
                    this.experience.trim() !== "" &&
                    hasSpecialty &&
                    this.petSizes.length > 0 &&
                    otherOk
                );
            },

            payload() {
                return {
                    experience: this.experience,
                    petSpecialties: this.petSpecialties,
                    specialtyOther: this.specialtyOtherTags.join(", "),
                    petSizes: this.petSizes,
                    customServices: this.customServices,
                    selectedServices: this.selectedServices,
                    customAddons: this.customAddons,
                    selectedAddons: this.selectedAddons,
                    servicesPricing: this.servicesPricing,
                    addonPricing: this.addonPricing,
                };
            },

            resolveWire() {
                const root =
                    (this.$el && this.$el.closest("[wire\\:id]")) ||
                    document.querySelector(
                        ".verify-qualify-page [wire\\:id]",
                    ) ||
                    document.querySelector("[wire\\:id]");
                const id = root && root.getAttribute("wire:id");
                if (
                    id &&
                    typeof Livewire !== "undefined" &&
                    typeof Livewire.find === "function"
                ) {
                    const found = Livewire.find(id);
                    if (found) {
                        return found;
                    }
                }

                return this.$wire || null;
            },

            async submitForm() {
                if (!this.canContinue || this.submitting) {
                    return;
                }

                const wire = this.resolveWire();
                const callFn =
                    wire &&
                    (typeof wire.submitGroomerBusinessProfile === "function"
                        ? wire.submitGroomerBusinessProfile.bind(wire)
                        : typeof wire.call === "function"
                          ? (payload) =>
                                wire.call(
                                    "submitGroomerBusinessProfile",
                                    payload,
                                )
                          : typeof wire.$call === "function"
                            ? (payload) =>
                                  wire.$call(
                                      "submitGroomerBusinessProfile",
                                      payload,
                                  )
                            : null);

                if (!callFn) {
                    console.error(
                        "[verify-qualify] Unable to call submitGroomerBusinessProfile — Livewire component not found.",
                    );
                    return;
                }

                this.submitting = true;
                try {
                    await callFn(this.payload());
                } finally {
                    this.submitting = false;
                }
            },
        }));
    }
})();
