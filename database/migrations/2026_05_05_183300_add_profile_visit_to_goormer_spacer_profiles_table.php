<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('goormer_spacer_profiles', 'profile_visit')) {
                $table->unsignedInteger('profile_visit')->default(0)->after('spacer_business_profile');
            }
        });
    }

    public function down(): void
    {
        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('goormer_spacer_profiles', 'profile_visit')) {
                $table->dropColumn('profile_visit');
            }
        });
    }
};
