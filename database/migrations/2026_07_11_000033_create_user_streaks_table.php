<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_streaks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique('uq_user_streaks_user');
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->boolean('streak_status')->default(false);
            $table->timestamp('updated_at')->nullable();

            $table->foreign('user_id', 'fk_user_streaks_user')
                  ->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_streaks');
    }
};
