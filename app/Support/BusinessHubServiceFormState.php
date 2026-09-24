<?php

namespace App\Support;

use App\Models\AddOn;
use App\Models\Service;

class BusinessHubServiceFormState
{
    public static function fromService(Service $service, string $variant = 'groomer'): array
    {
        $compat = (array) $service->pet_compatibility;
        $duration = (array) $service->duration;
        $pricing = (array) $service->pricing;
        $shortMin = $variant === 'space';
        $basePrice = self::numericPrice(data_get($pricing, 'base_price'), $shortMin ? 25.0 : 35.0);
        $largePrice = data_get($pricing, 'pricing_by_size.large');

        $state = [
            'editingId' => (int) $service->id,
            'serviceName' => (string) $service->service_name,
            'description' => (string) ($service->description ?? ''),
            'selectedPets' => array_values((array) data_get($compat, 'pet_types', [])),
            'otherPets' => array_values((array) data_get($compat, 'other_pets', [])),
            'selectedSizes' => array_values((array) data_get($compat, 'pet_sizes', [])),
            'baseDuration' => self::minutesLabel(data_get($duration, 'base_duration'), $shortMin ? '60 Minutes' : '90 Minutes', $shortMin),
            'bufferTime' => self::minutesLabel(data_get($duration, 'buffer_time'), $shortMin ? '15 min' : '15 Minutes', $shortMin),
            'basePrice' => $basePrice,
            'overtimeCharge' => self::numericPrice(data_get($pricing, 'overtime_charge.price'), 10.0),
            'overtimePer' => self::minutesLabel(data_get($pricing, 'overtime_charge.per'), '15 Minutes'),
            'addOnsCompatibility' => (bool) $service->add_ons_compatibility,
            'visibilityControls' => (bool) $service->visibility_controls,
        ];

        if (!$shortMin) {
            $state['durationSmall'] = self::minutesLabel(data_get($duration, 'duration_by_size.small'), '90 Minutes');
            $state['durationMedium'] = self::minutesLabel(data_get($duration, 'duration_by_size.medium'), '90 Minutes');
            $state['durationLarge'] = self::minutesLabel(data_get($duration, 'duration_by_size.large'), '90 Minutes');
            $state['priceSmall'] = self::numericPrice(data_get($pricing, 'pricing_by_size.small'), $basePrice);
            $state['priceMedium'] = self::numericPrice(data_get($pricing, 'pricing_by_size.medium'), $basePrice);
            $state['priceLarge'] = $largePrice === '' || $largePrice === null ? null : (float) $largePrice;
        }

        return $state;
    }

    public static function fromAddOn(AddOn $addOn, string $variant = 'groomer'): array
    {
        $compat = (array) $addOn->pet_compatibility;
        $duration = (array) $addOn->duration;
        $pricing = (array) $addOn->pricing;
        $shortMin = $variant === 'space';
        $basePrice = self::numericPrice(data_get($pricing, 'base_price'), $shortMin ? 25.0 : 35.0);
        $largePrice = data_get($pricing, 'pricing_by_size.large');

        $state = [
            'editingId' => (int) $addOn->id,
            'addOnsName' => (string) $addOn->add_ons_name,
            'description' => (string) ($addOn->description ?? ''),
            'selectedPets' => array_values((array) (data_get($compat, 'pet_type') ?: data_get($compat, 'pet_types', []))),
            'otherPets' => array_values((array) data_get($compat, 'other_pets', [])),
            'selectedSizes' => array_values((array) (data_get($compat, 'pet_size') ?: data_get($compat, 'pet_sizes', []))),
            'basePrice' => $basePrice,
            'overtimeCharge' => self::numericPrice(data_get($pricing, 'overtime_charge.price'), 10.0),
            'overtimePer' => self::minutesLabel(data_get($pricing, 'overtime_charge.per'), '15 Minutes'),
            'visibilityControls' => (bool) $addOn->visibility_controls,
        ];

        if (!$shortMin) {
            $state['baseDuration'] = self::minutesLabel(data_get($duration, 'base_duration'), '90 Minutes');
            $state['bufferTime'] = self::minutesLabel(data_get($duration, 'buffer_time'), '15 Minutes');
            $state['durationSmall'] = self::minutesLabel(data_get($duration, 'duration_by_size.small'), '90 Minutes');
            $state['durationMedium'] = self::minutesLabel(data_get($duration, 'duration_by_size.medium'), '90 Minutes');
            $state['durationLarge'] = self::minutesLabel(data_get($duration, 'duration_by_size.large'), '90 Minutes');
            $state['priceSmall'] = self::numericPrice(data_get($pricing, 'pricing_by_size.small'), $basePrice);
            $state['priceMedium'] = self::numericPrice(data_get($pricing, 'pricing_by_size.medium'), $basePrice);
            $state['priceLarge'] = $largePrice === '' || $largePrice === null ? null : (float) $largePrice;
            $state['addOnsCompatibility'] = (bool) $addOn->add_ons_compatibility;
        }

        return $state;
    }

    public static function minutesLabel(mixed $value, string $fallback, bool $shortMin = false): string
    {
        if (is_string($value) && preg_match('/\d+/', $value, $matches)) {
            $minutes = (int) $matches[0];
            if ($shortMin && str_contains($value, 'min') && !str_contains($value, 'Minutes')) {
                return $minutes . ' min';
            }

            return $minutes . ' Minutes';
        }

        $minutes = (int) $value;

        if ($minutes <= 0) {
            return $fallback;
        }

        return $shortMin && str_contains($fallback, 'min') ? $minutes . ' min' : $minutes . ' Minutes';
    }

    public static function numericPrice(mixed $value, float $fallback): float
    {
        if ($value === null || $value === '') {
            return $fallback;
        }

        return (float) $value;
    }
}
