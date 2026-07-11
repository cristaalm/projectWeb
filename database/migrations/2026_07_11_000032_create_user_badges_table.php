<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('badge_id');
            $table->timestamp('earned_at');

            $table->foreign('user_id', 'fk_user_badges_user')
                  ->references('id')->on('users');

            $table->foreign('badge_id', 'fk_user_badges_badge')
                  ->references('id')->on('badge');

            $table->index('user_id', 'idx_user_badges_user');
            $table->index('badge_id', 'idx_user_badges_badge');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
