<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('redeemed_by')->unsigned()->nullable();

            // Datos inmutables al momento del canje
            $table->string('reward_name', 150);
            $table->string('reward_image_url', 255)->nullable();
            $table->integer('points_used');

            // Estado
            $table->tinyInteger('status')->default(1); // 1: canjeado, 2: entregado, 3: cancelado, 4: expirado
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('redeemed_at')->nullable(); // Se asigna al canjear

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('redeemed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reward_redemptions');
    }
};
