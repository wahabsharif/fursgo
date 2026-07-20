<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_settings', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');
            $table->unique(['owner_type', 'owner_id']);

            // General
            $table->string('language', 32)->default('en_GB');
            $table->string('timezone')->default('Europe/London');
            $table->char('currency', 3)->default('GBP');

            // App & system
            $table->string('theme', 16)->default('light');
            $table->boolean('push_notifications')->default(true);

            // Login & security
            $table->boolean('two_factor_enabled')->default(false);

            // Notifications
            $table->boolean('notify_booking_updates')->default(true);
            $table->boolean('notify_groomer_messages')->default(true);
            $table->boolean('notify_space_owner_messages')->default(true);
            $table->boolean('notify_promotions')->default(false);
            $table->boolean('notify_reminder_alerts')->default(true);

            // Privacy & permissions
            $table->boolean('profile_visibility')->default(true);
            $table->boolean('data_sharing_consent')->default(true);
            $table->boolean('email_marketing')->default(true);
            $table->boolean('sms_notifications')->default(true);
            $table->boolean('partner_offers')->default(true);
            $table->boolean('analytics_tracking')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_settings');
    }
};
