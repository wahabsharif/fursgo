<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_areas', function (Blueprint $table) {
            if (!Schema::hasColumn('service_areas', 'is_paused')) {
                $table->boolean('is_paused')->default(false)->after('map_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_areas', function (Blueprint $table) {
            if (Schema::hasColumn('service_areas', 'is_paused')) {
                $table->dropColumn('is_paused');
            }
        });
    }
};
