<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();

            $table->string('hero_headline');
            $table->decimal('hero_rating_value', 2, 1)->default(4.9);
            $table->string('hero_rating_headline');
            $table->text('hero_rating_copy');
            $table->string('hero_photo')->nullable();

            $table->string('feature_headline');
            $table->string('feature_photo')->nullable();

            $table->string('how_it_works_title');
            $table->text('how_it_works_intro');

            $table->string('popular_services_title');
            $table->text('popular_services_intro');

            $table->string('testimonials_intro');
            $table->text('testimonials_desc');
            $table->string('testimonials_trust')->nullable();

            $table->string('mission_title');
            $table->text('mission_desc');
            $table->string('mission_cta');
            $table->string('mission_cta_url')->nullable();
            $table->string('mission_photo')->nullable();

            $table->string('service_map_title');
            $table->text('service_map_desc');
            $table->string('service_map_image')->nullable();

            $table->timestamps();
        });

        Schema::create('homepage_items', function (Blueprint $table) {
            $table->id();
            $table->string('section', 40);
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['section', 'is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_items');
        Schema::dropIfExists('homepage_settings');
    }
};
