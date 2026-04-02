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
        Schema::create('pet_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('photo')->nullable();
            $table->date('birthday');
            $table->string('pet_type');
            $table->string('breed');
            $table->string('sex');
            $table->decimal('weight', 5, 2);
            $table->text('notes')->nullable();
            $table->string('address')->nullable();
            $table->boolean('home_address_toggled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_details');
    }
};
