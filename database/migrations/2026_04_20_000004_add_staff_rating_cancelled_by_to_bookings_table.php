<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('staff')->nullable()->after('extra_add_ons');
            $table->decimal('rating', 3, 1)->nullable()->after('staff');
            $table->string('cancelled_by')->nullable()->after('booking_status');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['staff', 'rating', 'cancelled_by']);
        });
    }
};
