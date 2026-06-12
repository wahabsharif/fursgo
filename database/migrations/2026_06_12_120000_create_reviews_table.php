<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('pet_owner_id')->constrained('users')->cascadeOnDelete();
            $table->text('review');
            $table->text('reply')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->foreignId('reply_from')->nullable()->constrained('goormer_spacer_profiles')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
