<?php

use App\Models\PromoCode;
use App\Models\Service;
use App\Support\MarketingHubPromos;
use App\Support\MarketingHubStats;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Volt\Component;

new #[Layout('layouts.marketing-hub')] class extends Component {
    public bool $showPromoForm = false;

    public ?int $editingPromoId = null;

    public string $discountCode = '';

    public string $description = '';

    public string $startDate = '';

    public string $endDate = '';

    public bool $noEndDate = false;

    public string $discountType = PromoCode::DISCOUNT_TYPE_PERCENT;

    public string $discountAmount = '10';

    public bool $allServices = true;

    public string $selectedService = '';

    public bool $allPetTypes = true;

    public string $selectedPetType = 'Cat';

    public bool $allPetSizes = true;

    public string $selectedPetSize = 'Small 0 - 7 kg';

    public bool $visibility = true;

    #[Renderless]
    public function togglePromoVisibility(int $promoCodeId): void
    {
        $spacerId = auth('groomer_spacer')->id();

        if (!$spacerId) {
            return;
        }

        $promo = PromoCode::query()->where('goormer_spacer_id', $spacerId)->whereKey($promoCodeId)->first();

        if (!$promo) {
            return;
        }

        $promo->visibility = !$promo->visibility;
        $promo->save();
    }

    public function openCreatePromo(): void
    {
        $this->resetPromoForm();
        $this->showPromoForm = true;
        $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end"))');
    }

    public function openEditPromo(int $promoCodeId): void
    {
        $spacerId = auth('groomer_spacer')->id();

        if (!$spacerId) {
            $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end"))');

            return;
        }

        $promo = PromoCode::query()->where('goormer_spacer_id', $spacerId)->whereKey($promoCodeId)->first();

        if (!$promo) {
            $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end"))');

            return;
        }

        $services = is_array($promo->services) ? $promo->services : [];
        $petTypes = is_array($promo->pet_types) ? $promo->pet_types : [];
        $petSizes = is_array($promo->pet_sizes) ? $promo->pet_sizes : [];

        $this->editingPromoId = $promo->id;
        $this->discountCode = strtoupper(preg_replace('/\s+/', '', (string) $promo->discount_code) ?? '');
        $this->description = (string) ($promo->description ?? '');
        $this->startDate = $promo->start_date?->format('Y-m-d') ?? now()->toDateString();
        $this->endDate = $promo->end_date?->format('Y-m-d') ?? '';
        $this->noEndDate = (bool) $promo->no_end_date;
        $this->discountType = in_array($promo->discount_type, PromoCode::DISCOUNT_TYPES, true) ? (string) $promo->discount_type : PromoCode::DISCOUNT_TYPE_PERCENT;
        $this->discountAmount = rtrim(rtrim(number_format((float) $promo->discount_amount, 2, '.', ''), '0'), '.') ?: '0';
        $this->allServices = (bool) ($services['allow_all'] ?? true);
        $this->selectedService = (string) ($services['selected'][0] ?? '' ?: '');
        $this->allPetTypes = (bool) ($petTypes['allow_all'] ?? true);
        $this->selectedPetType = (string) ($petTypes['selected'][0] ?? '' ?: 'Cat');
        $this->allPetSizes = (bool) ($petSizes['allow_all'] ?? true);
        $this->selectedPetSize = (string) ($petSizes['selected'][0] ?? '' ?: 'Small 0 - 7 kg');
        $this->visibility = (bool) $promo->visibility;
        $this->showPromoForm = true;
        $this->resetValidation();
        $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end"))');
    }

    public function cancelPromoForm(): void
    {
        $this->resetPromoForm();
        $this->showPromoForm = false;
        $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end"))');
    }

    public function savePromo(): void
    {
        $spacerId = auth('groomer_spacer')->id();

        if (!$spacerId) {
            $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end"))');

            return;
        }

        try {
            $this->discountCode = strtoupper(preg_replace('/\s+/', '', (string) $this->discountCode) ?? '');

            $validated = $this->validate([
                'discountCode' => ['required', 'string', 'max:80', Rule::unique('promo_codes', 'discount_code')->where(fn($query) => $query->where('goormer_spacer_id', $spacerId))->ignore($this->editingPromoId)],
                'description' => ['nullable', 'string', 'max:255'],
                'startDate' => ['nullable', 'date'],
                'endDate' => [Rule::requiredIf(!$this->noEndDate), 'nullable', 'date', 'after_or_equal:startDate'],
                'noEndDate' => ['boolean'],
                'discountType' => ['required', Rule::in(PromoCode::DISCOUNT_TYPES)],
                'discountAmount' => ['required', 'numeric', 'min:0'],
                'allServices' => ['boolean'],
                'selectedService' => array_values(array_filter([Rule::requiredIf(!$this->allServices), 'nullable', 'string', 'max:120', $this->allServices ? null : Rule::in($this->serviceOptionsForUser())])),
                'allPetTypes' => ['boolean'],
                'selectedPetType' => [Rule::requiredIf(!$this->allPetTypes), 'nullable', 'string', 'max:80'],
                'allPetSizes' => ['boolean'],
                'selectedPetSize' => [Rule::requiredIf(!$this->allPetSizes), 'nullable', 'string', 'max:80'],
                'visibility' => ['boolean'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end"))');
            throw $e;
        }

        $payload = [
            'goormer_spacer_id' => $spacerId,
            'discount_code' => $validated['discountCode'],
            'description' => trim((string) ($validated['description'] ?? '')) ?: null,
            'start_date' => $validated['startDate'] ?: null,
            'end_date' => $this->noEndDate ? null : ($validated['endDate'] ?: null),
            'no_end_date' => $this->noEndDate,
            'discount_type' => $validated['discountType'],
            'discount_amount' => $validated['discountAmount'],
            'services' => [
                'allow_all' => $this->allServices,
                'selected' => $this->allServices || $this->selectedService === '' ? [] : [$this->selectedService],
            ],
            'pet_types' => [
                'allow_all' => $this->allPetTypes,
                'selected' => $this->allPetTypes || $this->selectedPetType === '' ? [] : [$this->selectedPetType],
            ],
            'pet_sizes' => [
                'allow_all' => $this->allPetSizes,
                'selected' => $this->allPetSizes || $this->selectedPetSize === '' ? [] : [$this->selectedPetSize],
            ],
            'visibility' => $this->visibility,
        ];

        if ($this->editingPromoId) {
            PromoCode::query()->where('goormer_spacer_id', $spacerId)->whereKey($this->editingPromoId)->update($payload);
        } else {
            PromoCode::query()->create($payload);
        }

        $this->resetPromoForm();
        $this->showPromoForm = false;
        $this->dispatch('promo-form-saved');
        $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end"))');
    }

    public function updatedDiscountCode(?string $value): void
    {
        $normalized = strtoupper(preg_replace('/\s+/', '', (string) $value) ?? '');

        if ($this->discountCode !== $normalized) {
            $this->discountCode = $normalized;
        }
    }

    public function updatedNoEndDate(bool $value): void
    {
        if ($value) {
            $this->endDate = '';
        }
    }

    public function updatedAllServices(bool $value): void
    {
        if ($value) {
            $this->selectedService = '';
        } elseif ($this->selectedService === '') {
            $this->selectedService = $this->serviceOptionsForUser()[0] ?? '';
        }
    }

    /**
     * @return list<string>
     */
    private function serviceOptionsForUser(): array
    {
        $spacerId = auth('groomer_spacer')->id();

        if (!$spacerId) {
            return [];
        }

        return Service::query()->where('groomer_spacer_id', $spacerId)->orderBy('service_name')->pluck('service_name')->map(fn($name) => trim((string) $name))->filter()->unique()->values()->all();
    }

    public function updatedAllPetTypes(bool $value): void
    {
        if ($value) {
            $this->selectedPetType = '';
        } elseif ($this->selectedPetType === '') {
            $this->selectedPetType = 'Cat';
        }
    }

    public function updatedAllPetSizes(bool $value): void
    {
        if ($value) {
            $this->selectedPetSize = '';
        } elseif ($this->selectedPetSize === '') {
            $this->selectedPetSize = 'Small 0 - 7 kg';
        }
    }

    private function resetPromoForm(): void
    {
        $this->editingPromoId = null;
        $this->discountCode = '';
        $this->description = '';
        $this->startDate = now()->toDateString();
        $this->endDate = '';
        $this->noEndDate = false;
        $this->discountType = PromoCode::DISCOUNT_TYPE_PERCENT;
        $this->discountAmount = '10';
        $this->allServices = true;
        $this->selectedService = '';
        $this->allPetTypes = true;
        $this->selectedPetType = 'Cat';
        $this->allPetSizes = true;
        $this->selectedPetSize = 'Small 0 - 7 kg';
        $this->visibility = true;
        $this->resetValidation();
    }

    public function with(): array
    {
        $spacerId = auth('groomer_spacer')->id();

        return [
            'mh' => MarketingHubStats::forSpacer($spacerId),
            'mhPromos' => MarketingHubPromos::forSpacer($spacerId),
            'promoServiceOptions' => $this->serviceOptionsForUser(),
            'promoPetTypeOptions' => ['Cat', 'Dog', 'Other'],
            'promoPetSizeOptions' => ['Small 0 - 7 kg', 'Medium 8 - 18 kg', 'Large 19+ kg'],
        ];
    }
}; ?>

<div>
    @include('marketing-hub')
</div>
