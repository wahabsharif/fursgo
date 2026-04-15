<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_owner_id')->constrained('users')->onDelete('cascade');
            $table->string('time');
            $table->date('date');
            $table->string('service');
            $table->decimal('amount', 8, 2);
            $table->enum('visit_type', ['home_visit', 'salon', 'mobile_station']);
            $table->enum('booking_status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::create('booking_pet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('pet_detail_id')->constrained('pet_details')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_pet');
        Schema::dropIfExists('bookings');
    }
};
