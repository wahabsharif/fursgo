<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Drops MySQL ENUM definitions on existing installs (fresh installs already use strings
 * from create_bookings_table). SQLite tests never use MySQL ENUMs.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE `bookings` MODIFY `visit_type` VARCHAR(64) NOT NULL');
        DB::statement("ALTER TABLE `bookings` MODIFY `booking_status` VARCHAR(64) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Irreversible: do not restore ENUM constraints.
    }
};
