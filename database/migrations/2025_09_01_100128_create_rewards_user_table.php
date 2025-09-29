<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        Schema::create('rewards_user', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reward_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamp('redeemed_at')->default(now());

            $table->index('reward_id');
            $table->index('user_id');
            
            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();
        Schema::dropIfExists('rewards_user');
        DB::commit();
    }
};
