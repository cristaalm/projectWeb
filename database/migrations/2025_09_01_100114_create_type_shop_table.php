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

        Schema::create('type_shop', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->timestamps();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();
        Schema::dropIfExists('type_shop');
        DB::commit();
    }
};
