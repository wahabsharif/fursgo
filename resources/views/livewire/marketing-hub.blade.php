<?php

use App\Models\PromoCode;
use App\Support\MarketingHubPromos;
use App\Support\MarketingHubStats;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Volt\Component;

new #[Layout('layouts.marketing-hub')] class extends Component {
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

    public function with(): array
    {
        $spacerId = auth('groomer_spacer')->id();

        return [
            'mh' => MarketingHubStats::forSpacer($spacerId),
            'mhPromos' => MarketingHubPromos::forSpacer($spacerId),
        ];
    }
}; ?>

<div>
    @include('marketing-hub')
</div>
