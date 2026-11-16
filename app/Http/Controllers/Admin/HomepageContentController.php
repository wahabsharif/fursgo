<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageItem;
use App\Models\HomepageSetting;
use App\Support\HomepageContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageContentController extends Controller
{
    private const SECTIONS = [
        'hero',
        'feature',
        'how_it_works',
        'popular_services',
        'testimonials',
        'mission',
        'service_map',
    ];

    public function update(Request $request, string $section): JsonResponse
    {
        if (! in_array($section, self::SECTIONS, true)) {
            return response()->json(['message' => 'Unknown section.'], 404);
        }

        $settings = HomepageSetting::current();

        if (! $settings) {
            return response()->json(['message' => 'Homepage content is not seeded yet.'], 422);
        }

        $this->decodeJsonFields($request, ['items', 'steps', 'cities']);

        match ($section) {
            'hero' => $this->saveHero($request, $settings),
            'feature' => $this->saveFeature($request, $settings),
            'how_it_works' => $this->saveHowItWorks($request, $settings),
            'popular_services' => $this->savePopularServices($request, $settings),
            'testimonials' => $this->saveTestimonials($request, $settings),
            'mission' => $this->saveMission($request, $settings),
            'service_map' => $this->saveServiceMap($request, $settings),
        };

        return response()->json([
            'ok' => true,
            'content' => HomepageContent::customer(),
        ]);
    }

    private function saveHero(Request $request, HomepageSetting $settings): void
    {
        $data = $request->validate([
            'headline' => ['required', 'string', 'max:255'],
            'rating_value' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rating_headline' => ['required', 'string', 'max:255'],
            'rating_copy' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $settings->hero_headline = $data['headline'];
        $settings->hero_rating_value = $data['rating_value'] ?? $settings->hero_rating_value;
        $settings->hero_rating_headline = $data['rating_headline'];
        $settings->hero_rating_copy = $data['rating_copy'] ?? '';

        if ($request->hasFile('photo')) {
            $settings->hero_photo = $this->storePhoto($request->file('photo'), $settings->hero_photo);
        }

        $settings->save();
    }

    private function saveFeature(Request $request, HomepageSetting $settings): void
    {
        $data = $request->validate([
            'headline' => ['required', 'string', 'max:255'],
            'items' => ['nullable', 'array'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.desc' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $settings->feature_headline = $data['headline'];

        if ($request->hasFile('photo')) {
            $settings->feature_photo = $this->storePhoto($request->file('photo'), $settings->feature_photo);
        }

        $settings->save();

        if ($request->has('items')) {
            $this->syncItems(HomepageItem::SECTION_FEATURE, collect($data['items'] ?? [])->map(fn (array $item, int $i) => [
                'title' => $item['title'],
                'description' => $item['desc'] ?? '',
                'sort_order' => $i + 1,
            ])->all());
        }
    }

    private function saveHowItWorks(Request $request, HomepageSetting $settings): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'intro' => ['nullable', 'string'],
            'steps' => ['nullable', 'array'],
            'steps.*.title' => ['required', 'string', 'max:255'],
            'steps.*.desc' => ['nullable', 'string'],
            'steps.*.image_path' => ['nullable', 'string', 'max:255'],
        ]);

        $settings->how_it_works_title = $data['title'];
        $settings->how_it_works_intro = $data['intro'] ?? '';
        $settings->save();

        if ($request->has('steps')) {
            $this->syncItems(HomepageItem::SECTION_HOW_IT_WORKS, collect($data['steps'] ?? [])->map(fn (array $step, int $i) => [
                'title' => $step['title'],
                'description' => $step['desc'] ?? '',
                'image' => $step['image_path'] ?? null,
                'sort_order' => $i + 1,
            ])->all());
        }
    }

    private function savePopularServices(Request $request, HomepageSetting $settings): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'intro' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.desc' => ['nullable', 'string'],
            'items.*.image_path' => ['nullable', 'string', 'max:255'],
            'items.*.shape_class' => ['nullable', 'string', 'max:40'],
        ]);

        $settings->popular_services_title = $data['title'];
        $settings->popular_services_intro = $data['intro'] ?? '';
        $settings->save();

        if ($request->has('items')) {
            $this->syncItems(HomepageItem::SECTION_POPULAR_SERVICE, collect($data['items'] ?? [])->map(fn (array $item, int $i) => [
                'title' => $item['title'],
                'description' => $item['desc'] ?? '',
                'image' => $item['image_path'] ?? null,
                'meta' => ['shape_class' => $item['shape_class'] ?? 'oval'],
                'sort_order' => $i + 1,
            ])->all());
        }
    }

    private function saveTestimonials(Request $request, HomepageSetting $settings): void
    {
        $data = $request->validate([
            'intro' => ['required', 'string', 'max:255'],
            'desc' => ['nullable', 'string'],
            'trust' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.quote' => ['nullable', 'string'],
            'items.*.when' => ['nullable', 'string', 'max:255'],
            'items.*.dateSubmitted' => ['nullable', 'string', 'max:255'],
            'items.*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'items.*.image_path' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'photo_index' => ['nullable', 'integer', 'min:0'],
        ]);

        $settings->testimonials_intro = $data['intro'];
        $settings->testimonials_desc = $data['desc'] ?? '';
        $settings->testimonials_trust = $data['trust'] ?? '';
        $settings->save();

        $items = collect($data['items'] ?? [])->values();

        if ($request->hasFile('photo') && $request->filled('photo_index')) {
            $index = (int) $request->input('photo_index');
            if ($items->has($index)) {
                $row = $items->get($index);
                $row['image_path'] = $this->storePhoto($request->file('photo'), $row['image_path'] ?? null);
                $items->put($index, $row);
            }
        }

        if ($request->has('items')) {
            $this->syncItems(HomepageItem::SECTION_TESTIMONIAL, $items->map(fn (array $item, int $i) => [
                'title' => $item['name'],
                'description' => $item['quote'] ?? '',
                'image' => $item['image_path'] ?? null,
                'meta' => [
                    'rating' => (int) ($item['rating'] ?? 5),
                    'date_label' => $item['when'] ?? ($item['dateSubmitted'] ?? ''),
                ],
                'sort_order' => $i + 1,
            ])->all());
        }
    }

    private function saveMission(Request $request, HomepageSetting $settings): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'desc' => ['nullable', 'string'],
            'cta' => ['required', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $settings->mission_title = $data['title'];
        $settings->mission_desc = $data['desc'] ?? '';
        $settings->mission_cta = $data['cta'];
        $settings->mission_cta_url = $data['cta_url'] ?? $settings->mission_cta_url;

        if ($request->hasFile('photo')) {
            $settings->mission_photo = $this->storePhoto($request->file('photo'), $settings->mission_photo);
        }

        $settings->save();
    }

    private function saveServiceMap(Request $request, HomepageSetting $settings): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'desc' => ['nullable', 'string'],
            'cities' => ['nullable', 'array'],
            'cities.*' => ['required', 'string', 'max:120'],
        ]);

        $settings->service_map_title = $data['title'];
        $settings->service_map_desc = $data['desc'] ?? '';
        $settings->save();

        if ($request->has('cities')) {
            $this->syncItems(HomepageItem::SECTION_CITY, collect($data['cities'] ?? [])->map(fn (string $name, int $i) => [
                'title' => $name,
                'sort_order' => $i + 1,
            ])->all());
        }
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function syncItems(string $section, array $rows): void
    {
        HomepageItem::query()->where('section', $section)->delete();

        foreach ($rows as $row) {
            HomepageItem::create([
                'section' => $section,
                'title' => $row['title'] ?? null,
                'description' => $row['description'] ?? null,
                'image' => $row['image'] ?? null,
                'meta' => $row['meta'] ?? null,
                'sort_order' => $row['sort_order'] ?? 0,
                'is_published' => true,
            ]);
        }
    }

    private function storePhoto($file, ?string $previousPath): string
    {
        if ($previousPath && str_starts_with($previousPath, 'homepage/') && Storage::disk('public')->exists($previousPath)) {
            Storage::disk('public')->delete($previousPath);
        }

        return $file->store('homepage', 'public');
    }

    /**
     * @param  list<string>  $keys
     */
    private function decodeJsonFields(Request $request, array $keys): void
    {
        foreach ($keys as $key) {
            $value = $request->input($key);

            if (! is_string($value) || $value === '') {
                continue;
            }

            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge([$key => $decoded]);
            }
        }
    }
}
