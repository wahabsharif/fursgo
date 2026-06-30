<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(
                ['goormer_spacer_id', 'booking_status', 'created_at', 'id'],
                'bookings_dashboard_status_created_idx'
            );
            $table->index(
                ['goormer_spacer_id', 'created_at', 'id'],
                'bookings_dashboard_created_idx'
            );
            $table->index(
                ['goormer_spacer_id', 'booking_status', 'amount', 'created_at', 'id'],
                'bookings_dashboard_status_amount_idx'
            );
            $table->index(
                ['goormer_spacer_id', 'amount', 'created_at', 'id'],
                'bookings_dashboard_amount_idx'
            );
        });

        Schema::table('booking_pet', function (Blueprint $table) {
            $table->index(['booking_id', 'pet_detail_id'], 'booking_pet_booking_pet_detail_idx');
        });
    }

    public function down(): void
    {
        Schema::table('booking_pet', function (Blueprint $table) {
            $table->dropIndex('booking_pet_booking_pet_detail_idx');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_dashboard_amount_idx');
            $table->dropIndex('bookings_dashboard_status_amount_idx');
            $table->dropIndex('bookings_dashboard_created_idx');
            $table->dropIndex('bookings_dashboard_status_created_idx');
        });
    }
};
