<?php

namespace App\Support;

use App\Models\Groomer;
use App\Models\Space;
use Illuminate\Support\Collection;

/**
 * Shared search listing queries for /search-results and unavailability variants.
 * Keep design pages thin; evolve filters here later.
 */
class SearchResultsData
{
    public static function groomers(string $search = '', string $sort = ''): Collection
    {
        $results = Groomer::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('studio_name', 'like', '%'.$search.'%');
                });
            })
            ->when($sort !== '', function ($query) use ($sort) {
                match ($sort) {
                    'distance' => $query->orderBy('distance'),
                    'lowest_price' => $query->orderBy('price'),
                    default => $query->latest(),
                };
            })
            ->get();

        if ($results->isEmpty() && $search === '' && $sort === '') {
            return self::showcaseGroomers();
        }

        return $results;
    }

    public static function spaces(string $search = '', ?string $venueType = null): Collection
    {
        $results = Space::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($venueType, function ($query) use ($venueType) {
                $query->where('venue_type', $venueType);
            })
            ->get();

        if ($results->isEmpty() && $search === '' && $venueType === null) {
            return self::showcaseSpaces();
        }

        return $results;
    }

    /**
     * Design cards from custom search_results.php, used until live listings exist.
     */
    public static function showcaseGroomers(): Collection
    {
        $slots = ['Mon 1, 08:30 AM', 'Tue 15, 12:30 PM', 'Wed 27, 09:15 AM', 'Thu 9, 15:45 PM'];
        $names = ['Sarah W.', 'Ken T.', 'Cathy P.', 'Sarah W.'];
        $images = ['images/card1.png', 'images/card2.png', 'images/card3.png', 'images/card1.png'];
        $tags = [
            ['Home Visit', 'Mobile Station'],
            ['Salons'],
            ["Groomer's studio"],
            ['Visiting Groomers'],
        ];
        $distances = ['1.0', '1.6', '2.2', '2.8'];
        $prices = [30, 37, 44, 51];

        $experienceShort = 'Gentle, breed-specific trims · 6+ years experience.';
        $experienceLong = 'Hi, I\'m Sarah — a professional groomer in West London, specialising in small breeds, anxious pets, & gentle handling! Gentle, breed-specific trims · 6+ years experience.';

        return collect($names)->map(function (string $name, int $index) use ($slots, $images, $tags, $distances, $prices, $experienceShort, $experienceLong) {
            $imageUrl = asset($images[$index]);

            return [
                'name' => $name,
                'studio_name' => "Sarah's Grooming Studio",
                'distance' => $distances[$index],
                'rating' => '4.3',
                'reviews_count' => 20,
                'rating_count' => 20,
                'experience_text' => $experienceShort,
                'experience' => $experienceLong,
                'price' => $prices[$index],
                'image_url' => $images[$index],
                'image' => $imageUrl,
                'tags' => $tags[$index],
                'slots' => $slots,
                'is_top_rated' => true,
                'top_rated' => true,
            ];
        });
    }

    public static function showcaseSpaces(): Collection
    {
        $slots = ['Mon 1, 08:30 AM', 'Wed 27, 09:15 AM'];
        $rows = [
            [
                'name' => 'The Garden Grooming Spot',
                'image' => 'images/space_card3.png',
                'tag' => 'Salon',
                'distance' => '1.0 mi',
                'price' => 30,
                'host' => 'Chloe D.',
                'description' => 'Outdoor garden grooming area. Calm, spacious, and ideal for stress-free sessions in fresh air.',
            ],
            [
                'name' => 'Paws & Bubbles',
                'image' => 'images/space_card1.png',
                'tag' => 'Garden / Shed',
                'distance' => '1.0 mi',
                'price' => 37,
            ],
            [
                'name' => 'Furs & Co. Studio',
                'image' => 'images/space_card2.png',
                'tag' => 'Private rooms',
                'distance' => '1.0 mi',
                'price' => 44,
            ],
            [
                'name' => 'Furs & Co. Studio',
                'image' => 'images/space_card3.png',
                'tag' => 'Mobile station',
                'distance' => '1.0 mi',
                'price' => 51,
            ],
            [
                'name' => 'Paws Paradise',
                'image' => 'images/space_card1.png',
                'tag' => 'Others',
                'distance' => '1.0 mi',
                'price' => 37,
            ],
        ];

        $description = 'Professional grooming spaces in London. Spotless, well-equipped, easy to access.';

        return collect($rows)->map(function (array $row) use ($slots, $description) {
            $imageUrl = asset($row['image']);

            return [
                'name' => $row['name'],
                'hosted_by' => $row['host'] ?? 'Dev É.',
                'host' => $row['host'] ?? 'Dev É.',
                'host_name' => $row['host'] ?? 'Dev É.',
                'distance' => $row['distance'],
                'rating' => '4.3',
                'reviews_count' => 20,
                'reviews' => 20,
                'experience' => $row['description'] ?? $description,
                'description' => $row['description'] ?? $description,
                'amenities' => 'Bath • Table • Dryer ...',
                'slots' => $slots,
                'price' => $row['price'],
                'time_frame' => '/ hour',
                'price_unit' => '/ hour',
                'image' => $imageUrl,
                'image_url' => $imageUrl,
                'tags' => [$row['tag']],
                'is_top_rated' => true,
                'top_rated' => true,
            ];
        });
    }
}
