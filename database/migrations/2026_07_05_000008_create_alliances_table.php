<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alliances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_shop_id');
            $table->string('name', 150);
            $table->string('contact_name', 100);
            $table->string('contact_email', 100);
            $table->string('phone', 20);
            $table->string('address', 255);
            $table->string('logo_url', 255)->nullable();
            $table->boolean('has_exclusive_rewards')->default(false);
            $table->smallInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('type_shop_id', 'fk_alliances_type_shop')
                  ->references('id')->on('type_shop');

            $table->index('type_shop_id', 'idx_alliances_type_shop');
            $table->index('status', 'idx_alliances_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alliances');
    }
};
