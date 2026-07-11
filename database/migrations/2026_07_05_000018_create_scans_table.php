<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('container_id');
            $table->unsignedBigInteger('material_type_id');
            $table->string('image', 255);
            $table->boolean('is_crushed')->default(false);
            $table->integer('points_awarded')->default(0);
            $table->smallInteger('scan_status')->default(0);
            $table->string('description', 255)->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'fk_scans_user')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->foreign('container_id', 'fk_scans_container')
                  ->references('id')->on('containers')->onDelete('cascade');

            $table->foreign('material_type_id', 'fk_scans_material')
                  ->references('id')->on('material_types')->onDelete('cascade');

            $table->index('user_id', 'idx_scans_user');
            $table->index('container_id', 'idx_scans_container');
            $table->index('material_type_id', 'idx_scans_material');
            $table->index('scan_status', 'idx_scans_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
