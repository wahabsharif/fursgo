<?php

use App\Models\ServiceArea;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $selectedAreaId = null;

    public ?int $highlightItemId = null;

    private function getProfileId(): ?int
    {
        $id = auth('groomer_spacer')->id() ?? auth()->id();

        return $id ? (int) $id : null;
    }

    public function getServiceAreasProperty(): array
    {
        $profileId = $this->getProfileId();

        if (!$profileId) {
            return [];
        }

        return ServiceArea::query()
            ->where('groomer_spacer_id', $profileId)
            ->latest()
            ->get()
            ->map(
                fn(ServiceArea $area) => [
                    'id' => $area->id,
                    'name' => $area->name,
                    'radius' => (float) $area->radius,
                    'lat' => (float) $area->latitude,
                    'lng' => (float) $area->longitude,
                    'color' => $area->map_color,
                    'address' => (string) ($area->address ?? ''),
                    'is_paused' => (bool) $area->is_paused,
                ],
            )
            ->all();
    }

    public function selectArea(int $areaId): void
    {
        $this->selectedAreaId = $this->selectedAreaId === $areaId ? null : $areaId;
    }

    public function formatRadius(float $radius): string
    {
        return number_format($radius, 1) . ' mi';
    }

    public function togglePaused(int $areaId): void
    {
        $area = ServiceArea::query()->where('groomer_spacer_id', $this->getProfileId())->find($areaId);
        if (!$area) {
            return;
        }

        $area->update(['is_paused' => !$area->is_paused]);
        $this->refreshList();
    }

    public function deleteArea(int $areaId): void
    {
        $area = ServiceArea::query()->where('groomer_spacer_id', $this->getProfileId())->find($areaId);
        if (!$area) {
            return;
        }

        $area->delete();
        if ($this->selectedAreaId === $areaId) {
            $this->selectedAreaId = null;
        }
        $this->refreshList();
    }

    #[On('service-area-created')]
    public function refreshList(int $itemId = 0): void
    {
        $this->highlightItemId = $itemId > 0 ? $itemId : null;

        $areas = collect($this->serviceAreas)
            ->map(
                fn(array $area) => array_merge($area, [
                    'radiusLabel' => $this->formatRadius((float) $area['radius']),
                ]),
            )
            ->all();

        $this->dispatch('service-area-data-updated', areas: $areas);
    }

    public function clearHighlight(): void
    {
        $this->highlightItemId = null;
    }
}; ?>

@php
    $mapAreas = collect($this->serviceAreas)
        ->map(
            fn($area) => [
                'id' => $area['id'],
                'name' => $area['name'],
                'radius' => $area['radius'],
                'lat' => $area['lat'],
                'lng' => $area['lng'],
                'color' => $area['color'],
                'address' => $area['address'] ?? '',
                'is_paused' => (bool) ($area['is_paused'] ?? false),
                'radiusLabel' => $this->formatRadius((float) $area['radius']),
            ],
        )
        ->values()
        ->all();
@endphp

<section class="service-area-panel" aria-label="Service area list" wire:ignore.self
    x-data="serviceAreaMap(@js($mapAreas), @entangle('selectedAreaId').live)"
    x-on:service-area-map-refresh.window="refreshMap()"
    x-on:service-area-data-updated.window="setAreas($event.detail?.areas ?? [])">
    <div class="service-area-layout">
        <div class="service-area-table-col">
            @php
                $activeCount = collect($this->serviceAreas)->where('is_paused', false)->count();
                $pausedCount = collect($this->serviceAreas)->where('is_paused', true)->count();
            @endphp
            <div class="service-area-card">
                <div class="service-area-card-head">
                    <h4>Your Service Areas <span>{{ $activeCount }} active · {{ $pausedCount }} paused</span></h4>
                </div>
                <div class="service-area-rows">
                    @forelse ($this->serviceAreas as $area)
                        <article wire:key="service-area-row-{{ $area['id'] }}"
                            @class([
                                'service-area-row',
                                'is-selected' => $selectedAreaId === $area['id'],
                                'is-paused' => $area['is_paused'],
                                'is-newly-added' => $highlightItemId === $area['id'],
                            ])
                            @if ($highlightItemId === $area['id']) x-init="setTimeout(() => $wire.clearHighlight(), 2000)" @endif
                            wire:click="selectArea({{ $area['id'] }})" role="button" tabindex="0"
                            @keydown.enter.prevent="$wire.selectArea({{ $area['id'] }})">
                            <div class="service-area-row-copy">
                                <div class="service-area-row-title">
                                    <strong>{{ $area['name'] }}</strong>
                                    @if ($area['is_paused'])
                                        <span class="service-area-paused-badge">Paused</span>
                                    @endif
                                </div>
                                <p>{{ $area['address'] !== '' ? $area['address'] : '—' }}</p>
                            </div>
                            <span class="service-area-row-radius">{{ $this->formatRadius((float) $area['radius']) }}</span>
                            <div class="service-action-btns" wire:click.stop>
                                <button type="button" class="service-action-btn" aria-label="Edit {{ $area['name'] }}"
                                    @click.stop="window.dispatchEvent(new CustomEvent('service-area-edit-requested', { detail: { areaId: {{ $area['id'] }}, title: @js($area['name']), state: { editingId: {{ $area['id'] }}, name: @js($area['name']), address: @js($area['address']), radius: {{ $area['radius'] }}, latitude: {{ $area['lat'] }}, longitude: {{ $area['lng'] }}, isActive: {{ $area['is_paused'] ? 'false' : 'true' }} } } }))">
                                    <x-business-hub.common.icon name="edit" />
                                </button>
                                <div class="service-area-more" x-data="{ open: false }" @click.outside="open = false"
                                    @keydown.escape.window="open = false">
                                    <button type="button" class="service-action-btn" aria-label="Actions for {{ $area['name'] }}"
                                        @click.stop="open = !open">
                                        <x-business-hub.common.icon name="more" />
                                    </button>
                                    <div class="service-area-more-menu" x-cloak x-show="open" x-transition.opacity.duration.120ms>
                                        <button type="button"
                                            wire:click="togglePaused({{ $area['id'] }})"
                                            @click="open = false">
                                            <span>{{ $area['is_paused'] ? 'Resume area' : 'Pause area' }}</span>
                                        </button>
                                        <button type="button" class="is-danger"
                                            wire:click="deleteArea({{ $area['id'] }})"
                                            @click="open = false">
                                            <span>Delete area</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="service-area-empty">No service areas added yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="service-area-map-col">
            <div class="service-area-map-shell" wire:ignore>
                <div id="service-area-map" class="service-area-map" role="img" aria-label="Map of service areas">
                </div>
                <p class="service-area-zoom-hint">Hold Ctrl to Zoom in &amp; out</p>
            </div>
        </div>
    </div>
</section>

@push('script')
    <script>
        (() => {
            const register = () => {
                if (typeof Alpine === 'undefined' || window.__serviceAreaMapRegistered) {
                    return;
                }

                window.__serviceAreaMapRegistered = true;

                const METERS_PER_STATUTE_MILE = {{ json_encode(\App\Models\ServiceArea::METERS_PER_STATUTE_MILE) }};

                Alpine.data('serviceAreaMap', (areas, selectedAreaId) => ({
                    areas: areas ?? [],
                    selectedAreaId,
                    map: null,
                    layers: {},
                    initMapAttempt: 0,
                    init() {
                        this.$nextTick(() => this.initMap());
                        this.$watch('selectedAreaId', () => this.highlightSelected());
                        window.addEventListener('services-menu-selected', (event) => {
                            if (event?.detail?.menu === 'service-area') {
                                setTimeout(() => this.refreshMap(), 220);
                            }
                        });
                    },
                    mapElementVisible(mapEl) {
                        if (!mapEl) {
                            return false;
                        }

                        const rect = mapEl.getBoundingClientRect();
                        return rect.width > 0 && rect.height > 0;
                    },
                    formatRadius(radius) {
                        const value = Number(radius);
                        const label = value === 1 ? 'mile' : 'miles';
                        const formatted = Number.isInteger(value) ? String(value) : value.toFixed(1).replace(/\.0$/, '');

                        return `${formatted} ${label}`;
                    },
                    initMap() {
                        if (typeof L === 'undefined') {
                            if (this.initMapAttempt++ < 50) {
                                setTimeout(() => this.initMap(), 100);
                            }
                            return;
                        }

                        const mapEl = document.getElementById('service-area-map');
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
                            setTimeout(() => this.map?.invalidateSize(), 80);
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

                        this.drawAreas();
                        setTimeout(() => this.map?.invalidateSize(), 120);
                    },
                    clearLayers() {
                        Object.values(this.layers).forEach(({
                            circle,
                            marker
                        }) => {
                            this.map.removeLayer(circle);
                            if (marker && this.map.hasLayer(marker)) {
                                this.map.removeLayer(marker);
                            }
                        });
                        this.layers = {};
                    },
                    drawAreas() {
                        if (!this.map) {
                            return;
                        }

                        const pinIcon = L.divIcon({
                            className: 'service-area-pin-wrap',
                            html: `<span class="service-area-pin" aria-hidden="true"></span>`,
                            iconSize: [22, 30],
                            iconAnchor: [11, 15],
                        });

                        this.areas.forEach((area) => {
                            const radiusMeters = Number(area.radius) * METERS_PER_STATUTE_MILE;
                            const circle = L.circle([area.lat, area.lng], {
                                radius: radiusMeters,
                                color: area.color,
                                fillColor: area.color,
                                fillOpacity: 0.35,
                                weight: 3,
                                opacity: 0.9,
                            }).addTo(this.map);

                            const marker = L.marker([area.lat, area.lng], {
                                icon: pinIcon,
                            });
                            const spokenRadius = this.formatRadius(area.radius);
                            marker.bindTooltip(
                                `<div class="service-area-map-tooltip"><strong>${area.name}</strong><span>Radius: ${spokenRadius}</span></div>`, {
                                    permanent: true,
                                    direction: 'top',
                                    offset: [0, -18],
                                    className: 'service-area-leaflet-tooltip',
                                });

                            this.layers[area.id] = {
                                circle,
                                marker,
                            };
                        });

                        if (this.areas.length) {
                            const bounds = L.latLngBounds(this.areas.map((area) => [area.lat, area
                                .lng
                            ]));
                            this.map.fitBounds(bounds.pad(0.45), {
                                maxZoom: 14,
                            });
                        } else {
                            this.map.setView([51.5074, -0.1278], 12);
                        }

                        this.highlightSelected();
                    },
                    setAreas(areas) {
                        this.areas = areas ?? [];
                        if (!this.map) {
                            this.initMap();
                            return;
                        }
                        this.clearLayers();
                        this.drawAreas();
                    },
                    highlightSelected() {
                        Object.entries(this.layers).forEach(([id, layer]) => {
                            const isActive = Number(id) === Number(this.selectedAreaId);
                            const area = this.areas.find((item) => Number(item.id) === Number(id));
                            const paused = !!area?.is_paused;
                            layer.circle.setStyle({
                                fillOpacity: isActive ? 0.45 : (paused ? 0.18 : 0.28),
                                weight: isActive ? 3 : 2,
                                opacity: paused ? 0.45 : 0.9,
                            });
                            if (!layer.marker) {
                                return;
                            }
                            if (isActive) {
                                layer.marker.addTo(this.map);
                                layer.marker.openTooltip();
                            } else if (this.map.hasLayer(layer.marker)) {
                                this.map.removeLayer(layer.marker);
                            }
                        });
                    },
                    refreshMap() {
                        this.initMapAttempt = 0;
                        setTimeout(() => {
                            if (!this.map) {
                                this.initMap();
                                return;
                            }

                            this.map.invalidateSize();
                            const size = this.map.getSize();
                            if (!size || size.x === 0) {
                                this.map.remove();
                                this.map = null;
                                this.layers = {};
                                this.initMap();
                            }
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
    .service-area-panel {
        margin-top: 0;
    }

    .service-area-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(280px, 505px);
        gap: 20px;
        align-items: start;
    }

    .service-area-table-col {
        position: relative;
        z-index: 2;
        min-width: 0;
    }

    .service-area-card {
        background: #fdfdfd;
        border-radius: 10px;
        box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.1);
        overflow: visible;
    }

    .service-area-card-head {
        display: flex;
        align-items: center;
        height: 50px;
        padding: 0 20px;
        background: #f6f5f5;
        border-radius: 10px 10px 0 0;
    }

    .service-area-card-head h4 {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .service-area-card-head h4 span {
        margin-left: 8px;
        color: #948f88;
        font-weight: 400;
    }

    .service-area-rows {
        display: flex;
        flex-direction: column;
    }

    .service-area-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
        align-items: center;
        gap: 16px;
        min-height: 81px;
        padding: 12px 20px;
        border-top: 1px solid #ebebeb;
        cursor: pointer;
        background: transparent;
        transition: background-color 0.2s ease;
    }

    .service-area-row:first-child {
        border-top: 0;
    }

    .service-area-row:hover {
        background: #fffaf2;
    }

    .service-area-row.is-selected,
    .service-area-row.is-selected:hover {
        background: #fff7e7;
    }

    .service-area-row.is-paused .service-area-row-copy strong,
    .service-area-row.is-paused .service-area-row-copy p,
    .service-area-row.is-paused .service-area-row-radius {
        opacity: 0.5;
    }

    .service-area-row.is-newly-added {
        animation: service-area-row-highlight-blink 2s ease-in-out;
    }

    @keyframes service-area-row-highlight-blink {

        0%,
        100% {
            background-color: transparent;
        }

        25%,
        75% {
            background-color: rgba(255, 201, 122, 0.35);
        }
    }

    .service-area-row-copy {
        min-width: 0;
    }

    .service-area-row-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .service-area-row-copy strong {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
        line-height: 25px;
    }

    .service-area-row-copy p {
        margin: 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: 25px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .service-area-paused-badge {
        display: inline-flex;
        align-items: center;
        height: 22px;
        padding: 0 10px;
        border-radius: 100px;
        background: #f2f2f2;
        color: #948f88;
        font-family: Lato;
        font-size: 14px;
        font-weight: 600;
        line-height: normal;
    }

    .service-area-row-radius {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
        line-height: normal;
        white-space: nowrap;
    }

    .service-area-more {
        position: relative;
    }

    .service-area-more-menu {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        width: 130px;
        background: #fff;
        border: 1px solid #D9D9D9;
        border-radius: 5px;
        overflow: hidden;
        z-index: 40;
    }

    .service-area-more-menu button {
        width: 100%;
        height: 36px;
        border: 0;
        border-bottom: 1px solid #D9D9D9;
        background: #fff;
        padding: 4px;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
        text-align: left;
        cursor: pointer;
    }

    .service-area-more-menu button:last-child {
        border-bottom: 0;
    }

    .service-area-more-menu button span {
        display: flex;
        align-items: center;
        height: 28px;
        padding: 0 6px;
        border-radius: 5px;
    }

    .service-area-more-menu button:hover span {
        background: #FAF8F4;
    }

    .service-area-more-menu button.is-danger span {
        background: #FAF8F4;
        color: #FF6E6E;
    }

    .service-area-empty {
        text-align: center;
        color: #9d9b98;
        padding: 2rem 1rem;
        font-family: Lato;
    }

    .service-area-map-col {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 505px;
        justify-self: end;
    }

    .service-area-map-shell {
        position: relative;
        width: 100%;
        aspect-ratio: 1;
        border: 1px solid #e3e3e3;
        border-radius: 10px;
        overflow: hidden;
        background: #f4f4f4;
        box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.1);
    }

    .service-area-map,
    .service-area-map.leaflet-container,
    .service-area-map .leaflet-container {
        width: 100%;
        height: 100%;
        border-radius: 10px;
        font-family: Lato;
    }

    .service-area-zoom-hint {
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

    .service-area-map .leaflet-bottom.leaflet-right {
        margin-right: 15px;
        margin-bottom: 15px;
    }

    .service-area-map .leaflet-control-zoom {
        border: 1px solid #e2e2e2;
        border-radius: 5px;
        box-shadow: none;
        overflow: hidden;
    }

    .service-area-map .leaflet-control-zoom a {
        width: 33px;
        height: 33px;
        line-height: 33px;
        color: #3b3731;
        background: #fff;
        border-bottom-color: #e2e2e2;
        font-size: 18px;
    }

    .service-area-map .leaflet-control-zoom a:hover {
        background: #fafafa;
    }

    /* Grayscale basemap only; markers, circles, and labels stay in colour */
    .service-area-map .leaflet-tile-pane {
        filter: grayscale(1);
    }

    .service-area-panel .service-action-btns {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .service-area-panel .service-action-btn {
        width: 36px;
        height: 36px;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .service-area-panel .service-action-btn svg {
        display: block;
        width: 36px;
        height: 36px;
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

    .service-area-leaflet-tooltip {
        background: #fff;
        border: 0;
        border-radius: 10px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        padding: 0;
        color: #3b3731;
        font-family: Lato;
    }

    .service-area-leaflet-tooltip::before {
        border-top-color: #fff;
    }

    .service-area-map-tooltip {
        display: flex;
        flex-direction: column;
        gap: 2px;
        padding: 8px 12px 10px;
        min-width: 106px;
    }

    .service-area-map-tooltip strong {
        font-size: 14px;
        font-weight: 600;
        line-height: normal;
        color: #3b3731;
    }

    .service-area-map-tooltip span {
        font-size: 14px;
        color: #9d9b98;
        font-weight: 400;
        line-height: normal;
    }

    @media (max-width: 992px) {
        .service-area-layout {
            grid-template-columns: 1fr;
        }

        .service-area-table-col {
            padding-right: 0;
        }

        .service-area-map-col {
            max-width: none;
            justify-self: stretch;
        }

        .service-area-map-shell {
            margin-top: 0;
        }
    }
</style>
