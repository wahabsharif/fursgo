<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('account_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('blocker_type');
            $table->unsignedBigInteger('blocker_id');
            $table->string('blocked_type');
            $table->unsignedBigInteger('blocked_id');
            $table->timestamps();

            $table->index(['blocker_type', 'blocker_id']);
            $table->index(['blocked_type', 'blocked_id']);
            $table->unique(
                ['blocker_type', 'blocker_id', 'blocked_type', 'blocked_id'],
                'account_blocks_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_blocks');
    }
};
