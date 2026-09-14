<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faq_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40);
            $table->string('name');
            $table->string('chip_label')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('icon', 40)->default('bookings');
            $table->string('audience', 20);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['audience', 'slug']);
            $table->index('audience');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_categories');
    }
};
