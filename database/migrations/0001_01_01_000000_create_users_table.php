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
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('no_hp')->unique()->nullable(); 
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->enum('role', ['admin', 'user', 'verifier'])->default('user');
            $table->rememberToken();
            $table->timestamps();
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
                    $table->id();
                    $table->string('identifier'); 
                    $table->enum('via', ['email', 'phone']);
                    $table->text('token'); 
                    $table->timestamp('created_at')->nullable();
                    $table->timestamp('expires_at')->nullable();
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
