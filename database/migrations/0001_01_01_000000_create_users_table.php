<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();

            // Field custom kamu
            $table->string('nama', 100);
            $table->string('username', 50)->unique();
            $table->enum('role', ['admin', 'petani'])->default('petani');

            // Field bawaan Laravel (WAJIB untuk auth)
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->timestamps();
        });

        Schema::create('token_reset_sandi', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_reset_sandi');
        Schema::dropIfExists('pengguna');
    }
};
