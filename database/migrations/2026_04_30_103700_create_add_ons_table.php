<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('add_ons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('groomer_spacer_id')->index();
            $table->string('add_ons_name');
            $table->text('description')->nullable();
            $table->json('pet_compatibility');
            $table->json('duration');
            $table->json('pricing');
            $table->boolean('add_ons_compatibility')->default(false);
            $table->boolean('visibility_controls')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('add_ons');
    }
};
