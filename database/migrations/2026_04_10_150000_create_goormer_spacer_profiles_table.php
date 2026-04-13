<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goormer_spacer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('email');
            $table->string('password');
            $table->string('user_type')->nullable();
            $table->string('account_type')->nullable();
            $table->json('select_location_type')->nullable();
            $table->json('business_details')->nullable();
            $table->json('freelance_details')->nullable();
            $table->json('business_basics')->nullable();
            $table->json('groomer_business_profile')->nullable();
            $table->boolean('legal_policy_agreements')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goormer_spacer_profiles');
    }
};
