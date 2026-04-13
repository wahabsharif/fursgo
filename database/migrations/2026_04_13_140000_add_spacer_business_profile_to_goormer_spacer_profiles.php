<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('goormer_spacer_profiles')) {
            return;
        }

        if (! Schema::hasColumn('goormer_spacer_profiles', 'spacer_business_profile')) {
            Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
                $table->json('spacer_business_profile')->nullable();
            });
        }

        DB::table('goormer_spacer_profiles')->orderBy('id')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                if ($row->spacer_business_profile !== null && $row->spacer_business_profile !== '') {
                    continue;
                }

                $bbRaw = $row->business_basics ?? null;
                $bb = [];
                if ($bbRaw !== null && $bbRaw !== '') {
                    $bb = is_string($bbRaw) ? (json_decode($bbRaw, true) ?: []) : (array) $bbRaw;
                }
                $legacy = $bb['spacer_profile'] ?? null;
                if (! is_array($legacy) || $legacy === []) {
                    continue;
                }

                $payload = [
                    'legacy' => [
                        'location' => $legacy['location'] ?? '',
                        'capacity' => $legacy['capacity'] ?? '',
                        'amenities' => $legacy['amenities'] ?? '',
                    ],
                ];

                unset($bb['spacer_profile']);
                DB::table('goormer_spacer_profiles')->where('id', $row->id)->update([
                    'spacer_business_profile' => json_encode($payload),
                    'business_basics' => empty($bb) ? null : json_encode($bb),
                ]);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('goormer_spacer_profiles')) {
            return;
        }

        if (Schema::hasColumn('goormer_spacer_profiles', 'spacer_business_profile')) {
            Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
                $table->dropColumn('spacer_business_profile');
            });
        }
    }
};
