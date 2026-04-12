<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('goormer_spacer_profiles', 'personal_info_completed_at')) {
                $table->dropColumn('personal_info_completed_at');
            }
            if (Schema::hasColumn('goormer_spacer_profiles', 'personal_info_completed')) {
                $table->dropColumn('personal_info_completed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('goormer_spacer_profiles', 'personal_info_completed')) {
                $table->boolean('personal_info_completed')->default(false)->after('insurance_details');
            }
            if (! Schema::hasColumn('goormer_spacer_profiles', 'personal_info_completed_at')) {
                $table->timestamp('personal_info_completed_at')->nullable()->after('personal_info_completed');
            }
        });
    }
};
