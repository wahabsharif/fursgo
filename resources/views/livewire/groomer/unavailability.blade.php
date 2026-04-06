<?php

use Livewire\Volt\Component;

new class extends Component {
    // Groomer unavailability - static content wrapper with sample data
    public array $groomers = [];
    public array $spaces = [];

    public function mount(): void
    {
        $this->groomers = [
            [
                'name' => 'Sarah W.',
                'studio_name' => 'Sarah\'s Grooming Studio',
                'distance' => '2.5',
                'rating' => '4.3',
                'reviews_count' => 20,
                'experience_text' => '6+ years experience',
                'price' => 38,
                'image_url' => 'assets/images/card1.png',
                'tags' => ['Home Visit'],
                'slots' => ['Mon 1, 08:30 AM'],
                'is_top_rated' => true,
            ]
        ];

        $this->spaces = [
            [
                'name' => 'Pet Care Space',
                'location' => 'London',
                'price' => 25,
                'image_url' => 'assets/images/space1.png',
            ]
        ];
    }
}; ?>

<div>
    @include('groomer-unavailability')
</div>
