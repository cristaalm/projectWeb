<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_redemptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reward_id');
            $table->unsignedBigInteger('alliance_id');
            $table->unsignedBigInteger('merchant_user_id')->nullable();
            $table->integer('points_spent');
            $table->integer('quantity')->default(1);
            $table->smallInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('user_id', 'fk_point_red_user')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->foreign('reward_id', 'fk_point_red_reward')
                  ->references('id')->on('rewards')->onDelete('cascade');

            $table->foreign('alliance_id', 'fk_point_red_alliance')
                  ->references('id')->on('alliances');

            $table->foreign('merchant_user_id', 'fk_point_red_merchant')
                  ->references('id')->on('users')->onDelete('set null');

            $table->index('user_id', 'idx_point_red_user');
            $table->index('reward_id', 'idx_point_red_reward');
            $table->index('alliance_id', 'idx_point_red_alliance');
            $table->index('status', 'idx_point_red_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_redemptions');
    }
};
