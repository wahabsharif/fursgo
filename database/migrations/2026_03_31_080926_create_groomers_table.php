<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groomers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('studio_name');
            $table->decimal('distance', 4, 1)->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->integer('reviews_count')->default(0);
            $table->text('experience_text')->nullable();
            $table->decimal('price', 6, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->json('tags')->nullable();
            $table->json('slots')->nullable();
            $table->boolean('is_top_rated')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groomers');
    }
};
