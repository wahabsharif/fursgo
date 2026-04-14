<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('goormer_spacer_profiles')) {
            return;
        }

        if (! Schema::hasColumn('goormer_spacer_profiles', 'remember_token')) {
            Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
                $table->rememberToken();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('goormer_spacer_profiles')
            || ! Schema::hasColumn('goormer_spacer_profiles', 'remember_token')) {
            return;
        }

        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
    }
};
