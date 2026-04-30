<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('groomer_spacer_id')->index();
            $table->json('pet_compatibility');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_preferences');
    }
};
