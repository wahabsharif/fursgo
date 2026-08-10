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
            customAddonRows: Array.isArray(seed.customAddonRows)
                ? seed.customAddonRows.map((row) => ({
                      name: String(row?.name ?? "").trim(),
                      selected: Boolean(row?.selected),
                      price: String(row?.price ?? "").trim(),
                  }))
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
                        this.customAddonRows.push({
                            name,
                            selected: true,
                            price: "",
                        });
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

            stepAddonPrice(index, delta) {
                const row = this.customAddonRows[index];
                if (!row) {
                    return;
                }

                const current = Number.parseFloat(String(row.price ?? "")) || 0;
                const next = Math.max(0, current + delta);
                row.price = String(next);
            },

            clientPayload() {
                return {
                    suitableFor: this.suitableFor,
                    selectedRules: this.selectedRules,
                    selectedAmenities: this.selectedAmenities,
                    customAddonRows: this.customAddonRows,
                    rulesCustom: this.rulesCustom,
                    amenitiesCustom: this.amenitiesCustom,
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
                if (this.submitting) {
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
                        "[verify-qualify] Unable to call submitSpacerBusinessProfile — Livewire component not found.",
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
