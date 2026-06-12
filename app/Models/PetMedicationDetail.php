<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class PetMedicationDetail extends Model
{
    protected $fillable = [
        'pet_owner_id',
        'pet_detail_id',
        'last_verified',
        'veterinary_clinic',
        'vaccinations',
        'health_conditions',
        'current_medication',
        'allergies',
        'emergency_contact',
        'groomer_guidance_notes',
        'preferred_grooming_style',
        'grooming_behaviour',
        'tolerance_levels',
        'product_preferences',
        'handling_notes',
        'photo_gallery',
        'groomer_notes',
        'owner_notes',
    ];

    protected $casts = [
        'last_verified' => 'datetime',
        'vaccinations' => 'array',
        'health_conditions' => 'array',
        'current_medication' => 'array',
        'allergies' => 'array',
        'emergency_contact' => 'array',
        'preferred_grooming_style' => 'array',
        'grooming_behaviour' => 'array',
        'tolerance_levels' => 'array',
        'photo_gallery' => 'array',
        'groomer_notes' => 'array',
        'owner_notes' => 'array',
    ];

    public function petOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pet_owner_id');
    }

    public function petDetail(): BelongsTo
    {
        return $this->belongsTo(PetDetail::class);
    }

    public static function parseVaccinationDate(mixed $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse((string) $value)->startOfDay();
        } catch (Throwable) {
            return null;
        }
    }

    public static function formatVaccinationDate(mixed $value): string
    {
        return static::parseVaccinationDate($value)?->format('d M Y') ?? '—';
    }

    public function vaccinationIsOverdue(array $vaccination): bool
    {
        $nextDueDate = static::parseVaccinationDate($vaccination['next_due'] ?? null);

        return $nextDueDate ? $nextDueDate->lt(today()) : false;
    }

    public function hasOverdueVaccinations(): bool
    {
        foreach ($this->vaccinations ?? [] as $vaccination) {
            if ($this->vaccinationIsOverdue($vaccination)) {
                return true;
            }
        }

        return false;
    }

    public function vaccinationStatusLabel(): string
    {
        return $this->hasOverdueVaccinations() ? 'Over Due' : 'Up to Date';
    }

    /**
     * @param  array<int, string|array<string, mixed>>|null  $items
     * @return array<int, string>
     */
    public static function itemLabels(?array $items): array
    {
        return collect($items ?? [])
            ->map(function (mixed $item): string {
                if (is_string($item)) {
                    return trim($item);
                }

                if (!is_array($item)) {
                    return '';
                }

                return trim((string) (
                    $item['condition']
                        ?? $item['name']
                        ?? $item['allergen']
                        ?? $item['style']
                        ?? $item['temperament']
                        ?? ''
                ));
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string|array<string, mixed>>|null  $items
     */
    public static function formatItemList(?array $items): string
    {
        $labels = static::itemLabels($items);

        return $labels !== [] ? implode(', ', $labels) : '—';
    }

    /**
     * @return array<int, array{activity: string, label: string, tone: string}>
     */
    public function toleranceLevelRows(): array
    {
        $activities = [
            'bathing' => 'Bathing',
            'dryer' => 'Dryer',
            'nail_trim' => 'Nail trim',
        ];

        return collect($activities)
            ->map(function (string $activity, string $key): ?array {
                $raw = trim((string) (($this->tolerance_levels ?? [])[$key] ?? ''));

                if ($raw === '') {
                    return null;
                }

                return [
                    'activity' => $activity,
                    'label' => static::formatToleranceLabel($raw),
                    'tone' => static::toleranceTone($raw),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private static function formatToleranceLabel(string $raw): string
    {
        return match (strtolower($raw)) {
            'high' => 'Comfortable',
            'medium' => 'Slightly nervous',
            'low' => 'Sensitive paws',
            default => $raw,
        };
    }

    private static function toleranceTone(string $raw): string
    {
        $normalized = strtolower($raw);

        if (in_array($normalized, ['high', 'comfortable', 'relaxed', 'calm', 'fine', 'good', 'ok'], true)) {
            return 'ok';
        }

        if (preg_match('/\b(comfortable|relaxed|calm|fine|good)\b/', $normalized)) {
            return 'ok';
        }

        return 'caution';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function vaccinationRows(): array
    {
        return collect($this->vaccinations ?? [])
            ->map(function (array $vaccination) {
                $isOverdue = $this->vaccinationIsOverdue($vaccination);

                return [
                    'name' => trim((string) ($vaccination['name'] ?? 'Vaccine')) ?: 'Vaccine',
                    'status_label' => $isOverdue ? 'Over Due' : 'Up to Date',
                    'is_overdue' => $isOverdue,
                    'last_given' => static::formatVaccinationDate($vaccination['date'] ?? null),
                    'next_due' => static::formatVaccinationDate($vaccination['next_due'] ?? null),
                ];
            })
            ->values()
            ->all();
    }
}
