<?php

use App\Models\PetDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $petDetails = null;

    public function mount(): void
    {
        $this->petDetails = PetDetail::where('user_id', Auth::id())->first();
    }
}; ?>

<div>
    @include('booking-groomer', [
        'petDetails' => $petDetails,
    ])
</div>
