<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkpoints', function (Blueprint $table) {
            $table->text('thread_id');
            $table->text('checkpoint_ns')->default('');
            $table->text('checkpoint_id');
            $table->text('parent_checkpoint_id')->nullable();
            $table->binary('checkpoint')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');

            $table->primary(['thread_id', 'checkpoint_ns', 'checkpoint_id'], 'pk_checkpoints');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoints');
    }
};
