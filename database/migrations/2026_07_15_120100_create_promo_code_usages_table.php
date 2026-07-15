<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promo_code_usages', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('promo_code_id')
                ->constrained('promo_codes')
                ->cascadeOnDelete();
            $table
                ->foreignId('goormer_spacer_id')
                ->constrained('goormer_spacer_profiles')
                ->cascadeOnDelete();
            $table
                ->foreignId('pet_owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table
                ->foreignId('booking_id')
                ->nullable()
                ->constrained('bookings')
                ->nullOnDelete();
            $table->string('discount_code', 80);
            $table->decimal('discount_applied', 10, 2)->nullable();
            $table->timestamp('used_at');
            $table->timestamps();

            $table->index(['promo_code_id', 'used_at']);
            $table->index(['goormer_spacer_id', 'used_at']);
            $table->index(['pet_owner_id', 'used_at']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_usages');
    }
};
