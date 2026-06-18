<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('goormer_spacer_profiles', 'auto_accept_booking')) {
                $table->boolean('auto_accept_booking')->default(true)->after('legal_policy_agreements');
            }
        });
    }

    public function down(): void
    {
        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('goormer_spacer_profiles', 'auto_accept_booking')) {
                $table->dropColumn('auto_accept_booking');
            }
        });
    }
};
