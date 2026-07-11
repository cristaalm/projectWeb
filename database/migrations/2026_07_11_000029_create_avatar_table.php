<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avatar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->unique('uq_avatar_user');
            $table->string('preferred_language', 10);
            $table->string('selected_tone', 50);
            $table->boolean('socratic_mode')->nullable();
            $table->boolean('feedback_enabled')->nullable();
            $table->string('voice_model', 50);
            $table->string('current_mood', 50);
            $table->string('state', 50);
            $table->timestamp('updated_at')->nullable();

            $table->foreign('user_id', 'fk_avatar_user')
                  ->references('id')->on('users');

            $table->index('user_id', 'idx_avatar_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avatar');
    }
};
