<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('badge_id');
            $table->date('month');
            $table->integer('recycles_count')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->foreign('user_id', 'fk_badge_progress_user')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->foreign('badge_id', 'fk_badge_progress_badge')
                  ->references('id')->on('badge')->onDelete('cascade');

            $table->unique(['user_id', 'badge_id', 'month'], 'uq_badge_progress');
            $table->index('user_id', 'idx_badge_progress_user');
            $table->index('month', 'idx_badge_progress_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_progress');
    }
};
