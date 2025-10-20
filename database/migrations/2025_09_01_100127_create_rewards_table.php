<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('alliance_id')->unsigned();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->integer('points_required');
            $table->integer('stock')->nullable();
            $table->string('code', 150)->unique(); // codigo con el que el comercio puede identificar la recompensa
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('alliance_id');
            $table->index('code');
            $table->index('is_active');
            $table->index('expires_at');
            
            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('cascade');
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();
        Schema::dropIfExists('rewards');
        DB::commit();
    }
};
