<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('serial_number', 100)->unique();
            $table->string('location', 255);
            $table->json('capacity')->nullable()->default('{"sensor1":0,"sensor2":0,"sensor3":0}');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('containers');
    }
};
