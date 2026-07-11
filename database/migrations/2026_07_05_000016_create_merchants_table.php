<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('alliance_id');
            $table->timestamps();

            $table->foreign('user_id', 'fk_merchants_user')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->foreign('alliance_id', 'fk_merchants_alliance')
                  ->references('id')->on('alliances');

            $table->index('alliance_id', 'idx_merchants_alliance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
