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
            ->whereNotNull('payout_details')
            ->chunkById(100, function ($profiles) use ($table, $idColumn) {
                foreach ($profiles as $profile) {
                    $payoutDetails = $this->decodePayoutDetails($profile->payout_details);

                    if ($payoutDetails === [] || filled($payoutDetails['payout_frequency'] ?? null)) {
                        continue;
                    }

                    $payoutDetails['payout_frequency'] = 'Weekly';

                    DB::table($table)
                        ->where($idColumn, $profile->id)
                        ->update(['payout_details' => json_encode($payoutDetails)]);
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
            ->whereNotNull('payout_details')
            ->chunkById(100, function ($profiles) use ($table, $idColumn) {
                foreach ($profiles as $profile) {
                    $payoutDetails = $this->decodePayoutDetails($profile->payout_details);

                    if (($payoutDetails['payout_frequency'] ?? null) !== 'Weekly') {
                        continue;
                    }

                    unset($payoutDetails['payout_frequency']);

                    DB::table($table)
                        ->where($idColumn, $profile->id)
                        ->update(['payout_details' => $payoutDetails === [] ? null : json_encode($payoutDetails)]);
                }
            }, $idColumn);
    }

    private function decodePayoutDetails(mixed $value): array
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
