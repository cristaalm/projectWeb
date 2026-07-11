<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('admin_user_id');
            $table->integer('points');
            $table->text('reason');
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id', 'fk_point_adj_user')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->foreign('admin_user_id', 'fk_point_adj_admin')
                  ->references('id')->on('users');

            $table->index('user_id', 'idx_point_adj_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_adjustments');
    }
};
