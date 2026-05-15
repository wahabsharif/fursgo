<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goormer_spacer_profile_id')
                ->constrained('goormer_spacer_profiles')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('job_title')->nullable();
            $table->string('image')->nullable();
            $table->json('working_hours')->nullable();
            $table->json('holiday_time_off')->nullable();
            $table->boolean('pause_booking')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
