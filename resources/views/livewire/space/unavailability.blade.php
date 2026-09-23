<?php

use App\Support\SearchResultsData;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

/**
 * Space unavailability variants (design shell).
 * Variant resolved from URL path to keep custom routes intact.
 */
new #[Layout('layouts.app'), Title('Fursgo - Space Unavailability')] class extends Component {
    #[Url]
    public string $search = '';

    #[Url]
    public ?string $venue_type = null;

    public string $variant = 'location';

    public string $focusTab = 'calendar';

    public function mount(): void
    {
        $path = request()->path();

        if (str_contains($path, 'time-unavailability')) {
            $this->variant = 'time';
            $this->focusTab = 'calendar';
        } elseif (str_contains($path, 'specific_space_unavailability_list_view')) {
            $this->variant = 'specific';
            $this->focusTab = 'list';
        } elseif (str_contains($path, 'specific_space_unavailability_map_view')) {
            $this->variant = 'specific';
            $this->focusTab = 'map';
        } elseif (str_contains($path, 'specific_space_unavailability_calendar_view')) {
            $this->variant = 'specific';
            $this->focusTab = 'calendar';
        } else {
            $this->variant = 'location';
            $this->focusTab = 'calendar';
        }
    }

    public function with(): array
    {
        return [
            'groomers' => SearchResultsData::groomers($this->search),
            'spaces' => SearchResultsData::spaces($this->search, $this->venue_type),
        ];
    }
}; ?>

<div>
    @include('partials.customer-journey.results-shell', [
        'variant' => $variant,
        'entity' => 'space',
        'focusTab' => $focusTab,
        'activeMain' => 'space',
        'groomers' => $groomers,
        'spaces' => $spaces,
    ])
</div>
