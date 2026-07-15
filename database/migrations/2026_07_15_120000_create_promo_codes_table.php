<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('goormer_spacer_id')
                ->constrained('goormer_spacer_profiles')
                ->cascadeOnDelete();
            $table->string('discount_code', 80);
            $table->string('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('no_end_date')->default(false);
            $table->string('discount_type', 10)->default('%');  // % or £
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->json('services')->nullable();  // {allow_all: bool, selected: string[]}
            $table->json('pet_types')->nullable();
            $table->json('pet_sizes')->nullable();
            $table->boolean('visibility')->default(true);
            $table->timestamps();

            $table->unique(['goormer_spacer_id', 'discount_code']);
            $table->index(['goormer_spacer_id', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
