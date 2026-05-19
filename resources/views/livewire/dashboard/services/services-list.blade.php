<?php

use App\Models\GroomerSpacerProfile;
use App\Models\Service;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public int $perPage = 10;

    public ?int $highlightItemId = null;

    public function getServicesProperty()
    {
        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = GroomerSpacerProfile::where('email', $email)->first();

        if (!$profile) {
            return collect();
        }

        return Service::query()->where('groomer_spacer_id', $profile->id)->latest()->limit($this->perPage)->get();
    }

    public function getTotalServicesProperty(): int
    {
        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = GroomerSpacerProfile::where('email', $email)->first();

        if (!$profile) {
            return 0;
        }

        return (int) Service::query()->where('groomer_spacer_id', $profile->id)->count();
    }

    public function getCanLoadMoreProperty(): bool
    {
        return $this->services->count() < $this->totalServices;
    }

    public function toggleVisibility(int $serviceId): void
    {
        $service = Service::find($serviceId);

        if (!$service) {
            return;
        }

        $service->update([
            'visibility_controls' => !(bool) $service->visibility_controls,
        ]);
    }

    public function appliesTo(array $petCompatibility): string
    {
        $petTypes = data_get($petCompatibility, 'pet_types', []);

        if (!is_array($petTypes) || empty($petTypes)) {
            return '-';
        }

        return collect($petTypes)->map(fn($type) => ucfirst((string) $type))->join(', ');
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    #[On('service-created')]
    public function refreshList(int $itemId = 0): void
    {
        $this->perPage = 10;
        $this->highlightItemId = $itemId > 0 ? $itemId : null;
    }

    public function clearHighlight(): void
    {
        $this->highlightItemId = null;
    }
}; ?>

<section class="service-list-wrapper" aria-label="Service list">
    <div class="service-list-table-shell">
        <table class="service-list-table">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Applies to</th>
                    <th>Base Duration</th>
                    <th>Base Price</th>
                    <th>Active</th>
                    <th class="service-edit-col">Edit</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->services as $service)
                    @php
                        $duration = (int) data_get($service->duration, 'base_duration', 0);
                        $price = (float) data_get($service->pricing, 'base_price', 0);
                        $isVisible = (bool) $service->visibility_controls;
                    @endphp
                    <tr wire:key="service-row-{{ $service->id }}" @class([
                        'is-muted' => !$isVisible,
                        'is-newly-added' => $highlightItemId === $service->id,
                    ])
                        @if ($highlightItemId === $service->id) x-init="setTimeout(() => $wire.clearHighlight(), 2000)" @endif>
                        <td>{{ $service->service_name }}</td>
                        <td>{{ $this->appliesTo((array) $service->pet_compatibility) }}</td>
                        <td style="font-weight: 600;">{{ $duration > 0 ? $duration . ' mins' : '-' }}</td>
                        <td>{{ '£' . number_format($price, 2) }}</td>
                        <td>
                            <button type="button" class="service-toggle {{ $isVisible ? 'is-on' : '' }}"
                                aria-label="Toggle service visibility"
                                x-on:click="window.dispatchEvent(new CustomEvent('nav-list-loading-start'))"
                                wire:click="toggleVisibility({{ $service->id }})"></button>
                        </td>
                        <td class="service-edit-col">
                            <button type="button" class="icon-btn" aria-label="Edit service">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16"
                                    viewBox="0 0 17 16" fill="none">
                                    <path
                                        d="M10.8529 2.51425L13.6765 5.29691M8.97059 15.5H16.5M1.44118 11.7898L0.5 15.5L4.26471 14.5724L15.1692 3.82581C15.5221 3.47793 15.7203 3.00616 15.7203 2.51425C15.7203 2.02234 15.5221 1.55057 15.1692 1.20269L15.0073 1.04315C14.6543 0.695371 14.1756 0.5 13.6765 0.5C13.1773 0.5 12.6986 0.695371 12.3456 1.04315L1.44118 11.7898Z"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <button type="button" class="icon-btn dots-btn" aria-label="Service actions">•••</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;color: #9D9B98;">No services added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($this->canLoadMore)
        <div class="service-load-more-wrap">
            <button type="button" class="service-load-more-btn"
                x-on:click="window.dispatchEvent(new CustomEvent('nav-list-loading-start'))" wire:click="loadMore">
                Load More
            </button>
        </div>
    @endif
</section>

<style>
    .service-list-wrapper {
        margin-top: 0;
    }

    .service-list-table-shell {
        overflow-x: auto;
    }

    .service-list-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 820px;
    }

    .service-list-table th,
    .service-list-table td {
        border-bottom: 1px solid #dcdcdc;
        text-align: left;
        padding: 1.2rem 0;
    }

    .service-list-table td {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .service-list-table th {
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .service-list-table .service-edit-col {
        border-left: 1px solid #dcdcdc;
        text-align: left;
        padding-left: 4rem;
    }

    .service-list-table th.service-edit-col {
        width: 180px;
        text-align: center;
    }

    .service-list-table th.service-edit-col:last-child {
        width: 23rem;
        text-align: left;
        padding-left: 4rem;
    }

    .service-list-table tr.is-muted td:not(.service-edit-col) {
        opacity: 0.5;
    }

    .service-list-table tr.is-newly-added td {
        animation: service-row-highlight-blink 2s ease-in-out;
    }

    @keyframes service-row-highlight-blink {

        0%,
        100% {
            background-color: transparent;
        }

        25%,
        75% {
            background-color: rgba(216, 232, 183, 0.55);
        }
    }

    .service-toggle {
        width: 56px;
        height: 30px;
        border-radius: 999px;
        border: none;
        background: #cfcfcf;
        position: relative;
        display: inline-block;
        cursor: pointer;
        transition: background-color 0.24s ease;
    }

    .service-toggle::after {
        content: "";
        position: absolute;
        top: 3px;
        left: 4px;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: white !important;
        z-index: 1;
        transition: left 0.24s ease;
    }

    .service-toggle::before {
        content: none;
        opacity: 0;
        transform: scale(0.88);
    }

    .service-toggle.is-on {
        border: none;
        background: #D8E8B7;
    }

    .service-toggle.is-on::after {
        left: 28.5px;
        background: #ffffff;
        z-index: 1;
    }

    .service-toggle.is-on::before {
        content: "";
        position: absolute;
        right: 9px;
        top: 9px;
        width: 13px;
        height: 13px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='11' viewBox='0 0 13 11' fill='none'%3E%3Cpath d='M1.25 5.8L4.4 8.95L11.75 1.6' stroke='%23C7D59F' stroke-width='2.1' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        z-index: 2;
        opacity: 1;
        transform: scale(1);
        animation: toggle-icon-in 0.16s ease-in 0.24s both;
    }

    @keyframes toggle-icon-in {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .icon-btn {
        border: 0;
        background: transparent;
        color: #4a4a4a;
        cursor: pointer;
        font-size: 24px;
        line-height: 1;
        vertical-align: middle;
    }

    .dots-btn {
        font-size: 22px;
        letter-spacing: 2px;
        margin-left: 4.5rem;
    }

    .service-load-more-wrap {
        display: flex;
        justify-content: center;
        margin-top: 3rem;
    }

    .service-load-more-btn {
        width: 133px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 75px;
        border: 1px solid #3B3731;
        background: transparent;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
    }
</style>
