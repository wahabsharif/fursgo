<?php

use App\Models\PetDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string')]
    public ?string $photo = null;

    #[Validate('required|date')]
    public string $birthday = '';

    #[Validate('required|string|max:255')]
    public string $pet_type = '';

    #[Validate('required|string|max:255')]
    public string $breed = '';

    #[Validate('required|string|in:male,female')]
    public string $sex = '';

    #[Validate('required|numeric|min:0|max:999.99')]
    public float $weight = 0;

    #[Validate('nullable|string|max:1000')]
    public ?string $notes = null;

    #[Validate('nullable|string|max:500')]
    public ?string $address = null;

    public bool $home_address_toggled = false;

    public bool $showForm = false;
    public bool $isEditing = false;

    public function mount(): void
    {
        $this->loadPetDetails();
    }

    public function loadPetDetails(): void
    {
        $petDetail = PetDetail::where('user_id', Auth::id())->first();

        if ($petDetail) {
            $this->name = $petDetail->name;
            $this->photo = $petDetail->photo;
            $this->birthday = $petDetail->birthday?->format('Y-m-d') ?? '';
            $this->pet_type = $petDetail->pet_type;
            $this->breed = $petDetail->breed;
            $this->sex = $petDetail->sex;
            $this->weight = $petDetail->weight;
            $this->notes = $petDetail->notes;
            $this->address = $petDetail->address;
            $this->home_address_toggled = $petDetail->home_address_toggled ?? false;
            $this->isEditing = true;
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        PetDetail::updateOrCreate(
            ['user_id' => Auth::id()],
            array_merge($validated, ['user_id' => Auth::id()])
        );

        $this->isEditing = true;
        $this->showForm = false;
        $this->dispatch('pet-details-saved');
    }

    public function delete(): void
    {
        PetDetail::where('user_id', Auth::id())->delete();
        $this->reset();
        $this->isEditing = false;
        $this->showForm = false;
        $this->dispatch('pet-details-deleted');
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->loadPetDetails();
        }
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->loadPetDetails();
    }
}; ?>

<div class="pet-details-manager">
    @if (!$showForm && $isEditing)
        <div class="pet-details-summary">
            <h3>{{ $name }}</h3>
            <p>{{ $breed }} • {{ $pet_type }}</p>
            <button wire:click="toggleForm" class="btn btn-primary">Edit Pet Details</button>
            <button wire:click="delete" wire:confirm="Are you sure you want to delete your pet details?" class="btn btn-danger">Delete</button>
        </div>
    @elseif (!$showForm && !$isEditing)
        <div class="no-pet-details">
            <p>No pet details found.</p>
            <button wire:click="toggleForm" class="btn btn-primary">Add Pet Details</button>
        </div>
    @endif

    @if ($showForm)
        <form wire:submit="save" class="pet-details-form">
            <div class="form-group">
                <label for="name">Pet Name</label>
                <input type="text" id="name" wire:model="name" class="form-control">
                @error('name') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="pet_type">Pet Type</label>
                <input type="text" id="pet_type" wire:model="pet_type" class="form-control">
                @error('pet_type') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="breed">Breed</label>
                <input type="text" id="breed" wire:model="breed" class="form-control">
                @error('breed') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="sex">Sex</label>
                <select id="sex" wire:model="sex" class="form-control">
                    <option value="">Select</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                @error('sex') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="birthday">Birthday</label>
                <input type="date" id="birthday" wire:model="birthday" class="form-control">
                @error('birthday') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="weight">Weight (kg)</label>
                <input type="number" id="weight" wire:model="weight" step="0.01" class="form-control">
                @error('weight') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" wire:model="notes" class="form-control" rows="3"></textarea>
                @error('notes') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="home_address_toggled">
                    <span>Use home address</span>
                </label>
            </div>

            @if ($home_address_toggled)
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" wire:model="address" class="form-control" rows="2"></textarea>
                    @error('address') <span class="error">{{ $message }}</span> @enderror
                </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" wire:click="toggleForm" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    @endif

    <x-action-message on="pet-details-saved">
        Pet details saved successfully!
    </x-action-message>

    <x-action-message on="pet-details-deleted">
        Pet details deleted successfully!
    </x-action-message>
</div>
