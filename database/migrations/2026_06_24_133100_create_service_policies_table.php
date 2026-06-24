<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goormer_spacer_profiles_id')->index();
            $table->json('cancellation_policy');
            $table->json('late_arrival_policy');
            $table->boolean('refund_policy')->default(false);
            $table->json('service_limitations');
            $table->boolean('animal_welfare_statement')->default(false);
            $table->json('hygiene_safety_standards');
            $table->boolean('compliance_declaration')->default(false);
            $table->json('compliance_timeline');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_policies');
    }
};
