<?php

use App\Models\GroomerSpacerProfile;
use App\Models\ServiceArea;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $selectedAreaId = null;

    public ?int $highlightItemId = null;

    private function getProfileId(): ?int
    {
        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = GroomerSpacerProfile::where('email', $email)->first();

        return $profile?->id;
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
        $label = $radius == 1 ? 'mile' : 'miles';

        return rtrim(rtrim(number_format($radius, 1), '0'), '.') . ' ' . $label;
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
                'radiusLabel' => $this->formatRadius((float) $area['radius']),
            ],
        )
        ->values()
        ->all();
@endphp

<section class="service-area-panel" aria-label="Service area list" wire:ignore.self x-data="serviceAreaMap(@js($mapAreas), @entangle('selectedAreaId').live)"
    x-on:service-area-map-refresh.window="refreshMap()"
    x-on:service-area-data-updated.window="setAreas($event.detail?.areas ?? [])">
    <div class="service-area-layout">
        <div class="service-area-table-col">
            <div class="service-area-table-shell">
                <table class="service-area-table">
                    <thead>
                        <tr>
                            <th>Service Area</th>
                            <th>Radius</th>
                            <th class="service-area-edit-col">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->serviceAreas as $area)
                            <tr wire:key="service-area-row-{{ $area['id'] }}" @class([
                                'is-selected' => $selectedAreaId === $area['id'],
                                'is-newly-added' => $highlightItemId === $area['id'],
                            ])
                                @if ($highlightItemId === $area['id']) x-init="setTimeout(() => $wire.clearHighlight(), 2000)" @endif
                                wire:click="selectArea({{ $area['id'] }})" role="button" tabindex="0"
                                @keydown.enter.prevent="$wire.selectArea({{ $area['id'] }})">
                                <td>{{ $area['name'] }}</td>
                                <td>{{ $this->formatRadius((float) $area['radius']) }}</td>
                                <td class="service-area-edit-col" wire:click.stop>
                                    <button type="button" class="icon-btn" aria-label="Edit {{ $area['name'] }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16"
                                            viewBox="0 0 17 16" fill="none">
                                            <path
                                                d="M10.8529 2.51425L13.6765 5.29691M8.97059 15.5H16.5M1.44118 11.7898L0.5 15.5L4.26471 14.5724L15.1692 3.82581C15.5221 3.47793 15.7203 3.00616 15.7203 2.51425C15.7203 2.02234 15.5221 1.55057 15.1692 1.20269L15.0073 1.04315C14.6543 0.695371 14.1756 0.5 13.6765 0.5C13.1773 0.5 12.6986 0.695371 12.3456 1.04315L1.44118 11.7898Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <button type="button" class="icon-btn dots-btn"
                                        aria-label="Actions for {{ $area['name'] }}">•••
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="service-area-empty">No service areas added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="service-area-map-col">
            <div class="service-area-map-shell" wire:ignore>
                <div id="service-area-map" class="service-area-map" role="img" aria-label="Map of service areas">
                </div>
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
                        const formatted = String(value).replace(/\.0$/, '');

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

                        this.drawAreas();
                        setTimeout(() => this.map?.invalidateSize(), 120);
                    },
                    clearLayers() {
                        Object.values(this.layers).forEach(({
                            circle,
                            marker
                        }) => {
                            this.map.removeLayer(circle);
                            this.map.removeLayer(marker);
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
                            const radiusLabel = area.radiusLabel ?? this.formatRadius(area
                                .radius);
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
                            }).addTo(this.map);

                            const tooltipHtml =
                                `<div class="service-area-map-tooltip"><strong>${area.name}</strong><span>Radius: ${radiusLabel}</span></div>`;
                            marker.bindTooltip(tooltipHtml, {
                                permanent: true,
                                direction: 'top',
                                offset: [0, -22],
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
                            layer.circle.setStyle({
                                fillOpacity: isActive ? 0.5 : 0.35,
                                weight: 3,
                            });
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
        grid-template-columns: minmax(280px, 38%) minmax(0, 1fr);
        gap: 0;
        align-items: stretch;
    }

    .service-area-table-col {
        padding-right: 1.5rem;
        padding-top: 0.25rem;
    }

    .service-area-table-shell {
        overflow-x: auto;
    }

    .service-area-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 320px;
    }

    .service-area-table th,
    .service-area-table td {
        border-bottom: 1px solid #dcdcdc;
        text-align: left;
        padding: 1.2rem 0;
        vertical-align: middle;
    }

    .service-area-table td {
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
        text-transform: capitalize;
    }

    .service-area-table th {
        color: #000;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .service-area-table tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .service-area-table tbody tr:hover,
    .service-area-table tbody tr.is-selected {
        background-color: rgba(216, 232, 183, 0.18);
    }

    .service-area-table tr.is-newly-added td {
        animation: service-area-row-highlight-blink 2s ease-in-out;
    }

    @keyframes service-area-row-highlight-blink {

        0%,
        100% {
            background-color: transparent;
        }

        25%,
        75% {
            background-color: rgba(216, 232, 183, 0.55);
        }
    }

    .service-area-table .service-area-edit-col {
        border-left: 1px solid #dcdcdc;
        text-align: left;
        padding-left: 2.5rem;
        white-space: nowrap;
    }

    .service-area-table th.service-area-edit-col {
        width: 140px;
        padding-left: 2.5rem;
    }

    .service-area-empty {
        text-align: center;
        color: #9d9b98;
        padding: 2rem 0 !important;
    }

    .service-area-map-col {
        min-height: 420px;
    }

    .service-area-map-shell {
        width: 100%;
        height: 505px;
        aspect-ratio: 122/101;
        border-radius: 10px;
        overflow: hidden;
        background: #f4f4f4;
        margin-left: auto;
    }

    .service-area-map {
        width: 100%;
        height: 505px;
        aspect-ratio: 122/101;
        border-radius: 10px;
    }

    .service-area-map.leaflet-container,
    .service-area-map .leaflet-container {
        width: 100%;
        height: 505px;
        aspect-ratio: 122/101;
        font-family: Lato, sans-serif;
    }

    /* Grayscale basemap only; markers, circles, and labels stay in colour */
    .service-area-map .leaflet-tile-pane {
        filter: grayscale(1);
    }

    .service-area-panel .icon-btn {
        border: 0;
        background: transparent;
        color: #4a4a4a;
        cursor: pointer;
        font-size: 24px;
        line-height: 1;
        vertical-align: middle;
    }

    .service-area-panel .dots-btn {
        font-size: 22px;
        letter-spacing: 2px;
        margin-left: 2.5rem;
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
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        box-shadow: 0 4px 14px rgba(59, 55, 49, 0.12);
        padding: 0;
        color: #3b3731;
        font-family: Lato, sans-serif;
    }

    .service-area-leaflet-tooltip::before {
        border-top-color: #fff;
    }

    .service-area-map-tooltip {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        padding: 0.45rem 0.65rem;
        min-width: 120px;
    }

    .service-area-map-tooltip strong {
        font-size: 14px;
        font-weight: 600;
        color: #3b3731;
    }

    .service-area-map-tooltip span {
        font-size: 12px;
        color: #9d9b98;
        font-weight: 400;
    }

    @media (max-width: 992px) {
        .service-area-layout {
            grid-template-columns: 1fr;
        }

        .service-area-table-col {
            padding-right: 0;
        }

        .service-area-map-shell {
            border-radius: 12px;
            margin-top: 1.5rem;
        }
    }
</style>
