<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alembic_version', function (Blueprint $table) {
            $table->string('version_num', 32)->primary();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alembic_version');
    }
};
