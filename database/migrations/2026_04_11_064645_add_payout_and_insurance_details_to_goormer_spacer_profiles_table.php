<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            $table->json('payout_details')->nullable()->after('business_details');
            $table->json('insurance_details')->nullable()->after('payout_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            $table->dropColumn(['payout_details', 'insurance_details']);
        });
    }
};
