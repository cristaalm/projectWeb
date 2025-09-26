<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        Schema::create('alliances', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('contact_name', 100);
            $table->string('contact_email', 100);
            $table->string('phone', 20);
            $table->string('address', 255);
            $table->unsignedBigInteger('type_shop_id');
            $table->boolean('logo')->default(false);
            $table->string('ext', 10)->nullable();
            $table->tinyInteger('status')->default(1); // 1: activo, 0: pausado
            $table->timestamps();

            $table->index('type_shop_id');
            $table->foreign('type_shop_id')->references('id')->on('type_shop')->onDelete('cascade');
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();
        Schema::dropIfExists('alliances');
        DB::commit();
    }
};
