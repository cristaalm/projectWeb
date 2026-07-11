<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkpoint_migrations', function (Blueprint $table) {
            $table->increments('v');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoint_migrations');
    }
};
