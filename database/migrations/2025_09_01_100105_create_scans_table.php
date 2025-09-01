<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('container_id')->unsigned();
            $table->bigInteger('material_type_id')->unsigned();
            $table->string('image_url', 255);
            $table->boolean('is_valid')->default(false);
            $table->integer('points_awarded')->default(0);
            $table->tinyInteger('scan_status')->default(0); // 0: pendiente, 1: aceptado, 2: rechazado
            $table->string('rejection_reason', 255)->nullable();
            $table->timestamp('scanned_at')->nullable(); // Se asigna en app
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('container_id')->references('id')->on('containers')->onDelete('cascade');
            $table->foreign('material_type_id')->references('id')->on('material_types')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('scans');
    }
};
