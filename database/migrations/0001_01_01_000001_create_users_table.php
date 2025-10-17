<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alliance_id')->index();
            $table->string('name', 100); 
            $table->string('last_name', 100); 
            $table->string('email', 255)->unique(); 
            $table->string('phone', 20)->nullable(); 
            $table->string('curp', 20)->unique();
            $table->string('avatar')->nullable();
            $table->string('password'); 
            $table->timestamp('email_verified_at')->nullable(); 
            $table->integer('total_points')->default(0); 
            $table->tinyInteger('verification_status')->default(3); 
            $table->tinyInteger('two_factor_status')->default(0); 
            $table->string('google2fa_secret', 100)->nullable();
            $table->string('code_identity', 30)->unique();
            $table->tinyInteger('status')->default(1); 
            $table->bigInteger('role_id')->unsigned();

            $table->rememberToken();
            $table->timestamps(); 

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('no action');
            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('no action');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
