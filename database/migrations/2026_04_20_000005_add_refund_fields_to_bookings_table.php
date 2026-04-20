<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('refund_amount', 8, 2)->nullable()->after('amount');
            $table->string('refund_status')->nullable()->after('cancelled_by');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['refund_amount', 'refund_status']);
        });
    }
};
