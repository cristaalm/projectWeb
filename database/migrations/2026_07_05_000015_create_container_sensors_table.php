<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('container_sensors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('container_id');
            $table->string('sensor_key', 50);
            $table->unsignedBigInteger('material_type_id')->nullable();
            $table->decimal('fill_level', 5, 2)->default(0.00);
            $table->timestamp('updated_at')->nullable();

            $table->foreign('container_id', 'fk_sensors_container')
                  ->references('id')->on('containers')->onDelete('cascade');

            $table->foreign('material_type_id', 'fk_sensors_material')
                  ->references('id')->on('material_types')->onDelete('set null');

            $table->unique(['container_id', 'sensor_key'], 'uq_container_sensor');
            $table->index('container_id', 'idx_sensors_container');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('container_sensors');
    }
};
