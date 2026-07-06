<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table = 'goormer_spacer_profiles';
        $idColumn = 'id';

        DB::table($table)
            ->whereNotNull('freelance_details')
            ->chunkById(100, function ($profiles) use ($table, $idColumn) {
                foreach ($profiles as $profile) {
                    $details = $this->decodeJsonObject($profile->freelance_details);
                    if ($details === []) {
                        continue;
                    }

                    $legacy = $details['id_verification_images'] ?? null;
                    $governmentId = $details['government_id'] ?? null;

                    if (!is_array($governmentId) || $governmentId === []) {
                        if (is_array($legacy) && $legacy !== []) {
                            $details['government_id'] = array_values(array_filter(
                                $legacy,
                                fn($path) => is_string($path) && $path !== ''
                            ));
                        }
                    }

                    unset($details['id_verification_images']);

                    DB::table($table)
                        ->where($idColumn, $profile->id)
                        ->update(['freelance_details' => json_encode($details)]);
                }
            }, $idColumn);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = 'goormer_spacer_profiles';
        $idColumn = 'id';

        DB::table($table)
            ->whereNotNull('freelance_details')
            ->chunkById(100, function ($profiles) use ($table, $idColumn) {
                foreach ($profiles as $profile) {
                    $details = $this->decodeJsonObject($profile->freelance_details);
                    if ($details === []) {
                        continue;
                    }

                    $governmentId = $details['government_id'] ?? null;
                    if (is_array($governmentId) && $governmentId !== []) {
                        $details['id_verification_images'] = $governmentId;
                    }

                    unset($details['government_id']);

                    DB::table($table)
                        ->where($idColumn, $profile->id)
                        ->update(['freelance_details' => json_encode($details)]);
                }
            }, $idColumn);
    }

    private function decodeJsonObject(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }
};
