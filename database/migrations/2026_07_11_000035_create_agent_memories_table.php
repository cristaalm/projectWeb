<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_memories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('content')->nullable();
            $table->json('metadata')->nullable();
            $table->json('embedding')->nullable();
            $table->timestamp('created_at');

            $table->foreign('user_id', 'fk_agent_memories_user')
                  ->references('id')->on('users');

            $table->index('user_id', 'idx_agent_memories_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_memories');
    }
};
