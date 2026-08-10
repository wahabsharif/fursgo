<?php

use App\Models\ServiceArea;
use Livewire\Volt\Component;

new class extends Component {
    public const MAP_COLORS = ['#B8D4E8', '#FFD4B8', '#C8E8B8'];

    public string $name = '';
    public string $address = '';
    public float $radius = 1;
    public ?float $latitude = 51.5074;
    public ?float $longitude = -0.1278;

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'radius' => ['required', 'numeric', 'min:0.1', 'max:50'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $profileId = auth()->id();

        if (!$profileId) {
            $this->addError('name', 'Groomer profile not found for current user.');
            return;
        }

        $existingCount = ServiceArea::query()->where('groomer_spacer_id', $profileId)->count();

        $serviceArea = ServiceArea::create([
            'groomer_spacer_id' => $profileId,
            'name' => $this->name,
            'radius' => $this->radius,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address !== '' ? $this->address : null,
            'map_color' => self::MAP_COLORS[$existingCount % count(self::MAP_COLORS)],
        ]);

        $this->dispatch('service-area-created', itemId: $serviceArea->id);
        $this->reset(['name', 'address']);
        $this->radius = 1;
        $this->latitude = 51.5074;
        $this->longitude = -0.1278;
    }
}; ?>

<section class="service-area-form-panel" aria-label="Add service area form"
    x-data="serviceAreaFormMap(@entangle('latitude').live, @entangle('longitude').live, @entangle('radius').live, @entangle('address').live)">
    <form class="service-area-form" wire:submit.prevent="save">
        <div class="service-area-form-layout">
            <div class="service-area-form-fields">
                <label class="service-field">
                    <span>Service Area Name</span>
                    <input type="text" placeholder="e.g. Waterloo South Bank" wire:model="name" />
                </label>

                <label class="service-field service-area-postcode-field" @click.outside="closeSuggestions()">
                    <span>UK Postcode</span>
                    <div class="service-area-postcode-wrap">
                        <input type="text" x-model="address" placeholder="Start typing a UK postcode, e.g. SW1A 1AA"
                            autocomplete="off" autocapitalize="characters" spellcheck="false" role="combobox"
                            aria-autocomplete="list" :aria-expanded="showSuggestions && suggestions.length > 0"
                            aria-controls="service-area-postcode-suggestions" @input.debounce.300ms="searchPostcodes()"
                            @keydown.arrow-down.prevent="highlightNext()"
                            @keydown.arrow-up.prevent="highlightPrevious()" @keydown.enter.prevent="selectHighlighted()"
                            @keydown.escape.prevent="closeSuggestions()" @focus="searchPostcodes()" />
                        <ul id="service-area-postcode-suggestions" class="service-area-postcode-suggestions"
                            x-show="showSuggestions && suggestions.length > 0" x-cloak role="listbox">
                            <template x-for="(item, index) in suggestions" :key="item.postcode">
                                <li role="option" :aria-selected="highlightedIndex === index">
                                    <button type="button" class="service-area-postcode-suggestion"
                                        :class="{ 'is-active': highlightedIndex === index }"
                                        @click="selectPostcode(item)">
                                        <span class="service-area-postcode-code" x-text="item.postcode"></span>
                                        <span class="service-area-postcode-place" x-text="item.label"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                        <p class="service-area-searching-hint" x-show="isSearching" x-cloak>Searching UK postcodes…</p>
                    </div>
                    <p class="service-area-field-hint service-area-field-hint-error" x-show="geocodeError"
                        x-text="geocodeError" x-cloak></p>
                </label>

                <label class="service-field service-area-radius-field">
                    <span>Service Radius (miles)</span>
                    <input type="number" wire:model.live="radius" min="0.1" max="50" step="0.1" inputmode="decimal"
                        placeholder="e.g. 1" aria-describedby="service-area-radius-hint" />
                    <p id="service-area-radius-hint" class="service-area-radius-hint">Between 0.1 and 50 miles.</p>
                </label>

                <p class="service-area-map-hint">Click the map or drag the pin to set your service area centre.</p>
            </div>

            <div class="service-area-form-map-col">
                <div class="service-area-map-shell" wire:ignore>
                    <div id="service-area-form-map" class="service-area-map" role="img"
                        aria-label="Service area location picker"></div>
                </div>
            </div>
        </div>

        <div class="service-form-actions">
            <button type="button" class="service-form-btn service-form-btn-cancel"
                @click="$dispatch('service-form-cancel')">Cancel</button>
            <button type="submit" class="service-form-btn service-form-btn-save" wire:loading.attr="disabled"
                wire:target="save">
                <span class="save-btn-text" wire:loading.class="hidden" wire:target="save">Save Changes</span>
                <span class="save-btn-loading hidden" wire:loading.class.remove="hidden" wire:target="save">
                    <span class="save-spinner"></span>
                </span>
            </button>
        </div>
    </form>
</section>

@push('script')
    <script>
        (() => {
            const register = () => {
                if (typeof Alpine === 'undefined' || window.__serviceAreaFormMapRegistered) {
                    return;
                }

                window.__serviceAreaFormMapRegistered = true;

                const METERS_PER_STATUTE_MILE = {{ json_encode(\App\Models\ServiceArea::METERS_PER_STATUTE_MILE) }};

                Alpine.data('serviceAreaFormMap', (latitude, longitude, radius, address) => ({
                    latitude,
                    longitude,
                    radius,
                    address,
                    map: null,
                    marker: null,
                    circle: null,
                    geocodeError: '',
                    suggestions: [],
                    showSuggestions: false,
                    highlightedIndex: -1,
                    isSearching: false,
                    searchToken: 0,
                    initMapAttempt: 0,
                    radiusAnimFrameId: null,
                    init() {
                        this.$nextTick(() => this.initMap());
                        this.$watch('latitude', () => this.updatePreview());
                        this.$watch('longitude', () => this.updatePreview());
                        this.$watch('radius', () => this.animateCircleRadiusToModel());
                        window.addEventListener('service-area-form-map-refresh', () => this
                            .refreshMap());
                        window.addEventListener('services-menu-selected', (event) => {
                            if (event?.detail?.menu === 'service-area') {
                                setTimeout(() => this.refreshMap(), 220);
                            }
                        });
                    },
                    normalizePostcode(value) {
                        return String(value || '').trim().toUpperCase();
                    },
                    closeSuggestions() {
                        this.showSuggestions = false;
                        this.highlightedIndex = -1;
                    },
                    highlightNext() {
                        if (!this.suggestions.length) {
                            return;
                        }
                        this.showSuggestions = true;
                        this.highlightedIndex = (this.highlightedIndex + 1) % this.suggestions.length;
                    },
                    highlightPrevious() {
                        if (!this.suggestions.length) {
                            return;
                        }
                        this.showSuggestions = true;
                        this.highlightedIndex = this.highlightedIndex <= 0 ?
                            this.suggestions.length - 1 :
                            this.highlightedIndex - 1;
                    },
                    selectHighlighted() {
                        const highlighted = this.suggestions[this.highlightedIndex];
                        if (this.showSuggestions && this.highlightedIndex >= 0 && highlighted) {
                            this.selectPostcode(highlighted);
                            return;
                        }
                        this.lookupExactPostcode();
                    },
                    async searchPostcodes() {
                        const query = this.normalizePostcode(this.address);
                        const token = ++this.searchToken;

                        if (query.length < 2) {
                            this.suggestions = [];
                            this.closeSuggestions();
                            this.geocodeError = '';
                            return;
                        }

                        this.isSearching = true;
                        this.geocodeError = '';

                        try {
                            const autocompleteResponse = await fetch(
                                `https://api.postcodes.io/postcodes/${encodeURIComponent(query)}/autocomplete`,
                            );
                            const autocompleteData = await autocompleteResponse.json();

                            if (token !== this.searchToken) {
                                return;
                            }

                            const postcodes = Array.isArray(autocompleteData?.result) ?
                                autocompleteData.result.slice(0, 8) : [];

                            if (!postcodes.length) {
                                this.suggestions = [];
                                this.closeSuggestions();
                                this.geocodeError = query.length >= 3 ?
                                    'No UK postcodes found. Try a different search.' :
                                    '';
                                return;
                            }

                            const bulkResponse = await fetch('https://api.postcodes.io/postcodes', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    Accept: 'application/json',
                                },
                                body: JSON.stringify({
                                    postcodes,
                                }),
                            });
                            const bulkData = await bulkResponse.json();

                            if (token !== this.searchToken) {
                                return;
                            }

                            const resolved = (bulkData?.result || [])
                                .filter((entry) => entry?.result)
                                .map((entry) => ({
                                    postcode: entry.result.postcode,
                                    label: [entry.result.admin_district, entry.result
                                        .region
                                    ]
                                        .filter(Boolean)
                                        .join(', '),
                                    lat: entry.result.latitude,
                                    lng: entry.result.longitude,
                                }));

                            const resolvedCodes = new Set(resolved.map((item) => item.postcode));
                            postcodes.forEach((postcode) => {
                                if (!resolvedCodes.has(postcode)) {
                                    resolved.push({
                                        postcode,
                                        label: 'United Kingdom',
                                        lat: null,
                                        lng: null,
                                    });
                                }
                            });

                            this.suggestions = resolved;
                            this.showSuggestions = this.suggestions.length > 0;
                            this.highlightedIndex = this.showSuggestions ? 0 : -1;
                        } catch (error) {
                            if (token !== this.searchToken) {
                                return;
                            }
                            this.suggestions = [];
                            this.closeSuggestions();
                            this.geocodeError =
                                'Unable to search postcodes right now. Please try again.';
                        } finally {
                            if (token === this.searchToken) {
                                this.isSearching = false;
                            }
                        }
                    },
                    async selectPostcode(item) {
                        if (!item) {
                            return;
                        }

                        if (item.lat !== null && item.lng !== null) {
                            this.applyPostcode(item.postcode, item.lat, item.lng);
                            return;
                        }

                        await this.lookupExactPostcode(item.postcode);
                    },
                    async lookupExactPostcode(forcedPostcode = null) {
                        const postcode = this.normalizePostcode(forcedPostcode ?? this.address);
                        if (!postcode) {
                            this.geocodeError = 'Enter a UK postcode to search.';
                            return;
                        }

                        this.isSearching = true;
                        this.geocodeError = '';

                        try {
                            const response = await fetch(
                                `https://api.postcodes.io/postcodes/${encodeURIComponent(postcode)}`,
                            );
                            const data = await response.json();

                            if (data.status !== 200 || !data.result) {
                                this.geocodeError = 'Enter a valid UK postcode.';
                                return;
                            }

                            this.applyPostcode(
                                data.result.postcode,
                                data.result.latitude,
                                data.result.longitude,
                            );
                        } catch (error) {
                            this.geocodeError =
                                'Unable to look up that postcode. Please try again.';
                        } finally {
                            this.isSearching = false;
                        }
                    },
                    applyPostcode(postcode, lat, lng) {
                        this.address = postcode;
                        this.latitude = Number(lat);
                        this.longitude = Number(lng);
                        this.closeSuggestions();
                        this.geocodeError = '';
                        this.map?.setView([this.latitude, this.longitude], 14);
                        this.updatePreview();
                    },
                    async reverseGeocodeFromMap(lat, lng) {
                        try {
                            const response = await fetch(
                                `https://api.postcodes.io/postcodes?lon=${encodeURIComponent(lng)}&lat=${encodeURIComponent(lat)}`,
                            );
                            const data = await response.json();
                            const nearest = Array.isArray(data?.result) ? data.result[0] : null;
                            if (nearest) {
                                this.address = nearest;
                            }
                        } catch (error) {
                            // Keep existing postcode text when reverse lookup fails.
                        }
                    },
                    initMap() {
                        if (typeof L === 'undefined') {
                            if (this.initMapAttempt++ < 50) {
                                setTimeout(() => this.initMap(), 100);
                            }
                            return;
                        }

                        const mapEl = document.getElementById('service-area-form-map');
                        if (!mapEl) {
                            return;
                        }

                        this.initMapAttempt = 0;

                        if (this.map) {
                            setTimeout(() => this.map?.invalidateSize(), 120);
                            return;
                        }

                        this.map = L.map(mapEl, {
                            zoomControl: true,
                            attributionControl: false,
                            preferCanvas: true,
                        });

                        L.tileLayer(
                            'https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
                            subdomains: 'abcd',
                            maxZoom: 20,
                        }).addTo(this.map);

                        L.tileLayer(
                            'https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
                            subdomains: 'abcd',
                            maxZoom: 20,
                            pane: 'overlayPane',
                        }).addTo(this.map);

                        const pinIcon = L.divIcon({
                            className: 'service-area-pin-wrap',
                            html: `<span class="service-area-pin" aria-hidden="true"></span>`,
                            iconSize: [22, 30],
                            iconAnchor: [11, 15],
                        });

                        this.marker = L.marker([this.latitude, this.longitude], {
                            icon: pinIcon,
                            draggable: true,
                        }).addTo(this.map);

                        this.marker.on('dragend', () => {
                            const {
                                lat,
                                lng
                            } = this.marker.getLatLng();
                            this.latitude = lat;
                            this.longitude = lng;
                            this.reverseGeocodeFromMap(lat, lng);
                        });

                        this.circle = L.circle([this.latitude, this.longitude], this.circleOptions())
                            .addTo(
                                this.map);
                        this.map.setView([this.latitude, this.longitude], 13);

                        this.map.on('click', (event) => {
                            this.latitude = event.latlng.lat;
                            this.longitude = event.latlng.lng;
                            this.reverseGeocodeFromMap(event.latlng.lat, event.latlng.lng);
                        });

                        setTimeout(() => this.refreshMap(), 140);
                    },
                    circleOptions() {
                        return {
                            radius: this.radiusMetersFromModel(),
                            color: '#B8D4E8',
                            fillColor: '#B8D4E8',
                            fillOpacity: 0.35,
                            weight: 3,
                            opacity: 0.9,
                        };
                    },
                    radiusMetersFromModel() {
                        const r = Number(this.radius);
                        const miles = Number.isFinite(r) && r > 0 ?
                            Math.min(50, Math.max(0.1, r)) :
                            1;

                        return miles * METERS_PER_STATUTE_MILE;
                    },
                    cancelRadiusAnimation() {
                        if (this.radiusAnimFrameId !== null) {
                            cancelAnimationFrame(this.radiusAnimFrameId);
                            this.radiusAnimFrameId = null;
                        }
                    },
                    animateCircleRadiusToModel() {
                        if (!this.circle) {
                            return;
                        }

                        this.cancelRadiusAnimation();

                        const target = this.radiusMetersFromModel();
                        const from = this.circle.getRadius();
                        if (Math.abs(from - target) < 0.25) {
                            this.circle.setRadius(target);
                            return;
                        }

                        const durationMs = 420;
                        const start = performance.now();

                        const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

                        const step = (now) => {
                            const elapsed = now - start;
                            const t = Math.min(1, elapsed / durationMs);
                            const eased = easeOutCubic(t);
                            this.circle.setRadius(from + (target - from) * eased);

                            if (t < 1) {
                                this.radiusAnimFrameId = requestAnimationFrame(step);
                            } else {
                                this.circle.setRadius(target);
                                this.radiusAnimFrameId = null;
                            }
                        };

                        this.radiusAnimFrameId = requestAnimationFrame(step);
                    },
                    updatePreview() {
                        if (!this.map || !this.marker || !this.circle) {
                            return;
                        }

                        const latLng = [Number(this.latitude), Number(this.longitude)];
                        this.marker.setLatLng(latLng);
                        this.circle.setLatLng(latLng);
                        this.animateCircleRadiusToModel();
                    },
                    refreshMap() {
                        this.initMapAttempt = 0;
                        setTimeout(() => {
                            if (!this.map) {
                                this.initMap();
                                return;
                            }

                            this.map.invalidateSize();
                            this.updatePreview();
                        }, 280);
                    },
                }));
            };

            document.addEventListener('alpine:init', register);
            if (window.Alpine) {
                register();
            }
        })();
    </script>
@endpush

<style>
    .service-area-form-panel {
        margin-top: 0;
    }

    .service-area-form {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .service-area-form-layout {
        display: grid;
        grid-template-columns: minmax(280px, 38%) minmax(0, 1fr);
        gap: 0;
        align-items: stretch;
    }

    .service-area-form-fields {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        padding-right: 1.5rem;
        padding-top: 1.5rem;
    }

    .service-field {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .service-field>span {
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    .service-field input,
    .service-field select {
        width: 100%;
        height: 48px;
        border: 1px solid #d9d9d9;
        border-radius: 10px;
        background: #fff;
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;
        padding: 0.65rem 0.9rem;
    }

    .service-area-postcode-field {
        position: relative;
    }

    .service-area-postcode-wrap {
        position: relative;
    }

    .service-area-postcode-suggestions {
        position: absolute;
        top: calc(100% + 0.35rem);
        left: 0;
        right: 0;
        z-index: 30;
        margin: 0;
        padding: 0.35rem 0;
        list-style: none;
        max-height: 240px;
        overflow-y: auto;
        border: 1px solid #e2e2e2;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(59, 55, 49, 0.12);
    }

    .service-area-postcode-suggestion {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.15rem;
        padding: 0.7rem 0.9rem;
        border: 0;
        background: transparent;
        text-align: left;
        cursor: pointer;
        font-family: Lato;
    }

    .service-area-postcode-suggestion:hover,
    .service-area-postcode-suggestion.is-active {
        background: rgba(216, 232, 183, 0.35);
    }

    .service-area-postcode-code {
        color: #3b3731;
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .service-area-postcode-place {
        color: #9d9b98;
        font-size: 14px;
        font-weight: 400;
    }

    .service-area-searching-hint {
        margin: 0.35rem 0 0;
        color: #9d9b98;
        font-family: Lato;
        font-size: 14px;
    }

    .service-area-field-hint {
        margin: 0.35rem 0 0;
        font-family: Lato;
        font-size: 14px;
    }

    .service-area-field-hint-error {
        color: #c45c5c;
    }

    .service-area-radius-hint {
        margin: 0;
        color: #9d9b98;
        font-family: Lato;
        font-size: 13px;
        font-weight: 400;
        line-height: 1.35;
    }

    .service-area-map-hint {
        margin: 0;
        color: #9d9b98;
        font-family: Lato;
        font-size: 14px;
        line-height: 1.4;
    }

    .service-area-form-map-col {
        min-height: 420px;
    }

    .service-area-form-map-col .service-area-map-shell {
        width: 100%;
        height: 505px;
        aspect-ratio: 122/101;
        border-radius: 10px;
        overflow: hidden;
        background: #f4f4f4;
        margin-left: auto;
    }

    .service-area-form-map-col .service-area-map {
        width: 100%;
        height: 505px;
        aspect-ratio: 122/101;
        border-radius: 10px;
    }

    .service-area-form-map-col .service-area-map.leaflet-container,
    .service-area-form-map-col .service-area-map .leaflet-container {
        width: 100%;
        height: 505px;
        aspect-ratio: 122/101;
    }

    .service-area-form-map-col .service-area-map .leaflet-tile-pane {
        filter: grayscale(1);
    }

    .service-area-form-panel .service-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 0.5rem;
    }

    .service-area-form-panel .service-form-btn {
        min-width: 133px;
        height: 48px;
        border-radius: 75px;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
    }

    .service-area-form-panel .service-form-btn-cancel {
        border: 1px solid #3b3731;
        background: transparent;
        color: #3b3731;
    }

    .service-area-form-panel .service-form-btn-save {
        border: none;
        background: #bacf8e;
        color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .service-area-form-panel .save-btn-loading {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 90px;
    }

    .service-area-form-panel .save-spinner {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        animation: service-area-save-spin 0.8s linear infinite;
    }

    @keyframes service-area-save-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .service-area-pin-wrap {
        background: transparent !important;
        border: none !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .service-area-pin {
        display: block;
        width: 22px;
        height: 30px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='34' height='48' viewBox='0 0 34 48' fill='none'%3E%3Cpath d='M17 22.8C15.3898 22.8 13.8455 22.1679 12.7069 21.0426C11.5682 19.9174 10.9286 18.3913 10.9286 16.8C10.9286 15.2087 11.5682 13.6826 12.7069 12.5574C13.8455 11.4321 15.3898 10.8 17 10.8C18.6102 10.8 20.1545 11.4321 21.2931 12.5574C22.4318 13.6826 23.0714 15.2087 23.0714 16.8C23.0714 17.5879 22.9144 18.3681 22.6093 19.0961C22.3042 19.8241 21.8569 20.4855 21.2931 21.0426C20.7294 21.5998 20.0601 22.0417 19.3234 22.3433C18.5868 22.6448 17.7973 22.8 17 22.8ZM17 0C12.4913 0 8.1673 1.76999 4.97918 4.92061C1.79107 8.07122 0 12.3444 0 16.8C0 29.4 17 48 17 48C17 48 34 29.4 34 16.8C34 12.3444 32.2089 8.07122 29.0208 4.92061C25.8327 1.76999 21.5087 0 17 0Z' fill='%23FFC97A'/%3E%3C/svg%3E");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center center;
    }

    .service-area-form-panel .hidden {
        display: none !important;
    }

    @media (max-width: 992px) {
        .service-area-form-layout {
            grid-template-columns: 1fr;
        }

        .service-area-form-fields {
            padding-right: 0;
        }

        .service-area-form-map-col .service-area-map-shell {
            border-radius: 12px;
            margin-top: 0.5rem;
        }
    }
</style>