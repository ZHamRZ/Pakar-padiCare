<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ubah email menjadi nullable jika sebelumnya not null
            $table->string('email')->nullable()->change();

            // Tambahkan kolom untuk token verifikasi dan waktu verifikasi
            $table->string('email_verification_token')->nullable()->after('email');
            $table->timestamp('email_verified_at')->nullable()->after('email_verification_token');

            // Pastikan unique tetap ada tapi mengabaikan null (di beberapa DB driver perlu penanganan khusus, 
            // namun di MySQL modern unique index mengabaikan multiple NULL)
            // Jika error, hapus unique index lama dulu lalu buat baru.
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_verification_token', 'email_verified_at']);
            // Kembalikan email menjadi not null jika diperlukan
            $table->string('email')->nullable(false)->change();
        });
    }
};
