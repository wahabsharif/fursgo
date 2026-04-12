<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('goormer_spacer_profiles', 'information_accuracy_confirmed')) {
                $table->boolean('information_accuracy_confirmed')->default(false)->after('full_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('goormer_spacer_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('goormer_spacer_profiles', 'information_accuracy_confirmed')) {
                $table->dropColumn('information_accuracy_confirmed');
            }
        });
    }
};
