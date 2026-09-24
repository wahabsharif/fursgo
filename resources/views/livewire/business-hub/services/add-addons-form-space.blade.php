<?php

use App\Models\AddOn;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $editingId = null;
    public string $addOnsName = '';
    public string $description = '';
    public array $otherPets = [];
    public string $otherPetInput = '';
    public array $selectedPets = [];
    public array $selectedSizes = [];
    public bool $visibilityControls = true;
    public float $basePrice = 25;
    public float $overtimeCharge = 10;
    public string $overtimePer = '15 Minutes';

    public function mount(?int $editingId = null): void
    {
        $this->editingId = $editingId;

        if ($this->editingId) {
            $this->loadAddOn($this->editingId);
        }
    }

    private function profileId(): ?int
    {
        $id = auth('groomer_spacer')->id() ?? auth()->id();

        return $id ? (int) $id : null;
    }

    private function minutesOption(mixed $value, string $fallback): string
    {
        if (is_string($value) && preg_match('/(\d+)\s*min/i', $value, $matches)) {
            return $matches[1] . ' Minutes';
        }

        if (is_numeric($value) && (int) $value > 0) {
            return ((int) $value) . ' Minutes';
        }

        return $fallback;
    }

    #[On('reset-addon-form')]
    public function resetForm(): void
    {
        $this->reset();
        $this->editingId = null;
        $this->js('window.dispatchEvent(new CustomEvent("service-form-baseline"))');
    }

    public function loadAddOn(int $addOnId, bool $silent = false): void
    {
        $profileIds = array_values(array_unique(array_filter([auth('groomer_spacer')->id(), auth()->id(), $this->profileId()])));

        $addOn = AddOn::query()
            ->when($profileIds !== [], fn($query) => $query->whereIn('groomer_spacer_id', $profileIds))
            ->find($addOnId);

        if (!$addOn) {
            return;
        }

        $this->editingId = (int) $addOn->id;
        $this->addOnsName = (string) $addOn->add_ons_name;
        $this->description = (string) ($addOn->description ?? '');
        $compat = (array) $addOn->pet_compatibility;
        $this->selectedPets = array_values((array) (data_get($compat, 'pet_type') ?: data_get($compat, 'pet_types', [])));
        $this->otherPets = array_values((array) data_get($compat, 'other_pets', []));
        $this->selectedSizes = array_values((array) (data_get($compat, 'pet_size') ?: data_get($compat, 'pet_sizes', [])));
        $pricing = (array) $addOn->pricing;
        $this->basePrice = (float) data_get($pricing, 'base_price', 25);
        $this->overtimeCharge = (float) data_get($pricing, 'overtime_charge.price', 10);
        $this->overtimePer = $this->minutesOption(data_get($pricing, 'overtime_charge.per'), '15 Minutes');
        $this->visibilityControls = (bool) $addOn->visibility_controls;
        if ($silent) {
            $this->skipRender();
        }
        $this->js('window.dispatchEvent(new CustomEvent("service-form-baseline"))');
    }

    private function payload(): array
    {
        return [
            'add_ons_name' => $this->addOnsName,
            'description' => $this->description !== '' ? $this->description : '',
            'pet_compatibility' => [
                'pet_type' => array_values($this->selectedPets),
                'other_pets' => array_values($this->otherPets),
                'pet_size' => array_values($this->selectedSizes),
            ],
            'duration' => '',
            'pricing' => [
                'base_price' => (float) $this->basePrice,
                'overtime_charge' => ['price' => (float) $this->overtimeCharge, 'per' => $this->overtimePer],
                'pricing_by_size' => [
                    'small' => (float) $this->basePrice,
                    'medium' => (float) $this->basePrice,
                    'large' => (float) $this->basePrice,
                ],
            ],
            'add_ons_compatibility' => false,
            'visibility_controls' => $this->visibilityControls,
        ];
    }

    public function save(): void
    {
        $this->validate([
            'addOnsName' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $profileId = $this->profileId();

        if (!$profileId) {
            $this->addError('addOnsName', 'Groomer/Spacer profile not found for current user.');
            return;
        }

        if ($this->editingId) {
            $addOn = AddOn::query()->where('groomer_spacer_id', $profileId)->find($this->editingId);

            if (!$addOn) {
                $this->addError('addOnsName', 'Add-on not found.');
                return;
            }

            $addOn->update($this->payload());
        } else {
            $addOn = AddOn::create(
                array_merge($this->payload(), [
                    'groomer_spacer_id' => $profileId,
                ]),
            );
        }

        $this->dispatch('add-on-created', itemId: $addOn->id);
        $this->reset(['addOnsName', 'description', 'otherPets', 'otherPetInput']);
    }

    public function deleteCurrent(): void
    {
        $profileId = $this->profileId();

        if (!$profileId || !$this->editingId) {
            return;
        }

        $addOn = AddOn::query()->where('groomer_spacer_id', $profileId)->find($this->editingId);

        if ($addOn) {
            $addOn->delete();
        }

        $this->dispatch('add-on-deleted');
        $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end")); window.dispatchEvent(new CustomEvent("service-form-cancel"))');
    }
}; ?>

<section class="service-form-wrapper" aria-label="Add add-on form"
    x-data="{ visibilityControls: $wire.entangle('visibilityControls').live }"
    x-on:service-form-delete.window="$wire.deleteCurrent()"
    x-on:add-on-edit-requested.window="
        const d = $event.detail || {};
        const id = Number(d.addOnId || d.add_on_id || d.id || 0);
        if (d.state) { window.applyServiceFormState($wire, d.state); }
        if (id) { $wire.loadAddOn(id, !!d.state); }
    ">
    <form class="service-form service-form-space" wire:submit.prevent="save">
        <div class="service-form-card">
            <div class="service-form-grid">
                <label class="service-field">
                    <span>Add-on Name</span>
                    <input type="text" placeholder="Storage Locker" wire:model="addOnsName"
                        @input="window.dispatchEvent(new CustomEvent('service-form-title-changed', { detail: { title: $event.target.value } }))" />
                </label>

                <label class="service-field">
                    <span>Description</span>
                    <input type="text" placeholder="Keep your belongings in one of our on-site lockers."
                        wire:model="description" />
                </label>
            </div>
        </div>

        <div class="service-form-block">
            <h4 class="service-section-title">Pet Compatibility</h4>
            <div class="service-form-card">
                <x-business-hub.services.pet-compatibility title="" other-pets-input-id="addon-other-pet-space" />
            </div>
        </div>

        <div class="service-form-block">
            <h4 class="service-section-title">Duration & Price</h4>
            <div class="service-form-card">
                <x-business-hub.services.duration-price :show-by-size="false" :show-buffer="false"
                    :show-base-duration="false" money />
            </div>
        </div>

        <div class="service-form-card service-settings-card">
            <h4>Setting Options</h4>
            <div class="service-setting-row" :class="{ 'is-active': visibilityControls }">
                <div>
                    <strong>Visibility Controls</strong>
                    <p>This add-on is active on your profile — clients can book it now</p>
                </div>
                <button type="button" class="service-toggle" :class="{ 'is-on': visibilityControls }"
                    @click="visibilityControls = !visibilityControls"></button>
            </div>
        </div>

        <x-business-hub.services.form-footer />
    </form>
</section>

<style>
    .service-form-space {
        gap: 40px;
    }

    .service-form-space .service-form-block {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .service-form-space .service-form-card {
        overflow: visible;
    }

    .service-form-space .service-setting-row strong {
        font-size: 18px;
        font-weight: 600;
    }

    .service-form-space .service-setting-row p {
        color: #9c9790;
        font-size: 18px;
        font-weight: 400;
        line-height: normal;
    }

    .service-form-space .service-toggle {
        width: 44px;
        height: 24px;
        background: #d4d4d4;
    }

    .service-form-space .service-toggle::after {
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background: #fff;
    }

    .service-form-space .service-toggle.is-on {
        background: #d8e8b7;
    }

    .service-form-space .service-toggle.is-on::after {
        left: 22px;
        background: transparent url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 20 20' fill='none'%3E%3Cpath d='M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z' fill='white'/%3E%3C/svg%3E") center / 20px 20px no-repeat;
    }

    .service-form-space .service-toggle.is-on::before {
        content: none;
    }

    .service-form-space .service-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .service-form-space .service-field>span {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .service-form-space .service-form-grid .service-field input {
        width: 100%;
        height: 48px;
        border: 1px solid #d4d4d4;
        border-radius: 10px;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
        padding: 0 20px;
    }

    .service-form-space .service-field input::placeholder {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
    }

    .save-btn-loading {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .hidden {
        display: none !important;
    }

    .save-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        border-radius: 999px;
        animation: save-spin 0.8s linear infinite;
    }

    @keyframes save-spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
