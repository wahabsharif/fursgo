<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row = page-level text + photos (hero, section titles, CTAs).
        Schema::create('groomers_hosts_settings', function (Blueprint $table) {
            $table->id();

            // Page Banner (hero)
            $table->string('hero_eyebrow')->nullable();
            $table->string('hero_headline');
            $table->text('hero_description');
            $table->string('hero_primary_cta')->nullable();
            $table->string('hero_primary_cta_url')->nullable();
            $table->string('hero_secondary_cta')->nullable();
            $table->string('hero_secondary_cta_url')->nullable();
            $table->string('hero_photo')->nullable();

            // Banner 1 — Control & flexibility
            $table->string('flexibility_title');

            // Banner 2 — Guaranteed safety & support
            $table->string('safety_title');
            $table->string('safety_photo')->nullable();

            // Banner 3 — Professional tools & CRM
            $table->string('tools_title');

            // Banner 4 — How it works
            $table->string('how_it_works_title');
            $table->text('how_it_works_intro')->nullable();

            // Testimonials
            $table->string('testimonials_title');
            $table->text('testimonials_intro')->nullable();

            // Banner 5 — Service categories
            $table->string('services_title');
            $table->text('services_intro')->nullable();

            $table->timestamps();
        });

        // Repeating cards / steps / quotes / service categories.
        Schema::create('groomers_hosts_items', function (Blueprint $table) {
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
        Schema::dropIfExists('groomers_hosts_items');
        Schema::dropIfExists('groomers_hosts_settings');
    }
};
