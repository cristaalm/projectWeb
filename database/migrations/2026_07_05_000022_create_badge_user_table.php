<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('badge_id');
            $table->date('month');
            $table->timestamp('awarded_at')->useCurrent();
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id', 'fk_badge_user_user')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->foreign('badge_id', 'fk_badge_user_badge')
                  ->references('id')->on('badge')->onDelete('cascade');

            $table->unique(['user_id', 'badge_id', 'month'], 'uq_badge_user_month');
            $table->index('user_id', 'idx_badge_user_user');
            $table->index('month', 'idx_badge_user_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_user');
    }
};
