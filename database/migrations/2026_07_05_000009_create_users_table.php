<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->string('name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('password', 255);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->boolean('tour')->default(false);
            $table->boolean('two_factor_status')->default(false);
            $table->string('google2fa_secret', 100)->nullable();
            $table->string('code_identity', 30)->unique();
            $table->smallInteger('status')->default(1);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();

            $table->foreign('role_id', 'fk_users_role')
                  ->references('id')->on('roles');

            $table->index('role_id', 'idx_users_role');
            $table->index('status', 'idx_users_status');
        });

        DB::table('users')->insert([
            'role_id'       => 1,
            'name'          => 'EcoSort',
            'last_name'     => 'Admin',
            'email'         => 'somosecosort@gmail.com',
            'password'      => Hash::make('ecosort123'),
            'code_identity' => 'ECOSORT-SA-001',
            'status'        => 1,
            'tour'          => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
