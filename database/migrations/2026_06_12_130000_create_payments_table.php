<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pet_owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pet_detail_id')->nullable()->constrained('pet_details')->nullOnDelete();
            $table->date('date');
            $table->string('service_type');
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
