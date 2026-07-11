<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('scan_id');
            $table->integer('points');
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id', 'fk_point_earnings_user')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->foreign('scan_id', 'fk_point_earnings_scan')
                  ->references('id')->on('scans')->onDelete('cascade');

            $table->index('user_id', 'idx_point_earnings_user');
            $table->index('scan_id', 'idx_point_earnings_scan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_earnings');
    }
};
