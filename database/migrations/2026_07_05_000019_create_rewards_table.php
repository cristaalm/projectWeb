<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alliance_id');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->integer('points_required');
            $table->integer('stock')->nullable();
            $table->string('code', 150)->unique();
            $table->boolean('is_exclusive')->default(false);
            $table->smallInteger('status')->default(0);
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('alliance_id', 'fk_rewards_alliance')
                  ->references('id')->on('alliances')->onDelete('cascade');

            $table->foreign('approved_by', 'fk_rewards_approved_by')
                  ->references('id')->on('users')->onDelete('set null');

            $table->foreign('rejected_by', 'fk_rewards_rejected_by')
                  ->references('id')->on('users')->onDelete('set null');

            $table->index('alliance_id', 'idx_rewards_alliance');
            $table->index('status', 'idx_rewards_status');
            $table->index('code', 'idx_rewards_code');
            $table->index('expires_at', 'idx_rewards_expires');
            $table->index('is_exclusive', 'idx_rewards_exclusive');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
