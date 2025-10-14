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
            $table->bigInteger('comerciant_id')->unsigned()->nulleable(); // en caso de canjeo por medio del comerciante // osea type_history = 3
            $table->tinyInteger('type_history')->unsigned(); // canjeo = 1, suma = 2, canjeo por comerciante = 3 
            $table->unsignedBigInteger('material_type_id')->nullable(); // en caso de canjeo es null, en caso de suma es el material resiclado
            $table->unsignedBigInteger('scan_id')->nullable(); // en caso de canjeo es null, en caso de suma es el scan
            $table->integer('points')->default(0)->nullable(); // puntos sumados o restados, segun el caso
            $table->unsignedBigInteger('reward_id')->nullable(); // en caso de suma es null, en caso de canjeo es el comercio
            $table->unsignedBigInteger('alliance_id')->nullable(); // en caso de suma es null, en caso de canjeo es el comercio
            $table->timestamps();
        });
        
        Schema::table('history', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('comerciant_id');
            $table->index('type_history');
            $table->index('material_type_id');
            $table->index('scan_id');
            $table->index('reward_id');
            $table->index('alliance_id');
        });

        Schema::table('history', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('comerciant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('material_type_id')->references('id')->on('material_types')->onDelete('cascade');
            $table->foreign('scan_id')->references('id')->on('scans')->onDelete('cascade');
            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('cascade');
            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('history');
    }
};
