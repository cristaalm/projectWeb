<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type', 50);
            $table->string('title', 100)->nullable();
            $table->string('message', 255)->nullable();
            $table->boolean('is_read')->nullable();
            $table->string('action_url', 255)->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('user_id', 'fk_notifications_user')
                  ->references('id')->on('users');

            $table->index('user_id', 'idx_notifications_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
