<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkpoint_blobs', function (Blueprint $table) {
            $table->text('thread_id');
            $table->text('channel');
            $table->text('version');
            $table->binary('blob')->nullable();

            $table->primary(['thread_id', 'channel', 'version'], 'pk_checkpoint_blobs');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoint_blobs');
    }
};
