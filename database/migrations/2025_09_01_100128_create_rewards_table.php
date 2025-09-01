<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('alliance_id')->unsigned()->nullable();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->integer('points_required');
            $table->string('image_url', 255)->nullable();
            $table->integer('stock')->nullable(); // null = ilimitado
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rewards');
    }
};
