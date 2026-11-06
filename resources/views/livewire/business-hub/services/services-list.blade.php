<?php

use App\Models\Service;
use App\Support\BusinessHubServiceFormState;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public string $search = '';
    public string $sort = 'newest';
    public string $filter = 'all';

    public int $perPage = 10;

    public ?int $highlightItemId = null;

    public ?int $groomerSpacerId = null;

    public function mount(string $search = '', string $sort = 'newest', string $filter = 'all'): void
    {
        $this->search = $search;
        $this->sort = $sort;
        $this->filter = $filter;
        $this->groomerSpacerId = $this->profileId();
    }

    private function profileId(): ?int
    {
        if ($this->groomerSpacerId) {
            return $this->groomerSpacerId;
        }

        $id = auth('groomer_spacer')->id() ?? auth()->id();
        $this->groomerSpacerId = $id ? (int) $id : null;

        return $this->groomerSpacerId;
    }

    private function formVariant(): string
    {
        return strtolower((string) data_get(auth('groomer_spacer')->user() ?? auth()->user(), 'user_type', '')) === 'space' ? 'space' : 'groomer';
    }

    private function editState(Service $service): array
    {
        return BusinessHubServiceFormState::fromService($service, $this->formVariant());
    }

    private function baseQuery()
    {
        $profileId = $this->profileId();
        $query = Service::query()->where('groomer_spacer_id', $profileId ?: 0);
        $term = trim($this->search);

        if ($term !== '') {
            $query->where('service_name', 'like', '%' . $term . '%');
        }

        if (in_array($this->filter, ['cat', 'dog', 'other'], true)) {
            $query->whereJsonContains('pet_compatibility->pet_types', $this->filter);
        }

        return match ($this->sort) {
            'duration_asc' => $query->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(duration, '$.base_duration')) AS UNSIGNED) ASC"),
            'price_asc' => $query->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(pricing, '$.base_price')) AS DECIMAL(10,2)) ASC"),
            'name_asc' => $query->orderBy('service_name'),
            default => $query->latest(),
        };
    }

    #[Computed]
    public function services()
    {
        if (!$this->profileId()) {
            return collect();
        }

        return $this->baseQuery()->limit($this->perPage)->get();
    }

    #[Computed]
    public function totalServices(): int
    {
        if (!$this->profileId()) {
            return 0;
        }

        return (int) $this->baseQuery()->count();
    }

    #[Computed]
    public function canLoadMore(): bool
    {
        return $this->services->count() < $this->totalServices;
    }

    public function toggleVisibility(int $serviceId): void
    {
        try {
            $profileId = $this->profileId();

            if (!$profileId) {
                return;
            }

            $service = Service::query()->where('groomer_spacer_id', $profileId)->find($serviceId);

            if (!$service) {
                return;
            }

            $service->update([
                'visibility_controls' => !$service->visibility_controls,
            ]);

            unset($this->services, $this->totalServices, $this->canLoadMore);
        } finally {
            $this->endNavLoading();
        }
    }

    public function duplicateService(int $serviceId): void
    {
        try {
            $profileId = $this->profileId();

            if (!$profileId) {
                return;
            }

            $service = Service::query()->where('groomer_spacer_id', $profileId)->find($serviceId);

            if (!$service) {
                return;
            }

            $copy = $service->replicate();
            $copy->service_name = $service->service_name . ' (copy)';
            $copy->save();

            $this->highlightItemId = (int) $copy->id;
            unset($this->services, $this->totalServices, $this->canLoadMore);
            $this->dispatch('service-created', itemId: (int) $copy->id);
        } finally {
            $this->endNavLoading();
        }
    }

    public function deleteService(int $serviceId): void
    {
        try {
            $profileId = $this->profileId();

            if (!$profileId) {
                return;
            }

            $service = Service::query()->where('groomer_spacer_id', $profileId)->find($serviceId);

            if (!$service) {
                return;
            }

            $service->delete();

            if ($this->highlightItemId === $serviceId) {
                $this->highlightItemId = null;
            }

            unset($this->services, $this->totalServices, $this->canLoadMore);
            $this->dispatch('service-deleted');
        } finally {
            $this->endNavLoading();
        }
    }

    private function endNavLoading(): void
    {
        $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end"))');
    }

    public function appliesTo(array $petCompatibility): string
    {
        $petTypes = data_get($petCompatibility, 'pet_types', []);

        if (!is_array($petTypes) || empty($petTypes)) {
            return '-';
        }

        return collect($petTypes)->map(fn($type) => ucfirst((string) $type))->join(', ');
    }

    public function formatDuration(int $duration): string
    {
        if ($duration <= 0) {
            return '-';
        }

        if ($duration % 60 === 0) {
            return ((int) ($duration / 60)) . 'h';
        }

        return $duration . ' mins';
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
        unset($this->services, $this->totalServices, $this->canLoadMore);
    }

    public function updatedSearch(): void
    {
        $this->resetToolbarPagination();
    }

    public function updatedSort(): void
    {
        $this->resetToolbarPagination();
    }

    public function updatedFilter(): void
    {
        $this->resetToolbarPagination();
    }

    #[On('services-toolbar-updated')]
    public function applyToolbar(mixed $search = '', mixed $sort = 'newest', mixed $filter = 'all'): void
    {
        if (is_array($search)) {
            $payload = $search;
            $search = $payload['search'] ?? '';
            $sort = $payload['sort'] ?? 'newest';
            $filter = $payload['filter'] ?? 'all';
        }

        $this->search = (string) $search;
        $this->sort = (string) $sort;
        $this->filter = (string) $filter;
        $this->resetToolbarPagination();
    }

    private function resetToolbarPagination(): void
    {
        $this->perPage = 10;
        unset($this->services, $this->totalServices, $this->canLoadMore);
    }

    #[On('service-created')]
    #[On('service-deleted')]
    public function refreshList(int $itemId = 0): void
    {
        $this->perPage = 10;
        $this->highlightItemId = $itemId > 0 ? $itemId : null;
        unset($this->services, $this->totalServices, $this->canLoadMore);
    }

    public function clearHighlight(): void
    {
        $this->highlightItemId = null;
    }
}; ?>

@php
    $isSpace = strtolower((string) data_get(auth()->user(), 'user_type', '')) === 'space';
    $priceLabel = $isSpace ? 'Price' : 'Base Price';
@endphp

<section class="service-list-wrapper" aria-label="Service list">
    <div class="service-list-table-shell">
        <table @class(['service-list-table', 'is-space' => $isSpace])>
            <thead>
                <tr>
                    <th class="service-name-col">Service Name</th>
                    <th class="service-applies-col">Applies to</th>
                    <th class="service-duration-col">Base Duration</th>
                    <th class="service-price-col">{{ $priceLabel }}</th>
                    <th class="service-active-col">Active</th>
                    <th class="service-action-col">Action</th>
                </tr>
            </thead>
            <tbody wire:key="svc-tbody-{{ $this->services->pluck('id')->join('-') }}">
                @forelse ($this->services as $service)
                    @php
                        $duration = (int) data_get($service->duration, 'base_duration', 0);
                        $price = (float) data_get($service->pricing, 'base_price', 0);
                        $isVisible = (bool) $service->visibility_controls;
                    @endphp
                    <tr wire:key="service-row-{{ $service->id }}-{{ $isVisible ? 'on' : 'off' }}" @class([
                        'is-muted' => !$isVisible,
                        'is-newly-added' => $highlightItemId === $service->id,
                    ])
                        @if ($highlightItemId === $service->id) x-init="setTimeout(() => $wire.clearHighlight(), 2000)" @endif>
                        <td class="service-name-col">{{ $service->service_name }}</td>
                        <td class="service-applies-col">{{ $this->appliesTo((array) $service->pet_compatibility) }}</td>
                        <td class="service-duration-col">{{ $this->formatDuration($duration) }}</td>
                        <td class="service-price-col">{{ '£' . number_format($price, 2) }}</td>
                        <td class="service-active-col">
                            <button type="button" class="service-toggle {{ $isVisible ? 'is-on' : '' }}"
                                aria-label="Toggle service visibility"
                                x-on:click="window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } }))"
                                wire:click="toggleVisibility({{ $service->id }})"></button>
                        </td>
                        <td class="service-action-col">
                            <div class="service-action-btns">
                                <button type="button" class="service-action-btn" aria-label="Edit service"
                                    x-on:click="
                                        window.dispatchEvent(new CustomEvent('service-edit-requested', {
                                            detail: {
                                                serviceId: {{ $service->id }},
                                                title: @js($service->service_name),
                                                state: @js($this->editState($service))
                                            }
                                        }));
                                    ">
                                    <x-business-hub.common.icon name="edit" />
                                </button>
                                <x-business-hub.services.more-menu :id="$service->id" aria-label="Service actions" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="service-list-empty">No services added yet.</td>
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
