<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('pet_medication_details', 'status')) {
            return;
        }

        Schema::table('pet_medication_details', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('pet_medication_details', function (Blueprint $table) {
            $table->string('status')->nullable()->after('pet_detail_id');
        });
    }
};
