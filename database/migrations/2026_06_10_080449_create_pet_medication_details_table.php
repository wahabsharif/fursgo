<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pet_medication_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pet_detail_id')->unique()->constrained('pet_details')->cascadeOnDelete();
            $table->timestamp('last_verified')->nullable();
            $table->string('veterinary_clinic')->nullable();
            $table->json('vaccinations')->nullable();
            // JSON string arrays, e.g. ["Mild skin allergies", "Sensitive paws"]
            $table->json('health_conditions')->nullable();
            $table->json('current_medication')->nullable();
            $table->json('allergies')->nullable();
            $table->json('emergency_contact')->nullable();
            $table->text('groomer_guidance_notes')->nullable();
            $table->json('preferred_grooming_style')->nullable();
            $table->json('grooming_behaviour')->nullable();
            // JSON object keyed by activity, e.g. {"bathing": "Comfortable", "dryer": "Slightly nervous"}
            $table->json('tolerance_levels')->nullable();
            $table->text('product_preferences')->nullable();
            $table->text('handling_notes')->nullable();
            $table->json('photo_gallery')->nullable();
            // JSON arrays of { date, title, note }
            $table->json('groomer_notes')->nullable();
            $table->json('owner_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_medication_details');
    }
};
