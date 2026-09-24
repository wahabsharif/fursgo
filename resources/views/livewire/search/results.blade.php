<?php

use App\Support\SearchResultsData;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('layouts.app'), Title('Fursgo - Search Results')] class extends Component {
    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = '';

    #[Url]
    public ?string $venue_type = null;

    public function with(): array
    {
        return [
            'groomers' => SearchResultsData::groomers($this->search, $this->sort),
            'spaces' => SearchResultsData::spaces($this->search, $this->venue_type),
        ];
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'sort', 'venue_type']);
    }
}; ?>

<div>
    @include('search-results')
</div>
