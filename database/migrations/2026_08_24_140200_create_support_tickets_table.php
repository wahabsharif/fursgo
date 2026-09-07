<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('goormer_spacer_id')->nullable()->constrained('goormer_spacer_profiles')->nullOnDelete();
            $table->string('email');
            $table->string('subject');
            $table->string('category', 40);
            $table->string('booking_reference', 80)->nullable();
            $table->text('description');
            $table->string('status', 20)->default('open');
            $table->string('audience', 20)->default('pet_owner');
            $table->timestamps();

            $table->index(['email', 'status']);
            $table->index('audience');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
