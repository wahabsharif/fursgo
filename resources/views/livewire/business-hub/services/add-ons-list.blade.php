<?php

use App\Models\AddOn;
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

    private function editState(AddOn $addOn): array
    {
        return BusinessHubServiceFormState::fromAddOn($addOn, $this->formVariant());
    }

    private function baseQuery()
    {
        $profileId = $this->profileId();
        $query = AddOn::query()->where('groomer_spacer_id', $profileId ?: 0);
        $term = trim($this->search);

        if ($term !== '') {
            $query->where('add_ons_name', 'like', '%' . $term . '%');
        }

        if (in_array($this->filter, ['cat', 'dog', 'other'], true)) {
            $query->where(function ($q) {
                $q->whereJsonContains('pet_compatibility->pet_types', $this->filter)->orWhereJsonContains('pet_compatibility->pet_type', $this->filter);
            });
        }

        return match ($this->sort) {
            'duration_asc' => $query->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(duration, '$.base_duration')) AS UNSIGNED) ASC"),
            'price_asc' => $query->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(pricing, '$.base_price')) AS DECIMAL(10,2)) ASC"),
            'name_asc' => $query->orderBy('add_ons_name'),
            default => $query->latest(),
        };
    }

    #[Computed]
    public function addOns()
    {
        if (!$this->profileId()) {
            return collect();
        }

        return $this->baseQuery()->limit($this->perPage)->get();
    }

    #[Computed]
    public function totalAddOns(): int
    {
        if (!$this->profileId()) {
            return 0;
        }

        return (int) $this->baseQuery()->count();
    }

    #[Computed]
    public function canLoadMore(): bool
    {
        return $this->addOns->count() < $this->totalAddOns;
    }

    public function toggleVisibility(int $addOnId): void
    {
        try {
            $profileId = $this->profileId();

            if (!$profileId) {
                return;
            }

            $addOn = AddOn::query()->where('groomer_spacer_id', $profileId)->find($addOnId);

            if (!$addOn) {
                return;
            }

            $addOn->update([
                'visibility_controls' => !$addOn->visibility_controls,
            ]);

            unset($this->addOns, $this->totalAddOns, $this->canLoadMore);
        } finally {
            $this->endNavLoading();
        }
    }

    public function duplicateAddOn(int $addOnId): void
    {
        try {
            $profileId = $this->profileId();

            if (!$profileId) {
                return;
            }

            $addOn = AddOn::query()->where('groomer_spacer_id', $profileId)->find($addOnId);

            if (!$addOn) {
                return;
            }

            $copy = $addOn->replicate();
            $copy->add_ons_name = $addOn->add_ons_name . ' (copy)';
            $copy->save();

            $this->highlightItemId = (int) $copy->id;
            unset($this->addOns, $this->totalAddOns, $this->canLoadMore);
            $this->dispatch('add-on-created', itemId: (int) $copy->id);
        } finally {
            $this->endNavLoading();
        }
    }

    public function deleteAddOn(int $addOnId): void
    {
        try {
            $profileId = $this->profileId();

            if (!$profileId) {
                return;
            }

            $addOn = AddOn::query()->where('groomer_spacer_id', $profileId)->find($addOnId);

            if (!$addOn) {
                return;
            }

            $addOn->delete();

            if ($this->highlightItemId === $addOnId) {
                $this->highlightItemId = null;
            }

            unset($this->addOns, $this->totalAddOns, $this->canLoadMore);
            $this->dispatch('add-on-deleted');
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
        $petTypes = data_get($petCompatibility, 'pet_types', data_get($petCompatibility, 'pet_type', []));

        if (!is_array($petTypes) || empty($petTypes)) {
            return '-';
        }

        return collect($petTypes)->map(fn($type) => ucfirst((string) $type))->join(', ');
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
        unset($this->addOns, $this->totalAddOns, $this->canLoadMore);
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
        unset($this->addOns, $this->totalAddOns, $this->canLoadMore);
    }

    #[On('add-on-created')]
    #[On('add-on-deleted')]
    public function refreshList(int $itemId = 0): void
    {
        $this->perPage = 10;
        $this->highlightItemId = $itemId > 0 ? $itemId : null;
        unset($this->addOns, $this->totalAddOns, $this->canLoadMore);
    }

    public function clearHighlight(): void
    {
        $this->highlightItemId = null;
    }
}; ?>

<section class="service-list-wrapper" aria-label="Add-ons list">
    <div class="service-list-table-shell">
        <table class="service-list-table is-addons">
            <thead>
                <tr>
                    <th class="service-name-col">Service Name</th>
                    <th class="service-applies-col">Applies to</th>
                    <th class="service-price-col">Price</th>
                    <th class="service-active-col">Active</th>
                    <th class="service-action-col">Action</th>
                </tr>
            </thead>
            <tbody wire:key="addon-tbody-{{ $this->addOns->pluck('id')->join('-') }}">
                @forelse ($this->addOns as $addOn)
                    @php
                        $price = (float) data_get($addOn->pricing, 'base_price', 0);
                        $isVisible = (bool) $addOn->visibility_controls;
                    @endphp
                    <tr wire:key="addon-row-{{ $addOn->id }}-{{ $isVisible ? 'on' : 'off' }}" @class([
                        'is-muted' => !$isVisible,
                        'is-newly-added' => $highlightItemId === $addOn->id,
                    ])
                        @if ($highlightItemId === $addOn->id) x-init="setTimeout(() => $wire.clearHighlight(), 2000)" @endif>
                        <td class="service-name-col">{{ $addOn->add_ons_name }}</td>
                        <td class="service-applies-col">{{ $this->appliesTo((array) $addOn->pet_compatibility) }}</td>
                        <td class="service-price-col">{{ '£' . number_format($price, 2) }}</td>
                        <td class="service-active-col">
                            <button type="button" class="service-toggle {{ $isVisible ? 'is-on' : '' }}"
                                aria-label="Toggle add-on visibility"
                                x-on:click="window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } }))"
                                wire:click="toggleVisibility({{ $addOn->id }})"></button>
                        </td>
                        <td class="service-action-col">
                            <div class="service-action-btns">
                                <button type="button" class="service-action-btn" aria-label="Edit add-on"
                                    x-on:click="
                                        window.dispatchEvent(new CustomEvent('add-on-edit-requested', {
                                            detail: {
                                                addOnId: {{ $addOn->id }},
                                                title: @js($addOn->add_ons_name),
                                                state: @js($this->editState($addOn))
                                            }
                                        }));
                                    ">
                                    <x-business-hub.common.icon name="edit" />
                                </button>
                                <x-business-hub.services.more-menu :id="$addOn->id" duplicate-method="duplicateAddOn"
                                    delete-method="deleteAddOn" aria-label="Add-on actions" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="service-list-empty">No add-ons added yet.</td>
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
