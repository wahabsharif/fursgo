<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroomersHostsSetting;
use App\Support\GroomersHostsContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroomersHostsContentController extends Controller
{
    private const SECTIONS = [
        'hero',
    ];

    public function update(Request $request, string $section): JsonResponse
    {
        if (! in_array($section, self::SECTIONS, true)) {
            return response()->json(['message' => 'Unknown section.'], 404);
        }

        $settings = GroomersHostsSetting::current();

        if (! $settings) {
            return response()->json(['message' => 'Groomers & Hosts content is not seeded yet.'], 422);
        }

        match ($section) {
            'hero' => $this->saveHero($request, $settings),
        };

        return response()->json([
            'ok' => true,
            'content' => GroomersHostsContent::page(),
        ]);
    }

    private function saveHero(Request $request, GroomersHostsSetting $settings): void
    {
        $data = $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:255'],
            'headline' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'primary_cta' => ['nullable', 'string', 'max:255'],
            'primary_cta_url' => ['nullable', 'string', 'max:255'],
            'secondary_cta' => ['nullable', 'string', 'max:255'],
            'secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $settings->hero_eyebrow = $data['eyebrow'] ?? '';
        $settings->hero_headline = $data['headline'];
        $settings->hero_description = $data['description'];
        $settings->hero_primary_cta = $data['primary_cta'] ?? $settings->hero_primary_cta;
        $settings->hero_primary_cta_url = $data['primary_cta_url'] ?? $settings->hero_primary_cta_url;
        $settings->hero_secondary_cta = $data['secondary_cta'] ?? $settings->hero_secondary_cta;
        $settings->hero_secondary_cta_url = $data['secondary_cta_url'] ?? $settings->hero_secondary_cta_url;

        if ($request->hasFile('photo')) {
            $settings->hero_photo = $this->storePhoto($request->file('photo'), $settings->hero_photo);
        }

        $settings->save();
    }

    private function storePhoto($file, ?string $previousPath): string
    {
        if ($previousPath && str_starts_with($previousPath, 'groomers-hosts/') && Storage::disk('public')->exists($previousPath)) {
            Storage::disk('public')->delete($previousPath);
        }

        return $file->store('groomers-hosts', 'public');
    }
}
