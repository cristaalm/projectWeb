<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_account_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('actor_user_id')->constrained('users');
            $table->string('action_type', 30);
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('target_user_id', 'idx_user_account_actions_target');
            $table->index('action_type', 'idx_user_account_actions_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_account_actions');
    }
};
