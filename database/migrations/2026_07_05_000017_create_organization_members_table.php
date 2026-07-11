<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('alliance_id');
            $table->timestamps();

            $table->foreign('user_id', 'fk_org_members_user')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->foreign('alliance_id', 'fk_org_members_alliance')
                  ->references('id')->on('alliances');

            $table->index('alliance_id', 'idx_org_members_alliance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_members');
    }
};
