<?php

use App\Models\Groomer;
use App\Models\Space;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new class extends Component {
    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = '';

    #[Url]
    public ?string $venue_type = null;

    public function with(): array
    {
        return [
            'groomers' => $this->getGroomers(),
            'spaces' => $this->getSpaces(),
        ];
    }

    public function getGroomers()
    {
        return Groomer::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('studio_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->sort, function ($query) {
                match ($this->sort) {
                    'distance' => $query->orderBy('distance'),
                    'lowest_price' => $query->orderBy('price'),
                    default => $query->latest(),
                };
            })
            ->get();
    }

    public function getSpaces()
    {
        return Space::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->venue_type, function ($query) {
                $query->where('venue_type', $this->venue_type);
            })
            ->get();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'sort', 'venue_type']);
    }
}; ?>

<div>
    @include('search-results')
</div>
