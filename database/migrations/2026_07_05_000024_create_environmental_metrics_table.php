<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('environmental_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_users')->default(0);
            $table->integer('total_scans')->default(0);
            $table->integer('total_valid_scans')->default(0);
            $table->integer('total_points_awarded')->default(0);
            $table->decimal('kg_recycled', 10, 2)->default(0.00);
            $table->decimal('co2_saved_kg', 10, 2)->default(0.00);
            $table->decimal('kg_per_unit', 8, 4)->default(0.0000);
            $table->decimal('co2_factor', 8, 4)->default(0.0000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('environmental_metrics');
    }
};
