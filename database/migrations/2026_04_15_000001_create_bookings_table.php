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
            $table->string('visit_type', 64);
            $table->string('booking_status', 64)->default('pending');
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
