<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('goormer_spacer_profiles')
            && Schema::hasColumn('goormer_spacer_profiles', 'freelance_groomer_details')
            && ! Schema::hasColumn('goormer_spacer_profiles', 'freelance_details')) {
            Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
                $table->renameColumn('freelance_groomer_details', 'freelance_details');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('goormer_spacer_profiles')
            && Schema::hasColumn('goormer_spacer_profiles', 'freelance_details')
            && ! Schema::hasColumn('goormer_spacer_profiles', 'freelance_groomer_details')) {
            Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
                $table->renameColumn('freelance_details', 'freelance_groomer_details');
            });
        }
    }
};
