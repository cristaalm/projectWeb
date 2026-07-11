<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avatar_equipment', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('accessory_id');
            $table->timestamp('equipped_at');
            $table->string('position_slot', 50);

            $table->foreign('user_id', 'fk_avatar_equipment_user')
                  ->references('id')->on('users');

            $table->foreign('accessory_id', 'fk_avatar_equipment_accessory')
                  ->references('id')->on('accessories');

            $table->index('user_id', 'idx_avatar_equipment_user');
            $table->index('accessory_id', 'idx_avatar_equipment_accessory');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avatar_equipment');
    }
};
