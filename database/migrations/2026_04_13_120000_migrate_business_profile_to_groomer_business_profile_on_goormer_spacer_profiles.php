<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replace legacy business_profile with groomer_business_profile and move nested groomer data out of business_basics JSON.
     */
    public function up(): void
    {
        if (! Schema::hasTable('goormer_spacer_profiles')) {
            return;
        }

        $hasLegacy = Schema::hasColumn('goormer_spacer_profiles', 'business_profile');
        $hasNew = Schema::hasColumn('goormer_spacer_profiles', 'groomer_business_profile');

        if (! $hasNew) {
            Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
                $table->json('groomer_business_profile')->nullable();
            });
        }

        if ($hasLegacy || $hasNew) {
            DB::table('goormer_spacer_profiles')->orderBy('id')->chunkById(100, function ($rows) use ($hasLegacy) {
                foreach ($rows as $row) {
                    $bbRaw = $row->business_basics ?? null;
                    $bb = [];
                    if ($bbRaw !== null && $bbRaw !== '') {
                        $bb = is_string($bbRaw) ? (json_decode($bbRaw, true) ?: []) : (array) $bbRaw;
                    }
                    $groomerFromBasics = $bb['groomer_profile'] ?? null;
                    if (is_array($groomerFromBasics)) {
                        unset($bb['groomer_profile']);
                    } else {
                        $groomerFromBasics = null;
                    }

                    $groomerFromColumn = null;
                    if ($hasLegacy && isset($row->business_profile)) {
                        $bp = $row->business_profile;
                        if (is_string($bp) && $bp !== '') {
                            $groomerFromColumn = json_decode($bp, true);
                        } elseif (is_array($bp)) {
                            $groomerFromColumn = $bp;
                        }
                    }

                    $groomerPayload = null;
                    if (is_array($groomerFromBasics) && count($groomerFromBasics) > 0) {
                        $groomerPayload = $groomerFromBasics;
                    } elseif (is_array($groomerFromColumn) && count($groomerFromColumn) > 0) {
                        $groomerPayload = $groomerFromColumn;
                    }

                    $updates = [];
                    if ($groomerPayload !== null) {
                        $updates['groomer_business_profile'] = json_encode($groomerPayload);
                    }
                    if ($groomerFromBasics !== null) {
                        $updates['business_basics'] = empty($bb) ? null : json_encode($bb);
                    }

                    if ($updates !== []) {
                        DB::table('goormer_spacer_profiles')->where('id', $row->id)->update($updates);
                    }
                }
            });
        }

        if ($hasLegacy) {
            Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
                $table->dropColumn('business_profile');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('goormer_spacer_profiles')) {
            return;
        }

        if (! Schema::hasColumn('goormer_spacer_profiles', 'groomer_business_profile')) {
            return;
        }

        if (! Schema::hasColumn('goormer_spacer_profiles', 'business_profile')) {
            Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
                $table->json('business_profile')->nullable();
            });
        }

        DB::table('goormer_spacer_profiles')->orderBy('id')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                $g = $row->groomer_business_profile ?? null;
                DB::table('goormer_spacer_profiles')->where('id', $row->id)->update([
                    'business_profile' => $g,
                ]);
            }
        });

        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            $table->dropColumn('groomer_business_profile');
        });
    }
};
