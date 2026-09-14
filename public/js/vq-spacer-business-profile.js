(function () {
    document.addEventListener("alpine:init", registerSpacerBusinessProfile);
    if (window.Alpine && typeof window.Alpine.data === "function") {
        registerSpacerBusinessProfile();
    }

    function registerSpacerBusinessProfile() {
        Alpine.data("spacerBusinessProfile", (seed = {}) => ({
            suitableFor: Array.isArray(seed.suitableFor)
                ? [...seed.suitableFor]
                : [],
            selectedRules: Array.isArray(seed.selectedRules)
                ? [...seed.selectedRules]
                : [],
            selectedAmenities: Array.isArray(seed.selectedAmenities)
                ? [...seed.selectedAmenities]
                : [],
            servicesPricing: normalizeServicesPricing(seed.servicesPricing),
            fursgoAddons: normalizeFursgoAddons(
                seed.fursgoAddons,
                seed.addonCatalog,
            ),
            addonCatalog: Array.isArray(seed.addonCatalog)
                ? seed.addonCatalog.map((item) => ({
                      slug: String(item?.slug ?? "").trim(),
                      label: String(item?.label ?? "").trim(),
                  }))
                : [],
            customAddonRows: Array.isArray(seed.customAddonRows)
                ? seed.customAddonRows
                      .map((row) => normalizeCustomAddonRow(row, true))
                      .filter(Boolean)
                : [],
            rulesCustom: Array.isArray(seed.rulesCustom)
                ? seed.rulesCustom
                      .map((item) => normalizeCustomEntry(item))
                      .filter(Boolean)
                : [],
            amenitiesCustom: Array.isArray(seed.amenitiesCustom)
                ? seed.amenitiesCustom
                      .map((item) => normalizeCustomEntry(item))
                      .filter(Boolean)
                : [],
            addonInput: "",
            ruleInput: "",
            amenityInput: "",
            addonAddPending: false,
            ruleAddPending: false,
            amenityAddPending: false,
            submitting: false,

            get canContinue() {
                return Object.values(this.servicesPricing).some(
                    (row) => row?.selected,
                );
            },

            get selectedAddonList() {
                const list = [];

                this.customAddonRows.forEach((row, index) => {
                    if (!row?.name) {
                        return;
                    }
                    list.push({
                        key: "custom-" + index,
                        name: row.name,
                        kind: "custom",
                        index,
                    });
                });

                this.addonCatalog.forEach((addon) => {
                    if (
                        !addon.slug ||
                        !this.fursgoAddons[addon.slug]?.selected
                    ) {
                        return;
                    }
                    list.push({
                        key: "fursgo-" + addon.slug,
                        name: addon.label || addon.slug,
                        kind: "fursgo",
                        slug: addon.slug,
                    });
                });

                return list;
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
                        const exists = this.customAddonRows.some(
                            (row) =>
                                String(row.name).toLowerCase() ===
                                name.toLowerCase(),
                        );
                        if (!exists) {
                            this.customAddonRows.push(
                                normalizeCustomAddonRow(
                                    {
                                        name,
                                        selected: true,
                                        price: "",
                                        description: "",
                                    },
                                    false,
                                ),
                            );
                        }
                        this.addonInput = "";
                    } finally {
                        this.addonAddPending = false;
                    }
                });
            },

            addCustomRule() {
                if (this.ruleAddPending) {
                    return;
                }

                const text = this.ruleInput.trim();
                if (text === "") {
                    return;
                }

                this.ruleAddPending = true;
                window.requestAnimationFrame(() => {
                    try {
                        if (
                            !this.rulesCustom.some((rule) => rule.text === text)
                        ) {
                            this.rulesCustom.push({
                                text,
                                selected: true,
                            });
                        }
                        this.ruleInput = "";
                    } finally {
                        this.ruleAddPending = false;
                    }
                });
            },

            addCustomAmenity() {
                if (this.amenityAddPending) {
                    return;
                }

                const text = this.amenityInput.trim();
                if (text === "") {
                    return;
                }

                this.amenityAddPending = true;
                window.requestAnimationFrame(() => {
                    try {
                        if (
                            !this.amenitiesCustom.some(
                                (amenity) => amenity.text === text,
                            )
                        ) {
                            this.amenitiesCustom.push({
                                text,
                                selected: true,
                            });
                        }
                        this.amenityInput = "";
                    } finally {
                        this.amenityAddPending = false;
                    }
                });
            },

            addonEntryRow(entry) {
                if (!entry) {
                    return null;
                }
                if (entry.kind === "custom") {
                    return this.customAddonRows[entry.index] || null;
                }

                return this.fursgoAddons[entry.slug] || null;
            },

            addonDescriptionText(entry) {
                const text = String(
                    this.addonEntryRow(entry)?.description ?? "",
                ).trim();

                return text === "" ? "Not provided" : text;
            },

            addonDescriptionIsEmpty(entry) {
                return (
                    String(
                        this.addonEntryRow(entry)?.description ?? "",
                    ).trim() === ""
                );
            },

            showAddonDescriptionText(entry) {
                const row = this.addonEntryRow(entry);
                if (!row || row.descriptionEditing) {
                    return false;
                }

                return Boolean(row.descriptionCommitted);
            },

            editAddonDescription(entry) {
                const row = this.addonEntryRow(entry);
                if (row) {
                    row.descriptionEditing = true;
                }
            },

            commitAddonDescription(entry) {
                const row = this.addonEntryRow(entry);
                if (!row) {
                    return;
                }
                row.description = String(row.description ?? "").trim();
                row.descriptionCommitted = true;
                row.descriptionEditing = false;
            },

            removeCustomAddon(index) {
                if (index < 0 || index >= this.customAddonRows.length) {
                    return;
                }
                this.customAddonRows.splice(index, 1);
            },

            removeCustomRule(index) {
                if (index < 0 || index >= this.rulesCustom.length) {
                    return;
                }
                this.rulesCustom.splice(index, 1);
            },

            removeCustomAmenity(index) {
                if (index < 0 || index >= this.amenitiesCustom.length) {
                    return;
                }
                this.amenitiesCustom.splice(index, 1);
            },

            persistableAddonRow(row) {
                return {
                    selected: Boolean(row?.selected),
                    price: String(row?.price ?? "").trim(),
                    description: String(row?.description ?? "").trim(),
                };
            },

            clientPayload() {
                const fursgoAddons = {};
                Object.keys(this.fursgoAddons).forEach((slug) => {
                    fursgoAddons[slug] = this.persistableAddonRow(
                        this.fursgoAddons[slug],
                    );
                });

                return {
                    suitableFor: this.suitableFor,
                    selectedRules: this.selectedRules,
                    selectedAmenities: this.selectedAmenities,
                    servicesPricing: JSON.parse(
                        JSON.stringify(this.servicesPricing),
                    ),
                    fursgoAddons,
                    customAddonRows: this.customAddonRows.map((row) => ({
                        name: row.name,
                        selected: Boolean(row.selected),
                        price: String(row.price ?? "").trim(),
                        description: String(row.description ?? "").trim(),
                    })),
                    rulesCustom: this.rulesCustom,
                    amenitiesCustom: this.amenitiesCustom,
                };
            },

            resolveWire() {
                const root =
                    (this.$el && this.$el.closest("[wire\\:id]")) ||
                    document.querySelector(
                        ".business-verification-page [wire\\:id]",
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
                if (this.submitting || !this.canContinue) {
                    return;
                }

                const wire = this.resolveWire();
                const callFn =
                    wire &&
                    (typeof wire.submitSpacerBusinessProfile === "function"
                        ? wire.submitSpacerBusinessProfile.bind(wire)
                        : typeof wire.call === "function"
                          ? (payload) =>
                                wire.call(
                                    "submitSpacerBusinessProfile",
                                    payload,
                                )
                          : typeof wire.$call === "function"
                            ? (payload) =>
                                  wire.$call(
                                      "submitSpacerBusinessProfile",
                                      payload,
                                  )
                            : null);

                if (!callFn) {
                    console.error(
                        "[business-verification] Unable to call submitSpacerBusinessProfile — Livewire component not found.",
                    );
                    return;
                }

                this.submitting = true;
                try {
                    await callFn(this.clientPayload());
                } finally {
                    this.submitting = false;
                }
            },
        }));
    }

    function normalizeServicesPricing(raw) {
        const out = {};
        ["hourly", "half_day", "full_day"].forEach((slug) => {
            const row = raw && typeof raw === "object" ? raw[slug] : null;
            out[slug] = {
                selected: Boolean(row?.selected),
                price: String(row?.price ?? "").trim(),
            };
        });

        return out;
    }

    function normalizeFursgoAddons(raw, catalog) {
        const out = {};
        const source = raw && typeof raw === "object" ? raw : {};
        const slugs = Array.isArray(catalog)
            ? catalog
                  .map((item) => String(item?.slug ?? "").trim())
                  .filter(Boolean)
            : Object.keys(source);

        slugs.forEach((slug) => {
            const row = source[slug] || {};
            const description = String(row.description ?? "").trim();
            out[slug] = {
                selected: Boolean(row.selected),
                price: String(row.price ?? "").trim(),
                description,
                descriptionCommitted:
                    Boolean(row.selected) || description !== "",
                descriptionEditing: false,
            };
        });

        return out;
    }

    function normalizeCustomAddonRow(row, fromSaved) {
        if (!row || typeof row !== "object") {
            return null;
        }

        const name = String(row.name ?? "").trim();
        if (name === "") {
            return null;
        }

        const description = String(row.description ?? "").trim();

        return {
            name,
            selected: Boolean(row.selected ?? true),
            price: String(row.price ?? "").trim(),
            description,
            descriptionCommitted: Boolean(fromSaved) || description !== "",
            descriptionEditing: false,
        };
    }

    function normalizeCustomEntry(item) {
        if (typeof item === "string") {
            const text = item.trim();
            return text === "" ? null : { text, selected: true };
        }

        if (!item || typeof item !== "object") {
            return null;
        }

        const text = String(item.text ?? "").trim();
        if (text === "") {
            return null;
        }

        return {
            text,
            selected: Boolean(item.selected ?? true),
        };
    }
})();
