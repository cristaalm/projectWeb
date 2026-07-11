<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('thread_id', 255);
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamp('created_at');

            $table->foreign('user_id', 'fk_agent_feedback_user')
                  ->references('id')->on('users');

            $table->index('user_id', 'idx_agent_feedback_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_feedback');
    }
};
