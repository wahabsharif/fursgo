<?php

use App\Models\ServiceArea;
use Livewire\Volt\Component;

new class extends Component {
    public const MAP_COLORS = ['#B8D4E8', '#FFD4B8', '#C8E8B8'];

    public ?int $editingId = null;

    public string $name = '';

    public string $address = '';

    public float $radius = 1;

    public ?float $latitude = 51.5074;

    public ?float $longitude = -0.1278;

    public bool $isActive = true;

    private function profileId(): ?int
    {
        $id = auth('groomer_spacer')->id() ?? auth()->id();

        return $id ? (int) $id : null;
    }

    public function loadArea(int $areaId, bool $silent = false): void
    {
        $profileId = $this->profileId();
        $area = ServiceArea::query()->when($profileId, fn($query) => $query->where('groomer_spacer_id', $profileId))->find($areaId);

        if (!$area) {
            return;
        }

        $this->editingId = (int) $area->id;
        $this->name = (string) $area->name;
        $this->address = (string) ($area->address ?? '');
        $this->radius = (float) $area->radius;
        $this->latitude = (float) $area->latitude;
        $this->longitude = (float) $area->longitude;
        $this->isActive = !$area->is_paused;

        if ($silent) {
            $this->skipRender();
        }

        $this->js('window.dispatchEvent(new CustomEvent("service-form-baseline"))');
    }

    #[\Livewire\Attributes\On('reset-service-area-form')]
    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->address = '';
        $this->radius = 1;
        $this->latitude = 51.5074;
        $this->longitude = -0.1278;
        $this->isActive = true;
        $this->js('window.dispatchEvent(new CustomEvent("service-form-baseline"))');
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'radius' => ['required', 'numeric', 'min:0.2', 'max:10'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $profileId = $this->profileId();

        if (!$profileId) {
            $this->addError('name', 'Groomer profile not found for current user.');
            return;
        }

        $payload = [
            'name' => $this->name,
            'radius' => $this->radius,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address !== '' ? $this->address : null,
            'is_paused' => !$this->isActive,
        ];

        if ($this->editingId) {
            $serviceArea = ServiceArea::query()->where('groomer_spacer_id', $profileId)->find($this->editingId);
            if (!$serviceArea) {
                $this->addError('name', 'Service area not found.');
                return;
            }
            $serviceArea->update($payload);
        } else {
            $existingCount = ServiceArea::query()->where('groomer_spacer_id', $profileId)->count();
            $serviceArea = ServiceArea::create(
                array_merge($payload, [
                    'groomer_spacer_id' => $profileId,
                    'map_color' => self::MAP_COLORS[$existingCount % count(self::MAP_COLORS)],
                ]),
            );
        }

        $this->dispatch('service-area-created', itemId: $serviceArea->id);
        $this->resetForm();
    }

    public function deleteCurrent(): void
    {
        $profileId = $this->profileId();

        if (!$profileId || !$this->editingId) {
            return;
        }

        $area = ServiceArea::query()->where('groomer_spacer_id', $profileId)->find($this->editingId);
        if ($area) {
            $area->delete();
        }

        $this->dispatch('service-area-created');
        $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end")); window.dispatchEvent(new CustomEvent("service-form-cancel"))');
    }
}; ?>

<section class="service-area-form-panel" aria-label="Add service area form"
    x-data="serviceAreaFormMap(@entangle('latitude').live, @entangle('longitude').live, @entangle('radius').live, @entangle('address'), @entangle('isActive').live, @entangle('name'))"
    x-on:service-area-form-delete.window="$wire.deleteCurrent()"
    x-on:service-area-edit-requested.window="
        const d = $event.detail || {};
        if (d.state) { window.applyServiceFormState?.($wire, d.state); }
        const id = Number(d.areaId || 0);
        if (id) { $wire.loadArea(id, !!d.state); }
    ">
    <form class="service-area-form" wire:submit.prevent="save">
        <input type="hidden" data-service-area-map-dirty value="">

        <div class="service-form-card service-area-identity-card">
            <h4 class="service-section-title">Service Area</h4>
            <div class="service-area-identity-grid">
                <div class="service-field service-area-suggest-field" @click.outside="closeNameSuggestions()">
                    <span>Service Area Name</span>
                    <div class="service-area-postcode-wrap">
                        <input type="text" x-model="name" placeholder="e.g. Waterloo South Bank"
                            autocomplete="off" spellcheck="false" role="combobox"
                            aria-autocomplete="list"
                            :aria-expanded="showNameSuggestions && nameSuggestions.length > 0"
                            aria-controls="service-area-name-suggestions"
                            @input.debounce.300ms="searchNameSuggestions()"
                            @input="window.dispatchEvent(new CustomEvent('service-form-title-changed', { detail: { title: $event.target.value } }))"
                            @keydown.arrow-down.prevent="highlightNext('name')"
                            @keydown.arrow-up.prevent="highlightPrevious('name')"
                            @keydown.enter.prevent="selectHighlighted('name')"
                            @keydown.escape.prevent="closeNameSuggestions()"
                            @focus="searchNameSuggestions()" />
                        <ul id="service-area-name-suggestions" class="service-area-postcode-suggestions"
                            x-show="showNameSuggestions && nameSuggestions.length > 0" x-cloak role="listbox">
                            <template x-for="(item, index) in nameSuggestions" :key="item.id">
                                <li role="option" :aria-selected="nameHighlightedIndex === index">
                                    <button type="button" class="service-area-postcode-suggestion"
                                        :class="{ 'is-active': nameHighlightedIndex === index }"
                                        @mousedown.prevent="selectNameSuggestion(item)">
                                        <span class="service-area-postcode-code" x-text="item.title"></span>
                                        <span class="service-area-postcode-place" x-text="item.subtitle"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                        <p class="service-area-searching-hint" x-show="isNameSearching" x-cloak>Searching places…</p>
                    </div>
                </div>

                <div class="service-field service-area-suggest-field service-area-postcode-field"
                    @click.outside="closeCentreSuggestions()">
                    <span>Centre Point</span>
                    <div class="service-area-postcode-wrap">
                        <input type="text" x-model="address"
                            placeholder="Start typing a postcode, street or landmark ..."
                            autocomplete="off" spellcheck="false" role="combobox"
                            aria-autocomplete="list"
                            :aria-expanded="showCentreSuggestions && centreSuggestions.length > 0"
                            aria-controls="service-area-centre-suggestions"
                            @input.debounce.300ms="searchCentreSuggestions()"
                            @keydown.arrow-down.prevent="highlightNext('centre')"
                            @keydown.arrow-up.prevent="highlightPrevious('centre')"
                            @keydown.enter.prevent="selectHighlighted('centre')"
                            @keydown.escape.prevent="closeCentreSuggestions()"
                            @focus="searchCentreSuggestions()" />
                        <ul id="service-area-centre-suggestions" class="service-area-postcode-suggestions"
                            x-show="showCentreSuggestions && centreSuggestions.length > 0" x-cloak role="listbox">
                            <template x-for="(item, index) in centreSuggestions" :key="item.id">
                                <li role="option" :aria-selected="centreHighlightedIndex === index">
                                    <button type="button" class="service-area-postcode-suggestion"
                                        :class="{ 'is-active': centreHighlightedIndex === index }"
                                        @mousedown.prevent="selectCentreSuggestion(item)">
                                        <span class="service-area-postcode-code" x-text="item.title"></span>
                                        <span class="service-area-postcode-place" x-text="item.subtitle"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                        <p class="service-area-searching-hint" x-show="isCentreSearching" x-cloak>Searching locations…</p>
                    </div>
                    <p class="service-area-field-hint service-area-field-hint-error" x-show="geocodeError"
                        x-text="geocodeError" x-cloak></p>
                </div>
            </div>
        </div>

        <div class="service-form-card service-area-radius-card">
            <div class="service-area-radius-layout">
                <div class="service-area-radius-controls">
                    <h4 class="service-section-title service-area-radius-label">Service radius</h4>
                    <div class="service-area-radius-value-row">
                        <label class="service-field service-area-radius-input-field">
                            <span class="sr-only">Radius in miles</span>
                            <input type="number" min="0.2" max="10" step="0.1" wire:model.live="radius"
                                aria-label="Service radius in miles" />
                        </label>
                        <p class="service-area-radius-readout">
                            <span x-text="Number(radius || 0).toFixed(1)"></span>
                            <small>miles</small>
                        </p>
                    </div>
                    <div class="service-area-slider">
                        <input type="range" min="0.2" max="10" step="0.1" wire:model.live="radius"
                            aria-label="Service radius slider"
                            :style="`--p: ${(Number(radius) - 0.2) / 9.8}`" />
                        <img class="service-area-slider-thumb" src="{{ asset('images/business-hub/icon-radius-thumb.svg') }}"
                            alt="" width="39" height="39" :style="`--p: ${(Number(radius) - 0.2) / 9.8}`">
                        <div class="service-area-slider-labels" aria-hidden="true">
                            <span>0.2 mi</span>
                            <span>2.5 mi</span>
                            <span>5 mi</span>
                            <span>10 mi</span>
                        </div>
                    </div>
                    <p class="service-area-coverage">
                        <img src="{{ asset('images/business-hub/icon-radius-info.svg') }}" alt="" width="13" height="13">
                        <span x-text="coverageLabel()"></span>
                    </p>
                </div>

                <div class="service-area-form-map-col">
                    <div class="service-area-map-shell" wire:ignore>
                        <div id="service-area-form-map" class="service-area-map" role="img"
                            aria-label="Service area location picker"></div>
                        <p class="service-area-zoom-hint">Hold Ctrl to Zoom in &amp; out</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="service-form-card service-settings-card">
            <h4 class="service-section-title">Setting Options</h4>
            <div class="service-setting-row" :class="{ 'is-active': isActive }" role="switch"
                :aria-checked="isActive.toString()" tabindex="0" aria-label="Visibility controls"
                @click="isActive = !isActive"
                @keydown.enter.prevent="isActive = !isActive"
                @keydown.space.prevent="isActive = !isActive">
                <div>
                    <strong>Visibility Controls</strong>
                    <p x-text="isActive ? 'This is an active area - clients within this radius can book home visits' : 'This area is paused - clients cannot book home visits here'"></p>
                </div>
                <span class="service-toggle" :class="{ 'is-on': isActive }" aria-hidden="true"></span>
            </div>
        </div>

        <x-business-hub.services.form-footer />
    </form>
</section>

@push('script')
    <script>
        (() => {
            const register = () => {
                if (typeof Alpine === 'undefined') {
                    return;
                }

                const registryVersion = 2;
                if (window.__serviceAreaFormMapRegistered === registryVersion) {
                    return;
                }

                window.__serviceAreaFormMapRegistered = registryVersion;

                const METERS_PER_STATUTE_MILE = {{ json_encode(\App\Models\ServiceArea::METERS_PER_STATUTE_MILE) }};

                Alpine.data('serviceAreaFormMap', (latitude, longitude, radius, address, isActive, name) => ({
                    latitude,
                    longitude,
                    radius,
                    address,
                    isActive,
                    name,
                    map: null,
                    marker: null,
                    circle: null,
                    geocodeError: '',
                    nameSuggestions: [],
                    centreSuggestions: [],
                    showNameSuggestions: false,
                    showCentreSuggestions: false,
                    nameHighlightedIndex: -1,
                    centreHighlightedIndex: -1,
                    isNameSearching: false,
                    isCentreSearching: false,
                    nameSearchToken: 0,
                    centreSearchToken: 0,
                    initMapAttempt: 0,
                    radiusAnimFrameId: null,
                    coverageLabel() {
                        const miles = Number(this.radius);
                        const safe = Number.isFinite(miles) ? miles : 0;
                        const km = safe * 1.609344;
                        const area = Math.PI * km * km;

                        return `A ${safe.toFixed(1)} mile radius covers roughly ${area.toFixed(1)} km²`;
                    },
                    markMapDirty() {
                        this.$root.querySelector('[data-service-area-map-dirty]')?.dispatchEvent(new Event('input', {
                            bubbles: true
                        }));
                    },
                    mapElementVisible(mapEl) {
                        if (!mapEl) {
                            return false;
                        }
                        const rect = mapEl.getBoundingClientRect();
                        return rect.width > 0 && rect.height > 0;
                    },
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
                    closeNameSuggestions() {
                        this.showNameSuggestions = false;
                        this.nameHighlightedIndex = -1;
                    },
                    closeCentreSuggestions() {
                        this.showCentreSuggestions = false;
                        this.centreHighlightedIndex = -1;
                    },
                    closeSuggestions() {
                        this.closeNameSuggestions();
                        this.closeCentreSuggestions();
                    },
                    activeSuggestions(field) {
                        return field === 'name' ? this.nameSuggestions : this.centreSuggestions;
                    },
                    highlightNext(field) {
                        const list = this.activeSuggestions(field);
                        if (!list.length) {
                            return;
                        }
                        if (field === 'name') {
                            this.showNameSuggestions = true;
                            this.nameHighlightedIndex = (this.nameHighlightedIndex + 1) % list.length;
                            return;
                        }
                        this.showCentreSuggestions = true;
                        this.centreHighlightedIndex = (this.centreHighlightedIndex + 1) % list.length;
                    },
                    highlightPrevious(field) {
                        const list = this.activeSuggestions(field);
                        if (!list.length) {
                            return;
                        }
                        if (field === 'name') {
                            this.showNameSuggestions = true;
                            this.nameHighlightedIndex = this.nameHighlightedIndex <= 0 ?
                                list.length - 1 :
                                this.nameHighlightedIndex - 1;
                            return;
                        }
                        this.showCentreSuggestions = true;
                        this.centreHighlightedIndex = this.centreHighlightedIndex <= 0 ?
                            list.length - 1 :
                            this.centreHighlightedIndex - 1;
                    },
                    selectHighlighted(field) {
                        if (field === 'name') {
                            const item = this.nameSuggestions[this.nameHighlightedIndex];
                            if (this.showNameSuggestions && item) {
                                this.selectNameSuggestion(item);
                            }
                            return;
                        }

                        const item = this.centreSuggestions[this.centreHighlightedIndex];
                        if (this.showCentreSuggestions && item) {
                            this.selectCentreSuggestion(item);
                            return;
                        }
                        this.lookupExactPostcode();
                    },
                    async fetchPlaces(query) {
                        try {
                            const response = await fetch(
                                `https://api.postcodes.io/places?q=${encodeURIComponent(query)}`,
                            );
                            const data = await response.json();
                            if (!Array.isArray(data?.result)) {
                                return [];
                            }

                            return data.result.slice(0, 8).map((place) => ({
                                id: `place-${place.code}`,
                                title: place.name_1,
                                subtitle: [place.local_type, place.county_unitary || place.district_borough || place
                                        .region
                                    ]
                                    .filter(Boolean)
                                    .join(', '),
                                lat: place.latitude ?? null,
                                lng: place.longitude ?? null,
                                postcode: place.outcode || null,
                                kind: 'place',
                            }));
                        } catch (error) {
                            return [];
                        }
                    },
                    async fetchPostcodeSuggestions(query) {
                        try {
                            const normalized = this.normalizePostcode(query);
                            if (normalized.length < 2 || !/[0-9]/.test(normalized)) {
                                return [];
                            }

                            const autocompleteResponse = await fetch(
                                `https://api.postcodes.io/postcodes/${encodeURIComponent(normalized)}/autocomplete`,
                            );
                            const autocompleteData = await autocompleteResponse.json();
                            const postcodes = Array.isArray(autocompleteData?.result) ?
                                autocompleteData.result.slice(0, 8) : [];

                            if (!postcodes.length) {
                                return [];
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
                            const resolved = (bulkData?.result || [])
                                .filter((entry) => entry?.result)
                                .map((entry) => ({
                                    id: `pc-${entry.result.postcode}`,
                                    title: entry.result.postcode,
                                    subtitle: [entry.result.admin_district, entry.result.region]
                                        .filter(Boolean)
                                        .join(', '),
                                    lat: entry.result.latitude,
                                    lng: entry.result.longitude,
                                    postcode: entry.result.postcode,
                                    kind: 'postcode',
                                }));

                            const resolvedCodes = new Set(resolved.map((item) => item.postcode));
                            postcodes.forEach((postcode) => {
                                if (!resolvedCodes.has(postcode)) {
                                    resolved.push({
                                        id: `pc-${postcode}`,
                                        title: postcode,
                                        subtitle: 'United Kingdom',
                                        lat: null,
                                        lng: null,
                                        postcode,
                                        kind: 'postcode',
                                    });
                                }
                            });

                            return resolved;
                        } catch (error) {
                            return [];
                        }
                    },
                    async searchNameSuggestions() {
                        const query = String(this.name || '').trim();
                        const token = ++this.nameSearchToken;

                        if (query.length < 2) {
                            this.nameSuggestions = [];
                            this.closeNameSuggestions();
                            return;
                        }

                        this.isNameSearching = true;
                        this.closeCentreSuggestions();

                        try {
                            const places = await this.fetchPlaces(query);
                            if (token !== this.nameSearchToken) {
                                return;
                            }
                            this.nameSuggestions = places;
                            this.showNameSuggestions = places.length > 0;
                            this.nameHighlightedIndex = places.length ? 0 : -1;
                        } catch (error) {
                            if (token !== this.nameSearchToken) {
                                return;
                            }
                            this.nameSuggestions = [];
                            this.closeNameSuggestions();
                        } finally {
                            if (token === this.nameSearchToken) {
                                this.isNameSearching = false;
                            }
                        }
                    },
                    async searchCentreSuggestions() {
                        const query = String(this.address || '').trim();
                        const token = ++this.centreSearchToken;

                        if (query.length < 2) {
                            this.centreSuggestions = [];
                            this.closeCentreSuggestions();
                            this.geocodeError = '';
                            return;
                        }

                        this.isCentreSearching = true;
                        this.geocodeError = '';
                        this.closeNameSuggestions();

                        try {
                            const [placesResult, postcodesResult] = await Promise.allSettled([
                                this.fetchPlaces(query),
                                this.fetchPostcodeSuggestions(query),
                            ]);

                            if (token !== this.centreSearchToken) {
                                return;
                            }

                            const places = placesResult.status === 'fulfilled' ? placesResult.value : [];
                            const postcodes = postcodesResult.status === 'fulfilled' ? postcodesResult.value : [];
                            const looksLikePostcode = /\d/.test(query);
                            const merged = looksLikePostcode ? [...postcodes, ...places] : [...places, ...postcodes];
                            const seen = new Set();
                            this.centreSuggestions = merged.filter((item) => {
                                if (!item?.id || seen.has(item.id)) {
                                    return false;
                                }
                                seen.add(item.id);
                                return true;
                            }).slice(0, 8);

                            this.showCentreSuggestions = this.centreSuggestions.length > 0;
                            this.centreHighlightedIndex = this.showCentreSuggestions ? 0 : -1;

                            if (!this.centreSuggestions.length && query.length >= 3) {
                                this.geocodeError = 'No matching locations found. Try a different search.';
                            }
                        } catch (error) {
                            if (token !== this.centreSearchToken) {
                                return;
                            }
                            this.centreSuggestions = [];
                            this.closeCentreSuggestions();
                            this.geocodeError = 'Unable to search locations right now. Please try again.';
                        } finally {
                            if (token === this.centreSearchToken) {
                                this.isCentreSearching = false;
                            }
                        }
                    },
                    selectNameSuggestion(item) {
                        if (!item) {
                            return;
                        }
                        this.name = item.title;
                        this.closeNameSuggestions();
                        window.dispatchEvent(new CustomEvent('service-form-title-changed', {
                            detail: {
                                title: item.title,
                            },
                        }));
                    },
                    async selectCentreSuggestion(item) {
                        if (!item) {
                            return;
                        }

                        if (item.lat !== null && item.lng !== null) {
                            this.applyCentrePoint(
                                item.kind === 'postcode' ? item.postcode : [item.title, item.subtitle].filter(Boolean).join(', '),
                                item.lat,
                                item.lng,
                            );
                            return;
                        }

                        if (item.postcode) {
                            await this.lookupExactPostcode(item.postcode);
                        }
                    },
                    async lookupExactPostcode(forcedPostcode = null) {
                        const postcode = this.normalizePostcode(forcedPostcode ?? this.address);
                        if (!postcode) {
                            this.geocodeError = 'Enter a UK postcode or place to search.';
                            return;
                        }

                        this.isCentreSearching = true;
                        this.geocodeError = '';

                        try {
                            const response = await fetch(
                                `https://api.postcodes.io/postcodes/${encodeURIComponent(postcode)}`,
                            );
                            const data = await response.json();

                            if (data.status !== 200 || !data.result) {
                                this.geocodeError = 'Enter a valid UK postcode or choose a suggested place.';
                                return;
                            }

                            this.applyCentrePoint(
                                data.result.postcode,
                                data.result.latitude,
                                data.result.longitude,
                            );
                        } catch (error) {
                            this.geocodeError =
                                'Unable to look up that location. Please try again.';
                        } finally {
                            this.isCentreSearching = false;
                        }
                    },
                    applyCentrePoint(label, lat, lng) {
                        this.address = label;
                        this.latitude = Number(lat);
                        this.longitude = Number(lng);
                        this.closeCentreSuggestions();
                        this.geocodeError = '';
                        this.map?.setView([this.latitude, this.longitude], 14);
                        this.updatePreview();
                        this.markMapDirty();
                    },
                    applyPostcode(postcode, lat, lng) {
                        this.applyCentrePoint(postcode, lat, lng);
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

                        if (!this.mapElementVisible(mapEl)) {
                            if (this.initMapAttempt++ < 50) {
                                setTimeout(() => this.initMap(), 150);
                            }
                            return;
                        }

                        this.initMapAttempt = 0;

                        if (this.map) {
                            setTimeout(() => this.map?.invalidateSize(), 120);
                            return;
                        }

                        this.map = L.map(mapEl, {
                            zoomControl: false,
                            attributionControl: false,
                            preferCanvas: true,
                        });

                        L.control.zoom({
                            position: 'bottomright',
                        }).addTo(this.map);

                        const cartoKey = @json((string) config('services.carto.api_key'));
                        const cartoQuery = cartoKey ? ('?key=' + encodeURIComponent(cartoKey)) : '';

                        L.tileLayer(
                            'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png' + cartoQuery, {
                                subdomains: 'abcd',
                                maxZoom: 20,
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
                            this.markMapDirty();
                        });

                        this.circle = L.circle([this.latitude, this.longitude], this.circleOptions())
                            .addTo(
                                this.map);
                        this.map.setView([this.latitude, this.longitude], 13);

                        this.map.on('click', (event) => {
                            this.latitude = event.latlng.lat;
                            this.longitude = event.latlng.lng;
                            this.reverseGeocodeFromMap(event.latlng.lat, event.latlng.lng);
                            this.markMapDirty();
                        });

                        setTimeout(() => this.refreshMap(), 140);
                    },
                    circleOptions() {
                        return {
                            radius: this.radiusMetersFromModel(),
                            color: '#FFA899',
                            fillColor: '#FFA899',
                            fillOpacity: 0.35,
                            weight: 3,
                            opacity: 0.9,
                        };
                    },
                    radiusMetersFromModel() {
                        const r = Number(this.radius);
                        const miles = Number.isFinite(r) && r > 0 ?
                            Math.min(10, Math.max(0.2, r)) :
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
        gap: 40px;
    }

    .service-area-form-panel .service-form-card {
        display: flex;
        flex-direction: column;
        gap: 20px;
        background: #FDFDFD;
        border: 1px solid #E6E6E6;
        border-radius: 10px;
        box-shadow: none;
        padding: 20px;
        overflow: visible;
    }

    .service-area-form-panel .service-area-identity-card {
        position: relative;
        z-index: 6;
        overflow: visible;
    }

    .service-area-identity-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        align-items: start;
        overflow: visible;
    }

    .service-area-form-panel .service-area-radius-card {
        gap: 0;
    }

    .service-area-radius-layout {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .service-area-radius-controls {
        display: flex;
        flex-direction: column;
        flex: 0 1 465px;
        width: 100%;
        max-width: 465px;
        min-width: 0;
    }

    .service-area-radius-value-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        width: 100%;
        margin-bottom: 10px;
        margin-top: 20px;
    }

    .service-area-radius-input-field {
        width: 83px;
        flex: 0 0 83px;
        margin: 0;
        gap: 0;
    }

    .service-area-radius-input-field input {
        width: 83px;
        padding: 0 12px;
        -moz-appearance: textfield;
    }

    .service-area-radius-input-field input::-webkit-outer-spin-button,
    .service-area-radius-input-field input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .service-field {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .service-field>span {
        margin: 0;
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .service-area-radius-label {
        margin: 0 0 28px;
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 24px;
        font-weight: 600;
        line-height: normal;
    }

    .service-field input,
    .service-field select {
        width: 100%;
        height: 48px;
        border: 1px solid #d4d4d4;
        border-radius: 10px;
        background: #fff;
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        padding: 0 20px;
    }

    .service-field input::placeholder {
        color: #9d9b98;
    }

    .service-area-radius-readout {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin: 0;
        color: #3b3731;
        font-family: Lato;
        white-space: nowrap;
    }

    .service-area-radius-readout span {
        font-size: 28px;
        font-weight: 600;
        line-height: normal;
    }

    .service-area-radius-readout small {
        font-family: "Playfair Display", serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .service-area-slider {
        position: relative;
        width: 100%;
        margin-bottom: 20px;
    }

    .service-area-slider-thumb {
        position: absolute;
        top: 0;
        left: calc((100% - 39px) * var(--p, 0));
        width: 39px;
        height: 39px;
        max-width: none;
        pointer-events: none;
    }

    .service-area-slider input[type="range"] {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 39px;
        margin: 0;
        background: transparent;
        cursor: pointer;
    }

    .service-area-slider input[type="range"]::-webkit-slider-runnable-track {
        height: 5px;
        background: #ebebeb;
        border-radius: 50px;
    }

    .service-area-slider input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        box-sizing: border-box;
        width: 39px;
        height: 39px;
        margin-top: -17px;
        border: 0;
        border-radius: 50%;
        background: transparent;
        opacity: 0;
        cursor: pointer;
    }

    .service-area-slider input[type="range"]::-moz-range-track {
        height: 5px;
        background: #ebebeb;
        border: 0;
        border-radius: 50px;
    }

    .service-area-slider input[type="range"]::-moz-range-thumb {
        box-sizing: border-box;
        width: 39px;
        height: 39px;
        border: 0;
        border-radius: 50%;
        background: transparent;
        opacity: 0;
        cursor: pointer;
    }

    .service-area-slider-labels {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        margin-top: 2px;
    }

    .service-area-slider-labels span {
        color: #9d9b98;
        font-family: Lato;
        font-size: 12px;
        font-weight: 400;
        line-height: normal;
        white-space: nowrap;
    }

    .service-area-coverage {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        height: 48px;
        margin: 0;
        padding: 0 16px;
        border-radius: 10px;
        background: #f3f7fa;
        color: #6f93ad;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
        box-sizing: border-box;
    }

    .service-area-coverage img {
        width: 13px;
        height: 13px;
        max-width: none;
        flex: 0 0 13px;
    }

    .service-area-coverage span {
        white-space: nowrap;
    }

    .service-area-form-panel .service-section-title,
    .service-area-form-panel .service-settings-card h4 {
        margin: 0;
        padding: 0;
        border: 0;
        color: #3B3731;
        font-family: "Playfair Display", serif;
        font-size: 24px;
        font-weight: 600;
        line-height: normal;
    }

    .service-area-form-panel .service-setting-row {
        margin-bottom: 0;
        cursor: pointer;
        user-select: none;
    }

    .service-area-form-panel .service-setting-row .service-toggle {
        pointer-events: none;
    }

    .service-area-form-panel .service-setting-row.is-active,
    .service-area-form-panel .service-setting-row:has(.service-toggle.is-on) {
        border: 1px solid #BACF8E;
        background: #F1F5E9;
    }

    .service-area-suggest-field,
    .service-area-postcode-field {
        position: relative;
    }

    .service-area-postcode-wrap {
        position: relative;
    }

    .service-area-identity-grid .service-area-suggest-field:first-child .service-area-postcode-suggestions {
        z-index: 45;
    }

    .service-area-postcode-suggestions {
        position: absolute;
        top: calc(100% + 0.35rem);
        left: 0;
        right: 0;
        z-index: 40;
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
        margin: 8px 0 0;
        color: #9d9b98;
        font-family: Lato;
        font-size: 14px;
        font-weight: 400;
        line-height: normal;
    }

    .service-area-field-hint-error {
        color: #c45c5c;
    }

    .service-area-form-map-col {
        position: relative;
        z-index: 1;
        flex: 0 0 505px;
        width: 505px;
        max-width: 100%;
        min-height: 0;
    }

    .service-area-form-map-col .service-area-map-shell {
        position: relative;
        width: 100%;
        height: 350px;
        border: 1px solid #e2e2e2;
        border-radius: 10px;
        overflow: hidden;
        background: #f4f4f4;
        box-shadow: none;
    }

    .service-area-form-map-col .service-area-map,
    .service-area-form-map-col .service-area-map.leaflet-container,
    .service-area-form-map-col .service-area-map .leaflet-container {
        width: 100%;
        height: 100%;
        border-radius: 10px;
        font-family: Lato;
    }

    .service-area-form-panel .service-area-zoom-hint {
        position: absolute;
        left: 15px;
        bottom: 15px;
        z-index: 500;
        display: flex;
        align-items: center;
        height: 35px;
        margin: 0;
        padding: 0 10px;
        border: 1px solid #e2e2e2;
        border-radius: 5px;
        background: #fff;
        color: #737373;
        font-family: Lato;
        font-size: 14px;
        font-weight: 400;
        line-height: normal;
        white-space: nowrap;
        pointer-events: none;
    }

    .service-area-form-panel .leaflet-bottom.leaflet-right {
        margin-right: 15px;
        margin-bottom: 15px;
    }

    .service-area-form-panel .leaflet-control-zoom {
        border: 1px solid #e2e2e2;
        border-radius: 5px;
        box-shadow: none;
        overflow: hidden;
    }

    .service-area-form-panel .leaflet-control-zoom a {
        width: 33px;
        height: 33px;
        line-height: 33px;
        color: #3b3731;
        background: #fff;
        border-bottom-color: #e2e2e2;
    }

    .service-area-form-map-col .service-area-map .leaflet-tile-pane {
        filter: none;
    }

    .service-area-form-panel .save-btn-loading {
        display: inline-flex;
        align-items: center;
        justify-content: center;
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
        .service-area-identity-grid {
            grid-template-columns: 1fr;
        }

        .service-area-radius-layout {
            flex-direction: column;
        }

        .service-area-radius-controls {
            flex: 1 1 auto;
            max-width: none;
        }

        .service-area-form-map-col {
            flex: 1 1 auto;
            width: 100%;
            max-width: none;
        }

        .service-area-form-map-col .service-area-map-shell {
            height: 320px;
        }
    }
</style>
