<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('token')->unique();
            $table->string('platform', 50);
            $table->timestamps();

            $table->foreign('user_id', 'fk_device_tokens_user')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->index('user_id', 'idx_device_tokens_user');
            $table->index('platform', 'idx_device_tokens_platform');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
