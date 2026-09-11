<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('badge_user_id');
            $table->integer('points');
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id', 'fk_badge_earnings_user')
                ->references('id')->on('users')->onDelete('cascade');

            $table->foreign('badge_user_id', 'fk_badge_earnings_badge_user')
                ->references('id')->on('badge_user')->onDelete('cascade');

            $table->unique('badge_user_id', 'uq_badge_earnings_badge_user');
            $table->index('user_id', 'idx_badge_earnings_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_earnings');
    }
};
