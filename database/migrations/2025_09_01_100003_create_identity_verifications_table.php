<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('identity_verifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('ine_front_url', 255);
            $table->string('ine_back_url', 255);
            $table->string('document_number', 50);
            $table->tinyInteger('status')->default(0); // 0: pendiente, 1: aprobado, 2: rechazado, 3: corregir
            $table->text('rejection_reason')->nullable();
            $table->bigInteger('verified_by')->unsigned()->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('identity_verifications');
    }
};
