<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->tinyInteger('type_history')->unsigned(); // canjeo = 1, suma = 2
            $table->unsignedBigInteger('material_type_id')->nullable(); // en caso de canjeo es null, en caso de suma es el material resiclado
            $table->integer('points')->default(0)->nullable(); // puntos sumados o restados, segun el caso
            $table->unsignedBigInteger('alliance_id')->nullable(); // en caso de suma es null, en caso de canjeo es el comercio
            $table->timestamps();
        });
        
        Schema::table('history', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('type_history');
            $table->index('material_type_id');
            $table->index('alliance_id');
        });

        Schema::table('history', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('material_type_id')->references('id')->on('material_types')->onDelete('cascade');
            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('history');
    }
};
