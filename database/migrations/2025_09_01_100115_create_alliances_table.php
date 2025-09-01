<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alliances', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('logo_url', 255)->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->tinyInteger('status')->default(1); // 1: activo, 0: pausado
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alliances');
    }
};
