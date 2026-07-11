<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkpoint_writes', function (Blueprint $table) {
            $table->text('thread_id');
            $table->text('checkpoint_ns')->default('');
            $table->text('checkpoint_id');
            $table->text('task_id');
            $table->integer('idx');
            $table->text('channel');
            $table->text('type')->nullable();
            $table->binary('blob')->nullable();

            $table->primary(
                ['thread_id', 'checkpoint_ns', 'checkpoint_id', 'task_id', 'idx', 'channel'],
                'pk_checkpoint_writes'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoint_writes');
    }
};
