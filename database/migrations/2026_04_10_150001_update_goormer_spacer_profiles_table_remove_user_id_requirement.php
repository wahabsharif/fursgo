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
        if (! Schema::hasTable('goormer_spacer_profiles')
            || ! Schema::hasColumn('goormer_spacer_profiles', 'user_id')) {
            return;
        }

        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('goormer_spacer_profiles')
            || Schema::hasColumn('goormer_spacer_profiles', 'user_id')) {
            return;
        }

        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
